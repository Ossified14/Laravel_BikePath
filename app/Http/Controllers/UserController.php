<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
    // GET /users
    public function index()
    {
        $users = User::orderBy('name')->get();
        return view('users.index', compact('users'));
    }
}