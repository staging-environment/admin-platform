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
                    @if(auth()->user()?->can('ver_dashboard'))
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            Inicio
                        </x-nav-link>
                    @endif

                    @if(auth()->user()?->can('gestion_recursos_humanos'))
                        <div class="inline-flex items-center">
                            <x-dropdown align="left" width="w-64">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-1 py-2 border border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out {{ (request()->is('admin/recursos-humanos*') || request()->is('admin/job-offers*') || request()->is('admin/job-applications*')) ? 'text-gray-900 font-bold border-indigo-400' : '' }}">
                                        <span>{{ __('Recursos humanos') }}</span>
                                        <svg class="ms-1.5 h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </x-slot>
 
                                <x-slot name="content">
                                    <x-dropdown-link href="/admin/recursos-humanos" :active="request()->is('admin/recursos-humanos*')">
                                        {{ __('Empleados') }}
                                    </x-dropdown-link>
                                    
                                    <div x-data="{ openSub: {{ (request()->is('admin/job-offers*') || request()->is('admin/job-applications*')) ? 'true' : 'false' }} }">
                                        <button @click.stop="openSub = !openSub" class="w-full flex items-center justify-between px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 focus:outline-none transition duration-150 ease-in-out text-start text-left">
                                            <span>{{ __('Ofertas de Empleo') }}</span>
                                            <svg class="h-4 w-4 transform transition-transform duration-200" :class="{ 'rotate-90': openSub }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </button>
                                        <div x-show="openSub" style="display: none; border-left: 4px solid #6366f1 !important; background-color: #f9fafb !important; margin: 4px 0; padding: 4px 0;">
                                            <a href="/admin/job-offers" class="flex items-center w-full pr-4 py-2 text-xs font-semibold text-gray-600 hover:text-indigo-600 hover:bg-gray-100 transition duration-150 ease-in-out {{ request()->is('admin/job-offers*') ? 'text-indigo-600 font-extrabold bg-indigo-50/50' : '' }}" style="padding-left: 2rem !important;">
                                                <span style="color: #a5b4fc; font-family: monospace; margin-right: 6px; font-weight: bold;">└─</span>
                                                {{ __('Ver Ofertas') }}
                                            </a>
                                            <a href="/admin/job-applications" class="flex items-center w-full pr-4 py-2 text-xs font-semibold text-gray-600 hover:text-indigo-600 hover:bg-gray-100 transition duration-150 ease-in-out {{ request()->is('admin/job-applications*') ? 'text-indigo-600 font-extrabold bg-indigo-50/50' : '' }}" style="padding-left: 2rem !important;">
                                                <span style="color: #a5b4fc; font-family: monospace; margin-right: 6px; font-weight: bold;">└─</span>
                                                {{ __('Inscritos a Ofertas') }}
                                            </a>
                                        </div>
                                    </div>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @endif

                    @if(auth()->user()?->can('utilizar_explorador'))
                        <x-nav-link href="/admin/file-explorer" :active="request()->is('admin/file-explorer*')">
                            {{ __('Explorador de Archivos') }}
                        </x-nav-link>
                    @endif

                    @if(auth()->user()?->canAny(['ver_informes', 'gestion_gasolineras', 'gestion_usuarios', 'gestion_roles', 'gestion_portada']))
                        <div class="inline-flex items-center">
                            <x-dropdown align="left" width="60">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-1 py-2 border border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out {{ (request()->is('admin*') && !request()->is('admin/file-explorer*') && !request()->is('admin/job-offers*') && !request()->is('admin/job-applications*')) ? 'text-gray-900 font-bold border-indigo-400' : '' }}">
                                        <span>{{ __('Administración') }}</span>
                                        <svg class="ms-1.5 h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    @if(auth()->user()?->can('ver_informes'))
                                    <x-dropdown-link href="/admin/informes">
                                        {{ __('Informes') }}
                                    </x-dropdown-link>
                                    @endif
                                    
                                    @if(auth()->user()?->can('gestion_gasolineras'))
                                    <x-dropdown-link href="/admin/gasolineras">
                                        {{ __('Gasolineras') }}
                                    </x-dropdown-link>
                                    @endif

                                    @if(auth()->user()?->can('gestion_usuarios'))
                                    <x-dropdown-link href="/admin/users">
                                        {{ __('Usuarios') }}
                                    </x-dropdown-link>
                                    @endif

                                    @if(auth()->user()?->can('gestion_portada'))
                                    <x-dropdown-link href="/admin/manage-home">
                                        {{ __('Configuración de Portada') }}
                                    </x-dropdown-link>
                                    @endif

                                    @if(auth()->user()?->can('gestion_roles'))
                                    <x-dropdown-link href="/admin/permission-matrix">
                                        {{ __('Matriz de Permisos') }}
                                    </x-dropdown-link>
                                    @endif

                                    @if(auth()->user()?->can('gestion_roles'))
                                    <x-dropdown-link href="/admin/roles">
                                        {{ __('Roles') }}
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
                            <div>{{ Auth::user()?->name }}</div>

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
            @if(auth()->user()?->can('ver_dashboard'))
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    Inicio
                </x-responsive-nav-link>
            @endif

            @if(auth()->user()?->can('gestion_recursos_humanos'))
                <div class="pt-4 pb-2 border-t border-gray-200">
                    <div class="px-4 font-semibold text-xs uppercase tracking-wider text-gray-400">
                        {{ __('Recursos humanos') }}
                    </div>
                    <div class="mt-2 space-y-1">
                        <x-responsive-nav-link href="/admin/recursos-humanos" :active="request()->is('admin/recursos-humanos*')">
                            {{ __('Empleados') }}
                        </x-responsive-nav-link>
                        <div x-data="{ openSub: {{ (request()->is('admin/job-offers*') || request()->is('admin/job-applications*')) ? 'true' : 'false' }} }">
                            <button @click="openSub = !openSub" class="w-full flex items-center justify-between ps-3 pe-4 py-2 border-l-4 border-transparent text-left text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 focus:outline-none transition duration-150 ease-in-out">
                                <span>{{ __('Ofertas de Empleo') }}</span>
                                <svg class="h-4 w-4 transform transition-transform duration-200" :class="{ 'rotate-90': openSub }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                            <div x-show="openSub" class="ps-4 border-l-2 border-indigo-400/50 space-y-1 bg-gray-50/50" style="display: none;">
                                <x-responsive-nav-link href="/admin/job-offers" :active="request()->is('admin/job-offers*')">
                                    {{ __('Ver Ofertas') }}
                                </x-responsive-nav-link>
                                <x-responsive-nav-link href="/admin/job-applications" :active="request()->is('admin/job-applications*')">
                                    {{ __('Inscritos a Ofertas') }}
                                </x-responsive-nav-link>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if(auth()->user()?->can('utilizar_explorador'))
                <x-responsive-nav-link href="/admin/file-explorer" :active="request()->is('admin/file-explorer*')">
                    {{ __('Explorador de Archivos') }}
                </x-responsive-nav-link>
            @endif

            @if(auth()->user()?->canAny(['ver_informes', 'gestion_gasolineras', 'gestion_usuarios', 'gestion_roles', 'gestion_portada']))
                <div class="pt-4 pb-2 border-t border-gray-200">
                    <div class="px-4 font-semibold text-xs uppercase tracking-wider text-gray-400">
                        {{ __('Administración') }}
                    </div>
                    <div class="mt-2 space-y-1">
                        @if(auth()->user()?->can('ver_informes'))
                            <x-responsive-nav-link href="/admin/informes" :active="request()->is('admin/informes*')">
                                {{ __('Informes') }}
                            </x-responsive-nav-link>
                        @endif

                        @if(auth()->user()?->can('gestion_gasolineras'))
                            <x-responsive-nav-link href="/admin/gasolineras" :active="request()->is('admin/gasolineras*')">
                                {{ __('Gasolineras') }}
                            </x-responsive-nav-link>
                        @endif

                        @if(auth()->user()?->can('gestion_usuarios'))
                            <x-responsive-nav-link href="/admin/users" :active="request()->is('admin/users*')">
                                {{ __('Usuarios') }}
                            </x-responsive-nav-link>
                        @endif

                        @if(auth()->user()?->can('gestion_portada'))
                            <x-responsive-nav-link href="/admin/manage-home" :active="request()->is('admin/manage-home*')">
                                {{ __('Configuración de Portada') }}
                            </x-responsive-nav-link>
                        @endif

                        @if(auth()->user()?->can('gestion_roles'))
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
                <div class="font-medium text-base text-gray-800">{{ Auth::user()?->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()?->email }}</div>
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
