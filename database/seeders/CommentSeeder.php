<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Article, Comment, User};

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        $articles = Article::published()->get();
        $userIds  = User::pluck('id')->all();

        foreach ($articles as $article) {
            $count = rand(2, 6);
            $approvedCount = 0;

            for ($i = 0; $i < $count; $i++) {
                $useUser = (!empty($userIds) && rand(0, 1) === 1);
                $status  = (rand(0, 4) ? 'approved' : 'pending');

                $comment = Comment::create([
                    'article_id'  => $article->id,
                    'user_id'     => $useUser ? $userIds[array_rand($userIds)] : null,
                    'guest_name'  => $useUser ? null : $this->guestName(),
                    'guest_email' => $useUser ? null : $this->guestEmail(),
                    'parent_id'   => null,
                    'body'        => $this->sentence(rand(8, 16)),
                    'status'      => $status,
                    'ip'          => $this->ipv4(),
                    'user_agent'  => 'Seeder',
                ]);

                if ($comment->status === 'approved') $approvedCount++;

                // Reply (50% chance)
                if (rand(0, 1) === 1) {
                    $replyStatus = (rand(0, 4) ? 'approved' : 'pending');

                    $reply = Comment::create([
                        'article_id'  => $article->id,
                        'user_id'     => null,
                        'guest_name'  => $this->guestName(),
                        'guest_email' => $this->guestEmail(),
                        'parent_id'   => $comment->id,
                        'body'        => $this->sentence(rand(6, 12)),
                        'status'      => $replyStatus,
                        'ip'          => $this->ipv4(),
                        'user_agent'  => 'Seeder',
                    ]);

                    if ($reply->status === 'approved') $approvedCount++;
                }
            }

            $article->update(['comment_count' => $approvedCount]);
        }
    }

    // ---- helpers (tanpa Faker) ----

    private function guestName(): string
    {
        $names = ['Rani', 'Dimas', 'Aulia', 'Fajar', 'Nadia', 'Rizky', 'Tika', 'Bayu', 'Salsa', 'Arif'];
        return $names[array_rand($names)];
    }

    private function guestEmail(): string
    {
        $domains = ['example.com', 'mail.test', 'demo.local', 'school.id'];
        $user = strtolower($this->guestName()) . rand(10, 999);
        return $user . '@' . $domains[array_rand($domains)];
    }

    private function ipv4(): string
    {
        return rand(1, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . rand(1, 254);
    }

    private function sentence(int $words): string
    {
        $pool = [
            'artikel','ini','bagus','jelas','membantu','siswa','guru','sekolah','belajar','materi','contoh','praktik',
            'kegiatan','program','prestasi','pelatihan','kompetisi','literasi','teknologi','kelas','projek','inovasi',
        ];

        shuffle($pool);
        $words = max(4, min($words, count($pool)));
        $s = implode(' ', array_slice($pool, 0, $words));
        return ucfirst($s) . '.';
    }
}
