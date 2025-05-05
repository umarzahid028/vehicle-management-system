<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="space-y-1">
                <h2 class="text-2xl font-semibold tracking-tight">Transport Management</h2>
                <p class="text-sm text-muted-foreground">Manage vehicle transports and deliveries.</p>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('transports.batch.create') }}" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
                    <x-heroicon-o-truck class="mr-2 h-4 w-4" />
                    Batch Transport
                </a>
                <a href="{{ route('transports.create') }}" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
                    <x-heroicon-o-plus class="mr-2 h-4 w-4" />
                    New Transport
                </a>
            </div>
        </div>
    </x-slot>

    <div class="container mx-auto space-y-6">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-200 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <!-- Search & Filter Bar -->
                <div class="mb-6">
                    <form action="{{ route('transports.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <x-shadcn.input
                                type="text"
                                name="search"
                                placeholder="Search by vehicle, batch ID, or destination..."
                                value="{{ request('search') }}"
                            />
                        </div>
                        <div class="w-full md:w-48">
                            <x-shadcn.select name="status">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="in_transit" {{ request('status') == 'in_transit' ? 'selected' : '' }}>In Transit</option>
                                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </x-shadcn.select>
                        </div>
                        <div>
                            <x-shadcn.button type="submit" variant="outline">Search</x-shadcn.button>
                        </div>
                    </form>
                </div>
                
                @if($transports->isEmpty())
                    <div class="text-center py-12">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <p class="mt-4 text-gray-500">No transports found.</p>
                        @if(!auth()->user()->hasRole('Transporter'))
                            <div class="mt-6">
                                <a href="{{ route('transports.create') }}" class="text-indigo-600 hover:text-indigo-900">
                                    + Add your first transport
                                </a>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Vehicle
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Batch Information
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Origin → Destination
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Transporter
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Acknowledgment
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Documents
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($transports as $transport)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $transport->vehicle_count }} {{ Str::plural('Vehicle', $transport->vehicle_count) }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                In this batch
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                @if($transport->batch_id)
                                                    <a href="{{ route('transports.batch', ['batchId' => $transport->batch_id]) }}" class="text-indigo-600 hover:text-indigo-900">
                                                        Batch #{{ $transport->batch_id }}
                                                    </a>
                                                @else
                                                    <span class="text-gray-400">No batch assigned</span>
                                                @endif
                                            </div>
                                            @if($transport->batch_name)
                                                <div class="text-sm text-gray-500">{{ $transport->batch_name }}</div>
                                            @endif
                                            <div class="text-xs text-gray-500 mt-1">
                                                Created {{ $transport->batch_created_at ? \Carbon\Carbon::parse($transport->batch_created_at)->diffForHumans() : 'N/A' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $transport->batch_origin ?: '—' }} → {{ $transport->batch_destination }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                @if($transport->batch_pickup_date || $transport->batch_delivery_date)
                                                    {{ $transport->batch_pickup_date ? \Carbon\Carbon::parse($transport->batch_pickup_date)->format('M d, Y') : 'TBD' }}
                                                    to 
                                                    {{ $transport->batch_delivery_date ? \Carbon\Carbon::parse($transport->batch_delivery_date)->format('M d, Y') : 'TBD' }}
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                @if($transport->transporter_id && $transport->transporter)
                                                    {{ $transport->transporter->name }}
                                                @elseif($transport->batch_transporter_name)
                                                    {{ $transport->batch_transporter_name }}
                                                @else
                                                    <span class="text-gray-400">Not assigned</span>
                                                @endif
                                            </div>
                                            @if($transport->transporter_phone)
                                                <div class="text-xs text-gray-500">{{ $transport->transporter_phone }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($transport->batch_status === 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($transport->batch_status === 'in_transit') bg-blue-100 text-blue-800
                                                @elseif($transport->batch_status === 'delivered') bg-green-100 text-green-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                {{ ucfirst($transport->batch_status) }}
                                            </span>
                                            <div class="text-xs text-gray-500 mt-1">
                                                Last updated {{ $transport->batch_updated_at ? \Carbon\Carbon::parse($transport->batch_updated_at)->diffForHumans() : 'N/A' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($transport->is_acknowledged)
                                                <div class="text-sm text-gray-900">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        All Acknowledged
                                                    </span>
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $transport->acknowledged_at ? \Carbon\Carbon::parse($transport->acknowledged_at)->format('M d, Y H:i') : 'N/A' }}
                                                    @if($transport->acknowledgedBy)
                                                        by {{ $transport->acknowledgedBy->name }}
                                                    @endif
                                                </div>
                                            @else
                                                <div class="text-sm text-gray-900">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        Pending Acknowledgment
                                                    </span>
                                                </div>
                                                @if(auth()->user()->hasRole('Transporter'))
                                                    <form action="{{ route('transports.batch.acknowledge', ['batchId' => $transport->batch_id]) }}" method="POST" class="mt-1">
                                                        @csrf
                                                        <button type="submit" class="text-xs text-blue-600 hover:text-blue-900">
                                                            Acknowledge Now
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex flex-col space-y-2">
                                                @if($transport->qr_code_path)
                                                    <a href="{{ Storage::url($transport->qr_code_path) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 flex items-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                                        </svg>
                                                        QR Code
                                                    </a>
                                                @endif
                                                @if($transport->gate_pass_path)
                                                    <a href="{{ Storage::url($transport->gate_pass_path) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 flex items-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                        Gate Pass
                                                    </a>
                                                @endif
                                                @if(!$transport->qr_code_path && !$transport->gate_pass_path)
                                                    <span class="text-gray-400 text-sm">No documents available</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex space-x-3">
                                                <a href="{{ route('transports.batch', ['batchId' => $transport->batch_id]) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    View Details
                                                </a>
                                                @if(!auth()->user()->hasRole('Transporter'))
                                                    <a href="{{ route('transports.edit', $transport) }}" class="text-indigo-600 hover:text-indigo-900">
                                                        Edit
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $transports->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout> 