<template>
  <div class="gallery-container">
    <div class="gallery-header animate-fade-up">
      <div class="title-wrap">
        <h2 class="section-title">Galeria Histórica</h2>
        <div class="title-decorator"></div>
      </div>
      <div class="gallery-filters">
        <button class="filter-btn active">Todos</button>
        <button class="filter-btn">Missas</button>
        <button class="filter-btn">Eventos Oficiais</button>
        <button class="filter-btn">Obras</button>
      </div>
    </div>
    
    <div class="masonry-grid">
      <!-- Premium Thumbnail Cards -->
      <div 
        v-for="(photo, index) in photos" 
        :key="photo.id" 
        class="masonry-item"
        :style="{ animationDelay: `${(index % 10) * 0.1}s` }"
        @click="openLightbox(photo)"
      >
        <div class="image-wrapper">
          <img 
            :src="photo.thumbnail_path" 
            alt="Foto do arquivo" 
            loading="lazy"
            class="thumbnail"
          />
          <div class="glass-overlay">
            <div class="overlay-content">
              <PhMagnifyingGlassPlus :size="38" weight="fill" color="#D4AF37" />
              <span class="overlay-text">Ampliar Imagem</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Sentinel for Infinite Scroll -->
    <div ref="sentinel" class="scroll-sentinel">
      <div v-if="isLoading" class="loading-state">
        <PhSpinner :size="42" weight="bold" class="spinner" />
        <p>Buscando memórias...</p>
      </div>
      <div v-else-if="noMorePhotos" class="end-state">
        <div class="end-decorator"></div>
        <p>Você chegou ao fim deste álbum.</p>
      </div>
    </div>

    <!-- Premium Lightbox -->
    <Transition name="fade">
      <div v-if="lightboxOpen" class="lightbox-wrapper" @click.self="closeLightbox">
        <div class="lightbox-backdrop"></div>
        
        <button class="lightbox-close" @click="closeLightbox">
          <PhX :size="28" weight="bold" />
        </button>
        
        <div class="lightbox-content">
          <img :src="currentPhoto.image_path" alt="Alta Resolução" class="lightbox-img" />
          <div class="lightbox-caption glass-caption">
            <h3>Registro Histórico</h3>
            <p>Arquivo fotográfico - Centro Cultural e Memorial Padre Tiago</p>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { PhMagnifyingGlassPlus, PhX, PhSpinner } from '@phosphor-icons/vue';

const photos = ref([]);
const page = ref(1);
const isLoading = ref(false);
const noMorePhotos = ref(false);

const sentinel = ref(null);
let observer = null;

const lightboxOpen = ref(false);
const currentPhoto = ref(null);

// Simulating beautiful varied height images for masonry effect
const fetchPhotos = async () => {
  if (isLoading.value || noMorePhotos.value) return;
  isLoading.value = true;
  
  setTimeout(() => {
    const data = Array.from({ length: 12 }).map((_, i) => {
      // Simulate varied dimensions for masonry aesthetic
      const height = Math.floor(Math.random() * (600 - 300 + 1) + 300);
      return {
        id: `img-${page.value}-${i}`,
        thumbnail_path: `https://picsum.photos/seed/${page.value}${i}/400/${height}`,
        image_path: `https://picsum.photos/seed/${page.value}${i}/1920/1080`
      };
    });

    if (page.value > 3) {
      noMorePhotos.value = true;
    } else {
      photos.value = [...photos.value, ...data];
      page.value++;
    }
    isLoading.value = false;
  }, 800); // Artificial delay to show beautiful spinner
};

onMounted(() => {
  observer = new IntersectionObserver((entries) => {
    if (entries[0].isIntersecting) fetchPhotos();
  }, { rootMargin: '400px' });

  if (sentinel.value) observer.observe(sentinel.value);
});

onUnmounted(() => {
  if (observer && sentinel.value) observer.unobserve(sentinel.value);
});

const openLightbox = (photo) => {
  currentPhoto.value = photo;
  lightboxOpen.value = true;
  document.body.style.overflow = 'hidden';
};

const closeLightbox = () => {
  lightboxOpen.value = false;
  currentPhoto.value = null;
  document.body.style.overflow = 'auto';
};
</script>

<style scoped>
.gallery-container {
  padding: 2rem 0;
}

.gallery-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  margin-bottom: 4rem;
  border-bottom: 1px solid var(--c-border);
  padding-bottom: 1.5rem;
}

.title-wrap {
  position: relative;
}

.section-title {
  font-size: 3rem;
  color: var(--c-primary);
  margin: 0;
  font-weight: 700;
}

