/**
 * 💬 Chat Page - Chat com IA
 */

// Estado do chat
let messages = [];
let replyingTo = null;
let contextMenu = null;

/**
 * Inicializa a página de Chat
 * @param {SPA} app - Instância do SPA
 */
export function initChatPage(app) {
    const page = document.getElementById("page-chat");
    if (!page) return;

    page.addEventListener("page:enter", () => {
        console.log("💬 Chat page entered");
        loadMessages();
        setupChatInput();
        setupContextMenu();
        setupEmojiPicker();
        scrollToBottom();
    });

    page.addEventListener("page:leave", () => {
        hideContextMenu();
    });
}

/**
 * Carrega mensagens salvas
 */
function loadMessages() {
    const saved = localStorage.getItem("lamarck_chat_messages");
    if (saved) {
        try {
            messages = JSON.parse(saved);
            renderMessages();
        } catch (e) {
            messages = [];
        }
    }

    // Adiciona mensagem de boas-vindas se vazio
    if (messages.length === 0) {
        addBotMessage(
            "Olá! 👋 Sou a assistente virtual do Laboratório Lamarck. Como posso ajudar?"
        );
    }
}

/**
 * Salva mensagens
 */
function saveMessages() {
    localStorage.setItem(
        "lamarck_chat_messages",
        JSON.stringify(messages.slice(-50))
    ); // Mantém últimas 50
}

/**
 * Renderiza todas as mensagens
 */
function renderMessages() {
    const container = document.getElementById("chat-messages");
    if (!container) return;

    // Mantém aviso no topo
    const warning = container.querySelector(".bg-yellow-50");
    container.innerHTML = "";
    if (warning) container.appendChild(warning);

    messages.forEach((msg) => {
        const el = createMessageElement(msg);
        container.appendChild(el);
    });

    scrollToBottom();
}

/**
 * Cria elemento de mensagem
 * @param {Object} msg
 * @returns {HTMLElement}
 */
function createMessageElement(msg) {
    const wrapper = document.createElement("div");
    wrapper.className = `mb-3 flex ${
        msg.type === "sent" ? "justify-end" : "justify-start"
    }`;
    wrapper.dataset.messageId = msg.id;

    const bubble = document.createElement("div");
    bubble.className = `message ${
        msg.type === "sent" ? "message-sent" : "message-received"
    }`;

    // Reply reference
    if (msg.replyTo) {
        const replyRef = document.createElement("div");
        replyRef.className = "message-reply";
        replyRef.textContent = msg.replyTo;
        bubble.appendChild(replyRef);
    }

    // Content
    const content = document.createElement("div");
    content.className = "message-content";
    content.innerHTML = formatMessageContent(msg.text);
    bubble.appendChild(content);

    // Time
    const time = document.createElement("div");
    time.className = "message-time";
    time.textContent = formatTime(msg.timestamp);
    bubble.appendChild(time);

    // Reactions
    if (msg.reactions && msg.reactions.length > 0) {
        const reactions = document.createElement("div");
        reactions.className = "message-reactions flex gap-1 mt-2";
        msg.reactions.forEach((r) => {
            const span = document.createElement("span");
            span.className = "text-sm bg-white/20 px-2 py-0.5 rounded-full";
            span.textContent = r;
            reactions.appendChild(span);
        });
        bubble.appendChild(reactions);
    }

    // Long press / right click for context menu
    bubble.addEventListener("contextmenu", (e) => showContextMenu(e, msg));
    bubble.addEventListener("long-press", (e) => showContextMenu(e, msg));

    wrapper.appendChild(bubble);
    return wrapper;
}

/**
 * Formata conteúdo da mensagem (links, etc)
 * @param {string} text
 * @returns {string}
 */
function formatMessageContent(text) {
    // Converte URLs em links
    const urlRegex = /(https?:\/\/[^\s]+)/g;
    return text.replace(
        urlRegex,
        '<a href="$1" target="_blank" class="underline">$1</a>'
    );
}

/**
 * Formata timestamp
 * @param {number} timestamp
 * @returns {string}
 */
