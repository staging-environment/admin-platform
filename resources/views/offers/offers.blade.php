<!DOCTYPE html
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ofertas de Trabajo | Utrecar</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Outfit', sans-serif; }
        @keyframes fadeInUp { from { opacity:0; transform: translateY(20px); } to { opacity:1; transform: translateY(0); } }
        .animate-fade-in-up { animation: fadeInUp 0.7s cubic-bezier(0.16,1,0.3,1) both; }
    </style>
</head>
<body class="bg-slate-50/50 text-slate-800 antialiased min-h-screen flex items-center justify-center">
    <section class="py-12 bg-gray-50 flex flex-col items-center justify-center min-h-[40vh]">
        <div class="animate-fade-in-up text-center">
            <svg class="w-24 h-24 mx-auto mb-6 text-amber-600" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="12" y="22" width="40" height="30" rx="4" fill="url(#grad)"/>
                <rect x="12" y="16" width="40" height="8" rx="2" fill="url(#grad)"/>
                <defs>
                    <linearGradient id="grad" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#06b6d4"/>
                        <stop offset="100%" stop-color="#1e40af"/>
                    </linearGradient>
                </defs>
            </svg>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">¡Vaya!</h2>
            <p class="text-lg text-gray-600 max-w-md mx-auto">
                Actualmente no hay ofertas de trabajo activas.<br/>Vuelve pronto, estamos preparando nuevas oportunidades para ti.
            </p>
        </div>
    </section>
</body>
</html>