.title-decorator {
  position: absolute;
  bottom: -1.5rem;
  left: 0;
  width: 80px;
  height: 4px;
  background: var(--c-secondary);
}

.gallery-filters {
  display: flex;
  gap: 1rem;
}

.filter-btn {
  background: transparent;
  border: 1px solid var(--c-border);
  padding: 0.5rem 1.5rem;
  border-radius: 50px;
  font-family: var(--font-sans);
  font-weight: 600;
  color: var(--c-text-muted);
  cursor: pointer;
  transition: all 0.3s ease;
}

.filter-btn:hover, .filter-btn.active {
  background: var(--c-primary);
  border-color: var(--c-primary);
  color: #fff;
}

/* Elegant CSS Masonry Grid */
.masonry-grid {
  column-count: 3;
  column-gap: 2rem;
}

@media (max-width: 1024px) { .masonry-grid { column-count: 2; } }
@media (max-width: 640px) { .masonry-grid { column-count: 1; } }

.masonry-item {
  break-inside: avoid;
  margin-bottom: 2rem;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: var(--shadow-sm);
  background: var(--c-surface);
  transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.4s ease;
  cursor: pointer;
  animation: fadeInUp 0.8s ease both;
}

.masonry-item:hover {
  transform: translateY(-8px);
  box-shadow: var(--shadow-lg);
}

.image-wrapper {
  position: relative;
  width: 100%;
  height: 100%;
}

.thumbnail {
  width: 100%;
  display: block;
  object-fit: cover;
  transition: filter 0.5s ease;
}

.masonry-item:hover .thumbnail {
  filter: brightness(0.85) saturate(1.2);
}

/* Glassmorphism Hover Overlay */
.glass-overlay {
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background: linear-gradient(to top, rgba(10, 25, 48, 0.8) 0%, rgba(10, 25, 48, 0.2) 100%);
  opacity: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: opacity 0.4s ease;
}

.masonry-item:hover .glass-overlay {
  opacity: 1;
}

.overlay-content {
  text-align: center;
  transform: translateY(20px);
  transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
}

.masonry-item:hover .overlay-content {
  transform: translateY(0);
}

.overlay-text {
  display: block;
  color: #fff;
  font-family: var(--font-sans);
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 2px;
  font-size: 0.8rem;
  margin-top: 1rem;
}

/* Loading & Sentinel */
.scroll-sentinel {
  padding: 4rem 0;
  text-align: center;
}

.loading-state {
  color: var(--c-secondary);
}

.spinner {
  animation: spin 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
  margin-bottom: 1rem;
}

.end-state {
  color: var(--c-text-muted);
  font-family: var(--font-serif);
  font-style: italic;
  font-size: 1.2rem;
}

.end-decorator {
  width: 40px;
  height: 2px;
  background: var(--c-secondary);
  margin: 0 auto 1rem;
}

@keyframes spin { 100% { transform: rotate(360deg); } }

/* Premium Lightbox */
.lightbox-wrapper {
  position: fixed;
  top: 0; left: 0; width: 100vw; height: 100vh;
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
}

.lightbox-backdrop {
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(10, 25, 48, 0.95);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
}

.lightbox-close {
  position: absolute;
  top: 2rem;
  right: 3rem;
  background: rgba(255,255,255,0.1);
  border: 1px solid rgba(255,255,255,0.2);
  color: #fff;
  width: 50px;
  height: 50px;
  border-radius: 50%;
  cursor: pointer;
  z-index: 10000;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.lightbox-close:hover {
  background: var(--c-secondary);
  color: var(--c-primary);
  transform: rotate(90deg);
}

.lightbox-content {
  position: relative;
  z-index: 10000;
  max-width: 90vw;
  max-height: 90vh;
  box-shadow: 0 30px 60px rgba(0,0,0,0.5);
  border-radius: 4px;
  overflow: hidden;
}

.lightbox-img {
  display: block;
  max-width: 100%;
  max-height: 85vh;
  object-fit: contain;
}

.glass-caption {
  position: absolute;
  bottom: 0; left: 0; right: 0;
  background: linear-gradient(to top, rgba(10, 25, 48, 0.95), rgba(10, 25, 48, 0.7));
  backdrop-filter: blur(10px);
  padding: 1.5rem 2rem;
  color: #fff;
  border-top: 1px solid rgba(255,255,255,0.1);
}

.glass-caption h3 {
  color: var(--c-secondary);
  font-size: 1.5rem;
  margin-bottom: 0.5rem;
}

.glass-caption p {
  font-size: 0.9rem;
  opacity: 0.8;
}

/* Transitions */
.fade-enter-active, .fade-leave-active { transition: opacity 0.4s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
