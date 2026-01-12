# 🚀 Quick Start - SPA Framework

Comece a usar o SPA Framework em 5 minutos!

## 📦 Instalação

### Opção 1: Copiar Arquivos

Copie a pasta `spa-framework` para seu projeto e inclua os arquivos:

```html
<link rel="stylesheet" href="spa-framework/dist/spa.css" />
<script src="spa-framework/dist/spa.min.js"></script>
```

### Opção 2: Arquivos Individuais (Desenvolvimento)

```html
<link rel="stylesheet" href="spa-framework/dist/spa.css" />

<!-- Core -->
<script src="spa-framework/src/core/spa.js"></script>

<!-- UI Components -->
<script src="spa-framework/src/ui/modals.js"></script>

<!-- Storage -->
<script src="spa-framework/src/storage/indexeddb.js"></script>
<script src="spa-framework/src/storage/localstorage.js"></script>

<!-- Offline -->
<script src="spa-framework/src/offline/queue.js"></script>

<!-- PWA -->
<script src="spa-framework/src/pwa/install.js"></script>
<script src="spa-framework/src/pwa/notifications.js"></script>
```

## 🎯 Estrutura Básica HTML

```html
<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, viewport-fit=cover"
    />
    <meta name="theme-color" content="#1e293b" />
    <link rel="stylesheet" href="spa-framework/dist/spa.css" />
    <title>Meu App</title>
  </head>
  <body>
    <!-- Container Principal -->
    <div id="app">
      <!-- Página Home -->
      <div class="page active" id="page-home" data-level="home">
        <header class="app-header">
          <h1 class="header-title">Meu App</h1>
        </header>

        <main class="page-content">
          <div class="bento-grid">
            <div class="bento-widget" data-go="sobre">
              <div class="widget-icon">📄</div>
              <h3 class="widget-title">Sobre</h3>
            </div>
            <div class="bento-widget" data-go="contato">
              <div class="widget-icon">📞</div>
              <h3 class="widget-title">Contato</h3>
            </div>
          </div>
        </main>
      </div>

      <!-- Página Sobre -->
      <div class="page" id="page-sobre" data-level="primary">
        <header class="app-header">
          <button class="icon-btn" data-back>←</button>
          <h1 class="header-title">Sobre</h1>
        </header>

        <main class="page-content">
          <p>Conteúdo da página Sobre</p>
        </main>
      </div>

      <!-- Página Contato -->
      <div class="page" id="page-contato" data-level="primary">
        <header class="app-header">
          <button class="icon-btn" data-back>←</button>
          <h1 class="header-title">Contato</h1>
        </header>

        <main class="page-content">
          <p>Conteúdo da página Contato</p>
        </main>
      </div>
    </div>

    <!-- Backdrop para Overlays -->
    <div id="backdrop"></div>

    <!-- Toast Container -->
    <div id="toast-container"></div>

    <!-- Loading Overlay -->
    <div id="loading-overlay">
      <div class="loading-spinner"></div>
      <p class="loading-text">Carregando...</p>
    </div>

    <!-- Scripts -->
    <script src="spa-framework/dist/spa.min.js"></script>
    <script>
      const app = new SPA({
        homePage: "home",
        animation: {
          type: "fade", // fade, slide, stack, zoom, flip, cube
          speed: 0.35,
        },
      });

      app.init();

      // Torna global para acesso
      window.app = app;
    </script>
  </body>
</html>
```

## 🎨 Data Attributes

O framework usa data attributes para navegação declarativa:

### Navegação

```html
<!-- Navegar para página -->
<button data-go="nome-pagina">Ir para Página</button>

<!-- Voltar (inteligente) -->
<button data-back>Voltar</button>

<!-- Voltar para página específica -->
<button data-back="home">Ir para Home</button>
```

### Overlays

```html
<!-- Abrir Drawer -->
<button data-drawer="menu">Abrir Menu</button>

<!-- Abrir Bottom Sheet -->
<button data-sheet="filtros">Abrir Filtros</button>

<!-- Abrir Modal -->
<button data-modal="confirmar">Abrir Modal</button>

<!-- Fechar Overlay -->
<button data-close-overlay>Fechar</button>
```

### Hierarquia de Páginas

```html
<!-- Página raiz (home) -->
<div class="page" id="page-home" data-level="home">
  <!-- Página principal (volta para home) -->
  <div class="page" id="page-sobre" data-level="primary">
    <!-- Sub-página (volta para parent) -->
    <div
      class="page"
      id="page-detalhes"
      data-level="secondary"
      data-parent="sobre"
    ></div>
  </div>
</div>
```

