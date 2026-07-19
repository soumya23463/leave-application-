<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Record Admin Leave</h2>
    </x-slot>

    <x-card>
        <form method="POST" action="{{ route('admin-leaves.store') }}">
            @csrf
            @include('admin-leaves._form', ['submitLabel' => 'Record leave'])
        </form>
    </x-card>
</x-app-layout>
