<?php

namespace App\Actions;

use App\Models\Idea;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UpdateIdea
{
    public function handle(Idea $idea, array $attributes): Idea
    {
        return DB::transaction(function () use ($idea, $attributes) {
            // 1. Filtrar enlaces vacíos si existen
            if (isset($attributes['links'])) {
                $attributes['links'] = array_values(array_filter($attributes['links']));
            }

            // 2. Si se sube una nueva imagen, eliminar la anterior del disco (si existía) y guardar la nueva
            if (isset($attributes['image'])) {
                if ($idea->image_path) {
                    Storage::disk('public')->delete($idea->image_path);
                }

                $attributes['image_path'] = $attributes['image']->store('ideas', 'public');
            }

            // 3. Actualizar los datos de la idea
            $idea->update(Arr::except($attributes, ['steps', 'image']));

            // 4. Sincronizar pasos (steps) si fueron proporcionados
            if (isset($attributes['steps'])) {
                $idea->steps()->delete();

                $steps = array_filter($attributes['steps']);
                foreach ($steps as $step) {
                    $description = is_array($step) ? ($step['description'] ?? '') : $step;
                    if ($description) {
                        $idea->steps()->create([
                            'description' => $description,
                            'completed' => is_array($step) ? ($step['completed'] ?? false) : false,
                        ]);
                    }
                }
            }

            return $idea;
        });
    }
}
