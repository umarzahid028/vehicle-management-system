<x-guest-layout>
    <div class="flex flex-col items-center space-y-6 w-full">
        <h1 class="text-2xl font-bold tracking-tight">Forgot password</h1>
        <p class="text-sm text-muted-foreground text-center">
            {{ __('Enter your email address and we will send you a password reset link.') }}
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="w-full">
        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

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
                    autofocus
                />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between pt-4">
                <a class="text-sm text-primary underline-offset-4 hover:underline" href="{{ route('login') }}">
                    {{ __('Back to login') }}
                </a>

                <button type="submit" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2">
                    {{ __('Email Password Reset Link') }}
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
