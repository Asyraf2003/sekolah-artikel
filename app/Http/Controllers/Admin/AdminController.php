<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\GalleryImage;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;  

class AdminController extends Controller
{
    use AuthorizesRequests;
    
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        $this->authorize('viewAny', User::class);

        $totalSiswa = User::where('role', 'user')->count();
        $totalGuru  = User::where('role', 'other')->count();

        $totalArtikel = Article::count();
        $totalGaleri  = DB::table('gallery_images')->count();

        return view('admin.dashboard', compact(
            'totalSiswa',
            'totalGuru',
            'totalArtikel',
            'totalGaleri'
        ));
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);
        return view('admin.show', compact('user'));
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);
        return view('admin.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|in:admin,user,other',
        ]);

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
