# 🚀 SPA Framework

Framework SPA completo para criar aplicações web com experiência nativa, desenvolvido com foco em simplicidade e performance.

## ✨ Características

- **🎯 Navegação Declarativa** - Configure navegação via HTML com `data-attributes`
- **🍱 Toasts Bento** - Sistema de notificações moderno com progress e persistência
- **🔗 Data Bindings** - Vincule elementos com páginas de forma declarativa
- **📱 PWA Ready** - Suporte completo a Progressive Web Apps
- **🔄 Offline First** - Funciona sem internet com sincronização automática
- **💾 Storage ORM** - ORM simples para IndexedDB e localStorage
- **⚡ Jobs Queue** - Sistema de filas para sincronização de dados
- **🎨 Bento Design** - UI moderna com bordas XL e componentes estilizados
- **👆 Gestos Touch** - Swipe, drag e gestos nativos
- **📦 Zero Dependências** - Funciona com HTML puro

## 📦 Instalação

### Via CDN (Recomendado)

```html
<link rel="stylesheet" href="spa-framework/dist/spa.css" />
<script src="spa-framework/dist/spa.min.js"></script>
```

### Via npm

```bash
npm install @lamarck/spa-framework
```

## 🚀 Quick Start

```html
<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, viewport-fit=cover"
    />
    <link rel="stylesheet" href="dist/spa.css" />
    <title>Meu App</title>
  </head>
  <body>
    <!-- Container Principal -->
    <div id="app">
      <!-- Página Home -->
      <div class="page active" id="page-home" data-level="home">
        <header class="app-header">
          <h1>Home</h1>
        </header>
        <main class="page-content">
          <button data-go="sobre">Ir para Sobre</button>
        </main>
      </div>

      <!-- Página Sobre -->
      <div class="page" id="page-sobre" data-level="primary" data-parent="home">
        <header class="app-header">
          <button data-back class="icon-btn">←</button>
          <h1>Sobre</h1>
        </header>
        <main class="page-content">
          <p>Conteúdo da página Sobre</p>
        </main>
      </div>
    </div>

    <!-- Bottom Nav com Data Bindings -->
    <nav class="bottom-nav" data-show-on="home,sobre">
      <button data-go="home">Home</button>
      <button data-go="sobre">Sobre</button>
    </nav>

    <script src="dist/spa.min.js"></script>
    <script>
      // Inicialização básica
      const app = new SPA();
      await app.init();

      // Toast de boas-vindas
      app.toastSuccess('Bem-vindo!', 'Framework carregado com sucesso');
    </script>
  </body>
</html>
```

## 📖 Documentação

- [Quick Start](docs/QUICK-START.md)
- [🍱 Bento Toasts](docs/BENTO_TOASTS.md) ⭐ **NOVO**
- [🔗 Data Bindings](docs/DATA_BINDINGS.md) ⭐ **NOVO**
- [Navegação](docs/NAVIGATION.md)
- [Componentes UI](docs/UI-COMPONENTS.md)
- [Storage ORM](docs/STORAGE.md)
- [Offline & Sync](docs/OFFLINE.md)
- [PWA](docs/PWA.md)
- [API Reference](docs/API.md)

## 🧪 Testes & Demo

- **Executando o demo localmente (pasta `basic/`)**:

```bash
# entre na pasta do projeto e sirva a pasta `basic`
cd basic
# Python 3 (porta 8000)
python -m http.server 8000

# ou via npx (serve)
npx serve . -p 8000
```

Abra `http://localhost:8000` no navegador para acessar o demo interativo. Para passos de teste de navegação e cenários manuais, veja `TESTE_NAVEGACAO.md`.

## 🔁 Notas de Migração (Rápido)

- Documentação atualizada com exemplos de execução local e instruções de teste.
- API principal mantém compatibilidade, prefira os atalhos tipados (ex.: `app.toastSuccess()` em vez de `app.toast(..., 'success')`).

## 📜 Changelog

Veja o histórico de mudanças em [CHANGELOG.md](CHANGELOG.md).

## 🍱 Toasts Bento (NOVO v1.1.0)

Sistema de notificações moderno com progress bar e persistência:

```javascript
// Toasts simples
app.toastSuccess("Salvo!", "Dados atualizados com sucesso");
app.toastError("Erro!", "Não foi possível conectar");

// Toast com progress bar
const id = app.toastLoading("Baixando...", "Conectando", { progress: 0 });
app.updateToast(id, { progress: 50, description: "Baixando... 50%" });
app.updateToast(id, {
  type: "success",
  title: "Completo!",
  dismissible: true,
});

// Toast persistente (só fecha com ação)
const id = app.toast({
  title: "Atualizando Sistema",
  description: "Não feche o navegador",
  type: "loading",
  dismissible: false,
  duration: Infinity,
});
```

[Ver documentação completa →](docs/BENTO_TOASTS.md)

## 🔗 Data Bindings (NOVO v1.1.0)

