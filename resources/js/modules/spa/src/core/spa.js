/**
 * 🚀 SPA Framework - Core
 * Framework SPA completo para aplicações web nativas
 *
 * @version 2.0.0
 * @author devreyz
 */

import { IndexedDBORM } from "../storage/indexeddb.js";
import { LocalStorageORM } from "../storage/localstorage.js";
import { JobQueue } from "../offline/queue.js";
import { PWAInstaller } from "../pwa/install.js";
import { NotificationManager } from "../pwa/notifications.js";
import {
    DrawerManager,
    SheetManager,
    ModalManager,
    MultiStepSheetManager,
} from "../ui/modals.js";

/**
 * Configurações padrão do framework
 */
export const DEFAULT_CONFIG = {
    // Navegação
    homePage: "home",
    pagePrefix: "page-",
    useHistory: true,
    scrollToTop: true,

    // Animações
    animation: {
        type: "fade",
        speed: 0.35,
        // velocidade menor para animações de retorno (back)
        backSpeed: 0.08,
        easing: "cubic-bezier(0.25, 0.46, 0.45, 0.94)",
    },

    // Gestos
    gestures: {
        enabled: true,
        swipeBack: true,
        swipeThreshold: 50,
        edgeWidth: 30,
        velocityThreshold: 0.3,
    },

    // UI
    ui: {
        autoTheme: false, // Detecta automaticamente o tema do sistema
        toast: {
            duration: 3000,
            position: "bottom-right",
            maxVisible: 3,
        },
        backdrop: {
            opacity: 0.3,
            blur: 12,
            closeOnClick: true,
        },
    },

    // Double tap to exit
    doubleTapExit: true,
    doubleTapTimeout: 2000,

    // Debug
    debug: {
        enabled: false,
        level: 1,
    },

    // PWA
    pwa: {
        enabled: true,
        serviceWorker: "/sw.js",
        showBanner: true,
        bannerDelay: 5000,
    },

    // Database
    db: {
        name: "spa_app",
        version: 1,
    },
    storage: {
        prefix: "spa_",
    },
    // Callbacks
    callbacks: {
        beforeNavigate: null,
        afterNavigate: null,
        onOverlayOpen: null,
        onOverlayClose: null,
        onError: null,
    },
};

/**
 * Classe principal do SPA Framework
 */
class SPA {
    constructor(config = {}) {
        // Merge configs
        this.config = this._deepMerge(DEFAULT_CONFIG, config);
        // Estado
        this.current = this.config.homePage;
        this.previousPage = null;
        this.activeOverlays = [];
        this.lastBackPress = 0;
        this._lastNavigate = 0;

        // Hierarquia de páginas
        this.pageHierarchy = new Map();

        // Elementos do DOM
        this.pages = new Map();
        this.backdrop = null;
        this.toastContainer = null;
        this.loadingOverlay = null;

        // Managers
        this.drawerManager = null;
        this.sheetManager = null;
        this.modalManager = null;

        // Storage
        this.db = null;
        this.storage = null;
        this.queue = null;

        // PWA
        this.pwa = null;
        this.notifications = null;

        // Estado de conexão
        this._online = navigator.onLine;

        // Bind methods
        this._handlePopState = this._handlePopState.bind(this);
        this._handleClick = this._handleClick.bind(this);
        this._handleOnline = this._handleOnline.bind(this);
        this._handleOffline = this._handleOffline.bind(this);

        // Inicializa o Proxy de elementos (app.el)
        this._setupElProxy();
    }

    /**
     * Configura o Proxy para acesso a elementos via app.el
     * Permite: app.el("selector") e app.el.name
     * @private
     */
    _setupElProxy() {
        // Objeto base que é uma função: app.el("selector")
        const elBase = (selector) => {
            if (!selector) return null;
            // Se for um objeto (elemento DOM), retorna ele mesmo
            if (typeof selector === "object" && selector.nodeType)
                return selector;

            // Se não tiver caracteres de seletor (., #, [, >), assume que é um ID
            if (/^[a-zA-Z0-9_-]+$/.test(selector)) {
                return document.getElementById(selector);
            }
            return document.querySelector(selector);
        };

        // Cache para os elementos registrados via registerElements
        this._elCache = {};

        // Proxy para permitir app.el.name e app.el("selector")
        this.el = new Proxy(elBase, {
            get: (target, prop) => {
                // Se a propriedade existe no cache, retorne
                if (this._elCache && prop in this._elCache) {
                    return this._elCache[prop];
                }
                // Se for uma propriedade da função base (como 'bind', 'call')
                if (prop in target) {
                    return target[prop];
                }
                return undefined;
            },
            set: (target, prop, value) => {
                if (this._elCache) {
                    this._elCache[prop] = value;
                }
                return true;
            },
        });
    }

    /**
     * Registra uma lista de elementos para acesso rápido via app.el.nome
     * @param {Object|Array} elements - Objeto {nome: seletor} ou Array de seletores (usa ID como nome)
     */
    registerElements(elements) {
        if (!elements) return;

        if (Array.isArray(elements)) {
            elements.forEach((selector) => {
                const el = this.el(selector);
                if (el) {
                    // Pega o ID ou o nome da tag como chave
                    const key = el.id || selector.replace(/[^a-zA-Z0-9]/g, "_");
                    this.el[key] = el;
                }
            });
        } else {
            for (const [name, selector] of Object.entries(elements)) {
                const el = this.el(selector);
                if (el) {
                    this.el[name] = el;
                }
            }
        }
    }

    /**
     * Inicializa o framework
     */
    async init() {
        try {
            // Registra páginas
            this._registerPages();

            // Cria elementos necessários
            this._createElements();

            // Inicializa managers
            this._initManagers();

            // Configura event listeners
            this._setupEventListeners();

            // Configura histórico inicial
            this._setupInitialHistory();

            // Inicializa storage
            await this._initStorage();

            // Aplica preferências salvas
            this._applyPreferences();

            // Inicializa PWA
            this._initPWA();

            // Aplica animação padrão
            document.documentElement.dataset.animation =
                this.config.animation.type;
            document.documentElement.style.setProperty(
                "--spa-animation-speed",
                this.config.animation.speed + "s"
            );

            // Esconde loading inicial
            this.hideLoading();

            // Emite evento de inicialização
            this._emit("spa:ready", { spa: this });
            this._emitPageEvent(this.current, "page:enter");

            this._log(1, "✅ SPA Framework inicializado");
            this._log(2, `📊 ${this.pageHierarchy.size} páginas registradas`);

            return this;
        } catch (error) {
            console.error("❌ Erro ao inicializar SPA:", error);
            throw error;
        }
    }

