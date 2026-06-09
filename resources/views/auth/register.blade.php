<x-guest-layout>
    <div class="mb-8 text-center">
        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-brand-orange-light">
            <i class="fa-solid fa-user-plus text-xl text-brand-orange"></i>
        </div>
        <h2 class="text-2xl font-bold text-brand-black">Criar conta</h2>
        <p class="mt-1 text-sm text-brand-muted">Comece a gerenciar sua empresa agora</p>
    </div>

    <form method="POST" action="{{ url('/register') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Nome')" />
            <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('E-mail')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Senha')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirmar senha')" />
            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        <x-primary-button class="w-full">
            <i class="fa-solid fa-user-plus"></i> {{ __('Cadastrar') }}
        </x-primary-button>

        <p class="text-center text-sm text-brand-muted">
            Já possui conta?
            <a href="{{ url('/login') }}" class="mc-link">Entrar</a>
        </p>
    </form>
</x-guest-layout>
