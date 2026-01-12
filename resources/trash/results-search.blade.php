<!-- Container do Modal Único -->
<div x-data="resultSearchModal()" x-init="init()" x-cloak>
  <!-- Overlay Fixo (sem animação de blur, apenas na cor) -->
  <div 
    x-show="open" 
    class="fixed inset-0 z-[9998] backdrop-blur-md bg-foreground/70"
    x-transition:enter="transition-colors duration-300"
    x-transition:enter-start="bg-transparent"
    x-transition:enter-end="bg-foreground/70"
    x-transition:leave="transition-colors duration-200"
    x-transition:leave-start="bg-foreground/70"
    x-transition:leave-end="bg-transparent"
    @click="close" 
  ></div>
  
  <!-- Modal (Card) Animado -->
  <div 
    id="searchResultModals"
    x-show="open"
    x-transition:enter="transition ease-out duration-300 transform"
    x-transition:enter-start="opacity-0 translate-y-16 scale-90"
    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
    x-transition:leave="transition ease-in duration-200 transform"
    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
    x-transition:leave-end="opacity-0 translate-y-16 scale-90"
    class="fixed inset-0 flex items-center justify-center z-[9999]"
  >
    <div class="bg-card p-10 rounded-xl shadow-2xl relative">
      <!-- Botão para fechar o modal -->
      <button 
        @click="close" 
        class="absolute top-2 right-2 text-text-secondary hover:text-primary"
      >
        <svg class="w-10 h-10">
          <use xlink:href="#icon-close"></use>
        </svg>
      </button>
      <h3 class="text-3xl font-bold mb-4">Consultar Resultados</h3>
      <form @submit.prevent="submitForm" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-text-secondary mb-1">
            Código do Pedido
          </label>
          <!-- Adicione o x-ref para o primeiro input -->
          <input 
  type="text" 
  x-model="code" 
  x-ref="firstInput"
  maxlength="6"
  @input="code = $event.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0,6)"
  class="w-full px-4 py-2 rounded-lg border border-border focus:ring-2 focus:ring-primary" 
  placeholder="Ex: SV1234" 
  required
>

        </div>
       <div>
  <label class="block text-sm font-medium text-text-secondary mb-1">
    Data de Nascimento
  </label>
  <input 
    type="text" 
    x-model="birthdate" 
    @input="birthdate = formatBirthdate($event.target.value)"
    class="w-full px-4 py-2 rounded-lg border border-border focus:ring-2 focus:ring-primary" 
    placeholder="dd/mm/aaaa" 
    required
  >
</div>

        <button 
          type="submit" 
          class="w-full bg-primary-dark text-white py-3 rounded-lg hover:bg-primary transition-colors"
        >
          Buscar Resultados
        </button>
        <!-- Mensagens de Status -->
        <div x-show="loading" class="text-primary flex items-center mt-2">
          <svg class="animate-spin h-5 w-5 mr-2" viewBox="0 0 24 24">
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
          </svg>
          Buscando resultados...
        </div>
        <div x-show="error" class="text-red-500 mt-2">
          ⚠️ Erro ao buscar resultados. Verifique os dados e tente novamente.
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  function resultSearchModal() {
    return {
      open: false,
      code: '',
      birthdate: '',
      loading: false,
      error: false,
      // Formata o valor digitado para o padrão dd/mm/aaaa
    formatBirthdate(value) {
      // Remove qualquer caractere que não seja número
      let v = value.replace(/\D/g, '');
      // Insere a primeira barra após o dia (2 dígitos)
      if (v.length > 2) {
        v = v.slice(0, 2) + '/' + v.slice(2);
      }
      // Insere a segunda barra após o mês (mais 2 dígitos)
      if (v.length > 5) {
        v = v.slice(0, 5) + '/' + v.slice(5);
      }
      // Limita a 10 caracteres (dd/mm/aaaa)
      return v.slice(0, 10);
    },
      init() {
        // Bloqueia o scroll ao abrir o modal e aplica o foco
        this.$watch('open', (value) => {
          if (value) {
            document.body.classList.add('overflow-hidden');
            this.$nextTick(() => {
              this.$refs.firstInput.focus();
            });
          } else {
            document.body.classList.remove('overflow-hidden');
          }
        });
        // Captura o evento ESC para fechar
      window.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && this.open) {
          this.close();
        }
      });

      // Captura o evento de botão voltar no navegador
      window.addEventListener('popstate', () => {
        if (this.open) {
          this.close();
        }
      });
    
        window.eventEmitter.on('openResultsModal', () => {
          this.open = true;
          history.pushState({ modalOpen: true }, "");
        });
      },
      close() {
        this.open = false;
        history.back(); 
      },
      async submitForm() {
        this.loading = true;
        this.error = false;
        try {
          // Integração com a API (ajuste a URL conforme necessário)
          const response = await fetch('http://localhost/', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json'
            },
            body: JSON.stringify({
              codigo: this.code,
              nascimento: this.birthdate
            })
          });
          if (!response.ok) throw new Error('Erro na requisição');
          const data = await response.json();
          // Redireciona para os resultados
          window.location.href = data.url_resultado;
        } catch (error) {
          this.error = true;
          console.error('Erro na busca:', error);
        } finally {
          this.loading = false;
          this.close();
        }
      }
    }
  }
</script>
