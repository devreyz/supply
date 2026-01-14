import{a as T,H as b}from"./vendor-CCKSAvAt.js";import{I as O,J as D}from"./queue-DX9w-n4L.js";window.axios=T;window.axios.defaults.headers.common["X-Requested-With"]="XMLHttpRequest";class y{constructor(e="spa_"){this.prefix=e,this._cache=new Map,this._watchers=new Map}_key(e){return`${this.prefix}${e}`}get(e,t=null){if(this._cache.has(e))return this._cache.get(e);try{const s=localStorage.getItem(this._key(e));if(s===null)return t;const i=JSON.parse(s);return this._cache.set(e,i),i}catch(s){return console.warn(`Erro ao ler localStorage[${e}]:`,s),t}}set(e,t){try{const s=JSON.stringify(t);return localStorage.setItem(this._key(e),s),this._cache.set(e,t),this._notify(e,t),!0}catch(s){return console.error(`Erro ao salvar localStorage[${e}]:`,s),!1}}remove(e){return localStorage.removeItem(this._key(e)),this._cache.delete(e),this._notify(e,null),!0}has(e){return localStorage.getItem(this._key(e))!==null}keys(){const e=[];for(let t=0;t<localStorage.length;t++){const s=localStorage.key(t);s.startsWith(this.prefix)&&e.push(s.substring(this.prefix.length))}return e}all(){const e={};return this.keys().forEach(t=>{e[t]=this.get(t)}),e}clear(){return this.keys().forEach(e=>{this.remove(e)}),this._cache.clear(),!0}async remember(e,t){if(this.has(e))return this.get(e);const s=typeof t=="function"?await t():t;return this.set(e,s),s}increment(e,t=1){const s=this.get(e,0),i=(Number(s)||0)+t;return this.set(e,i),i}decrement(e,t=1){return this.increment(e,-t)}push(e,t){const s=this.get(e,[]);if(!Array.isArray(s))throw new Error(`${e} não é um array`);return s.push(t),this.set(e,s),s}pop(e){const t=this.get(e,[]);if(!Array.isArray(t))throw new Error(`${e} não é um array`);const s=t.pop();return this.set(e,t),s}setNested(e,t,s){const i=this.get(e,{}),a=t.split(".");let n=i;for(let o=0;o<a.length-1;o++)a[o]in n||(n[a[o]]={}),n=n[a[o]];return n[a[a.length-1]]=s,this.set(e,i),i}getNested(e,t,s=null){const i=this.get(e,{}),a=t.split(".");let n=i;for(const o of a)if(n&&typeof n=="object"&&o in n)n=n[o];else return s;return n}watch(e,t){return this._watchers.has(e)||this._watchers.set(e,new Set),this._watchers.get(e).add(t),()=>{const s=this._watchers.get(e);s&&s.delete(t)}}_notify(e,t){const s=this._watchers.get(e);if(s){const i=this._cache.get(e);s.forEach(a=>{try{a(t,i)}catch(n){console.error("Erro no watcher:",n)}})}}setWithExpiry(e,t,s){const i={value:t,expiry:Date.now()+s*1e3};return this.set(e,i)}getWithExpiry(e,t=null){const s=this.get(e);return s?s.expiry&&Date.now()>s.expiry?(this.remove(e),t):s.value:t}export(){return JSON.stringify(this.all(),null,2)}import(e){const t=JSON.parse(e);return Object.entries(t).forEach(([s,i])=>{this.set(s,i)}),!0}size(){let e=0;return this.keys().forEach(t=>{const s=localStorage.getItem(this._key(t));s&&(e+=s.length*2)}),e}isNearLimit(e=.9){const t=this.size(),s=5*1024*1024;return t>=s*e}}const M=new y("spa_");typeof window<"u"&&(window.LocalStorageORM=y,window.Storage=M);class S{constructor(e={},t={}){e&&typeof e.modal=="function"?(this.spa=e,this.options={showBanner:!0,bannerDelay:5e3,bannerDismissKey:"spa_pwa_dismissed",serviceWorker:"./service-worker.js",...t}):(this.spa=null,this.options={showBanner:!0,bannerDelay:5e3,bannerDismissKey:"spa_pwa_dismissed",serviceWorker:"./service-worker.js",...e}),this.deferredPrompt=null,this.isInstalled=!1,this.isStandalone=!1,this.isShowingPrompt=!1,this.hasAttemptedPrompt=!1,this.installationPending=!1,this.promptTimeout=null,this._init()}async _showIOSInstallModal(){if(this.isInstalled||this.isShowingPrompt)return;this.isShowingPrompt=!0;const e=this.options.installHtml||`
    <style>
        .ios-wrapper {
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Text", "Segoe UI", Roboto, sans-serif;
            display: flex;
            flex-direction: column;
            gap: 1.25rem; /* Aumentei levemente o gap geral */
            color: #1c1c1e;
            margin-bottom: 1.5rem; /* Margem inferior solicitada */
        }

        /* --- Header: Identidade do App --- */
        .ios-hero {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .ios-app-icon {
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 0.8rem;
            background: linear-gradient(135deg, #ea580c, #c2410c);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            box-shadow: 0 4px 12px rgba(234, 88, 12, 0.3);
            flex-shrink: 0;
        }
        .ios-hero-content h3 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: -0.02em;
        }
        .ios-hero-content p {
            margin: 0.2rem 0 0;
            font-size: 0.85rem;
            color: #8e8e93;
        }

        /* --- Faixa de Recursos (Mini Bento) --- */
        .ios-features-strip {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            background: #F2F2F7;
            border-radius: 0.8rem;
            padding: 0.8rem 0.5rem;
            text-align: center;
        }
        .ios-feature-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.3rem;
            border-right: 1px solid rgba(0,0,0,0.08);
        }
        .ios-feature-item:last-child {
            border-right: none;
        }
        .ios-feat-icon {
            color: #007AFF;
            background: rgba(0, 122, 255, 0.1);
            padding: 0.35rem;
            border-radius: 50%;
        }
        .ios-feat-title {
            font-size: 0.7rem;
            font-weight: 600;
            margin-top: 0.1rem;
        }
        .ios-feat-desc {
            font-size: 0.65rem;
            color: #636366;
        }

        /* --- Instruções --- */
        .ios-steps-container {
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
        }
        .ios-step-row {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.6rem 0;
            border-bottom: 1px solid #E5E5EA;
        }
        .ios-step-row:last-child {
            border-bottom: none;
        }
        .ios-step-highlight {
            font-weight: 700;
            color: #000;
        }

        /* --- Finalização --- */
        .ios-footer-note {
            text-align: center;
            font-size: 0.8rem;
            color: #8e8e93;
            background: #fff;
            padding-top: 0.5rem;
        }
    </style>

    <div class="ios-wrapper">
        
        <div class="ios-hero">
            <div class="ios-app-icon">
                <i data-lucide="smartphone" style="width: 1.6rem; height: 1.6rem;"></i>
            </div>
            <div class="ios-hero-content">
                <h3>Instale o Tymely</h3>
                <p>Tenha a melhor experiência direto na sua tela inicial.</p>
            </div>
        </div>

        <div class="ios-features-strip">
            <div class="ios-feature-item">
                <div class="ios-feat-icon">
                    <i data-lucide="zap" style="width: 1rem; height: 1rem;"></i>
                </div>
                <span class="ios-feat-title">Rápido</span>
                <span class="ios-feat-desc">Instantâneo</span>
            </div>
            <div class="ios-feature-item">
                <div class="ios-feat-icon" style="color: #34C759; background: rgba(52, 199, 89, 0.1);">
                    <i data-lucide="wifi-off" style="width: 1rem; height: 1rem;"></i>
                </div>
                <span class="ios-feat-title">Offline</span>
                <span class="ios-feat-desc">Sem net</span>
            </div>
            <div class="ios-feature-item">
                <div class="ios-feat-icon" style="color: #FF9500; background: rgba(255, 149, 0, 0.1);">
                    <i data-lucide="feather" style="width: 1rem; height: 1rem;"></i>
                </div>
                <span class="ios-feat-title">Leve</span>
                <span class="ios-feat-desc">Otimizado</span>
            </div>
        </div>

        <div class="ios-steps-container">
            <div style="font-size: 0.75rem; font-weight: 600; color: #8E8E93; text-transform: uppercase; margin-bottom: 0.25rem; letter-spacing: 0.05em;">Como Adicionar</div>
            
            <div class="ios-step-row">
                <i data-lucide="share" style="color: #007AFF; width: 1.2rem; height: 1.2rem;"></i>
                <span style="font-size: 0.9rem; font-weight: 500;">Toque em <span class="ios-step-highlight">Compartilhar</span> na barra.</span>
            </div>
            
            <div class="ios-step-row">
                <i data-lucide="plus-square" style="color: #636366; width: 1.2rem; height: 1.2rem;"></i>
                <span style="font-size: 0.9rem; font-weight: 500;">Escolha <span class="ios-step-highlight">Adicionar à Tela de Início</span>.</span>
            </div>
        </div>

        <div class="ios-footer-note">
            <span>Pronto! O app aparecerá nos seus apps.</span>
        </div>

    </div>
    `;try{const t={title:this.options.installTitle||"Instalar Aplicativo",html:e,type:"custom",closeOnBackdrop:!1,width:this.options.installWidth||"420px",customButtons:[{text:this.options.cancelText||"Fechar",class:"btn btn-outline",value:"cancel"},{text:this.options.okText||"Entendi",class:"btn btn-primary",value:"ok"}]},s=await this.spa.modal(t);this.isShowingPrompt=!1,this._dismiss()}catch(t){this.isShowingPrompt=!1,console.error("📱 PWA: Falha ao abrir modal iOS",t)}}_init(){if(this.isStandalone=window.matchMedia("(display-mode: standalone)").matches||window.navigator.standalone===!0,this.isStandalone){this.isInstalled=!0,console.log("📱 PWA: App instalado (standalone)");return}window.addEventListener("beforeinstallprompt",e=>{e.preventDefault(),this.deferredPrompt=e,console.log("📱 PWA: Prompt de instalação disponível"),this._emit("pwa:installable"),!(this.hasAttemptedPrompt||this.isShowingPrompt||this.isInstalled)&&(this.promptTimeout&&clearTimeout(this.promptTimeout),this.options.showBanner&&!this._wasDismissed()&&(this.hasAttemptedPrompt=!0,this.promptTimeout=setTimeout(()=>{this.isShowingPrompt||this.isInstalled||(this.spa&&typeof this.spa.modal=="function"?this._showInstallModal():this._showBanner())},this.options.bannerDelay)))}),window.addEventListener("appinstalled",()=>{this.isInstalled=!0,this.deferredPrompt=null,this.promptTimeout&&clearTimeout(this.promptTimeout),this.isShowingPrompt=!1,console.log("📱 PWA: App instalado!"),this._emit("pwa:installed"),this._hideBanner(),this.installationPending=!1;try{this.spa&&typeof this.spa.toastSuccess=="function"&&this.spa.toastSuccess("App instalado","A aplicação foi adicionada à sua tela inicial")}catch{}}),this._registerServiceWorker();try{this.options.showBanner&&!this._wasDismissed()&&!this.isInstalled&&!this.hasAttemptedPrompt&&this._detectPlatform()==="ios"&&(this.hasAttemptedPrompt=!0,this.promptTimeout&&clearTimeout(this.promptTimeout),this.promptTimeout=setTimeout(()=>{this.isShowingPrompt||this.isInstalled||this._showIOSInstallModal()},this.options.bannerDelay))}catch{}}async _registerServiceWorker(){if(!("serviceWorker"in navigator)){console.warn("📱 PWA: Service Worker não suportado");return}try{const e={};this.options.scope&&(e.scope=this.options.scope);const t=await navigator.serviceWorker.register(this.options.serviceWorker||"./service-worker.js",e);console.log("📱 PWA: Service Worker registrado",t.scope),t.addEventListener("updatefound",()=>{const s=t.installing;s.addEventListener("statechange",()=>{s.state==="installed"&&navigator.serviceWorker.controller&&(console.log("📱 PWA: Nova versão disponível"),this._emit("pwa:update-available",t))})}),navigator.serviceWorker.addEventListener("message",s=>{s.data.type==="SYNC_TRIGGERED"&&this._emit("pwa:sync")})}catch(e){console.error("📱 PWA: Erro ao registrar SW:",e)}}showInstallPrompt(){if(this.isInstalled){console.log("📱 PWA: Já instalado, ignorando prompt");return}if(this._detectPlatform()==="ios")return this._showIOSInstallModal();this.deferredPrompt?this._showInstallModal():(console.warn("📱 PWA: Instalador iniciado manualmente, mas deferredPrompt ainda não foi capturado."),this._showBanner())}canInstall(){return this.deferredPrompt!==null&&!this.isInstalled}_detectPlatform(){const t=(navigator.userAgent||navigator.vendor||window.opera||"").toLowerCase();return/iphone|ipad|ipod/.test(t)||t.includes("mac")&&"ontouchend"in document?"ios":/android/.test(t)?"android":/windows/.test(t)?"windows":/mac os x/.test(t)?"mac":"desktop"}async promptInstall(){if(!this.deferredPrompt)return console.warn("📱 PWA: Prompt de instalação não disponível"),!1;this.deferredPrompt.prompt();const{outcome:e}=await this.deferredPrompt.userChoice;return console.log(`📱 PWA: Usuário ${e==="accepted"?"aceitou":"recusou"} instalação`),this.deferredPrompt=null,e==="accepted"}async _showInstallModal(){if(this.isInstalled||!this.canInstall()||this.isShowingPrompt)return;if(this.isShowingPrompt=!0,this._detectPlatform()==="ios")return this.isShowingPrompt=!1,this._showIOSInstallModal();const t=this.options.installHtml||`
    <style>
        /* Animação suave do Gradiente de Fundo */
        @keyframes premiumGradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        /* Animação de Flutuação (Floating) */
        @keyframes premiumFloat {
            0%, 100% { transform: translateY(0) rotate(-3deg); }
            50% { transform: translateY(-8px) rotate(2deg); }
        }

        .pwa-bento-container {
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "Segoe UI", Roboto, sans-serif;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
            margin: 1.5rem 0;
        }

        /* --- Hero Card (Card Principal) --- */
        .pwa-hero-card {
            grid-column: span 2;
            padding: 1.5rem;
            border-radius: 1.25rem;
            /* Gradiente Premium Sky Blue */
            background: linear-gradient(120deg, #e0f2fe, #bae6fd, #e0f2fe);
            background-size: 200% 200%;
            animation: premiumGradient 8s ease infinite;
            
            border: 1px solid rgba(14, 165, 233, 0.2);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px -10px rgba(14, 165, 233, 0.25);
        }

        /* Elemento Decorativo de Fundo */
        .pwa-hero-bg-blur {
            position: absolute;
            top: -50%;
            right: -20%;
            width: 150px;
            height: 150px;
            background: #0ea5e9;
            filter: blur(80px);
            opacity: 0.15;
            z-index: 0;
        }

        .pwa-hero-content {
            z-index: 1;
            flex: 1;
        }

        .pwa-hero-title {
            margin: 0;
            font-weight: 800;
            font-size: 1.2rem;
            color: #0c4a6e; /* Azul Escuro Profundo */
            letter-spacing: -0.03em;
            line-height: 1.1;
        }

        .pwa-hero-subtitle {
            margin: 0.35rem 0 0;
            font-size: 0.8rem;
            color: #0369a1;
            font-weight: 500;
            line-height: 1.3;
        }

        /* --- Composição dos Ícones Sobrepostos --- */
        .pwa-icon-composition {
            position: relative;
            width: 4rem;
            height: 4rem;
            flex-shrink: 0;
            z-index: 1;
        }

        /* Ícone Traseiro (Smartphone) */
        .pwa-layer-phone {
            position: absolute;
            right: 0;
            bottom: 0;
            width: 2.8rem;
            height: 2.8rem;
            color: #0ea5e9;
            opacity: 0.2;
            transform: rotate(10deg);
        }

        /* Ícone Frontal (Logo Flutuante) */
        .pwa-layer-brand {
            position: absolute;
            left: 0;
            top: 0;
            width: 3rem;
            height: 3rem;
            border-radius: 0.9rem;
            
            /* Gradiente do botão */
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            
            /* Efeito 3D e Sombra */
            box-shadow: 
                0 8px 20px rgba(14, 165, 233, 0.4),
                inset 0 1px 1px rgba(255,255,255,0.4);
            border: 2px solid rgba(255,255,255,0.6);
            
            animation: premiumFloat 5s ease-in-out infinite;
        }

        /* --- Cards de Benefícios --- */
        .pwa-benefit-card {
            padding: 1rem;
            border-radius: 1rem;
            background: #ffffff;
            border: 1px solid #f1f5f9;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .pwa-benefit-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border-color: #e2e8f0;
        }

        /* Container do ícone do benefício */
        .pwa-icon-box {
            width: 2.2rem;
            height: 2.2rem;
            border-radius: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pwa-benefit-text h5 {
            margin: 0;
            font-size: 0.85rem;
            font-weight: 700;
            color: #334155;
        }
        
        .pwa-benefit-text p {
            margin: 0.15rem 0 0;
            font-size: 0.7rem;
            color: #64748b;
            line-height: 1.25;
        }
    </style>

    <div class="pwa-install-bento pwa-bento-container">
        
        <div class="pwa-hero-card">
            <div class="pwa-hero-bg-blur"></div>
            
            <div class="pwa-hero-content">
                <h4 class="pwa-hero-title">Instale o App</h4>
                <p class="pwa-hero-subtitle">Acesso rápido, seguro e sem ocupar espaço.</p>
            </div>

            <div class="pwa-icon-composition">
                <div class="pwa-layer-phone">
                    <i data-lucide="smartphone" style="width: 100%; height: 100%;"></i>
                </div>
                <div class="pwa-layer-brand">
                    <i data-lucide="sparkles" style="width: 1.5rem; height: 1.5rem;"></i>
                </div>
            </div>
        </div>
        
        <div class="pwa-benefit-card">
            <div class="pwa-icon-box" style="background: #fffbeb; color: #d97706;">
                <i data-lucide="zap" style="width: 1.1rem; height: 1.1rem;"></i>
            </div>
            <div class="pwa-benefit-text">
                <h5>Instantâneo</h5>
                <p>Abre em segundos.</p>
            </div>
        </div>

        <div class="pwa-benefit-card">
            <div class="pwa-icon-box" style="background: #ecfdf5; color: #059669;">
                <i data-lucide="wifi-off" style="width: 1.1rem; height: 1.1rem;"></i>
            </div>
            <div class="pwa-benefit-text">
                <h5>Offline</h5>
                <p>Use sem internet.</p>
            </div>
        </div>

        <div class="pwa-benefit-card">
            <div class="pwa-icon-box" style="background: #f5f3ff; color: #7c3aed;">
                <i data-lucide="feather" style="width: 1.1rem; height: 1.1rem;"></i>
            </div>
            <div class="pwa-benefit-text">
                <h5>Super Leve</h5>
                <p>Poupa memória.</p>
            </div>
        </div>

        <div class="pwa-benefit-card">
            <div class="pwa-icon-box" style="background: #fff1f2; color: #e11d48;">
                <i data-lucide="refresh-cw" style="width: 1.1rem; height: 1.1rem;"></i>
            </div>
            <div class="pwa-benefit-text">
                <h5>Atualizado</h5>
                <p>Sync automático.</p>
            </div>
        </div>
    </div>
    `,s=this.options.installHtml||t;try{const i={title:this.options.installTitle||"Instalar Aplicativo",html:s,type:"custom",closeOnBackdrop:!1,width:this.options.installWidth||"480px",customButtons:[{text:this.options.cancelText||"Agora não",class:"btn btn-outline",value:"cancel"},{text:this.options.installText||"Instalar Agora",class:"btn btn-success",value:"ok"}]},n=await this.spa.modal(i);if(this.isShowingPrompt=!1,n==="ok")if(await this.promptInstall()){this.installationPending=!0;try{this.spa&&typeof this.spa.toastInfo=="function"?this.spa.toastInfo("Instalação iniciada","Aguarde enquanto o sistema finaliza a instalação."):this.spa&&typeof this.spa.toast=="function"?this.spa.toast("Instalação iniciada — aguardando confirmação."):console.log("📱 PWA: Instalação iniciada — aguardando evento appinstalled.")}catch{}}else this._dismiss();else this._dismiss()}catch(i){this.isShowingPrompt=!1,console.error("📱 PWA: Falha ao abrir modal de instalação, usando banner como fallback",i),this._showBanner()}}_showBanner(){if(this.isInstalled||!this.canInstall()||this.isShowingPrompt)return;this._hideBanner(),this.isShowingPrompt=!0;const e=document.createElement("div");e.id="pwa-install-banner",e.className="pwa-banner",e.innerHTML=`
                <div class="pwa-banner-content">
                    <div class="pwa-banner-icon">📱</div>
                    <div class="pwa-banner-text">
                        <strong>Instale o App</strong>
                        <span>Adicione à tela inicial para acesso rápido</span>
                    </div>
                </div>
                <div class="pwa-banner-actions">
                    <button class="pwa-banner-dismiss" data-pwa-dismiss>Agora não</button>
                    <button class="pwa-banner-install" data-pwa-install>Instalar</button>
                </div>
            `,document.body.appendChild(e),requestAnimationFrame(()=>{e.classList.add("show")}),e.querySelector("[data-pwa-install]").addEventListener("click",()=>{this.promptInstall(),this._hideBanner()}),e.querySelector("[data-pwa-dismiss]").addEventListener("click",()=>{this._dismiss(),this._hideBanner()})}_hideBanner(){const e=document.getElementById("pwa-install-banner");e?(e.classList.remove("show"),setTimeout(()=>{e.remove(),this.isShowingPrompt=!1},300)):this.isShowingPrompt=!1}_dismiss(){localStorage.setItem(this.options.bannerDismissKey,Date.now().toString())}_wasDismissed(){const e=localStorage.getItem(this.options.bannerDismissKey);if(!e)return!1;const t=30*60*1e3;return Date.now()-parseInt(e)<t}async update(){if(!("serviceWorker"in navigator))return;const e=await navigator.serviceWorker.getRegistration();e&&await e.update()}async sendMessage(e){if(!("serviceWorker"in navigator))return;const t=await navigator.serviceWorker.ready;t.active&&t.active.postMessage(e)}async clearCache(){await this.sendMessage({type:"CLEAR_CACHE"})}async cacheUrls(e){await this.sendMessage({type:"CACHE_URLS",payload:{urls:e}})}_emit(e,t=null){document.dispatchEvent(new CustomEvent(e,{detail:t}))}}typeof window<"u"&&(window.PWAInstaller=S);class E{constructor(e={}){e&&e._registerOverlay?(this.spa=e,this.options={vapidKey:null,defaultIcon:"/img/icon-192.png",defaultBadge:"/img/badge.png"}):(this.spa=null,this.options={vapidKey:null,defaultIcon:"/img/icon-192.png",defaultBadge:"/img/badge.png",...e}),this.permission=Notification.permission,this.subscription=null,this.db=null}isSupported(){return"Notification"in window&&"serviceWorker"in navigator}isEnabled(){return this.permission==="granted"}isDenied(){return this.permission==="denied"}async request(){if(!this.isSupported())return console.warn("🔔 Notificações não suportadas"),!1;if(this.isEnabled())return!0;if(this.isDenied())return console.warn("🔔 Notificações foram bloqueadas pelo usuário"),!1;try{const e=await Notification.requestPermission();return this.permission=e,e==="granted"?(console.log("🔔 Permissão concedida"),this._emit("notifications:granted"),!0):(console.log("🔔 Permissão negada"),this._emit("notifications:denied"),!1)}catch(e){return console.error("🔔 Erro ao solicitar permissão:",e),!1}}async init(){try{if(!this.spa||(this.db=this.spa.db,!this.db))return;await this.db.defineTable("notifications",{keyPath:"id",autoIncrement:!0,indexes:[{name:"tag",keyPath:"tag"},{name:"read",keyPath:"read"},{name:"createdAt",keyPath:"createdAt"}]}),this.spa&&typeof this.spa._log=="function"&&this.spa._log(2,"🔔 NotificationManager: tabela `notifications` pronta")}catch(e){console.error("🔔 Erro ao inicializar NotificationManager (DB):",e)}}async store(e={}){if(!this.db)return null;const t={title:e.title||"",body:e.body||"",tag:e.tag||null,data:e.data||null,icon:e.icon||this.options.defaultIcon,read:!!e.read,createdAt:new Date().toISOString(),updatedAt:new Date().toISOString()},s=await this.db.table("notifications").insert(t);return document.dispatchEvent(new CustomEvent("notifications:changed")),s}async list(){return this.db?(await this.db.table("notifications").all()).sort((t,s)=>new Date(s.createdAt)-new Date(t.createdAt)):[]}async markRead(e){if(!this.db)return null;const t=await this.db.table("notifications").find(e);if(!t)return null;t.read=!0,t.updatedAt=new Date().toISOString();const s=await this.db.table("notifications").upsert(t);return document.dispatchEvent(new CustomEvent("notifications:changed")),s}async remove(e){if(!this.db)return null;const t=await this.db.table("notifications").delete(e);return document.dispatchEvent(new CustomEvent("notifications:changed")),t}async show(e,t={}){var i;if(!this.isEnabled()&&!await this.request())return null;const s={icon:t.icon||this.options.defaultIcon,badge:t.badge||this.options.defaultBadge,body:t.body||"",requireInteraction:t.requireInteraction||!1,silent:t.silent||!1,vibrate:t.vibrate||[100,50,100],data:t.data||{},actions:t.actions||[]};t.tag&&(s.tag=t.tag);try{const a=await((i=navigator.serviceWorker)==null?void 0:i.ready);return a?await a.showNotification(e,s):new Notification(e,s),console.log("🔔 Notificação mostrada:",e),!0}catch(a){return console.error("🔔 Erro ao mostrar notificação:",a),!1}}success(e,t="",s={}){return this.show(e,{body:t,...s})}error(e,t="",s={}){return this.show(e,{body:t,...s})}info(e,t="",s={}){return this.show(e,{body:t,...s})}async subscribePush(){if(!this.options.vapidKey)return console.warn("🔔 VAPID key não configurada"),null;if(!this.isEnabled()&&!await this.request())return null;try{const e=await navigator.serviceWorker.ready;let t=await e.pushManager.getSubscription();return t||(t=await e.pushManager.subscribe({userVisibleOnly:!0,applicationServerKey:this._urlBase64ToUint8Array(this.options.vapidKey)})),this.subscription=t,console.log("🔔 Push subscription:",t),this._emit("push:subscribed",t),t}catch(e){return console.error("🔔 Erro ao assinar push:",e),null}}async unsubscribePush(){var e;if(!this.subscription){const t=await((e=navigator.serviceWorker)==null?void 0:e.ready);this.subscription=await(t==null?void 0:t.pushManager.getSubscription())}return this.subscription?(await this.subscription.unsubscribe(),this.subscription=null,console.log("🔔 Push subscription cancelada"),this._emit("push:unsubscribed"),!0):!1}async getSubscription(){var t;const e=await((t=navigator.serviceWorker)==null?void 0:t.ready);return e==null?void 0:e.pushManager.getSubscription()}async closeAll(){var s;const e=await((s=navigator.serviceWorker)==null?void 0:s.ready);if(!e)return;(await e.getNotifications()).forEach(i=>i.close())}async closeByTag(e){var i;const t=await((i=navigator.serviceWorker)==null?void 0:i.ready);if(!t)return;(await t.getNotifications({tag:e})).forEach(a=>a.close())}_urlBase64ToUint8Array(e){const t="=".repeat((4-e.length%4)%4),s=(e+t).replace(/-/g,"+").replace(/_/g,"/"),i=window.atob(s),a=new Uint8Array(i.length);for(let n=0;n<i.length;++n)a[n]=i.charCodeAt(n);return a}_emit(e,t=null){document.dispatchEvent(new CustomEvent(e,{detail:t}))}}typeof window<"u"&&(window.NotificationManager=E);class k{constructor(e){this.spa=e,this.drawers=new Map,this.isDragging=!1,this.startX=0,this.currentDrawer=null}register(e){const t=document.getElementById(e);if(t&&(this.drawers.set(e,{element:t,isOpen:!1}),this.spa.config.gestures.enabled)){const s=t.querySelector(".drawer-handle")||t;s.addEventListener("touchstart",i=>this._onDragStart(i,e),{passive:!0}),s.addEventListener("touchmove",i=>this._onDragMove(i),{passive:!1}),s.addEventListener("touchend",i=>this._onDragEnd(i))}}open(e){const t=this.drawers.get(e);!t||t.isOpen||(t.element.classList.add("open"),t.isOpen=!0,this.spa._registerOverlay(`drawer-${e}`))}close(e,t=!0){const s=this.drawers.get(e);if(!s||!s.isOpen)return;if(t){this.spa._closeOverlay(`drawer-${e}`,!0);return}s.element.classList.remove("open"),s.element.style.transform="",s.isOpen=!1;const i=`drawer-${e}`,a=this.spa.activeOverlays.indexOf(i);a>-1&&this.spa.activeOverlays.splice(a,1),this.spa.activeOverlays.length===0&&this.spa._hideBackdrop()}_onDragStart(e,t){const s=this.drawers.get(t);s!=null&&s.isOpen&&(this.isDragging=!0,this.currentDrawer=t,this.startX=e.touches[0].clientX,s.element.classList.add("is-dragging"))}_onDragMove(e){if(!this.isDragging||!this.currentDrawer)return;const t=this.drawers.get(this.currentDrawer);if(!t)return;const s=e.touches[0].clientX,i=this.startX-s;i>0&&(e.preventDefault(),t.element.style.transform=`translateX(-${i}px)`)}_onDragEnd(e){if(!this.isDragging||!this.currentDrawer)return;const t=this.drawers.get(this.currentDrawer);if(!t)return;t.element.classList.remove("is-dragging");const s=e.changedTouches[0].clientX;this.startX-s>this.spa.config.gestures.swipeThreshold?this.spa.closeTopOverlay():t.element.style.transform="",this.isDragging=!1,this.currentDrawer=null}}class x{constructor(e){this.spa=e,this.sheets=new Map,this.isDragging=!1,this.startY=0,this.currentSheet=null}register(e){const t=document.getElementById(e);if(t&&(this.sheets.set(e,{element:t,isOpen:!1}),this.spa.config.gestures.enabled)){const s=t.querySelector(".grabber-handle")||t.querySelector(".sheet-header")||t;s.addEventListener("touchstart",i=>this._onDragStart(i,e),{passive:!1}),s.addEventListener("mousedown",i=>this._onDragStart(i,e)),window.addEventListener("touchmove",i=>this._onDragMove(i),{passive:!1}),window.addEventListener("touchend",i=>this._onDragEnd(i)),window.addEventListener("mousemove",i=>this._onDragMove(i)),window.addEventListener("mouseup",i=>this._onDragEnd(i))}}open(e){const t=this.sheets.get(e);!t||t.isOpen||(t.element.classList.add("open"),t.isOpen=!0,this.spa._registerOverlay(`sheet-${e}`))}close(e,t=!0){const s=this.sheets.get(e);if(!s||!s.isOpen)return;if(t){this.spa._closeOverlay(`sheet-${e}`,!0);return}s.element.classList.remove("open"),s.element.style.transform="",s.isOpen=!1;const i=`sheet-${e}`,a=this.spa.activeOverlays.indexOf(i);a>-1&&this.spa.activeOverlays.splice(a,1),this.spa.activeOverlays.length===0&&this.spa._hideBackdrop()}_onDragStart(e,t){const s=this.sheets.get(t);s!=null&&s.isOpen&&(this.isDragging=!0,this.currentSheet=t,this.startY=e.touches?e.touches[0].clientY:e.clientY,s.element.classList.add("is-dragging"))}_onDragMove(e){if(!this.isDragging||!this.currentSheet)return;const t=this.sheets.get(this.currentSheet);if(!t)return;const i=(e.touches?e.touches[0].clientY:e.clientY)-this.startY;i>0&&(e.preventDefault(),t.element.style.transform=`translateY(${i}px)`)}_onDragEnd(e){if(!this.isDragging||!this.currentSheet)return;const t=this.sheets.get(this.currentSheet);if(!t)return;t.element.classList.remove("is-dragging");const i=(e.changedTouches?e.changedTouches[0].clientY:e.clientY)-this.startY,n=t.element.offsetHeight*.3;i>n?this.spa.closeTopOverlay():t.element.style.transform="",this.isDragging=!1,this.currentSheet=null}}class A{constructor(e){this.spa=e,this.modals=new Map,this.counter=0}open(e){return new Promise(t=>{const s=`modal-${++this.counter}`,i=e.dismissible!==!1,a=document.createElement("div");a.className="modal-overlay",a.id=s,a.dataset.dismissible=String(i);const n=document.createElement("div");if(n.className=`modal-dialog ${e.class||""}`,e.width&&(n.style.maxWidth=e.width),e.template){const o=document.getElementById(e.template);o&&(n.innerHTML=o.innerHTML)}else if(e.html){let o="";e.title&&(o+=`<h3 class="modal-title">${e.title}</h3>`),o+=e.html,e.customButtons&&e.customButtons.length>0&&(o+='<div class="modal-actions">',e.customButtons.forEach(c=>{o+=`<button class="${c.class}" data-action="${c.value}">${c.text}</button>`}),o+="</div>"),n.innerHTML=o}else{let o="";e.icon?o+=`<div class="modal-icon">${e.icon}</div>`:e.type==="confirm"?o+='<div class="modal-icon modal-icon-warning">⚠️</div>':e.type==="success"?o+='<div class="modal-icon modal-icon-success">✓</div>':e.type==="error"&&(o+='<div class="modal-icon modal-icon-error">✕</div>'),e.title&&(o+=`<h3 class="modal-title">${e.title}</h3>`),e.message&&(o+=`<p class="modal-message">${e.message}</p>`),e.type==="prompt"&&(o+=`<input type="${e.inputType||"text"}" 
                                          class="modal-input input-field" 
                                          placeholder="${e.placeholder||""}"
                                          value="${e.defaultValue||""}">`),o+='<div class="modal-actions">',(e.type==="confirm"||e.type==="prompt")&&(o+=`<button class="btn btn-outline modal-cancel" data-action="cancel">
                                      ${e.cancelText||"Cancelar"}
                                    </button>`),o+=`<button class="btn btn-primary modal-confirm" data-action="confirm">
                                  ${e.confirmText||"OK"}
                                </button>`,o+="</div>",n.innerHTML=o}a.appendChild(n),document.body.appendChild(a),this.modals.set(s,{element:a,resolve:t,dismissible:i}),requestAnimationFrame(()=>{window.lucide&&window.lucide.createIcons()}),requestAnimationFrame(()=>{a.classList.add("show")}),this.spa._registerOverlay(s),a.addEventListener("click",o=>{const c=o.target.dataset.action;if(c)if(e.customButtons)this._resolveAndClose(s,c);else if(c==="confirm"){let d=!0;if(e.type==="prompt"){const l=a.querySelector(".modal-input");d=l?l.value:""}this._resolveAndClose(s,d)}else c==="cancel"&&this._resolveAndClose(s,!1);else o.target===a&&i&&e.closeOnBackdrop!==!1&&this._resolveAndClose(s,null)}),e.type==="prompt"&&setTimeout(()=>{const o=a.querySelector(".modal-input");o&&o.focus()},300)})}close(e){const t=this.modals.get(e);t&&(t.element.classList.remove("show"),setTimeout(()=>{t.element.remove(),this.modals.delete(e)},300))}_resolveAndClose(e,t){const s=this.modals.get(e);if(s){s.resolve(t);try{this.spa._closeOverlay(e,!0)}catch{const a=this.spa.activeOverlays.indexOf(e);a>-1&&this.spa.activeOverlays.splice(a,1),this.close(e),this.spa.activeOverlays.length===0&&this.spa._hideBackdrop()}}}}class C{constructor(e){this.spa=e,this.instances=new Map}register(e){const t=document.getElementById(e);if(!t)return;const s=Array.from(t.querySelectorAll("[data-step]"));this.instances.set(e,{element:t,steps:s,currentStepIndex:0,totalSteps:s.length,formData:{}}),t.addEventListener("click",i=>{const a=i.target.closest("[data-step-action]");if(!a)return;i.preventDefault();const n=a.dataset.stepAction;n==="next"&&this.next(e),n==="prev"&&this.prev(e),n==="finish"&&this.finish(e)}),this._updateView(e,0)}reset(e){const t=this.instances.get(e);if(!t)return;t.element.querySelectorAll("input, select, textarea").forEach(i=>{i.type==="checkbox"||i.type==="radio"?i.checked=!1:i.value=""}),t.formData={},this._updateView(e,0)}next(e){const t=this.instances.get(e);if(!t)return;const s=t.steps[t.currentStepIndex];this._validateStep(s)&&(this._collectStepData(t,s),t.currentStepIndex<t.totalSteps-1&&this._updateView(e,t.currentStepIndex+1))}prev(e){const t=this.instances.get(e);t&&t.currentStepIndex>0&&this._updateView(e,t.currentStepIndex-1)}finish(e){var a,n,o;const t=this.instances.get(e);if(!t)return;const s=t.steps[t.currentStepIndex];if(!this._validateStep(s))return;this._collectStepData(t,s),this.spa.closeSheet(e);const i=new CustomEvent("multistep:finish",{detail:{sheetId:e,data:t.formData}});document.dispatchEvent(i),(o=(n=(a=this.spa)==null?void 0:a.config)==null?void 0:n.debug)!=null&&o.enabled&&console.log("📦 MultiStep Data:",t.formData),setTimeout(()=>this.reset(e),500)}_updateView(e,t){const s=this.instances.get(e);if(!s)return;s.currentStepIndex=t,s.steps.forEach((n,o)=>{n.classList.remove("active","prev","next"),o===t?n.classList.add("active"):o<t?n.classList.add("prev"):n.classList.add("next")}),s.element.querySelectorAll(".step-indicator .dot").forEach((n,o)=>{n.classList.toggle("active",o<=t)});const a=s.element.querySelector("[data-step-counter]");a&&(a.textContent=`${t+1}/${s.totalSteps}`)}_collectStepData(e,t){t.querySelectorAll("input, select, textarea").forEach(i=>{if(!i.name)return;if(i.type==="checkbox"){i.checked&&(Array.isArray(e.formData[i.name])||(e.formData[i.name]!==void 0?e.formData[i.name]=[e.formData[i.name]]:e.formData[i.name]=[]),e.formData[i.name].push(i.value||!0));return}let a=i.value;if(i.type==="radio"){if(!i.checked)return;a=i.value}else i.type==="number"&&(a=parseFloat(i.value));e.formData[i.name]!==void 0?(Array.isArray(e.formData[i.name])||(e.formData[i.name]=[e.formData[i.name]]),e.formData[i.name].push(a)):e.formData[i.name]=a})}_validateStep(e){const t=e.querySelectorAll("input, select, textarea");let s=!0;return t.forEach(i=>{i.checkValidity()||(s=!1,i.reportValidity(),i.classList.add("input-error"),i.addEventListener("input",()=>i.classList.remove("input-error"),{once:!0}))}),s}}const B={container:null,config:{duration:3e3,position:"bottom-right",maxVisible:3},configure(r){Object.assign(this.config,r)},_ensureContainer(){this.container||(this.container=document.getElementById("toast-container"),this.container||(this.container=document.createElement("div"),this.container.id="toast-container",this.container.className=`toast-container toast-${this.config.position}`,document.body.appendChild(this.container)))},show(r,e="info"){this._ensureContainer();const t=document.createElement("div");t.className=`toast-item toast-${e}`;const s={success:"✓",error:"✕",warning:"⚠",info:"ℹ"};t.innerHTML=`
                <span class="toast-icon">${s[e]||s.info}</span>
                <span class="toast-message">${r}</span>
            `,this.container.appendChild(t);const i=this.container.querySelectorAll(".toast-item");return i.length>this.config.maxVisible&&i[0].remove(),requestAnimationFrame(()=>{t.classList.add("show")}),setTimeout(()=>{t.classList.remove("show"),setTimeout(()=>t.remove(),300)},this.config.duration),t},success(r){return this.show(r,"success")},error(r){return this.show(r,"error")},warning(r){return this.show(r,"warning")},info(r){return this.show(r,"info")}};typeof window<"u"&&(window.DrawerManager=k,window.SheetManager=x,window.ModalManager=A,window.Toast=B);const I={homePage:"home",pagePrefix:"page-",useHistory:!0,scrollToTop:!0,animation:{type:"fade",speed:.35,backSpeed:.08,easing:"cubic-bezier(0.25, 0.46, 0.45, 0.94)"},gestures:{enabled:!0,swipeBack:!0,swipeThreshold:50,edgeWidth:30,velocityThreshold:.3},ui:{autoTheme:!1,toast:{duration:3e3,position:"bottom-right",maxVisible:3},backdrop:{opacity:.3,blur:12,closeOnClick:!0}},doubleTapExit:!0,doubleTapTimeout:2e3,debug:{enabled:!1,level:1},pwa:{enabled:!0,serviceWorker:"/sw.js",showBanner:!0,bannerDelay:5e3},callbacks:{beforeNavigate:null,afterNavigate:null,onOverlayOpen:null,onOverlayClose:null,onError:null}};class P{constructor(e={}){this.config=this._deepMerge(I,e),this.current=this.config.homePage,this.previousPage=null,this.activeOverlays=[],this.lastBackPress=0,this._lastNavigate=0,this.pageHierarchy=new Map,this.pages=new Map,this.backdrop=null,this.toastContainer=null,this.loadingOverlay=null,this.drawerManager=null,this.sheetManager=null,this.modalManager=null,this.db=null,this.storage=null,this.queue=null,this.pwa=null,this.notifications=null,this._online=navigator.onLine,this._handlePopState=this._handlePopState.bind(this),this._handleClick=this._handleClick.bind(this),this._handleOnline=this._handleOnline.bind(this),this._handleOffline=this._handleOffline.bind(this)}async init(){try{return console.log("🚀 Inicializando SPA Framework..."),this._registerPages(),this._createElements(),this._initManagers(),this._setupEventListeners(),this._setupInitialHistory(),await this._initStorage(),this._applyPreferences(),this._initPWA(),document.documentElement.dataset.animation=this.config.animation.type,document.documentElement.style.setProperty("--spa-animation-speed",this.config.animation.speed+"s"),this.hideLoading(),this._emit("spa:ready",{spa:this}),this._emitPageEvent(this.current,"page:enter"),this._log(1,"✅ SPA Framework inicializado"),this._log(2,`📊 ${this.pageHierarchy.size} páginas registradas`),this}catch(e){throw console.error("❌ Erro ao inicializar SPA:",e),e}}go(e,t={}){if(e===this.current)return;const s=Date.now();if(this._lastNavigate&&s-this._lastNavigate<150){this._log(2,"⏳ Navegação ignorada (debounce)");return}this._lastNavigate=s;const i=this.pages.get(this.current),a=this.pages.get(e);if(!a){console.error(`❌ Página não encontrada: ${e}`),this.toastError("❌  ERROR",`Página não encontrada: ${e||"undefined"}`);return}const n=this.pageHierarchy.get(this.current),o=this.pageHierarchy.get(e),c=this._getLevelValue((n==null?void 0:n.level)||"primary"),d=this._getLevelValue((o==null?void 0:o.level)||"primary"),l=e===this.config.homePage;if(l){this.back();return}if(d<c){this.back();return}try{this._prepareBindingsForNavigation(e)}catch{}if(this.config.callbacks.beforeNavigate&&this.config.callbacks.beforeNavigate(this.current,e)===!1)return;const h=t.animation||this.config.animation.type;document.documentElement.style.setProperty("--spa-animation-speed",this.config.animation.speed+"s"),document.documentElement.dataset.animation=h,a.dataset.animBack=h,this._emitPageEvent(this.current,"page:leave"),this._clearPageClasses(i,a),i.classList.add("prev"),a.classList.add("active"),this.previousPage=this.current,this.current=e;const u={page:e,overlays:[],type:(o==null?void 0:o.level)||(l?"home":"primary"),parent:(o==null?void 0:o.parent)||this.config.homePage};t.replaceHistory||d===c&&this.current!==this.config.homePage?history.replaceState(u,"",`#${e}`):history.pushState(u,"",`#${e}`),this.config.scrollToTop&&(a.scrollTop=0),setTimeout(()=>{i.classList.remove("prev"),this._emitPageEvent(e,"page:enter"),this.config.callbacks.afterNavigate&&this.config.callbacks.afterNavigate(this.previousPage,e)},this.config.animation.speed*1e3)}back(e){if(this.activeOverlays.length>0){this.closeTopOverlay();return}let t=null;if(typeof e=="string")t=e;else if(e!=null&&e.target){const s=e.target.closest("[data-back]");if(s){const i=s.dataset.back;i&&i!=="true"&&i!==""&&(t=i)}}if(t){this.go(t);return}history.back()}home(){this.go(this.config.homePage)}openDrawer(e){this.drawerManager&&this.drawerManager.open(e)}closeDrawer(e){this.drawerManager&&this.drawerManager.close(e)}openSheet(e){var t;if(this.sheetManager){const s=document.getElementById(e);if(s&&s.hasAttribute&&s.hasAttribute("data-multistep-sheet"))try{(t=this.multiStepManager)==null||t.reset(e)}catch{}this.sheetManager.open(e)}}closeSheet(e){this.sheetManager&&this.sheetManager.close(e)}async modal(e){return this.modalManager?this.modalManager.open(e):null}closeTopOverlay(){if(this.activeOverlays.length===0)return;const e=this.activeOverlays[this.activeOverlays.length-1];this._closeOverlay(e,!0)}_registerOverlay(e){this.activeOverlays.push(e),this._showBackdrop(),history.pushState({page:this.current,overlays:this.activeOverlays.slice()},"",`#${this.current}_${e}`),this.config.callbacks.onOverlayOpen&&this.config.callbacks.onOverlayOpen(e),this._emit("spa:overlay:open",{id:e})}_closeOverlay(e,t=!0){var i,a,n;const s=this.activeOverlays.indexOf(e);s!==-1&&(this.activeOverlays.splice(s,1),e.startsWith("drawer-")?(i=this.drawerManager)==null||i.close(e.replace("drawer-",""),!1):e.startsWith("sheet-")?(a=this.sheetManager)==null||a.close(e.replace("sheet-",""),!1):e.startsWith("modal-")&&((n=this.modalManager)==null||n.close(e)),this.activeOverlays.length===0&&this._hideBackdrop(),t&&history.back(),this.config.callbacks.onOverlayClose&&this.config.callbacks.onOverlayClose(e),this._emit("spa:overlay:close",{id:e}))}toast(e,t=null,s={}){this.toastContainer||this._createToastContainer();let i;typeof e=="object"?i=e:i={title:e,description:typeof t=="string"?t:null,type:typeof t=="string"&&s.type?s.type:t||"info",duration:(typeof s=="object"?s.duration:s)||4e3,...s};const{title:a,description:n,type:o="info",duration:c=4e3,dismissible:d=!0,progress:l=null,onClose:h=null}=i,u=Math.random().toString(36).substring(2,9),p=document.createElement("div");p.className="toast-item",p.dataset.id=u,p.dataset.type=o,p.dataset.state="closed",p.dataset.dismissible=String(d),l!==null&&(p.dataset.hasProgress="true");const m={success:'<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>',error:'<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>',info:'<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>',warning:'<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>',loading:'<div class="toast-spinner"></div>',close:'<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>'};p.innerHTML=`
                <div class="toast-header">
                    <div class="toast-icon">${m[o]||m.info}</div>
                    <div class="toast-content">
                        <div class="toast-title">${a}</div>
                        ${n?`<div class="toast-desc">${n}</div>`:""}
                    </div>
                    <button class="toast-close" aria-label="Fechar">${m.close}</button>
                </div>
                <div class="toast-progress-bg">
                    <div class="toast-progress-fill" style="width: ${l||0}%"></div>
                </div>
            `,this.toastContainer.appendChild(p),this._updateToastStack();const f=this.toastContainer.querySelectorAll(".toast-item:not(.removing)");f.length>this.config.ui.toast.maxVisible&&this._removeToast(f[0]),s.swipeToClose!==!1&&this._setupToastGestures(p,u,h);const g=p.querySelector(".toast-close");return g&&(g.onclick=L=>{L.stopPropagation(),this.dismissToast(u)}),requestAnimationFrame(()=>{p.dataset.state="open",this._updateToastStack()}),d&&c!==1/0&&o!=="loading"&&setTimeout(()=>{this.dismissToast(u)},c),u}updateToast(e,t){var d;const s=(d=this.toastContainer)==null?void 0:d.querySelector(`[data-id="${e}"]`);if(!s)return;const{title:i,description:a,type:n,progress:o,dismissible:c}=t;if(n){s.dataset.type=n;const l={success:'<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>',error:'<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>',info:'<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>',warning:'<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>'},h=s.querySelector(".toast-icon");h&&(h.innerHTML=l[n]||l.info)}if(i){const l=s.querySelector(".toast-title");l&&(l.textContent=i)}if(a!==void 0){let l=s.querySelector(".toast-desc");a&&!l&&(l=document.createElement("div"),l.className="toast-desc",s.querySelector(".toast-content").appendChild(l)),l&&(l.textContent=a)}if(o!==void 0){s.dataset.hasProgress="true";const l=s.querySelector(".toast-progress-fill");l&&(l.style.width=`${o}%`)}c!==void 0&&(s.dataset.dismissible=String(c))}dismissToast(e){var s;const t=(s=this.toastContainer)==null?void 0:s.querySelector(`[data-id="${e}"]`);t&&this._removeToast(t)}toastSuccess(e,t,s={}){return this.toast({title:e,description:t,type:"success",...s})}toastError(e,t,s={}){return this.toast({title:e,description:t,type:"error",...s})}toastWarning(e,t,s={}){return this.toast({title:e,description:t,type:"warning",...s})}toastInfo(e,t,s={}){return this.toast({title:e,description:t,type:"info",...s})}toastLoading(e,t,s={}){return this.toast({title:e,description:t,type:"loading",duration:1/0,dismissible:!1,...s})}_removeToast(e){!e.isConnected||e.classList.contains("removing")||(e.classList.add("removing"),e.classList.remove("show"),setTimeout(()=>{e.remove(),this._updateToastStack()},300))}_updateToastStack(){const e=Array.from(this.toastContainer.querySelectorAll(".toast-item:not(.removing)")),t=e.length,s=this.config.ui.toast.maxVisible,i=document.querySelector(".bottom-nav");i&&getComputedStyle(i).display!=="none"&&!i.classList.contains("hidden")?this.toastContainer.style.transform="translateY(-70px)":this.toastContainer.style.transform="translateY(0)",e.forEach((n,o)=>{if(n.dataset.state==="closed")return;const c=t-1-o;if(c>=s){n.style.opacity="0",n.style.pointerEvents="none";return}const d=o*14,l=1-c*.05,u=this.config.ui.toast.position.startsWith("top")?d:-d;n.style.zIndex=1e3+o,n.style.opacity="1",n.style.transform=`translateY(${u}px) scale(${l})`,n.style.pointerEvents=o===t-1?"auto":"none"})}_setupToastGestures(e,t,s){let i=0,a=0,n=!1;const o=l=>{n=!0,i=l.touches?l.touches[0].clientX:l.clientX,e.style.transition="none"},c=l=>{if(!n)return;l.preventDefault(),a=(l.touches?l.touches[0].clientX:l.clientX)-i,a<0&&(a=0);const h=Math.max(.3,1-a/200);e.style.transform=`translateX(${a}px)`,e.style.opacity=h},d=()=>{n&&(n=!1,e.style.transition="",a>100?(e.style.transform="translateX(100%)",e.style.opacity="0",setTimeout(()=>{s&&s(),this._removeToast(e)},300)):(e.style.transform="",e.style.opacity="",this._updateToastStack()),a=0)};e.addEventListener("mousedown",o),window.addEventListener("mousemove",c),window.addEventListener("mouseup",d),e.addEventListener("touchstart",o,{passive:!1}),e.addEventListener("touchmove",c,{passive:!1}),e.addEventListener("touchend",d)}showLoading(){this.loadingOverlay&&this.loadingOverlay.classList.remove("hidden")}message(e,t="info",s=2e3){const i=document.createElement("div");i.className="message-overlay";const a={success:"✓",error:"✕",warning:"⚠",info:"ℹ"},n={success:"#10b981",error:"#ef4444",warning:"#f59e0b",info:"#3b82f6"};return i.innerHTML=`
                <div class="message-box message-${t}">
                    <div class="message-icon" style="color: ${n[t]}">${a[t]||a.info}</div>
                    <div class="message-text">${e}</div>
                </div>
            `,document.body.appendChild(i),requestAnimationFrame(()=>{i.classList.add("active")}),setTimeout(()=>{i.classList.remove("active"),setTimeout(()=>i.remove(),300)},s),i}hideLoading(){this.loadingOverlay&&this.loadingOverlay.classList.add("hidden")}toggleTheme(){const t=(document.documentElement.dataset.theme||"light")==="light"?"dark":"light";return this.setTheme(t),t}setTheme(e){document.documentElement.dataset.theme=e,this.storage.set("pref_theme",e),window.lucide&&window.lucide.createIcons(),document.querySelectorAll("#theme-toggle-sheet, .theme-toggle").forEach(s=>s.checked=e==="dark"),this._log(2,`🌓 Tema alterado para: ${e}`)}setAnimation(e){this.config.animation.type=e,document.documentElement.dataset.animation=e,this.storage.set("pref_animation",e),document.querySelectorAll(".animation-select").forEach(s=>s.value=e),this.toastInfo("Animação Alterada",`Estilo "${e}" aplicado com sucesso.`),this._log(2,`🎬 Animação alterada para: ${e}`)}_applyPreferences(){const e=this.storage.get("pref_theme");e?this.setTheme(e):this.config.ui.autoTheme&&window.matchMedia&&window.matchMedia("(prefers-color-scheme: dark)").matches&&this.setTheme("dark");const t=this.storage.get("pref_animation");t&&(this.config.animation.type=t,document.documentElement.dataset.animation=t,document.querySelectorAll(".animation-select").forEach(i=>i.value=t)),this._log(2,"⚙️ Preferências aplicadas")}openModal(e){return this.modal(e)}async logout(){var e;try{this.showLoading();const t=document.createElement("form");t.method="POST",t.action="/logout";const s=(e=document.querySelector('meta[name="csrf-token"]'))==null?void 0:e.content;if(s){const i=document.createElement("input");i.type="hidden",i.name="_token",i.value=s,t.appendChild(i)}document.body.appendChild(t),t.submit()}catch{this.hideLoading(),this.toastError("Erro ao sair","Não foi possível realizar o logout.")}}get db(){return this._db}set db(e){this._db=e}get storage(){return this._storage}set storage(e){this._storage=e}get queue(){return this._queue}set queue(e){this._queue=e}_registerPages(){document.querySelectorAll(".page").forEach(e=>{let t=e.id;t.startsWith(this.config.pagePrefix)&&(t=t.substring(this.config.pagePrefix.length)),this.pages.set(t,e);const s=e.dataset.level||"primary",i=e.dataset.parent||this.config.homePage,a=e.dataset.keepHistory==="true";this.pageHierarchy.set(t,{level:s,parent:i,keepHistory:a,element:e}),e.classList.contains("active")&&(this.current=t)})}_createElements(){this.backdrop=document.getElementById("backdrop"),this.backdrop||(this.backdrop=document.createElement("div"),this.backdrop.id="backdrop",document.body.appendChild(this.backdrop)),this._createToastContainer(),this.loadingOverlay=document.getElementById("loading-overlay")}_createToastContainer(){this.toastContainer=document.getElementById("toast-container"),this.toastContainer||(this.toastContainer=document.createElement("div"),this.toastContainer.id="toast-container",this.toastContainer.className=`toast-container toast-${this.config.ui.toast.position}`,document.body.appendChild(this.toastContainer))}_initManagers(){this.drawerManager=new k(this),this.sheetManager=new x(this),this.modalManager=new A(this),this.multiStepManager=new C(this),document.querySelectorAll(".drawer").forEach(e=>{this.drawerManager.register(e.id)}),document.querySelectorAll(".bottom-sheet").forEach(e=>{this.sheetManager.register(e.id)}),document.querySelectorAll("[data-multistep-sheet]").forEach(e=>{this.sheetManager&&this.sheetManager.register(e.id),this.multiStepManager&&this.multiStepManager.register(e.id)}),requestAnimationFrame(()=>{window.lucide&&window.lucide.createIcons()}),console.log("")}_setupEventListeners(){window.addEventListener("popstate",this._handlePopState),document.addEventListener("click",this._handleClick),this.backdrop&&this.config.ui.backdrop.closeOnClick&&this.backdrop.addEventListener("click",()=>{this.activeOverlays.length>0&&this.closeTopOverlay()}),document.addEventListener("keydown",e=>{e.key==="Escape"&&this.activeOverlays.length>0&&this.closeTopOverlay(),e.altKey&&e.key==="ArrowLeft"&&this.back()}),window.addEventListener("online",this._handleOnline),window.addEventListener("offline",this._handleOffline),this.config.doubleTapExit&&window.addEventListener("popstate",this._handleDoubleTapExit.bind(this))}_handleClick(e){const t=e.target,s=t.closest("[data-go]");if(s){e.preventDefault();const d=s.closest(".drawer"),l=s.closest(".bottom-sheet"),h=s.closest(".modal");if(d&&this.drawerManager&&typeof this.drawerManager.close=="function")try{this.drawerManager.close(d.id,!1)}catch{this._closeOverlay(`drawer-${d.id}`,!1)}else if(l&&this.sheetManager&&typeof this.sheetManager.close=="function")try{this.sheetManager.close(l.id,!1)}catch{this._closeOverlay(`sheet-${l.id}`,!1)}else if(h&&this.modalManager&&typeof this.modalManager.close=="function")try{this.modalManager.close(h.id)}catch{this._closeOverlay(`modal-${h.id}`,!1)}this.go(s.dataset.go);return}if(t.closest("[data-back]")){e.preventDefault(),this.back(e);return}const a=t.closest("[data-drawer]");if(a){e.preventDefault(),this.openDrawer(a.dataset.drawer);return}const n=t.closest("[data-sheet]");if(n){e.preventDefault(),this.openSheet(n.dataset.sheet);return}const o=t.closest("[data-modal]");if(o){e.preventDefault();const d=o.dataset.modal;document.getElementById(d)&&this.modal({template:d});return}if(t.closest("[data-close-overlay]")){e.preventDefault(),this.closeTopOverlay();return}}_handlePopState(e){this._log(2,"⬅️ Popstate detectado");let t,s=[];if(e.state&&e.state.page)t=e.state.page,s=e.state.overlays||[];else{const c=window.location.hash.slice(1);t=c&&this.pages.has(c)?c:this.config.homePage}if(this._log(2,`⬅️ Indo para: ${t}`),s.length<this.activeOverlays.length){const c=this.activeOverlays[this.activeOverlays.length-1];this._closeOverlay(c,!1);return}if(t===this.current)return;const i=this.pages.get(this.current),a=this.pages.get(t);if(!a)return;try{this._prepareBindingsForNavigation(t)}catch{}document.documentElement.dataset.animation=this.config.animation.type;const n=this.config.animation&&this.config.animation.backSpeed||Math.max(this.config.animation.speed/3,.06),o=getComputedStyle(document.documentElement).getPropertyValue("--spa-animation-speed")||this.config.animation.speed+"s";document.documentElement.style.setProperty("--spa-animation-speed",n+"s"),this._emitPageEvent(this.current,"page:leave"),this._clearPageClasses(i,a),i.classList.remove("active"),a.classList.add("revealing"),this.previousPage=this.current,this.current=t,setTimeout(()=>{a.classList.remove("revealing"),a.classList.add("active"),this._emitPageEvent(t,"page:enter"),document.documentElement.style.setProperty("--spa-animation-speed",o.trim()||this.config.animation.speed+"s")},n*1e3)}_handleDoubleTapExit(e){if(!this.config.doubleTapExit)return;const t=e.state;if(t&&t.type==="exit"){const s=Date.now();s-this.lastBackPress<this.config.doubleTapTimeout?window.close():(this.lastBackPress=s,this.toast("Pressione voltar novamente para sair","info"),history.pushState({page:this.current,overlays:[],type:"home"},"",`#${this.current}`))}}_handleOnline(){this._online=!0,this.toast("🌐 Conexão restaurada","success"),this._emit("spa:online"),this.queue&&this.queue.processAll()}_handleOffline(){this._online=!1,this.toast("📴 Você está offline","warning"),this._emit("spa:offline")}get isOnline(){return this._online}_setupInitialHistory(){console.log("SPA: Setting up initial history...");const e=window.location.hash.substring(1);if(e&&this.pages.has(e)&&(this.current=e,this.pages.forEach((s,i)=>{s.classList.toggle("active",i===e)})),console.log({hash:e}),["_modal","_sheet","_drawer"].some(s=>e.includes(s))){const s=e.split(/_(modal|sheet|drawer)/)[0];this.pages.has(s)&&(this.current=s,this.pages.forEach((i,a)=>{i.classList.toggle("active",a===s)}))}if(!history.state||!history.state.page){const s=this.pageHierarchy.get(this.current);history.replaceState({page:this.current,overlays:[],type:(s==null?void 0:s.level)||"home"},"",`#${this.current}`)}}async _initStorage(){try{console.log("SPA: _initStorage starting..."),console.log("SPA: initializing IndexedDBORM..."),this._db=new O("spa_app"),await this._db.init(),console.log("SPA: IndexedDBORM.init() resolved"),this._log(2,"💾 IndexedDB inicializado"),this._storage=new y("spa_"),this._log(2,"💾 localStorage inicializado"),console.log("SPA: initializing JobQueue..."),this._queue=new D(this._db),await this._queue.init(),console.log("SPA: JobQueue.init() resolved"),this._log(2,"📋 Job Queue inicializada"),console.log("SPA: _initStorage completed")}catch(e){console.error("Erro ao inicializar storage:",e),this._emit("spa:error",{error:e})}}_initPWA(){this.config.pwa&&this.config.pwa.enabled&&(this.pwa=new S(this,this.config.pwa)),this.notifications=new E(this),typeof this.notifications.init=="function"&&this.notifications.init()}_clearPageClasses(e,t){const s=["active","prev","revealing"];e&&e.classList.remove(...s),t&&t.classList.remove(...s)}_showBackdrop(){this.backdrop&&this.backdrop.classList.add("show")}_hideBackdrop(){this.backdrop&&this.backdrop.classList.remove("show")}_emit(e,t={}){const s=new CustomEvent(e,{detail:t});document.dispatchEvent(s)}_emitPageEvent(e,t){const s=this.pages.get(e);if(s){const i=new CustomEvent(t,{detail:{pageId:e,element:s}});s.dispatchEvent(i),document.dispatchEvent(i)}t==="page:enter"&&this._updatePageBindings()}_getLevelValue(e){const t={home:0,primary:1,secondary:2,tertiary:3};return t[e]!==void 0?t[e]:1}_prepareBindingsForNavigation(e){document.querySelectorAll("[data-show-on]").forEach(t=>{t.dataset.showOn.split(",").map(i=>i.trim()).includes(e)||(t.style.opacity="0",t.style.pointerEvents="none",t.setAttribute("disabled","true"))}),document.querySelectorAll("[data-hide-on]").forEach(t=>{t.dataset.hideOn.split(",").map(i=>i.trim()).includes(e)&&(t.style.opacity="0",t.style.pointerEvents="none",t.setAttribute("disabled","true"))})}_updatePageBindings(){const e=this.current;document.querySelectorAll("[data-show-on]").forEach(t=>{t.dataset.showOn.split(",").map(i=>i.trim()).includes(e)?(t.style.display="",t.style.opacity="",t.style.pointerEvents="",t.removeAttribute("disabled"),t.classList.remove("hidden")):(t.style.display="none",t.classList.add("hidden"))}),document.querySelectorAll("[data-hide-on]").forEach(t=>{t.dataset.hideOn.split(",").map(i=>i.trim()).includes(e)?(t.style.display="none",t.classList.add("hidden")):(t.style.display="",t.style.opacity="",t.style.pointerEvents="",t.removeAttribute("disabled"),t.classList.remove("hidden"))}),document.querySelectorAll("[data-active-on]").forEach(t=>{t.dataset.activeOn.split(",").map(i=>i.trim()).includes(e)?t.classList.add("active"):t.classList.remove("active")}),document.querySelectorAll("[data-go]").forEach(t=>{t.dataset.go===e?t.classList.add("active"):t.classList.remove("active")})}_log(e,t,s=null){this.config.debug.enabled&&(e>this.config.debug.level||(s?console.log(t,s):console.log(t)))}_deepMerge(e,t){const s=Object.assign({},e);return this._isObject(e)&&this._isObject(t)&&Object.keys(t).forEach(i=>{this._isObject(t[i])?i in e?s[i]=this._deepMerge(e[i],t[i]):Object.assign(s,{[i]:t[i]}):Object.assign(s,{[i]:t[i]})}),s}_isObject(e){return e&&typeof e=="object"&&!Array.isArray(e)}destroy(){window.removeEventListener("popstate",this._handlePopState),document.removeEventListener("click",this._handleClick),window.removeEventListener("online",this._handleOnline),window.removeEventListener("offline",this._handleOffline),this.pages.clear(),this.pageHierarchy.clear(),this.activeOverlays=[],this._log(1,"🗑️ SPA Framework destruído")}}typeof window<"u"&&(window.SPA=P);function $(r){q(),H()}function q(r){W()}function W(){var t,s;const r=((t=window.SPA_CONFIG)==null?void 0:t.cities)||["Uberlândia","Uberaba","Araguari"],e=((s=window.SPA_CONFIG)==null?void 0:s.insurances)||["Unimed","Bradesco","Particular"];_("whatsapp-cities",r,"whatsapp-city"),_("whatsapp-insurances",e,"whatsapp-insurance")}function _(r,e,t){const s=document.getElementById(r);s&&(s.innerHTML="",e.forEach(i=>{const a=document.createElement("button");a.type="button",a.className="px-3 py-1.5 rounded-full border text-sm bg-white hover:bg-slate-50 transition-colors",a.textContent=i,a.dataset.value=i,a.onclick=()=>{Array.from(s.children).forEach(o=>{o.classList.remove("ring-2","ring-red-400","bg-red-50")}),a.classList.add("ring-2","ring-red-400","bg-red-50");const n=document.getElementById(t);n&&(n.value=i)},s.appendChild(a)}))}function N(r,e){const t=window.app,s=document.getElementById("whatsapp-topic");s&&(s.value=e);const i=document.getElementById("whatsapp-intent");i&&(i.value=r);const a=document.getElementById("whatsapp-dob-row");a&&(a.style.display=e==="results"?"block":"none"),t==null||t.closeSheet(),setTimeout(()=>{t==null||t.openSheet("tpl-sheet-whatsapp-form")},300)}function F(r){let e=r.value.replace(/\D/g,"");e.length>2&&(e=e.slice(0,2)+"/"+e.slice(2)),e.length>5&&(e=e.slice(0,5)+"/"+e.slice(5,9)),r.value=e}function z(){var l,h,u,p,m,f,v,g;const r=window.app,e=(l=document.getElementById("whatsapp-name"))==null?void 0:l.value.trim(),t=(h=document.getElementById("whatsapp-city"))==null?void 0:h.value,s=(u=document.getElementById("whatsapp-insurance"))==null?void 0:u.value,i=((p=document.getElementById("whatsapp-intent"))==null?void 0:p.value)||"",a=((m=document.getElementById("whatsapp-topic"))==null?void 0:m.value)||"other",n=(f=document.getElementById("whatsapp-dob"))==null?void 0:f.value;if(!e){r==null||r.toastWarning("Nome obrigatório","Informe o nome do paciente");return}if(!t){r==null||r.toastWarning("Cidade obrigatória","Selecione a cidade");return}let o=i;o+=`

*Nome:* ${e}`,o+=`
*Cidade:* ${t}`,s&&(o+=`
*Convênio:* ${s}`),a==="results"&&n&&(o+=`
*Data de Nascimento:* ${n}`);const d=`https://wa.me/${((g=(v=window.SPA_CONFIG)==null?void 0:v.contacts)==null?void 0:g.whatsappNumber)||"5534999999999"}?text=${encodeURIComponent(o)}`;r==null||r.closeSheet(),setTimeout(()=>{window.open(d,"_blank"),r==null||r.toastSuccess("Abrindo WhatsApp...","")},300)}function j(r){var i,a;const e=window.app,s=`https://wa.me/${((a=(i=window.SPA_CONFIG)==null?void 0:i.contacts)==null?void 0:a.whatsappNumber)||"5534999999999"}?text=${encodeURIComponent(r)}`;e==null||e.closeSheet(),setTimeout(()=>{window.open(s,"_blank"),e==null||e.toastSuccess("Abrindo WhatsApp...","")},300)}function H(r){const e=document.getElementById("theme-toggle-sheet");if(e){const s=JSON.parse(localStorage.getItem("lamarck_settings")||"{}");e.checked=s.theme==="dark"}const t=document.querySelector(".animation-select");if(t){const s=JSON.parse(localStorage.getItem("lamarck_settings")||"{}");t.value=s.animation||"fade"}}window.abrirWhatsAppTopic=N;window.abrirWhatsApp=j;window.maskDob=F;window.submitWhatsAppMiddleware=z;function w(){typeof lucide<"u"&&lucide.createIcons&&lucide.createIcons()}document.addEventListener("DOMContentLoaded",()=>{window.app=new P,app.init({homePage:"home",animation:{type:"fade",speed:.35},ui:{autoTheme:!1},pwa:{enabled:!0,showBanner:!1}}),app,$(app),w();try{typeof b<"u"&&(window.Html5Qrcode=b)}catch(r){console.warn("Could not expose Html5Qrcode to window",r)}document.addEventListener("spa:page-loaded",r=>{try{w()}catch(e){console.warn("initLucide error",e)}});try{const r=function(){let s;return function(){clearTimeout(s),s=setTimeout(()=>{try{w()}catch{}},120)}}();let e=!1;new MutationObserver(s=>{for(const i of s)if(i.addedNodes&&i.addedNodes.length){for(const a of i.addedNodes)if(a.nodeType===1){if(e)return;if(a.matches&&a.matches("[data-lucide]")){e=!0,r(),setTimeout(()=>e=!1,400);return}if(a.querySelector&&a.querySelector("[data-lucide]")){e=!0,r(),setTimeout(()=>e=!1,400);return}}}}).observe(document.body,{childList:!0,subtree:!0})}catch(r){console.warn("Lucide observer init failed",r)}console.log("🚀 Lamarck SPA initialized")});
