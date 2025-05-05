<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-zinc-800 dark:text-zinc-200">
                {{ __('Assign Vehicle to Sales Team') }}
            </h2>
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
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            {{ ucfirst($vehicle->status) }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                        
                        <div>
                            <dl class="grid grid-cols-1 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Inspection Completed') }}</dt>
                                    <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                                        {{ isset($completedInspection) && $completedInspection->completed_date ? $completedInspection->completed_date->format('M d, Y') : 'N/A' }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Repair Status') }}</dt>
                                    <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            {{ __('All Repairs Completed') }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Date Added') }}</dt>
                                    <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                                        {{ $vehicle->created_at ? $vehicle->created_at->format('M d, Y') : 'N/A' }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Assignment Form -->
            <div class="mt-8 overflow-hidden bg-white shadow-sm dark:bg-zinc-950 sm:rounded-lg">
                <div class="px-6 py-5 border-b border-zinc-200 dark:border-zinc-800">
                    <h3 class="text-lg font-medium leading-6 text-zinc-900 dark:text-zinc-100">
                        {{ __('Assign to Sales Team Member') }}
                    </h3>
                </div>
                
                <div class="p-6">
                    <form action="{{ route('sales-assignments.store', $vehicle) }}" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label for="sales_team_id" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                    {{ __('Select Sales Team Member') }} <span class="text-red-500">*</span>
                                </label>
                                <select id="sales_team_id" name="sales_team_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option value="">{{ __('-- Select Sales Team Member --') }}</option>
                                    @foreach($salesTeamMembers as $member)
                                        <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->email }})</option>
                                    @endforeach
                                </select>
                                @error('sales_team_id')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="notes" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                    {{ __('Notes') }}
                                </label>
                                <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full border border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ __('Optional notes about this assignment.') }}
                                </p>
                            </div>
                            
                            <div class="flex justify-end mt-4">
                                <a href="{{ route('sales-assignments.index') }}" class="inline-flex items-center px-4 py-2 border border-zinc-300 dark:border-zinc-600 text-sm font-medium rounded-md text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-4">
                                    {{ __('Cancel') }}
                                </a>
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    {{ __('Assign to Sales Team') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 