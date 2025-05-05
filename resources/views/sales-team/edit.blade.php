<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Team Member') }}: {{ $salesTeam->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <x-sales-team.form :salesTeam="$salesTeam" :managers="$managers" />
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 