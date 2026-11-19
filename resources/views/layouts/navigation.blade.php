<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <img src="{{ asset('images/mini-logo.png') }}" alt="Gilvan Vieira Corretora" class="h-10 w-auto">
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Dashboard
                    </x-nav-link>
                    <x-nav-link :href="route('clients.index')" :active="request()->routeIs('clients.*')">
                        Meus clientes
                    </x-nav-link>
                    <x-nav-link :href="route('imoveis.index')" :active="request()->routeIs('imoveis.*')">
                        Imóveis
                    </x-nav-link>
                    <x-nav-link :href="route('contracts.index')" :active="request()->routeIs('contracts.*')">
                        Geração de Contratos
                    </x-nav-link>
                    @if (Auth::user()->isAdmin())
                        <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.*')">
                            Usuários
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Notifications + Perfil -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-2">

                <!-- Notificações -->
                <x-dropdown align="right" width="w-96">
                    <x-slot name="trigger">
                        <button class="relative inline-flex items-center p-2 rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none transition ease-in-out duration-150">
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>

                            @if ($notificacoes->count() > 0)
                                <span class="absolute top-1 right-1 inline-flex items-center justify-center h-4 min-w-4 px-1 text-[10px] font-bold leading-none text-white bg-red-500 rounded-full">
                                    {{ $notificacoes->count() > 9 ? '9+' : $notificacoes->count() }}
                                </span>
                            @endif
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-2 border-b border-gray-100 flex items-center justify-between">
                            <span class="text-sm font-semibold text-gray-700">Notificações</span>
                            <span class="text-xs text-gray-400">
                                {{ $notificacoes->count() }} {{ $notificacoes->count() === 1 ? 'vencimento' : 'vencimentos' }}
                            </span>
                        </div>

                        <div class="max-h-80 overflow-y-auto divide-y divide-gray-100">
                            @forelse ($notificacoes as $n)
                                <a href="{{ $n['url'] }}" class="block px-4 py-3 hover:bg-gray-50">
                                    <p class="text-sm font-medium text-gray-800">{{ $n['titulo'] }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $n['imovel'] }} • {{ $n['cliente'] }}</p>
                                    <p class="text-xs mt-1 {{ $n['cor'] }}">Término: {{ $n['data'] }}</p>
                                </a>
                            @empty
                                <div class="px-4 py-8 text-center text-sm text-gray-400">
                                    Nenhum vencimento próximo.
                                </div>
                            @endforelse
                        </div>

                        <a href="{{ route('contracts.index') }}" class="block px-4 py-2 text-center text-sm text-blue-600 hover:bg-gray-50 border-t border-gray-100">
                            Ver todos os contratos
                        </a>
                    </x-slot>
                </x-dropdown>

                <!-- Perfil (link direto) -->
                <a href="{{ route('profile.edit') }}"
                   class="inline-flex items-center gap-2 px-3 py-2 text-sm leading-4 font-medium rounded-md transition ease-in-out duration-150 focus:outline-none
                          {{ request()->routeIs('profile.*') ? 'text-gray-900 bg-gray-100' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                    <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-gray-200 text-gray-500 overflow-hidden shrink-0">
                        @if (Auth::user()->avatar_url)
                            <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="h-full w-full object-cover">
                        @else
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 12a5 5 0 100-10 5 5 0 000 10zm0 2c-4.418 0-8 2.239-8 5v1a1 1 0 001 1h14a1 1 0 001-1v-1c0-2.761-3.582-5-8-5z" />
                            </svg>
                        @endif
                    </span>

                    <span>{{ Auth::user()->name }}</span>
                </a>
            </div>

            <!-- Hamburger + sino (mobile) -->
            <div class="-me-2 flex items-center gap-1 sm:hidden">
                <a href="{{ route('contracts.index') }}" class="relative p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    @if ($notificacoes->count() > 0)
                        <span class="absolute top-1.5 right-1.5 h-2 w-2 bg-red-500 rounded-full"></span>
                    @endif
                </a>

                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                Dashboard
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('clients.index')" :active="request()->routeIs('clients.*')">
                Meus clientes
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('imoveis.index')" :active="request()->routeIs('imoveis.*')">
                Imóveis
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('contracts.index')" :active="request()->routeIs('contracts.*')">
                Geração de Contratos
            </x-responsive-nav-link>
            @if (Auth::user()->isAdmin())
                <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.*')">
                    Usuários
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Perfil (link direto) -->
        <div class="pt-4 pb-3 border-t border-gray-200">
            <a href="{{ route('profile.edit') }}"
               class="px-4 py-2 flex items-center gap-3 hover:bg-gray-50 transition duration-150 ease-in-out {{ request()->routeIs('profile.*') ? 'bg-gray-50' : '' }}">
                <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-gray-200 text-gray-500 overflow-hidden shrink-0">
                    @if (Auth::user()->avatar_url)
                        <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="h-full w-full object-cover">
                    @else
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 12a5 5 0 100-10 5 5 0 000 10zm0 2c-4.418 0-8 2.239-8 5v1a1 1 0 001 1h14a1 1 0 001-1v-1c0-2.761-3.582-5-8-5z" />
                        </svg>
                    @endif
                </span>

                <div>
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </a>
        </div>
    </div>
</nav>
