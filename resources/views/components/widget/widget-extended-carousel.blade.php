<div class="bento-widget bento-extended-banner-carousel span-full">
    <!-- Swiper CSS (CDN) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <div class="swiper-container-wrapper">
        <div class="swiper extendedBannerSwiper">
            <div class="swiper-wrapper">
                <!-- Slide 1: Welcome -->
                <div class="swiper-slide banner-slide" data-bg="https://images.unsplash.com/photo-1576091160550-217359f4ecf8?auto=format&fit=crop&w=1200&q=40">
                    <div class="slide-overlay"></div>
                    <div class="slide-content">
                        <div class="content-text">
                            <span class="badge-new">Bem-vindo</span>
                            <h3>Olá, {{ explode(' ', auth()->user()->name ?? 'Visitante')[0] }}!</h3>
                            <p>Que bom ter você de volta ao Lamarck.</p>
                        </div>
                        <div class="content-action">
                            <button class="btn-banner" data-go="perfil">Ver Perfil</button>
                        </div>
                    </div>
                </div>

                <!-- Slide 2: Promotion -->
                <div class="swiper-slide banner-slide" data-bg="https://images.unsplash.com/photo-1516549221187-ea4bb1558ec7?auto=format&fit=crop&w=1200&q=40" data-blur="3">
                    <div class="slide-overlay"></div>
                    <div class="slide-content">
                        <div class="content-text">
                            <span class="badge-promo">Destaque</span>
                            <h3>Exames do Coração</h3>
                            <p>Check-up completo com 20% de desconto este mês.</p>
                        </div>
                        <div class="content-action">
                            <button class="btn-banner" data-go="exams">Saiba Mais</button>
                        </div>
                    </div>
                </div>

                <!-- Slide 3: App Info -->
                <div class="swiper-slide banner-slide" data-bg="https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?auto=format&fit=crop&w=1200&q=40">
                    <div class="slide-overlay"></div>
                    <div class="slide-content">
                        <div class="content-text">
                            <span class="badge-info">Novidade</span>
                            <h3>Sistema Inteligente</h3>
                            <p>Seus resultados agora analisados por nossa IA.</p>
                        </div>
                        <div class="content-action">
                            <button class="btn-banner secondary" data-go="chat">Abrir Chat</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Custom Animated Bullets -->
            <div class="swiper-pagination"></div>
        </div>
    </div>

    <style>
        .bento-extended-banner-carousel {
            position: relative;
            padding: 0;
            overflow: hidden;
            border-radius: 20px;
            background: #111; /* Fallback */
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            height: auto; /* allow content to define height */
            min-height: 180px; /* Compact minimum */
            margin-bottom: 1.25rem;
            width: 100%; /* Ensure full scale */
            box-sizing: border-box;
        }

        /* Ensure swiper and slides stretch vertically to the container height */
        .extendedBannerSwiper,
        .extendedBannerSwiper .swiper-wrapper,
        .extendedBannerSwiper .swiper-slide {
            width: 100%;
            height: 100%;
        }

        .banner-slide {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            padding: 0 2rem;
            min-height: 180px; /* ensure visual stability */
            height: 100%;
            width: 100% !important; /* Force swiper to respect full container */
            background-size: cover;
            background-position: center;
            color: #fff;
            transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
            box-sizing: border-box;
        }

        /* Set BG dynamically via data-bg in JS to keep HTML clean */
        .banner-slide::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: var(--bg-url);
            background-size: cover;
            background-position: center;
            z-index: 0;
            filter: var(--banner-blur, none);
        }

        .slide-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, rgba(17, 24, 39, 0.95) 0%, rgba(17, 24, 39, 0.5) 50%, rgba(17, 24, 39, 0.1) 100%);
            z-index: 1;
        }

        .slide-content {
            position: relative;
            z-index: 2;
            display: flex;
            width: 100%;
            justify-content: space-between;
            align-items: center;
            gap: 1.5rem;
            padding-right: 1rem; /* Space for dots on the right if needed */
        }

        .content-text {
            max-width: 65%;
        }

        .content-text h3 {
            font-size: 1.4rem;
            font-weight: 800;
            margin: 0.25rem 0;
            letter-spacing: -0.02em;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .content-text p {
            font-size: 0.85rem;
            opacity: 0.9;
            margin: 0;
            line-height: 1.3;
        }

        .badge-new, .badge-promo, .badge-info {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 50px;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        .badge-new { background: var(--spa-primary, #dc2626); color: #fff; box-shadow: 0 0 15px rgba(220, 38, 38, 0.4); }
        .badge-promo { background: #f59e0b; color: #fff; }
        .badge-info { background: #3b82f6; color: #fff; }

        .btn-banner {
            background: #fff;
            color: #111827;
            border: none;
            padding: 10px 22px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .btn-banner:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .btn-banner.secondary {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* PAGINATION BULLETS - Moved to Right */
        .bento-extended-banner-carousel .swiper-pagination {
            bottom: 12px !important;
            right: 2rem !important;
            left: auto !important;
            text-align: right;
            width: auto;
            display: flex;
            gap: 2px;
            z-index: 10;
        }

        .bento-extended-banner-carousel .swiper-pagination-bullet {
            width: 8px;
            height: 8px;
            background: rgba(255, 255, 255, 0.3);
            opacity: 1;
            margin: 0 4px !important;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border-radius: 4px;
        }

        .bento-extended-banner-carousel .swiper-pagination-bullet-active {
            width: 28px;
            background: var(--spa-primary, #dc2626);
            box-shadow: 0 0 15px rgba(220, 38, 38, 0.8);
        }

        /* TRANSITIONS */
        .swiper-slide-active .content-text {
            animation: slideInUp 0.6s both 0.2s;
        }
        .swiper-slide-active .content-action {
            animation: slideInUp 0.6s both 0.4s;
        }

        @keyframes slideInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 640px) {
            /* increase min-height on small devices so content doesn't get clipped */
            .bento-extended-banner-carousel { min-height: 260px; }

            /* Make slides stretch vertically and center content */
            .banner-slide { align-items: stretch; padding: 0 1.25rem; }
            .slide-content { 
                flex-direction: column; 
                align-items: stretch; 
                justify-content: center; 
                gap: 0.75rem; 
                height: 260px;
                padding-bottom: 1rem; /* keep space for pagination */
            }

            .content-text { max-width: 100%; }

            /* Keep bullets to the right/bottom but away from action buttons */
            .bento-extended-banner-carousel .swiper-pagination { 
                right: 1.25rem !important; 
                bottom: 12px !important;
            }
        }
    </style>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        (function(){
            function initBanner() {
                const slides = document.querySelectorAll('.banner-slide');
                slides.forEach(slide => {
                    const bg = slide.getAttribute('data-bg');
                    const blur = slide.getAttribute('data-blur');
                    if (bg) slide.style.setProperty('--bg-url', `url('${bg}')`);
                    if (blur) slide.style.setProperty('--banner-blur', `blur(${blur}px)`);
                });

                try {
                    new Swiper('.extendedBannerSwiper', {
                        loop: true,
                        speed: 800,
                        autoplay: {
                            delay: 7000,
                            disableOnInteraction: false,
                        },
                        effect: 'fade',
                        fadeEffect: {
                            crossFade: true
                        },
                        pagination: {
                            el: '.extendedBannerSwiper .swiper-pagination',
                            clickable: true,
                        },
                    });
                } catch(e) { console.error("Extended Carousel Init Error", e); }
            }

            // Init on first load
            initBanner();
            
            // Re-init if SPA reloads content
            document.addEventListener('spa:page-loaded', (e) => {
                if(e.detail.id === 'home') initBanner();
            });
        })();
    </script>
    @endpush
</div>
