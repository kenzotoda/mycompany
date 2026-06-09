<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('layouts.partials.head')
    </head>
    <body class="min-h-screen bg-brand-surface font-sans text-brand-black antialiased">
        <div class="flex min-h-screen flex-col">
            <header class="border-b border-brand-border bg-brand-black">
                <div class="mx-auto flex h-14 w-full max-w-6xl items-center justify-between px-4 sm:px-6">
                    <a href="/" class="flex items-center gap-2.5 font-bold text-white">
                        <i class="fa-solid fa-chart-pie text-brand-orange"></i>
                        MEIControl
                    </a>
                    <a href="/" class="text-sm text-zinc-400 hover:text-white">
                        <i class="fa-solid fa-arrow-left mr-1"></i> Voltar
                    </a>
                </div>
            </header>

            <div class="flex flex-1 items-center justify-center px-4 py-12">
                <div class="w-full max-w-md">
                    <div class="rounded-2xl border border-brand-border bg-white p-8 shadow-card">
                        {{ $slot }}
                    </div>
                    <p class="mt-6 text-center text-sm text-brand-muted">
                        Gestão inteligente para MEI e microempresas
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>
