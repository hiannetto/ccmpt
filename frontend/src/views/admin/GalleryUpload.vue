<template>
  <div class="gallery-upload-container">
    <h2>Upload de Fotos do Acervo (Lote)</h2>

    <div class="form-section">
      <h3>1. Selecione ou Crie uma Galeria</h3>
      
      <div class="gallery-selection">
        <label>
          <input type="radio" v-model="galleryMode" value="existing" /> Galeria Existente
        </label>
        <label>
          <input type="radio" v-model="galleryMode" value="new" /> Nova Galeria
        </label>
      </div>

      <div v-if="galleryMode === 'existing'" class="input-group">
        <label>Galeria:</label>
        <select v-model="selectedGalleryId" :disabled="isUploading">
          <option value="" disabled>Selecione...</option>
          <option v-for="gal in existingGalleries" :key="gal.id" :value="gal.id">
            {{ gal.title }}
          </option>
        </select>
      </div>

      <div v-if="galleryMode === 'new'" class="input-group">
        <label>Nome da Nova Galeria:</label>
        <input 
          type="text" 
          v-model="newGalleryName" 
          placeholder="Ex: Inauguração do Centro - 2010" 
          :disabled="isUploading"
        />
      </div>
    </div>

    <div class="form-section">
      <h3>2. Selecione as Fotos</h3>
      
      <div 
        class="drop-zone" 
        @dragover.prevent="dragover = true"
        @dragleave.prevent="dragover = false"
        @drop.prevent="handleDrop"
        :class="{ 'drag-over': dragover }"
      >
        <p v-if="!files.length">Arraste as imagens aqui ou clique para selecionar</p>
        <div v-else class="file-list">
          <span v-for="(file, index) in files" :key="index" class="file-badge">
            {{ file.name }}
          </span>
        </div>
        <input 
          type="file" 
          multiple 
          accept="image/*" 
          @change="handleFileSelect" 
          ref="fileInput" 
          class="hidden-input"
          :disabled="isUploading"
        />
      </div>
    </div>

    <div class="actions">
      <button 
        @click="submitUpload" 
        :disabled="isUploading || files.length === 0 || (!selectedGalleryId && !newGalleryName)"
        class="btn-primary"
      >
        {{ isUploading ? 'Enviando...' : 'Iniciar Upload em Lote' }}
      </button>
    </div>

    <div v-if="isUploading" class="progress-section">
      <p>Processando imagens no servidor... {{ uploadProgress }}%</p>
      <div class="progress-bar">
        <div class="progress-fill" :style="{ width: uploadProgress + '%' }"></div>
      </div>
    </div>

    <div v-if="successMessage" class="alert success">{{ successMessage }}</div>
    <div v-if="errorMessage" class="alert error">{{ errorMessage }}</div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../api';

// Estado do Componente
const galleryMode = ref('existing');
const existingGalleries = ref([]);
const selectedGalleryId = ref('');
const newGalleryName = ref('');

const files = ref([]);
const fileInput = ref(null);
const dragover = ref(false);

const isUploading = ref(false);
const uploadProgress = ref(0);
const successMessage = ref('');
const errorMessage = ref('');

// Simulando carregamento de galerias existentes da API
onMounted(async () => {
  try {
    const response = await api.get('/galleries');
    existingGalleries.value = response.data;
  } catch (error) {
    console.error('Erro ao carregar galerias', error);
  }
});

const handleFileSelect = (event) => {
  const selectedFiles = Array.from(event.target.files);
  files.value = [...files.value, ...selectedFiles];
};

const handleDrop = (event) => {
  dragover.value = false;
  const droppedFiles = Array.from(event.dataTransfer.files).filter(f => f.type.startsWith('image/'));
  files.value = [...files.value, ...droppedFiles];
};

const submitUpload = async () => {
  successMessage.value = '';
  errorMessage.value = '';
  isUploading.value = true;
  uploadProgress.value = 0;

  const formData = new FormData();
  
  try {
    let targetGalleryId = selectedGalleryId.value;

    if (galleryMode.value === 'new') {
      const galRes = await api.post('/galleries', { title: newGalleryName.value, description: '' });
      targetGalleryId = galRes.data.id;
    }

    files.value.forEach((file) => {
      formData.append('photos[]', file);
    });

    const response = await api.post(`/galleries/${targetGalleryId}/photos`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
      onUploadProgress: (progressEvent) => {
        const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
        uploadProgress.value = percentCompleted;
      }
    });

    successMessage.value = `${response.data.success_count} imagens processadas e salvas com sucesso!`;
    files.value = []; // Clear form
    if (galleryMode.value === 'new') {
        galleryMode.value = 'existing';
        const res = await api.get('/galleries');
        existingGalleries.value = res.data;
        selectedGalleryId.value = targetGalleryId;
    }
  } catch (error) {
    errorMessage.value = error.response?.data?.error || 'Ocorreu um erro durante o upload.';
  } finally {
    isUploading.value = false;
  }
};
</script>

<style scoped>
.gallery-upload-container {
  max-width: 800px;
  background: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  color: #333;
}

h2 {
  font-size: 1.5rem;
  margin-bottom: 1.5rem;
  color: var(--color-primary, #0B1C3D);
}

.form-section {
  background: #f8f9fa;
  padding: 1.5rem;
  border-radius: 8px;
  margin-bottom: 1.5rem;
  border: 1px solid #e9ecef;
}

h3 {
  font-size: 1.1rem;
  margin-bottom: 1rem;
  color: #495057;
}

.gallery-selection {
  margin-bottom: 1rem;
}

.gallery-selection label {
  margin-right: 1.5rem;
  font-weight: 500;
  cursor: pointer;
}

.input-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

input[type="text"], select {
  padding: 0.75rem;
  border: 1px solid #ced4da;
  border-radius: 4px;
  font-size: 1rem;
}

.drop-zone {
  border: 2px dashed #adb5bd;
  border-radius: 8px;
  padding: 3rem 2rem;
  text-align: center;
  background: #fff;
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
}

.drop-zone.drag-over {
  border-color: var(--color-secondary, #D4AF37);
  background: #fdfaf2;
}

.hidden-input {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  opacity: 0;
  cursor: pointer;
}

.file-list {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  justify-content: center;
}

.file-badge {
  background: #e9ecef;
  padding: 0.25rem 0.75rem;
  border-radius: 16px;
  font-size: 0.85rem;
}

.actions {
  display: flex;
  justify-content: flex-end;
  margin-top: 1rem;
}

.btn-primary {
  background: var(--color-primary, #0B1C3D);
  color: white;
  border: none;
  padding: 0.75rem 2rem;
  border-radius: 4px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-primary:hover:not(:disabled) {
  background: #1a365d;
}

.btn-primary:disabled {
  background: #6c757d;
  cursor: not-allowed;
}

.progress-section {
  margin-top: 2rem;
}

.progress-bar {
  height: 10px;
  background: #e9ecef;
  border-radius: 5px;
  overflow: hidden;
  margin-top: 0.5rem;
}

.progress-fill {
  height: 100%;
  background: var(--color-secondary, #D4AF37);
  transition: width 0.3s ease;
}

.alert {
  padding: 1rem;
  border-radius: 4px;
  margin-top: 1rem;
}

.alert.success {
  background: #d1e7dd;
  color: #0f5132;
  border-left: 4px solid #198754;
}

.alert.error {
  background: #f8d7da;
  color: #842029;
  border-left: 4px solid #dc3545;
}
</style>
