<!-- PWA Tags -->
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#d97706">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="Utrecar">
<link rel="apple-touch-icon" href="/ronda_norte_logo.png">

<!-- Floating PWA Install Banner -->
<div id="pwa-install-banner" style="display: none; position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); width: calc(100% - 32px); max-width: 400px; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(0,0,0,0.05); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1); padding: 16px; border-radius: 20px; z-index: 999999; align-items: center; justify-content: space-between; gap: 12px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <div style="display: flex; align-items: center; gap: 12px; text-align: left;">
        <img src="/ronda_norte_logo.png" style="width: 42px; height: 42px; object-fit: contain; border-radius: 10px;" alt="Logo" />
        <div>
            <h4 style="margin: 0; font-size: 14px; font-weight: 800; color: #111827;">Instalar Utrecar</h4>
            <p style="margin: 2px 0 0 0; font-size: 11px; color: #6b7280; line-height: 1.3;">Añade el acceso directo en tu móvil para entrar más rápido.</p>
        </div>
    </div>
    <div style="display: flex; align-items: center; gap: 8px; flex-shrink: 0;">
        <button id="pwa-install-btn" style="background-color: #d97706; color: white; border: none; font-size: 12px; font-weight: bold; padding: 8px 14px; border-radius: 10px; cursor: pointer; transition: background-color 0.2s;">
            Instalar
        </button>
        <button id="pwa-close-btn" style="background: none; border: none; color: #9ca3af; cursor: pointer; padding: 4px;">
            <svg style="width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2.5;" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>

<!-- iOS Instruction Modal -->
<div id="pwa-ios-instructions" style="display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.4); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); z-index: 9999999; justify-content: center; align-items: flex-end; padding: 16px;">
    <div style="background: white; width: 100%; max-width: 400px; border-radius: 24px; padding: 24px; text-align: center; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04); font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; position: relative;">
        <button id="pwa-ios-close-btn" style="position: absolute; top: 16px; right: 16px; background: none; border: none; color: #9ca3af; cursor: pointer; padding: 4px;">
            <svg style="width: 20px; height: 20px; stroke: currentColor; fill: none; stroke-width: 2.5;" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        
        <img src="/ronda_norte_logo.png" style="width: 60px; height: 60px; object-fit: contain; margin: 0 auto 16px auto; border-radius: 12px; display: block;" />
        <h3 style="margin: 0 0 8px 0; font-size: 18px; font-weight: 800; color: #111827;">Instalar en tu iPhone</h3>
        <p style="margin: 0 0 20px 0; font-size: 13px; color: #6b7280; line-height: 1.5;">Sigue estos dos sencillos pasos para añadir Utrecar a tu pantalla de inicio:</p>
        
        <div style="display: flex; flex-direction: column; gap: 16px; text-align: left; margin-bottom: 24px;">
            <div style="display: flex; align-items: flex-start; gap: 12px;">
                <div style="background: #fef3c7; color: #d97706; font-weight: bold; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 12px;">1</div>
                <div style="font-size: 13px; color: #374151; line-height: 1.4;">
                    Pulsa el botón de <strong>Compartir</strong> en Safari (el icono de la caja con la flecha apuntando hacia arriba <svg style="width: 16px; height: 16px; display: inline; vertical-align: middle; stroke: #007aff; fill: none; stroke-width: 2; margin: 0 2px;" viewBox="0 0 24 24"><path d="M4 12v8a2 2 0 002 2h12a2 2 0 002-2v-8M16 6l-4-4-4 4M12 2v13"/></svg>).
                </div>
            </div>
            <div style="display: flex; align-items: flex-start; gap: 12px;">
                <div style="background: #fef3c7; color: #d97706; font-weight: bold; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 12px;">2</div>
                <div style="font-size: 13px; color: #374151; line-height: 1.4;">
                    Desplázate hacia abajo y pulsa en <strong>"Añadir a la pantalla de inicio"</strong>.
                </div>
            </div>
        </div>
        
        <button id="pwa-ios-ok-btn" style="background: #d97706; color: white; border: none; width: 100%; font-size: 14px; font-weight: bold; padding: 12px; border-radius: 12px; cursor: pointer; transition: background-color 0.2s;">
            Entendido
        </button>
    </div>
</div>

<script>
    let deferredPrompt;

    // Detect device type
    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) || (navigator.userAgent.includes("Mac") && "ontouchend" in document);
    const isStandalone = window.navigator.standalone || window.matchMedia('(display-mode: standalone)').matches;

    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js')
                .then((reg) => console.log('Service Worker registered successfully.', reg.scope))
                .catch((err) => console.error('Service Worker registration failed.', err));
        });
    }

    // Capture the installation prompt (Android / Chrome Desktop)
    window.addEventListener('beforeinstallprompt', (e) => {
        // Prevent Chrome 67 and earlier from automatically showing the prompt
        e.preventDefault();
        // Stash the event so it can be triggered later
        deferredPrompt = e;
        
        // Check if user has previously dismissed this banner during the session
        if (!sessionStorage.getItem('pwa-banner-dismissed')) {
            const banner = document.getElementById('pwa-install-banner');
            if (banner) {
                banner.style.display = 'flex';
            }
        }
    });

    // Show banner on iOS if not standalone and not dismissed
    if (isIOS && !isStandalone && !sessionStorage.getItem('pwa-banner-dismissed')) {
        const banner = document.getElementById('pwa-install-banner');
        if (banner) {
            banner.style.display = 'flex';
        }
    }

    // Handle install button click
    const installBtn = document.getElementById('pwa-install-btn');
    if (installBtn) {
        installBtn.addEventListener('click', async () => {
            if (isIOS) {
                // Show iOS instructions modal
                const instructions = document.getElementById('pwa-ios-instructions');
                if (instructions) {
                    instructions.style.display = 'flex';
                }
                return;
            }

            if (!deferredPrompt) return;
            // Show the install prompt
            deferredPrompt.prompt();
            // Wait for the user to respond to the prompt
            const { outcome } = await deferredPrompt.userChoice;
            console.log(`User response to the install prompt: ${outcome}`);
            // We've used the prompt, and can't use it again
            deferredPrompt = null;
            // Hide the banner
            const banner = document.getElementById('pwa-install-banner');
            if (banner) banner.style.display = 'none';
        });
    }

    // Handle close button click
    const closeBtn = document.getElementById('pwa-close-btn');
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            const banner = document.getElementById('pwa-install-banner');
            if (banner) banner.style.display = 'none';
            // Remember dismissal for this session
            sessionStorage.setItem('pwa-banner-dismissed', 'true');
        });
    }

    // Handle iOS Instruction Modal closing
    const iosCloseBtn = document.getElementById('pwa-ios-close-btn');
    const iosOkBtn = document.getElementById('pwa-ios-ok-btn');
    const instructions = document.getElementById('pwa-ios-instructions');
    
    if (iosCloseBtn) {
        iosCloseBtn.addEventListener('click', () => {
            if (instructions) instructions.style.display = 'none';
        });
    }
    if (iosOkBtn) {
        iosOkBtn.addEventListener('click', () => {
            if (instructions) instructions.style.display = 'none';
        });
    }
</script>
