@php
    $ws = $weekendSetting ?? null;
    $weekdays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    $selectedDays = old('days', $ws->days ?? ['Saturday', 'Sunday']);
    $statusChecked = old('status', optional($ws)->status ?? true);
@endphp

<div class="space-y-5">
    <div>
        <x-input-label value="Weekend days" />
        <div class="mt-2 grid grid-cols-2 sm:grid-cols-4 gap-2">
            @foreach ($weekdays as $day)
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="days[]" value="{{ $day }}"
                           @checked(in_array($day, (array) $selectedDays))
                           class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                    {{ $day }}
                </label>
            @endforeach
        </div>
        <x-input-error :messages="$errors->get('days')" class="mt-1" />
        <x-input-error :messages="$errors->get('days.*')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="effective_date" value="Effective date" />
        <x-text-input type="date" name="effective_date" id="effective_date" class="mt-1 block w-full"
                      :value="old('effective_date', optional($ws)->effective_date?->toDateString() ?? now()->toDateString())" />
        <x-input-error :messages="$errors->get('effective_date')" class="mt-1" />
    </div>

    <div>
        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
            <input type="hidden" name="status" value="0">
            <input type="checkbox" name="status" value="1" @checked($statusChecked)
                   class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
            Active
        </label>
        <x-input-error :messages="$errors->get('status')" class="mt-1" />
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="inline-flex items-center px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded-md hover:bg-brand-700">
            {{ $submitLabel ?? 'Save' }}
        </button>
        <a href="{{ route('weekend-settings.index') }}" class="text-sm text-gray-600 hover:text-gray-800">Cancel</a>
    </div>
</div>
