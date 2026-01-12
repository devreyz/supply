@props(['slides' => []])

<div class="swiper-container relative max-w-screen overflow-hidden w-full md:h-[75lvh] md:min-h-96 mb:max-h-[80%] aspect-square md:aspect-[16/9]">
    <!-- Wrapper dos Slides -->
    <div class="swiper-wrapper">
        @foreach ($slides as $slide)
        <div class="swiper-slide relative z-0">
            <!-- Imagem do Slide -->
            @if(isset($slide['url']))
                @php
                    $hasHeroContent = isset($slide['content']) || isset($slide['title']) || isset($slide['description']) || isset($slide['button_text']);
                    $shouldWrapImage = isset($slide['link']) && !$hasHeroContent;
                @endphp

                @if($shouldWrapImage)
                    <a href="{{ $slide['link'] }}" class="block h-full w-full">
                        <img src="{{ asset('storage/' . $slide['url']) }}" alt="{{ $slide['title'] ?? '' }}" class="object-contain absolute z-0 w-full h-full">
                    </a>
                @else
                    <img src="{{ asset('storage/' . $slide['url']) }}" alt="{{ $slide['title'] ?? '' }}" class="object-contain absolute z-0 w-full h-full">
                @endif
            @endif

            <!-- Hero Section -->
            @if($hasHeroContent)
                <div class="absolute z-[10] w-full bg-gradient-to-t from-background via-background/90 to-background/40 h-full"></div>
                <div class="absolute inset-0 z-20 flex items-center justify-center">
                    <div class="relative container mx-auto flex items-center justify-center">
                        <div class="md:w-[75%] w-full px-10 text-white">
                            @if(isset($slide['content']))
                                {!! $slide['content'] !!}
                            @else
                                @if(isset($slide['title']))
                                <h1 class="text-4xl font-bold mb-8">{{ $slide['title'] }}</h1>
                                @endif
                                @if(isset($slide['description']))
                                <p class="text-lg text-text mb-6">{{ $slide['description'] }}</p>
                                @endif
                                @if(isset($slide['link']) && isset($slide['button_text']))
                                <x-link href="{{ $slide['link'] }}">
                                  <span class="px-6 min-w-32 text-center py-2">
                                    {{ $slide['button_text'] }}
                                  </span>
                                </x-link>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
        @endforeach
    </div>

    <!-- Botões de navegação -->
    <div class="swiper-button-prev after:primary z-0"></div>
    <div class="swiper-button-next after:primary z-0"></div>

    <!-- Indicadores (Pontos) -->
    <div class="swiper-pagination z-0"></div>
</div>

<!-- Incluir o arquivo CSS e JS do Swiper -->
<link rel="stylesheet" href="{{ asset('css/swiper/swiper.css') }}">
<script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>

<script>
    // Função para inicializar ou atualizar o Swiper
    function initializeSwiper() {
        const swiper = new Swiper('.swiper-container', {
            loop: true,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            spaceBetween: 10,
        });

        swiper.update();
    }

    document.addEventListener('DOMContentLoaded', initializeSwiper);
    document.addEventListener('livewire:load', initializeSwiper);
    document.addEventListener('livewire:updated', initializeSwiper);
    window.addEventListener('popstate', initializeSwiper);
</script>