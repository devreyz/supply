<div {{ $attributes->merge(['class' => 'relative w-full overflow-hidden']) }}>
    <!-- Skeleton Loader -->
    <div class="skeleton absolute inset-0 bg-gray-200 animate-pulse"></div>
    <!-- Imagem Lazy Load -->
    <img
        src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Crect width='100' height='100' fill='%23ddd'%3E%3C/rect%3E%3C/svg%3E"
        data-src="{{ $src }}"
        alt="{{ $alt }}"
        class="lazy-image w-full h-full object-cover opacity-0 transition-opacity duration-300 ease-in-out"
        loading="lazy"
        onlo="this.style.opacity=1; this.previousElementSibling.style.display='none';">
</div>
