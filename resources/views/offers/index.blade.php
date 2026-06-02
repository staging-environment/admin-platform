@component('layouts.public', ['homeConfig' => $homeConfig, 'gasolineras' => $gasolineras])
    <div class="py-12">
        <div class="text-center mb-12 animate-fade-in-up">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-700 border border-amber-100/50 text-[10px] font-bold uppercase tracking-wider rounded-full mb-3">
                Únete a Utrecar
            </span>
            <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight">
                Ofertas de Empleo
            </h1>
            <p class="mt-3 max-w-2xl mx-auto text-base text-slate-500 font-medium">
                Descubre las oportunidades profesionales que tenemos para ti. Desarrolla tu carrera en una red de estaciones líder.
            </p>
        </div>

        <div class="space-y-6">
            @forelse($offers as $offer)
                <div class="group bg-white rounded-3xl border border-slate-200/60 border-l-4 border-l-amber-500/80 shadow-[0_8px_30px_rgb(0,0,0,0.015)] hover:border-amber-500/20 hover:shadow-[0_20px_50px_rgba(245,158,11,0.08)] hover:-translate-y-1 hover:scale-[1.005] transition-all duration-500 overflow-hidden p-6 md:p-8 animate-fade-in-up"
                     style="animation-delay: {{ $loop->index * 0.08 }}s;">
                    
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <span class="inline-block text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100/50 px-2 py-0.5 rounded-full uppercase tracking-wider mb-2">
                                Activa
                            </span>
                            <h2 class="text-xl md:text-2xl font-bold text-slate-900 tracking-tight group-hover:text-amber-600 transition-colors duration-300">
                                {{ $offer->title ?? $offer->co_title }}
                            </h2>
                            <p class="text-xs text-slate-400 font-medium mt-1">
                                Publicada el: {{ $offer->created_at ? $offer->created_at->format('d/m/Y') : 'Reciente' }}
                            </p>
                        </div>
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('offers.show', $offer->id) }}" class="inline-flex items-center justify-center px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg shadow-amber-500/20 hover:shadow-xl hover:shadow-amber-500/35 transition-all duration-300 transform active:scale-95">
                                Ver Detalles y Postularse
                            </a>
                        </div>
                    </div>

                    @if($offer->description ?? $offer->co_description)
                        <div class="mt-4 text-slate-600 text-sm leading-relaxed line-clamp-3 border-t border-slate-100 pt-4 prose max-w-none">
                            {!! $offer->description ?? $offer->co_description !!}
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-16 bg-white rounded-3xl border border-slate-200/60 shadow-sm animate-fade-in-up p-8">
                    <svg class="w-16 h-16 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <h3 class="text-lg font-bold text-slate-900 mb-1">No hay vacantes disponibles</h3>
                    <p class="text-sm text-slate-500 max-w-sm mx-auto font-medium">
                        Actualmente no tenemos ofertas de trabajo activas, pero estamos creciendo constantemente. ¡Vuelve a consultar pronto!
                    </p>
                </div>
            @endforelse
        </div>
    </div>
@endcomponent