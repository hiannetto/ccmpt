<template>
  <div v-if="loading" class="loading-state">
    <div class="container">Carregando detalhes do item...</div>
  </div>
  
  <div v-else-if="error" class="error-state">
    <div class="container">{{ error }}</div>
  </div>
  
  <div v-else-if="item" class="item-page">
    <div class="container">
      <div class="breadcrumb">
        <router-link to="/memorial">&laquo; Voltar para o Acervo Histórico</router-link>
      </div>

      <div class="item-header">
        <h1>{{ item.title }}</h1>
        <div class="categories" v-if="item.categories && item.categories.length">
          <span v-for="cat in item.categories" :key="cat.id" class="badge">
            {{ cat.name }}
          </span>
        </div>
      </div>

      <div class="item-layout">
        <!-- Coluna da Foto Principal + Ficha Técnica -->
        <div class="sidebar">
          <div class="main-image-container" v-if="item.main_image_url">
            <img :src="item.main_image_url" :alt="item.title" @click="openModal(item.main_image_url, item.main_image_caption)" />
            <p v-if="item.main_image_caption" class="caption">{{ item.main_image_caption }}</p>
          </div>

          <div class="tech-sheet">
            <h3>Ficha Técnica</h3>
            <ul class="tech-list">
              <li v-if="item.inventory_number">
                <strong>Nº Inventário:</strong> <span>{{ item.inventory_number }}</span>
              </li>
              <li v-if="item.dating_label || item.year">
                <strong>Datação:</strong> <span>{{ item.dating_label || item.year }}</span>
              </li>
              <li v-if="item.material">
                <strong>Material/Técnica:</strong> <span>{{ item.material }}</span>
              </li>
              <li v-if="item.dimensions">
                <strong>Dimensões:</strong> <span>{{ item.dimensions }}</span>
              </li>
              <li v-if="item.provenance">
                <strong>Proveniência:</strong> <span>{{ item.provenance }}</span>
              </li>
              <li v-if="item.conservation_state">
                <strong>Estado de Conservação:</strong> <span class="capitalize">{{ item.conservation_state }}</span>
              </li>
            </ul>
          </div>
        </div>

        <!-- Coluna do Conteúdo Principal -->
        <div class="main-content">
          <div class="historical-description content-formatted" v-html="item.historical_description"></div>
          
          <!-- Galeria de Imagens Adicionais -->
          <div v-if="item.images && item.images.length > 0" class="additional-images">
            <h3>Galeria de Imagens</h3>
            <div class="images-grid">
              <div v-for="img in item.images" :key="img.id" class="grid-img-wrapper" @click="openModal(img.image_url, img.caption)">
                <img :src="img.thumbnail_url" :alt="img.caption || 'Foto adicional'" loading="lazy" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Lightbox -->
    <div v-if="modalImage" class="lightbox-modal" @click="closeModal">
      <div class="modal-content" @click.stop>
        <button class="close-btn" @click="closeModal">&times;</button>
        <img :src="modalImage" :alt="modalCaption" />
        <p v-if="modalCaption" class="lightbox-caption">{{ modalCaption }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import api from '../../api';

const route = useRoute();
const item = ref(null);
const loading = ref(true);
const error = ref('');

const modalImage = ref(null);
const modalCaption = ref('');

onMounted(async () => {
  try {
    const res = await api.get(`/memorial/${route.params.id}`);
    item.value = res.data;
  } catch (err) {
    error.value = 'Item não encontrado ou indisponível.';
    console.error(err);
  } finally {
    loading.value = false;
  }
});

const openModal = (url, caption) => {
  modalImage.value = url;
  modalCaption.value = caption;
  document.body.style.overflow = 'hidden';
};

const closeModal = () => {
  modalImage.value = null;
  modalCaption.value = '';
  document.body.style.overflow = '';
};
</script>

<style scoped>
.item-page {
  padding: 3rem 0;
}

.breadcrumb {
  margin-bottom: 2rem;
}
.breadcrumb a {
  color: var(--color-secondary, #D4AF37);
  text-decoration: none;
  font-weight: 500;
}
.breadcrumb a:hover {
  text-decoration: underline;
}

.item-header {
  margin-bottom: 3rem;
  border-bottom: 2px solid #f1f3f5;
  padding-bottom: 1.5rem;
}

.item-header h1 {
  font-size: 2.5rem;
  color: var(--color-primary, #0B1C3D);
  margin: 0 0 1rem;
}

.badge {
  display: inline-block;
  background: #e9ecef;
  color: #495057;
  padding: 0.4rem 0.8rem;
  border-radius: 20px;
  font-size: 0.85rem;
  margin-right: 0.5rem;
  margin-bottom: 0.5rem;
}

.item-layout {
  display: flex;
  gap: 3rem;
  align-items: flex-start;
}

.sidebar {
  flex: 0 0 350px;
}

.main-content {
  flex: 1;
  min-width: 0; /* Previne overflow flex */
}

/* Sidebar / Imagem Principal */
.main-image-container {
  margin-bottom: 2rem;
  background: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.main-image-container img {
  width: 100%;
  height: auto;
  display: block;
  cursor: zoom-in;
  transition: transform 0.3s;
}

.main-image-container img:hover {
  transform: scale(1.02);
}

.caption {
  padding: 1rem;
  margin: 0;
  font-size: 0.9rem;
  color: #6c757d;
  background: #f8f9fa;
  text-align: center;
  border-top: 1px solid #eee;
}

/* Ficha Técnica */
.tech-sheet {
  background: #f8f9fa;
  padding: 1.5rem;
  border-radius: 8px;
  border-left: 4px solid var(--color-secondary, #D4AF37);
}

.tech-sheet h3 {
  margin-top: 0;
  color: var(--color-primary, #0B1C3D);
  font-size: 1.2rem;
  margin-bottom: 1rem;
}

.tech-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.tech-list li {
  padding: 0.75rem 0;
  border-bottom: 1px solid #e9ecef;
  display: flex;
  flex-direction: column;
}
.tech-list li:last-child {
  border-bottom: none;
}

.tech-list strong {
  color: #495057;
  font-size: 0.85rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 0.25rem;
}

.tech-list span {
  color: #212529;
}
.capitalize {
  text-transform: capitalize;
}

/* Descrição Histórica */
.historical-description {
  font-size: 1.1rem;
  line-height: 1.8;
  color: #333;
  margin-bottom: 4rem;
}

/* Galeria Adicional */
.additional-images h3 {
  color: var(--color-primary, #0B1C3D);
  margin-bottom: 1.5rem;
  font-size: 1.5rem;
}

.images-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 1rem;
}

.grid-img-wrapper {
  aspect-ratio: 1;
  border-radius: 8px;
  overflow: hidden;
  cursor: zoom-in;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.grid-img-wrapper img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s;
}

.grid-img-wrapper:hover img {
  transform: scale(1.1);
}

/* Modal Lightbox */
.lightbox-modal {
  position: fixed;
  top: 0; left: 0; width: 100vw; height: 100vh;
  background-color: rgba(11, 28, 61, 0.95);
  display: flex; justify-content: center; align-items: center;
  z-index: 9999;
  padding: 2rem;
}

.modal-content {
  position: relative;
  max-width: 90vw;
  max-height: 90vh;
  display: flex; flex-direction: column; align-items: center;
}

.modal-content img {
  max-width: 100%;
  max-height: 80vh;
  object-fit: contain;
  box-shadow: 0 4px 20px rgba(0,0,0,0.5);
  border: 4px solid white;
}

.lightbox-caption {
  color: white; margin-top: 1rem; font-size: 1.1rem; text-align: center;
}

.close-btn {
  position: absolute; top: -40px; right: -40px; background: transparent;
  border: none; color: white; font-size: 3rem; cursor: pointer;
  line-height: 1; transition: color 0.2s;
}
.close-btn:hover { color: var(--color-secondary, #D4AF37); }

@media (max-width: 992px) {
  .item-layout { flex-direction: column; }
  .sidebar { flex: none; width: 100%; }
}

.loading-state, .error-state {
  text-align: center; padding: 4rem 0; font-size: 1.2rem; color: #6c757d;
}
</style>
