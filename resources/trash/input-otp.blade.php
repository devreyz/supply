<div x-data="otpInput()" class="flex flex-col items-center space-y-4">
  <form @submit.prevent="submitCode" class="w-full max-w-sm mx-auto">
    <!-- Linha de Inputs OTP -->
    <div class="flex justify-center space-x-2">
      <template x-for="(digit, index) in otp" :key="index">
        <input
          type="text"
          maxlength="1"
          x-model="otp[index]"
          @input="onInput($event, index)"
          @keydown.backspace="onBackspace($event, index)"
          :ref="'otp' + index"
          class="w-12 h-12 text-center text-2xl border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
          placeholder="-"
        >
      </template>
    </div>

    <!-- Botão de envio -->
    <button 
      type="submit"
      class="mt-4 w-full bg-primary-dark text-white py-3 rounded-lg hover:bg-primary transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
    >
      Enviar Código
    </button>

    <!-- Input oculto para envio (opcional) -->
    <input type="hidden" name="otp" :value="joinedOtp">
  </form>
</div>

<script>
  function otpInput() {
    return {
      // Array com 6 posições para os dígitos do OTP
      otp: ['', '', '', '', '', ''],

      // Propriedade computada que junta os 6 caracteres
      get joinedOtp() {
        return this.otp.join('');
      },

      // Ao digitar, converte para maiúscula, valida e foca no próximo input
      onInput(event, index) {
        let value = event.target.value.toUpperCase();
        // Permite apenas letras A-Z e números 0-9
        if (!/^[A-Z0-9]$/.test(value)) {
          this.otp[index] = '';
          return;
        }
        this.otp[index] = value;

        // Aguarda a atualização do DOM e foca no próximo input, se existir
        this.$nextTick(() => {
          if (index < this.otp.length - 1) {
            let nextInput = this.$refs['otp' + (index + 1)];
            if (Array.isArray(nextInput)) {
              nextInput[0].focus();
            } else {
              nextInput.focus();
            }
          }
        });
      },

      // Ao pressionar backspace em um campo vazio, foca no input anterior
      onBackspace(event, index) {
        if (!event.target.value && index > 0) {
          this.$nextTick(() => {
            let prevInput = this.$refs['otp' + (index - 1)];
            if (Array.isArray(prevInput)) {
              prevInput[0].focus();
            } else {
              prevInput.focus();
            }
          });
        }
      },

      // Exemplo de envio do código completo
      submitCode() {
        // Aqui você pode integrar a requisição com AJAX, Livewire, etc.
        alert("Código OTP: " + this.joinedOtp);
      }
    }
  }
</script>
