<template>
  <div class="gallery-detail-page">
    <div v-if="loading" class="loading-state">
      <div class="container">Carregando galeria...</div>
    </div>
    
    <div v-else-if="error" class="error-state">
      <div class="container">{{ error }}</div>
    </div>
    
    <div v-else-if="gallery" class="gallery-content">
      <div class="page-header">
        <div class="container">
          <div class="breadcrumb">
            <router-link to="/galeria">&laquo; Voltar para as Galerias</router-link>
          </div>
          <h1>{{ gallery.title }}</h1>
          <p v-if="gallery.description">{{ gallery.description }}</p>
        </div>
      </div>

      <div class="container photos-container">
        <div v-if="photos.length > 0" class="photos-grid">
          <div v-for="(photo, index) in photos" :key="photo.id" class="photo-card" @click="openModal(index)">
            <div class="img-wrapper">
              <img :src="photo.thumbnail_url" :alt="photo.tags || gallery.title" loading="lazy" />
            </div>
            <div class="photo-caption" v-if="photo.tags">
              <p>{{ photo.tags }}</p>
            </div>
          </div>
        </div>
        <div v-else class="no-photos">
          Nenhuma foto adicionada nesta galeria ainda.
        </div>
      </div>
    </div>

    <!-- Lightbox Modal Completo -->
    <div v-if="selectedPhoto" class="lightbox-modal" @click="closeModal">
      
      <!-- Controles de Navegação -->
      <button v-if="selectedIndex > 0" class="nav-btn prev-btn" @click.stop="prevPhoto">&lsaquo;</button>
      <button v-if="selectedIndex < photos.length - 1" class="nav-btn next-btn" @click.stop="nextPhoto">&rsaquo;</button>

      <!-- Botão Fechar -->
      <button class="close-btn" @click="closeModal">&times;</button>

      <!-- Imagem Central -->
      <div class="modal-content" @click.stop>
        <img :src="selectedPhoto.image_url" :alt="selectedPhoto.tags" />
        <p v-if="selectedPhoto.tags" class="lightbox-caption">{{ selectedPhoto.tags }}</p>
      </div>

      <!-- Tira de Miniaturas (Thumbnail Strip) -->
      <div class="thumbnail-strip" @click.stop>
        <div class="strip-container">
          <div 
            v-for="(photo, index) in photos" 
            :key="'thumb-'+photo.id" 
            class="thumb-wrapper" 
            :class="{ active: index === selectedIndex }"
            @click="selectedIndex = index"
          >
            <img :src="photo.thumbnail_url" alt="" loading="lazy" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRoute } from 'vue-router';
import api from '../../api';

const route = useRoute();
const gallery = ref(null);
const photos = ref([]);
const loading = ref(true);
const error = ref('');

const selectedIndex = ref(null);
const selectedPhoto = computed(() => selectedIndex.value !== null ? photos.value[selectedIndex.value] : null);

onMounted(async () => {
  try {
    const galRes = await api.get(`/galleries/${route.params.id}`);
    gallery.value = galRes.data;
    
    const photosRes = await api.get(`/galleries/${route.params.id}/photos`);
    photos.value = photosRes.data.data;
  } catch (err) {
    error.value = 'Galeria não encontrada ou indisponível.';
    console.error(err);
  } finally {
    loading.value = false;
  }
  
  window.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeydown);
});

const handleKeydown = (e) => {
  if (selectedIndex.value === null) return;
  if (e.key === 'Escape') closeModal();
  if (e.key === 'ArrowRight') nextPhoto();
  if (e.key === 'ArrowLeft') prevPhoto();
};

const openModal = (index) => {
  selectedIndex.value = index;
  document.body.style.overflow = 'hidden';
};

const closeModal = () => {
  selectedIndex.value = null;
  document.body.style.overflow = '';
};

const prevPhoto = () => {
  if (selectedIndex.value > 0) {
    selectedIndex.value--;
    scrollThumbIntoView();
  }
};

const nextPhoto = () => {
  if (selectedIndex.value < photos.value.length - 1) {
    selectedIndex.value++;
    scrollThumbIntoView();
  }
};

const scrollThumbIntoView = () => {
  setTimeout(() => {
    const activeThumb = document.querySelector('.thumb-wrapper.active');
    if (activeThumb) {
      activeThumb.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
    }
  }, 50);
};
</script>

