<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\{Article, Category, Tag, User};

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $userIds = User::pluck('id')->all();
        $catIds  = Category::pluck('id')->all();
        $tagIds  = Tag::pluck('id')->all();

        $total = 12;

        for ($i = 1; $i <= $total; $i++) {
            $titleId = $this->judulSekolah($i);
            $titleEn = 'Sample School Article ' . $i;
            $titleAr = 'مقالة مدرسية رقم ' . $i;

            // draft / published / archived
            $status = 'published';
            if ($i % 10 === 0) $status = 'archived';
            if ($i % 6 === 0)  $status = 'draft';

            // scheduled implicit: status published + published_at future
            $publishedAt = null;
            if ($status === 'published') {
                $isScheduled = ($i % 7 === 0);
                $publishedAt = $isScheduled
                    ? Carbon::now()->addDays(rand(1, 7))
                    : Carbon::now()->subDays(rand(0, 10));
            }

            $isFeatured  = ($i % 3 === 0);
            $pinnedUntil = ($i % 4 === 0) ? Carbon::now()->addDays(rand(2, 7)) : null;

            $delta = $this->makeQuillDelta($titleId);
            $html  = $this->renderDeltaToHtml($delta);

            $article = Article::create([
                'author_id'     => !empty($userIds) ? $userIds[array_rand($userIds)] : null,

                'title_id'      => $titleId,
                'title_en'      => $titleEn,
                'title_ar'      => $titleAr,

                // biarin kosong: auto-generated oleh model
                'slug'          => null,

                'hero_image'    => 'article/gambar' . (($i - 1) % 6 + 1) . '.jpg',

                'excerpt_id'    => 'Ringkasan singkat: ' . $this->sentence(10),
                'excerpt_en'    => $this->sentence(10),
                'excerpt_ar'    => 'ملخص قصير: ' . $this->sentence(10),

                'content_delta' => $delta,
                'content_html'  => $html,

                'status'        => $status,
                'published_at'  => $publishedAt,

                'is_featured'   => $isFeatured,
                'pinned_until'  => $pinnedUntil,

                'view_count'    => rand(50, 5000),
                'comment_count' => 0,
                'share_count'   => rand(0, 200),
                'reading_time'  => $this->computeReadingMinutesFromDelta($delta),
            ]);

            if (!empty($catIds)) {
                shuffle($catIds);
                $article->categories()->sync(array_slice($catIds, 0, rand(1, min(2, count($catIds)))));
            }

            if (!empty($tagIds)) {
                shuffle($tagIds);
                $article->tags()->sync(array_slice($tagIds, 0, rand(2, min(4, count($tagIds)))));
            }
        }

        $this->refreshTagUseCount();
    }

    private function judulSekolah(int $i): string
    {
        $judul = [
            'Kegiatan Ekstrakurikuler Paskibra',
            'Tips Lolos OSN Matematika',
            'PPDB Tahun Ajaran Baru',
            'Kompetisi Robotik Nasional',
            'Beasiswa Unggulan Semester Ini',
            'Gerakan Literasi Sekolah',
            'Pelatihan Coding untuk Siswa',
            'Turnamen Futsal Antar Kelas',
            'Pameran Seni Rupa',
            'Prakarya Sains Sederhana',
            'Workshop Kesehatan Remaja',
            'Kunjungan Industri Teknologi'
        ];
        return $judul[($i - 1) % count($judul)];
    }

    private function makeQuillDelta(string $title): array
    {
        $ops = [];

        $ops[] = ['insert' => $title];
        $ops[] = ['insert' => "\n", 'attributes' => ['header' => 2]];
        $ops[] = ['insert' => "\n"];

        $paraCount = rand(3, 5);
        for ($k = 0; $k < $paraCount; $k++) {
            $ops[] = ['insert' => $this->paragraph(rand(2, 4)) . "\n"];
            $ops[] = ['insert' => "\n"];
        }

        $ops[] = ['insert' => "Catatan: konten ini dummy buat testing Quill, bukan wahyu.\n", 'attributes' => ['blockquote' => true]];
        $ops[] = ['insert' => "\n"];

        $ops[] = ['insert' => "Poin penting 1\n", 'attributes' => ['list' => 'bullet']];
        $ops[] = ['insert' => "Poin penting 2\n", 'attributes' => ['list' => 'bullet']];
        $ops[] = ['insert' => "Poin penting 3\n", 'attributes' => ['list' => 'bullet']];

        return ['ops' => $ops];
    }

    private function renderDeltaToHtml(array $delta): string
    {
        $ops = $delta['ops'] ?? [];
        $html = '';
        $buffer = '';
        $ulOpen = false;

        $flushP = function () use (&$html, &$buffer, &$ulOpen) {
            $text = trim($buffer);
            if ($text !== '') {
                if ($ulOpen) { $html .= '</ul>'; $ulOpen = false; }
                $html .= '<p>' . e($text) . '</p>';
            }
            $buffer = '';
        };

        foreach ($ops as $op) {
            $insert = $op['insert'] ?? '';
            $attr   = $op['attributes'] ?? [];

            if ($insert === "\n") {
                if (isset($attr['header'])) {
                    $level = max(1, min(6, (int)$attr['header']));
                    $text = trim($buffer);
                    if ($text !== '') {
                        if ($ulOpen) { $html .= '</ul>'; $ulOpen = false; }
                        $html .= "<h{$level}>" . e($text) . "</h{$level}>";
                    }
                    $buffer = '';
                    continue;
                }

                if (!empty($attr['blockquote'])) {
                    $text = trim($buffer);
                    if ($text !== '') {
                        if ($ulOpen) { $html .= '</ul>'; $ulOpen = false; }
                        $html .= '<blockquote>' . e($text) . '</blockquote>';
                    }
                    $buffer = '';
                    continue;
                }

                if (($attr['list'] ?? null) === 'bullet') {
                    $text = trim($buffer);
                    if ($text !== '') {
                        if (!$ulOpen) { $html .= '<ul>'; $ulOpen = true; }
                        $html .= '<li>' . e($text) . '</li>';
                    }
                    $buffer = '';
                    continue;
                }

                $flushP();
                continue;
            }

            if (is_string($insert)) $buffer .= $insert;
        }

        $flushP();
        if ($ulOpen) $html .= '</ul>';

        return $html;
    }

    private function computeReadingMinutesFromDelta(array $delta): int
    {
        $ops = $delta['ops'] ?? [];
        $text = '';

        foreach ($ops as $op) {
            $insert = $op['insert'] ?? '';
            if (is_string($insert)) $text .= ' ' . $insert;
        }

        $text = str_replace("\n", ' ', $text);
        $words = str_word_count($text);
        $wpm = 220;

        return max(1, (int)ceil($words / $wpm));
    }

    private function refreshTagUseCount(): void
    {
        $rows = DB::table('article_tag')
            ->select('tag_id', DB::raw('COUNT(*) as c'))
            ->groupBy('tag_id')
            ->pluck('c', 'tag_id');

        foreach ($rows as $tagId => $count) {
            DB::table('tags')->where('id', $tagId)->update(['use_count' => $count]);
        }
    }

    // ---- tiny text generator (tanpa Faker) ----

    private function sentence(int $words = 10): string
    {
        $pool = $this->wordPool();
        shuffle($pool);
        $words = max(3, min($words, count($pool)));
        $s = implode(' ', array_slice($pool, 0, $words));
        return ucfirst($s) . '.';
    }

    private function paragraph(int $sentences = 3): string
    {
        $sentences = max(1, $sentences);
        $parts = [];
        for ($i = 0; $i < $sentences; $i++) {
            $parts[] = $this->sentence(rand(8, 14));
        }
        return implode(' ', $parts);
    }

    private function wordPool(): array
    {
        return [
            'sekolah','siswa','guru','belajar','program','kegiatan','prestasi','kompetisi','pelatihan','literasi',
            'teknologi','kelas','kurikulum','projek','kolaborasi','inovasi','kreatif','komunitas','disiplin','motivasi',
            'pembelajaran','praktik','materi','evaluasi','pengembangan','minat','bakat','olahraga','seni','sains',
        ];
    }
}
