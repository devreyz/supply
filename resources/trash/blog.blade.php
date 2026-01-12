<x-app-layout title="Blog">
  <x-breadcrumb :links="[
    ['url' => '', 'label' => 'Blogs']]" />
  
  <section class="py-20 bg-white">
    <x-container>
      <div class="max-w-3xl mx-auto mb-12" data-aos="fade-up">
        <div class="relative">
          <input type="text" placeholder="Buscar artigo..."
            class="w-full px-6 py-4 rounded-xl border border-border focus:ring-2 focus:ring-primary focus:border-transparent"
            x-data="{ search: '' }" x-model="search">
          <svg class="w-6 h-6 absolute right-4 top-4 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
      </div>

      <!-- Lista de Blogs -->
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8" x-data="{ search: '' }">
        <template x-for="post in filteredPosts" :key="post.id">
          <div class="bg-white shadow-lg rounded-xl overflow-hidden hover:shadow-xl transition-shadow duration-300">
            <img :src="post.image" alt="" class="w-full h-48 object-cover">
            <div class="p-6">
              <h2 class="text-xl font-semibold mb-2" x-text="post.title"></h2>
              <p class="text-text-secondary text-sm mb-4" x-text="post.excerpt"></p>
              <a :href="post.link" class="text-primary font-semibold">Ler mais →</a>
            </div>
          </div>
        </template>
      </div>
    </x-container>
  </section>

  <script>
    document.addEventListener('alpine:init', () => {
      Alpine.data('blogSearch', () => ({
        search: '',
        posts: [{
            id: 1,
            title: 'Postagem 1',
            excerpt: 'Resumo do primeiro post...',
            link: '#',
            image: 'https://via.placeholder.com/400'
          },
          {
            id: 2,
            title: 'Postagem 2',
            excerpt: 'Resumo do segundo post...',
            link: '#',
            image: 'https://via.placeholder.com/400'
          },
          {
            id: 3,
            title: 'Postagem 3',
            excerpt: 'Resumo do terceiro post...',
            link: '#',
            image: 'https://via.placeholder.com/400'
          }
        ],
        get filteredPosts() {
          return this.posts.filter(post => post.title.toLowerCase().includes(this.search.toLowerCase()));
        }
      }));
    });
  </script>
</x-app-layout>