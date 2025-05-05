<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Add Team Member') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8">
                    <!-- Form Header -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900">Team Member Information</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Add a new member to your sales team. Fill in their details below.
                        </p>
                    </div>

                    <!-- Form Component -->
                    <x-sales-team.form :managers="$managers" />
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 