    // =================== NAVEGAÇÃO ===================

    /**
     * Navega para uma página
     * @param {string} id - ID da página (sem prefixo)
     * @param {Object} options - Opções de navegação
     */
    go(id, options = {}) {
        if (id === this.current) return;

        // Debounce anti-clique-rápido
        const now = Date.now();
        if (this._lastNavigate && now - this._lastNavigate < 150) {
            this._log(2, "⏳ Navegação ignorada (debounce)");
            return;
        }
        this._lastNavigate = now;

        const currentEl = this.pages.get(this.current);
        const nextEl = this.pages.get(id);

        if (!nextEl) {
            console.error(`❌ Página não encontrada: ${id}`);
            this.toastError(
                `❌  ERROR`,
                `Página não encontrada: ${id || "undefined"}`
            );
            return;
        }

        // Lógica de hierarquia Lamarck
        const currentHierarchy = this.pageHierarchy.get(this.current);
        const targetHierarchy = this.pageHierarchy.get(id);

        const currentLevel = this._getLevelValue(
            currentHierarchy?.level || "primary"
        );
        const targetLevel = this._getLevelValue(
            targetHierarchy?.level || "primary"
        );
        const isGoingHome = id === this.config.homePage;

        // 1. Se for para home, sempre usa back
        if (isGoingHome) {
            this.back();
            return;
        }

        // 2. Se for para um nível inferior (ex: secondary -> primary), usa back
        if (targetLevel < currentLevel) {
            this.back();
            return;
        }

        // Prepare bindings: hide elements that shouldn't appear on target immediately
        // This gives instant visual feedback (opacity 0 + disabled) before navigation animation
        try {
            this._prepareBindingsForNavigation(id);
        } catch (err) {
            // ignore
        }

        // Callback before navigate
        if (this.config.callbacks.beforeNavigate) {
            const shouldContinue = this.config.callbacks.beforeNavigate(
                this.current,
                id
            );
            if (shouldContinue === false) return;
        }

        // Define animação
        const animation = options.animation || this.config.animation.type;
        // Garantir que a velocidade da animação forward esteja aplicada
        document.documentElement.style.setProperty(
            "--spa-animation-speed",
            this.config.animation.speed + "s"
        );
        document.documentElement.dataset.animation = animation;

        // Armazena animação de volta
        nextEl.dataset.animBack = animation;

        // Emite evento de saída
        this._emitPageEvent(this.current, "page:leave");

        // Limpa classes
        this._clearPageClasses(currentEl, nextEl);

        // Aplica transição FORWARD
        currentEl.classList.add("prev");
        nextEl.classList.add("active");

        // Atualiza estado
        this.previousPage = this.current;
        this.current = id;

        // Gerencia histórico
        const state = {
            page: id,
            overlays: [],
            type: targetHierarchy?.level || (isGoingHome ? "home" : "primary"),
            parent: targetHierarchy?.parent || this.config.homePage,
        };

        // 3. Se for mesmo nível (e não for home), usa replaceState
        // 4. Se for nível superior (ex: home -> primary ou primary -> secondary), usa pushState
        if (
            options.replaceHistory ||
            (targetLevel === currentLevel &&
                this.current !== this.config.homePage)
        ) {
            history.replaceState(state, "", `#${id}`);
        } else {
            history.pushState(state, "", `#${id}`);
        }

        // Scroll to top
        if (this.config.scrollToTop) {
            nextEl.scrollTop = 0;
        }

        // Limpa classes após animação (usa velocidade forward)
        setTimeout(() => {
            currentEl.classList.remove("prev");
            this._emitPageEvent(id, "page:enter");

            // Callback after navigate
            if (this.config.callbacks.afterNavigate) {
                this.config.callbacks.afterNavigate(this.previousPage, id);
            }
        }, this.config.animation.speed * 1000);
    }

    /**
     * Volta para página anterior com lógica hierárquica
     * @param {Event|string} eventOrTarget - Evento de clique ou ID da página alvo
     */
    back(eventOrTarget) {
        // Se há overlay aberto, fecha primeiro
        if (this.activeOverlays.length > 0) {
            this.closeTopOverlay();
            return;
        }

        // Verifica se é string (target direto)
        let targetId = null;
        if (typeof eventOrTarget === "string") {
            targetId = eventOrTarget;
        } else if (eventOrTarget?.target) {
            // Verifica data-back-target
            const btn = eventOrTarget.target.closest("[data-back]");
            if (btn) {
                const backValue = btn.dataset.back;
                if (backValue && backValue !== "true" && backValue !== "") {
                    targetId = backValue;
                }
            }
        }

        // Se tem target específico, navega direto
        if (targetId) {
            this.go(targetId);
            return;
        }

        // Usa history.back() nativo do navegador
        // Isso garante que funciona corretamente após reload
        // e respeita o histórico real do navegador
        history.back();
    }

    /**
     * Vai para home
     */
    home() {
        this.go(this.config.homePage);
    }

    // =================== OVERLAYS ===================

    /**
     * Abre um drawer
     * @param {string} drawerId - ID do drawer
     */
    openDrawer(drawerId) {
        if (this.drawerManager) {
            this.drawerManager.open(drawerId);
        }
    }

    /**
     * Fecha um drawer
     * @param {string} drawerId - ID do drawer
     */
    closeDrawer(drawerId) {
        if (this.drawerManager) {
            this.drawerManager.close(drawerId);
        }
    }

