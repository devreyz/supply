/**
 * 📱 SPA Framework - PWA Install
 * Gerenciador de instalação de PWA
 */

/**
 * Gerenciador de instalação PWA
 */
export class PWAInstaller {
    constructor(spaOrOptions = {}, maybeOptions = {}) {
        // Compatibilidade: aceita (options) ou (spa, options)
        if (spaOrOptions && typeof spaOrOptions.modal === "function") {
            this.spa = spaOrOptions;
            this.options = {
                showBanner: true,
                bannerDelay: 5000,
                bannerDismissKey: "spa_pwa_dismissed",
                serviceWorker: "./service-worker.js",
                ...maybeOptions,
            };
        } else {
            this.spa = null;
            this.options = {
                showBanner: true,
                bannerDelay: 5000,
                bannerDismissKey: "spa_pwa_dismissed",
                serviceWorker: "./service-worker.js",
                ...spaOrOptions,
            };
        }

        this.deferredPrompt = null;
        this.isInstalled = false;
        this.isStandalone = false;
        this.isShowingPrompt = false; // Trava para evitar múltiplos modais
        this.hasAttemptedPrompt = false; // Garante que só tenta mostrar UMA vez por sessão/página
        this.installationPending = false; // Marca quando usuário iniciou instalação
        this.promptTimeout = null; // Referência para limpar o timer

        this._init();
    }

