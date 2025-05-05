@props(['salesTeam' => null, 'managers' => []])

<form method="POST" 
      action="{{ $salesTeam ? route('sales-team.update', $salesTeam) : route('sales-team.store') }}"
      enctype="multipart/form-data"
      class="space-y-6">
    @csrf
    @if($salesTeam)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
            <input type="text" 
                   name="name" 
                   id="name" 
                   value="{{ old('name', $salesTeam?->name) }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                   required>
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" 
                   name="email" 
                   id="email" 
                   value="{{ old('email', $salesTeam?->email) }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                   required>
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="col-span-2 md:col-span-1">
            <x-password-input
                id="password"
                name="password"
                label="Password"
                :required="!isset($salesTeam)"
                placeholder="Enter password"
                :help-text="isset($salesTeam) ? 'Leave blank to keep current password' : 'Password must be at least 8 characters'"
            />
        </div>

        <!-- Password Confirmation -->
        <div class="col-span-2 md:col-span-1">
            <x-password-input
                id="password_confirmation"
                name="password_confirmation"
                label="Confirm Password"
                :required="!isset($salesTeam)"
                placeholder="Confirm password"
            />
        </div>

        <!-- Phone -->
        <div>
            <label for="position" class="block text-sm font-medium text-gray-700">Position</label>
            <input type="text" 
                   name="position" 
                   id="position" 
                   value="{{ old('position', $salesTeam?->position) }}"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                   required>
            @error('position')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Manager -->
        <div>
            <label for="manager_id" class="block text-sm font-medium text-gray-700">Manager</label>
            <select name="manager_id" 
                    id="manager_id" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="">Select a manager</option>
                @foreach($managers as $manager)
                    <option value="{{ $manager->id }}" {{ old('manager_id', $salesTeam?->manager_id) == $manager->id ? 'selected' : '' }}>
                        {{ $manager->name }}
                    </option>
                @endforeach
            </select>
            @error('manager_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Photo -->
        <div>
            <label for="photo" class="block text-sm font-medium text-gray-700">Photo</label>
            <div class="mt-1 flex items-center space-x-4">
                @if($salesTeam?->photo_path)
                    <div class="flex-shrink-0 h-12 w-12">
                        <img class="h-12 w-12 rounded-full object-cover" src="{{ $salesTeam->photo_url }}" alt="{{ $salesTeam->name }}">
                    </div>
                @endif
                <input type="file" 
                       name="photo" 
                       id="photo" 
                       accept="image/*"
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
            </div>
            @error('photo')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Active Status -->
        <div>
            <div class="flex items-center">
                <input type="checkbox" 
                       name="is_active" 
                       id="is_active" 
                       value="1"
                       {{ old('is_active', $salesTeam?->is_active ?? true) ? 'checked' : '' }}
                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <label for="is_active" class="ml-2 block text-sm text-gray-700">Active</label>
            </div>
            @error('is_active')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Bio -->
    <div>
        <label for="bio" class="block text-sm font-medium text-gray-700">Bio</label>
        <textarea name="bio" 
                  id="bio" 
                  rows="4"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('bio', $salesTeam?->bio) }}</textarea>
        @error('bio')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex justify-end space-x-3">
        <a href="{{ route('sales-team.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
            Cancel
        </a>
        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
            {{ $salesTeam ? 'Update' : 'Create' }} Team Member
        </button>
    </div>
</form> 