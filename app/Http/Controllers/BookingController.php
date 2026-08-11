<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Kamar;
use App\Models\Pembayaran;
use App\Models\Sewa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BookingController extends Controller
{
    /**
     * Modul 10 & 27: Customer memilih kamar & durasi, lalu melakukan booking.
     */
    public function create(Request $request, Kamar $kamar): View
    {
        abort_if($kamar->status !== 'tersedia', 404, 'Kamar tidak tersedia untuk booking.');

        $kamar->load(['fotos', 'kos']);

        return view('booking.create', ['kamar' => $kamar]);
    }

    public function store(Request $request, Kamar $kamar): RedirectResponse
    {
        abort_if($kamar->status !== 'tersedia', 422, 'Kamar tidak tersedia untuk booking.');

        $data = $request->validate([
            'jenis_durasi' => ['required', 'in:harian,mingguan,bulanan,tahunan'],
            'tanggal_mulai' => ['required', 'date', 'after_or_equal:today'],
            'catatan' => ['nullable', 'string'],
        ]);

        $booking = Booking::create([
            'customer_id' => $request->user()->id,
            'kamar_id' => $kamar->id,
            'jenis_durasi' => $data['jenis_durasi'],
            'tanggal_mulai' => $data['tanggal_mulai'],
            'catatan' => $data['catatan'] ?? null,
            'status' => 'menunggu',
        ]);

        $kamar->update(['status' => 'dipesan']);

        return redirect()->route('booking.confirmation', $booking);
    }

    /**
     * Halaman konfirmasi setelah booking dikirim -- Customer bisa langsung menghubungi
     * Owner via WhatsApp agar booking segera diproses.
     */
    public function confirmation(Request $request, Booking $booking): View
    {
        abort_unless($booking->customer_id === $request->user()->id, 403);
        $booking->load('kamar.kos.owner', 'customer');

        return view('booking.confirmation', ['booking' => $booking]);
    }

    /**
     * Owner/Penjaga melihat daftar booking yang masuk (Modul 10).
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = Booking::with(['customer', 'kamar.kos']);

        if ($user->isOwner()) {
            $query->whereHas('kamar.kos', fn ($q) => $q->where('owner_id', $user->id));
        } elseif ($user->isPenjaga()) {
            $query->whereHas('kamar', fn ($q) => $q->where('penjaga_id', $user->id));
        }

        return view('booking.index', ['bookings' => $query->latest()->paginate(15)]);
    }

    /**
     * Owner menyetujui booking: sekaligus membuat data Sewa & tagihan pembayaran pertama.
     */
    public function approve(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless((int) $booking->kamar->kos->owner_id === (int) $request->user()->id, 403, 'Booking ini bukan milik kos Anda.');

        DB::transaction(function () use ($booking) {
            $booking->update(['status' => 'disetujui']);

            $tanggalMulai = $booking->tanggal_mulai->copy();
            $tanggalSelesai = match ($booking->jenis_durasi) {
                'harian' => $tanggalMulai->copy()->addDay(),
                'mingguan' => $tanggalMulai->copy()->addWeek(),
                'bulanan' => $tanggalMulai->copy()->addMonth(),
                'tahunan' => $tanggalMulai->copy()->addYear(),
            };

            $sewa = Sewa::create([
                'kamar_id' => $booking->kamar_id,
                'customer_id' => $booking->customer_id,
                'booking_id' => $booking->id,
                'jenis_durasi' => $booking->jenis_durasi,
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai,
                'harga' => $booking->kamar->hargaUntuk($booking->jenis_durasi) ?? 0,
                'status' => 'menunggu_pembayaran',
            ]);

            Pembayaran::create([
                'no_transaksi' => 'TRX-'.Str::upper(Str::random(8)),
                'sewa_id' => $sewa->id,
                'jenis_pembayaran' => 'sewa',
                'nominal' => $sewa->harga,
                'status' => 'belum_bayar',
            ]);
        });

        return back()->with('status', 'Booking disetujui. Tagihan pembayaran telah dibuat untuk Customer.');
    }

    public function reject(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless((int) $booking->kamar->kos->owner_id === (int) $request->user()->id, 403, 'Booking ini bukan milik kos Anda.');

        $booking->update(['status' => 'ditolak']);
        $booking->kamar->update(['status' => 'tersedia']);

        return back()->with('status', 'Booking ditolak. Kamar kembali berstatus Tersedia.');
    }
}
