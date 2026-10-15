@props(['name', 'label', 'type' => 'text', 'value' => ''])

<div class="space-y-2">
    <label for="{{ $name }}" class="label">{{ $label }}</label>

    @if ($type === 'textarea')
        <textarea class="textarea" id="{{ $name }}" name="{{ $name }}" {{ $attributes }}>{{ old($name, $value) }}</textarea>
    @else
        <input
            type="{{ $type }}"
            class="input"
            id="{{ $name }}"
            name="{{ $name }}"
            @if($type !== 'file') value="{{ old($name, $value) }}" @endif
            {{ $attributes }}
        >
    @endif

    @error($name)
        <p class="error">{{ $message }}</p>
    @enderror
</div>
