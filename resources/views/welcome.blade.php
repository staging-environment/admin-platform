<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Utrecar | Red de Estaciones</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#020617] text-white antialiased min-h-screen">
<header class="max-w-7xl mx-auto px-6 py-8 flex justify-between items-center border-b border-white/5">
    <div class="flex items-center gap-3">
        <div class="bg-white text-slate-950 px-3 py-1 rounded-xl font-black text-2xl">U</div>
        <h1 class="font-bold text-xl tracking-tight">Utrecar</h1>
    </div>
</header>

<main class="max-w-7xl mx-auto px-6 py-16">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($gasolineras as $gasolinera)
            <a href="{{ route('estacion.show', $gasolinera->Codigo) }}"
               class="group block bg-slate-900/50 border border-white/10 p-8 rounded-[2.5rem] backdrop-blur-xl hover:border-blue-500 transition-all duration-500">

                <div class="w-12 h-12 bg-blue-500/20 rounded-2xl flex items-center justify-center text-blue-400 mb-6 group-hover:bg-blue-500 group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>

                <h3 class="text-xl font-bold mb-1">{{ $gasolinera->Nombre }}</h3>
                <p class="text-[10px] text-slate-500 uppercase tracking-widest font-bold mb-8">{{ $gasolinera->Direccion }}</p>

                <div class="space-y-4 pt-8 border-t border-white/10">
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-black text-slate-500 uppercase">Diesel</span>
                        <span class="text-xl font-black text-white">{{ number_format($gasolinera->diesel ?? 0, 3) }}€</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-black text-slate-500 uppercase">SP 95</span>
                        <span class="text-xl font-black text-cyan-400">{{ number_format($gasolinera->gasolina95 ?? 0, 3) }}€</span>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</main>
</body>
</html>
