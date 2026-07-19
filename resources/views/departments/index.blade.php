<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Departments</h2>
            <a href="{{ route('departments.create') }}" class="inline-flex items-center px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded-md hover:bg-brand-700">
                New Department
            </a>
        </div>
    </x-slot>

    <x-card>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="py-2 pr-4">Name</th>
                        <th class="py-2 pr-4">Description</th>
                        <th class="py-2 pr-4">Employees</th>
                        <th class="py-2 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($departments as $department)
                        <tr>
                            <td class="py-2 pr-4 font-medium text-gray-800">{{ $department->name }}</td>
                            <td class="py-2 pr-4 text-gray-500">{{ $department->description ?: '—' }}</td>
                            <td class="py-2 pr-4">
                                <span class="inline-flex items-center justify-center min-w-[1.5rem] px-2 py-0.5 rounded-full bg-brand-50 text-brand-700 text-xs font-semibold">{{ $department->users_count }}</span>
                            </td>
                            <td class="py-2 text-right whitespace-nowrap">
                                <a href="{{ route('departments.edit', $department) }}" class="text-brand-600 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('departments.destroy', $department) }}" class="inline ml-2" onsubmit="return confirm('Delete this department? Employees will be unassigned.')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-8 text-center text-gray-500">No departments yet. Create one to get started.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $departments->links() }}</div>
    </x-card>
</x-app-layout>
