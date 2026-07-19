<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Leave Request</h2>
    </x-slot>

    <x-card>
        <form method="POST" action="{{ route('leave-requests.update', $leaveRequest) }}">
            @csrf
            @method('PUT')
            @include('leave-requests._form', ['submitLabel' => 'Update request'])
        </form>
    </x-card>
</x-app-layout>