function formatTime(timestamp) {
    return new Date(timestamp).toLocaleTimeString("pt-BR", {
        hour: "2-digit",
        minute: "2-digit",
    });
}

/**
 * Adiciona mensagem do usuário
 * @param {string} text
 */
function addUserMessage(text) {
    const msg = {
        id: Date.now().toString(),
        type: "sent",
        text: text,
        timestamp: Date.now(),
        replyTo: replyingTo?.text || null,
        reactions: [],
    };

    messages.push(msg);
    saveMessages();

    const container = document.getElementById("chat-messages");
    if (container) {
        container.appendChild(createMessageElement(msg));
        scrollToBottom();
    }

    cancelReply();

    // Simula resposta do bot
    setTimeout(() => simulateBotResponse(text), 1000 + Math.random() * 1500);
}

/**
 * Adiciona mensagem do bot
 * @param {string} text
 */
function addBotMessage(text) {
    const msg = {
        id: Date.now().toString(),
        type: "received",
        text: text,
        timestamp: Date.now(),
        reactions: [],
    };

    messages.push(msg);
    saveMessages();

    const container = document.getElementById("chat-messages");
    if (container) {
        container.appendChild(createMessageElement(msg));
        scrollToBottom();
    }
}

/**
 * Simula resposta do bot
 * @param {string} userMessage
 */
function simulateBotResponse(userMessage) {
    const lowerMsg = userMessage.toLowerCase();
    let response = "";

    if (lowerMsg.includes("resultado") || lowerMsg.includes("exame")) {
        response =
            'Para consultar seus resultados, acesse a seção "Resultados" no menu principal ou entre em contato via WhatsApp com seu código de acesso. 📋';
    } else if (
        lowerMsg.includes("horário") ||
        lowerMsg.includes("funcionamento")
    ) {
        response =
            "🕐 Nosso horário de funcionamento:\n• Segunda a Sexta: 7h às 18h\n• Sábados: 7h às 12h\n• Domingos e feriados: Fechado";
    } else if (
        lowerMsg.includes("agendamento") ||
        lowerMsg.includes("agendar") ||
        lowerMsg.includes("marcar")
    ) {
        response =
            "Para agendar um exame, você pode usar nossa opção de pré-agendamento no app ou entrar em contato via WhatsApp. Quer que eu te direcione? 📅";
    } else if (lowerMsg.includes("convênio") || lowerMsg.includes("plano")) {
        response =
            'Trabalhamos com os principais convênios: Unimed, Bradesco, SulAmérica, Amil, Porto Seguro e outros. Consulte a lista completa na seção "Convênios". 💳';
    } else if (lowerMsg.includes("preparo") || lowerMsg.includes("jejum")) {
        response =
            'O preparo varia de acordo com o exame. Em geral:\n• Hemograma: jejum de 4h\n• Glicemia: jejum de 8-12h\n• Perfil lipídico: jejum de 12h\n\nConsulte "Nossos Exames" para ver o preparo específico. 📝';
    } else if (
        lowerMsg.includes("obrigado") ||
        lowerMsg.includes("valeu") ||
        lowerMsg.includes("agradeço")
    ) {
        response =
            "Por nada! Estou aqui para ajudar. Se precisar de mais alguma coisa, é só perguntar! 😊";
    } else if (
        lowerMsg.includes("olá") ||
        lowerMsg.includes("oi") ||
        lowerMsg.includes("bom dia") ||
        lowerMsg.includes("boa tarde")
    ) {
        response = "Olá! Como posso ajudar você hoje? 🙂";
    } else {
        const responses = [
            "Entendi! Para informações mais detalhadas, recomendo entrar em contato com nossa equipe via WhatsApp. 📱",
            "Certo! Se precisar de ajuda com agendamentos, resultados ou informações sobre exames, estou à disposição. 😊",
            "Para garantir que você receba a melhor orientação, sugiro conversar com nosso atendimento humano via WhatsApp. Posso te direcionar? 💬",
        ];
        response = responses[Math.floor(Math.random() * responses.length)];
    }

    addBotMessage(response);
}

