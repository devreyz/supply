@props([
    'align' => 'right',
    'width' => '64',
    'contentClasses' => 'bg-background shadow-lg p-4',
    'overlayClasses' => 'bg-black bg-opacity-50 h-screen',
])

@php
    switch ($align) {
        case 'left':
            $alignmentClasses = 'left-0';
            break;
        case 'right':
        default:
            $alignmentClasses = 'right-0';
            break;
    }

    switch ($width) {
        case '48':
            $width = 'w-48';
            break;
        case '64':
            $width = 'w-64';
            break;
        default:
            $width = 'w-48';
            break;
    }
@endphp

<div x-data="{ open: false }"
     x-effect="open ? document.body.classList.add('overflow-hidden') : document.body.classList.remove('overflow-hidden')">
    <!-- Botão de Toggle -->
    <button @click="open = !open" class="relative scale-75 z-10 items-center justify-center w-10 h-10 bg-transparent text-header-on rounded-md focus:ring-input-ring focus:border-input-focus md:hidden flex overflow-hidden">
        <div class="absolute rounded-full w-8 h-0.5 top-[8px] bg-header-on transition-transform duration-300" :class="{'-rotate-45 translate-y-3 ': open, 'rotate-0': !open}"></div>
        <div class="absolute rounded-full transition-[opacity_transform] duration-700 my-0.5" :class="{'opacity-0 left-48 bg-primary w-12 h-3': open, 'opacity-100 bg-header-on left-0.5 w-4 h-0.5': !open}"></div>
        <div class="absolute rounded-full w-8 h-0.5 top-[32px] bg-header-on transition-transform duration-300" :class="{'rotate-[-135deg] -translate-y-3': open, 'rotate-0': !open}"></div>
    </button>

    <!-- Overlay com x-cloak -->
    <div x-cloak
         x-show="open"
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed w-screen max-w-screen h-screen inset-0 z-[-100] bg-header/60 bg-opacity-50 backdrop-blur-xl"
         @click="open = false"></div>

    <!-- Sidebar com x-cloak -->
    <div x-cloak
         x-show="open"
         x-transition:enter="transition transform ease-out duration-500"
         x-transition:enter-start="translate-y-[-100%]"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition transform ease-in duration-200"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-[-100%]"
         class="fixed md:hidden inset-y-0 left-0 z-[-100] w-screen max-w-screen h-screen border-b-2 border-primary bg-header {{ $contentClasses }}">
        <div id="sidebar-content" class="flex flex-col h-screen relative">
            <!-- Conteúdo -->
            <x-container class="flex flex-col flex-1 overflow-y-auto items-end justify-start h-full">
                {{ $slot }}
            </x-container>
        </div>
    </div>
</div>
