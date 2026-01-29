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
        $q      = trim((string) $request->query('q', ''));
        $status = $request->query('status');
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
            'Bukti Pembayaran' => $ppdb->payment_proof_path
                ? route('admin.ppdb.paymentProof', $ppdb)
                : null,
        ];

        return view('admin.ppdb.show', compact('ppdb', 'files'));
    }

    public function updateStatus(Request $request, PpdbApplication $ppdb)
    {
        $data = $request->validate([
            'status' => ['required', 'string', Rule::in(['submitted', 'approved', 'activated', 'rejected'])],
        ]);

        // Catatan: approve/reject sebaiknya lewat controller verifikasi (biar token/verified_at keurus).
        // Tapi kamu minta halaman detail bisa ubah status, jadi ini disediakan.
        $ppdb->update([
            'status' => $data['status'],
        ]);

        return back()->with('success', 'Status berhasil diperbarui.');
    }

    public function paymentProof(PpdbApplication $ppdb)
    {
        abort_unless($ppdb->payment_proof_path, 404);

        $disk = Storage::disk('local');
        $path = $ppdb->payment_proof_path;

        abort_unless($disk->exists($path), 404);

        $absolute = $disk->path($path);

        // browser bisa preview image/pdf kalau content-type benar
        return response()->file($absolute);
    }
}
