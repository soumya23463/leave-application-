<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Holiday</h2>
    </x-slot>

    <x-card>
        <form method="POST" action="{{ route('holidays.update', $holiday) }}">
            @csrf
            @method('PUT')
            @include('holidays._form', ['submitLabel' => 'Update holiday'])
        </form>
    </x-card>
</x-app-layout>
