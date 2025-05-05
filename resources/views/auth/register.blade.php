<x-guest-layout>
    <div class="flex flex-col items-center space-y-6 w-full">
        <h1 class="text-2xl font-bold tracking-tight">Create an account</h1>
        <p class="text-sm text-muted-foreground">Enter your information below to create your account</p>
    </div>
    
    <div class="w-full">
        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <!-- Name -->
            <div class="space-y-2">
                <label for="name" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">{{ __('Name') }}</label>
                <input
                    id="name"
                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    autofocus
                    autocomplete="name"
                />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div class="space-y-2">
                <label for="email" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">{{ __('Email') }}</label>
                <input
                    id="email"
                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="username"
                />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="space-y-2">
                <label for="password" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">{{ __('Password') }}</label>
                <input
                    id="password"
                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                    type="password"
                    name="password"
                    required
                    autocomplete="new-password"
                />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="space-y-2">
                <label for="password_confirmation" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">{{ __('Confirm Password') }}</label>
                <input
                    id="password_confirmation"
                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between pt-4">
                <a class="text-sm text-primary underline-offset-4 hover:underline" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <button type="submit" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2">
                    {{ __('Register') }}
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
