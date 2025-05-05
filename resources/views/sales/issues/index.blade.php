<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Sales Issues') }}
            </h2>
            <a href="{{ route('sales.issues.create') }}">
                <x-shadcn.button variant="outline" class="bg-black text-white hover:bg-gray-50 hover:text-black">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    {{ __('Report Issue') }}
                </x-shadcn.button>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Filters -->
                    <div class="mb-6">
                        <form action="{{ route('sales.issues.index') }}" method="GET" class="flex flex-wrap gap-4">
                            <div class="flex-1 min-w-[200px]">
                                <x-input-label for="status" :value="__('Status')" />
                                <select id="status" name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">All Statuses</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                            </div>
                            <div class="flex-1 min-w-[200px]">
                                <x-input-label for="priority" :value="__('Priority')" />
                                <select id="priority" name="priority" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">All Priorities</option>
                                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                                    <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                            </div>
                            <div class="flex items-end">
                                <x-primary-button type="submit">
                                    {{ __('Filter') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>

                    <!-- Issues Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehicle</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Issue Type</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reported By</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($issues as $issue)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <a href="{{ route('vehicles.show', $issue->vehicle) }}" class="text-indigo-600 hover:text-indigo-900">
                                                {{ $issue->vehicle->stock_number }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @php
                                                $issueTypes = [
                                                    'mechanical' => 'Mechanical Issue',
                                                    'cosmetic' => 'Cosmetic Issue',
                                                    'documentation' => 'Documentation Issue',
                                                    'other' => 'Other Issue'
                                                ];
                                            @endphp
                                            {{ $issueTypes[$issue->issue_type] ?? ucfirst($issue->issue_type) }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ Str::limit($issue->description, 50) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ $issue->priority === 'urgent' ? 'bg-red-100 text-red-800' : '' }}
                                                {{ $issue->priority === 'high' ? 'bg-orange-100 text-orange-800' : '' }}
                                                {{ $issue->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $issue->priority === 'low' ? 'bg-green-100 text-green-800' : '' }}">
                                                {{ ucfirst($issue->priority) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ $issue->status === 'pending' ? 'bg-gray-100 text-gray-800' : '' }}
                                                {{ $issue->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $issue->status === 'resolved' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $issue->status === 'closed' ? 'bg-gray-100 text-gray-800' : '' }}">
                                                {{ str_replace('_', ' ', ucfirst($issue->status)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $issue->reportedBy->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $issue->created_at->diffForHumans() }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('sales.issues.show', $issue) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            No sales issues found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $issues->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div x-data="{ show: true }"
             x-show="show"
             x-transition
             x-init="setTimeout(() => show = false, 3000)"
             class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
            {{ session('success') }}
        </div>
    @endif
</x-app-layout> 