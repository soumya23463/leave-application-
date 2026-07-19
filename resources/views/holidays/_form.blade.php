@php $h = $holiday ?? null; @endphp

<div class="space-y-5">
    <div>
        <x-input-label for="name" value="Name" />
        <x-text-input type="text" name="name" id="name" :value="old('name', $h?->name)" class="mt-1 block w-full" />
        <x-input-error :messages="$errors->get('name')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="date" value="Date" />
        <x-date-input name="date" id="date" :value="old('date', $h?->date?->toDateString())" class="mt-1 block w-full" />
        <x-input-error :messages="$errors->get('date')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="description" value="Description" />
        <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition duration-150 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30">{{ old('description', $h?->description) }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-1" />
    </div>

    <div>
        <label class="inline-flex items-center gap-2">
            <input type="hidden" name="status" value="0">
            <input type="checkbox" name="status" value="1" class="rounded border-gray-300 text-brand-600 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                @checked(old('status', $h?->status ?? true))>
            <span class="text-sm text-gray-700">Active</span>
        </label>
        <x-input-error :messages="$errors->get('status')" class="mt-1" />
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="inline-flex items-center px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded-md hover:bg-brand-700">
            {{ $submitLabel ?? 'Save' }}
        </button>
        <a href="{{ route('holidays.index') }}" class="text-sm text-gray-600 hover:text-gray-800">Cancel</a>
    </div>
</div>
