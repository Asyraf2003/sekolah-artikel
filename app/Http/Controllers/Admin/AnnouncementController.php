<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $query = Announcement::query()->withTrashed();

        // published filter: '' | '1' | '0'
        $published = (string) $request->query('published', '');
        if ($published === '1') $query->where('is_published', true);
        if ($published === '0') $query->where('is_published', false);

        // search
        $term = trim((string) $request->query('q', ''));
        if ($term !== '') {
            $query->where(function ($qq) use ($term) {
                $qq->where('title_id', 'like', "%{$term}%")
                    ->orWhere('title_en', 'like', "%{$term}%")
                    ->orWhere('title_ar', 'like', "%{$term}%")
                    ->orWhere('desc_id',  'like', "%{$term}%")
                    ->orWhere('desc_en',  'like', "%{$term}%")
                    ->orWhere('desc_ar',  'like', "%{$term}%");
            });
        }

        // sorting
        $sort = (string) $request->query('sort', 'latest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at');
                break;

            case 'event_oldest':
                $query->orderBy('event_date')->orderBy('sort_order');
                break;

            case 'event_latest':
                $query->orderByDesc('event_date')->orderBy('sort_order');
                break;

            case 'title_asc':
                $query->orderBy('title_id')->orderByDesc('event_date')->orderBy('sort_order');
                break;

            case 'title_desc':
                $query->orderByDesc('title_id')->orderByDesc('event_date')->orderBy('sort_order');
                break;

            case 'latest':
            default:
                $query->orderByDesc('created_at');
                break;
        }

        $announcements = $query->paginate(6)->withQueryString();

        return view('admin.announcements.index', compact('announcements'));
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title_id' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'title_ar' => ['nullable', 'string', 'max:255'],

            'desc_id' => ['nullable', 'string'],
            'desc_en' => ['nullable', 'string'],
            'desc_ar' => ['nullable', 'string'],

            'event_date' => ['required', 'date'],

            'link_url' => ['nullable', 'string', 'max:2048'],

            // checkbox: jangan boolean langsung karena bisa "on"
            'is_published' => ['sometimes'],

            'published_at' => ['nullable', 'date'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ], [
            'title_id.required' => 'Judul (ID) wajib diisi.',
            'event_date.required' => 'Tanggal pengumuman wajib diisi.',
        ]);

        // Normalisasi checkbox (ini yang bener-bener aman)
        $data['is_published'] = $request->boolean('is_published');

        // Normalisasi sort_order biar ga jadi null
        $data['sort_order'] = (int) ($request->input('sort_order', 0) ?: 0);

        return $data;
    }

    public function create()
    {
        $announcement = new Announcement([
            'is_published' => true,
            'event_date' => now()->toDateString(),
            'sort_order' => 0,
        ]);

        return view('admin.announcements.create', compact('announcement'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        // published_at auto
        if ($data['is_published'] && empty($data['published_at'])) {
            $data['published_at'] = now();
        }
        if (!$data['is_published']) {
            $data['published_at'] = null;
        }

        $announcement = Announcement::create($data);

        return redirect()
            ->route('admin.announcements.edit', $announcement->id)
            ->with('success', 'Pengumuman berhasil dibuat.');
    }

    // NOTE: pake $id supaya trashed juga bisa kebuka
    public function edit(int $id)
    {
        $announcement = Announcement::withTrashed()->findOrFail($id);

        return view('admin.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, int $id)
    {
        $announcement = Announcement::withTrashed()->findOrFail($id);
        $data = $this->validated($request);

        if ($data['is_published'] && empty($data['published_at'])) {
            $data['published_at'] = now();
        }
        if (!$data['is_published']) {
            $data['published_at'] = null;
        }

        $announcement->update($data);

        return back()->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $announcement = Announcement::withTrashed()->findOrFail($id);
        $announcement->delete();

        return back()->with('success', 'Pengumuman berhasil dihapus.');
    }

    // Opsional: restore
    public function restore(int $id)
    {
        $announcement = Announcement::withTrashed()->findOrFail($id);
        $announcement->restore();

        return back()->with('success', 'Pengumuman berhasil dipulihkan.');
    }

    // Opsional: hapus permanen
    public function forceDestroy(int $id)
    {
        $announcement = Announcement::withTrashed()->findOrFail($id);
        $announcement->forceDelete();

        return back()->with('success', 'Pengumuman berhasil dihapus permanen.');
    }
}
