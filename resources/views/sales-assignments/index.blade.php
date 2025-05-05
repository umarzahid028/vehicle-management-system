<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-zinc-800 dark:text-zinc-200">
                {{ __('Sales Team Assignments') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-full sm:px-6 lg:px-8">
            @include('layouts.partials.messages')
            
            <!-- Vehicles Ready for Sales Assignment -->
            <div class="mb-8 overflow-hidden bg-white shadow-sm dark:bg-zinc-950 sm:rounded-lg">
                <div class="px-6 py-5 border-b border-zinc-200 dark:border-zinc-800">
                    <h3 class="text-lg font-medium leading-6 text-zinc-900 dark:text-zinc-100">
                        {{ __('Vehicles Ready for Sales Assignment') }}
                    </h3>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                        {{ __('These vehicles have completed inspections and repairs, and are ready to be assigned to sales team members.') }}
                    </p>
                </div>
                
                <div class="p-6">
                    @if($readyVehicles->isEmpty())
                        <div class="text-center py-4">
                            <p class="text-zinc-500 dark:text-zinc-400">
                                {{ __('No vehicles ready for sales assignment at this time.') }}
                            </p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                                <thead class="bg-zinc-50 dark:bg-zinc-950">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                            {{ __('Vehicle') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                            {{ __('VIN') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                            {{ __('Stock #') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                            {{ __('Inspection Completed') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                            {{ __('Actions') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-zinc-200 dark:bg-zinc-950 dark:divide-zinc-800">
                                    @foreach($readyVehicles as $vehicle)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                                    {{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}
                                                </div>
                                                <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                                    {{ $vehicle->trim }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                                {{ $vehicle->vin }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                                {{ $vehicle->stock_number }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                                @if($vehicle->vehicleInspections->isNotEmpty())
                                                    {{ $vehicle->vehicleInspections->first()->completed_date ? $vehicle->vehicleInspections->first()->completed_date->format('M d, Y') : 'N/A' }}
                                                @else
                                                    {{ __('N/A') }}
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('sales-assignments.create', $vehicle) }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6z" />
                                                        <path d="M16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z" />
                                                    </svg>
                                                    {{ __('Assign to Sales') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            {{ $readyVehicles->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Vehicles Already Assigned to Sales -->
            <div class="overflow-hidden bg-white shadow-sm dark:bg-zinc-950 sm:rounded-lg">
                <div class="px-6 py-5 border-b border-zinc-200 dark:border-zinc-800">
                    <h3 class="text-lg font-medium leading-6 text-zinc-900 dark:text-zinc-100">
                        {{ __('Vehicles Assigned to Sales Team') }}
                    </h3>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                        {{ __('These vehicles have been assigned to sales team members.') }}
                    </p>
                </div>
                
                <div class="p-6">
                    @if($assignedVehicles->isEmpty())
                        <div class="text-center py-4">
                            <p class="text-zinc-500 dark:text-zinc-400">
                                {{ __('No vehicles are currently assigned to sales team members.') }}
                            </p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                                <thead class="bg-zinc-50 dark:bg-zinc-950">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                            {{ __('Vehicle') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                            {{ __('Stock #') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                            {{ __('Assigned To') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                            {{ __('Assigned By') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                            {{ __('Assigned Date') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                            {{ __('Actions') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-zinc-200 dark:bg-zinc-950 dark:divide-zinc-800">
                                    @foreach($assignedVehicles as $vehicle)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                                    {{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}
                                                </div>
                                                <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                                    {{ $vehicle->trim }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                                {{ $vehicle->stock_number }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                                    {{ $vehicle->salesTeam->name ?? 'N/A' }}
                                                </div>
                                                <div class="text-sm text-zinc-500 dark:text-zinc-400">
                                                    {{ $vehicle->salesTeam->email ?? '' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                                {{ $vehicle->assignedBy->name ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                                {{ $vehicle->assigned_for_sale_at ? $vehicle->assigned_for_sale_at->format('M d, Y H:i') : 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('sales-assignments.show', $vehicle) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3">
                                                    {{ __('View') }}
                                                </a>
                                                
                                                <form action="{{ route('sales-assignments.destroy', $vehicle) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" onclick="return confirm('{{ __('Are you sure you want to remove this sales assignment?') }}')">
                                                        {{ __('Remove') }}
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            {{ $assignedVehicles->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 