## 🎯 Drawer (Menu Lateral)

```html
<!-- Botão para abrir -->
<button data-drawer="menu">☰</button>

<!-- Drawer -->
<div class="drawer" id="menu">
  <div class="drawer-header">
    <h2>Menu</h2>
  </div>
  <div class="drawer-body">
    <div class="list-item" data-go="home" data-close-overlay>
      <span>🏠</span>
      <span>Home</span>
    </div>
    <div class="list-item" data-go="sobre" data-close-overlay>
      <span>📄</span>
      <span>Sobre</span>
    </div>
  </div>
</div>
```

## 📋 Bottom Sheet

```html
<!-- Botão para abrir -->
<button data-sheet="opcoes">Opções</button>

<!-- Bottom Sheet -->
<div class="bottom-sheet" id="opcoes">
  <div class="grabber-handle">
    <div class="grabber-bar"></div>
  </div>
  <div class="sheet-header">
    <h3>Opções</h3>
  </div>
  <div class="sheet-body">
    <div class="list-item">Opção 1</div>
    <div class="list-item">Opção 2</div>
    <div class="list-item">Opção 3</div>
  </div>
</div>
```

## 💬 Modal via JavaScript

```javascript
// Modal de confirmação
const confirmado = await app.modal({
  title: "Confirmar Ação",
  message: "Deseja realmente continuar?",
  type: "confirm",
  confirmText: "Sim",
  cancelText: "Não",
});

if (confirmado) {
  console.log("Usuário confirmou!");
}

// Modal de prompt
const nome = await app.modal({
  title: "Seu Nome",
  message: "Digite seu nome:",
  type: "prompt",
  placeholder: "Nome completo",
});

// Modal de alerta
await app.modal({
  title: "Sucesso!",
  message: "Operação realizada com sucesso.",
  type: "success",
});
```

## 🍞 Toasts

```javascript
// Tipos de toast
app.toast("Operação realizada!", "success");
app.toast("Algo deu errado!", "error");
app.toast("Atenção!", "warning");
app.toast("Informação importante", "info");
```

## 💾 Storage

### localStorage

```javascript
// Salvar
app.storage.set("usuario", { nome: "João", idade: 30 });

// Ler
const usuario = app.storage.get("usuario");

// Ler com default
const config = app.storage.get("config", { tema: "light" });

// Verificar
if (app.storage.has("usuario")) {
}

// Remover
app.storage.remove("usuario");

// Observar mudanças
app.storage.watch("usuario", (novo, antigo) => {
  console.log("Usuário mudou:", novo);
});
```

### IndexedDB

```javascript
// Todos os registros
const usuarios = await app.db.table("usuarios").all();

// Buscar por ID
const usuario = await app.db.table("usuarios").find(1);

// Inserir
const novo = await app.db.table("usuarios").insert({
  nome: "Maria",
  email: "maria@email.com",
});

// Atualizar
await app.db.table("usuarios").update(1, { nome: "Maria Silva" });

// Deletar
await app.db.table("usuarios").delete(1);

// Query com filtro
const ativos = await app.db
  .table("usuarios")
  .where("ativo", true)
  .orderBy("nome", "asc")
  .limit(10)
  .all();
```

## 📋 Job Queue (Sincronização Offline)

```javascript
// Registrar handler para um tipo de job
app.queue.register("sync-user", async (data, job) => {
  const response = await fetch("/api/users", {
    method: "POST",
    body: JSON.stringify(data),
  });
  return response.json();
});

// Adicionar job à fila
const jobId = await app.queue.add("sync-user", {
  nome: "João",
  email: "joao@email.com",
});

// Listar jobs
const todos = await app.queue.all();
const pendentes = await app.queue.pending();
const completos = await app.queue.completed();
const falhos = await app.queue.failed();

// Estatísticas
const stats = await app.queue.stats();
console.log(stats); // { total, pending, processing, completed, failed }

// Remover job
await app.queue.remove(jobId);

// Marcar como completo (com resultado)
await app.queue.complete(jobId, { success: true });

// Reenviar job falho
await app.queue.retry(jobId);

// Processar todos os pendentes
await app.queue.processAll();

// Escutar eventos
app.queue.on("job:completed", (job) => {
  console.log("Job completado:", job);
});

app.queue.on("job:failed", (job) => {
  console.log("Job falhou:", job);
});
```

