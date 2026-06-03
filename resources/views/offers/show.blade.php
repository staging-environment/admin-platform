@component('layouts.public', ['homeConfig' => $homeConfig, 'gasolineras' => $gasolineras])
    <div class="py-12">
        <!-- Back Link -->
        <div class="mb-8 animate-fade-in-up">
            <a href="{{ route('offers.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-amber-600 hover:text-amber-700 transition-colors uppercase tracking-wider">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver al listado de ofertas
            </a>
        </div>

        <!-- Session Status / Errors -->
        @if(session('success'))
            <div class="mb-8 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-700 flex items-center gap-3 animate-fade-in-up">
                <svg class="w-6 h-6 flex-shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-8 p-5 bg-red-50 border border-red-200 rounded-2xl text-red-700 animate-fade-in-up">
                <div class="flex items-center gap-3 mb-3">
                    <svg class="w-6 h-6 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-bold">Por favor, corrige los siguientes errores:</span>
                </div>
                <ul class="list-disc pl-9 text-sm space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            <!-- Job Description (Col 1-2) -->
            <div class="lg:col-span-2 space-y-8 animate-fade-in-up">
                <div class="bg-white rounded-3xl border border-slate-200/60 p-6 md:p-10 shadow-[0_8px_30px_rgb(0,0,0,0.015)] border-l-4 border-l-amber-500/80">
                    <span class="inline-block text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-100/50 px-2.5 py-0.5 rounded-full uppercase tracking-wider mb-3">
                        Vacante Disponible
                    </span>
                    <h1 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight mb-2">
                        {{ $offer->title ?? $offer->co_title }}
                    </h1>
                    <p class="text-xs text-slate-400 font-medium mb-6">
                        Publicada el: {{ $offer->created_at ? $offer->created_at->format('d/m/Y') : 'Reciente' }}
                    </p>

                    <div class="prose max-w-none text-slate-600 leading-relaxed border-t border-slate-100 pt-6">
                        {!! $offer->description ?? $offer->co_description !!}
                    </div>
                </div>
            </div>

            <!-- Apply Form Sidebar (Col 3) -->
            <div class="space-y-6 w-full animate-fade-in-up" style="animation-delay: 0.1s;">
                <div class="bg-white rounded-3xl border border-slate-200/60 p-6 md:p-8 shadow-[0_15px_40px_-10px_rgba(0,0,0,0.02)] border-t-4 border-t-amber-500">
                    <h3 class="text-lg font-bold text-slate-900 mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                        Inscribirse a la Oferta
                    </h3>

                    <form action="{{ route('offers.apply', $offer->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                        @csrf
                        
                        {{-- Honeypot (Campo oculto para robots) --}}
                        <div style="display: none;">
                            <input type="text" name="website_url_check" tabindex="-1" autocomplete="off" placeholder="No rellenar">
                        </div>

                        @php
                            $num1 = rand(1, 9);
                            $num2 = rand(1, 9);
                            $sum = $num1 + $num2;
                            $captcha_token = encrypt($sum);
                        @endphp

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Nombre *</label>
                            <input type="text" name="first_name" value="{{ old('first_name') }}" required class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:border-amber-500 focus:ring-1 focus:ring-amber-500/20 transition-all outline-none text-sm" placeholder="Tu nombre">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Apellidos *</label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}" required class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:border-amber-500 focus:ring-1 focus:ring-amber-500/20 transition-all outline-none text-sm" placeholder="Tus apellidos">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Correo Electrónico *</label>
                            <input type="email" name="email" value="{{ old('email') }}" required class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:border-amber-500 focus:ring-1 focus:ring-amber-500/20 transition-all outline-none text-sm" placeholder="ejemplo@correo.com">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Teléfono *</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" required class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:border-amber-500 focus:ring-1 focus:ring-amber-500/20 transition-all outline-none text-sm" placeholder="Ej: 600 000 000">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Carta de presentación / Mensaje</label>
                            <textarea name="profile_description" rows="3" class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:border-amber-500 focus:ring-1 focus:ring-amber-500/20 transition-all outline-none text-sm resize-none" placeholder="Háblanos un poco sobre ti..."></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Adjuntar Currículum (PDF, DOC, DOCX) *</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-2xl hover:border-amber-500 transition-colors">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-xs text-slate-600 justify-center">
                                        <label for="cv-upload" class="relative cursor-pointer rounded-md font-bold text-amber-600 hover:text-amber-700 focus-within:outline-none">
                                            <span>Sube un archivo</span>
                                            <input id="cv-upload" name="cv" type="file" required class="sr-only">
                                        </label>
                                    </div>
                                    <p class="text-[10px] text-slate-400 font-medium">Hasta 5MB</p>
                                    <p id="cv-filename" class="text-xs text-emerald-600 font-bold hidden"></p>
                                </div>
                            </div>
                        </div>

                        {{-- Captcha Matemático --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Verificación de seguridad: ¿Cuánto es {{ $num1 }} + {{ $num2 }}? *</label>
                            <input type="number" name="captcha_answer" required class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:border-amber-500 focus:ring-1 focus:ring-amber-500/20 transition-all outline-none text-sm" placeholder="Escribe el resultado aquí">
                            <input type="hidden" name="captcha_token" value="{{ $captcha_token }}">
                        </div>

                        <button type="submit" class="w-full py-3.5 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs rounded-xl shadow-lg shadow-amber-500/20 hover:shadow-xl hover:shadow-amber-500/35 transition-all duration-300 uppercase tracking-widest">
                            Enviar candidatura
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Script to display selected CV filename -->
    <script>
        document.getElementById('cv-upload').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            const el = document.getElementById('cv-filename');
            if (fileName) {
                el.textContent = "✓ Archivo: " + fileName;
                el.classList.remove('hidden');
            } else {
                el.classList.add('hidden');
            }
        });
    </script>
@endcomponent