<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class PpdbTokenSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->where('email', 'admin@example.com')->first()
            ?? User::query()->first();

        $apps = DB::table('ppdb_applications')->select('id', 'status', 'created_at', 'verified_at')->get();
        if ($apps->isEmpty()) return;

        $rows = [];
        $now = now();

        foreach ($apps as $app) {
            $createdAtBase = Carbon::parse($app->verified_at ?? $app->created_at)->copy();

            $types = match ($app->status) {
                'submitted' => ['edit'],
                'approved'  => ['edit', 'activation'],
                'activated' => ['activation'],
                'rejected'  => ['edit'], // boleh kamu hapus kalau ga mau
                default     => ['edit'],
            };

            foreach ($types as $type) {
                $plain = $this->makePlainToken($type, (int)$app->id);
                $hash  = hash('sha256', $plain);

                $createdAt = $createdAtBase->copy()->addMinutes(rand(5, 180));
                $expiresAt = $createdAt->copy()->addHours(48);

                $usedAt = null;

                if ($app->status === 'activated' && $type === 'activation') {
                    // token activation dianggap sudah dipakai
                    $usedAt = $createdAt->copy()->addMinutes(rand(10, 240));
                } else {
                    // random: sebagian expired, sebagian used, sebagian valid
                    $roll = rand(1, 10);
                    if ($roll <= 2) {
                        $expiresAt = $now->copy()->subHours(rand(1, 72)); // expired
                    } elseif ($roll <= 4) {
                        $usedAt = $createdAt->copy()->addMinutes(rand(10, 240)); // used
                    }
                }

                $rows[] = [
                    'ppdb_application_id' => $app->id,
                    'type'                => $type,
                    'token_hash'          => $hash,
                    'expires_at'          => $expiresAt,
                    'used_at'             => $usedAt,
                    'created_by'          => $admin?->id,
                    'created_at'          => $createdAt,
                    'updated_at'          => $usedAt ?? $createdAt,
                ];
            }
        }

        DB::table('ppdb_tokens')->insert($rows);
    }

    private function makePlainToken(string $type, int $appId): string
    {
        $prefix = $type === 'activation' ? 'act_' : 'edit_';
        return $prefix . $appId . '_' . Str::lower(Str::random(48));
    }
}
