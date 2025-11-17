<x-guest-layout>
    <!-- Title -->
    <div class="text-center mb-8">
        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl flex items-center justify-center" style="background: linear-gradient(135deg, rgba(223, 85, 38, 0.2) 0%, rgba(251, 187, 42, 0.2) 100%); border: 1px solid rgba(223, 85, 38, 0.3);">
            <svg class="w-8 h-8 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
            </svg>
        </div>
        <h2 class="text-3xl font-bold text-white mb-2">Mot de passe oublié ?</h2>
        <p class="text-gray-400 text-sm max-w-sm mx-auto">
            Pas de problème. Indiquez votre email et nous vous enverrons un lien pour réinitialiser votre mot de passe.
        </p>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-6 p-4 rounded-xl bg-green-500/10 border border-green-500/20">
            <div class="flex items-center space-x-3">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm text-green-400">{{ session('status') }}</p>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
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
                placeholder="votre@email.com"
                class="input-field block w-full px-4 py-3.5 rounded-xl text-sm"
            />
            @error('email')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <button
            type="submit"
            class="btn-primary w-full py-3.5 rounded-xl text-white font-semibold text-sm"
        >
            Envoyer le lien de réinitialisation
        </button>

        <!-- Back to Login -->
        <div class="text-center pt-4 border-t border-gray-700/50">
            <a href="{{ route('login') }}" class="inline-flex items-center space-x-2 text-sm font-medium text-gray-400 hover:text-yellow-400 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Retour à la connexion</span>
            </a>
        </div>
    </form>
</x-guest-layout>
