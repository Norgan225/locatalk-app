<x-guest-layout>
    <!-- Title -->
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-white mb-2">Bienvenue</h2>
        <p class="text-gray-400">Connectez-vous à votre espace LocaTalk</p>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-6 p-4 rounded-xl bg-green-500/10 border border-green-500/20">
            <p class="text-sm text-green-400">{{ session('status') }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-300 mb-2">
                Adresse email
            </label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
                placeholder="votre@email.com"
                class="input-field block w-full px-4 py-3.5 rounded-xl text-sm"
            />
            @error('email')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
                Mot de passe
            </label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="••••••••"
                class="input-field block w-full px-4 py-3.5 rounded-xl text-sm"
            />
            @error('password')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between">
            <label class="inline-flex items-center cursor-pointer">
                <input
                    id="remember_me"
                    type="checkbox"
                    name="remember"
                    class="w-4 h-4 rounded border-gray-600 bg-gray-700 text-orange-500 focus:ring-2 focus:ring-orange-500 focus:ring-offset-0"
                >
                <span class="ml-2 text-sm text-gray-300">Se souvenir de moi</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm font-medium text-orange-400 hover:text-orange-300 transition">
                    Mot de passe oublié ?
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <button
            type="submit"
            class="btn-primary w-full py-3.5 rounded-xl text-white font-semibold text-sm"
        >
            Se connecter
        </button>

        <!-- Register Link -->
        <div class="text-center pt-4 border-t border-gray-700/50">
            <p class="text-sm text-gray-400">
                Pas encore de compte ?
                <a href="{{ route('register') }}" class="font-semibold text-yellow-400 hover:text-yellow-300 transition">
                    Créer un compte
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
