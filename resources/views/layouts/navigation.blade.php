<nav x-data="{ open: false }" class="border-b border-zinc-800 bg-brand-black">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-14 items-center justify-between">
            <div class="flex items-center gap-6">
                <a href="{{ route('dashboard') }}">
                    <x-application-logo />
                </a>

                <div class="hidden lg:flex lg:items-center lg:gap-1">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="fa-gauge-high">
                        Dashboard
                    </x-nav-link>
                    <x-nav-link :href="route('purchases.index')" :active="request()->routeIs('purchases.*')" icon="fa-cart-shopping">
                        Compras
                    </x-nav-link>
                    <x-nav-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')" icon="fa-truck-field">
                        Fornecedores
                    </x-nav-link>
                    <x-nav-link :href="route('products.create')" :active="request()->routeIs('products.create')" icon="fa-box">
                        Produtos
                    </x-nav-link>
                    <x-nav-link :href="route('customers.index')" :active="request()->routeIs('customers.*')" icon="fa-users">
                        Clientes
                    </x-nav-link>
                    <x-nav-link :href="route('sales.index')" :active="request()->routeIs('sales.*')" icon="fa-cash-register">
                        Vendas
                    </x-nav-link>
                    <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.index')" icon="fa-warehouse">
                        Estoque
                    </x-nav-link>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-zinc-300 transition hover:bg-zinc-800 hover:text-white">
                            <span class="flex h-7 w-7 items-center justify-center rounded-full bg-brand-orange text-xs font-bold text-white">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                            <span class="hidden md:inline">{{ Auth::user()->name }}</span>
                            <i class="fa-solid fa-chevron-down text-xs text-zinc-500"></i>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            <i class="fa-solid fa-user-gear text-brand-muted"></i>
                            Perfil
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                <i class="fa-solid fa-right-from-bracket text-brand-muted"></i>
                                Sair
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <button @click="open = ! open" class="inline-flex items-center justify-center rounded-lg p-2 text-zinc-400 hover:bg-zinc-800 hover:text-white lg:hidden">
                <i class="fa-solid" :class="open ? 'fa-xmark' : 'fa-bars'"></i>
            </button>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="border-t border-zinc-800 lg:hidden">
        <div class="space-y-1 px-2 py-3">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="fa-gauge-high">Dashboard</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('purchases.index')" :active="request()->routeIs('purchases.*')" icon="fa-cart-shopping">Compras</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')" icon="fa-truck-field">Fornecedores</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('products.create')" :active="request()->routeIs('products.create')" icon="fa-box">Produtos</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('customers.index')" :active="request()->routeIs('customers.*')" icon="fa-users">Clientes</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('sales.index')" :active="request()->routeIs('sales.*')" icon="fa-cash-register">Vendas</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.index')" icon="fa-warehouse">Estoque</x-responsive-nav-link>
        </div>

        <div class="border-t border-zinc-800 px-4 py-4">
            <p class="text-sm font-medium text-white">{{ Auth::user()->name }}</p>
            <p class="text-xs text-zinc-500">{{ Auth::user()->email }}</p>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" icon="fa-user-gear">Perfil</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" icon="fa-right-from-bracket"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        Sair
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
