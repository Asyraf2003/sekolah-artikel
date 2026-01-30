<?php

namespace App\Http\Controllers;

use App\Enums\PpdbStatus;
use App\Models\PpdbApplication;
use App\Models\PpdbToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class PpdbPublicController extends Controller
{
    public function create()
    {
        return view('ppdb.create');
    }

    public function store(Request $request)
    {
        // Honeypot anti bot
        if ($request->filled('website')) {
            abort(422, 'Spam detected');
        }

        $data = $request->validate([
            'full_name'     => ['required', 'string', 'max:120'],
            'email'         => ['required', 'email', 'max:190', Rule::unique('ppdb_applications', 'email')],
            'whatsapp'      => ['required', 'string', 'max:30', Rule::unique('ppdb_applications', 'whatsapp')],
            'payment_proof' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ]);

        $disk = Storage::disk('ppdb_private');
        $path = $disk->putFile('payment_proofs', $request->file('payment_proof'));

        $app = new PpdbApplication([
            'full_name'          => $data['full_name'],
            'email'              => $data['email'],
            'whatsapp'           => $data['whatsapp'],
            'payment_proof_path' => $path,
        ]);

        // set field yang tidak mau bisa dimass-assign dari request
        $app->forceFill([
            'public_code' => Str::upper(Str::random(16)),
            'status'      => PpdbStatus::SUBMITTED,
        ]);

        $app->save();

        return redirect()
            ->route('ppdb.receipt', $app->public_code)
            ->with('success', 'Pendaftaran berhasil dikirim. Tunggu verifikasi admin.');
    }

    public function receipt(string $code)
    {
        $app = PpdbApplication::where('public_code', $code)->firstOrFail();

        return view('ppdb.receipt', compact('app'));
    }

    private function findValidToken(string $plain, string $type): PpdbToken
    {
        $hash = hash('sha256', $plain);

        return PpdbToken::query()
            ->where('token_hash', $hash)
            ->where('type', $type)
            ->whereNull('used_at')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->firstOrFail();
    }

    public function showActivate(string $token)
    {
        $t = $this->findValidToken($token, 'activation');
        $app = $t->application;

        // Aktivasi hanya setelah admin approve
        abort_unless($app->status === PpdbStatus::APPROVED, 403);

        return view('ppdb.activate', [
            'token' => $token,
            'app' => $app,
        ]);
    }
        
    public function activate(Request $request, string $token)
    {
        $t = $this->findValidToken($token, 'activation');
        $app = $t->application;

        abort_unless($app->status === PpdbStatus::APPROVED, 403);

        $data = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = DB::transaction(function () use ($app, $t, $data) {
            $user = $app->user_id
                ? User::findOrFail($app->user_id)
                : User::where('email', $app->email)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $app->full_name,
                    'email' => $app->email,
                    'password' => Hash::make($data['password']),
                    'role' => 'user',
                    'email_verified_at' => now(),
                ]);
            } else {
                $user->forceFill(['password' => Hash::make($data['password'])])->save();
            }

            $app->markActivated($user);
            $t->update(['used_at' => now()]);

            return $user;
        });

        auth()->login($user);

        return redirect()->route('user.dashboard');
    }

    public function showEdit(string $token)
    {
        $t = $this->findValidToken($token, 'edit');
        $app = $t->application;

        // Edit hanya kalau status rejected (sesuai flow kamu)
        abort_unless($app->status === PpdbStatus::REJECTED, 403);

        return view('ppdb.edit', [
            'token' => $token,
            'app' => $app,
        ]);
    }

    public function updateEdit(Request $request, string $token)
    {
        $t = $this->findValidToken($token, 'edit');
        $app = $t->application;

        abort_unless($app->status === PpdbStatus::REJECTED, 403);

        $data = $request->validate([
            'full_name'     => ['required', 'string', 'max:120'],
            'email'         => ['required', 'email', 'max:190', Rule::unique('ppdb_applications', 'email')->ignore($app->id)],
            'whatsapp'      => ['required', 'string', 'max:30', Rule::unique('ppdb_applications', 'whatsapp')->ignore($app->id)],
            'payment_proof' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ]);

        $disk = \Storage::disk('ppdb_private');

        DB::transaction(function () use ($request, $data, $app, $t, $disk) {
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

            $app->markResubmitted();
            $t->update(['used_at' => now()]);
        });

        return redirect()
            ->route('ppdb.receipt', $app->public_code)
            ->with('success', 'Data berhasil diperbarui. Silakan tunggu verifikasi admin.');
    }
}
