<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('layouts.partials.head')
    </head>
    <body class="min-h-screen bg-white font-sans text-brand-black antialiased">
        <header class="border-b border-brand-border bg-brand-black">
            <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 sm:px-6">
                <a href="/" class="flex items-center gap-2.5 text-lg font-bold text-white">
                    <i class="fa-solid fa-chart-pie text-brand-orange"></i>
                    MEIControl
                </a>
                <nav class="flex items-center gap-2">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="mc-btn-primary">
                            <i class="fa-solid fa-gauge-high"></i> Dashboard
                        </a>
                    @else
                        <a href="{{ url('/login') }}" class="mc-btn-ghost !text-zinc-300 hover:!bg-zinc-800 hover:!text-white">
                            Entrar
                        </a>
                        <a href="{{ url('/register') }}" class="mc-btn-primary">
                            Criar conta
                        </a>
                    @endauth
                </nav>
            </div>
        </header>

        <main>
            <section class="border-b border-brand-border bg-brand-surface">
                <div class="mx-auto max-w-6xl px-4 py-20 text-center sm:px-6 sm:py-28">
                    <span class="mc-badge bg-brand-orange-light text-brand-orange-dark">
                        <i class="fa-solid fa-bolt mr-1"></i> Gestão para MEI e microempresas
                    </span>
                    <h1 class="mt-6 text-4xl font-bold tracking-tight text-brand-black sm:text-6xl">
                        Controle seu negócio<br>
                        <span class="text-brand-orange">com clareza</span>
                    </h1>
                    <p class="mx-auto mt-6 max-w-2xl text-lg text-brand-muted">
                        Compras, vendas, estoque e cadastros em uma plataforma moderna, rápida e feita para quem empreende.
                    </p>
                    <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="mc-btn-primary px-8 py-3 text-base">
                                <i class="fa-solid fa-arrow-right"></i> Abrir painel
                            </a>
                        @else
                            <a href="{{ url('/register') }}" class="mc-btn-primary px-8 py-3 text-base">
                                Começar grátis
                            </a>
                            <a href="{{ url('/login') }}" class="mc-btn-secondary px-8 py-3 text-base">
                                Já tenho conta
                            </a>
                        @endauth
                    </div>
                </div>
            </section>

            <section class="mx-auto max-w-6xl px-4 py-20 sm:px-6">
                <div class="grid gap-6 sm:grid-cols-3">
                    <div class="mc-stat-card">
                        <i class="fa-solid fa-cart-shopping text-2xl text-brand-orange"></i>
                        <h3 class="mt-4 text-lg font-semibold">Compras e vendas</h3>
                        <p class="mt-2 text-sm text-brand-muted">Registre operações com itens, clientes, fornecedores e anexos.</p>
                    </div>
                    <div class="mc-stat-card">
                        <i class="fa-solid fa-warehouse text-2xl text-brand-orange"></i>
                        <h3 class="mt-4 text-lg font-semibold">Estoque automático</h3>
                        <p class="mt-2 text-sm text-brand-muted">Entradas e saídas sincronizadas em tempo real.</p>
                    </div>
                    <div class="mc-stat-card">
                        <i class="fa-solid fa-chart-line text-2xl text-brand-orange"></i>
                        <h3 class="mt-4 text-lg font-semibold">Indicadores</h3>
                        <p class="mt-2 text-sm text-brand-muted">Acompanhe compras, vendas e alertas de estoque baixo.</p>
                    </div>
                </div>
            </section>

            <section class="border-t border-brand-border bg-brand-black py-16">
                <div class="mx-auto max-w-6xl px-4 text-center sm:px-6">
                    <h2 class="text-2xl font-bold text-white">Pronto para organizar sua empresa?</h2>
                    <p class="mt-3 text-zinc-400">Crie sua conta e comece em minutos.</p>
                    @guest
                        <a href="{{ url('/register') }}" class="mc-btn-primary mt-8 inline-flex px-8 py-3 text-base">
                            <i class="fa-solid fa-user-plus"></i> Criar conta grátis
                        </a>
                    @endguest
                </div>
            </section>
        </main>
    </body>
</html>
