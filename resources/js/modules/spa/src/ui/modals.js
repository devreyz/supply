/**
 * 🎨 SPA Framework - UI Components
 * Gerenciadores de Drawer, Sheet e Modal
 */

// =================== DRAWER MANAGER ===================

/**
 * Gerenciador de Drawers (menu lateral)
 */
export class DrawerManager {
    constructor(spa) {
        this.spa = spa;
        this.drawers = new Map();
        this.isDragging = false;
        this.startX = 0;
        this.currentDrawer = null;
    }

    /**
     * Registra um drawer
     */
    register(drawerId) {
        const drawer = document.getElementById(drawerId);
        if (!drawer) return;

        this.drawers.set(drawerId, {
            element: drawer,
            isOpen: false,
        });

        // Configura gestos de arrasto
        if (this.spa.config.gestures.enabled) {
            const handle = drawer.querySelector(".drawer-handle") || drawer;

            handle.addEventListener(
                "touchstart",
                (e) => this._onDragStart(e, drawerId),
                { passive: true }
            );
            handle.addEventListener("touchmove", (e) => this._onDragMove(e), {
                passive: false,
            });
            handle.addEventListener("touchend", (e) => this._onDragEnd(e));
        }
    }

    /**
     * Abre um drawer
     */
    open(drawerId) {
        const drawerData = this.drawers.get(drawerId);
        if (!drawerData || drawerData.isOpen) return;

        drawerData.element.classList.add("open");
        drawerData.isOpen = true;

        // Registra como overlay ativo
        this.spa._registerOverlay(`drawer-${drawerId}`);
    }

    /**
     * Fecha um drawer
     */
    close(drawerId, triggerHistory = true) {
        const drawerData = this.drawers.get(drawerId);
        if (!drawerData || !drawerData.isOpen) return;

        // Se pediram para gerenciar histórico, delegue para o SPA
        // SPA._closeOverlay chamará este método novamente com triggerHistory=false
        // evitando duplicação de lógica.
        if (triggerHistory) {
            this.spa._closeOverlay(`drawer-${drawerId}`, true);
            return;
        }

        // Fecha visualmente e remove estado local
        drawerData.element.classList.remove("open");
        drawerData.element.style.transform = "";
        drawerData.isOpen = false;

        // Remove do array de overlays ativos e esconde backdrop se não houver mais
        const overlayId = `drawer-${drawerId}`;
        const index = this.spa.activeOverlays.indexOf(overlayId);
        if (index > -1) {
            this.spa.activeOverlays.splice(index, 1);
        }
        if (this.spa.activeOverlays.length === 0) {
            this.spa._hideBackdrop();
        }
    }

    _onDragStart(e, drawerId) {
        const drawerData = this.drawers.get(drawerId);
        if (!drawerData?.isOpen) return;

        this.isDragging = true;
        this.currentDrawer = drawerId;
        this.startX = e.touches[0].clientX;
        drawerData.element.classList.add("is-dragging");
    }

    _onDragMove(e) {
        if (!this.isDragging || !this.currentDrawer) return;

        const drawerData = this.drawers.get(this.currentDrawer);
        if (!drawerData) return;

        const currentX = e.touches[0].clientX;
        const diff = this.startX - currentX;

        if (diff > 0) {
            e.preventDefault();
            drawerData.element.style.transform = `translateX(-${diff}px)`;
        }
    }

    _onDragEnd(e) {
        if (!this.isDragging || !this.currentDrawer) return;

        const drawerData = this.drawers.get(this.currentDrawer);
        if (!drawerData) return;

        drawerData.element.classList.remove("is-dragging");

        const currentX = e.changedTouches[0].clientX;
        const diff = this.startX - currentX;

        if (diff > this.spa.config.gestures.swipeThreshold) {
            this.spa.closeTopOverlay();
        } else {
            drawerData.element.style.transform = "";
        }

        this.isDragging = false;
        this.currentDrawer = null;
    }
}

// =================== SHEET MANAGER ===================

/**
 * Gerenciador de Bottom Sheets
 */
export class SheetManager {
    constructor(spa) {
        this.spa = spa;
        this.sheets = new Map();
        this.isDragging = false;
        this.startY = 0;
        this.currentSheet = null;
    }

