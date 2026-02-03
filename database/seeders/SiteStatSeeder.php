<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SiteStatSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [
                'slot' => 1,
                'value' => 41,
                'label_id' => 'Siswa',
                'label_en' => 'Students',
                'label_ar' => 'طلاب',
                'desc_id'  => 'Yang telah lulus',
                'desc_en'  => 'Graduated',
                'desc_ar'  => 'الذين تخرجوا',
                'sort_order' => 0,
                'is_active' => true,
            ],
            [
                'slot' => 2,
                'value' => 12,
                'label_id' => 'Penghargaan',
                'label_en' => 'Awards',
                'label_ar' => 'جوائز',
                'desc_id'  => 'Kami telah memenangkan berbagai penghargaan bergengsi dalam bidang pendidikan.',
                'desc_en'  => 'We have won various prestigious awards in education.',
                'desc_ar'  => 'لقد فزنا بالعديد من الجوائز المرموقة في مجال التعليم.',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'slot' => 3,
                'value' => 11,
                'label_id' => 'Jam',
                'label_en' => 'Hours',
                'label_ar' => 'ساعات',
                'desc_id'  => 'Kegiatan belajar mengajar.',
                'desc_en'  => 'Teaching and learning activities.',
                'desc_ar'  => 'أنشطة التعليم والتعلّم.',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'slot' => 4,
                'value' => 7,
                'label_id' => 'Program',
                'label_en' => 'Programs',
                'label_ar' => 'برامج',
                'desc_id'  => 'Ekstrakurikuler unggulan.',
                'desc_en'  => 'Featured extracurricular programs.',
                'desc_ar'  => 'برامج لا صفية مميزة.',
                'sort_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($rows as $r) {
            DB::table('site_stats')->updateOrInsert(
                ['slot' => $r['slot']],
                array_merge($r, [
                    'updated_at' => now(),
                    'created_at' => now(),
                ])
            );
        }
    }
}
