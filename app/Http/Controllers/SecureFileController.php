<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\PenyewaLangsung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SecureFileController extends Controller
{
    /**
     * BR05: foto KTP hanya dapat diakses oleh pihak yang memiliki hak akses (Owner),
     * tidak dapat diakses hanya dengan mengetahui URL file.
     */
    public function ktp(Request $request, PenyewaLangsung $penyewaLangsung): StreamedResponse
    {
        $user = $request->user();
        $isOwnerPemilik = $user->isOwner() && (int) $penyewaLangsung->kamar->kos->owner_id === (int) $user->id;
        $isSuperadmin = $user->isSuperadmin();

        abort_unless(($isOwnerPemilik || $isSuperadmin) && $penyewaLangsung->foto_ktp, 403);

        return Storage::disk('private')->response($penyewaLangsung->foto_ktp);
    }

    /**
     * Bukti pembayaran hanya bisa dilihat oleh Customer pemilik sewa dan Owner kos terkait.
     */
    public function buktiPembayaran(Request $request, Pembayaran $pembayaran): StreamedResponse
    {
        $user = $request->user();
        $isPemilikSewa = $pembayaran->sewa->customer_id === $user->id;
        $isOwnerTerkait = $user->isOwner() && (int) $pembayaran->sewa->kamar->kos->owner_id === (int) $user->id;

        abort_unless(($isPemilikSewa || $isOwnerTerkait || $user->isSuperadmin()) && $pembayaran->bukti_pembayaran, 403);

        return Storage::disk('private')->response($pembayaran->bukti_pembayaran);
    }
}
