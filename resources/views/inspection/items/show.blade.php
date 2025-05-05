<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Inspection Item') }}: {{ $item->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('inspection.items.edit', $item) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <x-heroicon-o-pencil class="h-4 w-4 mr-1" />
                    Edit
                </a>
                <a href="{{ route('inspection.items.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <x-heroicon-o-arrow-left class="h-4 w-4 mr-1" />
                    Back to Items
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6 bg-gray-50">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Item Details
                            </h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                                Detailed information about this inspection item.
                            </p>
                        </div>
                        <div class="border-t border-gray-200">
                            <dl>
                                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        Item Name
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                                        {{ $item->name }}
                                    </dd>
                                </div>
                                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        Inspection Stage
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                                        {{ $item->inspectionStage->name }}
                                    </dd>
                                </div>
                                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        Description
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                                        {{ $item->description ?: 'No description provided' }}
                                    </dd>
                                </div>
                                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        Vendor Required
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item->vendor_required ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $item->vendor_required ? 'Yes' : 'No' }}
                                        </span>
                                    </dd>
                                </div>
                                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        Cost Tracking
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item->cost_tracking ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $item->cost_tracking ? 'Yes' : 'No' }}
                                        </span>
                                    </dd>
                                </div>
                                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        Status
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $item->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6 bg-gray-50 flex justify-between items-center">
                            <div>
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    Inspection History
                                </h3>
                                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                                    Past results for this inspection item.
                                </p>
                            </div>
                        </div>

                        <div class="p-6">
                            @if($item->itemResults && $item->itemResults->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Date
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Vehicle
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Result
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Notes
                                                </th>
                                                @if($item->cost_tracking)
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Cost
                                                </th>
                                                @endif
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Actions
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($item->itemResults as $result)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ $result->created_at->format('M d, Y') }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ $result->vehicleInspection->vehicle->make }} {{ $result->vehicleInspection->vehicle->model }} ({{ $result->vehicleInspection->vehicle->year }})
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $result->status === 'pass' ? 'bg-green-100 text-green-800' : ($result->status === 'fail' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                            {{ ucfirst($result->status) }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ $result->notes ?: 'No notes' }}
                                                    </td>
                                                    @if($item->cost_tracking)
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        ${{ number_format($result->cost, 2) }}
                                                    </td>
                                                    @endif
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        <a href="{{ route('inspection.inspections.show', $result->vehicleInspection) }}" class="text-indigo-600 hover:text-indigo-900">
                                                            View Inspection
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-8 text-gray-500">
                                    <p>No inspection results found for this item.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 