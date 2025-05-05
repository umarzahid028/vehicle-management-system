<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Sales Team') }}
            </h2>
            <a href="{{ route('sales-team.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                <x-heroicon-o-plus class="h-4 w-4 mr-1" />
                Add Team Member
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto space-y-6">
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Team Member
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Position
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Contact
                                    </th>
                                    
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($salesTeam as $member)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    @if($member->photo_path)
                                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ $member->photo_url }}" alt="{{ $member->name }}">
                                                    @else
                                                        <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                            <x-heroicon-o-user class="h-6 w-6 text-gray-400" />
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $member->name }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $member->position }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $member->email }}</div>
                                            @if($member->phone)
                                                <div class="text-sm text-gray-500">{{ $member->phone }}</div>
                                            @endif
                                        </td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <form action="{{ route('sales-team.toggle-active', $member) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $member->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $member->is_active ? 'Active' : 'Inactive' }}
                                                </button>
                                            </form>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end space-x-2">
                                                <a href="{{ route('sales-team.show', $member) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    <x-heroicon-o-eye class="h-5 w-5" />
                                                </a>
                                                <a href="{{ route('sales-team.edit', $member) }}" class="text-yellow-600 hover:text-yellow-900">
                                                    <x-heroicon-o-pencil class="h-5 w-5" />
                                                </a>
                                                <form action="{{ route('sales-team.destroy', $member) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this team member?')">
                                                        <x-heroicon-o-trash class="h-5 w-5" />
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                            No team members found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $salesTeam->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 