/**
 * Configura input do chat
 */
function setupChatInput() {
    const input = document.getElementById("chat-input");
    if (!input) return;

    // Auto-resize
    input.addEventListener("input", () => {
        input.style.height = "auto";
        input.style.height = Math.min(input.scrollHeight, 128) + "px";
    });
}

/**
 * Envia mensagem
 */
export function sendMessage() {
    const input = document.getElementById("chat-input");
    if (!input) return;

    const text = input.value.trim();
    if (!text) return;

    addUserMessage(text);
    input.value = "";
    input.style.height = "auto";
}

/**
 * Trata keydown no input
 * @param {KeyboardEvent} event
 */
export function handleChatKeydown(event) {
    if (event.key === "Enter" && !event.shiftKey) {
        event.preventDefault();
        sendMessage();
    }
}

/**
 * Auto-resize do textarea
 * @param {HTMLTextAreaElement} el
 */
export function autoResizeTextarea(el) {
    el.style.height = "auto";
    el.style.height = Math.min(el.scrollHeight, 128) + "px";
}

/**
 * Scroll para o final
 */
export function scrollToBottom() {
    const container = document.getElementById("chat-messages");
    if (container) {
        setTimeout(() => {
            container.scrollTop = container.scrollHeight;
        }, 100);
    }
}

/**
 * Configura menu de contexto
 */
function setupContextMenu() {
    // Fecha ao clicar fora
    document.addEventListener("click", (e) => {
        const menu = document.getElementById("message-context-menu");
        if (menu && !menu.contains(e.target)) {
            hideContextMenu();
        }
    });
}

/**
 * Mostra menu de contexto
 * @param {Event} e
 * @param {Object} msg
 */
function showContextMenu(e, msg) {
    e.preventDefault();

    contextMenu = msg;

    const menu = document.getElementById("message-context-menu");
    if (!menu) return;

    // Posiciona menu
    const x = e.clientX || e.touches?.[0]?.clientX || 100;
    const y = e.clientY || e.touches?.[0]?.clientY || 100;

    menu.style.left = Math.min(x, window.innerWidth - 200) + "px";
    menu.style.top = Math.min(y, window.innerHeight - 200) + "px";
    menu.classList.remove("hidden");
}

/**
 * Esconde menu de contexto
 */
function hideContextMenu() {
    const menu = document.getElementById("message-context-menu");
    if (menu) menu.classList.add("hidden");
    contextMenu = null;
}

/**
 * Ação do menu de contexto
 * @param {string} action
 */
export function contextMenuAction(action) {
    const app = window.app;

    switch (action) {
        case "reply":
            if (contextMenu) {
                replyingTo = contextMenu;
                showReplyPreview(contextMenu.text);
            }
            break;

        case "react":
            showReactionPicker();
            return; // Não fecha menu

        case "copy":
            if (contextMenu) {
                navigator.clipboard.writeText(contextMenu.text);
                app?.toastSuccess(
                    "Copiado!",
                    "Texto copiado para área de transferência"
                );
            }
            break;

        case "delete":
            if (contextMenu) {
                deleteMessage(contextMenu.id);
            }
            break;
    }

    hideContextMenu();
}

/**
 * Mostra preview de resposta
 * @param {string} text
 */
function showReplyPreview(text) {
    const preview = document.getElementById("reply-preview");
    const replyText = document.getElementById("reply-text");

    if (preview && replyText) {
        replyText.textContent =
            text.substring(0, 100) + (text.length > 100 ? "..." : "");
        preview.classList.remove("hidden");
    }

    // Foca no input
    document.getElementById("chat-input")?.focus();
}

/**
 * Cancela resposta
 */
export function cancelReply() {
    replyingTo = null;
    const preview = document.getElementById("reply-preview");
    if (preview) preview.classList.add("hidden");
}

/**
 * Deleta mensagem
 * @param {string} id
 */
function deleteMessage(id) {
    messages = messages.filter((m) => m.id !== id);
    saveMessages();
    renderMessages();

    window.app?.toastInfo("Mensagem excluída", "");
}

