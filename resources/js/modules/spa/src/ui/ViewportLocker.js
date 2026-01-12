/**
 * 🔒 Viewport Locker
 * Bloqueia comportamentos nativos do navegador para emular app nativo.
 */
export const ViewportLocker = {
    init() {
        // 1. Força a Meta Tag correta (Primeira linha de defesa)
        this._enforceMetaTag();

        // 2. Injeta CSS Crítico (Bloqueio passivo e eficiente)
        this._injectBlockerCSS();

        // 3. Adiciona Listeners JS (Bloqueio ativo e lógica condicional)
        this._attachListeners();

        // 4. Ativa o Fail-safe (Monitora se o zoom vazou)
        this._watchVisualViewport();
    },

    /**
     * Garante que a meta tag viewport esteja configurada para não escalar
     */
    _enforceMetaTag() {
        let meta = document.querySelector('meta[name="viewport"]');
        if (!meta) {
            meta = document.createElement('meta');
            meta.name = 'viewport';
            document.head.appendChild(meta);
        }
        // user-scalable=no, maximum-scale=1.0, minimum-scale=1.0 previnem zoom na maioria dos casos
        meta.content = 'width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no, viewport-fit=cover';
    },

    /**
     * CSS é mais leve que JS para bloquear drag e touch actions
     */
    _injectBlockerCSS() {
        const style = document.createElement('style');
        style.id = 'spa-viewport-lock';
        style.innerHTML = `
            html, body {
                /* Previne pull-to-refresh e overscroll no iOS */
                overscroll-behavior: none;
                /* Previne double-tap zoom e permite apenas pan (scroll) */
                touch-action: pan-x pan-y; 
                /* Remove highlight de toque no mobile */
                -webkit-tap-highlight-color: transparent;
                /* Previne seleção de texto globalmente (habilite em inputs via CSS específico) */
                user-select: none;
                -webkit-user-select: none;
            }
            
            /* Previne drag de imagens e links */
            img, a {
                -webkit-user-drag: none;
                user-drag: none;
                pointer-events: auto;
            }
        `;
        document.head.appendChild(style);
    },

    _attachListeners() {
        // Opções para garantir que o preventDefault funcione (passive: false é crucial)
        const options = { passive: false };

        // --- BLOQUEIO DE ZOOM (PINÇA / MULTI-TOUCH) ---
        document.addEventListener('touchmove', (e) => {
            // Se houver mais de 1 dedo (2, 3, 4, 5...)
            if (e.touches.length > 1) {
                // Verifica se o alvo (ou pai) tem permissão explícita para gestos
                if (e.target.closest('[data-allow-gestures]')) {
                    return; // Permite o gesto neste elemento específico
                }
                // Caso contrário, mata o evento de zoom
                e.preventDefault();
            }
        }, options);

        // --- BLOQUEIO DE GESTOS SAFARI (GestureStart) ---
        // Safari dispara eventos específicos de 'gesture' antes do touchmove em alguns casos
        document.addEventListener('gesturestart', (e) => {
            if (!e.target.closest('[data-allow-gestures]')) {
                e.preventDefault();
            }
        }, options);

        // --- BLOQUEIO DE MENU DE CONTEXTO (LONG PRESS/RIGHT CLICK) ---
        document.addEventListener('contextmenu', (e) => {
            // Permite apenas em inputs ou áreas específicas se necessário
            if (!e.target.closest('input, textarea, [data-allow-context]')) {
                e.preventDefault();
            }
        }, false);

        // --- BLOQUEIO DE ZOOM VIA TECLADO/MOUSE (CTRL + WHEEL) ---
        document.addEventListener('wheel', (e) => {
            if (e.ctrlKey) {
                e.preventDefault();
            }
        }, options);

        // --- BLOQUEIO DE DOUBLE TAP ZOOM (Legacy) ---
        // O CSS 'touch-action: manipulation' já resolve 99%, mas isso garante
        let lastTouchEnd = 0;
        document.addEventListener('touchend', (e) => {
            const now = Date.now();
            if (now - lastTouchEnd <= 300) {
                // Se o toque for muito rápido (double tap), previne o padrão (zoom)
                // Mas não previne cliques em botões
                if (!e.target.closest('button, a, input')) {
                    e.preventDefault();
                }
            }
            lastTouchEnd = now;
        }, false);
    },

    /**
     * Fail-safe: Monitora se o zoom foi alterado e reseta
     */
    _watchVisualViewport() {
        if (!window.visualViewport) return;

        const handleResize = () => {
            // Se a escala for diferente de 1.0 (zoom ocorreu)
            if (window.visualViewport.scale !== 1.0) {
                // Força reset removendo e readicionando a meta tag (hack eficaz)
                const meta = document.querySelector('meta[name="viewport"]');
                const content = meta.content;
                meta.content = ''; 
                
                // Pequeno delay para o browser processar
                setTimeout(() => {
                    meta.content = content;
                }, 10);
            }
        };

        window.visualViewport.addEventListener('resize', handleResize);
        window.visualViewport.addEventListener('scroll', handleResize); // Opcional, para scroll agressivo
    }
};
