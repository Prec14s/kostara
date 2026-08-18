<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentSettingController extends Controller
{
    /**
     * Modul 14.1: Owner mencantumkan rekening bank dan/atau QRIS untuk ditampilkan ke Customer.
     */
    public function edit(Request $request): View
    {
        return view('owner.payment-settings.edit', ['owner' => $request->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'bank_account_holder' => ['nullable', 'string', 'max:255'],
            'qris_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'terima_tunai' => ['sometimes', 'boolean'],
        ]);

        $data['terima_tunai'] = $request->boolean('terima_tunai');

        if ($request->hasFile('qris_image')) {
            $data['qris_image'] = $request->file('qris_image')->store('qris', 'public');
        }

        $request->user()->update($data);

        return back()->with('status', 'Metode pembayaran berhasil disimpan.');
    }
}
