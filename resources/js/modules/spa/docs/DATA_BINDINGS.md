# 🔗 Sistema de Data Bindings Página-Elemento

Sistema declarativo para vincular elementos HTML com páginas específicas, sem necessidade de JavaScript manual.

## 🎯 Objetivo

Controlar visibilidade e estado de elementos baseado na página atual, de forma automática e declarativa.

## 📖 Data Attributes

### `data-show-on`

Mostra o elemento **apenas** nas páginas especificadas.

```html
<!-- Aparece apenas em home e dashboard -->
<div data-show-on="home,dashboard">
  <h1>Bem-vindo!</h1>
</div>

<!-- Bottom nav visível apenas em páginas principais -->
<nav class="bottom-nav" data-show-on="home,products,cart,profile">
  <!-- ... -->
</nav>
```

### `data-hide-on`

Esconde o elemento nas páginas especificadas.

```html
<!-- Esconde nas páginas de auth -->
<button class="help-btn" data-hide-on="login,register,forgot-password">
  Ajuda
</button>

<!-- Header diferente no onboarding -->
<header data-hide-on="onboarding-1,onboarding-2,onboarding-3">
  <img src="logo.svg" alt="Logo" />
</header>
```

### `data-active-on`

Adiciona classe `active` quando na página especificada.

```html
<!-- Tabs com estado ativo automático -->
<div class="tabs">
  <button data-active-on="profile-info" data-go="profile-info">Info</button>
  <button data-active-on="profile-security" data-go="profile-security">
    Segurança
  </button>
  <button data-active-on="profile-privacy" data-go="profile-privacy">
    Privacidade
  </button>
</div>

<style>
  .tabs button.active {
    color: var(--spa-primary);
    border-bottom: 2px solid var(--spa-primary);
  }
</style>
```

### `data-go` (Automático)

Botões com `data-go` **automaticamente** recebem classe `active` quando estão na página correspondente.

```html
<nav class="bottom-nav">
  <!-- Automaticamente fica .active quando page === "home" -->
  <button data-go="home">
    <svg>...</svg>
    <span>Home</span>
  </button>

  <button data-go="search">
    <svg>...</svg>
    <span>Buscar</span>
  </button>
</nav>
```

## 🎯 Casos de Uso

### 1. Bottom Navigation Contextual

Mostre bottom nav apenas em páginas principais:

```html
<nav class="bottom-nav" data-show-on="home,explore,notifications,profile">
  <button data-go="home">
    <svg>...</svg>
    <span>Início</span>
  </button>
  <button data-go="explore">
    <svg>...</svg>
    <span>Explorar</span>
  </button>
  <button data-go="notifications">
    <svg>...</svg>
    <span>Notificações</span>
  </button>
  <button data-go="profile">
    <svg>...</svg>
    <span>Perfil</span>
  </button>
</nav>

<style>
  .bottom-nav button.active {
    color: var(--spa-primary);
  }
  .bottom-nav button.active svg {
    fill: var(--spa-primary);
  }
</style>
```

### 2. Header Condicional

Headers diferentes para diferentes contextos:

```html
<!-- Header padrão (escondido em auth e onboarding) -->
<header
  class="app-header"
  data-hide-on="login,register,onboarding-1,onboarding-2"
>
  <button data-back>←</button>
  <h1>Título</h1>
  <button data-sheet="menu">☰</button>
</header>

<!-- Header mínimo para auth -->
<header class="auth-header" data-show-on="login,register">
  <img src="logo.svg" alt="Logo" />
</header>

<!-- Sem header no onboarding -->
```

### 3. Tabs com Estado Ativo

Sistema de tabs declarativo:

