<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Extracurricular;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class ExtracurricularController extends Controller
{
    public function index(Request $request)
    {
        $query = Extracurricular::query()->withTrashed();

        // published filter: '' | '1' | '0'
        $published = (string) $request->query('published', '');
        if ($published === '1') $query->where('is_published', true);
        if ($published === '0') $query->where('is_published', false);

        // search
        $term = trim((string) $request->query('q', ''));
        if ($term !== '') {
            $query->where(function (Builder $qq) use ($term) {
                $qq->where('name_id', 'like', "%{$term}%")
                    ->orWhere('name_en', 'like', "%{$term}%")
                    ->orWhere('name_ar', 'like', "%{$term}%");
            });
        }

        // sorting
        $sort = (string) $request->query('sort', 'ordered');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at');
                break;

            case 'name_asc':
                $query->orderBy('name_id')->orderBy('sort_order')->orderBy('id');
                break;

            case 'name_desc':
                $query->orderByDesc('name_id')->orderBy('sort_order')->orderBy('id');
                break;

            case 'latest':
                $query->orderByDesc('created_at');
                break;

            case 'ordered':
            default:
                $query->orderBy('sort_order')->orderBy('id');
                break;
        }

        $extracurriculars = $query->paginate(10)->withQueryString();

        return view('admin.extracurriculars.index', compact('extracurriculars'));
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name_id' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'name_ar' => ['nullable', 'string', 'max:255'],

            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['sometimes'],
        ], [
            'name_id.required' => 'Nama (ID) wajib diisi.',
        ]);

        $data['is_published'] = $request->boolean('is_published');
        $data['sort_order'] = (int) ($request->input('sort_order', 0) ?: 0);

        return $data;
    }

    public function create()
    {
        $extracurricular = new Extracurricular([
            'is_published' => true,
            'sort_order' => 0,
        ]);

        return view('admin.extracurriculars.create', compact('extracurricular'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $extracurricular = Extracurricular::create($data);

        return redirect()
            ->route('admin.extracurriculars.edit', $extracurricular->id)
            ->with('success', 'Ekstrakurikuler berhasil dibuat.');
    }

    // pakai int biar bisa edit trashed juga
    public function edit(int $id)
    {
        $extracurricular = Extracurricular::withTrashed()->findOrFail($id);

        return view('admin.extracurriculars.edit', compact('extracurricular'));
    }

    public function update(Request $request, int $id)
    {
        $extracurricular = Extracurricular::withTrashed()->findOrFail($id);
        $data = $this->validated($request);

        $extracurricular->update($data);

        return back()->with('success', 'Ekstrakurikuler berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $extracurricular = Extracurricular::withTrashed()->findOrFail($id);
        $extracurricular->delete();

        return back()->with('success', 'Ekstrakurikuler berhasil dihapus.');
    }

    // opsional (kalau kamu pasang routenya)
    public function restore(int $id)
    {
        $extracurricular = Extracurricular::withTrashed()->findOrFail($id);
        $extracurricular->restore();

        return back()->with('success', 'Ekstrakurikuler berhasil dipulihkan.');
    }

    public function forceDestroy(int $id)
    {
        $extracurricular = Extracurricular::withTrashed()->findOrFail($id);
        $extracurricular->forceDelete();

        return back()->with('success', 'Ekstrakurikuler berhasil dihapus permanen.');
    }
}
