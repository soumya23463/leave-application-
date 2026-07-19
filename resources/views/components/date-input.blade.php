@props(['disabled' => false])

{{-- flatpickr (loaded in layouts.app) turns this into a picker that shows
     "17 July, 2025" while submitting the value as Y-m-d. --}}
<input
    type="text"
    autocomplete="off"
    placeholder="Select a date"
    @disabled($disabled)
    {{ $attributes->merge([
        'class' => 'js-datepicker block w-full cursor-pointer rounded-lg border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 placeholder-gray-400 shadow-sm transition duration-150 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30 disabled:cursor-not-allowed disabled:bg-gray-50 disabled:text-gray-500',
    ]) }}>
