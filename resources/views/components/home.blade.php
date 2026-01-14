{{-- 
    Página Home - Dashboard Bento UI
    Hierarquia: HOME (raiz)
--}}
<section class="page active" id="page-home" data-level="home">
    {{-- Header --}}
    <header class="app-header">
        <button class="icon-btn" data-drawer="menu">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="4" x2="20" y1="12" y2="12" />
                <line x1="4" x2="20" y1="6" y2="6" />
                <line x1="4" x2="20" y1="18" y2="18" />
            </svg>
        </button>
        
        <h1 class="header-title"></h1>
        
        <button class="icon-btn" aria-label="Notificações" data-go="notifications" style="position:relative;">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0 1 18 14.158V11a6 6 0 1 0-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5" />
                <path d="M13.73 21a2 2 0 0 1-3.46 0" />
            </svg>
            <span id="notif-badge" class="badge badge-error" style="position:absolute;top:4px;right:4px;min-width:18px;height:18px;padding:0 4px;display:none;align-items:center;justify-content:center;font-size:10px;">0</span>
        </button>
        
        <button class="icon-btn avatar avatar-sm" data-sheet="user-settings">
           {{-- User image avatar --}}
          @if(auth()->user() && auth()->user()->avatar)
            <img src="{{ auth()->user()->avatar }}" alt="Avatar" class="rounded-full w-full h-full object-cover"/>
          @else
            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
          @endif
        </button>
    </header>

    <main class="page-content pb-nav">
       
    </main>


</section>
