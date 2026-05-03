<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Utrecar | Red Virtusgesnet</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#020617] text-white antialiased min-h-screen font-sans">
<div class="relative min-h-screen overflow-hidden">
    <!-- Luces de fondo (Glow) -->
    <div class="absolute -top-40 -right-40 h-96 w-96 rounded-full bg-blue-500/10 blur-[100px]"></div>
    <div class="absolute bottom-0 left-0 h-96 w-96 rounded-full bg-cyan-500/5 blur-[100px]"></div>

    <header class="relative z-10 max-w-7xl mx-auto px-6 py-8 flex justify-between items-center border-b border-white/5">
        <div class="flex items-center gap-3">
            <div class="bg-white text-slate-950 px-3 py-1 rounded-xl font-black text-2xl">U</div>
            <div>
                <p class="font-bold text-xl tracking-tight leading-none">Utrecar</p>
                <p class="text-[10px] uppercase tracking-[0.3em] text-blue-400 font-bold mt-1">Virtusgesnet Active</p>
            </div>
        </div>
        <a href="/admin/login" class="text-xs font-bold border border-white/20 px-5 py-2.5 rounded-full hover:bg-white/10 transition-all">
            ACCEDER AL PANEL
        </a>
    </header>

    <main class="relative z-10 max-w-7xl mx-auto px-6 py-16">
        <div class="max-w-2xl mb-12">
            <h2 class="text-4xl md:text-5xl font-black mb-4 leading-tight">
                Estado de nuestras <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-300 to-blue-500">Estaciones de Servicio</span>
            </h2>
            <p class="text-slate-400 text-lg">Información en tiempo real desde la base de datos central de 5.5 GB.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($gasolineras as $gasolinera)
                <div class="group bg-slate-900/40 border border-white/10 p-8 rounded-[2.5rem] backdrop-blur-xl hover:border-blue-500/50 transition-all duration-500 shadow-2xl">
                    <div class="w-12 h-12 bg-blue-500/20 rounded-2xl flex items-center justify-center text-blue-400 mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        </svg>
                    </div>

                    <h3 class="text-xl font-bold text-white mb-2">{{ $gasolinera->Nombre ?? 'Estación Utrecar' }}</h3>
                    <p class="text-[10px] text-slate-500 uppercase tracking-widest font-bold leading-relaxed min-h-[32px]">
                        {{ $gasolinera->Direccion }}
                    </p>

                    <div class="mt-8 pt-8 border-t border-white/10 space-y-5">
                        <div class="flex justify-between items-end">
                            <span class="text-[10px] font-black text-slate-500 uppercase tracking-tighter">Diésel A</span>
                            <span class="text-2xl font-black text-white tabular-nums">
                                    {{ number_format($gasolinera->diesel ?? 0, 3) }}<span class="text-xs text-slate-500 ml-1">€</span>
                                </span>
                        </div>
                        <div class="flex justify-between items-end">
                            <span class="text-[10px] font-black text-slate-500 uppercase tracking-tighter">Gasolina 95</span>
                            <span class="text-2xl font-black text-cyan-400 tabular-nums">
                                    {{ number_format($gasolinera->gasolina95 ?? 0, 3) }}<span class="text-xs text-slate-500 ml-1">€</span>
                                </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 text-center bg-white/5 rounded-[2.5rem] border border-dashed border-white/20">
                    <p class="text-slate-400 italic font-medium text-lg">Cargando estaciones desde Virtusgesnet...</p>
                </div>
            @endforelse
        </div>
    </main>
</div>
</body>
</html>
