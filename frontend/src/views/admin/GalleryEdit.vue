<template>
  <div class="form-container">
    <div class="header-actions">
      <h2>Gerenciar Galeria: {{ gallery.title }}</h2>
      <router-link :to="{ name: 'AdminGallery' }" class="btn-secondary">Voltar</router-link>
    </div>

    <!-- Edição da Galeria -->
    <div class="card form-section">
      <h3>Dados da Galeria</h3>
      <form @submit.prevent="updateGallery" class="form-row">
        <div class="form-group">
          <label>Título</label>
          <input type="text" v-model="gallery.title" required />
        </div>
        <div class="form-group">
          <label>Descrição</label>
          <input type="text" v-model="gallery.description" />
        </div>
        <div class="form-group actions-vertical">
          <button type="submit" class="btn-primary" :disabled="saving">Atualizar Dados</button>
        </div>
      </form>
    </div>

    <!-- Upload de Fotos -->
    <div class="card upload-section">
      <h3>Adicionar Fotos</h3>
      <div 
        class="drop-zone" 
        @dragover.prevent="dragover = true"
        @dragleave.prevent="dragover = false"
        @drop.prevent="handleDrop"
        :class="{ 'drag-over': dragover }"
      >
        <p v-if="!files.length">Arraste as imagens aqui ou clique para selecionar</p>
        <div v-else class="file-list">
          <span v-for="(file, index) in files" :key="index" class="file-badge">{{ file.name }}</span>
        </div>
        <input type="file" multiple accept="image/*" @change="handleFileSelect" ref="fileInput" class="hidden-input" :disabled="isUploading" />
      </div>
      <div class="actions" v-if="files.length > 0">
        <button @click="submitUpload" :disabled="isUploading" class="btn-primary">
          {{ isUploading ? `Enviando... ${uploadProgress}%` : 'Iniciar Upload' }}
        </button>
        <button @click="files = []" class="btn-secondary" :disabled="isUploading">Limpar</button>
      </div>
    </div>

    <!-- Lista de Fotos -->
    <div class="card gallery-section">
      <h3>Fotos da Galeria ({{ photos.length }})</h3>
      <div class="gallery-grid" v-if="photos.length > 0">
        <div class="gallery-item" v-for="photo in photos" :key="photo.id">
          <div class="img-wrapper">
            <img :src="photo.thumbnail_url" alt="" />
            <span v-if="photo.id === gallery.cover_photo_id" class="badge-cover">Capa</span>
          </div>
          <div class="gallery-item-actions">
            <textarea v-model="photo.tags" placeholder="Tags / Legenda..." rows="2"></textarea>
            <div class="btn-group">
              <button @click="updatePhoto(photo)" class="btn-small">Salvar Legenda</button>
              <button v-if="photo.id !== gallery.cover_photo_id" @click="setCover(photo.id)" class="btn-small">Capa</button>
              <button @click="deletePhoto(photo.id)" class="btn-small btn-danger">Excluir</button>
            </div>
          </div>
        </div>
      </div>
      <div v-else class="empty-state">
        Nenhuma foto nesta galeria ainda.
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import api from '../../api';

const route = useRoute();
const galleryId = route.params.id;

const gallery = ref({ title: '', description: '', cover_photo_id: null });
const photos = ref([]);
const saving = ref(false);

const dragover = ref(false);
const files = ref([]);
const fileInput = ref(null);
const isUploading = ref(false);
const uploadProgress = ref(0);

const loadGallery = async () => {
  try {
    const res = await api.get(`/galleries/${galleryId}`);
    gallery.value = res.data;
    loadPhotos();
  } catch (e) {
    alert('Erro ao carregar galeria.');
  }
};

const loadPhotos = async () => {
  try {
    const res = await api.get(`/galleries/${galleryId}/photos`);
    photos.value = res.data.data;
  } catch (e) {
    console.error('Erro ao carregar fotos', e);
  }
};

const updateGallery = async () => {
  saving.value = true;
  try {
    await api.put(`/galleries/${galleryId}`, {
      title: gallery.value.title,
      description: gallery.value.description,
      cover_photo_id: gallery.value.cover_photo_id
    });
    alert('Galeria atualizada com sucesso.');
  } catch (e) {
    alert('Erro ao atualizar galeria.');
  } finally {
    saving.value = false;
  }
};

const setCover = async (photoId) => {
  try {
    await api.put(`/galleries/${galleryId}`, {
      title: gallery.value.title,
      description: gallery.value.description,
      cover_photo_id: photoId
    });
    gallery.value.cover_photo_id = photoId;
  } catch (e) {
    alert('Erro ao definir capa.');
  }
};

