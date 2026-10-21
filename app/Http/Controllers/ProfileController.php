<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ProfileController extends Controller
{
    private array $jenisList = ['Road', 'Gravel', 'Mountain'];

    // GET /profile
    public function index()
    {
        $user = User::find(Auth::id());

        $profile = [
            'nama'         => $user->name,
            'umur'         => $user->age,
            'jenis_sepeda' => $this->jenisList,
            'pilihan'      => $user->bike_type,
        ];

        return view('profile', $profile);
    }

    // POST /profile — simpan perubahan
    public function store(Request $request)
    {
        $request->validate([
            'nama'         => 'required|string|max:100',
            'umur'         => 'required|integer|min:1|max:120',
            'jenis_sepeda' => 'required|in:' . implode(',', $this->jenisList),
        ]);

        $user = User::find(Auth::id());
        $user->name      = $request->input('nama');
        $user->age       = (int) $request->input('umur');
        $user->bike_type = $request->input('jenis_sepeda');
        $user->save();

        $profile = [
            'nama'         => $user->name,
            'umur'         => $user->age,
            'jenis_sepeda' => $this->jenisList,
            'pilihan'      => $user->bike_type,
        ];

        return view('profile', $profile)->with('success', 'Profil berhasil diperbarui!');
    }
}