    /**
     * Registra um bottom sheet
     */
    register(sheetId) {
        const sheet = document.getElementById(sheetId);
        if (!sheet) return;

        this.sheets.set(sheetId, {
            element: sheet,
            isOpen: false,
        });

        // Configura gestos de arrasto (touch + mouse)
        if (this.spa.config.gestures.enabled) {
            const handle =
                sheet.querySelector(".grabber-handle") ||
                sheet.querySelector(".sheet-header") ||
                sheet;

            // Touch start on handle
            handle.addEventListener(
                "touchstart",
                (e) => this._onDragStart(e, sheetId),
                { passive: false }
            );

            // Mouse support (desktop)
            handle.addEventListener("mousedown", (e) =>
                this._onDragStart(e, sheetId)
            );

            // Listen on window for move/end so drag works even if pointer leaves handle
            window.addEventListener("touchmove", (e) => this._onDragMove(e), {
                passive: false,
            });
            window.addEventListener("touchend", (e) => this._onDragEnd(e));

            window.addEventListener("mousemove", (e) => this._onDragMove(e));
            window.addEventListener("mouseup", (e) => this._onDragEnd(e));
        }
    }

    /**
     * Abre um bottom sheet
     */
    open(sheetId) {
        const sheetData = this.sheets.get(sheetId);
        if (!sheetData || sheetData.isOpen) return;

        sheetData.element.classList.add("open");
        sheetData.isOpen = true;

        // Registra como overlay ativo
        this.spa._registerOverlay(`sheet-${sheetId}`);
    }

    /**
     * Fecha um bottom sheet
     */
    close(sheetId, triggerHistory = true) {
        const sheetData = this.sheets.get(sheetId);
        if (!sheetData || !sheetData.isOpen) return;

        if (triggerHistory) {
            this.spa._closeOverlay(`sheet-${sheetId}`, true);
            return;
        }

        sheetData.element.classList.remove("open");
        sheetData.element.style.transform = "";
        sheetData.isOpen = false;

        const overlayId = `sheet-${sheetId}`;
        const index = this.spa.activeOverlays.indexOf(overlayId);
        if (index > -1) this.spa.activeOverlays.splice(index, 1);
        if (this.spa.activeOverlays.length === 0) this.spa._hideBackdrop();
    }

    _onDragStart(e, sheetId) {
        const sheetData = this.sheets.get(sheetId);
        if (!sheetData?.isOpen) return;

        this.isDragging = true;
        this.currentSheet = sheetId;
        // Suporta touch e mouse
        this.startY = e.touches ? e.touches[0].clientY : e.clientY;
        sheetData.element.classList.add("is-dragging");
    }

    _onDragMove(e) {
        if (!this.isDragging || !this.currentSheet) return;

        const sheetData = this.sheets.get(this.currentSheet);
        if (!sheetData) return;

        const currentY = e.touches ? e.touches[0].clientY : e.clientY;
        const diff = currentY - this.startY;

        if (diff > 0) {
            e.preventDefault();
            sheetData.element.style.transform = `translateY(${diff}px)`;
        }
    }

    _onDragEnd(e) {
        if (!this.isDragging || !this.currentSheet) return;

        const sheetData = this.sheets.get(this.currentSheet);
        if (!sheetData) return;

        sheetData.element.classList.remove("is-dragging");

        const currentY = e.changedTouches
            ? e.changedTouches[0].clientY
            : e.clientY;
        const diff = currentY - this.startY;
        const sheetHeight = sheetData.element.offsetHeight;
        const threshold = sheetHeight * 0.3;

        if (diff > threshold) {
            this.spa.closeTopOverlay();
        } else {
            sheetData.element.style.transform = "";
        }

        this.isDragging = false;
        this.currentSheet = null;
    }
}

// =================== MODAL MANAGER ===================

/**
 * Gerenciador de Modais
 */
export class ModalManager {
    constructor(spa) {
        this.spa = spa;
        this.modals = new Map();
        this.counter = 0;
    }

