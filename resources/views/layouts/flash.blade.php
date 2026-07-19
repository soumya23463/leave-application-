@php
    $flashSuccess = session('success') ?? session('discord_success');
    $flashError = session('error') ?? session('discord_error');
@endphp

@if ($flashSuccess)
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
         class="mb-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800 flex justify-between items-center">
        <span>{{ $flashSuccess }}</span>
        <button @click="show = false" class="text-green-600 hover:text-green-800">&times;</button>
    </div>
@endif

@if ($flashError)
    <div x-data="{ show: true }" x-show="show"
         class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800 flex justify-between items-center">
        <span>{{ $flashError }}</span>
        <button @click="show = false" class="text-red-600 hover:text-red-800">&times;</button>
    </div>
@endif

@if ($errors->any())
    <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
