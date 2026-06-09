<x-app-layout>
    <div class="mc-page space-y-6">
        <div class="mc-page-header">
            <div>
                <h1 class="mc-page-title"><i class="fa-solid fa-user-gear mc-icon"></i> Perfil</h1>
                <p class="mc-page-subtitle">Gerencie suas informações de conta</p>
            </div>
        </div>

        <div class="mc-card max-w-xl">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="mc-card max-w-xl">
            @include('profile.partials.update-password-form')
        </div>

        <div class="mc-card max-w-xl">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>
