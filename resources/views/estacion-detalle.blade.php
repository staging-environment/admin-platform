<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $estacion->Nombre }} | Utrecar</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-white min-h-screen">
<header class="max-w-7xl mx-auto px-6 py-8">
    <a href="/" class="text-blue-400 hover:underline flex items-center gap-2 font-bold">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Volver a la red principal
    </a>
</header>

<main class="max-w-7xl mx-auto px-6 py-12 grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-slate-900 border border-white/10 p-10 rounded-[3rem]">
            <h2 class="text-5xl font-black mb-4">{{ $estacion->Nombre }}</h2>
            <p class="text-blue-400 font-bold mb-6 italic">{{ $estacion->Direccion }}</p>
            <p class="text-slate-400 text-lg leading-relaxed">{{ $extras['descripcion'] }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white/5 p-8 rounded-[2rem] border border-white/10">
                <h4 class="font-bold mb-4">Información Local</h4>
                <ul class="space-y-2 text-sm text-slate-300">
                    <li><strong>Población:</strong> {{ $estacion->Poblacion }}</li>
                    <li><strong>Provincia:</strong> {{ $estacion->Provincia }}</li>
                    <li><strong>Estado:</strong> <span class="text-emerald-400 font-bold">Operativa</span></li>
                </ul>
            </div>
            <div class="bg-white/5 p-8 rounded-[2rem] border border-white/10">
                <h4 class="font-bold mb-4 font-bold text-yellow-400">Puntuación: {{ $extras['rating'] }} / 5</h4>
                <div class="flex flex-wrap gap-2">
                    @foreach($extras['servicios'] as $servicio)
                        <span class="bg-blue-500/10 text-blue-400 text-[10px] font-bold px-3 py-1 rounded-full border border-blue-500/20">{{ $servicio }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="bg-slate-900 border border-white/10 p-8 rounded-[3rem] h-fit sticky top-8">
        <h4 class="text-xs font-black text-slate-500 uppercase tracking-widest mb-6">Precios en tiempo real</h4>
        <div class="space-y-6">
            <div class="flex justify-between items-center">
                <span class="text-slate-400 font-bold">Diesel A</span>
                <span class="text-3xl font-black text-white">{{ number_format($estacion->diesel ?? 0, 3) }}€</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-slate-400 font-bold">Sin Plomo 95</span>
                <span class="text-3xl font-black text-cyan-400">{{ number_format($estacion->gasolina95 ?? 0, 3) }}€</span>
            </div>
        </div>
        <button class="w-full mt-8 bg-blue-600 hover:bg-blue-700 py-4 rounded-2xl font-black transition">CÓMO LLEGAR</button>
    </div>
</main>
</body>
</html>
