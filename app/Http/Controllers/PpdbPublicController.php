<?php

namespace App\Http\Controllers;

use App\Models\PpdbApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\PpdbToken;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PpdbPublicController extends Controller
{
    public function create()
    {
        return view('ppdb.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', Rule::unique('ppdb_applications', 'email')],
            'whatsapp' => ['required', 'string', 'max:30', Rule::unique('ppdb_applications', 'whatsapp')],
            'payment_proof' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ]);

        $path = $request->file('payment_proof')->store('ppdb/payment_proofs', 'local');

        $app = PpdbApplication::create([
            'public_code' => Str::upper(Str::random(16)),
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'whatsapp' => $data['whatsapp'],
            'payment_proof_path' => $path,
            'status' => 'submitted',
        ]);

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

        // safety: hanya boleh activate kalau status approved (atau resubmitted->approved)
        abort_unless(in_array($app->status, ['approved'], true), 403);

        return view('ppdb.activate', [
            'token' => $token,
            'app' => $app,
        ]);
    }

    public function activate(Request $request, string $token)
    {
        $t = $this->findValidToken($token, 'activation');
        $app = $t->application;

        abort_unless(in_array($app->status, ['approved'], true), 403);

        $data = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        // buat user kalau belum ada
        $user = $app->user_id
            ? User::findOrFail($app->user_id)
            : User::create([
                'name' => $app->full_name,
                'email' => $app->email,
                'password' => Hash::make($data['password']),
                'role' => 'user',
                'email_verified_at' => now(), // karena ini aktivasi resmi
            ]);

        $app->update([
            'user_id' => $user->id,
            'status' => 'activated',
        ]);

        $t->update(['used_at' => now()]);

        // auto login
        auth()->login($user);

        return redirect()->route('user.dashboard');
    }

    public function showEdit(string $token)
    {
        $t = $this->findValidToken($token, 'edit');
        $app = $t->application;

        // edit hanya boleh kalau masih submitted atau rejected (sesuaikan kalau kamu mau)
        abort_unless(in_array($app->status, ['submitted', 'rejected'], true), 403);

        return view('ppdb.edit', [
            'token' => $token,
            'app' => $app,
        ]);
    }

    public function updateEdit(Request $request, string $token)
    {
        $t = $this->findValidToken($token, 'edit');
        $app = $t->application;

        abort_unless(in_array($app->status, ['submitted', 'rejected'], true), 403);

        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', Rule::unique('ppdb_applications', 'email')->ignore($app->id)],
            'whatsapp' => ['required', 'string', 'max:30', Rule::unique('ppdb_applications', 'whatsapp')->ignore($app->id)],
            'payment_proof' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ]);

        // kalau upload baru, replace path lama
        $newPath = $app->payment_proof_path;
        if ($request->hasFile('payment_proof')) {
            $newPath = $request->file('payment_proof')->store('ppdb/payment_proofs', 'local');
        }

        $app->update([
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'whatsapp' => $data['whatsapp'],
            'payment_proof_path' => $newPath,
            'status' => 'submitted', // balik ke antrian verifikasi
            'rejected_reason' => null,
            'verified_at' => null,
            'verified_by' => null,
        ]);

        // token edit sekali pakai
        $t->update(['used_at' => now()]);

        return redirect()
            ->route('ppdb.receipt', $app->public_code)
            ->with('success', 'Data berhasil diperbarui. Silakan tunggu verifikasi admin.');
    }
}