    /**
     * Abre um modal
     * @param {Object} options - Opções do modal
     * @returns {Promise} - Resolve com o resultado
     */
    open(options) {
        return new Promise((resolve) => {
            const modalId = `modal-${++this.counter}`;
            const dismissible = options.dismissible !== false; // Default true

            // Cria estrutura do modal
            const overlay = document.createElement("div");
            overlay.className = "modal-overlay";
            overlay.id = modalId;
            overlay.dataset.dismissible = String(dismissible);

            const dialog = document.createElement("div");
            dialog.className = `modal-dialog ${options.class || ""}`;

            // Aplica largura customizada
            if (options.width) {
                dialog.style.maxWidth = options.width;
            }

            // Modal de template
            if (options.template) {
                const template = document.getElementById(options.template);
                if (template) {
                    dialog.innerHTML = template.innerHTML;
                }
            } else if (options.html) {
                // Modal com HTML customizado
                let content = "";

                // Título
                if (options.title) {
                    content += `<h3 class="modal-title">${options.title}</h3>`;
                }

                // HTML Customizado
                content += options.html;

                // Botões customizados
                if (options.customButtons && options.customButtons.length > 0) {
                    content += '<div class="modal-actions">';
                    options.customButtons.forEach((btn) => {
                        content += `<button class="${btn.class}" data-action="${btn.value}">${btn.text}</button>`;
                    });
                    content += "</div>";
                }

                dialog.innerHTML = content;
            } else {
                // Modal dinâmico padrão
                let content = "";

                // Ícone
                if (options.icon) {
                    content += `<div class="modal-icon">${options.icon}</div>`;
                } else if (options.type === "confirm") {
                    content += `<div class="modal-icon modal-icon-warning">⚠️</div>`;
                } else if (options.type === "success") {
                    content += `<div class="modal-icon modal-icon-success">✓</div>`;
                } else if (options.type === "error") {
                    content += `<div class="modal-icon modal-icon-error">✕</div>`;
                }

                // Título
                if (options.title) {
                    content += `<h3 class="modal-title">${options.title}</h3>`;
                }

                // Mensagem
                if (options.message) {
                    content += `<p class="modal-message">${options.message}</p>`;
                }

                // Input (para modais de prompt)
                if (options.type === "prompt") {
                    content += `<input type="${options.inputType || "text"}" 
                                          class="modal-input input-field" 
                                          placeholder="${
                                              options.placeholder || ""
                                          }"
                                          value="${
                                              options.defaultValue || ""
                                          }">`;
                }

                // Botões
                content += '<div class="modal-actions">';

                if (options.type === "confirm" || options.type === "prompt") {
                    content += `<button class="btn btn-outline modal-cancel" data-action="cancel">
                                      ${options.cancelText || "Cancelar"}
                                    </button>`;
                }

                content += `<button class="btn btn-primary modal-confirm" data-action="confirm">
                                  ${options.confirmText || "OK"}
                                </button>`;

                content += "</div>";

                dialog.innerHTML = content;
            }

            overlay.appendChild(dialog);
            document.body.appendChild(overlay);

            // Registra modal
            this.modals.set(modalId, {
                element: overlay,
                resolve,
                dismissible,
            });

            // Inicializa os ícones Lucide após o modal ser injetado no DOM
            requestAnimationFrame(() => {
                if (window.lucide) {
                    window.lucide.createIcons();
                }
            });

            // Anima entrada
            requestAnimationFrame(() => {
                overlay.classList.add("show");
            });

            // Registra como overlay ativo
            this.spa._registerOverlay(modalId);

            // Event listeners
            overlay.addEventListener("click", (e) => {
                const action = e.target.dataset.action;

                if (action) {
                    // Se for custom modal, retorna o valor do botão
                    if (options.customButtons) {
                        this._resolveAndClose(modalId, action);
                    } else if (action === "confirm") {
                        let result = true;

                        // Pega valor do input se for prompt
                        if (options.type === "prompt") {
                            const input = overlay.querySelector(".modal-input");
                            result = input ? input.value : "";
                        }

                        this._resolveAndClose(modalId, result);
                    } else if (action === "cancel") {
                        this._resolveAndClose(modalId, false);
                    }
                } else if (
                    e.target === overlay &&
                    dismissible &&
                    options.closeOnBackdrop !== false
                ) {
                    this._resolveAndClose(modalId, null);
                }
            });

            // Focus no input se for prompt
            if (options.type === "prompt") {
                setTimeout(() => {
                    const input = overlay.querySelector(".modal-input");
                    if (input) input.focus();
                }, 300);
            }
        });
    }

