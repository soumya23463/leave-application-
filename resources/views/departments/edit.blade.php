<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Department</h2>
    </x-slot>

    <x-card>
        <form method="POST" action="{{ route('departments.update', $department) }}">
            @csrf
            @method('PATCH')
            @include('departments._form', ['submitLabel' => 'Update Department'])
        </form>
    </x-card>
</x-app-layout>
