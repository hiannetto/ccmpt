<template>
  <div class="gallery-page">
    <div class="page-header">
      <div class="container">
        <h1>Acervo Fotográfico</h1>
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
      
      <div v-else class="galleries-container">
        <div v-for="gallery in galleries" :key="gallery.id" class="gallery-section">
          <div class="gallery-info">
            <h2>{{ gallery.title }}</h2>
            <p v-if="gallery.description">{{ gallery.description }}</p>
          </div>
          
          <div v-if="gallery.photos && gallery.photos.length > 0" class="photos-grid">
            <div v-for="photo in gallery.photos" :key="photo.id" class="photo-card" @click="openModal(photo)">
              <div class="img-wrapper">
                <img :src="`http://localhost:8000${photo.thumbnail_path || photo.image_path}`" :alt="photo.caption || gallery.title" loading="lazy" />
              </div>
              <div class="photo-caption" v-if="photo.caption">
                <p>{{ photo.caption }}</p>
              </div>
            </div>
          </div>
          <div v-else class="no-photos">
            Nenhuma foto nesta galeria.
          </div>
        </div>
      </div>
    </div>

    <!-- Lightbox Modal Simples -->
    <div v-if="selectedPhoto" class="lightbox-modal" @click="closeModal">
      <div class="modal-content" @click.stop>
        <button class="close-btn" @click="closeModal">&times;</button>
        <img :src="`http://localhost:8000${selectedPhoto.image_path}`" :alt="selectedPhoto.caption" />
        <p v-if="selectedPhoto.caption" class="lightbox-caption">{{ selectedPhoto.caption }}</p>
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
const selectedPhoto = ref(null);

onMounted(async () => {
  try {
    // Busca todas as galerias
    const res = await api.get('/galleries');
    const gals = res.data;
    
    // Para cada galeria, busca suas fotos
    // Em uma API ideal de listagem pública, o endpoint /galleries poderia retornar as fotos embarcadas,
    // mas de acordo com nossa modelagem, elas estão no endpoint /galleries/:id/photos
    for (let gal of gals) {
      try {
        const photosRes = await api.get(`/galleries/${gal.id}/photos`);
        // O endpoint de fotos retorna uma resposta paginada: { data: [...], total: X }
        gal.photos = photosRes.data.data;
      } catch (e) {
        gal.photos = [];
      }
    }
    
    galleries.value = gals;
  } catch (err) {
    error.value = 'Ocorreu um erro ao carregar o acervo fotográfico.';
    console.error(err);
  } finally {
    loading.value = false;
  }
});

const openModal = (photo) => {
  selectedPhoto.value = photo;
  document.body.style.overflow = 'hidden'; // Impede scroll
};

const closeModal = () => {
  selectedPhoto.value = null;
  document.body.style.overflow = '';
};
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
  color: white;
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

.gallery-section {
  margin-bottom: 4rem;
}

.gallery-info {
  margin-bottom: 1.5rem;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid #eee;
}

.gallery-info h2 {
  color: var(--color-primary, #0B1C3D);
  margin: 0 0 0.5rem;
  display: inline-block;
  position: relative;
}

.gallery-info h2::after {
  content: '';
  position: absolute;
  bottom: -9px;
  left: 0;
  width: 50%;
  height: 4px;
  background-color: var(--color-secondary, #D4AF37);
}

.gallery-info p {
  color: #666;
  margin: 0;
}

.photos-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 1.5rem;
}

.photo-card {
  background: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  cursor: pointer;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.photo-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 12px rgba(0,0,0,0.15);
}

.img-wrapper {
  position: relative;
  padding-top: 100%; /* Aspect ratio 1:1 (Quadrado) */
  overflow: hidden;
  background-color: #f0f0f0;
}

.img-wrapper img {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.photo-card:hover .img-wrapper img {
  transform: scale(1.05);
}

.photo-caption {
  padding: 0.75rem;
  font-size: 0.85rem;
  color: #555;
  text-align: center;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.photo-caption p {
  margin: 0;
}

.no-photos {
  color: #999;
  font-style: italic;
  padding: 1rem 0;
}

/* Modal / Lightbox */
.lightbox-modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background-color: rgba(11, 28, 61, 0.95); /* Navy blue com opacidade */
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  padding: 2rem;
}

.modal-content {
  position: relative;
  max-width: 90vw;
  max-height: 90vh;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.modal-content img {
  max-width: 100%;
  max-height: 80vh;
  object-fit: contain;
  box-shadow: 0 4px 20px rgba(0,0,0,0.5);
  border: 4px solid white;
}

.lightbox-caption {
  color: white;
  margin-top: 1rem;
  font-size: 1.1rem;
  text-align: center;
}

.close-btn {
  position: absolute;
  top: -40px;
  right: -40px;
  background: transparent;
  border: none;
  color: white;
  font-size: 3rem;
  cursor: pointer;
  line-height: 1;
  transition: color 0.2s;
}

.close-btn:hover {
  color: var(--color-secondary, #D4AF37);
}

@media (max-width: 768px) {
  .close-btn {
    top: -40px;
    right: 0;
  }
}
</style>
