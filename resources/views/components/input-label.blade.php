@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-white/85 mb-1']) }}>
    {{ $value ?? $slot }}
</label>
