<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-zinc-800 dark:text-zinc-200">
                {{ __('Sales Assignment Details') }}
            </h2>
            <div>
                <a href="{{ route('sales-assignments.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-600 rounded-md hover:bg-zinc-50 dark:hover:bg-zinc-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    {{ __('Back to List') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @include('layouts.partials.messages')
            
            <div class="overflow-hidden bg-white shadow-sm dark:bg-zinc-950 sm:rounded-lg">
                <div class="px-6 py-5 border-b border-zinc-200 dark:border-zinc-800">
                    <h3 class="text-lg font-medium leading-6 text-zinc-900 dark:text-zinc-100">
                        {{ __('Vehicle Details') }}
                    </h3>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dl class="grid grid-cols-1 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Stock Number') }}</dt>
                                    <dd class="mt-1 text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ $vehicle->stock_number }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Vehicle') }}</dt>
                                    <dd class="mt-1 text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }} {{ $vehicle->trim }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('VIN') }}</dt>
                                    <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $vehicle->vin }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Status') }}</dt>
                                    <dd class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ ucfirst($vehicle->status) }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Assignment Details -->
            <div class="mt-8 overflow-hidden bg-white shadow-sm dark:bg-zinc-950 sm:rounded-lg">
                <div class="px-6 py-5 border-b border-zinc-200 dark:border-zinc-800">
                    <h3 class="text-lg font-medium leading-6 text-zinc-900 dark:text-zinc-100">
                        {{ __('Assignment Details') }}
                    </h3>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dl class="grid grid-cols-1 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Assigned To') }}</dt>
                                    <dd class="mt-1 text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                                        {{ $vehicle->salesTeam->name ?? 'N/A' }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Email') }}</dt>
                                    <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                                        {{ $vehicle->salesTeam->email ?? 'N/A' }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Assigned By') }}</dt>
                                    <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                                        {{ $vehicle->assignedBy->name ?? 'N/A' }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Assigned Date') }}</dt>
                                    <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                                        {{ $vehicle->assigned_for_sale_at ? $vehicle->assigned_for_sale_at->format('M d, Y H:i') : 'N/A' }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                        
                        <div>
                            <dl class="grid grid-cols-1 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Inspection Completed') }}</dt>
                                    <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                                        @if($vehicle->vehicleInspections->isNotEmpty())
                                            {{ $vehicle->vehicleInspections->first()->completed_date ? $vehicle->vehicleInspections->first()->completed_date->format('M d, Y') : 'N/A' }}
                                        @else
                                            {{ __('N/A') }}
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                    
                    <div class="flex justify-end mt-6">
                        <form action="{{ route('sales-assignments.destroy', $vehicle) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" onclick="return confirm('{{ __('Are you sure you want to remove this sales assignment?') }}')">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                {{ __('Remove Assignment') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 