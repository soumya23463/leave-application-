<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">New Leave Balance</h2>
    </x-slot>

    <x-card>
        <form method="POST" action="{{ route('leave-balances.store') }}">
            @csrf
            @include('leave-balances._form', ['submitLabel' => 'Create balance'])
        </form>
    </x-card>
</x-app-layout>
