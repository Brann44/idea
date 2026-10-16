<x-layout>
    <div>
        <header class="py-8 md:py-12">
            <h1 class="text-3xl font-bold">Ideas</h1>
            <p class="text-muted-foreground text-sm mt-2">Capture your thoughts. Make a plan.</p>

            <x-card
                x-data
                @click="$dispatch('open-modal', 'create-idea')"
                is="button"
                data-test="create-idea-button"
                class="mt-10 cursor-pointer h-32 w-full text-left"
            >
                <p class="text-muted-foreground">What's the idea?</p>
            </x-card>
        </header>

        <div>
            <div class="flex gap-x-2">
                <a href="/ideas" class="btn {{ request()->has('status') ? 'btn-outlined' : '' }}"> 
                    All <span class="text-xs pl-2">{{ $statusCounts['all'] ?? 0 }}</span>
                </a>

                @foreach (App\IdeaStatus::cases() as $status)
                    <a 
                        href="/ideas?status={{ $status->value }}" 
                        class="btn {{ request('status') === $status->value ? '' : 'btn-outlined' }}"
                    > 
                        {{ $status->label() }} 
                        <span class="text-xs pl-2">{{ $statusCounts[$status->value] ?? 0 }}</span>
                    </a>
                @endforeach
            </div>

            <div class="mt-10 text-muted-foreground">
                <div class="grid md:grid-cols-2 gap-6">
                    @forelse($ideas as $idea)
                        <x-card href="{{ route('idea.show', $idea) }}">
                            @if ($idea->image_path)
                                <div class="-m-4 mb-4 aspect-video overflow-hidden rounded-t-lg bg-muted">
                                    <img 
                                        src="{{ $idea->image_url }}"
                                        alt="{{ $idea->title }}" 
                                        class="w-full h-full object-cover"
                                    >
                                </div>
                            @endif

                            <h3 class="text-foreground text-lg font-medium">{{ $idea->title }}</h3>

                            <div class="mt-2">
                                <x-idea.status-label status="{{ $idea->status->value }}">
                                    {{ $idea->status->label() }}
                                </x-idea.status-label>
                            </div>

                            @if ($idea->description)
                                <div class="mt-4 line-clamp-2 text-sm text-muted-foreground">{{ $idea->description }}</div>
                            @endif

                            <div class="mt-4 text-xs text-muted-foreground">{{ $idea->created_at->diffForHumans() }}</div>
                        </x-card>
                    @empty
                        <x-card>
                            <p>No ideas at this time.</p>
                        </x-card>
                    @endforelse

                </div>
            </div>
        </div>

        <!-- Modal para Crear Idea -->
        <x-idea.modal />
    </div>
</x-layout>
