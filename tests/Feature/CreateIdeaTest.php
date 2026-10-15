<?php

use App\IdeaStatus;
use App\Models\Idea;
use App\Models\User;

test('un usuario autenticado puede crear una idea', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('idea.store'), [
        'title' => 'Mi primera idea de prueba',
        'description' => 'Esta es una descripción detallada para la prueba.',
        'status' => IdeaStatus::PENDING->value,
    ]);

    $response->assertRedirect(route('idea.index'));

    $this->assertDatabaseHas('ideas', [
        'user_id' => $user->id,
        'title' => 'Mi primera idea de prueba',
        'description' => 'Esta es una descripción detallada para la prueba.',
        'status' => IdeaStatus::PENDING->value,
    ]);
});

test('se requiere título, descripción y estado para crear una idea', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('idea.store'), [
        'title' => '',
        'description' => '',
        'status' => '',
    ]);

    $response->assertSessionHasErrors(['title', 'description', 'status']);
});

test('un usuario no autenticado no puede crear una idea', function () {
    $response = $this->post(route('idea.store'), [
        'title' => 'Idea no autorizada',
        'description' => 'Intento de creación anónimo.',
        'status' => IdeaStatus::PENDING->value,
    ]);

    $response->assertRedirect(route('login'));
    $this->assertDatabaseMissing('ideas', [
        'title' => 'Idea no autorizada',
    ]);
});

test('un usuario puede crear una idea con enlaces', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('idea.store'), [
        'title' => 'Idea con enlaces',
        'description' => 'Descripción con recursos útiles.',
        'status' => IdeaStatus::PENDING->value,
        'links' => [
            'https://laravel.com',
            'https://laracasts.com',
        ],
    ]);

    $response->assertRedirect(route('idea.index'));

    $idea = Idea::where('title', 'Idea con enlaces')->first();
    expect($idea->links)->toHaveCount(2);
});

test('un usuario no puede ver la idea de otro usuario', function () {
    $user = User::factory()->create();
    $otherIdea = Idea::factory()->create();

    $response = $this->actingAs($user)->get(route('idea.show', $otherIdea));

    $response->assertForbidden();
});

test('un usuario puede ver su propia idea', function () {
    $user = User::factory()->create();
    $myIdea = Idea::factory()->for($user)->create();

    $response = $this->actingAs($user)->get(route('idea.show', $myIdea));

    $response->assertOk();
});

test('un usuario no puede borrar la idea de otro usuario', function () {
    $user = User::factory()->create();
    $otherIdea = Idea::factory()->create();

    $response = $this->actingAs($user)->delete(route('idea.destroy', $otherIdea));

    $response->assertForbidden();
    $this->assertDatabaseHas('ideas', ['id' => $otherIdea->id]);
});

test('un usuario puede borrar su propia idea', function () {
    $user = User::factory()->create();
    $myIdea = Idea::factory()->for($user)->create();

    $response = $this->actingAs($user)->delete(route('idea.destroy', $myIdea));

    $response->assertRedirect(route('idea.index'));
    $this->assertDatabaseMissing('ideas', ['id' => $myIdea->id]);
});