    /**
     * Abre um bottom sheet
     * @param {string} sheetId - ID do sheet
     */
    openSheet(sheetId) {
        if (this.sheetManager) {
            // If this is a multi-step sheet, reset it before opening
            const sheet = document.getElementById(sheetId);
            if (
                sheet &&
                sheet.hasAttribute &&
                sheet.hasAttribute("data-multistep-sheet")
            ) {
                try {
                    this.multiStepManager?.reset(sheetId);
                } catch (err) {
                    // ignore
                }
            }

            this.sheetManager.open(sheetId);
        }
    }

    /**
     * Fecha um bottom sheet
     * @param {string} sheetId - ID do sheet
     */
    closeSheet(sheetId) {
        if (this.sheetManager) {
            this.sheetManager.close(sheetId);
        }
    }

    /**
     * Abre um modal
     * @param {Object} options - Opções do modal
     * @returns {Promise} - Resolve com o resultado do modal
     */
    async modal(options) {
        if (this.modalManager) {
            return this.modalManager.open(options);
        }
        return null;
    }

    /**
     * Fecha o overlay do topo
     */
    closeTopOverlay() {
        if (this.activeOverlays.length === 0) return;

        const topOverlay = this.activeOverlays[this.activeOverlays.length - 1];
        this._closeOverlay(topOverlay, true);
    }

    /**
     * Registra um overlay ativo
     * @param {string} overlayId - ID do overlay
     */
    _registerOverlay(overlayId) {
        this.activeOverlays.push(overlayId);
        this._showBackdrop();

        // Adiciona ao histórico
        history.pushState(
            { page: this.current, overlays: this.activeOverlays.slice() },
            "",
            `#${this.current}_${overlayId}`
        );

        if (this.config.callbacks.onOverlayOpen) {
            this.config.callbacks.onOverlayOpen(overlayId);
        }

        this._emit("spa:overlay:open", { id: overlayId });
    }

    /**
     * Fecha um overlay
     * @param {string} overlayId - ID do overlay
     * @param {boolean} triggerHistory - Se deve usar history.back()
     */
    _closeOverlay(overlayId, triggerHistory = true) {
        const index = this.activeOverlays.indexOf(overlayId);
        if (index === -1) return;

        // Remove do array
        this.activeOverlays.splice(index, 1);

        // Fecha overlay específico
        if (overlayId.startsWith("drawer-")) {
            this.drawerManager?.close(overlayId.replace("drawer-", ""), false);
        } else if (overlayId.startsWith("sheet-")) {
            this.sheetManager?.close(overlayId.replace("sheet-", ""), false);
        } else if (overlayId.startsWith("modal-")) {
            this.modalManager?.close(overlayId);
        }

        // Gerencia backdrop
        if (this.activeOverlays.length === 0) {
            this._hideBackdrop();
        }

        // Gerencia histórico
        if (triggerHistory) {
            history.back();
        }

        if (this.config.callbacks.onOverlayClose) {
            this.config.callbacks.onOverlayClose(overlayId);
        }

        this._emit("spa:overlay:close", { id: overlayId });
    }

    // =================== UI ===================

