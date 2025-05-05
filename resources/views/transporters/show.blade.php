<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Transporter Details') }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('transporters.index') }}">
                    <x-shadcn.button variant="outline">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        {{ __('Back to List') }}
                    </x-shadcn.button>
                </a>
                @if(auth()->user()->hasRole('Admin'))
                    <a href="{{ route('transporters.edit', $transporter) }}">
                        <x-shadcn.button>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            {{ __('Edit') }}
                        </x-shadcn.button>
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                            <dl class="grid grid-cols-1 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Company Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $transporter->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Contact Person</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $transporter->contact_person ?? 'Not specified' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $transporter->email }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Phone</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $transporter->phone ?? 'Not specified' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $transporter->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $transporter->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Address Information</h3>
                            <dl class="grid grid-cols-1 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Address</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $transporter->address ?? 'Not specified' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">City</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $transporter->city ?? 'Not specified' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">State</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $transporter->state ?? 'Not specified' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">ZIP Code</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $transporter->zip ?? 'Not specified' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    @if($transporter->notes)
                        <div class="mt-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Notes</h3>
                            <div class="bg-gray-50 p-4 rounded-md">
                                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $transporter->notes }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Transport History</h3>
                        @if($transporter->transports->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehicle</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pickup Date</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delivery Date</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($transporter->transports as $transport)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $transport->vehicle->year }} {{ $transport->vehicle->make }} {{ $transport->vehicle->model }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $transport->vehicle->vin }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                        {{ $transport->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                        {{ $transport->status === 'in_transit' ? 'bg-blue-100 text-blue-800' : '' }}
                                                        {{ $transport->status === 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                                                        {{ $transport->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                                        {{ ucfirst($transport->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $transport->pickup_date ? date('M d, Y', strtotime($transport->pickup_date)) : 'Not scheduled' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $transport->delivery_date ? date('M d, Y', strtotime($transport->delivery_date)) : 'Not scheduled' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    <a href="{{ route('transports.show', $transport) }}" class="text-indigo-600 hover:text-indigo-900">View Details</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">No transport history available.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 