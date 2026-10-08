<template>
  <section class="gallery-section">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">{{ siteSettings.gallery.title }}</h2>
      </div>

      <div class="gallery-preview">
        <div v-if="loading && (!galleries || galleries.length === 0)" class="loading-state">
          <div class="spinner"></div>
        </div>
        
        <div v-else-if="!galleries || galleries.length === 0" class="empty-state">
          <p>Galeria sendo atualizada.</p>
        </div>

        <div v-else class="photo-grid">
          <router-link 
            v-for="gallery in galleries.slice(0, 4)" 
            :key="gallery.id" 
            :to="{ name: 'GalleryDetail', params: { id: gallery.slug || gallery.id } }"
            class="photo-item"
          >
            <img v-if="gallery.cover_thumbnail_url" :src="gallery.cover_thumbnail_url" :alt="gallery.title || 'Galeria'" loading="lazy" />
            <div v-else class="no-cover">Sem Capa</div>
            <div class="photo-overlay">
              <span v-if="gallery.title">{{ gallery.title }}</span>
              <span v-else>Ver Galeria</span>
            </div>
          </router-link>
        </div>
      </div>

      <div class="section-actions">
        <router-link :to="siteSettings.gallery.ctaLink" class="btn btn-primary">
          {{ siteSettings.gallery.ctaText }}
        </router-link>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { siteSettings } from '../../config/siteSettings';
import api from '../../api';

// Essa é a prévia (home), então vamos buscar apenas as primeiras fotos (limit=4).
// A lógica de Infinite Scroll (Intersection Observer) será colocada na View da Galeria (Gallery.vue).
const galleries = ref([]);
const loading = ref(true);

onMounted(async () => {
  try {
    const res = await api.get('/galleries?limit=4');
    let data = Array.isArray(res.data) ? res.data : (res.data.data || []);
    galleries.value = data;
  } catch (err) {
    console.error('Erro ao buscar fotos:', err);
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
.gallery-section {
  padding: 6rem 0;
  background-color: white;
}

.section-header {
  text-align: center;
  margin-bottom: 3rem;
}

.section-title {
  font-size: 2.2rem;
}

.photo-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 1rem;
  margin-bottom: 3rem;
}

.photo-item {
  position: relative;
  border-radius: 8px;
  overflow: hidden;
  aspect-ratio: 1;
  background-color: #f1f3f5;
  cursor: pointer;
}

.photo-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.photo-item:hover img {
  transform: scale(1.1);
}

.photo-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(10, 17, 40, 0.7);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s ease;
  font-weight: 600;
  font-size: 1.1rem;
}

.photo-item:hover .photo-overlay {
  opacity: 1;
}

.section-actions {
  text-align: center;
}

.btn-primary {
  padding: 1rem 2rem;
  font-size: 1.1rem;
  text-decoration: none;
  display: inline-block;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid rgba(10, 17, 40, 0.1);
  border-left-color: var(--color-secondary);
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.no-cover {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #f1f3f5;
  color: var(--text-secondary);
  font-size: 1.1rem;
  font-weight: 500;
}
</style>