/**
 * Toggle emoji picker
 */
export function toggleEmojiPicker() {
    const grid = document.getElementById("emoji-grid");
    if (!grid) return;

    grid.classList.toggle("hidden");

    if (!grid.classList.contains("hidden")) {
        populateEmojiGrid();
    }
}

/**
 * Popula grid de emojis
 */
function populateEmojiGrid() {
    const container = document.getElementById("emoji-grid-content");
    if (!container) return;

    const emojis = [
        "😀",
        "😂",
        "🥹",
        "😊",
        "🙂",
        "😉",
        "😍",
        "🥰",
        "😘",
        "😋",
        "🤔",
        "🤗",
        "🤭",
        "😏",
        "😌",
        "😴",
        "🤒",
        "😷",
        "🤕",
        "🤢",
        "👍",
        "👎",
        "👏",
        "🙏",
        "💪",
        "❤️",
        "🔥",
        "⭐",
        "✨",
        "💯",
        "✅",
        "❌",
    ];

    container.innerHTML = "";
    emojis.forEach((emoji) => {
        const btn = document.createElement("button");
        btn.type = "button";
        btn.className =
            "text-2xl p-2 hover:bg-slate-100 rounded-lg transition-colors";
        btn.textContent = emoji;
        btn.onclick = () => insertEmoji(emoji);
        container.appendChild(btn);
    });
}

/**
 * Insere emoji no input
 * @param {string} emoji
 */
function insertEmoji(emoji) {
    const input = document.getElementById("chat-input");
    if (input) {
        input.value += emoji;
        input.focus();
    }
}

/**
 * Mostra picker de reações
 */
function showReactionPicker() {
    const picker = document.getElementById("reaction-picker");
    if (!picker) return;

    const reactions = ["❤️", "👍", "😂", "😮", "😢", "🙏"];

    picker.innerHTML = "";
    reactions.forEach((emoji) => {
        const btn = document.createElement("button");
        btn.className =
            "text-2xl p-2 hover:bg-slate-100 rounded-lg transition-all hover:scale-125";
        btn.textContent = emoji;
        btn.onclick = () => addReaction(emoji);
        picker.appendChild(btn);
    });

    picker.classList.remove("hidden");

    // Posiciona próximo ao menu
    const menu = document.getElementById("message-context-menu");
    if (menu) {
        picker.style.left = menu.style.left;
        picker.style.top =
            parseInt(menu.style.top) + menu.offsetHeight + 8 + "px";
    }
}

/**
 * Adiciona reação
 * @param {string} emoji
 */
function addReaction(emoji) {
    if (contextMenu) {
        const msg = messages.find((m) => m.id === contextMenu.id);
        if (msg) {
            if (!msg.reactions) msg.reactions = [];
            if (!msg.reactions.includes(emoji)) {
                msg.reactions.push(emoji);
                saveMessages();
                renderMessages();
            }
        }
    }

    hideContextMenu();
    document.getElementById("reaction-picker")?.classList.add("hidden");
}

/**
 * Configura emoji picker
 */
function setupEmojiPicker() {
    const closeBtn = document.getElementById("emoji-grid-close");
    if (closeBtn) {
        closeBtn.onclick = () => {
            document.getElementById("emoji-grid")?.classList.add("hidden");
        };
    }
}

/**
 * Toggle menu do chat
 */
export function toggleChatMenu() {
    const app = window.app;
    app?.openSheet("chat-options");
}

/**
 * Anexar arquivo
 */
export function attachFile() {
    window.app?.toastInfo(
        "Em breve",
        "Envio de arquivos estará disponível em breve"
    );
}

// Expõe funções globalmente
window.sendMessage = sendMessage;
window.handleChatKeydown = handleChatKeydown;
window.autoResizeTextarea = autoResizeTextarea;
window.scrollToBottom = scrollToBottom;
window.contextMenuAction = contextMenuAction;
window.cancelReply = cancelReply;
window.toggleEmojiPicker = toggleEmojiPicker;
window.toggleChatMenu = toggleChatMenu;
window.attachFile = attachFile;
