<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PpdbApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PpdbController extends Controller
{
    public function index(Request $request)
    {
        $q       = trim((string) $request->query('q', ''));
        $status  = $request->query('status');
        $perPage = (int) ($request->query('per_page', 15));

        $ppdbs = PpdbApplication::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('full_name', 'like', "%{$q}%")
                       ->orWhere('email', 'like', "%{$q}%")
                       ->orWhere('whatsapp', 'like', "%{$q}%")
                       ->orWhere('public_code', 'like', "%{$q}%");
                });
            })
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.ppdb.index', compact('ppdbs'));
    }

    public function show(PpdbApplication $ppdb)
    {
        $files = [
            'Bukti Pembayaran' => route('admin.ppdb.paymentProof', $ppdb),
        ];

        return view('admin.ppdb.show', compact('ppdb', 'files'));
    }

    public function paymentProof(\App\Models\PpdbApplication $ppdb)
    {
        abort_unless($ppdb->payment_proof_path, 404);

        $path = $ppdb->payment_proof_path;

        $disk = \Storage::disk('ppdb_private');
        if (!$disk->exists($path)) {
            $disk = \Storage::disk('local');
        }

        abort_unless($disk->exists($path), 404);

        return response()->file($disk->path($path));
    }
}
