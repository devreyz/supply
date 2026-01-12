@props([
'unidades',
'initialCenter' => [-14.752461470399224, -43.9416878230808],
'initialZoom' => 10
])
<div
  x-data="mapUnidades({{ json_encode($unidades) }}, {{ json_encode($initialCenter) }}, {{ $initialZoom }})"
  x-on:focus-map.window="focusOnMap($event.detail)"
  class="relative rounded-2xl overflow-hidden shadow-xl border border-border/20">
  <div id="mapa" x-ref="map" class="h-[520px] w-full z-0"></div>
  <div class="absolute bg-background/70 border border-border/50 backdrop-blur p-4 rounded-md top-4 right-4 flex flex-col gap-4 h-fit max-h- overflow-auto">
    @foreach($unidades as $unidade)
    <button
      type="button"
      class="bg-primary text-sm text-primary-on px-4 py-1 rounded"
      x-on:click="$dispatch('focus-map', {{ json_encode($unidade['coordenadas']) }})">
      {{ $unidade['nome'] }}
    </button>
    @endforeach
  </div>
</div>

<!-- Leaflet JS & CSS -->
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />

<style>
  .leaflet-control-attribution {
    display: none;
  }

  .leaflet-popup-content-wrapper {
    border-radius: 0.75rem;
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    z-index: 999;
  }

  #mapa .leaflet-popup-content a {
    color: hsl(var(--on-secondary));
    text-align: center;
  }

  #mapa .leaflet-popup-content-wrapper {
    background-color: hsl(var(--background) / 0.5);
    backdrop-filter: blur(10px);
    width: fit-content;
  }
</style>

<script>
  function mapUnidades(unidades, initialCenter, initialZoom) {
    return {
      unidades: unidades,
      map: null,
      markers: [],
      init() {
        // Inicializa o mapa com o centro e zoom informados
        this.map = L.map(this.$refs.map).setView(initialCenter, initialZoom);

        // Adiciona a camada de tiles do OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '&copy; OpenStreetMap contributors'
        }).addTo(this.map);

        // Cria os marcadores para cada unidade
        this.markers = this.unidades.map(unidade => {
          const marker = L.marker(unidade.coordenadas).addTo(this.map);
            marker.bindPopup(`
              <div class="flex flex-col min-w-[200px]">
                <div class="flex flex-1 w-full">
                <img src="/img/${unidade.imagem}" class="w-[72px] h-[72px] rounded" alt="${unidade.nome}" />
                <div class="w-full pl-3">
                  <h3 class="font-bold text-base">${unidade.nome}</h3>
                  <a href="https://www.google.com/maps/dir/?api=1&destination=${unidade.coordenadas}"
                   target="_blank"
                   class="text-lg text-secondary-on hover:text-success bg-secondary hover:bg-primary transition-colors duration-300 px-4 py-1 rounded-full w-fit text-center flex items-center justify-center"
                   aria-label="Ver no Google Maps">
                   <span class="flex items-center justify-center w-full">
                   Rotas  
                   <svg class="w-6 h-6 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                   <use xlink:href="#icon-location" />
                   </svg>
                   </span>
                </a>
                </div>
                </div>
                <div class="mt-2">
                <p class="text-gray-600">${unidade.endereco}</p>
                </div>
              </div>
              `);
          return marker;
        });

        // Adiciona EventListener do Eventemitter para focar no mapa
        window.eventEmitter.on('focus-map', coords => {
          this.map.flyTo(coords, 20);
          const marker = this.markers.find(m => {
            const latlng = m.getLatLng();
            return latlng.lat === coords[0] && latlng.lng === coords[1];
          });
          if (marker) {
            marker.openPopup();
          }
        });
      },
      focusOnMap(coords) {
        // Centraliza o mapa na coordenada e abre o popup do marcador
        this.map.flyTo(coords, 16);
        const marker = this.markers.find(m => {
          const latlng = m.getLatLng();
          return latlng.lat === coords[0] && latlng.lng === coords[1];
        });
        if (marker) {
          marker.openPopup();
        }
      }
    }
  }
</script>