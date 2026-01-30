<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PpdbApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PpdbProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();
        $app = PpdbApplication::where('user_id', $user->id)->firstOrFail();

        $this->authorize('editAsUser', $app);

        return view('user.ppdb.edit', compact('app'));
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $app = PpdbApplication::where('user_id', $user->id)->firstOrFail();

        $this->authorize('editAsUser', $app);

        $data = $request->validate([
            'full_name'     => ['required', 'string', 'max:120'],
            'email'         => ['required', 'email', 'max:190', Rule::unique('ppdb_applications', 'email')->ignore($app->id)],
            'whatsapp'      => ['required', 'string', 'max:30', Rule::unique('ppdb_applications', 'whatsapp')->ignore($app->id)],
            'payment_proof' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ]);

        $disk = Storage::disk('ppdb_private');

        DB::transaction(function () use ($request, $data, $app, $disk) {
            $newPath = $app->payment_proof_path;

            if ($request->hasFile('payment_proof')) {
                $newPath = $disk->putFile('payment_proofs', $request->file('payment_proof'));
                if ($app->payment_proof_path && $disk->exists($app->payment_proof_path)) {
                    $disk->delete($app->payment_proof_path);
                }
            }

            $app->update([
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'whatsapp' => $data['whatsapp'],
                'payment_proof_path' => $newPath,
            ]);

            // balik antrean verifikasi lagi
            $app->markResubmitted();
        });

        return back()->with('success', 'Perubahan dikirim. Menunggu verifikasi admin lagi.');
    }

    public function paymentProof(Request $request)
    {
        $user = $request->user();
        $app = PpdbApplication::where('user_id', $user->id)->firstOrFail();

        $this->authorize('viewPaymentProof', $app);

        $disk = Storage::disk('ppdb_private');

        abort_unless($app->payment_proof_path && $disk->exists($app->payment_proof_path), 404);

        return response()->file($disk->path($app->payment_proof_path));
    }
}
