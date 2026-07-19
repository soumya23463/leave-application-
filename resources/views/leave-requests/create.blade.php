<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Apply for Leave</h2>
    </x-slot>

    <x-card>
        <form method="POST" action="{{ route('leave-requests.store') }}">
            @csrf
            @include('leave-requests._form', ['submitLabel' => 'Submit request'])
        </form>
    </x-card>
</x-app-layout>
