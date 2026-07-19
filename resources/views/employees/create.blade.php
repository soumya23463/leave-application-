<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">New Employee</h2>
    </x-slot>

    <x-card>
        <form method="POST" action="{{ route('employees.store') }}">
            @csrf
            @include('employees._form', ['submitLabel' => 'Create employee'])
        </form>
    </x-card>
</x-app-layout>
