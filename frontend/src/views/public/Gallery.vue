<template>
  <div class="gallery-page">
    <div class="page-header">
      <div class="container">
        <h1>Galeria de Fotos</h1>
        <p>Explore as memórias visuais que contam a história do Padre Tiago e da nossa comunidade.</p>
      </div>
    </div>

    <div class="container">
      <div v-if="loading" class="loading-state">
        Carregando galerias...
      </div>
      
      <div v-else-if="error" class="error-state">
        {{ error }}
      </div>
      
      <div v-else-if="galleries.length === 0" class="empty-state">
        Nenhuma galeria de fotos disponível no momento.
      </div>
      
      <div v-else class="galleries-grid">
        <router-link 
          v-for="gallery in galleries" 
          :key="gallery.id" 
          :to="{ name: 'GalleryDetail', params: { id: gallery.slug || gallery.id } }"
          class="gallery-card"
        >
          <div class="img-wrapper">
            <img v-if="gallery.cover_thumbnail_url" :src="gallery.cover_thumbnail_url" :alt="gallery.title" loading="lazy" />
            <div v-else class="no-cover">Sem Capa</div>
            <div class="photo-count-badge">
              {{ gallery.photo_count }} {{ gallery.photo_count === 1 ? 'foto' : 'fotos' }}
            </div>
          </div>
          <div class="gallery-info">
            <h2>{{ gallery.title }}</h2>
            <p v-if="gallery.description">{{ gallery.description }}</p>
          </div>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../api';

const galleries = ref([]);
const loading = ref(true);
const error = ref('');

onMounted(async () => {
  try {
    const res = await api.get('/galleries');
    galleries.value = res.data;
  } catch (err) {
    error.value = 'Ocorreu um erro ao carregar o acervo fotográfico.';
    console.error(err);
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
.page-header {
  background-color: var(--color-primary, #0B1C3D);
  color: white;
  padding: 4rem 0;
  text-align: center;
  margin-bottom: 3rem;
  border-bottom: 4px solid var(--color-secondary, #D4AF37);
}

.page-header h1 {
  margin: 0 0 1rem;
  font-size: 2.5rem;
}

.page-header p {
  font-size: 1.1rem;
  opacity: 0.9;
  max-width: 600px;
  margin: 0 auto;
}

.loading-state, .error-state, .empty-state {
  text-align: center;
  padding: 4rem 0;
  color: #666;
  font-size: 1.1rem;
}

.galleries-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 2rem;
  margin-bottom: 4rem;
}

.gallery-card {
  background: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 4px 6px rgba(0,0,0,0.05);
  text-decoration: none;
  color: inherit;
  display: flex;
  flex-direction: column;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.gallery-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 15px rgba(0,0,0,0.1);
}

.img-wrapper {
  position: relative;
  height: 220px;
  background-color: #f8f9fa;
  overflow: hidden;
}

.img-wrapper img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.gallery-card:hover .img-wrapper img {
  transform: scale(1.05);
}

.no-cover {
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #adb5bd;
  font-size: 1.1rem;
}

.photo-count-badge {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: rgba(11, 28, 61, 0.85);
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 500;
}

.gallery-info {
  padding: 1.5rem;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.gallery-info h2 {
  color: var(--color-primary, #0B1C3D);
  margin: 0 0 0.5rem;
  font-size: 1.35rem;
}

.gallery-info p {
  color: #6c757d;
  margin: 0;
  font-size: 0.95rem;
  line-height: 1.5;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
