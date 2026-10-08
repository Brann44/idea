@props([
    'name', 'title'
])

<div
    x-data="{ show: false, name: @js($name) }"
    x-show="show"
    @open-modal.window="if($event.detail === name) show = true;"
    @keydown.escape.window="show = false"
    x-transition:enter="ease-out duration-200"
    x-transition:enter-start="opacity-0 -translate-y-4 -translate-x-4"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0 -translate-y-4 -translate-x-4"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-xs"
    style="display: none"
    role="dialog"
    arialmodal="true"
    aria-labelledby="modal-{{ $name }}-title"
    :aria-hidden="!show"
    tabindex="-1"
>
    <x-card @click.away="show = false" class="shadow-xl max-w-2xl">

        <div>
            <h2 id="modal-{{ $name }}-title" class="text-x1 font-bold">{{ $title }}</h2>
        </div>

        <div class="mt-3 space-y-3">
            {{ $slot }}
        </div>

    </x-card>
</div>