```html
<div class="profile-container">
  <!-- Tabs -->
  <div
    class="tabs"
    data-show-on="profile-info,profile-security,profile-privacy"
  >
    <button class="tab" data-active-on="profile-info" data-go="profile-info">
      Informações
    </button>
    <button
      class="tab"
      data-active-on="profile-security"
      data-go="profile-security"
    >
      Segurança
    </button>
    <button
      class="tab"
      data-active-on="profile-privacy"
      data-go="profile-privacy"
    >
      Privacidade
    </button>
  </div>
</div>

<style>
  .tab {
    padding: 12px 24px;
    border-bottom: 2px solid transparent;
    transition: all 0.2s;
  }
  .tab.active {
    color: var(--spa-primary);
    border-bottom-color: var(--spa-primary);
  }
</style>
```

### 4. Botões de Ação Contextuais

Mostre botões apenas onde fazem sentido:

```html
<!-- Botão FAB de criar apenas em listas -->
<button class="fab" data-show-on="products,contacts,notes">
  <svg>+</svg>
</button>

<!-- Botão de filtro apenas em páginas com lista -->
<button class="filter-btn" data-show-on="products,orders,search">
  <svg>🔍</svg>
  Filtros
</button>

<!-- Botão de compartilhar apenas em detalhes -->
<button class="share-btn" data-show-on="product-detail,article-detail">
  <svg>📤</svg>
</button>
```

### 5. Breadcrumbs Dinâmicos

```html
<nav class="breadcrumbs" data-hide-on="home,login,register">
  <a data-go="home">Home</a>
  <span>/</span>

  <a data-go="products" data-show-on="product-detail,product-edit">
    Produtos
  </a>

  <span data-show-on="product-detail,product-edit">/</span>

  <span data-show-on="product-detail">Detalhes</span>
  <span data-show-on="product-edit">Editar</span>
</nav>
```

### 6. Sidebar Condicional

Desktop sidebar que aparece apenas em certas páginas:

```html
<aside class="sidebar" data-show-on="dashboard,analytics,reports">
  <nav>
    <a data-active-on="dashboard" data-go="dashboard"> Dashboard </a>
    <a data-active-on="analytics" data-go="analytics"> Analytics </a>
    <a data-active-on="reports" data-go="reports"> Relatórios </a>
  </nav>
</aside>

<style>
  .sidebar a.active {
    background: var(--spa-bg-card);
    color: var(--spa-primary);
    border-left: 3px solid var(--spa-primary);
  }
</style>
```

### 7. Stepper/Wizard

Progress indicator para fluxos multi-etapa:

```html
<div class="stepper" data-show-on="checkout-1,checkout-2,checkout-3">
  <div class="step" data-active-on="checkout-1"><span>1</span> Carrinho</div>
  <div class="step" data-active-on="checkout-2"><span>2</span> Endereço</div>
  <div class="step" data-active-on="checkout-3"><span>3</span> Pagamento</div>
</div>

<style>
  .step {
    opacity: 0.5;
  }
  .step.active {
    opacity: 1;
    font-weight: 600;
  }
  .step.active span {
    background: var(--spa-primary);
    color: white;
  }
</style>
```

### 8. Floating Action Button Contextual

```html
<!-- FAB com ações diferentes por página -->
<button class="fab" data-show-on="contacts" onclick="addContact()">
  + Contato
</button>

<button class="fab" data-show-on="notes" onclick="addNote()">+ Nota</button>

<button class="fab" data-show-on="products" onclick="addProduct()">
  + Produto
</button>
```

## 🎨 Combinando Attributes

Você pode combinar múltiplos attributes:

```html
<!-- Mostra em algumas páginas E fica ativo em página específica -->
<nav data-show-on="home,products,cart">
  <button data-go="home" data-active-on="home">Home</button>
  <button data-go="products" data-active-on="products">Produtos</button>
  <button data-go="cart" data-active-on="cart">Carrinho</button>
</nav>

<!-- Esconde em auth MAS mostra botão de ajuda -->
<div data-hide-on="login,register">
  <button class="help" data-show-on="login,register">Precisa de ajuda?</button>
</div>
```

## 🔧 Como Funciona

