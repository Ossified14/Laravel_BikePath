<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    private function defaultProfile(): array
    {
        return [
            'nama'         => 'Munif',
            'umur'         => 21,
            'jenis_sepeda' => ['Road', 'Gravel', 'Mountain'],
            'pilihan'      => null,
        ];
    }

    // GET /profile
    public function index()
    {
        $profile = $this->defaultProfile();
        return view('profile', $profile);
    }

    // POST /profile — simpan perubahan
    public function store(Request $request)
    {
        $jenisList = ['Road', 'Gravel', 'Mountain'];

        $request->validate([
            'nama'         => 'required|string|max:100',
            'umur'         => 'required|integer|min:1|max:120',
            'jenis_sepeda' => 'required|in:' . implode(',', $jenisList),
        ]);

        $profile = [
            'nama'         => $request->input('nama'),
            'umur'         => (int) $request->input('umur'),
            'jenis_sepeda' => $jenisList,
            'pilihan'      => $request->input('jenis_sepeda'),
        ];

        return view('profile', $profile);
    }
}


