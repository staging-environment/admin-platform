<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $estacion->Nombre }} | Utrecar</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-white min-h-screen">
<header class="max-w-7xl mx-auto px-6 py-8 flex justify-between items-center">
    <a href="/" class="text-blue-400 hover:bg-blue-500/10 px-4 py-2 rounded-xl transition flex items-center gap-2 font-bold">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Volver a la red
    </a>
    @auth
        <a href="{{ url('/dashboard') }}" class="text-xs font-bold border border-blue-500/50 bg-blue-500/10 px-5 py-2.5 rounded-full hover:bg-blue-500 transition-all">PANEL</a>
    @else
        <a href="{{ route('login') }}" class="text-xs font-bold text-slate-500 hover:text-white transition-all uppercase tracking-widest">Login</a>
    @endauth
</header>

<main class="max-w-7xl mx-auto px-6 py-12 grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-slate-900 border border-white/10 p-10 rounded-[3rem]">
            <h2 class="text-5xl font-black mb-4">{{ $estacion->Nombre }}</h2>
            <p class="text-blue-400 font-bold mb-6 italic">{{ $estacion->Direccion }}</p>
            <p class="text-slate-400 text-lg leading-relaxed">{{ $extras['descripcion'] }}</p>
        </div>
        <!-- Otros detalles... -->
    </div>

    <div class="bg-slate-900 border border-white/10 p-8 rounded-[3rem] h-fit sticky top-8">
        <h4 class="text-xs font-black text-slate-500 uppercase tracking-widest mb-6">Precios Hoy</h4>
        <div class="space-y-6">
            <div class="flex justify-between items-center">
                <span class="text-slate-400 font-bold uppercase text-[10px]">Diesel A</span>
                <span class="text-3xl font-black text-white">{{ number_format($estacion->diesel ?? 0, 3) }}€</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-slate-400 font-bold uppercase text-[10px]">Sin Plomo 95</span>
                <span class="text-3xl font-black text-cyan-400">{{ number_format($estacion->gasolina95 ?? 0, 3) }}€</span>
            </div>
        </div>
    </div>
</main>
</body>
</html>