O framework chama automaticamente `_updatePageBindings()` após cada navegação (`page:enter` event).

```javascript
// Executado automaticamente após cada navegação
_updatePageBindings() {
    const currentPage = this.current;

    // 1. data-show-on
    document.querySelectorAll("[data-show-on]").forEach(el => {
        const pages = el.dataset.showOn.split(",").map(p => p.trim());
        el.style.display = pages.includes(currentPage) ? "" : "none";
    });

    // 2. data-hide-on
    document.querySelectorAll("[data-hide-on]").forEach(el => {
        const pages = el.dataset.hideOn.split(",").map(p => p.trim());
        el.style.display = pages.includes(currentPage) ? "none" : "";
    });

    // 3. data-active-on
    document.querySelectorAll("[data-active-on]").forEach(el => {
        const pages = el.dataset.activeOn.split(",").map(p => p.trim());
        el.classList.toggle("active", pages.includes(currentPage));
    });

    // 4. data-go (automático)
    document.querySelectorAll("[data-go]").forEach(el => {
        el.classList.toggle("active", el.dataset.go === currentPage);
    });
}
```

## 🚀 Performance

- **Zero overhead**: Apenas selectors CSS nativos
- **Lazy execution**: Só executa na mudança de página
- **Event-driven**: Responde ao evento `page:enter`

## 🧭 Debugging

- Use o evento `page:enter` para inspecionar bindings ao navegar:

```javascript
document.addEventListener("page:enter", (e) => {
  console.log("Página atual:", e.detail?.page || document.body.dataset.page);
});
```

- Verifique atributos `data-show-on`, `data-hide-on` e `data-active-on` no DevTools e confirme que os valores correspondem às páginas esperadas.

## 🎯 Boas Práticas

### ✅ Faça

```html
<!-- Use vírgulas para múltiplas páginas -->
<nav data-show-on="home,products,cart"></nav>

<!-- Combine com classes para estilos -->
<button data-go="home" class="nav-item">Home</button>

<!-- Use nomes descritivos de páginas -->
<div data-show-on="user-profile-edit,user-profile-view"></div>
```

### ❌ Evite

```html
<!-- NÃO use espaços sem vírgulas -->
<nav data-show-on="home products"></nav>

<!-- NÃO duplique lógica em JS -->
<button data-go="home" onclick="markActive(this)"></button>

<!-- NÃO use IDs genéricos -->
<div data-show-on="page1,page2"></div>
```

## 🔗 Integração com Outros Sistemas

### Com Permissões

```html
<!-- Mostra apenas se admin E na página certa -->
<button
  data-show-on="users,settings"
  data-permission="admin"
  data-go="admin-panel"
>
  Admin
</button>

<script>
  // Custom logic
  if (!user.hasPermission("admin")) {
    document.querySelector('[data-permission="admin"]').remove();
  }
</script>
```

### Com Temas

```css
/* Estilo ativo diferente por tema */
.dark .nav-item.active {
  color: #60a5fa;
  background: rgba(96, 165, 250, 0.1);
}

.light .nav-item.active {
  color: #2563eb;
  background: rgba(37, 99, 235, 0.1);
}
```

## 📱 Responsividade

```html
<!-- Desktop: sidebar | Mobile: bottom nav -->
<aside class="sidebar desktop-only" data-show-on="home,products">
  <!-- ... -->
</aside>

<nav class="bottom-nav mobile-only" data-show-on="home,products">
  <!-- ... -->
</nav>

<style>
  @media (max-width: 768px) {
    .desktop-only {
      display: none !important;
    }
  }
  @media (min-width: 769px) {
    .mobile-only {
      display: none !important;
    }
  }
</style>
```

## 🎓 Exemplos Completos

Ver arquivo `examples/basic/index.html` para implementações reais de:

- Bottom navigation com binding automático
- Headers condicionais
- Tabs dinâmicos
- Sidebars contextuais
