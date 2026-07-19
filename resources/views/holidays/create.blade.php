<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">New Holiday</h2>
    </x-slot>

    <x-card>
        <form method="POST" action="{{ route('holidays.store') }}">
            @csrf
            @include('holidays._form', ['submitLabel' => 'Create holiday'])
        </form>
    </x-card>
</x-app-layout>
