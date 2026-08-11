<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Kos;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KosOverviewController extends Controller
{
    /**
     * Superadmin dapat melihat seluruh kos yang terdaftar beserta Owner pemiliknya --
     * membantu memverifikasi kos id tertentu milik akun Owner yang mana (mis. saat
     * troubleshoot akses 403 "Kos ini bukan milik akun Anda").
     */
    public function index(Request $request): View
    {
        $query = Kos::with('owner')->withCount('kamars');

        if ($request->filled('q')) {
            $search = $request->string('q');
            $query->where(function ($qq) use ($search) {
                $qq->where('nama', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%")
                    ->orWhereHas('owner', fn ($o) => $o->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            });
        }

        return view('superadmin.kos.index', [
            'kosList' => $query->latest()->paginate(15)->withQueryString(),
            'filters' => $request->only('q'),
        ]);
    }
}
