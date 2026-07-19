@php $lb = $leaveBalance ?? null; @endphp

<div class="space-y-5">
    <div>
        <x-input-label for="employee_id" value="Employee" />
        <select name="employee_id" id="employee_id" class="mt-1 block w-full rounded-lg border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition duration-150 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30">
            <option value="">— Select employee —</option>
            @foreach ($employees as $emp)
                <option value="{{ $emp->id }}" @selected(old('employee_id', $lb?->employee_id) == $emp->id)>{{ $emp->name }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('employee_id')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="year" value="Year" />
        <x-text-input type="number" name="year" id="year" :value="old('year', $lb?->year ?? date('Y'))" min="2000" max="2100" class="mt-1 block w-full" />
        <x-input-error :messages="$errors->get('year')" class="mt-1" />
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <x-input-label for="total_days" value="Total days" />
            <x-text-input type="number" step="0.5" name="total_days" id="total_days" :value="old('total_days', $lb?->total_days)" min="0" class="mt-1 block w-full" />
            <x-input-error :messages="$errors->get('total_days')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="carried_forward" value="Carried forward" />
            <x-text-input type="number" step="0.5" name="carried_forward" id="carried_forward" :value="old('carried_forward', $lb?->carried_forward)" min="0" class="mt-1 block w-full" />
            <x-input-error :messages="$errors->get('carried_forward')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="used_days" value="Used days" />
            <x-text-input type="number" step="0.5" name="used_days" id="used_days" :value="old('used_days', $lb?->used_days)" min="0" class="mt-1 block w-full" />
            <x-input-error :messages="$errors->get('used_days')" class="mt-1" />
        </div>
    </div>

    <div class="rounded-md bg-brand-50 border border-brand-100 px-4 py-3 text-sm text-brand-700">
        Remaining days = Total + Carried forward − Used. This is computed automatically when you save.
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="inline-flex items-center px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded-md hover:bg-brand-700">
            {{ $submitLabel ?? 'Save' }}
        </button>
        <a href="{{ route('leave-balances.index') }}" class="text-sm text-gray-600 hover:text-gray-800">Cancel</a>
    </div>
</div>