Vincule elementos com páginas de forma declarativa:

```html
<!-- Bottom nav aparece apenas em páginas principais -->
<nav class="bottom-nav" data-show-on="home,products,cart,profile">
  <button data-go="home">Home</button>
  <button data-go="products">Produtos</button>
</nav>

<!-- Botão FAB apenas em listas -->
<button class="fab" data-show-on="products,contacts">+</button>

<!-- Tabs com estado ativo automático -->
<div class="tabs">
  <button data-active-on="profile-info" data-go="profile-info">Info</button>
  <button data-active-on="profile-security" data-go="profile-security">
    Segurança
  </button>
</div>

<!-- Esconde header no onboarding -->
<header data-hide-on="onboarding-1,onboarding-2">Logo</header>
```

**Data Attributes:**

- `data-show-on="page1,page2"` - Mostra apenas nessas páginas
- `data-hide-on="page1,page2"` - Esconde nessas páginas
- `data-active-on="page1"` - Adiciona classe `active`
- `data-go="page"` - Automaticamente fica `active` quando ativo

[Ver documentação completa →](docs/DATA_BINDINGS.md)

## 🎯 Navegação Declarativa

### Navegação Básica

```html
<!-- Navegar para página -->
<button data-go="pagina">Ir</button>

<!-- Voltar -->
<button data-back>Voltar</button>

<!-- Voltar para página específica -->
<button data-back="home">Ir para Home</button>
```

### Overlays

```html
<!-- Abrir Drawer -->
<button data-drawer="menu">Menu</button>

<!-- Abrir Sheet -->
<button data-sheet="filtros">Filtros</button>

<!-- Abrir Modal -->
<button data-modal="confirmar">Confirmar</button>
```

## 💾 Storage ORM

```javascript
// IndexedDB
const users = await SPA.db.table("users").all();
const user = await SPA.db.table("users").find(1);
await SPA.db.table("users").insert({ name: "João" });
await SPA.db.table("users").update(1, { name: "João Silva" });
await SPA.db.table("users").delete(1);

// LocalStorage
SPA.storage.set("config", { theme: "dark" });
const config = SPA.storage.get("config");
```

## 🔄 Sistema de Jobs/Queue

```javascript
// Adicionar job à fila
const jobId = await SPA.queue.add("sync-user", {
  userId: 123,
  data: { name: "João" },
});

// Listar jobs
const jobs = await SPA.queue.all();
const pending = await SPA.queue.pending();

// Processar jobs manualmente
await SPA.queue.process("sync-user");

// Remover job após sucesso
await SPA.queue.remove(jobId);

// Marcar como concluído
await SPA.queue.complete(jobId);
```

## 📱 PWA

```javascript
// Verificar se pode instalar
if (SPA.pwa.canInstall()) {
  SPA.pwa.promptInstall();
}

// Notificações
await SPA.notifications.request();
SPA.notifications.show("Título", { body: "Mensagem" });
```

## 🎨 Componentes UI

```javascript
// Toast
SPA.toast("Mensagem de sucesso", "success");
SPA.toast("Erro!", "error");

// Modal
const result = await SPA.modal({
  title: "Confirmar",
  message: "Deseja continuar?",
  type: "confirm",
});

// Loading
SPA.loading.show();
SPA.loading.hide();
```

## ⚙️ Configuração

```javascript
const app = new SPA({
  // Navegação
  homePage: "home",
  defaultAnimation: "fade",
  useHistory: true,

  // Animações
  animation: {
    type: "fade", // fade, slide, stack, flip, zoom, cube
    speed: 0.35,
  },

  // Gestos
  gestures: {
    swipeBack: true,
    threshold: 50,
  },

  // Offline
  offline: {
    enabled: true,
    syncOnReconnect: true,
  },

  // PWA
  pwa: {
    showInstallBanner: true,
    enableNotifications: true,
  },
});
```

## 📂 Estrutura do Projeto

```
spa-framework/
├── dist/                   # Arquivos compilados
│   ├── spa.min.js
│   └── spa.css
├── src/
│   ├── core/              # Núcleo do framework
│   │   ├── spa.js         # Classe principal
│   │   └── router.js      # Sistema de rotas
│   ├── ui/                # Componentes UI
│   │   ├── modals.js
│   │   ├── sheets.js
│   │   ├── drawers.js
│   │   └── toasts.js
│   ├── storage/           # ORM e Storage
│   │   ├── indexeddb.js
│   │   └── localstorage.js
│   ├── offline/           # Sistema offline
│   │   ├── queue.js
│   │   └── service-worker.js
│   └── pwa/               # PWA features
│       ├── install.js
│       └── notifications.js
├── examples/              # Exemplos
├── docs/                  # Documentação
├── manifest.json          # PWA Manifest
└── service-worker.js      # Service Worker
```

## 🤝 Licença

MIT © Lamarck Lab

---

**Feito com ❤️ para desenvolvedores que amam simplicidade.**
