<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class ProgramController extends Controller
{
    public function index(Request $request)
    {
        $query = Program::query()->withTrashed();

        // published filter: '' | '1' | '0'
        $published = (string) $request->query('published', '');
        if ($published === '1') $query->where('is_published', true);
        if ($published === '0') $query->where('is_published', false);

        // search by title/desc in any language
        $term = trim((string) $request->query('q', ''));
        if ($term !== '') {
            $query->where(function (Builder $qq) use ($term) {
                $qq->where('title_id', 'like', "%{$term}%")
                    ->orWhere('title_en', 'like', "%{$term}%")
                    ->orWhere('title_ar', 'like', "%{$term}%")
                    ->orWhere('desc_id',  'like', "%{$term}%")
                    ->orWhere('desc_en',  'like', "%{$term}%")
                    ->orWhere('desc_ar',  'like', "%{$term}%");
            });
        }

        // sorting
        $sort = (string) $request->query('sort', 'ordered');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at');
                break;

            case 'title_asc':
                $query->orderBy('title_id')->orderBy('sort_order')->orderBy('id');
                break;

            case 'title_desc':
                $query->orderByDesc('title_id')->orderBy('sort_order')->orderBy('id');
                break;

            case 'latest':
                $query->orderByDesc('created_at');
                break;

            case 'ordered':
            default:
                $query->orderBy('sort_order')->orderBy('id');
                break;
        }

        $programs = $query->paginate(10)->withQueryString();

        return view('admin.programs.index', compact('programs'));
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

            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['sometimes'],
        ], [
            'title_id.required' => 'Judul (ID) wajib diisi.',
        ]);

        $data['is_published'] = $request->boolean('is_published');
        $data['sort_order'] = (int) ($request->input('sort_order', 0) ?: 0);

        return $data;
    }

    public function create()
    {
        $program = new Program([
            'is_published' => true,
            'sort_order' => 0,
        ]);

        return view('admin.programs.create', compact('program'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $program = Program::create($data);

        return redirect()
            ->route('admin.programs.edit', $program->id)
            ->with('success', 'Program berhasil dibuat.');
    }

    // pakai int $id biar trashed bisa diedit juga (konsisten sama announcement kamu)
    public function edit(int $id)
    {
        $program = Program::withTrashed()->findOrFail($id);

        return view('admin.programs.edit', compact('program'));
    }

    public function update(Request $request, int $id)
    {
        $program = Program::withTrashed()->findOrFail($id);
        $data = $this->validated($request);

        $program->update($data);

        return back()->with('success', 'Program berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $program = Program::withTrashed()->findOrFail($id);
        $program->delete();

        return back()->with('success', 'Program berhasil dihapus.');
    }

    // Opsional: restore & force delete
    public function restore(int $id)
    {
        $program = Program::withTrashed()->findOrFail($id);
        $program->restore();

        return back()->with('success', 'Program berhasil dipulihkan.');
    }

    public function forceDestroy(int $id)
    {
        $program = Program::withTrashed()->findOrFail($id);
        $program->forceDelete();

        return back()->with('success', 'Program berhasil dihapus permanen.');
    }
}
