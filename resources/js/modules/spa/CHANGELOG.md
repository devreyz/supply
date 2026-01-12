# 🔧 Correções e Melhorias Aplicadas

## 📝 Unreleased - Documentação atualizada

- Atualização das documentações `README.md`, `docs/` e `TESTE_NAVEGACAO.md` com instruções de execução local, testes manuais e notas de migração.
- Adicionados trechos de Debug/Testing em `docs/BENTO_TOASTS.md` e `docs/DATA_BINDINGS.md`.

## ✨ v1.1.0 - REFATORAÇÃO COMPLETA: Toast Bento + Persistência + Data Bindings

### 🎨 Toast System Redesenhado (Estilo Bento)

Refatoração completa do sistema de toasts com visual moderno inspirado no Vercel/Sonner.

**Mudanças Visuais:**

- ✅ Bordas arredondadas XL (16px)
- ✅ Cards com background var(--spa-bg-card) e border sutil
- ✅ Ícones SVG modernos (feather icons)
- ✅ Tipografia aprimorada (título + descrição)
- ✅ Sombras sutis e transições suaves (cubic-bezier)

**Novas Funcionalidades:**

1. **Progress Bar**: Toasts podem mostrar progresso (0-100%)
2. **Persistência**: Toasts não-dismissible (bloqueados até ação do sistema)
3. **Atualização Dinâmica**: `updateToast(id, updates)` para modificar toasts existentes
4. **Loading State**: `toastLoading()` para operações assíncronas
5. **Atalhos Tipados**: `toastSuccess()`, `toastError()`, `toastWarning()`, `toastInfo()`

**API Nova:**

```javascript
// Simples (compatibilidade mantida)
app.toast("Mensagem", "success");

// Avançado com todas as opções
const id = app.toast({
  title: "Baixando...",
  description: "Conectando ao servidor",
  type: "loading",
  duration: Infinity,
  dismissible: false,
  progress: 0,
});

// Atualizar progresso
app.updateToast(id, {
  progress: 50,
  description: "Baixando... 50%",
});

// Desbloquear e finalizar
app.updateToast(id, {
  type: "success",
  title: "Completo!",
  dismissible: true,
});
```

### 🎯 Modal System com HTML Customizado

Modais agora suportam HTML customizado completo e botões configuráveis.

**Novo:**

- ✅ `html` property para conteúdo customizado
- ✅ `customButtons` array para botões personalizados
- ✅ `dismissible: false` para modais que só fecham com ação
- ✅ `width` configurável

**Exemplo:**

```javascript
const result = await app.modal({
  title: "🎨 Escolha uma Opção",
  html: `
        <div class="options-grid">
            <div class="option-card">Premium</div>
            <div class="option-card">Básico</div>
        </div>
    `,
  width: "500px",
  dismissible: false, // Não fecha com ESC ou backdrop
  customButtons: [
    { text: "Cancelar", class: "btn btn-outline", value: "cancel" },
    { text: "Confirmar", class: "btn btn-primary", value: "ok" },
  ],
});

if (result === "ok") {
  // Usuário confirmou
}
```

### 🔗 Sistema de Data Binding Página-Elemento

Novo sistema declarativo para vincular elementos com páginas específicas.

**Data Attributes:**

- `data-show-on="page1,page2"` - Mostra elemento apenas nessas páginas
- `data-hide-on="page1,page2"` - Esconde elemento nessas páginas
- `data-active-on="page1"` - Adiciona classe `active` quando na página
- `data-go="page"` - Automaticamente fica `active` quando na página

**Exemplo Prático:**

```html
<!-- Bottom Nav aparece apenas em páginas principais -->
<nav class="bottom-nav" data-show-on="home,components,storage,pwa">
  <button data-go="home" data-active-on="home">Home</button>
  <button data-go="components" data-active-on="components">UI</button>
</nav>

<!-- Header especial apenas na home -->
<div class="hero-header" data-show-on="home">
  <h1>Bem-vindo!</h1>
</div>

<!-- Botão de ajuda escondido na página de login -->
<button class="help-btn" data-hide-on="login,register">?</button>
```

**Comportamento Automático:**

- ✅ Bottom nav automaticamente marca botão ativo
- ✅ Elementos aparecem/desaparecem conforme navegação
- ✅ Classes `active` aplicadas automaticamente
- ✅ Zero JavaScript manual necessário

### 🎨 CSS Bento Design

Bordas mais arredondadas em todo o framework:

- `--spa-radius`: 0.5rem → 0.75rem
- `--spa-radius-md`: 0.75rem → 1rem
- `--spa-radius-lg`: 1rem → 1.25rem
- Toasts com 16px (xl)
- Cards e modais mais suaves

### 📦 Compatibilidade

**Quebra de Compatibilidade:**

- ⚠️ `toast(message, type, duration)` ainda funciona mas é deprecated
- ⚠️ Recomendado migrar para novo formato: `toast({ title, description, type })`

**Migração:**

```javascript
// ANTES (ainda funciona)
app.toast("Salvo!", "success", 3000);

// DEPOIS (recomendado)
app.toastSuccess("Salvo!", "Dados atualizados");
```

