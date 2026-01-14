
  <!-- =================== DRAWER =================== -->
  <div class="drawer" id="menu">
    <div class="drawer-header">
      <h2 style="margin: 0;">Menu</h2>
    </div>
    <div class="drawer-body" style="padding: 0;">
      <div class="list-item" data-go="home" data-close-overlay>
        <div class="list-item-icon">🏠</div>
        <div class="list-item-content">
          <div class="list-item-title">Home</div>
        </div>
      </div>
      <div class="list-item" data-go="navigation" data-close-overlay>
        <div class="list-item-icon">🧭</div>
        <div class="list-item-content">
          <div class="list-item-title">Navegação</div>
        </div>
      </div>
      <div class="list-item" data-go="components" data-close-overlay>
        <div class="list-item-icon">🎨</div>
        <div class="list-item-content">
          <div class="list-item-title">Componentes</div>
        </div>
      </div>
      <div class="list-item" data-go="storage" data-close-overlay>
        <div class="list-item-icon">💾</div>
        <div class="list-item-content">
          <div class="list-item-title">Storage</div>
        </div>
      </div>
      <div class="list-item" data-go="offline" data-close-overlay>
        <div class="list-item-icon">📋</div>
        <div class="list-item-content">
          <div class="list-item-title">Offline</div>
        </div>
      </div>
      <div class="list-item" data-go="pwa" data-close-overlay>
        <div class="list-item-icon">📱</div>
        <div class="list-item-content">
          <div class="list-item-title">PWA</div>
        </div>
      </div>
      <div class="divider"></div>
      <div class="list-item" onclick="toggleTheme()">
        <div class="list-item-icon">🌙</div>
        <div class="list-item-content">
          <div class="list-item-title">Tema Escuro</div>
        </div>
        <label class="toggle" style="pointer-events: none;">
          <input type="checkbox" class="toggle-input" id="theme-toggle">
          <span class="toggle-switch"></span>
        </label>
      </div>
    </div>
  </div>
