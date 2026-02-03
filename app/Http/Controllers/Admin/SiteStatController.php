<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteStat;
use Illuminate\Http\Request;

class SiteStatController extends Controller
{
    public function index()
    {
        $stats = SiteStat::query()
            ->orderBy('slot')
            ->get();

        return view('admin.site-stats.index', compact('stats'));
    }

    public function edit(SiteStat $siteStat)
    {
        return view('admin.site-stats.edit', compact('siteStat'));
    }

    public function update(Request $request, SiteStat $siteStat)
    {
        $data = $request->validate([
            'value' => ['required', 'integer', 'min:0'],

            'label_id' => ['nullable', 'string', 'max:255'],
            'label_en' => ['nullable', 'string', 'max:255'],
            'label_ar' => ['nullable', 'string', 'max:255'],

            'desc_id' => ['nullable', 'string', 'max:255'],
            'desc_en' => ['nullable', 'string', 'max:255'],
            'desc_ar' => ['nullable', 'string', 'max:255'],

            'is_active' => ['sometimes'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:255'],
        ], [
            'value.required' => 'Angka statistik wajib diisi.',
        ]);

        // checkbox normalization
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = (int) ($request->input('sort_order', 0) ?: 0);

        $siteStat->update($data);

        return back()->with('success', 'Statistik berhasil diperbarui.');
    }
}
