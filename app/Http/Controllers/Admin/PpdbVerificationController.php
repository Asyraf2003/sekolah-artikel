<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PpdbStatus;
use App\Http\Controllers\Controller;
use App\Models\PpdbApplication;
use App\Services\PpdbTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PpdbVerificationController extends Controller
{
    public function approve(Request $request, PpdbApplication $ppdb, PpdbTokenService $tokens)
    {
        abort_unless($ppdb->status === PpdbStatus::SUBMITTED, 403);

        $admin = $request->user();

        $result = DB::transaction(function () use ($ppdb, $tokens, $admin) {
            $ppdb->markApproved($admin);
            $t = $tokens->issue($ppdb, 'activation', $admin->id);
            $link = route('ppdb.activate.show', ['token' => $t['plain']]);

            return $link;
        });

        return back()
            ->with('success', 'Approved. Activation link dibuat.')
            ->with('activation_link', $result);
    }

    public function reject(Request $request, PpdbApplication $ppdb, PpdbTokenService $tokens)
    {
        abort_unless(in_array($ppdb->status, [PpdbStatus::SUBMITTED, PpdbStatus::REJECTED], true), 403);

        $data = $request->validate([
            'reason' => ['required', 'string', 'max:2000'],
        ]);

        $admin = $request->user();

        $link = DB::transaction(function () use ($ppdb, $tokens, $admin, $data) {
            // kalau sudah rejected dan admin cuma regen link, kamu bisa pilih:
            // - tetap panggil markRejected biar update verified_at/verified_by + refresh reason
            // - atau skip markRejected dan cuma issue token
            $ppdb->markRejected($admin, $data['reason']);

            $t = $tokens->issue($ppdb, 'edit', $admin->id);
            return route('ppdb.edit.show', ['token' => $t['plain']]);
        });

        return back()
            ->with('success', 'Rejected. Edit link dibuat.')
            ->with('edit_link', $link);
    }
}
