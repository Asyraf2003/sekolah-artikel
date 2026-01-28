<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PpdbApplication;
use App\Services\PpdbTokenService;
use Illuminate\Http\Request;

class PpdbVerificationController extends Controller
{
    public function approve(Request $request, PpdbApplication $ppdb, PpdbTokenService $tokens)
    {
        $user = $request->user();

        $ppdb->update([
            'status' => 'approved',
            'rejected_reason' => null,
            'verified_at' => now(),
            'verified_by' => $user->id,
        ]);

        $t = $tokens->issue($ppdb, 'activation', $user->id);

        $link = route('ppdb.activate.show', ['token' => $t['plain']]);

        return back()->with('success', 'Approved. Activation link dibuat.')->with('activation_link', $link);
    }

    public function reject(Request $request, PpdbApplication $ppdb, PpdbTokenService $tokens)
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'max:2000'],
        ]);

        $user = $request->user();

        $ppdb->update([
            'status' => 'rejected',
            'rejected_reason' => $data['reason'],
            'verified_at' => now(),
            'verified_by' => $user->id,
        ]);

        $t = $tokens->issue($ppdb, 'edit', $user->id);

        $link = route('ppdb.edit.show', ['token' => $t['plain']]);

        return back()->with('success', 'Rejected. Edit link dibuat.')->with('edit_link', $link);
    }
}
