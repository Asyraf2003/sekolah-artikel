<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutSection;
use Illuminate\Http\Request;

class AboutSectionController extends Controller
{
    public function edit()
    {
        $about = AboutSection::singleton();

        return view('admin.about.edit', compact('about'));
    }

    public function update(Request $request, AboutSection $aboutSection)
    {
        // Validasi minimal: boleh kosong, tapi kalau ada harus string
        $data = $request->validate([
            'vision_content_html_id'   => ['nullable', 'string'],
            'vision_content_html_en'   => ['nullable', 'string'],
            'vision_content_html_ar'   => ['nullable', 'string'],
            'vision_content_delta_id'  => ['nullable', 'string'], // JSON string dari hidden input
            'vision_content_delta_en'  => ['nullable', 'string'],
            'vision_content_delta_ar'  => ['nullable', 'string'],

            'mission_content_html_id'  => ['nullable', 'string'],
            'mission_content_html_en'  => ['nullable', 'string'],
            'mission_content_html_ar'  => ['nullable', 'string'],
            'mission_content_delta_id' => ['nullable', 'string'],
            'mission_content_delta_en' => ['nullable', 'string'],
            'mission_content_delta_ar' => ['nullable', 'string'],
        ]);

        // Convert delta JSON string -> array, supaya cocok dengan casts di model
        foreach ([
            'vision_content_delta_id','vision_content_delta_en','vision_content_delta_ar',
            'mission_content_delta_id','mission_content_delta_en','mission_content_delta_ar',
        ] as $key) {
            if (!array_key_exists($key, $data)) continue;

            $raw = trim((string) ($data[$key] ?? ''));
            if ($raw === '') {
                $data[$key] = null;
                continue;
            }

            $decoded = json_decode($raw, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()
                    ->withErrors([$key => 'Delta Quill tidak valid (JSON error).'])
                    ->withInput();
            }

            $data[$key] = $decoded;
        }

        $aboutSection->fill($data)->save();

        return back()->with('success', 'About berhasil diperbarui.');
    }
}
