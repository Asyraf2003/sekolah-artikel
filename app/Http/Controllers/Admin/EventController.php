<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::query()->withTrashed();

        // published filter: '' | '1' | '0'
        $published = (string) $request->query('published', '');
        if ($published === '1') $query->where('is_published', true);
        if ($published === '0') $query->where('is_published', false);

        // time filter: '' | 'upcoming' | 'past'
        $time = (string) $request->query('time', '');
        if ($time === 'upcoming') $query->where('event_date', '>=', now());
        if ($time === 'past')     $query->where('event_date', '<', now());

        // search: title/place in all languages
        $term = trim((string) $request->query('q', ''));
        if ($term !== '') {
            $query->where(function (Builder $qq) use ($term) {
                $qq->where('title_id', 'like', "%{$term}%")
                    ->orWhere('title_en', 'like', "%{$term}%")
                    ->orWhere('title_ar', 'like', "%{$term}%")
                    ->orWhere('place_id', 'like', "%{$term}%")
                    ->orWhere('place_en', 'like', "%{$term}%")
                    ->orWhere('place_ar', 'like', "%{$term}%");
            });
        }

        // sorting
        $sort = (string) $request->query('sort', 'event_upcoming');
        switch ($sort) {
            case 'event_past': // terbaru (paling baru) dulu
                $query->orderByDesc('event_date')->orderBy('sort_order')->orderBy('id');
                break;

            case 'event_upcoming': // event terdekat dulu
                $query->orderBy('event_date')->orderBy('sort_order')->orderBy('id');
                break;

            case 'title_asc':
                $query->orderBy('title_id')->orderBy('event_date')->orderBy('sort_order')->orderBy('id');
                break;

            case 'title_desc':
                $query->orderByDesc('title_id')->orderBy('event_date')->orderBy('sort_order')->orderBy('id');
                break;

            case 'latest':
                $query->orderByDesc('created_at');
                break;

            case 'oldest':
                $query->orderBy('created_at');
                break;

            default:
                $query->orderBy('event_date')->orderBy('sort_order')->orderBy('id');
                break;
        }

        $events = $query->paginate(10)->withQueryString();

        return view('admin.events.index', compact('events'));
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title_id' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'title_ar' => ['nullable', 'string', 'max:255'],

            'place_id' => ['nullable', 'string', 'max:255'],
            'place_en' => ['nullable', 'string', 'max:255'],
            'place_ar' => ['nullable', 'string', 'max:255'],

            // datetime-local biasanya format: Y-m-d\TH:i
            'event_date' => ['required', 'date'],

            'link_url' => ['nullable', 'string', 'max:2048'],

            'is_published' => ['sometimes'],
            'sort_order'   => ['nullable', 'integer', 'min:0'],
        ], [
            'title_id.required'  => 'Judul (ID) wajib diisi.',
            'event_date.required'=> 'Tanggal & waktu event wajib diisi.',
        ]);

        $data['is_published'] = $request->boolean('is_published');
        $data['sort_order'] = (int) ($request->input('sort_order', 0) ?: 0);

        return $data;
    }

    public function create()
    {
        $event = new Event([
            'is_published' => true,
            'sort_order' => 0,
            // default: sekarang + 1 jam biar ga “di masa lalu”
            'event_date' => now()->addHour(),
        ]);

        return view('admin.events.create', compact('event'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $event = Event::create($data);

        return redirect()
            ->route('admin.events.edit', $event->id)
            ->with('success', 'Event berhasil dibuat.');
    }

    // int $id biar trashed bisa diedit juga
    public function edit(int $id)
    {
        $event = Event::withTrashed()->findOrFail($id);

        return view('admin.events.edit', compact('event'));
    }

    public function update(Request $request, int $id)
    {
        $event = Event::withTrashed()->findOrFail($id);
        $data = $this->validated($request);

        $event->update($data);

        return back()->with('success', 'Event berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $event = Event::withTrashed()->findOrFail($id);
        $event->delete();

        return back()->with('success', 'Event berhasil dihapus.');
    }

    // opsional (kalau kamu pasang routenya)
    public function restore(int $id)
    {
        $event = Event::withTrashed()->findOrFail($id);
        $event->restore();

        return back()->with('success', 'Event berhasil dipulihkan.');
    }

    public function forceDestroy(int $id)
    {
        $event = Event::withTrashed()->findOrFail($id);
        $event->forceDelete();

        return back()->with('success', 'Event berhasil dihapus permanen.');
    }
}
