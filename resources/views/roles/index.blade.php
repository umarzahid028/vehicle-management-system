<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Roles & Permissions
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto space-y-6 py-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Role Information -->
                    <form action="{{ route('admin.roles.update-permissions') }}" method="POST" id="permissions-form">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="role" value="{{ $role->name }}">
                        
                        <div class="mb-6">
                            <h3 class="text-lg font-medium mb-4">Role Information</h3>
                            <div class="mb-4">
                                <label for="role_select" class="block text-sm font-medium text-gray-700">Role Name</label>
                                <select id="role_select" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                    onchange="window.location.href = '{{ route('admin.roles.index') }}?role=' + this.value">
                                    @foreach($roles as $r)
                                        <option value="{{ $r->name }}" {{ $role->name === $r->name ? 'selected' : '' }}>
                                            {{ \App\Enums\Role::tryFrom($r->name)?->label() ?? $r->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Permissions -->
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium">Permissions</h3>
                                <div class="flex items-center space-x-2">
                                    <button type="button" onclick="selectAll()" class="text-sm text-gray-600 hover:text-gray-900">
                                        <span class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                            Select All
                                        </span>
                                    </button>
                                    <button type="button" onclick="deselectAll()" class="text-sm text-gray-600 hover:text-gray-900">
                                        <span class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                            Deselect All
                                        </span>
                                    </button>
                                </div>
                            </div>

                            <!-- Permission Groups -->
                            <div class="space-y-6">
                                @foreach($permissionGroups as $resource => $permissions)
                                    <div class="border rounded-lg overflow-hidden">
                                        <div class="bg-gray-50 px-4 py-2 border-b flex items-center justify-between">
                                            <h4 class="text-sm font-medium text-gray-900">{{ ucfirst($resource) }}</h4>
                                            <button type="button" class="text-sm text-gray-600 hover:text-gray-900" onclick="toggleGroup('{{ $resource }}')">
                                                Toggle Group
                                            </button>
                                        </div>
                                        <div class="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                            @foreach($permissions as $permission)
                                                <label class="relative flex cursor-pointer group">
                                                    <div class="flex items-start p-4 border rounded-lg hover:bg-gray-50 w-full {{ $permission['checked'] ? 'border-primary-500 bg-primary-50' : '' }}">
                                                        <div class="flex items-center h-5">
                                                            <input type="checkbox" 
                                                                class="permission-checkbox h-4 w-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500" 
                                                                name="permissions[]" 
                                                                value="{{ $permission['id'] }}"
                                                                data-resource="{{ $resource }}"
                                                                {{ $permission['checked'] ? 'checked' : '' }}
                                                                {{ $role->name === 'Admin' ? 'disabled' : '' }}>
                                                        </div>
                                                        <div class="ml-3 text-sm">
                                                            <p class="font-medium text-gray-900">{{ ucfirst($permission['action']) }}</p>
                                                            <p class="text-gray-500">{{ $permission['name'] }}</p>
                                                        </div>
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Save Changes Button -->
                            <div class="mt-6 flex justify-end">
                                <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-900 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 disabled:opacity-50"
                                    {{ $role->name === 'Admin' ? 'disabled' : '' }}>
                                        Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function selectAll() {
            const checkboxes = document.querySelectorAll('.permission-checkbox:not(:disabled)');
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
        }

        function deselectAll() {
            const checkboxes = document.querySelectorAll('.permission-checkbox:not(:disabled)');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
        }

        function toggleGroup(resource) {
            const checkboxes = document.querySelectorAll(`.permission-checkbox[data-resource="${resource}"]:not(:disabled)`);
            const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = !allChecked;
            });
        }
    </script>
    @endpush
</x-app-layout> 