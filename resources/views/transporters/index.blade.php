<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Transporter Management') }}
            </h2>
            <div>
                <a href="{{ route('transporters.create') }}">
                    <x-shadcn.button variant="default">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        {{ __('Add Transporter') }}
                    </x-shadcn.button>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Flash Messages -->
                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-200 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 p-4 bg-red-100 border border-red-200 text-red-700 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Search and Filter -->
                    <div class="mb-6">
                        <form action="{{ route('transporters.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                            <div class="flex-1">
                                <label for="search" class="sr-only">Search</label>
                                <div class="relative rounded-md">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <x-shadcn.input 
                                        type="text" 
                                        name="search" 
                                        id="search" 
                                        :value="request('search')" 
                                        placeholder="Search by Name, Contact Person, or Email"
                                        class="pl-10" 
                                    />
                                </div>
                            </div>
                            <div class="w-full md:w-48">
                                <label for="status" class="sr-only">Status</label>
                                <x-shadcn.select name="status" id="status" placeholder="All Status">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </x-shadcn.select>
                            </div>
                            <div>
                                <x-shadcn.button type="submit" variant="default">
                                    Filter
                                </x-shadcn.button>
                                @if(request('search') || request('status'))
                                    <a href="{{ route('transporters.index') }}">
                                        <x-shadcn.button type="button" variant="outline" class="ml-2">
                                            Clear
                                        </x-shadcn.button>
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>

                    <!-- Transporters Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Name
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Contact Information
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Location
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($transporters as $transporter)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <div class="font-semibold">{{ $transporter->name }}</div>
                                            @if ($transporter->contact_person)
                                                <div class="text-xs text-gray-500">
                                                    {{ $transporter->contact_person }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if ($transporter->phone)
                                                <div>
                                                    <span class="font-medium">Phone:</span> {{ $transporter->phone }}
                                                </div>
                                            @endif
                                            @if ($transporter->email)
                                                <div>
                                                    <span class="font-medium">Email:</span> {{ $transporter->email }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if ($transporter->city && $transporter->state)
                                                <div>{{ $transporter->city }}, {{ $transporter->state }}</div>
                                            @endif
                                            @if ($transporter->address)
                                                <div class="text-xs">{{ $transporter->address }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $transporter->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}
                                            ">
                                                {{ $transporter->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end space-x-2">
                                                <a href="{{ route('transporters.show', $transporter) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>
                                                <a href="{{ route('transporters.edit', $transporter) }}" class="text-yellow-600 hover:text-yellow-900">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                                <form action="{{ route('transporters.destroy', $transporter) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this transporter?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            No transporters found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $transporters->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>