    /**
     * Fecha um modal
     */
    close(modalId) {
        const modalData = this.modals.get(modalId);
        if (!modalData) return;

        modalData.element.classList.remove("show");

        setTimeout(() => {
            modalData.element.remove();
            this.modals.delete(modalId);
        }, 300);
    }

    /**
     * Resolve promise e fecha modal
     */
    _resolveAndClose(modalId, result) {
        const modalData = this.modals.get(modalId);
        if (!modalData) return;

        modalData.resolve(result);

        // Use SPA handler to close overlay and keep history in sync
        // This will remove the overlay from activeOverlays and trigger history.back()
        try {
            this.spa._closeOverlay(modalId, true);
        } catch (err) {
            // Fallback: ensure modal removed if spa handler fails
            const index = this.spa.activeOverlays.indexOf(modalId);
            if (index > -1) this.spa.activeOverlays.splice(index, 1);
            this.close(modalId);
            if (this.spa.activeOverlays.length === 0) this.spa._hideBackdrop();
        }
    }
}

// =================== MULTI-STEP SHEET MANAGER ===================

/**
 * Gerenciador de Sheets com Múltiplas Etapas
 */
export class MultiStepSheetManager {
    constructor(spa) {
        this.spa = spa;
        this.instances = new Map();
    }

    /**
     * Registra um sheet multi-step
     */
    register(sheetId) {
        const sheet = document.getElementById(sheetId);
        if (!sheet) return;

        const steps = Array.from(sheet.querySelectorAll("[data-step]"));

        this.instances.set(sheetId, {
            element: sheet,
            steps: steps,
            currentStepIndex: 0,
            totalSteps: steps.length,
            formData: {},
        });

        sheet.addEventListener("click", (e) => {
            const actionBtn = e.target.closest("[data-step-action]");
            if (!actionBtn) return;

            e.preventDefault();
            const action = actionBtn.dataset.stepAction;

            if (action === "next") this.next(sheetId);
            if (action === "prev") this.prev(sheetId);
            if (action === "finish") this.finish(sheetId);
        });

        this._updateView(sheetId, 0);
    }

    reset(sheetId) {
        const instance = this.instances.get(sheetId);
        if (!instance) return;

        const inputs = instance.element.querySelectorAll(
            "input, select, textarea"
        );
        inputs.forEach((input) => {
            if (input.type === "checkbox" || input.type === "radio") {
                input.checked = false;
            } else {
                input.value = "";
            }
        });

        instance.formData = {};
        this._updateView(sheetId, 0);
    }

    next(sheetId) {
        const instance = this.instances.get(sheetId);
        if (!instance) return;

        const currentStepEl = instance.steps[instance.currentStepIndex];
        if (!this._validateStep(currentStepEl)) return;

        this._collectStepData(instance, currentStepEl);

        if (instance.currentStepIndex < instance.totalSteps - 1) {
            this._updateView(sheetId, instance.currentStepIndex + 1);
        }
    }

    prev(sheetId) {
        const instance = this.instances.get(sheetId);
        if (!instance) return;

        if (instance.currentStepIndex > 0) {
            this._updateView(sheetId, instance.currentStepIndex - 1);
        }
    }

    finish(sheetId) {
        const instance = this.instances.get(sheetId);
        if (!instance) return;

        const currentStepEl = instance.steps[instance.currentStepIndex];
        if (!this._validateStep(currentStepEl)) return;

        this._collectStepData(instance, currentStepEl);

        this.spa.closeSheet(sheetId);

        const event = new CustomEvent("multistep:finish", {
            detail: {
                sheetId: sheetId,
                data: instance.formData,
            },
        });
        document.dispatchEvent(event);

        if (this.spa?.config?.debug?.enabled) {
            console.log("📦 MultiStep Data:", instance.formData);
        }

        setTimeout(() => this.reset(sheetId), 500);
    }

