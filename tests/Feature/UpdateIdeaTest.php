<?php

use App\IdeaStatus;
use App\Models\Idea;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('un usuario puede actualizar su propia idea', function () {
    $user = User::factory()->create();
    $idea = Idea::factory()->for($user)->create([
        'title' => 'Título original',
        'description' => 'Descripción original',
        'status' => IdeaStatus::PENDING,
    ]);

    $response = $this->actingAs($user)->patch(route('idea.update', $idea), [
        'title' => 'Título actualizado',
        'description' => 'Descripción actualizada',
        'status' => IdeaStatus::IN_PROGRESS->value,
    ]);

    $response->assertRedirect(route('idea.show', $idea));

    $this->assertDatabaseHas('ideas', [
        'id' => $idea->id,
        'title' => 'Título actualizado',
        'description' => 'Descripción actualizada',
        'status' => IdeaStatus::IN_PROGRESS->value,
    ]);
});

test('un usuario no puede actualizar la idea de otro usuario', function () {
    $user = User::factory()->create();
    $otherIdea = Idea::factory()->create([
        'title' => 'Título original',
    ]);

    $response = $this->actingAs($user)->patch(route('idea.update', $otherIdea), [
        'title' => 'Intento no autorizado',
        'description' => 'Nueva descripción',
        'status' => IdeaStatus::COMPLETED->value,
    ]);

    $response->assertForbidden();

    $this->assertDatabaseHas('ideas', [
        'id' => $otherIdea->id,
        'title' => 'Título original',
    ]);
});

test('se requiere título, descripción y estado para actualizar una idea', function () {
    $user = User::factory()->create();
    $idea = Idea::factory()->for($user)->create();

    $response = $this->actingAs($user)->patch(route('idea.update', $idea), [
        'title' => '',
        'description' => '',
        'status' => '',
    ]);

    $response->assertSessionHasErrors(['title', 'description', 'status']);
});

test('un usuario puede actualizar los enlaces de su idea', function () {
    $user = User::factory()->create();
    $idea = Idea::factory()->for($user)->create([
        'links' => ['https://laravel.com'],
    ]);

    $response = $this->actingAs($user)->patch(route('idea.update', $idea), [
        'title' => $idea->title,
        'description' => $idea->description,
        'status' => $idea->status->value,
        'links' => ['https://laracasts.com', 'https://tailwindcss.com'],
    ]);

    $response->assertRedirect(route('idea.show', $idea));

    $idea->refresh();
    expect($idea->links)->toHaveCount(2);
    expect($idea->links[0])->toBe('https://laracasts.com');
});

test('un usuario puede actualizar la imagen de su idea', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $idea = Idea::factory()->for($user)->create([
        'image_path' => UploadedFile::fake()->image('old-idea.jpg')->store('ideas', 'public'),
    ]);

    $oldImagePath = $idea->image_path;
    Storage::disk('public')->assertExists($oldImagePath);

    $newImage = UploadedFile::fake()->image('new-idea.jpg');

    $response = $this->actingAs($user)->patch(route('idea.update', $idea), [
        'title' => $idea->title,
        'description' => $idea->description,
        'status' => $idea->status->value,
        'image' => $newImage,
    ]);

    $response->assertRedirect(route('idea.show', $idea));

    $idea->refresh();
    expect($idea->image_path)->not->toBe($oldImagePath);
    Storage::disk('public')->assertMissing($oldImagePath);
    Storage::disk('public')->assertExists($idea->image_path);
});

test('un usuario puede sincronizar los pasos de su idea', function () {
    $user = User::factory()->create();
    $idea = Idea::factory()->for($user)->create();
    $idea->steps()->create(['description' => 'Paso original 1']);
    $idea->steps()->create(['description' => 'Paso original 2']);

    $response = $this->actingAs($user)->patch(route('idea.update', $idea), [
        'title' => $idea->title,
        'description' => $idea->description,
        'status' => $idea->status->value,
        'steps' => ['Paso nuevo 1', 'Paso nuevo 2', 'Paso nuevo 3'],
    ]);

    $response->assertRedirect(route('idea.show', $idea));

    $idea->refresh();
    expect($idea->steps)->toHaveCount(3);
    expect($idea->steps->pluck('description')->all())->toBe([
        'Paso nuevo 1',
        'Paso nuevo 2',
        'Paso nuevo 3',
    ]);
});