const updatePhoto = async (photo) => {
  try {
    await api.put(`/photos/${photo.id}`, { tags: photo.tags });
    alert('Legenda salva com sucesso!');
  } catch (e) {
    alert('Erro ao salvar legenda.');
  }
};

const deletePhoto = async (id) => {
  if (confirm('Tem certeza que deseja excluir esta foto?')) {
    try {
      await api.delete(`/photos/${id}`);
      photos.value = photos.value.filter(p => p.id !== id);
    } catch (e) {
      alert('Erro ao excluir foto.');
    }
  }
};

// Upload
const handleFileSelect = (event) => {
  files.value = [...files.value, ...Array.from(event.target.files)];
};
const handleDrop = (event) => {
  dragover.value = false;
  files.value = [...files.value, ...Array.from(event.dataTransfer.files).filter(f => f.type.startsWith('image/'))];
};

const submitUpload = async () => {
  isUploading.value = true;
  uploadProgress.value = 0;
  const formData = new FormData();
  files.value.forEach(f => formData.append('photos[]', f));

  try {
    await api.post(`/galleries/${galleryId}/photos`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
      onUploadProgress: (ev) => {
        uploadProgress.value = Math.round((ev.loaded * 100) / ev.total);
      }
    });
    files.value = [];
    loadPhotos(); // Refresh list
  } catch (e) {
    alert('Erro durante o upload.');
  } finally {
    isUploading.value = false;
  }
};

onMounted(() => loadGallery());
</script>

<style scoped>
.form-container { max-width: 1000px; margin: 0 auto; padding-bottom: 3rem; }
.header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
h2 { margin: 0; color: var(--color-primary, #0B1C3D); }
.card { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 2rem; }
h3 { margin-top: 0; margin-bottom: 1.5rem; color: #444; border-bottom: 1px solid #eee; padding-bottom: 0.5rem; }

.form-row { display: flex; gap: 1rem; align-items: flex-end; }
.form-group { flex: 1; display: flex; flex-direction: column; gap: 0.5rem; }
.form-group label { font-weight: 500; }
.form-group input { padding: 0.75rem; border: 1px solid #ced4da; border-radius: 4px; font-family: inherit; }
.actions-vertical { flex: 0 0 auto; justify-content: flex-end; }

.btn-primary { background: var(--color-primary, #0B1C3D); color: white; padding: 0.75rem 1.5rem; border-radius: 4px; font-weight: 500; border: none; cursor: pointer; }
.btn-primary:disabled { opacity: 0.7; cursor: not-allowed; }
.btn-secondary { background: #6c757d; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; }

/* Dropzone */
.drop-zone { border: 2px dashed #adb5bd; border-radius: 8px; padding: 3rem 2rem; text-align: center; cursor: pointer; transition: all 0.3s ease; position: relative; }
.drop-zone.drag-over { border-color: var(--color-secondary, #D4AF37); background: #fdfaf2; }
.hidden-input { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; }
.file-list { display: flex; flex-wrap: wrap; gap: 0.5rem; justify-content: center; }
.file-badge { background: #e9ecef; padding: 0.25rem 0.75rem; border-radius: 16px; font-size: 0.85rem; }
.actions { display: flex; gap: 1rem; margin-top: 1rem; justify-content: flex-end; }

/* Galeria */
.gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1.5rem; }
.gallery-item { border: 1px solid #eee; border-radius: 4px; overflow: hidden; background: #fafafa; display: flex; flex-direction: column; }
.img-wrapper { position: relative; height: 150px; background: #000; }
.img-wrapper img { width: 100%; height: 100%; object-fit: cover; }
.badge-cover { position: absolute; top: 0.5rem; left: 0.5rem; background: var(--color-secondary, #D4AF37); color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: bold; }
.gallery-item-actions { padding: 0.75rem; display: flex; flex-direction: column; gap: 0.5rem; flex: 1; justify-content: space-between; }
.gallery-item-actions textarea { width: 100%; padding: 0.5rem; font-size: 0.85rem; border: 1px solid #ccc; border-radius: 4px; resize: none; font-family: inherit; }
.btn-group { display: flex; gap: 0.25rem; flex-wrap: wrap; justify-content: space-between; }
.btn-small { background: #e9ecef; border: 1px solid #ced4da; padding: 0.35rem 0.5rem; border-radius: 4px; font-size: 0.75rem; cursor: pointer; flex: 1; }
.btn-danger { background: #dc3545; color: white; border-color: #dc3545; }
.empty-state { text-align: center; padding: 2rem; color: #666; }
</style>
