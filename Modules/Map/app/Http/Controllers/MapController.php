<?php

namespace Modules\Map\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class MapController extends Controller
{
    /**
     * Menampilkan peta navigasi Bikepath (hanya peta saja).
     */
    public function index(): View
    {
        return view('map::index');
    }
}