    /**
     * Mostra um toast no estilo Bento Design
     * @param {string|object} titleOrOptions - Título ou objeto de opções
     * @param {string} description - Descrição (opcional)
     * @param {object} options - Opções adicionais
     * @returns {string} ID do toast
     */
    toast(titleOrOptions, description = null, options = {}) {
        if (!this.toastContainer) {
            this._createToastContainer();
        }

        // Parse argumentos (compatibilidade com versão antiga)
        let config;
        if (typeof titleOrOptions === "object") {
            config = titleOrOptions;
        } else {
            // Compatibilidade: toast(message, type, duration)
            config = {
                title: titleOrOptions,
                description:
                    typeof description === "string" ? description : null,
                type:
                    typeof description === "string" && options.type
                        ? options.type
                        : description || "info",
                // Se `options` for um objeto, leia `options.duration`,
                // caso contrário `options` pode ser um número (assinatura antiga)
                duration:
                    (typeof options === "object"
                        ? options.duration
                        : options) || 4000,
                ...options,
            };
        }

        const {
            title,
            description: desc,
            type = "info",
            duration = 4000,
            dismissible = true,
            progress = null,
            onClose = null,
        } = config;

        const id = Math.random().toString(36).substring(2, 9);
        const toast = document.createElement("div");

        toast.className = "toast-item";
        toast.dataset.id = id;
        toast.dataset.type = type;
        toast.dataset.state = "closed";
        toast.dataset.dismissible = String(dismissible);
        if (progress !== null) toast.dataset.hasProgress = "true";

        // Ícones SVG modernos
        const icons = {
            success: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>`,
            error: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>`,
            info: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>`,
            warning: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>`,
            loading: `<div class="toast-spinner"></div>`,
            close: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>`,
        };

        toast.innerHTML = `
                <div class="toast-header">
                    <div class="toast-icon">${icons[type] || icons.info}</div>
                    <div class="toast-content">
                        <div class="toast-title">${title}</div>
                        ${desc ? `<div class="toast-desc">${desc}</div>` : ""}
                    </div>
                    <button class="toast-close" aria-label="Fechar">${
                        icons.close
                    }</button>
                </div>
                <div class="toast-progress-bg">
                    <div class="toast-progress-fill" style="width: ${
                        progress || 0
                    }%"></div>
                </div>
            `;

        // Adiciona ao container (appendChild para que o mais novo seja o último no DOM)
        this.toastContainer.appendChild(toast);

        // Atualiza stack
        this._updateToastStack();

        // Limita número de toasts visíveis
        const toasts = this.toastContainer.querySelectorAll(
            ".toast-item:not(.removing)"
        );
        if (toasts.length > this.config.ui.toast.maxVisible) {
            this._removeToast(toasts[0]);
        }

        // Gestos para fechar (habilitar por padrão; permita desativar via options.swipeToClose = false)
        const allowSwipe = options.swipeToClose !== false;
        if (allowSwipe) {
            this._setupToastGestures(toast, id, onClose);
        }

        // Botão fechar
        const closeBtn = toast.querySelector(".toast-close");
        if (closeBtn) {
            closeBtn.onclick = (e) => {
                e.stopPropagation();
                this.dismissToast(id);
            };
        }

        // Anima entrada: abre e atualiza stack **depois** para garantir que o toast
        // mais novo apareça acima imediatamente (evita delay onde fica por baixo)
        requestAnimationFrame(() => {
            toast.dataset.state = "open";
            // Atualiza stack novamente agora que o estado mudou
            this._updateToastStack();
        });

        // Remove após duração (apenas se dismissible e não loading)
        if (dismissible && duration !== Infinity && type !== "loading") {
            setTimeout(() => {
                this.dismissToast(id);
            }, duration);
        }

        return id;
    }

    /**
     * Atualiza um toast existente
     * @param {string} id - ID do toast
     * @param {object} updates - Atualizações
     */
    updateToast(id, updates) {
        const toast = this.toastContainer?.querySelector(`[data-id="${id}"]`);
        if (!toast) return;

        const { title, description, type, progress, dismissible } = updates;

        if (type) {
            toast.dataset.type = type;
            const icons = {
                success: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>`,
                error: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>`,
                info: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>`,
                warning: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>`,
            };
            const iconContainer = toast.querySelector(".toast-icon");
            if (iconContainer)
                iconContainer.innerHTML = icons[type] || icons.info;
        }

        if (title) {
            const titleEl = toast.querySelector(".toast-title");
            if (titleEl) titleEl.textContent = title;
        }

        if (description !== undefined) {
            let descEl = toast.querySelector(".toast-desc");
            if (description && !descEl) {
                descEl = document.createElement("div");
                descEl.className = "toast-desc";
                toast.querySelector(".toast-content").appendChild(descEl);
            }
            if (descEl) descEl.textContent = description;
        }

        if (progress !== undefined) {
            toast.dataset.hasProgress = "true";
            const fill = toast.querySelector(".toast-progress-fill");
            if (fill) fill.style.width = `${progress}%`;
        }

        if (dismissible !== undefined) {
            toast.dataset.dismissible = String(dismissible);
        }
    }

    /**
     * Dismiss (remove) um toast
     * @param {string} id - ID do toast
     */
    dismissToast(id) {
        const toast = this.toastContainer?.querySelector(`[data-id="${id}"]`);
        if (toast) {
            this._removeToast(toast);
        }
    }

    /**
     * Atalhos para tipos específicos de toast
     */
    toastSuccess(title, description, options = {}) {
        return this.toast({
            title,
            description,
            type: "success",
            ...options,
        });
    }

    toastError(title, description, options = {}) {
        return this.toast({
            title,
            description,
            type: "error",
            ...options,
        });
    }

    toastWarning(title, description, options = {}) {
        return this.toast({
            title,
            description,
            type: "warning",
            ...options,
        });
    }

    toastInfo(title, description, options = {}) {
        return this.toast({ title, description, type: "info", ...options });
    }

    toastLoading(title, description, options = {}) {
        return this.toast({
            title,
            description,
            type: "loading",
            duration: Infinity,
            dismissible: false,
            ...options,
        });
    }

    /**
     * Remove toast com animação
     */
    _removeToast(toast) {
        if (!toast.isConnected || toast.classList.contains("removing")) return;

        toast.classList.add("removing");
        toast.classList.remove("show");

        setTimeout(() => {
            toast.remove();
            this._updateToastStack();
        }, 300);
    }

    /**
     * Atualiza stack 3D dos toasts estilo Bento
     */
    _updateToastStack() {
        const toasts = Array.from(
            this.toastContainer.querySelectorAll(".toast-item:not(.removing)")
        );

        const total = toasts.length;
        const maxVisible = this.config.ui.toast.maxVisible;

        // Verifica se existe bottom-nav visível para subir o container
        const bottomNav = document.querySelector(".bottom-nav");
        const isBottomNavVisible =
            bottomNav &&
            getComputedStyle(bottomNav).display !== "none" &&
            !bottomNav.classList.contains("hidden");

        if (isBottomNavVisible) {
            this.toastContainer.style.transform = `translateY(-70px)`;
        } else {
            this.toastContainer.style.transform = "translateY(0)";
        }

        toasts.forEach((toast, index) => {
            if (toast.dataset.state === "closed") return;

            // reverseIndex: 0 para o mais novo, 1 para o anterior...
            const reverseIndex = total - 1 - index;

            if (reverseIndex >= maxVisible) {
                toast.style.opacity = "0";
                toast.style.pointerEvents = "none";
                return;
            }

            // O mais novo (último do array) fica no topo da pilha visual (maior Y e maior Z)
            const offset = index * 14;
            const scale = 1 - reverseIndex * 0.05;
            const isTop = this.config.ui.toast.position.startsWith("top");
            const yPos = isTop ? offset : -offset;

            toast.style.zIndex = 1000 + index;
            toast.style.opacity = "1";

            toast.style.transform = `translateY(${yPos}px) scale(${scale})`;
            toast.style.pointerEvents = index === total - 1 ? "auto" : "none";
        });
    }

    /**
     * Configura gestos de swipe no toast
     */
    _setupToastGestures(toast, id, onClose) {
        let startX = 0;
        let currentX = 0;
        let isDragging = false;

        const handleStart = (e) => {
            // Permite gestos mesmo em toasts marcados como não-dismissible
            isDragging = true;
            startX = e.touches ? e.touches[0].clientX : e.clientX;
            toast.style.transition = "none";
        };

        const handleMove = (e) => {
            if (!isDragging) return;
            e.preventDefault();

            currentX = (e.touches ? e.touches[0].clientX : e.clientX) - startX;
            // Apenas swipe para direita
            if (currentX < 0) currentX = 0;

            const opacity = Math.max(0.3, 1 - currentX / 200);

            toast.style.transform = `translateX(${currentX}px)`;
            toast.style.opacity = opacity;
        };

        const handleEnd = () => {
            if (!isDragging) return;
            isDragging = false;

            toast.style.transition = "";

            // Remove se arrastou mais de 100px
            if (currentX > 100) {
                toast.style.transform = `translateX(100%)`;
                toast.style.opacity = "0";
                setTimeout(() => {
                    if (onClose) onClose();
                    this._removeToast(toast);
                }, 300);
            } else {
                // Volta ao stack position
                toast.style.transform = "";
                toast.style.opacity = "";
                this._updateToastStack();
            }

            currentX = 0;
        };

        toast.addEventListener("mousedown", handleStart);
        window.addEventListener("mousemove", handleMove);
        window.addEventListener("mouseup", handleEnd);

        toast.addEventListener("touchstart", handleStart, {
            passive: false,
        });
        toast.addEventListener("touchmove", handleMove, { passive: false });
        toast.addEventListener("touchend", handleEnd);
    }

    /**
     * Mostra loading overlay
     */
    showLoading() {
        if (this.loadingOverlay) {
            this.loadingOverlay.classList.remove("hidden");
        }
    }

    /**
     * Exibe mensagem overlay semi-transparente no centro
     * @param {string} message - Mensagem
     * @param {string} type - Tipo: success, error, warning, info
     * @param {number} duration - Duração em ms
     */
    message(message, type = "info", duration = 2000) {
        const overlay = document.createElement("div");
        overlay.className = "message-overlay";

        const icons = {
            success: "✓",
            error: "✕",
            warning: "⚠",
            info: "ℹ",
        };

        const colors = {
            success: "#10b981",
            error: "#ef4444",
            warning: "#f59e0b",
            info: "#3b82f6",
        };

        overlay.innerHTML = `
                <div class="message-box message-${type}">
                    <div class="message-icon" style="color: ${colors[type]}">${
            icons[type] || icons.info
        }</div>
                    <div class="message-text">${message}</div>
                </div>
            `;

        document.body.appendChild(overlay);

        requestAnimationFrame(() => {
            overlay.classList.add("active");
        });

        setTimeout(() => {
            overlay.classList.remove("active");
            setTimeout(() => overlay.remove(), 300);
        }, duration);

        return overlay;
    }

    /**
     * Esconde loading overlay
     */
    hideLoading() {
        if (this.loadingOverlay) {
            this.loadingOverlay.classList.add("hidden");
        }
    }

    // =================== PREFERÊNCIAS & CONFIGURAÇÕES ===================

    /**
     * Alterna entre tema claro e escuro
     */
    toggleTheme() {
        const currentTheme = document.documentElement.dataset.theme || "light";
        const newTheme = currentTheme === "light" ? "dark" : "light";

        this.setTheme(newTheme);
        return newTheme;
    }

    /**
     * Define o tema da aplicação
     * @param {string} theme - 'light' ou 'dark'
     */
    setTheme(theme) {
        document.documentElement.dataset.theme = theme;
        this.storage.set("pref_theme", theme);

        // Atualiza ícones se necessário
        if (window.lucide) {
            window.lucide.createIcons();
        }

        // Atualiza toggles na UI
        const toggles = document.querySelectorAll(
            "#theme-toggle-sheet, .theme-toggle"
        );
        toggles.forEach((t) => (t.checked = theme === "dark"));

        this._log(2, `🌓 Tema alterado para: ${theme}`);
    }

    /**
     * Altera o tipo de animação de transição
     * @param {string} type - Tipo de animação
     */
    setAnimation(type) {
        this.config.animation.type = type;
        document.documentElement.dataset.animation = type;
        this.storage.set("pref_animation", type);

        // Atualiza selects na UI
        const selects = document.querySelectorAll(".animation-select");
        selects.forEach((s) => (s.value = type));

        this.toastInfo(
            "Animação Alterada",
            `Estilo "${type}" aplicado com sucesso.`
        );
        this._log(2, `🎬 Animação alterada para: ${type}`);
    }

    /**
     * Aplica as preferências salvas no storage
     */
    _applyPreferences() {
        // 1. Tema
        const savedTheme = this.storage.get("pref_theme");
        if (savedTheme) {
            this.setTheme(savedTheme);
        } else if (
            this.config.ui.autoTheme &&
            window.matchMedia &&
            window.matchMedia("(prefers-color-scheme: dark)").matches
        ) {
            this.setTheme("dark");
        }

        // 2. Animação
        const savedAnim = this.storage.get("pref_animation");
        if (savedAnim) {
            this.config.animation.type = savedAnim;
            document.documentElement.dataset.animation = savedAnim;

            // Atualiza selects na UI sem disparar toast
            const selects = document.querySelectorAll(".animation-select");
            selects.forEach((s) => (s.value = savedAnim));
        }

        this._log(2, "⚙️ Preferências aplicadas");
    }

    /**
     * Atalho para abrir modal (compatibilidade)
     */
    openModal(options) {
        return this.modal(options);
    }

    /**
     * Realiza logout da aplicação
     */
    async logout() {
        try {
            this.showLoading();

            // Limpa dados locais sensíveis se necessário
            // this.storage.clear();

            // Se houver uma rota de logout no Laravel
            const logoutForm = document.createElement("form");
            logoutForm.method = "POST";
            logoutForm.action = "/logout";

            const csrfToken = document.querySelector(
                'meta[name="csrf-token"]'
            )?.content;
            if (csrfToken) {
                const input = document.createElement("input");
                input.type = "hidden";
                input.name = "_token";
                input.value = csrfToken;
                logoutForm.appendChild(input);
            }

            document.body.appendChild(logoutForm);
            logoutForm.submit();
        } catch (error) {
            this.hideLoading();
            this.toastError(
                "Erro ao sair",
                "Não foi possível realizar o logout."
            );
        }
    }

    // =================== STORAGE ===================

    /**
     * Acesso ao IndexedDB ORM
     */
    get db() {
        return this._db;
    }

    set db(value) {
        this._db = value;
    }

    /**
     * Acesso ao localStorage wrapper
     */
    get storage() {
        return this._storage;
    }

    set storage(value) {
        this._storage = value;
    }

    /**
     * Acesso ao sistema de queue
     */
    get queue() {
        return this._queue;
    }

    set queue(value) {
        this._queue = value;
    }

    // =================== INTERNAL METHODS ===================

    /**
     * Registra todas as páginas do DOM
     */
    _registerPages() {
        document.querySelectorAll(".page").forEach((page) => {
            // Extrai ID sem prefixo
            let pageId = page.id;
            if (pageId.startsWith(this.config.pagePrefix)) {
                pageId = pageId.substring(this.config.pagePrefix.length);
            }

            // Registra no Map
            this.pages.set(pageId, page);

            // Registra hierarquia
            const level = page.dataset.level || "primary";
            const parent = page.dataset.parent || this.config.homePage;
            const keepHistory = page.dataset.keepHistory === "true";

            this.pageHierarchy.set(pageId, {
                level,
                parent,
                keepHistory,
                element: page,
            });

            // Se é a página ativa inicial
            if (page.classList.contains("active")) {
                this.current = pageId;
            }
        });
    }

    /**
     * Cria elementos necessários (backdrop, toast container, etc)
     */
    _createElements() {
        // Backdrop
        this.backdrop = document.getElementById("backdrop");
        if (!this.backdrop) {
            this.backdrop = document.createElement("div");
            this.backdrop.id = "backdrop";
            document.body.appendChild(this.backdrop);
        }

        // Toast container
        this._createToastContainer();

        // Loading overlay
        this.loadingOverlay = document.getElementById("loading-overlay");
    }

    /**
     * Cria container de toasts
     */
    _createToastContainer() {
        this.toastContainer = document.getElementById("toast-container");
        if (!this.toastContainer) {
            this.toastContainer = document.createElement("div");
            this.toastContainer.id = "toast-container";
            this.toastContainer.className = `toast-container toast-${this.config.ui.toast.position}`;
            document.body.appendChild(this.toastContainer);
        }
    }

    /**
     * Inicializa managers de UI
     */
    _initManagers() {
        this.drawerManager = new DrawerManager(this);
        this.sheetManager = new SheetManager(this);
        this.modalManager = new ModalManager(this);
        this.multiStepManager = new MultiStepSheetManager(this);

        // Registra drawers existentes
        document.querySelectorAll(".drawer").forEach((drawer) => {
            this.drawerManager.register(drawer.id);
        });

        // Registra sheets existentes
        document.querySelectorAll(".bottom-sheet").forEach((sheet) => {
            this.sheetManager.register(sheet.id);
        });

        // Registra multi-step sheets (se houver)
        document.querySelectorAll("[data-multistep-sheet]").forEach((sheet) => {
            // garante registro no SheetManager para abertura/fechamento
            if (this.sheetManager) this.sheetManager.register(sheet.id);
            if (this.multiStepManager) this.multiStepManager.register(sheet.id);
        });

        // Inicializa os ícones Lucide após o modal ser injetado no DOM
        requestAnimationFrame(() => {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    }

    /**
     * Configura event listeners
     */
    _setupEventListeners() {
        // Histórico
        window.addEventListener("popstate", this._handlePopState);

        // Cliques delegados
        document.addEventListener("click", this._handleClick);

        // Backdrop
        if (this.backdrop && this.config.ui.backdrop.closeOnClick) {
            this.backdrop.addEventListener("click", () => {
                if (this.activeOverlays.length > 0) {
                    this.closeTopOverlay();
                }
            });
        }

        // Atalhos de teclado
        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape" && this.activeOverlays.length > 0) {
                this.closeTopOverlay();
            }
            if (e.altKey && e.key === "ArrowLeft") {
                this.back();
            }
        });

        // Conexão online/offline
        window.addEventListener("online", this._handleOnline);
        window.addEventListener("offline", this._handleOffline);

        // Double tap to exit
        if (this.config.doubleTapExit) {
            window.addEventListener(
                "popstate",
                this._handleDoubleTapExit.bind(this)
            );
        }
    }

    /**
     * Handler para cliques delegados
     */
    _handleClick(e) {
        const target = e.target;

        // data-go
        const goBtn = target.closest("[data-go]");
        if (goBtn) {
            e.preventDefault();

            // Se o botão estiver dentro de um overlay (drawer/sheet/modal),
            // feche o overlay primeiro sem acionar histórico para evitar
            // que o fechamento dispare um `history.back()` indesejado.
            const drawerEl = goBtn.closest(".drawer");
            const sheetEl = goBtn.closest(".bottom-sheet");
            const modalEl = goBtn.closest(".modal");

            if (
                drawerEl &&
                this.drawerManager &&
                typeof this.drawerManager.close === "function"
            ) {
                try {
                    this.drawerManager.close(drawerEl.id, false);
                } catch (err) {
                    // fallback: use internal overlay closer
                    this._closeOverlay(`drawer-${drawerEl.id}`, false);
                }
            } else if (
                sheetEl &&
                this.sheetManager &&
                typeof this.sheetManager.close === "function"
            ) {
                try {
                    this.sheetManager.close(sheetEl.id, false);
                } catch (err) {
                    this._closeOverlay(`sheet-${sheetEl.id}`, false);
                }
            } else if (
                modalEl &&
                this.modalManager &&
                typeof this.modalManager.close === "function"
            ) {
                try {
                    this.modalManager.close(modalEl.id);
                } catch (err) {
                    this._closeOverlay(`modal-${modalEl.id}`, false);
                }
            }

            this.go(goBtn.dataset.go);
            return;
        }

        // data-back
        const backBtn = target.closest("[data-back]");
        if (backBtn) {
            e.preventDefault();
            this.back(e);
            return;
        }

        // data-drawer
        const drawerBtn = target.closest("[data-drawer]");
        if (drawerBtn) {
            e.preventDefault();
            this.openDrawer(drawerBtn.dataset.drawer);
            return;
        }

        // data-sheet
        const sheetBtn = target.closest("[data-sheet]");
        if (sheetBtn) {
            e.preventDefault();
            this.openSheet(sheetBtn.dataset.sheet);
            return;
        }

        // data-modal
        const modalBtn = target.closest("[data-modal]");
        if (modalBtn) {
            e.preventDefault();
            const modalId = modalBtn.dataset.modal;
            const modalEl = document.getElementById(modalId);
            if (modalEl) {
                // Modal de template
                this.modal({
                    template: modalId,
                });
            }
            return;
        }

        // data-close-overlay
        const closeBtn = target.closest("[data-close-overlay]");
        if (closeBtn) {
            e.preventDefault();
            this.closeTopOverlay();
            return;
        }
    }

    /**
     * Handler do popstate (botão voltar)
     */
    _handlePopState(e) {
        this._log(2, "⬅️ Popstate detectado");

        // Determina a página alvo
        let targetPageId;
        let targetOverlays = [];

        if (e.state && e.state.page) {
            // Tem state no histórico
            targetPageId = e.state.page;
            targetOverlays = e.state.overlays || [];
        } else {
            // Não tem state - pega do hash da URL
            const hash = window.location.hash.slice(1);
            targetPageId =
                hash && this.pages.has(hash) ? hash : this.config.homePage;
        }

        this._log(2, `⬅️ Indo para: ${targetPageId}`);

        // Gerencia overlays
        if (targetOverlays.length < this.activeOverlays.length) {
            const overlayToClose =
                this.activeOverlays[this.activeOverlays.length - 1];
            this._closeOverlay(overlayToClose, false);
            return;
        }

        // Navegação de página
        if (targetPageId === this.current) return;

        const currentEl = this.pages.get(this.current);
        const prevEl = this.pages.get(targetPageId);

        if (!prevEl) return;

        // Prepare bindings: hide elements immediately
        try {
            this._prepareBindingsForNavigation(targetPageId);
        } catch (err) {
            // ignore
        }

        // Define animação de volta e aplica velocidade reduzida para back
        document.documentElement.dataset.animation = this.config.animation.type;

        // apply reduced back speed (temporary)
        const backSpeed =
            (this.config.animation && this.config.animation.backSpeed) ||
            Math.max(this.config.animation.speed / 3, 0.06);
        // guarda valor anterior para restaurar depois
        const prevSpeed =
            getComputedStyle(document.documentElement).getPropertyValue(
                "--spa-animation-speed"
            ) || this.config.animation.speed + "s";
        document.documentElement.style.setProperty(
            "--spa-animation-speed",
            backSpeed + "s"
        );

        this._emitPageEvent(this.current, "page:leave");
        this._clearPageClasses(currentEl, prevEl);

        // Transição BACKWARD
        currentEl.classList.remove("active");
        prevEl.classList.add("revealing");

        this.previousPage = this.current;
        this.current = targetPageId;

        setTimeout(() => {
            prevEl.classList.remove("revealing");
            prevEl.classList.add("active");
            this._emitPageEvent(targetPageId, "page:enter");
            // restaura velocidade padrão
            document.documentElement.style.setProperty(
                "--spa-animation-speed",
                prevSpeed.trim() || this.config.animation.speed + "s"
            );
        }, backSpeed * 1000);
    }

    /**
     * Handler do double tap to exit
     */
    _handleDoubleTapExit(e) {
        if (!this.config.doubleTapExit) return;

        const state = e.state;

        // Detecta se está na rota de exit
        if (state && state.type === "exit") {
            const now = Date.now();

            if (now - this.lastBackPress < this.config.doubleTapTimeout) {
                // Fecha o app (ou apenas não faz nada em browser)
                window.close();
            } else {
                this.lastBackPress = now;
                this.toast("Pressione voltar novamente para sair", "info");

                // Restaura estado atual
                history.pushState(
                    { page: this.current, overlays: [], type: "home" },
                    "",
                    `#${this.current}`
                );
            }
        }
    }

    /**
     * Handler para quando fica online
     */
    _handleOnline() {
        this._online = true;
        this.toast("🌐 Conexão restaurada", "success");
        this._emit("spa:online");

        // Processa queue pendente
        if (this.queue) {
            this.queue.processAll();
        }
    }

    /**
     * Handler para quando fica offline
     */
    _handleOffline() {
        this._online = false;
        this.toast("📴 Você está offline", "warning");
        this._emit("spa:offline");
    }

    /**
     * Verifica se está online
     */
    get isOnline() {
        return this._online;
    }

    /**
     * Configura histórico inicial
     */
    _setupInitialHistory() {
        // Verifica hash atual
        const hash = window.location.hash.substring(1);
        if (hash && this.pages.has(hash)) {
            this.current = hash;
            // Ativa página correta
            this.pages.forEach((page, id) => {
                page.classList.toggle("active", id === hash);
            });
        }

        // Verifica se na hash existe alguma referencia a _modal, _sheet ou _drawer e caso tenha redireciona para a página correta
        const hasOpenedOverlay = ["_modal", "_sheet", "_drawer"].some(
            (suffix) => hash.includes(suffix)
        );
        if (hasOpenedOverlay) {
            const basePageId = hash.split(/_(modal|sheet|drawer)/)[0];
            if (this.pages.has(basePageId)) {
                this.current = basePageId;
                // Ativa página correta
                this.pages.forEach((page, id) => {
                    page.classList.toggle("active", id === basePageId);
                });
            }
        }

        // Configura estado inicial APENAS se não houver state
        // Isso preserva o histórico após reload
        if (!history.state || !history.state.page) {
            const pageHierarchy = this.pageHierarchy.get(this.current);
            history.replaceState(
                {
                    page: this.current,
                    overlays: [],
                    type: pageHierarchy?.level || "home",
                },
                "",
                `#${this.current}`
            );
        }
    }

    /**
     * Inicializa storage (IndexedDB e localStorage)
     */
    async _initStorage() {
        try {
            // Inicializa IndexedDB ORM
            this._db = new IndexedDBORM(
                this.config.db.name,
                this.config.db.version
            );
            await this._db.init();
            this._log(2, "💾 IndexedDB inicializado");

            // Inicializa localStorage wrapper
            this._storage = new LocalStorageORM(this.config.storage.prefix);
            this._log(2, "💾 localStorage inicializado");

            // Inicializa queue
            this._queue = new JobQueue(this._db);
            await this._queue.init();
            this._log(2, "📋 Job Queue inicializada");
        } catch (error) {
            console.error("Erro ao inicializar storage:", error);
            this._emit("spa:error", { error });
        }
    }

    /**
     * Inicializa PWA
     */
    _initPWA() {
        if (this.config.pwa && this.config.pwa.enabled) {
            this.pwa = new PWAInstaller(this, this.config.pwa);
        }

        // Passe a instância do SPA para o NotificationManager
        this.notifications = new NotificationManager(this);
        // Inicializa manager (cria store de notifications se aplicável)
        if (typeof this.notifications.init === "function") {
            this.notifications.init();
        }
    }

    /**
     * Limpa classes de transição das páginas
     */
    _clearPageClasses(el1, el2) {
        const classes = ["active", "prev", "revealing"];
        if (el1) el1.classList.remove(...classes);
        if (el2) el2.classList.remove(...classes);
    }

    /**
     * Mostra backdrop
     */
    _showBackdrop() {
        if (this.backdrop) {
            this.backdrop.classList.add("show");
        }
    }

    /**
     * Esconde backdrop
     */
    _hideBackdrop() {
        if (this.backdrop) {
            this.backdrop.classList.remove("show");
        }
    }

    /**
     * Emite evento customizado
     */
    _emit(eventName, detail = {}) {
        const event = new CustomEvent(eventName, { detail });
        document.dispatchEvent(event);
    }

    /**
     * Emite evento de página
     */
    _emitPageEvent(pageId, eventName) {
        const pageEl = this.pages.get(pageId);
        if (pageEl) {
            const event = new CustomEvent(eventName, {
                detail: { pageId, element: pageEl },
            });
            pageEl.dispatchEvent(event);
            document.dispatchEvent(event);
        }

        // Atualiza bindings após evento enter
        if (eventName === "page:enter") {
            this._updatePageBindings();
        }
    }

    /**
     * Retorna o valor numérico do nível da página para lógica de histórico
     * @param {string} level - Nome do nível (home, primary, secondary, tertiary)
     * @returns {number} Valor numérico
     */
    _getLevelValue(level) {
        const levels = {
            home: 0,
            primary: 1,
            secondary: 2,
            tertiary: 3,
        };
        return levels[level] !== undefined ? levels[level] : 1;
    }

    // =================== ELEMENTO-PÁGINA BINDING ===================

    /**
     * Prepara bindings para navegação (feedback instantâneo)
     * Esconde elementos que não devem estar na página de destino
     * @param {string} targetPageId - ID da página de destino
     */
    _prepareBindingsForNavigation(targetPageId) {
        // 1. Elementos com data-show-on que NÃO incluem a página de destino
        document.querySelectorAll("[data-show-on]").forEach((el) => {
            const pages = el.dataset.showOn.split(",").map((p) => p.trim());
            if (!pages.includes(targetPageId)) {
                el.style.opacity = "0";
                el.style.pointerEvents = "none";
                el.setAttribute("disabled", "true");
            }
        });

        // 2. Elementos com data-hide-on que INCLUEM a página de destino
        document.querySelectorAll("[data-hide-on]").forEach((el) => {
            const pages = el.dataset.hideOn.split(",").map((p) => p.trim());
            if (pages.includes(targetPageId)) {
                el.style.opacity = "0";
                el.style.pointerEvents = "none";
                el.setAttribute("disabled", "true");
            }
        });
    }

    /**
     * Atualiza elementos vinculados com a página atual
     * Elementos com data-show-on, data-hide-on e data-active-on
     */
    _updatePageBindings() {
        const currentPage = this.current;

        // 1. Elementos com data-show-on="page1,page2" - mostra apenas nessas páginas
        document.querySelectorAll("[data-show-on]").forEach((el) => {
            const pages = el.dataset.showOn.split(",").map((p) => p.trim());
            if (pages.includes(currentPage)) {
                el.style.display = "";
                el.style.opacity = "";
                el.style.pointerEvents = "";
                el.removeAttribute("disabled");
                el.classList.remove("hidden");
            } else {
                el.style.display = "none";
                el.classList.add("hidden");
            }
        });

        // 2. Elementos com data-hide-on="page1,page2" - esconde nessas páginas
        document.querySelectorAll("[data-hide-on]").forEach((el) => {
            const pages = el.dataset.hideOn.split(",").map((p) => p.trim());
            if (pages.includes(currentPage)) {
                el.style.display = "none";
                el.classList.add("hidden");
            } else {
                el.style.display = "";
                el.style.opacity = "";
                el.style.pointerEvents = "";
                el.removeAttribute("disabled");
                el.classList.remove("hidden");
            }
        });

        // 3. Elementos com data-active-on="page1" - adiciona classe 'active' quando na página
        document.querySelectorAll("[data-active-on]").forEach((el) => {
            const pages = el.dataset.activeOn.split(",").map((p) => p.trim());
            if (pages.includes(currentPage)) {
                el.classList.add("active");
            } else {
                el.classList.remove("active");
            }
        });

        // 4. Botões com data-go - marca como ativo se for a página atual
        document.querySelectorAll("[data-go]").forEach((el) => {
            if (el.dataset.go === currentPage) {
                el.classList.add("active");
            } else {
                el.classList.remove("active");
            }
        });
    }

    /**
     * Log com nível de debug
     */
    _log(level, message, data = null) {
        if (!this.config.debug.enabled) return;
        if (level > this.config.debug.level) return;

        if (data) {
            console.log(message, data);
        } else {
            console.log(message);
        }
    }

    /**
     * Deep merge de objetos
     */
    _deepMerge(target, source) {
        const output = Object.assign({}, target);

        if (this._isObject(target) && this._isObject(source)) {
            Object.keys(source).forEach((key) => {
                if (this._isObject(source[key])) {
                    if (!(key in target)) {
                        Object.assign(output, { [key]: source[key] });
                    } else {
                        output[key] = this._deepMerge(target[key], source[key]);
                    }
                } else {
                    Object.assign(output, { [key]: source[key] });
                }
            });
        }

        return output;
    }

    /**
     * Verifica se é objeto
     */
    _isObject(item) {
        return item && typeof item === "object" && !Array.isArray(item);
    }

    /**
     * Destrói a instância
     */
    destroy() {
        window.removeEventListener("popstate", this._handlePopState);
        document.removeEventListener("click", this._handleClick);
        window.removeEventListener("online", this._handleOnline);
        window.removeEventListener("offline", this._handleOffline);

        this.pages.clear();
        this.pageHierarchy.clear();
        this.activeOverlays = [];

        this._log(1, "🗑️ SPA Framework destruído");
    }
}

// Exporta SPA
export { SPA };
export default SPA;

// Compatibilidade com uso global (opcional)
if (typeof window !== "undefined") {
    window.SPA = SPA;
}
