<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Start New Vehicle Inspection') }}
            </h2>
            <a href="{{ route('inspection.inspections.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <x-heroicon-o-arrow-left class="h-4 w-4 mr-1" />
                Back to Inspections
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($errors->any())
                        <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                            <p class="font-bold">Please fix the following errors:</p>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('info'))
                        <div class="mb-6 bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4" role="alert">
                            <p>{{ session('info') }}</p>
                        </div>
                    @endif

                    <div class="mb-5 p-4 bg-blue-50 border-l-4 border-blue-500 text-blue-700">
                        <h3 class="text-lg font-medium mb-2">Comprehensive Vehicle Inspection</h3>
                        <p>Select a vehicle below to start the inspection process. This comprehensive system allows you to inspect all stages at once and easily assign vendors to repair items.</p>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Vehicle Selection -->
                            <div>
                                <x-input-label for="vehicle_id" :value="__('Select Vehicle')" />
                                <select id="vehicle_id" name="vehicle_id" 
                                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required>
                                    <option value="">Select a vehicle</option>
                                    @foreach($vehicles as $id => $vehicle)
                                        <option value="{{ $id }}">{{ $vehicle }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('vehicle_id')" class="mt-2" />
                            </div>

                            <div class="flex items-end">
                                <button type="button" id="start-comprehensive-inspection" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <x-heroicon-o-clipboard-document-list class="h-4 w-4 mr-1" />
                                    Start Inspection
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('start-comprehensive-inspection').addEventListener('click', function() {
                const vehicleId = document.getElementById('vehicle_id').value;
                if (vehicleId) {
                    window.location.href = `/inspection/vehicles/${vehicleId}/comprehensive`;
                } else {
                    alert('Please select a vehicle first');
                }
            });
        });
    </script>
    @endpush
</x-app-layout> 