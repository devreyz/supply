  <!-- =================== USER SETTINGS SHEET =================== -->
  <div class="bottom-sheet" id="user-settings">
    <div class="grabber-handle">
      <div class="grabber-bar"></div>
    </div>
    
    <div class="sheet-body p-0">
      <!-- User Profile Header -->
      <div class="p-md flex items-center gap-md">
        <div class="avatar avatar-lg shadow-sm" style="background: var(--spa-primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.2rem;">
          {{-- User image avatar --}}
          @if(auth()->user() && auth()->user()->avatar)
            <img src="{{ auth()->user()->avatar }}" alt="Avatar" class="rounded-full w-full h-full object-cover"/>
          @else
            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
          @endif
        </div>
        <div class="flex-1">
          <div class="font-bold text-primary" style="font-size: 1.1rem;">{{ auth()->user()->name ?? 'Usuário' }}</div>
          <div class="text-muted" style="font-size: 0.85rem;">{{ auth()->user()->email ?? 'usuario@email.com' }}</div>
        </div>
        
      </div>

      <div class="divider m-0"></div>

      <!-- Menu Options -->
      <div class="p-sm">
        <div class="text-muted font-semibold px-md py-sm" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">Minha Conta</div>
        
        <div class="list-item" data-go="profile">
          <div class="list-item-icon"><i data-lucide="user" style="width: 1.2rem; height: 1.2rem;"></i></div>
          <div class="list-item-content">
            <div class="list-item-title">Meus Dados</div>
            <div class="list-item-subtitle">Editar informações do perfil</div>
          </div>
          <div class="list-item-action"><i data-lucide="chevron-right" style="width: 1rem; height: 1rem;"></i></div>
        </div>

        <div class="list-item" data-go="security">
          <div class="list-item-icon"><i data-lucide="shield-check" style="width: 1.2rem; height: 1.2rem;"></i></div>
          <div class="list-item-content">
            <div class="list-item-title">Segurança</div>
            <div class="list-item-subtitle">Senha e autenticação</div>
          </div>
          <div class="list-item-action"><i data-lucide="chevron-right" style="width: 1rem; height: 1rem;"></i></div>
        </div>

        <div class="text-muted font-semibold px-md py-sm mt-sm" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">Preferências</div>

        <div class="list-item" id="btn-toggle-theme">
          <div class="list-item-icon"><i data-lucide="moon" style="width: 1.2rem; height: 1.2rem;"></i></div>
          <div class="list-item-content">
            <div class="list-item-title">Tema Escuro</div>
            <div class="list-item-subtitle">Alternar modo claro/escuro</div>
          </div>
          <label class="toggle" style="pointer-events: none;">
            <input type="checkbox" class="toggle-input" id="theme-toggle-sheet">
            <span class="toggle-switch"></span>
          </label>
        </div>

        <div class="list-item">
          <div class="list-item-icon"><i data-lucide="layers" style="width: 1.2rem; height: 1.2rem;"></i></div>
          <div class="list-item-content">
            <div class="list-item-title">Animação</div>
            <div class="list-item-subtitle">Transição entre páginas</div>
          </div>
          <select class="input-field animation-select" style="width: auto; border: none; background: transparent; font-weight: 600; color: var(--spa-primary);">
            <option value="fade">Fade</option>
            <option value="slide">Slide</option>
            <option value="stack">Stack</option>
            <option value="zoom">Zoom</option>
            <option value="flip">Flip</option>
            <option value="cube">Cube</option>
          </select>
        </div>

        <div class="text-muted font-semibold px-md py-sm mt-sm" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">Outros</div>

        <div class="list-item" data-go="help">
          <div class="list-item-icon"><i data-lucide="help-circle" style="width: 1.2rem; height: 1.2rem;"></i></div>
          <div class="list-item-content">
            <div class="list-item-title">Ajuda & Suporte</div>
          </div>
        </div>

        <div class="list-item mt-md" id="btn-logout-settings" style="color: var(--spa-error); cursor: pointer;">
          <div class="list-item-icon" style="color: var(--spa-error);"><i data-lucide="log-out" style="width: 1.2rem; height: 1.2rem;"></i></div>
          <div class="list-item-content">
            <div class="list-item-title font-bold">Sair da Conta</div>
          </div>
        </div>
      </div>
      
      <div class="p-md text-center text-muted" style="font-size: 0.7rem; opacity: 0.5;">
        Tymely v4.0.0 • Made with ❤️
      </div>
    </div>
  </div>
