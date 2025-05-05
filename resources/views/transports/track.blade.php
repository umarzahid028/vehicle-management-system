<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Batch {{ $batchId }} Tracking</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            background-color: #f9fafb;
            font-family: 'Figtree', sans-serif;
            color: #1f2937;
            -webkit-font-smoothing: antialiased;
        }
        .progress-track {
            --percentage: 0%;
            --track-color: #dbeafe;
            --status-color: #3b82f6;
        }
        .progress-track[data-status="pending"] {
            --track-color: #fef3c7;
            --status-color: #f59e0b;
            --percentage: 0%;
        }
        .progress-track[data-status="in_transit"] {
            --track-color: #dbeafe;
            --status-color: #3b82f6;
            --percentage: 50%;
        }
        .progress-track[data-status="delivered"] {
            --track-color: #d1fae5;
            --status-color: #10b981;
            --percentage: 100%;
        }
        .progress-track[data-status="cancelled"] {
            --track-color: #fee2e2;
            --status-color: #ef4444;
            --percentage: 100%;
        }
        .progress-track-bar {
            height: 6px;
            background-color: var(--track-color);
            border-radius: 9999px;
            overflow: hidden;
            position: relative;
        }
        .progress-track-fill {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            background-color: var(--status-color);
            border-radius: 9999px;
            width: var(--percentage);
            transition: width 0.5s ease-in-out;
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.35rem 0.75rem;
            font-weight: 500;
            font-size: 0.875rem;
            border-radius: 9999px;
        }
        .status-pending { 
            background-color: #fef3c7; 
            color: #92400e; 
        }
        .status-in_transit { 
            background-color: #dbeafe; 
            color: #1e40af; 
        }
        .status-delivered { 
            background-color: #d1fae5; 
            color: #065f46; 
        }
        .status-cancelled { 
            background-color: #fee2e2; 
            color: #b91c1c; 
        }
        .timeline-container {
            position: relative;
            padding-left: 28px;
        }
        .timeline-container::before {
            content: "";
            position: absolute;
            top: 0;
            bottom: 0;
            left: 8px;
            width: 2px;
            background-color: #e5e7eb;
        }
        .timeline-marker {
            position: absolute;
            left: 0;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background-color: var(--status-color, #3b82f6);
            border: 3px solid white;
            box-shadow: 0 0 0 2px var(--status-color, #3b82f6);
            z-index: 1;
        }
        .card {
            background-color: #fff;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        .text-xs { font-size: 0.75rem; }
        .text-sm { font-size: 0.875rem; }
        .text-base { font-size: 1rem; }
        .text-lg { font-size: 1.125rem; }
        .text-xl { font-size: 1.25rem; }
        .text-2xl { font-size: 1.5rem; }
        .font-medium { font-weight: 500; }
        .font-semibold { font-weight: 600; }
        .font-bold { font-weight: 700; }
        .text-gray-400 { color: #9ca3af; }
        .text-gray-500 { color: #6b7280; }
        .text-gray-600 { color: #4b5563; }
        .text-gray-700 { color: #374151; }
        .text-gray-800 { color: #1f2937; }
        .text-gray-900 { color: #111827; }
        .flex { display: flex; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .mb-1 { margin-bottom: 0.25rem; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mb-3 { margin-bottom: 0.75rem; }
        .mb-4 { margin-bottom: 1rem; }
        .mb-6 { margin-bottom: 1.5rem; }
        .mt-1 { margin-top: 0.25rem; }
        .mt-2 { margin-top: 0.5rem; }
        .mt-3 { margin-top: 0.75rem; }
        .mt-4 { margin-top: 1rem; }
        .mt-6 { margin-top: 1.5rem; }
        .ml-2 { margin-left: 0.5rem; }
        .mr-2 { margin-right: 0.5rem; }
        .gap-2 { gap: 0.5rem; }
        .gap-4 { gap: 1rem; }
        .p-4 { padding: 1rem; }
        .py-1 { padding-top: 0.25rem; padding-bottom: 0.25rem; }
        .py-2 { padding-top: 0.5rem; padding-bottom: 0.5rem; }
        .px-2 { padding-left: 0.5rem; padding-right: 0.5rem; }
        .px-3 { padding-left: 0.75rem; padding-right: 0.75rem; }
        .py-4 { padding-top: 1rem; padding-bottom: 1rem; }
        .grid { display: grid; }
        .grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
        .grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .rounded-md { border-radius: 0.375rem; }
        .rounded-full { border-radius: 9999px; }
        .border { border-width: 1px; }
        .border-gray-200 { border-color: #e5e7eb; }
        .shadow-sm { box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); }
        
        @media (min-width: 768px) {
            .grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .md\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .container { max-width: 768px; }
        }
        @media (min-width: 640px) {
            .container { max-width: 640px; }
        }
        .container {
            width: 100%;
            margin-left: auto;
            margin-right: auto;
            padding-left: 1rem;
            padding-right: 1rem;
        }
        .truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .whitespace-pre-line {
            white-space: pre-line;
        }
    </style>
</head>
<body>
    <div class="py-4">
        <div class="container max-w-3xl mx-auto">
            <!-- Header with Live Status -->
            <div class="card">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">{{ $batchData->batch_name ?: 'Batch' }}</h1>
                        <p class="text-gray-600">{{ $batchId }}</p>
                    </div>
                    <span class="status-badge status-{{ $batchData->status }}">
                        {{ ucfirst($batchData->status) }}
                    </span>
                </div>
                
                <!-- Progress Tracker -->
                <div class="mb-6">
                    <div class="flex justify-between text-sm mb-1">
                        <span>Origin</span>
                        <span>In Transit</span>
                        <span>Delivered</span>
                    </div>
                    <div class="progress-track" data-status="{{ $batchData->status }}">
                        <div class="progress-track-bar">
                            <div class="progress-track-fill"></div>
                        </div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 mt-1">
                        <span>{{ $batchData->origin ?: 'Not specified' }}</span>
                        <span>{{ $batchData->destination }}</span>
                    </div>
                </div>
                
                <!-- Key Info Cards -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-3 bg-gray-50 rounded-md">
                        <p class="text-xs text-gray-500">Vehicles</p>
                        <p class="font-semibold">{{ $transports->count() }}</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-md">
                        <p class="text-xs text-gray-500">Expected Delivery</p>
                        <p class="font-semibold">{{ $batchData->delivery_date ? $batchData->delivery_date->format('M d, Y') : 'Not scheduled' }}</p>
                    </div>
                </div>
            </div>
            
        
            <!-- Route Information -->
            <div class="card">
                <h2 class="text-lg font-semibold mb-4">Shipment Details</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-sm text-gray-500">Origin</p>
                        <p class="font-medium">{{ $batchData->origin ?: 'Not specified' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Destination</p>
                        <p class="font-medium">{{ $batchData->destination }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Pickup Date</p>
                        <p class="font-medium">{{ $batchData->pickup_date ? $batchData->pickup_date->format('M d, Y') : 'Not scheduled' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Expected Delivery</p>
                        <p class="font-medium">{{ $batchData->delivery_date ? $batchData->delivery_date->format('M d, Y') : 'Not scheduled' }}</p>
                    </div>
                </div>
                
                <div class="rounded-md overflow-hidden bg-gray-100 h-32 flex items-center justify-center mb-2">
                    <div class="text-gray-500 text-sm flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                        Route map
                    </div>
                </div>
            </div>
            
            <!-- Transporter Information -->
            <div class="card">
                <h2 class="text-lg font-semibold mb-4">Transporter</h2>
                
                @if($batchData->transporter_id && $batchData->transporter)
                    <div class="flex items-center gap-4 mb-3">
                        <div class="flex-shrink-0 w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium">{{ $batchData->transporter->name }}</p>
                            <p class="text-sm text-gray-500">Professional Transporter</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($batchData->transporter->phone)
                            <div>
                                <p class="text-sm text-gray-500">Phone</p>
                                <p class="font-medium">{{ $batchData->transporter->phone }}</p>
                            </div>
                        @endif
                        @if($batchData->transporter->email)
                            <div>
                                <p class="text-sm text-gray-500">Email</p>
                                <p class="font-medium">{{ $batchData->transporter->email }}</p>
                            </div>
                        @endif
                    </div>
                @elseif($batchData->transporter_name)
                    <div class="flex items-center gap-4 mb-3">
                        <div class="flex-shrink-0 w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium">{{ $batchData->transporter_name }}</p>
                            <p class="text-sm text-gray-500">External Transporter</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($batchData->transporter_phone)
                            <div>
                                <p class="text-sm text-gray-500">Phone</p>
                                <p class="font-medium">{{ $batchData->transporter_phone }}</p>
                            </div>
                        @endif
                        @if($batchData->transporter_email)
                            <div>
                                <p class="text-sm text-gray-500">Email</p>
                                <p class="font-medium">{{ $batchData->transporter_email }}</p>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="py-3 px-4 bg-gray-50 rounded-md">
                        <p class="text-gray-500">No transporter information available</p>
                    </div>
                @endif
            </div>
            
            <!-- Vehicles in Batch -->
            <div class="card">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold">Vehicles</h2>
                    <span class="text-sm text-gray-500 py-1 px-2 bg-gray-100 rounded-md">{{ $transports->count() }} {{ Str::plural('vehicle', $transports->count()) }}</span>
                </div>
                
                <div class="space-y-3">
                    @foreach($transports as $transport)
                        <div class="border border-gray-200 rounded-md p-3">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $transport->vehicle->year }} {{ $transport->vehicle->make }} {{ $transport->vehicle->model }}</p>
                                    <p class="text-sm text-gray-500">{{ $transport->vehicle->stock_number }}</p>
                                </div>
                                <span class="status-badge status-{{ $transport->status }}">
                                    {{ ucfirst($transport->status) }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500">VIN: {{ $transport->vehicle->vin }}</span>
                                @if($transport->gate_pass_path)
                                    <a href="{{ Storage::url($transport->gate_pass_path) }}" target="_blank" class="flex items-center text-indigo-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Gate Pass
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Notes -->
            @if($batchData->notes)
                <div class="card">
                    <h2 class="text-lg font-semibold mb-4">Notes</h2>
                    <div class="p-3 bg-gray-50 rounded-md">
                        <p class="whitespace-pre-line">{{ $batchData->notes }}</p>
                    </div>
                </div>
            @endif
            
            <!-- Footer -->
            <div class="text-center text-gray-500 text-xs my-6">
                <p>Last updated: {{ $batchData->updated_at->format('M d, Y h:i A') }}</p>
                <p class="mt-1">Scan date: {{ now()->format('M d, Y h:i A') }}</p>
            </div>
        </div>
    </div>
</body>
</html> 