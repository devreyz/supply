# 🍱 Bento Toasts - Guia Completo

Sistema de notificações moderno inspirado no Vercel/Sonner com suporte a progresso, persistência e gestos.

## 🎯 Características

- ✅ **Visual Moderno**: Design Bento com bordas XL e sombras sutis
- ✅ **Progress Bar**: Acompanhe operações em tempo real
- ✅ **Persistência**: Toasts bloqueados até ação do sistema
- ✅ **Gestos**: Swipe para direita para fechar
- ✅ **Stacking 3D**: Empilhamento visual com escala e opacidade
- ✅ **Atualização Dinâmica**: Modifique toasts existentes
- ✅ **Ícones SVG**: Ícones modernos para cada tipo

## 📖 Uso Básico

### Toasts Simples

```javascript
// Formato simples (compatibilidade)
app.toast("Operação concluída!", "success");

// Novo formato com descrição
app.toastSuccess("Salvo!", "Seus dados foram atualizados");
app.toastError("Erro!", "Não foi possível conectar ao servidor");
app.toastWarning("Atenção!", "Você tem 5 mensagens não lidas");
app.toastInfo("Dica", "Use atalhos para navegar mais rápido");
```

### Toast com Opções Avançadas

```javascript
const id = app.toast({
  title: "Título",
  description: "Descrição detalhada",
  type: "success", // success, error, warning, info, loading
  duration: 5000, // ms (Infinity para não fechar)
  dismissible: true, // false bloqueia fechamento
  progress: 50, // Barra de progresso (0-100)
  onClose: () => console.log("Toast fechado"),
});
```

## 🎯 Casos de Uso

### 1. Download com Progress Bar

Simule um download mostrando progresso em tempo real:

```javascript
function downloadFile() {
  // Cria toast loading com progress inicial
  const id = app.toastLoading("Baixando arquivo...", "Conectando ao servidor", {
    progress: 0,
  });

  let progress = 0;
  const interval = setInterval(() => {
    progress += Math.floor(Math.random() * 15);

    if (progress >= 100) {
      progress = 100;
      clearInterval(interval);

      // Atualiza para sucesso e desbloqueia
      app.updateToast(id, {
        type: "success",
        title: "Download Completo",
        description: "O arquivo foi salvo com sucesso.",
        progress: 100,
        dismissible: true,
      });

      // Fecha automaticamente após 3s
      setTimeout(() => app.dismissToast(id), 3000);
    } else {
      // Atualiza progresso
      app.updateToast(id, {
        progress: progress,
        description: `Baixando... ${progress}%`,
      });
    }
  }, 400);
}
```

### 2. Operação Crítica Bloqueada

Para operações que não podem ser interrompidas:

```javascript
async function criticalUpdate() {
  // Toast bloqueado (não pode ser fechado pelo usuário)
  const id = app.toast({
    title: "Atualizando Sistema",
    description: "Não feche o navegador. Isso pode levar alguns minutos.",
    type: "loading",
    dismissible: false, // BLOQUEADO
    duration: Infinity,
  });

  try {
    await performCriticalOperation();

    // Desbloqueia e mostra sucesso
    app.updateToast(id, {
      type: "success",
      title: "Sistema Atualizado",
      description: "Todas as alterações foram aplicadas com sucesso.",
      dismissible: true,
    });

    setTimeout(() => app.dismissToast(id), 4000);
  } catch (error) {
    app.updateToast(id, {
      type: "error",
      title: "Falha na Atualização",
      description: error.message,
      dismissible: true,
    });
  }
}
```

### 3. Toast Stack 3D

Teste o empilhamento visual:

```javascript
function showMultipleToasts() {
  app.toastInfo("Primeira", "Toast 1", { duration: 5000 });
  setTimeout(
    () => app.toastSuccess("Segunda", "Toast 2", { duration: 5000 }),
    300
  );
  setTimeout(
    () => app.toastWarning("Terceira", "Toast 3", { duration: 5000 }),
    600
  );
  setTimeout(
    () => app.toastError("Quarta", "Toast 4", { duration: 5000 }),
    900
  );
}
```

### 4. Upload de Arquivos

```javascript
async function uploadFiles(files) {
  const id = app.toastLoading("Enviando arquivos...", "0 de " + files.length, {
    progress: 0,
  });

  for (let i = 0; i < files.length; i++) {
    await uploadFile(files[i]);

    const progress = Math.round(((i + 1) / files.length) * 100);
    app.updateToast(id, {
      progress: progress,
      description: `${i + 1} de ${files.length} enviados`,
    });
  }

  app.updateToast(id, {
    type: "success",
    title: "Upload Completo",
    description: `${files.length} arquivos enviados com sucesso`,
    dismissible: true,
  });

  setTimeout(() => app.dismissToast(id), 3000);
}
```

### 5. Transição de Estados

Mude o toast conforme o estado da operação:

