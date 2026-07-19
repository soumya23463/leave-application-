<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Admin Leave</h2>
    </x-slot>

    <x-card>
        <form method="POST" action="{{ route('admin-leaves.update', $leaveRequest) }}">
            @csrf
            @method('PUT')
            @include('admin-leaves._form', ['submitLabel' => 'Update leave'])
        </form>
    </x-card>
</x-app-layout>
