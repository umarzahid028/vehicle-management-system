<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Vendors') }}
            </h2>
            <a href="{{ route('vendors.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <x-heroicon-o-plus class="h-4 w-4 mr-1" />
                Add Vendor
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif

                    <!-- Filter Bar -->
                    <div class="mb-6 flex flex-col md:flex-row gap-4 items-start md:items-center">
                        <div>
                            <label for="type-filter" class="block text-sm font-medium text-gray-700 mb-1">Filter by Specialty:</label>
                            <select id="type-filter" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                onchange="window.location.href='{{ route('vendors.index') }}' + (this.value ? '?type=' + this.value : '')">
                                <option value="">All Specialties</option>
                                @foreach($types as $typeKey => $typeName)
                                    <option value="{{ $typeKey }}" {{ request('type') == $typeKey ? 'selected' : '' }}>
                                        {{ $typeName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Vendors Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tags</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($vendors as $vendor)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                <a href="{{ route('vendors.show', $vendor) }}" class="hover:text-indigo-600 hover:underline">
                                                    {{ $vendor->name }}
                                                </a>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex flex-wrap gap-1">
                                                @if(is_array($vendor->specialty_tags) && count($vendor->specialty_tags) > 0)
                                                    @foreach($vendor->specialty_tags as $tag)
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                            @if($tag == 'mechanical') bg-blue-100 text-blue-800
                                                            @elseif($tag == 'body_shop') bg-yellow-100 text-yellow-800
                                                            @elseif($tag == 'detail') bg-green-100 text-green-800
                                                            @elseif($tag == 'tire') bg-purple-100 text-purple-800
                                                            @elseif($tag == 'upholstery') bg-pink-100 text-pink-800
                                                            @elseif($tag == 'glass') bg-indigo-100 text-indigo-800
                                                            @else bg-gray-100 text-gray-800
                                                            @endif">
                                                            {{ $specialties[$tag] ?? ucfirst(str_replace('_', ' ', $tag)) }}
                                                        </span>
                                                    @endforeach
                                                @else
                                                    <span class="text-sm text-gray-500">No specialties assigned</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($vendor->type)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $vendor->type->is_on_site ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                                    {{ $vendor->type->name }}
                                                </span>
                                            @else
                                                <span class="text-xs text-gray-500">Not categorized</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $vendor->contact_person }}
                                                @if($vendor->phone)
                                                    <div class="text-xs text-gray-500">{{ $vendor->phone }}</div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $vendor->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $vendor->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('vendors.edit', $vendor) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                                    <x-heroicon-o-pencil class="h-5 w-5" />
                                                </a>
                                                
                                                <form method="POST" action="{{ route('vendors.toggle-active', $vendor) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="{{ $vendor->is_active ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900' }}" title="{{ $vendor->is_active ? 'Deactivate' : 'Activate' }}">
                                                        @if($vendor->is_active)
                                                            <x-heroicon-o-x-circle class="h-5 w-5" />
                                                        @else
                                                            <x-heroicon-o-check-circle class="h-5 w-5" />
                                                        @endif
                                                    </button>
                                                </form>
                                                
                                                <form method="POST" action="{{ route('vendors.destroy', $vendor) }}" class="inline delete-form" data-name="{{ $vendor->name }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                                        <x-heroicon-o-trash class="h-5 w-5" />
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                            No vendors found.
                                            @if($type)
                                                <a href="{{ route('vendors.index') }}" class="text-indigo-600 hover:text-indigo-900">Clear filter</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const vendorName = this.getAttribute('data-name');
                    
                    if (confirm(`Are you sure you want to delete the vendor "${vendorName}"? This action cannot be undone.`)) {
                        this.submit();
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout> 