    _updateView(sheetId, newIndex) {
        const instance = this.instances.get(sheetId);
        if (!instance) return;

        instance.currentStepIndex = newIndex;

        instance.steps.forEach((step, index) => {
            step.classList.remove("active", "prev", "next");
            if (index === newIndex) step.classList.add("active");
            else if (index < newIndex) step.classList.add("prev");
            else step.classList.add("next");
        });

        const indicators = instance.element.querySelectorAll(
            ".step-indicator .dot"
        );
        indicators.forEach((dot, index) => {
            dot.classList.toggle("active", index <= newIndex);
        });

        const progressText = instance.element.querySelector(
            "[data-step-counter]"
        );
        if (progressText)
            progressText.textContent = `${newIndex + 1}/${instance.totalSteps}`;
    }

    _collectStepData(instance, stepElement) {
        const inputs = stepElement.querySelectorAll("input, select, textarea");

        inputs.forEach((input) => {
            if (!input.name) return;

            // Trata checkbox múltiplo agregando valores
            if (input.type === "checkbox") {
                if (input.checked) {
                    if (!Array.isArray(instance.formData[input.name])) {
                        if (instance.formData[input.name] !== undefined) {
                            instance.formData[input.name] = [
                                instance.formData[input.name],
                            ];
                        } else {
                            instance.formData[input.name] = [];
                        }
                    }
                    instance.formData[input.name].push(input.value || true);
                }
                return;
            }

            let value = input.value;
            if (input.type === "radio") {
                if (!input.checked) return;
                value = input.value;
            } else if (input.type === "number") {
                value = parseFloat(input.value);
            }

            if (instance.formData[input.name] !== undefined) {
                if (!Array.isArray(instance.formData[input.name])) {
                    instance.formData[input.name] = [
                        instance.formData[input.name],
                    ];
                }
                instance.formData[input.name].push(value);
            } else {
                instance.formData[input.name] = value;
            }
        });
    }

    _validateStep(stepElement) {
        const inputs = stepElement.querySelectorAll("input, select, textarea");
        let isValid = true;

        inputs.forEach((input) => {
            if (!input.checkValidity()) {
                isValid = false;
                input.reportValidity();
                input.classList.add("input-error");
                input.addEventListener(
                    "input",
                    () => input.classList.remove("input-error"),
                    { once: true }
                );
            }
        });

        return isValid;
    }
}

// =================== TOAST MANAGER ===================

/**
 * Utilitário estático para toasts (standalone)
 */
const Toast = {
    container: null,
    config: {
        duration: 3000,
        position: "bottom-right",
        maxVisible: 3,
    },

    /**
     * Configura o toast manager
     */
    configure(options) {
        Object.assign(this.config, options);
    },

    /**
     * Cria container de toasts
     */
    _ensureContainer() {
        if (this.container) return;

        this.container = document.getElementById("toast-container");
        if (!this.container) {
            this.container = document.createElement("div");
            this.container.id = "toast-container";
            this.container.className = `toast-container toast-${this.config.position}`;
            document.body.appendChild(this.container);
        }
    },

    /**
     * Mostra um toast
     */
    show(message, type = "info") {
        this._ensureContainer();

        const toast = document.createElement("div");
        toast.className = `toast-item toast-${type}`;

        const icons = {
            success: "✓",
            error: "✕",
            warning: "⚠",
            info: "ℹ",
        };

        toast.innerHTML = `
                <span class="toast-icon">${icons[type] || icons.info}</span>
                <span class="toast-message">${message}</span>
            `;

        this.container.appendChild(toast);

        // Limita número de toasts
        const toasts = this.container.querySelectorAll(".toast-item");
        if (toasts.length > this.config.maxVisible) {
            toasts[0].remove();
        }

        // Anima entrada
        requestAnimationFrame(() => {
            toast.classList.add("show");
        });

        // Remove após duração
        setTimeout(() => {
            toast.classList.remove("show");
            setTimeout(() => toast.remove(), 300);
        }, this.config.duration);

        return toast;
    },

    success(message) {
        return this.show(message, "success");
    },

    error(message) {
        return this.show(message, "error");
    },

    warning(message) {
        return this.show(message, "warning");
    },

    info(message) {
        return this.show(message, "info");
    },
};

// Exporta Toast
export { Toast };

// Compatibilidade com uso global (opcional)
if (typeof window !== "undefined") {
    window.DrawerManager = DrawerManager;
    window.SheetManager = SheetManager;
    window.ModalManager = ModalManager;
    window.Toast = Toast;
}
