<?php

namespace App\Services;

use App\Models\PpdbApplication;
use App\Models\PpdbToken;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class PpdbTokenService
{
    public function issue(PpdbApplication $app, string $type, ?int $createdBy = null, int $ttlHours = 168): array
    {
        // invalidate token lama tipe sama (biar refresh gampang)
        PpdbToken::where('ppdb_application_id', $app->id)
            ->where('type', $type)
            ->whereNull('used_at')
            ->update(['used_at' => now()]);

        $plain = Str::random(64);
        $hash = hash('sha256', $plain);

        PpdbToken::create([
            'ppdb_application_id' => $app->id,
            'type' => $type,
            'token_hash' => $hash,
            'expires_at' => Carbon::now()->addHours($ttlHours),
            'created_by' => $createdBy,
        ]);

        return ['plain' => $plain, 'hash' => $hash];
    }
}
