<x-layout>
    <div class="py-8 max-w-4xl mx-auto space-y-6">
        {{-- Barra superior de navegación y acciones --}}
        <div class="flex justify-between items-center">
            <a href="{{ route('idea.index') }}" class="flex items-center gap-x-2 text-sm font-medium">
                &larr; Back to Ideas
            </a>

            <div class="gap-x-3 flex items-center">
                @can('update', $idea)
                    <button type="button" 
                    x-data
                    dusk="edit-idea-button"
                    @click="$dispatch('open-modal', 'edit-idea')" class="btn btn-outlined">
                        <x-icons.external/>
                        Edit Idea
                    </button>
                @endcan

                @can('delete', $idea)
                    <form method="POST" action="{{ route('idea.destroy', $idea) }}">
                        @csrf
                        @method('DELETE')

                        <button class="btn btn-outlined text-red-500">Delete</button>
                    </form>
                @endcan
            </div>
        </div>

        {{-- Imagen destacada (si existe) --}}
        @if ($idea->image_path)
            <div>
                <img src="{{ asset('storage/' . $idea->image_path) }}" alt="{{ $idea->title }}" class="rounded-lg w-full max-h-96 object-cover border border-border">
            </div>
        @endif

        {{-- Título, Status y Fecha --}}
        <div class="space-y-4">
            <h1 class="font-bold text-4xl">{{ $idea->title }}</h1>

            <div class="flex gap-x-3 items-center">
                <x-idea.status-label status="{{ $idea->status->value }}">
                    {{ $idea->status->label() }}
                </x-idea.status-label>
                <span class="text-muted-foreground text-sm">{{ $idea->created_at->diffForHumans() }}</span>
            </div>
        </div>

        {{-- Descripción --}}
        <x-card>
            <div class="prose prose-invert max-w-none">
                {!! $idea->formatted_description !!}
            </div>
        </x-card>

        {{-- Steps --}}
        @if ($idea->steps->count())
            <div class="mt-6">
                <h3 class="font-bold text-xl">Steps</h3>

                <div class="mt-3 space-y-2">
                    @foreach ($idea->steps as $step)
                        <form method="POST" action="{{ route('step.update', $step) }}">
                            @csrf
                            @method('PATCH')

                            <label class="flex items-center gap-x-3 cursor-pointer p-2 rounded hover:bg-muted/30">
                                <input
                                    type="checkbox"
                                    name="completed"
                                    onchange="this.form.submit()"
                                    {{ $step->completed ? 'checked' : '' }}
                                    class="checkbox"
                                >
                                <span class="{{ $step->completed ? 'line-through text-muted-foreground' : '' }}">
                                    {{ $step->description }}
                                </span>
                            </label>
                        </form>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Links --}}
        @if ($idea->links->count())
            <div class="mt-6">
                <h3 class="font-bold text-xl">Links</h3>

                <div class="mt-3 space-y-2"> 
                    @foreach ($idea->links as $link)
                        <x-card :href="$link" class="text-primary font-medium flex gap-x-3 items-center">
                            <x-icons.external/>
                            {{ $link }}
                        </x-card>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- Modal para Editar Idea --}}
@can('update', $idea)
    <x-idea.modal :idea="$idea" />

    {{-- Formulario para eliminar imagen (afuera del modal) --}}
    @if ($idea->image_path)
        <form id="delete-image-form" method="POST" action="{{ route('idea.image.destroy', $idea) }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endif
@endcan

</x-layout>
