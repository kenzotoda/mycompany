<x-guest-layout>
    <div class="mb-8 text-center">
        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-brand-orange-light">
            <i class="fa-solid fa-right-to-bracket text-xl text-brand-orange"></i>
        </div>
        <h2 class="text-2xl font-bold text-brand-black">Entrar</h2>
        <p class="mt-1 text-sm text-brand-muted">Acesse sua conta MEIControl</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ url('/login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('E-mail')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Senha')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <label for="remember_me" class="inline-flex items-center gap-2">
            <input id="remember_me" type="checkbox" class="rounded border-brand-border text-brand-orange focus:ring-brand-orange" name="remember">
            <span class="text-sm text-brand-muted">{{ __('Lembrar de mim') }}</span>
        </label>

        <x-primary-button class="w-full">
            <i class="fa-solid fa-right-to-bracket"></i> {{ __('Entrar') }}
        </x-primary-button>

        <div class="flex items-center justify-between text-sm">
            @if (Route::has('password.request'))
                <a class="text-brand-muted hover:text-brand-orange" href="{{ url('/forgot-password') }}">
                    {{ __('Esqueceu a senha?') }}
                </a>
            @endif
            <a class="mc-link" href="{{ url('/register') }}">Criar conta</a>
        </div>
    </form>
</x-guest-layout>
