@props(['links' => []])

<nav class="fixed z-30 top-[72px] left-0 px-6 py-2 bg-header/60 rounded-br-full border-b border-r border-border backdrop-blur-md">
  <x-container>
    <ol class="flex items-center space-x-2 text-sm">
      <!-- Home Link -->
      <li>
        <a href="/" class="flex items-center text-primary hover:text-primary-dark transition-colors">
        <svg class="w-6 h-6 text-primary">
              <use href="#icon-home" />
            </svg>
          
        </a>
      </li>

      <!-- Dynamic Links -->
      @foreach($links as $link)
        <li class="flex items-center">
          <span class="mx-2 text-text-secondary">/</span>
          @if(!$loop->last)
            <a href="{{ $link['url'] }}" class="text-primary hover:text-primary-dark transition-colors">
              {{ $link['label'] }}
            </a>
          @else
            <span class="text-text-secondary">{{ $link['label'] }}</span>
          @endif
        </li>
      @endforeach
    </ol>
  </x-container>
</nav>