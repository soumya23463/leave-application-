<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">New Department</h2>
    </x-slot>

    <x-card>
        <form method="POST" action="{{ route('departments.store') }}">
            @csrf
            @include('departments._form', ['submitLabel' => 'Create Department'])
        </form>
    </x-card>
</x-app-layout>
