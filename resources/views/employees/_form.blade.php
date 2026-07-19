@php $u = $user ?? null; @endphp

<div class="space-y-5">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <x-input-label for="name" value="Name" :required="true" />
            <x-text-input type="text" name="name" id="name" :value="old('name', $u?->name)" class="mt-1 block w-full" />
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="email" value="Email" :required="true" />
            <x-text-input type="email" name="email" id="email" :value="old('email', $u?->email)" class="mt-1 block w-full" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <x-input-label for="password" :value="$u ? 'Password (leave blank to keep current)' : 'Password'" :required="! $u" />
            <x-password-input name="password" id="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="phone" value="Phone" />
            <x-text-input type="text" name="phone" id="phone" :value="old('phone', $u?->phone)" class="mt-1 block w-full" />
            <x-input-error :messages="$errors->get('phone')" class="mt-1" />
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <x-input-label for="role" value="Role" :required="true" />
            @php
                // Only super admins can grant the superadmin role; keep an existing one visible either way.
                $roles = isSuperAdmin() ? ['employee', 'admin', 'superadmin'] : ['employee', 'admin'];
                if ($u && ! in_array($u->role, $roles, true)) { $roles[] = $u->role; }
            @endphp
            <select name="role" id="role" class="mt-1 block w-full rounded-lg border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition duration-150 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30">
                @foreach ($roles as $r)
                    <option value="{{ $r }}" @selected(old('role', $u?->role) === $r)>{{ ucfirst($r) }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="status" value="Status" :required="true" />
            <select name="status" id="status" class="mt-1 block w-full rounded-lg border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition duration-150 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30">
                @foreach (['active','inactive'] as $st)
                    <option value="{{ $st }}" @selected(old('status', $u?->status) === $st)>{{ ucfirst($st) }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('status')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="joining_date" value="Joining date" />
            <x-date-input name="joining_date" id="joining_date" :value="old('joining_date', $u?->joining_date?->toDateString())" class="mt-1 block w-full" />
            <x-input-error :messages="$errors->get('joining_date')" class="mt-1" />
        </div>
    </div>

    <div>
        <x-input-label for="department_id" value="Department" :required="true" />
        @if (isSuperAdmin())
            <select name="department_id" id="department_id" required class="mt-1 block w-full rounded-lg border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition duration-150 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30">
                <option value="" disabled @selected(! old('department_id', $u?->department_id))>— Select department —</option>
                @foreach (($departments ?? collect()) as $dept)
                    <option value="{{ $dept->id }}" @selected(old('department_id', $u?->department_id) == $dept->id)>{{ $dept->name }}</option>
                @endforeach
            </select>
        @else
            {{-- Admins can only work within their own department --}}
            <input type="text" disabled value="{{ authUser()->department?->name ?? 'No department assigned' }}"
                   class="mt-1 block w-full rounded-lg border-gray-200 bg-gray-50 px-3.5 py-2.5 text-sm text-gray-500 cursor-not-allowed">
            <p class="mt-1 text-xs text-gray-400">Employees you create are added to your department.</p>
        @endif
        <x-input-error :messages="$errors->get('department_id')" class="mt-1" />
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="inline-flex items-center px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded-md hover:bg-brand-700">
            {{ $submitLabel ?? 'Save' }}
        </button>
        <a href="{{ route('employees.index') }}" class="text-sm text-gray-600 hover:text-gray-800">Cancel</a>
    </div>
</div>