### 🧪 Novos Demos

Adicionados 3 exemplos interativos no `index.html`:

1. **Download com Progress**: Simula download com barra de progresso
2. **Tarefa Bloqueada**: Toast persistente que só fecha após conclusão
3. **Bottom Nav com Binding**: Navegação automática com data attributes

---

## ✅ v1.0.1 - CORREÇÃO CRÍTICA: Navegação com History API

### 🐛 Problema Identificado

Após recarregar a página, a navegação com as setas do navegador (botão voltar) não funcionava corretamente. O histórico estava sendo destruído ou não persistia após reload.

### 🔍 Causa Raiz

1. **Limpeza indevida do histórico**: Ao navegar para home, o código usava `history.replaceState()` que substituía o histórico em vez de adicionar
2. **Lógica hierárquica no back()**: O método `back()` usava lógica customizada em vez do `history.back()` nativo
3. **Estado inicial conflitante**: O `_setupInitialHistory()` adicionava estados extras desnecessários que confundiam o histórico

### ✅ Soluções Implementadas

#### 1. Sempre Usar `pushState` (exceto no init)

```javascript
// ANTES: Destruía o histórico ao ir para home
if (isGoingHome) {
    history.replaceState(...);
}

// DEPOIS: Sempre adiciona ao histórico
history.pushState(state, "", `#${id}`);
```

#### 2. Método `back()` Usa History Nativo

```javascript
// ANTES: Lógica customizada que não funcionava após reload
if (currentHierarchy?.level === "primary") {
  this.go(this.config.homePage);
  return;
}

// DEPOIS: Usa o history.back() nativo do navegador
history.back();
```

#### 3. Setup Inicial Preserva Histórico

```javascript
// ANTES: Sempre substituía o state
history.replaceState(...);

// DEPOIS: Só substitui se não houver state
if (!history.state || !history.state.page) {
    history.replaceState(...);
}
```

### 🧪 Como Testar

1. **Navegação Normal**:

   ```
   Home → Navigation → Components
   ```

   - Clique em voltar (seta do navegador ou botão físico)
   - Deve voltar: Components → Navigation → Home

2. **Após Reload**:

   ```
   Home → Navigation → Components → [RELOAD F5]
   ```

   - Clique em voltar
   - Deve voltar para Navigation (mesmo após reload!)

3. **Histórico do Navegador**:
   - Navegue entre várias páginas
   - Use as setas do navegador (← →)
   - Deve funcionar perfeitamente

### 📊 Comparação

| Cenário                      | Antes       | Depois      |
| ---------------------------- | ----------- | ----------- |
| Voltar após navegação normal | ✅ OK       | ✅ OK       |
| Voltar após reload           | ❌ Quebrado | ✅ Funciona |
| Setas do navegador           | ❌ Limitado | ✅ Funciona |
| Botão físico do celular      | ❌ Limitado | ✅ Funciona |
| Histórico preservado         | ❌ Não      | ✅ Sim      |

### 🎯 Benefícios

- ✅ **Navegação Natural**: Funciona como qualquer site/app web padrão
- ✅ **Compatibilidade**: Funciona em todos navegadores modernos
- ✅ **Experiência Mobile**: Botão voltar físico funciona perfeitamente
- ✅ **Após Reload**: Mantém contexto e permite voltar normalmente
- ✅ **Desenvolvimento**: Mais fácil debugar com DevTools → Application → History

---

## ✅ Problemas Corrigidos (versão anterior)

### 1. Navegação com Botão Voltar do Navegador

- **Problema**: Após reload da página, o botão voltar não funcionava
- **Solução**: Melhorado o handler `_handlePopState` para detectar o hash da URL quando não há state no history
- **Arquivo**: `src/core/spa.js`

### 2. Erro IndexedDB - Tabela Não Encontrada

- **Problema**: Tentativa de inserir dados antes da tabela ser criada
- **Solução**: Adiciona versão timestamp para forçar upgrade do banco e aguarda inicialização
- **Arquivo**: `examples/basic/index.html`

### 3. Service Worker - Caminho 404

- **Problema**: SW procurando em `/service-worker.js` em vez de `./service-worker.js`
- **Solução**: Configuração correta do caminho relativo no PWAInstaller
- **Arquivo**: `src/pwa/install.js`

### 4. Meta Tag Deprecated

- **Problema**: Warning sobre meta tag obsoleta do Apple
- **Solução**: Adicionada meta tag `mobile-web-app-capable` (padrão atual)
- **Arquivo**: `examples/basic/index.html`

### 5. Ícones PNG Faltando

- **Solução**: Adicionado guia para gerar ícones + SVG placeholder
- **Arquivos**: `img/GENERATE_ICONS.md`, `img/icon-192.svg`

## 🚀 Novos Recursos Implementados

### 1. Sistema de Toasts Melhorado

**Características:**

- ✅ Gestos de arrasto para fechar (swipe left/right)
- ✅ Empilhamento 3D com perspectiva
- ✅ Botão de fechar individual
- ✅ Animações suaves de entrada/saída
- ✅ Opacidade e escala gradual nos itens empilhados

**Como usar:**

```javascript
app.toast("Mensagem", "success", 3000);

