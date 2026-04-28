<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Utrecar | Plataforma Administrativa</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-950 text-white antialiased">
<div class="relative min-h-screen overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-slate-900 to-blue-950"></div>
    <div class="absolute -top-40 -right-40 h-96 w-96 rounded-full bg-blue-500/20 blur-3xl"></div>
    <div class="absolute top-1/3 -left-40 h-96 w-96 rounded-full bg-cyan-400/10 blur-3xl"></div>
    <div class="absolute bottom-0 right-1/4 h-72 w-72 rounded-full bg-indigo-500/10 blur-3xl"></div>

    <header class="relative z-10">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-6 lg:px-8">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white text-slate-950 shadow-lg shadow-blue-900/30">
                    <span class="text-xl font-black">U</span>
                </div>
                <div>
                    <p class="text-lg font-bold tracking-wide">Utrecar</p>
                    <p class="text-xs uppercase tracking-[0.28em] text-blue-200">Admin Platform</p>
                </div>
            </a>

            <nav class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}"
                       class="rounded-full bg-white px-5 py-2.5 text-sm font-semibold text-slate-950 shadow-lg shadow-blue-950/30 transition hover:bg-blue-50">
                        Ir al panel
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="rounded-full border border-white/20 px-5 py-2.5 text-sm font-semibold text-white transition hover:border-white/40 hover:bg-white/10">
                        Acceder
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="relative z-10">
        <section class="mx-auto grid min-h-[calc(100vh-96px)] max-w-7xl items-center gap-12 px-6 py-12 lg:grid-cols-2 lg:px-8">
            <div>
                <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-blue-300/20 bg-blue-400/10 px-4 py-2 text-sm text-blue-100">
                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                    Plataforma interna segura
                </div>

                <h1 class="max-w-3xl text-4xl font-black tracking-tight text-white sm:text-5xl lg:text-6xl">
                    Gestión administrativa moderna para
                    <span class="bg-gradient-to-r from-cyan-300 to-blue-400 bg-clip-text text-transparent">
                            Utrecar
                        </span>
                </h1>

                <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-300">
                    Accede de forma centralizada a las herramientas internas, consulta información corporativa
                    y gestiona usuarios, roles y datos operativos desde un entorno privado y controlado.
                </p>

                <div class="mt-10 flex flex-col gap-4 sm:flex-row">
                    @auth
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center justify-center rounded-full bg-blue-500 px-7 py-3 text-sm font-bold text-white shadow-xl shadow-blue-900/30 transition hover:bg-blue-400">
                            Entrar al dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center justify-center rounded-full bg-blue-500 px-7 py-3 text-sm font-bold text-white shadow-xl shadow-blue-900/30 transition hover:bg-blue-400">
                            Iniciar sesión
                        </a>
                    @endauth

                    <a href="#caracteristicas"
                       class="inline-flex items-center justify-center rounded-full border border-white/20 px-7 py-3 text-sm font-bold text-white transition hover:bg-white/10">
                        Ver características
                    </a>
                </div>

                <div class="mt-10 grid max-w-xl grid-cols-3 gap-4">
                    <div>
                        <p class="text-2xl font-black text-white">100%</p>
                        <p class="mt-1 text-sm text-slate-400">Acceso privado</p>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-white">2</p>
                        <p class="mt-1 text-sm text-slate-400">Bases conectadas</p>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-white">24/7</p>
                        <p class="mt-1 text-sm text-slate-400">Disponibilidad</p>
                    </div>
                </div>
            </div>

            <div class="relative">
                <div class="absolute -inset-4 rounded-[2rem] bg-gradient-to-r from-blue-500/20 to-cyan-400/20 blur-2xl"></div>

                <div class="relative overflow-hidden rounded-[2rem] border border-white/10 bg-white/10 p-4 shadow-2xl shadow-blue-950/50 backdrop-blur">
                    <div class="rounded-[1.5rem] bg-slate-950/80 p-5">
                        <div class="mb-5 flex items-center justify-between">
                            <div>
                                <p class="text-sm text-slate-400">Panel Utrecar</p>
                                <p class="text-xl font-bold text-white">Resumen operativo</p>
                            </div>
                            <div class="rounded-full bg-emerald-400/10 px-3 py-1 text-xs font-semibold text-emerald-300">
                                Online
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <div class="mb-4 h-10 w-10 rounded-xl bg-blue-500/20"></div>
                                <p class="text-sm text-slate-400">Usuarios activos</p>
                                <p class="mt-2 text-3xl font-black">Admin</p>
                            </div>

                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <div class="mb-4 h-10 w-10 rounded-xl bg-cyan-500/20"></div>
                                <p class="text-sm text-slate-400">Consultas</p>
                                <p class="mt-2 text-3xl font-black">Datos</p>
                            </div>

                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4 sm:col-span-2">
                                <div class="mb-4 flex items-center justify-between">
                                    <p class="font-semibold">Estado de sistemas</p>
                                    <span class="text-sm text-emerald-300">Correcto</span>
                                </div>

                                <div class="space-y-3">
                                    <div>
                                        <div class="mb-1 flex justify-between text-xs text-slate-400">
                                            <span>Autenticación</span>
                                            <span>100%</span>
                                        </div>
                                        <div class="h-2 rounded-full bg-white/10">
                                            <div class="h-2 w-full rounded-full bg-blue-400"></div>
                                        </div>
                                    </div>

                                    <div>
                                        <div class="mb-1 flex justify-between text-xs text-slate-400">
                                            <span>Datos corporativos</span>
                                            <span>92%</span>
                                        </div>
                                        <div class="h-2 rounded-full bg-white/10">
                                            <div class="h-2 w-[92%] rounded-full bg-cyan-400"></div>
                                        </div>
                                    </div>

                                    <div>
                                        <div class="mb-1 flex justify-between text-xs text-slate-400">
                                            <span>Permisos</span>
                                            <span>98%</span>
                                        </div>
                                        <div class="h-2 rounded-full bg-white/10">
                                            <div class="h-2 w-[98%] rounded-full bg-emerald-400"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 rounded-2xl border border-blue-400/20 bg-blue-400/10 p-4 text-sm text-blue-100">
                            Acceso restringido a personal autorizado de Utrecar.
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="caracteristicas" class="relative border-t border-white/10 bg-slate-950/60 px-6 py-16 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="max-w-2xl">
                    <p class="text-sm font-bold uppercase tracking-[0.3em] text-blue-300">Características</p>
                    <h2 class="mt-3 text-3xl font-black text-white">
                        Una entrada clara y profesional a la plataforma
                    </h2>
                </div>

                <div class="mt-10 grid gap-6 md:grid-cols-3">
                    <div class="rounded-3xl border border-white/10 bg-white/[0.06] p-6">
                        <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-500/20 text-blue-200">
                            01
                        </div>
                        <h3 class="text-lg font-bold text-white">Acceso seguro</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-400">
                            Entrada privada para usuarios autorizados, integrada con la autenticación interna.
                        </p>
                    </div>

                    <div class="rounded-3xl border border-white/10 bg-white/[0.06] p-6">
                        <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-2xl bg-cyan-500/20 text-cyan-200">
                            02
                        </div>
                        <h3 class="text-lg font-bold text-white">Datos centralizados</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-400">
                            Consulta de información corporativa y operativa desde una única plataforma.
                        </p>
                    </div>

                    <div class="rounded-3xl border border-white/10 bg-white/[0.06] p-6">
                        <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-500/20 text-emerald-200">
                            03
                        </div>
                        <h3 class="text-lg font-bold text-white">Gestión de usuarios</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-400">
                            Administración de accesos, roles y permisos para mantener el control de la aplicación.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="relative z-10 border-t border-white/10 px-6 py-6 lg:px-8">
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-4 text-sm text-slate-400 sm:flex-row">
            <p>© {{ date('Y') }} Utrecar. Todos los derechos reservados.</p>
            <p>Plataforma administrativa interna</p>
        </div>
    </footer>
</div>
</body>
</html>
