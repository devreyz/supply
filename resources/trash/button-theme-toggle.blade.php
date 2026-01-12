<button id="button-theme-toggle" class="text-xl px-2" type="button" aria-label="Toggle Theme">
  <i class="fi fi-rr-sun" aria-hidden="true"></i>
  <span class="sr-only">Toggle Theme</span>
  {{$slot}}
</button>

<script type="text/javascript" charset="utf-8">
 
  window.addEventListener('DOMContentLoaded', () => {
    setTheme(localStorage.getItem('theme') === 'dark');
    const btnThemeToggle = document.getElementById('button-theme-toggle');
    if (btnThemeToggle) {
      btnThemeToggle.addEventListener('click', toggleTheme);
    }
  });

  document.addEventListener("livewire:navigated", () => {
    setTheme(localStorage.getItem('theme') === 'dark');
    const btn = document.getElementById('button-theme-toggle');
    if (btn) {
      btn.replaceWith(btn.cloneNode(true));
      document.getElementById('button-theme-toggle').addEventListener('click', toggleTheme);
    }
  });

  document.addEventListener("livewire:updated", () => {
    setTheme(localStorage.getItem('theme') === 'dark');
  });

  function setTheme(dark) {
    const btnThemeToggle = document.getElementById('button-theme-toggle');
    if (btnThemeToggle) {
      const icon = btnThemeToggle.querySelector('i');
      if (icon) {
        icon.className = dark ? 'fi fi-rr-moon' : 'fi fi-rr-sun';
      } else {
        btnThemeToggle.textContent = dark ? 'Dark Mode' : 'Light Mode';
      }
      document.documentElement.classList.toggle('dark', dark);
    }
  }

  function toggleTheme() {
    const isDark = localStorage.getItem('theme') === 'dark';
    document.documentElement.classList.toggle('dark', !isDark);
    localStorage.setItem('theme', isDark ? '' : 'dark');
    setTheme(!isDark);
  }
</script>