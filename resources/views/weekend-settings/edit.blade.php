<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Weekend Setting</h2>
    </x-slot>

    <x-card>
        <form method="POST" action="{{ route('weekend-settings.update', $weekendSetting) }}">
            @csrf
            @method('PUT')
            @include('weekend-settings._form', ['submitLabel' => 'Update setting'])
        </form>
    </x-card>
</x-app-layout>
