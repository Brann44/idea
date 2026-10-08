<x-layout>
    <div>
        <header class="py-8 md:py-12">
            <h1 class="text-3xl font-bold">Ideas</h1>
            <p class="text-muted-foreground text-sm mt-2">Capture your thoughts. Make a plan.</p>

            <x-card
                x-data
                @click="$dispatch('open-modal', 'create-idea')"
                class="mt-10 cursor-pointer h-32 w-full text-left"
            >
                <p>What's the Idea?</p>    
            </x-card>

        </header>

        <div>

            <a href="/ideas" class="btn {{ request()->has('status') ? 'btn-outlined' : '' }}"> 
                All 
            </a>

            @foreach (App\IdeaStatus::cases() as $status)
                <a 
                    href="/ideas?status={{ $status->value }}" 
                    class="btn {{ request('status') === $status->value ? '' : 'btn-outlined' }}"
                > 
                    {{ $status->label() }} 
                    <span class="text-xs pl-3">{{ $statusCounts[$status->value] ?? 0 }}</span>
                </a>
            @endforeach


        <div class="mt-10 text-muted-foreground">
            <div class="grid md:grid-cols-2 gap-6">
                @forelse($ideas as $idea)
                    <x-card href="{{ route('ideas.show', $idea) }}">
                        <h3 class="text-foreground text-lg">{{ $idea->title }}</h3>

                        <x-idea.status-label status="{{ $idea->status->value }}">

                                {{ $idea->status->label() }}

                        </x-idea.status-label>

                        <div class="mt-5 line-clamp-3">{{ $idea->description }}</div>
                        <div class="mt-4">{{ $idea->created_at->diffForHumans() }}</div>
                    </x-card>
                @empty
                    <x-card>
                        <p>No ideas at this time.</p>
                    </x-card>
                @endforelse
            </div>
        </div>

        <x-modal name="create-idea" title="New Idea">
            <p>Slot content here.</p>
        </x-modal>


    </div>
</x-layout>