    /**
     * Mostra instruções de instalação para iOS (Safari)
     */
    async _showIOSInstallModal() {
        if (this.isInstalled || this.isShowingPrompt) return;

        this.isShowingPrompt = true;

        const html =
            this.options.installHtml ||
            `
    <style>
        .ios-wrapper {
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Text", "Segoe UI", Roboto, sans-serif;
            display: flex;
            flex-direction: column;
            gap: 1.25rem; /* Aumentei levemente o gap geral */
            color: #1c1c1e;
            margin-bottom: 1.5rem; /* Margem inferior solicitada */
        }

        /* --- Header: Identidade do App --- */
        .ios-hero {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .ios-app-icon {
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 0.8rem;
            background: linear-gradient(135deg, #ea580c, #c2410c);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            box-shadow: 0 4px 12px rgba(234, 88, 12, 0.3);
            flex-shrink: 0;
        }
        .ios-hero-content h3 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: -0.02em;
        }
        .ios-hero-content p {
            margin: 0.2rem 0 0;
            font-size: 0.85rem;
            color: #8e8e93;
        }

        /* --- Faixa de Recursos (Mini Bento) --- */
        .ios-features-strip {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            background: #F2F2F7;
            border-radius: 0.8rem;
            padding: 0.8rem 0.5rem;
            text-align: center;
        }
        .ios-feature-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.3rem;
            border-right: 1px solid rgba(0,0,0,0.08);
        }
        .ios-feature-item:last-child {
            border-right: none;
        }
        .ios-feat-icon {
            color: #007AFF;
            background: rgba(0, 122, 255, 0.1);
            padding: 0.35rem;
            border-radius: 50%;
        }
        .ios-feat-title {
            font-size: 0.7rem;
            font-weight: 600;
            margin-top: 0.1rem;
        }
        .ios-feat-desc {
            font-size: 0.65rem;
            color: #636366;
        }

        /* --- Instruções --- */
        .ios-steps-container {
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
        }
        .ios-step-row {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.6rem 0;
            border-bottom: 1px solid #E5E5EA;
        }
        .ios-step-row:last-child {
            border-bottom: none;
        }
        .ios-step-highlight {
            font-weight: 700;
            color: #000;
        }

        /* --- Finalização --- */
        .ios-footer-note {
            text-align: center;
            font-size: 0.8rem;
            color: #8e8e93;
            background: #fff;
            padding-top: 0.5rem;
        }
    </style>

    <div class="ios-wrapper">
        
        <div class="ios-hero">
            <div class="ios-app-icon">
                <i data-lucide="smartphone" style="width: 1.6rem; height: 1.6rem;"></i>
            </div>
            <div class="ios-hero-content">
                <h3>Instale o Tymely</h3>
                <p>Tenha a melhor experiência direto na sua tela inicial.</p>
            </div>
        </div>

        <div class="ios-features-strip">
            <div class="ios-feature-item">
                <div class="ios-feat-icon">
                    <i data-lucide="zap" style="width: 1rem; height: 1rem;"></i>
                </div>
                <span class="ios-feat-title">Rápido</span>
                <span class="ios-feat-desc">Instantâneo</span>
            </div>
            <div class="ios-feature-item">
                <div class="ios-feat-icon" style="color: #34C759; background: rgba(52, 199, 89, 0.1);">
                    <i data-lucide="wifi-off" style="width: 1rem; height: 1rem;"></i>
                </div>
                <span class="ios-feat-title">Offline</span>
                <span class="ios-feat-desc">Sem net</span>
            </div>
            <div class="ios-feature-item">
                <div class="ios-feat-icon" style="color: #FF9500; background: rgba(255, 149, 0, 0.1);">
                    <i data-lucide="feather" style="width: 1rem; height: 1rem;"></i>
                </div>
                <span class="ios-feat-title">Leve</span>
                <span class="ios-feat-desc">Otimizado</span>
            </div>
        </div>

        <div class="ios-steps-container">
            <div style="font-size: 0.75rem; font-weight: 600; color: #8E8E93; text-transform: uppercase; margin-bottom: 0.25rem; letter-spacing: 0.05em;">Como Adicionar</div>
            
            <div class="ios-step-row">
                <i data-lucide="share" style="color: #007AFF; width: 1.2rem; height: 1.2rem;"></i>
                <span style="font-size: 0.9rem; font-weight: 500;">Toque em <span class="ios-step-highlight">Compartilhar</span> na barra.</span>
            </div>
            
            <div class="ios-step-row">
                <i data-lucide="plus-square" style="color: #636366; width: 1.2rem; height: 1.2rem;"></i>
                <span style="font-size: 0.9rem; font-weight: 500;">Escolha <span class="ios-step-highlight">Adicionar à Tela de Início</span>.</span>
            </div>
        </div>

        <div class="ios-footer-note">
            <span>Pronto! O app aparecerá nos seus apps.</span>
        </div>

    </div>
    `;

        try {
            const modalOptions = {
                title: this.options.installTitle || "Instalar Aplicativo",
                html,
                type: "custom",
                closeOnBackdrop: false,
                width: this.options.installWidth || "420px",
                customButtons: [
                    {
                        text: this.options.cancelText || "Fechar",
                        class: "btn btn-outline",
                        value: "cancel",
                    },
                    {
                        text: this.options.okText || "Entendi",
                        class: "btn btn-primary",
                        value: "ok",
                    },
                ],
            };

            const result = await this.spa.modal(modalOptions);
            this.isShowingPrompt = false;

            // Registra timestamp de "dispensa" também quando o usuário clicar em 'Entendi'
            this._dismiss();
        } catch (e) {
            this.isShowingPrompt = false;
            console.error("📱 PWA: Falha ao abrir modal iOS", e);
        }
    }

