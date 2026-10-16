@props(['idea' => null])

<x-modal 
    :name="$idea ? 'edit-idea' : 'create-idea'" 
    :title="$idea ? 'Edit Idea' : 'Create an Idea'"
>
    <form 
        method="POST" 
        action="{{ $idea ? route('idea.update', $idea) : route('idea.store') }}" 
        enctype="multipart/form-data" 
        class="space-y-6"
    >
        @csrf
        @if ($idea)
            @method('PATCH')
        @endif

        {{-- Título --}}
        <x-form.field label="Title" name="title" :value="old('title', $idea?->title)" required />

        {{-- Descripción --}}
        <x-form.field label="Description" name="description" type="textarea" rows="4" :value="old('description', $idea?->description)" required />

        {{-- Previsualización y botón para eliminar imagen existente --}}
        @if ($idea && $idea->image_path)
            <div class="space-y-2">
                <img src="{{ $idea->image_url }}" class="rounded-lg w-full max-h-48 object-cover border border-border">
                <button 
                    type="submit"
                    form="delete-image-form"
                    class="text-xs text-red-500 hover:underline cursor-pointer block"
                >
                    Remove Image
                </button> 
            </div>
        @endif

        {{-- Subida de imagen --}}
        <x-form.field label="Image" name="image" type="file" />

        {{-- Selector de Estado --}}
        <div class="space-y-2" x-data="{ status: @js(old('status', $idea?->status?->value ?? App\IdeaStatus::PENDING->value)) }">
            <label class="label">Status</label>
            <input type="hidden" name="status" :value="status">

            <div class="flex gap-x-2">
                @foreach (App\IdeaStatus::cases() as $case)
                    <button
                        type="button"
                        dusk="button-status-{{ $case->value }}"
                        @click="status = '{{ $case->value }}'"
                        class="btn"
                        :class="status === '{{ $case->value }}' ? '' : 'btn-outlined'"
                    >
                        {{ $case->label() }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Actionable Steps interactivos con selector dusk --}}
        <div class="space-y-2" x-data="{ 
            steps: @js(old('steps', $idea ? $idea->steps->pluck('description') : [])),
            newStep: ''
        }">
            <label class="label">Actionable Steps</label>

            <div class="space-y-2">
                <template x-for="(step, index) in steps" :key="index">
                    <div class="flex gap-x-2 items-center">
                        <input
                            type="text"
                            name="steps[]"
                            x-model="steps[index]"
                            class="input"
                        >
                        <button
                            type="button"
                            @click="steps.splice(index, 1)"
                            class="btn btn-outlined text-muted-foreground hover:text-red-500 px-3"
                            title="Remove step"
                        >
                            &times;
                        </button>
                    </div>
                </template>

                <div class="flex gap-x-2 items-center">
                    <input
                        type="text"
                        dusk="new-step"
                        x-model="newStep"
                        @keydown.enter.prevent="if (newStep.trim()) { steps.push(newStep.trim()); newStep = ''; }"
                        placeholder="What needs to be done?"
                        class="input"
                    >
                    <button
                        type="button"
                        dusk="submit-new-step-button"
                        @click="if (newStep.trim()) { steps.push(newStep.trim()); newStep = ''; }"
                        class="btn btn-outlined px-3"
                        title="Add step"
                    >
                        +
                    </button>
                </div>
            </div>
        </div>

        {{-- Links Dinámicos interactivos con selector dusk --}}
        <div class="space-y-2" x-data="{ 
            links: @js(old('links', $idea ? (count($idea->links) ? $idea->links : []) : [])),
            newLink: ''
        }">
            <label class="label">Links</label>

            <div class="space-y-2">
                <template x-for="(link, index) in links" :key="index">
                    <div class="flex gap-x-2 items-center">
                        <input
                            type="url"
                            name="links[]"
                            x-model="links[index]"
                            class="input"
                        >
                        <button
                            type="button"
                            @click="links.splice(index, 1)"
                            class="btn btn-outlined text-muted-foreground hover:text-red-500 px-3"
                            title="Remove link"
                        >
                            &times;
                        </button>
                    </div>
                </template>

                <div class="flex gap-x-2 items-center">
                    <input
                        type="url"
                        dusk="new-link"
                        x-model="newLink"
                        @keydown.enter.prevent="if (newLink.trim()) { links.push(newLink.trim()); newLink = ''; }"
                        placeholder="https://example.com"
                        class="input"
                    >
                    <button
                        type="button"
                        dusk="submit-new-link-button"
                        @click="if (newLink.trim()) { links.push(newLink.trim()); newLink = ''; }"
                        class="btn btn-outlined px-3"
                        title="Add link"
                    >
                        +
                    </button>
                </div>
            </div>
        </div>

        {{-- Botones Cancelar y Guardar --}}
        <div class="flex justify-end gap-x-3 pt-2">
            <button 
                type="button" 
                @click="$dispatch('close-modal', '{{ $idea ? 'edit-idea' : 'create-idea' }}')" 
                class="btn btn-outlined"
            >
                Cancel
            </button>
            <button type="submit" class="btn">
                {{ $idea ? 'Update' : 'Create Idea' }}
            </button>
        </div>
    </form>
</x-modal>
