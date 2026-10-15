<?php

use App\Models\User;
use App\Notifications\EmailAddressChangedNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

test('un usuario autenticado puede ver su pagina de perfil', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('profile.edit'));

    $response->assertOk();
    $response->assertSee($user->name);
    $response->assertSee($user->email);
});

test('un usuario no autenticado no puede ver el perfil', function () {
    $response = $this->get(route('profile.edit'));

    $response->assertRedirect(route('login'));
});

test('un usuario puede actualizar su nombre y correo', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->patch(route('profile.update'), [
        'name' => 'Nuevo Nombre',
        'email' => 'nuevo@ejemplo.com',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('status', 'Profile updated successfully!');

    $user->refresh();
    expect($user->name)->toBe('Nuevo Nombre');
    expect($user->email)->toBe('nuevo@ejemplo.com');
});

test('un usuario puede actualizar su contrasena', function () {
    $user = User::factory()->create([
        'password' => 'password123',
    ]);

    $response = $this->actingAs($user)->patch(route('profile.update'), [
        'name' => $user->name,
        'email' => $user->email,
        'password' => 'nueva-contrasena123',
        'password_confirmation' => 'nueva-contrasena123',
    ]);

    $response->assertRedirect();

    $user->refresh();
    expect(Hash::check('nueva-contrasena123', $user->password))->toBeTrue();
});

test('no se puede actualizar el correo a uno que ya pertenece a otro usuario', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create(['email' => 'otro@ejemplo.com']);

    $response = $this->actingAs($user)->patch(route('profile.update'), [
        'name' => $user->name,
        'email' => 'otro@ejemplo.com',
    ]);

    $response->assertSessionHasErrors(['email']);
});

test('se envia una notificacion cuando el usuario cambia su correo', function () {
    Notification::fake();

    $user = User::factory()->create([
        'email' => 'antiguo@ejemplo.com',
    ]);

    $this->actingAs($user)->patch(route('profile.update'), [
        'name' => $user->name,
        'email' => 'nuevo@ejemplo.com',
    ]);

    Notification::assertSentTo($user, EmailAddressChangedNotification::class);
});

test('no se envia notificacion si el correo no cambia', function () {
    Notification::fake();

    $user = User::factory()->create([
        'email' => 'mismo@ejemplo.com',
    ]);

    $this->actingAs($user)->patch(route('profile.update'), [
        'name' => 'Nombre Cambiado',
        'email' => 'mismo@ejemplo.com',
    ]);

    Notification::assertNotSentTo($user, EmailAddressChangedNotification::class);
});