// Toast infinito (não desaparece automaticamente)
app.toast("Permanente", "info", 0);

// Teste de stack 3D
testToastStack(); // Cria 4 toasts empilhados
```

**Métodos internos:**

- `_removeToast(toast)` - Remove com animação
- `_updateToastStack()` - Atualiza posições 3D
- `_setupToastGestures(toast)` - Configura swipe

### 2. Mensagens Overlay no Centro

**Características:**

- ✅ Exibidas no centro da tela
- ✅ Backdrop semi-transparente com blur
- ✅ Animação bounce ao aparecer
- ✅ Auto-fechamento após duração

**Como usar:**

```javascript
app.message("Salvo com sucesso!", "success", 2000);
app.message("Erro ao processar", "error");
app.message("Aguarde...", "info", 3000);
```

### 3. Modais Personalizados

**Características:**

- ✅ HTML customizado
- ✅ Botões personalizáveis
- ✅ Largura configurável
- ✅ Controle de backdrop
- ✅ Suporte a ESC para fechar

**Como usar:**

```javascript
// Modal com HTML customizado
const result = await app.modal({
  title: "🎨 Título",
  html: `<div>Seu HTML aqui</div>`,
  type: "custom",
  width: "600px",
  closeOnBackdrop: true,
  customButtons: [
    { text: "Cancelar", class: "btn btn-outline", value: "cancel" },
    { text: "OK", class: "btn btn-primary", value: "ok" },
  ],
});

if (result === "ok") {
  // Usuário clicou em OK
}
```

## 📝 Arquivos Modificados

1. **src/core/spa.js**

   - Melhorado `_handlePopState()` para funcionar após reload
   - Refatorado `toast()` com gestos e 3D stacking
   - Novo método `message()` para overlay central
   - Melhorado `modal()` com suporte a HTML customizado

2. **src/pwa/install.js**

   - Corrigido caminho do service worker
   - Adicionado suporte a configuração do caminho via options

3. **dist/spa.css**

   - Novos estilos para toast com gestos (.toast-close)
   - Estilos para .message-overlay e .message-box
   - Animações @keyframes modalEnter/modalExit
   - Backdrop com blur effect

4. **examples/basic/index.html**
   - Adicionada meta tag mobile-web-app-capable
   - Corrigida inicialização do IndexedDB
   - Novos botões de teste (Stack 3D, Mensagens, Custom Modal)
   - Novas funções: showCustomModal(), testToastStack()

## 🎨 CSS Adicionado

```css
/* Toast com gestos */
.toast-item {
  cursor: grab;
  user-select: none;
  will-change: transform, opacity;
}

.toast-close {
  /* Botão X para fechar */
}

/* Message Overlay */
.message-overlay {
  backdrop-filter: blur(2px);
  /* Centro da tela, semi-transparente */
}

.message-box {
  /* Card central com animação bounce */
}

/* Modal Animations */
@keyframes modalEnter {
  /* scale + translateY */
}
@keyframes modalExit {
  /* inverso */
}
```

## 🧪 Como Testar

1. **Toasts com Gestos:**

   - Vá para "Componentes" > Toasts
   - Clique em "Stack 3D" para ver empilhamento
   - Arraste um toast para os lados para fechar
   - Clique no X para fechar individual

2. **Mensagens Centro:**

   - Vá para "Componentes" > Mensagens Centro
   - Clique em "Mensagem" ou "Erro"
   - Observe animação bounce no centro da tela

3. **Modal Personalizado:**

   - Vá para "Componentes" > Modais
   - Clique em "Custom"
   - Interaja com o conteúdo HTML customizado

4. **Navegação com Botão Voltar:**

   - Navegue entre páginas
   - Recarregue a página (F5)
   - Use botão voltar do navegador ou gesto de voltar
   - Deve voltar corretamente para a página anterior

5. **IndexedDB:**
   - Vá para "Storage" > IndexedDB
   - Adicione itens
   - Verifique que não há mais erros no console

## 📊 Comparação Antes vs Depois

| Recurso          | Antes                  | Depois                          |
| ---------------- | ---------------------- | ------------------------------- |
| Toast            | Simples, sem interação | Gestos swipe, empilhamento 3D   |
| Modal            | Básico                 | HTML customizado, botões config |
| Mensagens        | Só toasts              | Toast + Overlay centro          |
| Navegação reload | ❌ Quebrava            | ✅ Funciona                     |
| IndexedDB init   | ❌ Erro                | ✅ OK                           |
| Service Worker   | ❌ 404                 | ✅ Registrado                   |

## 🎯 Próximos Passos Sugeridos

1. Gerar ícones PNG reais (usar ferramenta sugerida)
2. Testar PWA instalado em dispositivo móvel
3. Adicionar mais animações de toast (slide from top, bottom)
4. Implementar fila de mensagens overlay (não sobrepor)
5. Adicionar testes automatizados

## 📚 Documentação Atualizada

Todos os novos métodos estão documentados inline com JSDoc.
Veja os comentários no código para mais detalhes.
