<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class PpdbApplicationSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan ada "admin" buat verified_by / created_by token.
        $admin = User::query()->where('email', 'admin@example.com')->first();

        if (!$admin) {
            $admin = User::query()->first();
        }

        if (!$admin) {
            // Kalau bener-bener ga ada user sama sekali (harusnya tidak, tapi ya manusia).
            $admin = User::create([
                'name' => 'Admin Seeder',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
            ]);
        }

        $now = now();

        $samples = [
            ['full_name' => 'Bintang Pratama',  'email' => 'bintang.pratama',  'whatsapp' => '628121000001'],
            ['full_name' => 'Nadia Safitri',    'email' => 'nadia.safitri',    'whatsapp' => '628121000002'],
            ['full_name' => 'Fajar Ramadhan',   'email' => 'fajar.ramadhan',   'whatsapp' => '628121000003'],
            ['full_name' => 'Aulia Rahma',      'email' => 'aulia.rahma',      'whatsapp' => '628121000004'],
            ['full_name' => 'Dimas Saputra',    'email' => 'dimas.saputra',    'whatsapp' => '628121000005'],
            ['full_name' => 'Rani Wulandari',   'email' => 'rani.wulandari',   'whatsapp' => '628121000006'],
            ['full_name' => 'Arif Nugroho',     'email' => 'arif.nugroho',     'whatsapp' => '628121000007'],
            ['full_name' => 'Salsa Maharani',   'email' => 'salsa.maharani',   'whatsapp' => '628121000008'],
            ['full_name' => 'Bayu Setiawan',    'email' => 'bayu.setiawan',    'whatsapp' => '628121000009'],
            ['full_name' => 'Tika Apriliyani',  'email' => 'tika.apriliyani',  'whatsapp' => '628121000010'],
        ];

        $rows = [];

        foreach ($samples as $idx => $s) {
            $publicCode = $this->makePublicCode();

            // status: submitted/approved/rejected/activated
            $status = match (true) {
                $idx % 7 === 0 => 'rejected',
                $idx % 5 === 0 => 'approved',
                $idx % 3 === 0 => 'activated',
                default        => 'submitted',
            };

            $createdAt = $now->copy()->subDays(rand(0, 14))->subHours(rand(0, 23));

            $verifiedAt = null;
            $verifiedBy = null;
            $rejectedReason = null;
            $userId = null;

            if (in_array($status, ['approved', 'rejected', 'activated'], true)) {
                $verifiedAt = $createdAt->copy()->addHours(rand(2, 48));
                $verifiedBy = $admin->id;
            }

            if ($status === 'rejected') {
                $rejectedReason = $this->randomRejectedReason();
            }

            if ($status === 'activated') {
                // Sinkron: activated harus punya user yang "dibentuk" dari aplikasi.
                // Email user dibuat unik supaya ga bentrok sama UserSeeder.
                $userEmail = "ppdb.{$publicCode}@ppdb-user.test";

                $user = User::firstOrCreate(
                    ['email' => $userEmail],
                    [
                        'name' => $s['full_name'],
                        'password' => Hash::make('password'),
                    ]
                );

                $userId = $user->id;
            }

            $rows[] = [
                'public_code'         => $publicCode,
                'full_name'           => $s['full_name'],
                'email'               => $s['email'] . '@example.com',
                'whatsapp'            => $s['whatsapp'],
                'payment_proof_path'  => 'ppdb_private/payments/' . $this->fakeFileName($s['full_name']),
                'status'              => $status,
                'rejected_reason'     => $rejectedReason,
                'verified_at'         => $verifiedAt,
                'verified_by'         => $verifiedBy,
                'user_id'             => $userId,
                'created_at'          => $createdAt,
                'updated_at'          => $verifiedAt ?? $createdAt,
            ];
        }

        DB::table('ppdb_applications')->insert($rows);
    }

    private function makePublicCode(): string
    {
        return Str::lower(Str::random(32));
    }

    private function fakeFileName(string $name): string
    {
        $slug = Str::slug($name);
        return $slug . '-' . Str::lower(Str::random(6)) . '.jpg';
    }

    private function randomRejectedReason(): string
    {
        $reasons = [
            'Bukti pembayaran tidak jelas atau tidak terbaca.',
            'Data pendaftar tidak sesuai dengan bukti pembayaran.',
            'Nomor WhatsApp tidak valid atau tidak bisa dihubungi.',
            'Email sudah digunakan pada pendaftaran lain.',
        ];
        return $reasons[array_rand($reasons)];
    }
}
