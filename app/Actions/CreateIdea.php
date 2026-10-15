<?php

namespace App\Actions;

use App\Models\Idea;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Support\Arr;

class CreateIdea
{
    public function __construct(
        #[CurrentUser] protected User $user
    ) {}

    public function handle(array $attributes): Idea
    {
        // 1. Filtrar enlaces vacíos si existen
        if (isset($attributes['links'])) {
            $attributes['links'] = array_values(array_filter($attributes['links']));
        }

        // 2. Si hay imagen, guardarla en storage público
        if (isset($attributes['image'])) {
            $attributes['image_path'] = $attributes['image']->store('ideas', 'public');
        }

        // 3. Crear la idea asociada al usuario autenticado
        $idea = $this->user->ideas()->create(
            Arr::except($attributes, ['steps', 'image'])
        );

        // 4. Crear los pasos (steps) asociados
        if (! empty($attributes['steps'])) {
            $steps = array_filter($attributes['steps']);
            foreach ($steps as $step) {
                $idea->steps()->create(['description' => $step]);
            }
        }

        return $idea;
    }
}
