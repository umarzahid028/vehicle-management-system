<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Team Member Details') }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('sales-team.edit', $salesTeam) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700">
                    <x-heroicon-o-pencil class="h-4 w-4 mr-1" />
                    Edit
                </a>
                <a href="{{ route('sales-team.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    <x-heroicon-o-arrow-left class="h-4 w-4 mr-1" />
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Photo and Basic Info -->
                        <div class="md:col-span-1">
                            <div class="flex flex-col items-center">
                                @if($salesTeam->photo_path)
                                    <img src="{{ $salesTeam->photo_url }}" alt="{{ $salesTeam->name }}" class="h-48 w-48 rounded-full object-cover mb-4">
                                @else
                                    <div class="h-48 w-48 rounded-full bg-gray-200 flex items-center justify-center mb-4">
                                        <x-heroicon-o-user class="h-24 w-24 text-gray-400" />
                                    </div>
                                @endif
                                <h3 class="text-xl font-medium text-gray-900">{{ $salesTeam->name }}</h3>
                                <p class="text-gray-500">{{ $salesTeam->position }}</p>
                                <span class="mt-2 px-3 py-1 rounded-full text-sm font-medium {{ $salesTeam->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $salesTeam->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>

                        <!-- Contact and Manager Info -->
                        <div class="md:col-span-2">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Contact Information</h4>
                                    <div class="space-y-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-500">Email</label>
                                            <p class="mt-1">{{ $salesTeam->email }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-500">Phone</label>
                                            <p class="mt-1">{{ $salesTeam->phone ?? 'Not provided' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Management</h4>
                                    <div class="space-y-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-500">Reports To</label>
                                            <p class="mt-1">{{ $salesTeam->manager?->name ?? 'Not assigned' }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-500">Added On</label>
                                            <p class="mt-1">{{ $salesTeam->created_at->format('M d, Y') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($salesTeam->bio)
                                <div class="mt-6">
                                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Bio</h4>
                                    <p class="text-gray-700 whitespace-pre-line">{{ $salesTeam->bio }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 