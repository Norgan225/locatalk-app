<x-guest-layout>
    <!-- Title -->
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-white mb-2">Créer un compte</h2>
        <p class="text-gray-400">Rejoignez LocaTalk en quelques secondes</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-300 mb-2">
                Nom complet
            </label>
            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name') }}"
                required
                autofocus
                autocomplete="name"
                placeholder="Jean Dupont"
                class="input-field block w-full px-4 py-3.5 rounded-xl text-sm"
            />
            @error('name')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

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
                autocomplete="username"
                placeholder="jean@entreprise.com"
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
                autocomplete="new-password"
                placeholder="••••••••"
                class="input-field block w-full px-4 py-3.5 rounded-xl text-sm"
            />
            @error('password')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-2">
                Confirmer le mot de passe
            </label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                placeholder="••••••••"
                class="input-field block w-full px-4 py-3.5 rounded-xl text-sm"
            />
            @error('password_confirmation')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <button
            type="submit"
            class="btn-primary w-full py-3.5 rounded-xl text-white font-semibold text-sm mt-6"
        >
            Créer mon compte
        </button>

        <!-- Login Link -->
        <div class="text-center pt-4 border-t border-gray-700/50">
            <p class="text-sm text-gray-400">
                Déjà un compte ?
                <a href="{{ route('login') }}" class="font-semibold text-yellow-400 hover:text-yellow-300 transition">
                    Se connecter
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
