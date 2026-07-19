@props(['value' => null, 'required' => false])

<label {{ $attributes->merge(['class' => 'block mb-1.5 text-sm font-medium text-gray-700']) }}>
    {{ $value ?? $slot }}@if ($required)<span class="text-red-500">&nbsp;*</span>@endif
</label>
