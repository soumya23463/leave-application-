<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Employee</h2>
    </x-slot>

    <x-card>
        <form method="POST" action="{{ route('employees.update', $user) }}">
            @csrf
            @method('PUT')
            @include('employees._form', ['submitLabel' => 'Update employee'])
        </form>
    </x-card>
</x-app-layout>
