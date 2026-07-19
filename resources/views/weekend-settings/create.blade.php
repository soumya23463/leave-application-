<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">New Weekend Setting</h2>
    </x-slot>

    <x-card>
        <form method="POST" action="{{ route('weekend-settings.store') }}">
            @csrf
            @include('weekend-settings._form', ['submitLabel' => 'Create setting'])
        </form>
    </x-card>
</x-app-layout>
