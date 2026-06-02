<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center">
                        <x-logo class="block h-9 w-auto" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex sm:items-center">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @if(auth()->user()->hasRole('Admin') || auth()->user()->can('utilizar_explorador'))
                        <x-nav-link href="/admin/file-explorer" :active="request()->is('admin/file-explorer*')">
                            {{ __('Explorador de Archivos') }}
                        </x-nav-link>
                    @endif

                    @if(auth()->user()->hasRole('Admin'))
                        <x-nav-link href="/admin/virtusgesnet" :active="request()->is('admin/virtusgesnet*')">
                            {{ __('Virtusgesnet') }}
                        </x-nav-link>
                    @endif

                    @if(auth()->user()->hasRole('Admin'))
                        <x-nav-link href="/admin/sii" :active="request()->is('admin/sii*')">
                            {{ __('SII') }}
                        </x-nav-link>
                    @endif

                    @if(auth()->user()->hasRole('Admin') || auth()->user()->canAny(['ver_informes', 'gestion_gasolineras', 'gestion_usuarios_roles', 'gestion_portada', 'gestion_ofertas']))
                        <div class="inline-flex items-center">
                            <x-dropdown align="left" width="60">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-1 py-2 border border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out {{ (request()->is('admin*') && !request()->is('admin/file-explorer*')) ? 'text-gray-900 font-bold border-indigo-400' : '' }}">
                                        <span>{{ __('Administración') }}</span>
                                        <svg class="ms-1.5 h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    @if(auth()->user()->hasRole('Admin') || auth()->user()->can('ver_informes'))
                                    <x-dropdown-link href="/admin/informes">
                                        {{ __('Informes') }}
                                    </x-dropdown-link>
                                    @endif
                                    
                                    @if(auth()->user()->hasRole('Admin') || auth()->user()->can('gestion_gasolineras'))
                                    <x-dropdown-link href="/admin/gasolineras">
                                        {{ __('Gasolineras') }}
                                    </x-dropdown-link>
                                    @endif

                                    @if(auth()->user()->hasRole('Admin') || auth()->user()->can('gestion_usuarios_roles'))
                                    <x-dropdown-link href="/admin/users">
                                        {{ __('Usuarios') }}
                                    </x-dropdown-link>
                                    @endif

                                    @if(auth()->user()->hasRole('Admin') || auth()->user()->can('gestion_portada'))
                                    <x-dropdown-link href="/admin/manage-home">
                                        {{ __('Configuración de Portada') }}
                                    </x-dropdown-link>
                                    @endif

                                     @if(auth()->user()->hasRole('Admin') || auth()->user()->can('gestion_ofertas'))
                                     <div x-data="{ openOfertas: {{ (request()->is('admin/job-offers*') || request()->is('admin/job-applications*')) ? 'true' : 'false' }} }" class="border-t border-b border-gray-100 py-1 bg-gray-50/50">
                                         <button @click.stop="openOfertas = !openOfertas" class="flex justify-between items-center w-full px-4 py-2 text-xs font-bold uppercase tracking-wider text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition duration-150 ease-in-out">
                                             <span>Gestión de Empleo</span>
                                             <svg class="h-3 w-3 transform transition-transform" :class="openOfertas ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" />
                                             </svg>
                                         </button>
                                         <div x-show="openOfertas" x-cloak class="pl-4 space-y-1 mt-1 pb-1">
                                             <x-dropdown-link href="/admin/job-offers" :active="request()->is('admin/job-offers*')" class="text-xs">
                                                 — {{ __('Ofertas de Empleo') }}
                                             </x-dropdown-link>
                                             <x-dropdown-link href="/admin/job-applications" :active="request()->is('admin/job-applications*')" class="text-xs">
                                                 — {{ __('Inscritos a Ofertas') }}
                                             </x-dropdown-link>
                                         </div>
                                     </div>
                                     @endif

                                    @if(auth()->user()->hasRole('Admin') || auth()->user()->can('gestion_usuarios_roles'))
                                    <x-dropdown-link href="/admin/permission-matrix">
                                        {{ __('Matriz de Permisos') }}
                                    </x-dropdown-link>
                                    @endif
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                <a href="/" target="_blank" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-lg shadow transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                    Ver Web
                </a>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
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
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @if(auth()->user()->hasRole('Admin') || auth()->user()->can('utilizar_explorador'))
                <x-responsive-nav-link href="/admin/file-explorer" :active="request()->is('admin/file-explorer*')">
                    {{ __('Explorador de Archivos') }}
                </x-responsive-nav-link>
            @endif

            @if(auth()->user()->hasRole('Admin'))
                <x-responsive-nav-link href="/admin/virtusgesnet" :active="request()->is('admin/virtusgesnet*')">
                    {{ __('Virtusgesnet') }}
                </x-responsive-nav-link>
            @endif

            @if(auth()->user()->hasRole('Admin'))
                <x-responsive-nav-link href="/admin/sii" :active="request()->is('admin/sii*')">
                    {{ __('SII') }}
                </x-responsive-nav-link>
            @endif

            @if(auth()->user()->hasRole('Admin') || auth()->user()->canAny(['ver_informes', 'gestion_gasolineras', 'gestion_usuarios_roles', 'gestion_portada', 'gestion_ofertas']))
                <div class="pt-4 pb-2 border-t border-gray-200">
                    <div class="px-4 font-semibold text-xs uppercase tracking-wider text-gray-400">
                        {{ __('Administración') }}
                    </div>
                    <div class="mt-2 space-y-1">
                        @if(auth()->user()->hasRole('Admin') || auth()->user()->can('ver_informes'))
                            <x-responsive-nav-link href="/admin/informes" :active="request()->is('admin/informes*')">
                                {{ __('Informes') }}
                            </x-responsive-nav-link>
                        @endif

                        @if(auth()->user()->hasRole('Admin') || auth()->user()->can('gestion_gasolineras'))
                            <x-responsive-nav-link href="/admin/gasolineras" :active="request()->is('admin/gasolineras*')">
                                {{ __('Gasolineras') }}
                            </x-responsive-nav-link>
                        @endif

                        @if(auth()->user()->hasRole('Admin') || auth()->user()->can('gestion_usuarios_roles'))
                            <x-responsive-nav-link href="/admin/users" :active="request()->is('admin/users*')">
                                {{ __('Usuarios') }}
                            </x-responsive-nav-link>
                        @endif

                        @if(auth()->user()->hasRole('Admin') || auth()->user()->can('gestion_portada'))
                            <x-responsive-nav-link href="/admin/manage-home" :active="request()->is('admin/manage-home*')">
                                {{ __('Configuración de Portada') }}
                            </x-responsive-nav-link>
                        @endif

                        @if(auth()->user()->hasRole('Admin') || auth()->user()->can('gestion_ofertas'))
                            <div class="pl-4 border-l-2 border-indigo-400/30 my-2">
                                <div class="px-4 py-1 text-[10px] font-black uppercase tracking-wider text-gray-400">
                                    Gestión de Empleo
                                </div>
                                <x-responsive-nav-link href="/admin/job-offers" :active="request()->is('admin/job-offers*')" class="text-xs">
                                    {{ __('Ofertas de Empleo') }}
                                </x-responsive-nav-link>
                                <x-responsive-nav-link href="/admin/job-applications" :active="request()->is('admin/job-applications*')" class="text-xs">
                                    {{ __('Inscritos a Ofertas') }}
                                </x-responsive-nav-link>
                            </div>
                        @endif

                        @if(auth()->user()->hasRole('Admin') || auth()->user()->can('gestion_usuarios_roles'))
                            <x-responsive-nav-link href="/admin/permission-matrix" :active="request()->is('admin/permission-matrix*')">
                                {{ __('Matriz de Permisos') }}
                            </x-responsive-nav-link>
                            <x-responsive-nav-link href="/admin/roles" :active="request()->is('admin/roles*')">
                                {{ __('Roles') }}
                            </x-responsive-nav-link>
                        @endif
                    </div>
                </div>
            @endif

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