```javascript
async function processPayment() {
  const id = app.toastInfo("Processando Pagamento", "Aguarde...");

  try {
    // Validando
    app.updateToast(id, {
      type: "loading",
      description: "Validando dados do cartão...",
    });
    await validateCard();

    // Processando
    app.updateToast(id, {
      description: "Enviando para operadora...",
    });
    await sendToProcessor();

    // Sucesso
    app.updateToast(id, {
      type: "success",
      title: "Pagamento Aprovado",
      description: "Seu pedido será enviado em breve",
    });
  } catch (error) {
    app.updateToast(id, {
      type: "error",
      title: "Pagamento Recusado",
      description: error.message,
    });
  }
}
```

## 🎨 Personalização CSS

### Cores Customizadas

```css
.toast-item[data-type="custom"] .toast-icon {
  color: #8b5cf6; /* Purple */
}

.toast-item[data-type="custom"] .toast-progress-fill {
  background: linear-gradient(90deg, #8b5cf6, #ec4899);
}
```

### Animação Customizada

```css
.toast-item[data-state="open"] {
  animation: slideInBounce 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

@keyframes slideInBounce {
  0% {
    transform: translateX(100%) scale(0.8);
    opacity: 0;
  }
  100% {
    transform: translateX(0) scale(1);
    opacity: 1;
  }
}
```

## 🔧 API Completa

### Métodos

```javascript
// Criar toast
const id = app.toast(options);

// Atalhos
app.toastSuccess(title, description, options);
app.toastError(title, description, options);
app.toastWarning(title, description, options);
app.toastInfo(title, description, options);
app.toastLoading(title, description, options);

// Atualizar toast existente
app.updateToast(id, updates);

// Fechar toast
app.dismissToast(id);
```

### Opções

| Propriedade   | Tipo     | Padrão | Descrição                              |
| ------------- | -------- | ------ | -------------------------------------- |
| `title`       | string   | -      | Título principal                       |
| `description` | string   | null   | Texto descritivo                       |
| `type`        | string   | 'info' | success, error, warning, info, loading |
| `duration`    | number   | 4000   | Duração em ms (Infinity = nunca fecha) |
| `dismissible` | boolean  | true   | Se pode ser fechado pelo usuário       |
| `progress`    | number   | null   | Barra de progresso 0-100               |
| `onClose`     | function | null   | Callback ao fechar                     |

### Updates

Todas as propriedades podem ser atualizadas via `updateToast()`:

```javascript
app.updateToast(id, {
  title: "Novo título",
  description: "Nova descrição",
  type: "success",
  progress: 75,
  dismissible: true,
});
```

## 🎯 Boas Práticas

### ✅ Faça

- Use `toastLoading()` para operações assíncronas longas
- Bloqueie toasts (`dismissible: false`) em operações críticas
- Mostre progresso em uploads/downloads
- Use descrições para contexto adicional
- Desbloqueie toasts quando concluir operação

### ❌ Evite

- Toasts muito longos (> 10s sem ser loading)
- Bloquear toasts sem necessidade
- Empilhar muitos toasts simultaneamente (max 3-4)
- Texto muito longo na descrição

## 🚀 Performance

- **Zero dependências**: Apenas vanilla JS
- **Lightweight**: ~2KB adicional ao framework
- **Hardware accelerated**: Usa `transform` e `opacity`
- **Will-change optimized**: Melhor performance em animações

## 📱 Responsividade

Toasts se adaptam automaticamente:

- Mobile: 100% width com margin
- Desktop: Max-width 356px
- Safe areas respeitadas (notch, etc)

## 🔗 Integração

### Com formulários

```javascript
async function handleSubmit(e) {
  e.preventDefault();

  const id = app.toastLoading("Salvando...", "Enviando dados");

  try {
    await api.save(formData);
    app.updateToast(id, {
      type: "success",
      title: "Salvo!",
      description: "Dados atualizados com sucesso",
      dismissible: true,
    });
  } catch (error) {
    app.updateToast(id, {
      type: "error",
      title: "Erro ao salvar",
      description: error.message,
      dismissible: true,
    });
  }
}
```

### Com WebSockets

````javascript
socket.on("notification", (data) => {
    app.toastInfo(data.title, data.message);
});

socket.on("progress", (data) => {
    app.updateToast(currentTaskId, {
        progress: data.progress,
        description: data.status,
    });
});

## 🧪 Testes e Debug

- Para testar toasts com progresso, use o demo em `basic/index.html` ou os exemplos em `examples/`.
- No Console do navegador, você pode criar toasts manualmente:

```javascript
const id = app.toastLoading('Teste', '0%', { progress: 0 });
app.updateToast(id, { progress: 50, description: '50%' });
app.updateToast(id, { type: 'success', title: 'Concluído', dismissible: true });
````

- Verifique logs no console para eventos de toast (ex.: `toast:open`, `toast:close`) se habilitados no `app`.

```

```
