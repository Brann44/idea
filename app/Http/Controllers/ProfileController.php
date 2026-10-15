<?php

namespace App\Http\Controllers;

use App\Notifications\EmailAddressChangedNotification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', [
            'user' => auth()->user(),
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $attributes = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:50'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        // Verificamos si el correo cambió antes de guardar
        $emailChanged = $user->email !== $attributes['email'];

        if ($request->filled('password')) {
            $user->update($attributes);
        } else {
            $user->update([
                'name' => $attributes['name'],
                'email' => $attributes['email'],
            ]);
        }

        // Si cambió el correo, notificamos al usuario
        if ($emailChanged) {
            $user->notify(new EmailAddressChangedNotification);
        }

        return back()->with('status', 'Profile updated successfully!');
    }
}