    /**
     * Inicializa o instalador
     */
    _init() {
        // Verifica se já está instalado
        this.isStandalone =
            window.matchMedia("(display-mode: standalone)").matches ||
            window.navigator.standalone === true;

        if (this.isStandalone) {
            this.isInstalled = true;
            console.log("📱 PWA: App instalado (standalone)");
            return;
        }

        // Escuta evento beforeinstallprompt
        window.addEventListener("beforeinstallprompt", (e) => {
            e.preventDefault();
            this.deferredPrompt = e;
            console.log("📱 PWA: Prompt de instalação disponível");

            // Emite evento
            this._emit("pwa:installable");

            // Se já tentou mostrar nesta sessão, ou já está mostrando, ou já instalou, ignora
            if (
                this.hasAttemptedPrompt ||
                this.isShowingPrompt ||
                this.isInstalled
            ) {
                return;
            }

            // Limpa timeout anterior para evitar acúmulo
            if (this.promptTimeout) clearTimeout(this.promptTimeout);

            // Mostra modal/banner se configurado
            if (this.options.showBanner && !this._wasDismissed()) {
                this.hasAttemptedPrompt = true; // Marca que já agendou a exibição única

                this.promptTimeout = setTimeout(() => {
                    // Verificação final antes de abrir
                    if (this.isShowingPrompt || this.isInstalled) return;

                    // Preferência: usar modal do SPA quando disponível
                    if (this.spa && typeof this.spa.modal === "function") {
                        this._showInstallModal();
                    } else {
                        this._showBanner();
                    }
                }, this.options.bannerDelay);
            }
        });

        // Escuta quando app é instalado
        window.addEventListener("appinstalled", () => {
            this.isInstalled = true;
            this.deferredPrompt = null;
            if (this.promptTimeout) clearTimeout(this.promptTimeout);
            this.isShowingPrompt = false;
            console.log("📱 PWA: App instalado!");
            this._emit("pwa:installed");
            this._hideBanner();
            this.installationPending = false;
            try {
                if (this.spa && typeof this.spa.toastSuccess === "function") {
                    this.spa.toastSuccess(
                        "App instalado",
                        "A aplicação foi adicionada à sua tela inicial"
                    );
                }
            } catch (e) {
                /* ignore */
            }
        });

        // Registra Service Worker
        this._registerServiceWorker();

        // Fallback para iOS: mostrar instruções manuais (iOS não dispara beforeinstallprompt)
        try {
            if (
                this.options.showBanner &&
                !this._wasDismissed() &&
                !this.isInstalled &&
                !this.hasAttemptedPrompt
            ) {
                const platform = this._detectPlatform();
                if (platform === "ios") {
                    this.hasAttemptedPrompt = true;
                    if (this.promptTimeout) clearTimeout(this.promptTimeout);
                    this.promptTimeout = setTimeout(() => {
                        if (this.isShowingPrompt || this.isInstalled) return;
                        this._showIOSInstallModal();
                    }, this.options.bannerDelay);
                }
            }
        } catch (e) {
            /* noop */
        }
    }

    /**
     * Registra o Service Worker
     */
    async _registerServiceWorker() {
        if (!("serviceWorker" in navigator)) {
            console.warn("📱 PWA: Service Worker não suportado");
            return;
        }

        try {
            const options = {};
            if (this.options.scope) {
                options.scope = this.options.scope;
            }

            const registration = await navigator.serviceWorker.register(
                this.options.serviceWorker || "./service-worker.js",
                options
            );
            console.log(
                "📱 PWA: Service Worker registrado",
                registration.scope
            );

            // Escuta atualizações
            registration.addEventListener("updatefound", () => {
                const newWorker = registration.installing;
                newWorker.addEventListener("statechange", () => {
                    if (
                        newWorker.state === "installed" &&
                        navigator.serviceWorker.controller
                    ) {
                        console.log("📱 PWA: Nova versão disponível");
                        this._emit("pwa:update-available", registration);
                    }
                });
            });

            // Escuta mensagens do SW
            navigator.serviceWorker.addEventListener("message", (event) => {
                if (event.data.type === "SYNC_TRIGGERED") {
                    this._emit("pwa:sync");
                }
            });
        } catch (error) {
            console.error("📱 PWA: Erro ao registrar SW:", error);
        }
    }

    /**
     * Dispara manualmente o prompt de instalação
     */
    showInstallPrompt() {
        if (this.isInstalled) {
            console.log("📱 PWA: Já instalado, ignorando prompt");
            return;
        }

        const platform = this._detectPlatform();
        if (platform === "ios") {
            return this._showIOSInstallModal();
        }

        if (this.deferredPrompt) {
            this._showInstallModal();
        } else {
            console.warn(
                "📱 PWA: Instalador iniciado manualmente, mas deferredPrompt ainda não foi capturado."
            );
            // Se não tem prompt, tenta mostrar o banner padrão do SPA (que pode ter instruções ou fallback)
            this._showBanner();
        }
    }

    /**
     * Verifica se pode instalar
     */
    canInstall() {
        return this.deferredPrompt !== null && !this.isInstalled;
    }

    /**
     * Detecta plataforma simplificada
     */
    _detectPlatform() {
        const ua =
            navigator.userAgent || navigator.vendor || window.opera || "";
        const uaLower = ua.toLowerCase();
        if (
            /iphone|ipad|ipod/.test(uaLower) ||
            (uaLower.includes("mac") && "ontouchend" in document)
        ) {
            return "ios";
        }
        if (/android/.test(uaLower)) return "android";
        if (/windows/.test(uaLower)) return "windows";
        if (/mac os x/.test(uaLower)) return "mac";
        return "desktop";
    }