<style scoped>
.page-header {
  background-color: var(--color-primary, #0B1C3D);
  color: white;
  padding: 3rem 0;
  margin-bottom: 3rem;
  border-bottom: 4px solid var(--color-secondary, #D4AF37);
  text-align: center;
}

.breadcrumb {
  margin-bottom: 1.5rem;
}
.breadcrumb a {
  color: #adb5bd;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.2s;
}
.breadcrumb a:hover {
  color: white;
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

.photos-container {
  padding-bottom: 4rem;
}

.photos-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 1.5rem;
}

.photo-card {
  background: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  cursor: zoom-in;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  display: flex;
  flex-direction: column;
}

.photo-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 12px rgba(0,0,0,0.15);
}

.img-wrapper {
  position: relative;
  aspect-ratio: 1; /* Quadrado perfeito */
  overflow: hidden;
  background-color: #f0f0f0;
}

.img-wrapper img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.photo-card:hover .img-wrapper img {
  transform: scale(1.08);
}

.photo-caption {
  padding: 0.75rem 1rem;
  background: white;
  border-top: 1px solid #f1f3f5;
}

.photo-caption p {
  margin: 0;
  font-size: 0.9rem;
  color: #495057;
  text-align: center;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.no-photos {
  text-align: center;
  color: #999;
  font-style: italic;
  padding: 3rem 0;
}

/* Lightbox Modal */
.lightbox-modal {
  position: fixed;
  top: 0; left: 0; width: 100vw; height: 100vh;
  background-color: rgba(0, 0, 0, 0.95);
  display: flex; flex-direction: column; justify-content: center; align-items: center;
  z-index: 1000;
  padding: 2rem 2rem 7rem 2rem; /* espaço extra embaixo para o thumb strip */
}

.modal-content {
  position: relative;
  max-width: 90vw;
  max-height: calc(100vh - 150px);
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  flex: 1;
}

.modal-content img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
  box-shadow: 0 4px 20px rgba(0,0,0,0.5);
  border: 2px solid #333;
}

.lightbox-caption {
  color: white; margin-top: 1rem; font-size: 1.1rem; text-align: center;
  max-width: 80%;
}

.close-btn {
  position: absolute; top: 1.5rem; right: 2rem; background: transparent;
  border: none; color: white; font-size: 3rem; cursor: pointer;
  line-height: 1; transition: color 0.2s; z-index: 1010;
}
.close-btn:hover { color: var(--color-secondary, #D4AF37); }

/* Setas de navegação */
.nav-btn {
  position: absolute; top: 50%; transform: translateY(-50%);
  background: rgba(255,255,255,0.1); border: none; color: white;
  font-size: 4rem; padding: 1rem; cursor: pointer; transition: all 0.2s;
  border-radius: 50%; width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;
  z-index: 1005;
}
.nav-btn:hover {
  background: rgba(255,255,255,0.3); color: var(--color-secondary, #D4AF37);
}
.prev-btn { left: 2rem; }
.next-btn { right: 2rem; }

/* Tira de miniaturas */
.thumbnail-strip {
  position: absolute; bottom: 0; left: 0; width: 100%; height: 90px;
  background: rgba(0,0,0,0.8);
  display: flex; align-items: center; justify-content: center;
}

.strip-container {
  display: flex; gap: 0.5rem; overflow-x: auto; padding: 0.5rem 1rem; max-width: 100%;
  scrollbar-width: thin; scrollbar-color: #555 transparent;
}
.strip-container::-webkit-scrollbar { height: 6px; }
.strip-container::-webkit-scrollbar-thumb { background: #555; border-radius: 4px; }

.thumb-wrapper {
  flex: 0 0 60px; height: 60px; cursor: pointer; border-radius: 4px; overflow: hidden;
  opacity: 0.5; transition: all 0.2s; border: 2px solid transparent;
}
.thumb-wrapper:hover { opacity: 0.8; }
.thumb-wrapper.active {
  opacity: 1; border-color: var(--color-secondary, #D4AF37); transform: scale(1.1);
}

.thumb-wrapper img {
  width: 100%; height: 100%; object-fit: cover;
}

@media (max-width: 768px) {
  .nav-btn { font-size: 2.5rem; width: 50px; height: 50px; }
  .prev-btn { left: 0.5rem; }
  .next-btn { right: 0.5rem; }
  .close-btn { top: 0.5rem; right: 1rem; }
  .lightbox-modal { padding: 1rem 0 6rem 0; }
  .thumbnail-strip { height: 80px; }
  .thumb-wrapper { flex: 0 0 50px; height: 50px; }
}

.loading-state, .error-state {
  text-align: center; padding: 4rem 0; font-size: 1.1rem; color: #666;
}
</style>
