<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionsController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $attributes = $request->validate([
            'email' => ['required', 'email', 'max:255', 'exists:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if (Auth::attempt($attributes)) {
            return redirect('/')->with('success', 'Sesión iniciada correctamente');
        }

        return back()->withErrors([
            'email' => 'Las credenciales no son válidas.',
            'password' => 'Las credenciales no son válidas.',
        ])->withInput();
    }

    public function destroy()
    {
        Auth::logout();

        return redirect('/login');
    }
}