    /**
     * Mostra prompt de instalação
     */
    async promptInstall() {
        if (!this.deferredPrompt) {
            console.warn("📱 PWA: Prompt de instalação não disponível");
            return false;
        }

        this.deferredPrompt.prompt();

        const { outcome } = await this.deferredPrompt.userChoice;
        console.log(
            `📱 PWA: Usuário ${
                outcome === "accepted" ? "aceitou" : "recusou"
            } instalação`
        );

        this.deferredPrompt = null;
        return outcome === "accepted";
    }

    /**
     * Mostra modal de instalação (usa sistema de modais do SPA quando disponível)
     */
    async _showInstallModal() {
        if (this.isInstalled || !this.canInstall() || this.isShowingPrompt)
            return;

        this.isShowingPrompt = true;

        // Se for iOS, redireciona para o modal de instruções (iOS não usa beforeinstallprompt)
        const platform = this._detectPlatform();
        if (platform === "ios") {
            this.isShowingPrompt = false;
            return this._showIOSInstallModal();
        }

        const defaultHtml =
            this.options.installHtml ||
            `
    <style>
        /* Animação suave do Gradiente de Fundo */
        @keyframes premiumGradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        /* Animação de Flutuação (Floating) */
        @keyframes premiumFloat {
            0%, 100% { transform: translateY(0) rotate(-3deg); }
            50% { transform: translateY(-8px) rotate(2deg); }
        }

        .pwa-bento-container {
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "Segoe UI", Roboto, sans-serif;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
            margin: 1.5rem 0;
        }

        /* --- Hero Card (Card Principal) --- */
        .pwa-hero-card {
            grid-column: span 2;
            padding: 1.5rem;
            border-radius: 1.25rem;
            /* Gradiente Premium Sky Blue */
            background: linear-gradient(120deg, #e0f2fe, #bae6fd, #e0f2fe);
            background-size: 200% 200%;
            animation: premiumGradient 8s ease infinite;
            
            border: 1px solid rgba(14, 165, 233, 0.2);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px -10px rgba(14, 165, 233, 0.25);
        }

        /* Elemento Decorativo de Fundo */
        .pwa-hero-bg-blur {
            position: absolute;
            top: -50%;
            right: -20%;
            width: 150px;
            height: 150px;
            background: #0ea5e9;
            filter: blur(80px);
            opacity: 0.15;
            z-index: 0;
        }

        .pwa-hero-content {
            z-index: 1;
            flex: 1;
        }

        .pwa-hero-title {
            margin: 0;
            font-weight: 800;
            font-size: 1.2rem;
            color: #0c4a6e; /* Azul Escuro Profundo */
            letter-spacing: -0.03em;
            line-height: 1.1;
        }

        .pwa-hero-subtitle {
            margin: 0.35rem 0 0;
            font-size: 0.8rem;
            color: #0369a1;
            font-weight: 500;
            line-height: 1.3;
        }

        /* --- Composição dos Ícones Sobrepostos --- */
        .pwa-icon-composition {
            position: relative;
            width: 4rem;
            height: 4rem;
            flex-shrink: 0;
            z-index: 1;
        }

        /* Ícone Traseiro (Smartphone) */
        .pwa-layer-phone {
            position: absolute;
            right: 0;
            bottom: 0;
            width: 2.8rem;
            height: 2.8rem;
            color: #0ea5e9;
            opacity: 0.2;
            transform: rotate(10deg);
        }

        /* Ícone Frontal (Logo Flutuante) */
        .pwa-layer-brand {
            position: absolute;
            left: 0;
            top: 0;
            width: 3rem;
            height: 3rem;
            border-radius: 0.9rem;
            
            /* Gradiente do botão */
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            
            /* Efeito 3D e Sombra */
            box-shadow: 
                0 8px 20px rgba(14, 165, 233, 0.4),
                inset 0 1px 1px rgba(255,255,255,0.4);
            border: 2px solid rgba(255,255,255,0.6);
            
            animation: premiumFloat 5s ease-in-out infinite;
        }

        /* --- Cards de Benefícios --- */
        .pwa-benefit-card {
            padding: 1rem;
            border-radius: 1rem;
            background: #ffffff;
            border: 1px solid #f1f5f9;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .pwa-benefit-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border-color: #e2e8f0;
        }

        /* Container do ícone do benefício */
        .pwa-icon-box {
            width: 2.2rem;
            height: 2.2rem;
            border-radius: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pwa-benefit-text h5 {
            margin: 0;
            font-size: 0.85rem;
            font-weight: 700;
            color: #334155;
        }
        
        .pwa-benefit-text p {
            margin: 0.15rem 0 0;
            font-size: 0.7rem;
            color: #64748b;
            line-height: 1.25;
        }
    </style>

    <div class="pwa-install-bento pwa-bento-container">
        
        <div class="pwa-hero-card">
            <div class="pwa-hero-bg-blur"></div>
            
            <div class="pwa-hero-content">
                <h4 class="pwa-hero-title">Instale o App</h4>
                <p class="pwa-hero-subtitle">Acesso rápido, seguro e sem ocupar espaço.</p>
            </div>

            <div class="pwa-icon-composition">
                <div class="pwa-layer-phone">
                    <i data-lucide="smartphone" style="width: 100%; height: 100%;"></i>
                </div>
                <div class="pwa-layer-brand">
                    <i data-lucide="sparkles" style="width: 1.5rem; height: 1.5rem;"></i>
                </div>
            </div>
        </div>
        
        <div class="pwa-benefit-card">
            <div class="pwa-icon-box" style="background: #fffbeb; color: #d97706;">
                <i data-lucide="zap" style="width: 1.1rem; height: 1.1rem;"></i>
            </div>
            <div class="pwa-benefit-text">
                <h5>Instantâneo</h5>
                <p>Abre em segundos.</p>
            </div>
        </div>

        <div class="pwa-benefit-card">
            <div class="pwa-icon-box" style="background: #ecfdf5; color: #059669;">
                <i data-lucide="wifi-off" style="width: 1.1rem; height: 1.1rem;"></i>
            </div>
            <div class="pwa-benefit-text">
                <h5>Offline</h5>
                <p>Use sem internet.</p>
            </div>
        </div>

        <div class="pwa-benefit-card">
            <div class="pwa-icon-box" style="background: #f5f3ff; color: #7c3aed;">
                <i data-lucide="feather" style="width: 1.1rem; height: 1.1rem;"></i>
            </div>
            <div class="pwa-benefit-text">
                <h5>Super Leve</h5>
                <p>Poupa memória.</p>
            </div>
        </div>

        <div class="pwa-benefit-card">
            <div class="pwa-icon-box" style="background: #fff1f2; color: #e11d48;">
                <i data-lucide="refresh-cw" style="width: 1.1rem; height: 1.1rem;"></i>
            </div>
            <div class="pwa-benefit-text">
                <h5>Atualizado</h5>
                <p>Sync automático.</p>
            </div>
        </div>
    </div>
    `;

        const html = this.options.installHtml || defaultHtml;

        try {
            const modalOptions = {
                title: this.options.installTitle || "Instalar Aplicativo",
                html,
                type: "custom",
                closeOnBackdrop: false,
                width: this.options.installWidth || "480px",
                customButtons: [
                    {
                        text: this.options.cancelText || "Agora não",
                        class: "btn btn-outline",
                        value: "cancel",
                    },
                    {
                        text: this.options.installText || "Instalar Agora",
                        class: "btn btn-success",
                        value: "ok",
                    },
                ],
            };

            // Abre o modal
            const modalPromise = this.spa.modal(modalOptions);

            const result = await modalPromise;
            this.isShowingPrompt = false;

            if (result === "ok") {
                const accepted = await this.promptInstall();
                if (accepted) {
                    // Não mostrar o toast final aqui — esperamos pelo evento `appinstalled`.
                    this.installationPending = true;
                    try {
                        if (
                            this.spa &&
                            typeof this.spa.toastInfo === "function"
                        ) {
                            this.spa.toastInfo(
                                "Instalação iniciada",
                                "Aguarde enquanto o sistema finaliza a instalação."
                            );
                        } else if (
                            this.spa &&
                            typeof this.spa.toast === "function"
                        ) {
                            this.spa.toast(
                                "Instalação iniciada — aguardando confirmação."
                            );
                        } else {
                            console.log(
                                "📱 PWA: Instalação iniciada — aguardando evento appinstalled."
                            );
                        }
                    } catch (e) {
                        /* ignore */
                    }
                } else {
                    this._dismiss();
                }
            } else {
                // Usuário dispensou
                this._dismiss();
            }
        } catch (e) {
            this.isShowingPrompt = false;
            // Se modal falhar, fallback para banner
            console.error(
                "📱 PWA: Falha ao abrir modal de instalação, usando banner como fallback",
                e
            );
            this._showBanner();
        }
    }