## 📱 PWA

````javascript
// Verificar se pode instalar
if (app.pwa.canInstall()) {
    // Mostrar botão de instalação
    btnInstalar.style.display = "block";
}

// Solicitar instalação
btnInstalar.addEventListener("click", async () => {
    const instalado = await app.pwa.promptInstall();
    if (instalado) {
        app.toast("App instalado!", "success");
    }
});

// Verificar se está instalado
if (app.pwa.isStandalone) {
    console.log("Rodando como app instalado");
}

## ▶️ Executando o Demo Localmente

Para testar rapidamente o demo incluído no repositório, sirva a pasta `basic/` com um servidor estático e abra no navegador:

```bash
cd basic
python -m http.server 8000
# ou
npx serve . -p 8000
````

Acesse `http://localhost:8000` e interaja com os exemplos (toasts, navegação, storage, PWA).

````

## 🔔 Notificações

```javascript
// Solicitar permissão
const permitido = await app.notifications.request();

// Mostrar notificação
if (permitido) {
    app.notifications.show("Nova mensagem", {
        body: "Você recebeu uma nova mensagem!",
        icon: "/img/icon.png",
    });
}

// Atalhos
app.notifications.success("Sucesso", "Operação realizada");
app.notifications.error("Erro", "Algo deu errado");
app.notifications.info("Info", "Nova atualização disponível");
````

## 🎨 Classes CSS Úteis

```html
<!-- Grid Bento -->
<div class="bento-grid">
  <div class="bento-widget">Widget 1</div>
  <div class="bento-widget span-2">Widget Grande</div>
  <div class="bento-widget">Widget 2</div>
</div>

<!-- Botões -->
<button class="btn btn-primary">Primário</button>
<button class="btn btn-secondary">Secundário</button>
<button class="btn btn-outline">Outline</button>
<button class="btn btn-ghost">Ghost</button>

<!-- Cards -->
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Título</h3>
  </div>
  <div class="card-body">Conteúdo do card</div>
</div>

<!-- Listas -->
<div class="list-item">
  <div class="list-item-icon">📄</div>
  <div class="list-item-content">
    <div class="list-item-title">Título</div>
    <div class="list-item-subtitle">Subtítulo</div>
  </div>
  <div class="list-item-action">→</div>
</div>

<!-- Formulários -->
<div class="input-group">
  <label class="input-label">Email</label>
  <input type="email" class="input-field" placeholder="seu@email.com" />
</div>

<!-- Toggle Switch -->
<label class="toggle">
  <input type="checkbox" class="toggle-input" />
  <span class="toggle-switch"></span>
  <span>Ativar opção</span>
</label>

<!-- Badge -->
<span class="badge badge-success">Ativo</span>

<!-- Avatar -->
<div class="avatar">JS</div>

<!-- Loading Skeleton -->
<div class="skeleton" style="width: 100%; height: 20px;"></div>

<!-- Empty State -->
<div class="empty-state">
  <div class="empty-state-icon">📭</div>
  <h3 class="empty-state-title">Nada aqui</h3>
  <p class="empty-state-message">Não há itens para mostrar.</p>
</div>
```

## 📱 PWA - Arquivos Necessários

### manifest.json

```json
{
  "name": "Meu App",
  "short_name": "MeuApp",
  "start_url": "/",
  "display": "standalone",
  "background_color": "#ffffff",
  "theme_color": "#1e293b",
  "icons": [
    {
      "src": "/img/icon-192.png",
      "sizes": "192x192",
      "type": "image/png"
    },
    {
      "src": "/img/icon-512.png",
      "sizes": "512x512",
      "type": "image/png"
    }
  ]
}
```

### No HTML

```html
<link rel="manifest" href="/manifest.json" />
<meta name="theme-color" content="#1e293b" />
<link rel="apple-touch-icon" href="/img/icon-192.png" />
```

## 🎉 Pronto!

Agora você tem um app SPA completo com:

- ✅ Navegação fluida entre páginas
- ✅ Animações de transição
- ✅ Drawer, Sheet e Modal
- ✅ Gestos touch
- ✅ Armazenamento local
- ✅ Funcionamento offline
- ✅ Sistema de filas
- ✅ PWA ready
- ✅ Notificações

Consulte a [documentação completa](README.md) para mais opções e configurações.
