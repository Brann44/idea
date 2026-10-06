<?php

use App\Models\Idea;
use App\Models\User;

test('it belongs to a user', function () {
    $idea = Idea::factory()->create();


    expect($idea->user)->toBeInstanceOf(User::class);
});

test('it can have steps', function () {
    $idea = Idea::factory()->create();

    expect($idea->steps)->toBeEmpty();

    $idea->steps()->create([
        'description' => 'Do the thing',
        'completed' => false,
    ]);

    expect($idea->refresh()->steps)->toHaveCount(1);
});