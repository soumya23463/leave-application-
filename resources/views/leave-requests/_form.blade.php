@php $lr = $leaveRequest ?? null; @endphp

<div class="space-y-5">
    @if (isAdmin())
        <div>
            <x-input-label for="user_id" value="Employee" />
            <select name="user_id" id="user_id" class="mt-1 block w-full rounded-lg border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition duration-150 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30">
                <option value="">— Select employee —</option>
                @foreach ($employees as $emp)
                    <option value="{{ $emp->id }}" @selected(old('user_id', $lr?->user_id) == $emp->id)>{{ $emp->name }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('user_id')" class="mt-1" />
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <x-input-label for="from_date" value="From date" />
            <x-text-input type="date" name="from_date" id="from_date" value="{{ old('from_date', $lr?->from_date?->toDateString()) }}" class="mt-1 block w-full" />
            <x-input-error :messages="$errors->get('from_date')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="to_date" value="To date" />
            <x-text-input type="date" name="to_date" id="to_date" value="{{ old('to_date', $lr?->to_date?->toDateString()) }}" class="mt-1 block w-full" />
            <x-input-error :messages="$errors->get('to_date')" class="mt-1" />
        </div>
    </div>

    <div id="days-preview" class="rounded-lg bg-brand-50 border border-brand-100 px-4 py-3 flex items-center justify-between" style="display:none">
        <span class="text-sm text-brand-700">Total leave days
            <span class="text-brand-500">(excluding weekends &amp; holidays)</span>
        </span>
        <span id="days-count" class="text-xl font-bold text-brand-700">0</span>
    </div>

    <div>
        <x-input-label for="reason" value="Reason" />
        <textarea name="reason" id="reason" rows="4" class="mt-1 block w-full rounded-lg border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition duration-150 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30">{{ old('reason', $lr?->reason) }}</textarea>
        <x-input-error :messages="$errors->get('reason')" class="mt-1" />
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="inline-flex items-center px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded-md hover:bg-brand-700">
            {{ $submitLabel ?? 'Submit' }}
        </button>
        <a href="{{ route('leave-requests.index') }}" class="text-sm text-gray-600 hover:text-gray-800">Cancel</a>
    </div>
</div>

<script>
    (function () {
        const weekendDays = @json($weekendDays);
        const holidays = @json($holidayDates);
        const names = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        const fromEl = document.getElementById('from_date');
        const toEl = document.getElementById('to_date');
        const box = document.getElementById('days-preview');
        const out = document.getElementById('days-count');
        if (!fromEl || !toEl || !box || !out) return;

        function localIso(d) {
            return d.getFullYear() + '-' +
                String(d.getMonth() + 1).padStart(2, '0') + '-' +
                String(d.getDate()).padStart(2, '0');
        }

        function recalc() {
            const f = fromEl.value, t = toEl.value;
            if (!f || !t) { box.style.display = 'none'; return; }
            const start = new Date(f + 'T00:00:00');
            const end = new Date(t + 'T00:00:00');
            if (isNaN(start) || isNaN(end) || end < start) { box.style.display = 'none'; return; }

            let count = 0;
            for (let d = new Date(start); d <= end; d.setDate(d.getDate() + 1)) {
                if (weekendDays.includes(names[d.getDay()])) continue;
                if (holidays.includes(localIso(d))) continue;
                count++;
            }
            out.textContent = count;
            box.style.display = 'flex';
        }

        ['change', 'input'].forEach(function (ev) {
            fromEl.addEventListener(ev, recalc);
            toEl.addEventListener(ev, recalc);
        });
        recalc();
    })();
</script>
