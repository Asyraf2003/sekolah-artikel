<?php

namespace Database\Seeders;

use App\Models\AboutSection;
use Illuminate\Database\Seeder;

class AboutSectionSeeder extends Seeder
{
    public function run(): void
    {
        $about = AboutSection::query()->first() ?? new AboutSection();

        /** ======================
         * INDONESIA
         * ====================== */
        $visionHtmlId = <<<HTML
<h3>Visi</h3>
<p>
  Program Pendidikan Islamic School bertujuan untuk membina generasi
  <span class="yellow-text">Muslim</span> yang memiliki karakter
  <span class="yellow-text">menginspirasi</span>,
  <span class="yellow-text">inovatif</span>, dan
  <span class="yellow-text">berintegritas</span>,
  mampu menjalankan peran mereka sebagai
  <span class="bold-text">'Khalifatullah'</span>
  melalui pengembangan pendidikan
  <span class="yellow-text">holistik</span>.
</p>
HTML;

        $visionDeltaId = [
            'ops' => [
                ['insert' => "Visi\n", 'attributes' => ['header' => 3]],
                ['insert' => "Program Pendidikan Islamic School bertujuan untuk membina generasi "],
                ['insert' => "Muslim", 'attributes' => ['bold' => true]],
                ['insert' => " yang memiliki karakter "],
                ['insert' => "menginspirasi", 'attributes' => ['bold' => true]],
                ['insert' => ", "],
                ['insert' => "inovatif", 'attributes' => ['bold' => true]],
                ['insert' => ", dan "],
                ['insert' => "berintegritas", 'attributes' => ['bold' => true]],
                ['insert' => ", mampu menjalankan peran mereka sebagai "],
                ['insert' => "'Khalifatullah'", 'attributes' => ['bold' => true]],
                ['insert' => " melalui pengembangan pendidikan "],
                ['insert' => "holistik", 'attributes' => ['bold' => true]],
                ['insert' => ".\n"],
            ],
        ];

        $missionHtmlId = <<<HTML
<h3>Misi</h3>
<ul>
  <li>Membentuk generasi Islam berdasarkan Al-Qur’an dan Sunnah Rasulullah SAW.</li>
  <li>Mengembangkan semangat inspirasi dengan wawasan global dan kemampuan berkomunikasi secara sosial, melalui penguasaan lebih dari satu bahasa.</li>
  <li>Menciptakan generasi pembelajar sepanjang hayat yang berpikir kritis dan inovatif.</li>
  <li>Membentuk karakter peduli lingkungan dengan integritas, baik secara lokal maupun global.</li>
</ul>
HTML;

        $missionDeltaId = [
            'ops' => [
                ['insert' => "Misi\n", 'attributes' => ['header' => 3]],

                ['insert' => "Membentuk generasi Islam berdasarkan Al-Qur’an dan Sunnah Rasulullah SAW."],
                ['insert' => "\n", 'attributes' => ['list' => 'bullet']],

                ['insert' => "Mengembangkan semangat inspirasi dengan wawasan global dan kemampuan berkomunikasi secara sosial, melalui penguasaan lebih dari satu bahasa."],
                ['insert' => "\n", 'attributes' => ['list' => 'bullet']],

                ['insert' => "Menciptakan generasi pembelajar sepanjang hayat yang berpikir kritis dan inovatif."],
                ['insert' => "\n", 'attributes' => ['list' => 'bullet']],

                ['insert' => "Membentuk karakter peduli lingkungan dengan integritas, baik secara lokal maupun global."],
                ['insert' => "\n", 'attributes' => ['list' => 'bullet']],
            ],
        ];

        /** ======================
         * ENGLISH (translated)
         * ====================== */
        $visionHtmlEn = <<<HTML
<h3>Vision</h3>
<p>
  The Islamic School Education Program aims to nurture a generation of
  <span class="yellow-text">Muslims</span> with
  <span class="yellow-text">inspiring</span>,
  <span class="yellow-text">innovative</span>, and
  <span class="yellow-text">integrity-driven</span> character, who are able to carry out their role as
  <span class="bold-text">'Khalifatullah'</span>
  through the development of
  <span class="yellow-text">holistic</span> education.
</p>
HTML;

        $visionDeltaEn = [
            'ops' => [
                ['insert' => "Vision\n", 'attributes' => ['header' => 3]],
                ['insert' => "The Islamic School Education Program aims to nurture a generation of "],
                ['insert' => "Muslims", 'attributes' => ['bold' => true]],
                ['insert' => " with "],
                ['insert' => "inspiring", 'attributes' => ['bold' => true]],
                ['insert' => ", "],
                ['insert' => "innovative", 'attributes' => ['bold' => true]],
                ['insert' => ", and "],
                ['insert' => "integrity-driven", 'attributes' => ['bold' => true]],
                ['insert' => " character, who are able to carry out their role as "],
                ['insert' => "'Khalifatullah'", 'attributes' => ['bold' => true]],
                ['insert' => " through the development of "],
                ['insert' => "holistic", 'attributes' => ['bold' => true]],
                ['insert' => " education.\n"],
            ],
        ];

        $missionHtmlEn = <<<HTML
<h3>Mission</h3>
<ul>
  <li>To form a Muslim generation grounded in the Qur’an and the Sunnah of the Prophet Muhammad (peace be upon him).</li>
  <li>To develop an inspiring spirit with global insight and strong social communication skills through mastery of more than one language.</li>
  <li>To cultivate lifelong learners who think critically and innovatively.</li>
  <li>To build environmentally conscious character with integrity, both locally and globally.</li>
</ul>
HTML;

        $missionDeltaEn = [
            'ops' => [
                ['insert' => "Mission\n", 'attributes' => ['header' => 3]],

                ['insert' => "To form a Muslim generation grounded in the Qur’an and the Sunnah of the Prophet Muhammad (peace be upon him)."],
                ['insert' => "\n", 'attributes' => ['list' => 'bullet']],

                ['insert' => "To develop an inspiring spirit with global insight and strong social communication skills through mastery of more than one language."],
                ['insert' => "\n", 'attributes' => ['list' => 'bullet']],

                ['insert' => "To cultivate lifelong learners who think critically and innovatively."],
                ['insert' => "\n", 'attributes' => ['list' => 'bullet']],

                ['insert' => "To build environmentally conscious character with integrity, both locally and globally."],
                ['insert' => "\n", 'attributes' => ['list' => 'bullet']],
            ],
        ];

        /** ======================
         * ARABIC (translated)
         * ====================== */
        $visionHtmlAr = <<<HTML
<h3>الرؤية</h3>
<p>
  يهدف برنامج التعليم في المدرسة الإسلامية إلى إعداد جيل من
  <span class="yellow-text">المسلمين</span> يتمتع بشخصية
  <span class="yellow-text">مُلهمة</span>،
  <span class="yellow-text">مُبتكرة</span>، وذات
  <span class="yellow-text">نزاهة</span>، قادر على القيام بدوره بوصفه
  <span class="bold-text">'Khalifatullah'</span>
  من خلال تطوير تعليم
  <span class="yellow-text">شمولي</span>.
</p>
HTML;

        $visionDeltaAr = [
            'ops' => [
                ['insert' => "الرؤية\n", 'attributes' => ['header' => 3]],
                ['insert' => "يهدف برنامج التعليم في المدرسة الإسلامية إلى إعداد جيل من "],
                ['insert' => "المسلمين", 'attributes' => ['bold' => true]],
                ['insert' => " يتمتع بشخصية "],
                ['insert' => "مُلهمة", 'attributes' => ['bold' => true]],
                ['insert' => "، "],
                ['insert' => "مُبتكرة", 'attributes' => ['bold' => true]],
                ['insert' => "، وذات "],
                ['insert' => "نزاهة", 'attributes' => ['bold' => true]],
                ['insert' => "، قادر على القيام بدوره بوصفه "],
                ['insert' => "'Khalifatullah'", 'attributes' => ['bold' => true]],
                ['insert' => " من خلال تطوير تعليم "],
                ['insert' => "شمولي", 'attributes' => ['bold' => true]],
                ['insert' => ".\n"],
            ],
        ];

        $missionHtmlAr = <<<HTML
<h3>الرسالة</h3>
<ul>
  <li>تكوين جيلٍ مسلمٍ على أساس القرآن الكريم وسنّة رسول الله ﷺ.</li>
  <li>تنمية روح الإلهام برؤية عالمية ومهارات تواصل اجتماعي قوية من خلال إتقان أكثر من لغة واحدة.</li>
  <li>إعداد متعلمين مدى الحياة يفكرون تفكيرًا نقديًا وإبداعيًا.</li>
  <li>بناء شخصية واعية بالبيئة تتصف بالنزاهة، محليًا وعالميًا.</li>
</ul>
HTML;

        $missionDeltaAr = [
            'ops' => [
                ['insert' => "الرسالة\n", 'attributes' => ['header' => 3]],

                ['insert' => "تكوين جيلٍ مسلمٍ على أساس القرآن الكريم وسنّة رسول الله ﷺ."],
                ['insert' => "\n", 'attributes' => ['list' => 'bullet']],

                ['insert' => "تنمية روح الإلهام برؤية عالمية ومهارات تواصل اجتماعي قوية من خلال إتقان أكثر من لغة واحدة."],
                ['insert' => "\n", 'attributes' => ['list' => 'bullet']],

                ['insert' => "إعداد متعلمين مدى الحياة يفكرون تفكيرًا نقديًا وإبداعيًا."],
                ['insert' => "\n", 'attributes' => ['list' => 'bullet']],

                ['insert' => "بناء شخصية واعية بالبيئة تتصف بالنزاهة، محليًا وعالميًا."],
                ['insert' => "\n", 'attributes' => ['list' => 'bullet']],
            ],
        ];

        /** ======================
         * SAVE
         * ====================== */
        $about->fill([
            'vision_content_html_id'   => $visionHtmlId,
            'vision_content_delta_id'  => $visionDeltaId,
            'vision_content_html_en'   => $visionHtmlEn,
            'vision_content_delta_en'  => $visionDeltaEn,
            'vision_content_html_ar'   => $visionHtmlAr,
            'vision_content_delta_ar'  => $visionDeltaAr,

            'mission_content_html_id'  => $missionHtmlId,
            'mission_content_delta_id' => $missionDeltaId,
            'mission_content_html_en'  => $missionHtmlEn,
            'mission_content_delta_en' => $missionDeltaEn,
            'mission_content_html_ar'  => $missionHtmlAr,
            'mission_content_delta_ar' => $missionDeltaAr,
        ]);

        $about->save();
    }
}
