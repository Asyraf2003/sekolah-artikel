<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            AboutSectionSeeder::class,
            GalleryImageSeeder::class,

            ArticleSeeder::class,
            TagSeeder::class,
            CategorySeeder::class,
            CommentSeeder::class,

            HotInfoSeeder::class,
            AnnouncementSeeder::class,
            SiteStatSeeder::class,
            ProgramSeeder::class,
            ExtracurricularSeeder::class,
            EventSeeder::class,

            // GANTI INI:
            PpdbApplicationSeeder::class,
            PpdbTokenSeeder::class,
        ]);
    }
}
