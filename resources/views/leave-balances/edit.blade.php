<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Leave Balance</h2>
    </x-slot>

    <x-card>
        <form method="POST" action="{{ route('leave-balances.update', $leaveBalance) }}">
            @csrf
            @method('PUT')
            @include('leave-balances._form', ['submitLabel' => 'Update balance'])
        </form>
    </x-card>
</x-app-layout>
