@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Roles & Permissions</h2>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-lg font-semibold mb-4">Role Information</h3>
        <form id="roleForm" action="{{ route('roles.update-permissions') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Role Name</label>
                <select id="role" name="role" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach($roles as $r)
                        <option value="{{ $r->id }}" {{ $role->id === $r->id ? 'selected' : '' }}>
                            {{ $r->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mt-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Permissions</h3>
                    <div class="space-x-2">
                        <button type="button" id="selectAll" class="text-sm text-blue-600 hover:text-blue-800">Select All</button>
                        <button type="button" id="deselectAll" class="text-sm text-blue-600 hover:text-blue-800">Deselect All</button>
                    </div>
                </div>
                <div class="space-y-6">
                    @foreach($permissionGroups as $resource => $permissions)
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="text-md font-medium text-gray-700 capitalize">{{ $resource }}</h4>
                                <button type="button" class="text-sm text-gray-600 hover:text-gray-800 toggle-group">Toggle Group</button>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach($permissions as $permission)
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" 
                                               name="permissions[]" 
                                               value="{{ $permission['id'] }}"
                                               {{ $permission['checked'] ? 'checked' : '' }}
                                               class="permission-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-600 capitalize">{{ $permission['action'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Update Permissions
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('role').addEventListener('change', function() {
        window.location.href = "{{ route('roles.index') }}?role=" + this.options[this.selectedIndex].text;
    });

    // Select All functionality
    document.getElementById('selectAll').addEventListener('click', function() {
        document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
            checkbox.checked = true;
        });
    });

    // Deselect All functionality
    document.getElementById('deselectAll').addEventListener('click', function() {
        document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
            checkbox.checked = false;
        });
    });

    // Toggle Group functionality
    document.querySelectorAll('.toggle-group').forEach(button => {
        button.addEventListener('click', function() {
            const group = this.closest('.bg-gray-50');
            const checkboxes = group.querySelectorAll('.permission-checkbox');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = !allChecked;
            });
        });
    });
</script>
@endpush
@endsection 