    /**
     * Mostra banner de instalação
     */
    _showBanner() {
        if (this.isInstalled || !this.canInstall() || this.isShowingPrompt)
            return;

        // Remove banner existente
        this._hideBanner();

        this.isShowingPrompt = true;

        const banner = document.createElement("div");
        banner.id = "pwa-install-banner";
        banner.className = "pwa-banner";
        banner.innerHTML = `
                <div class="pwa-banner-content">
                    <div class="pwa-banner-icon">📱</div>
                    <div class="pwa-banner-text">
                        <strong>Instale o App</strong>
                        <span>Adicione à tela inicial para acesso rápido</span>
                    </div>
                </div>
                <div class="pwa-banner-actions">
                    <button class="pwa-banner-dismiss" data-pwa-dismiss>Agora não</button>
                    <button class="pwa-banner-install" data-pwa-install>Instalar</button>
                </div>
            `;

        document.body.appendChild(banner);

        // Anima entrada
        requestAnimationFrame(() => {
            banner.classList.add("show");
        });

        // Event listeners
        banner
            .querySelector("[data-pwa-install]")
            .addEventListener("click", () => {
                this.promptInstall();
                this._hideBanner();
            });

        banner
            .querySelector("[data-pwa-dismiss]")
            .addEventListener("click", () => {
                this._dismiss();
                this._hideBanner();
            });
    }

    /**
     * Esconde banner
     */
    _hideBanner() {
        const banner = document.getElementById("pwa-install-banner");
        if (banner) {
            banner.classList.remove("show");
            setTimeout(() => {
                banner.remove();
                this.isShowingPrompt = false;
            }, 300);
        } else {
            this.isShowingPrompt = false;
        }
    }

    /**
     * Marca como dispensado
     */
    _dismiss() {
        localStorage.setItem(
            this.options.bannerDismissKey,
            Date.now().toString()
        );
    }

    /**
     * Verifica se foi dispensado recentemente
     */
    _wasDismissed() {
        const dismissed = localStorage.getItem(this.options.bannerDismissKey);
        if (!dismissed) return false;

        // Mostra novamente após 30 minutos (conforme solicitado)
        const waitTime = 30 * 60 * 1000;
        return Date.now() - parseInt(dismissed) < waitTime;
    }

    /**
     * Atualiza o Service Worker
     */
    async update() {
        if (!("serviceWorker" in navigator)) return;

        const registration = await navigator.serviceWorker.getRegistration();
        if (registration) {
            await registration.update();
        }
    }

    /**
     * Envia mensagem para o Service Worker
     */
    async sendMessage(message) {
        if (!("serviceWorker" in navigator)) return;

        const registration = await navigator.serviceWorker.ready;
        if (registration.active) {
            registration.active.postMessage(message);
        }
    }

    /**
     * Limpa cache do Service Worker
     */
    async clearCache() {
        await this.sendMessage({ type: "CLEAR_CACHE" });
    }

    /**
     * Adiciona URLs ao cache
     */
    async cacheUrls(urls) {
        await this.sendMessage({ type: "CACHE_URLS", payload: { urls } });
    }

    /**
     * Emite evento
     */
    _emit(event, data = null) {
        document.dispatchEvent(new CustomEvent(event, { detail: data }));
    }
}

// Compatibilidade com uso global (opcional)
if (typeof window !== "undefined") {
    window.PWAInstaller = PWAInstaller;
}
