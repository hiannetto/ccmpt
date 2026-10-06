<template>
  <div class="form-container">
    <div class="header-actions">
      <h2>{{ isEditing ? 'Editar Item do Acervo' : 'Registrar Novo Item' }}</h2>
      <router-link :to="{ name: 'AdminMemorial' }" class="btn-secondary">Voltar</router-link>
    </div>

    <div v-if="error" class="alert error">{{ error }}</div>
    <div v-if="success" class="alert success">{{ success }}</div>

    <form @submit.prevent="saveItem" class="form-card">
      <div class="form-group">
        <label>Título / Nome do Item *</label>
        <input type="text" v-model="form.title" required placeholder="Ex: Máquina de Escrever Underwood" />
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Nº de Inventário (Tombo)</label>
          <input type="text" v-model="form.inventory_number" placeholder="Opcional" />
        </div>
        <div class="form-group">
          <label>Ano de Origem</label>
          <input type="number" v-model="form.year" placeholder="Opcional" />
        </div>
        <div class="form-group">
          <label>Datação (texto livre)</label>
          <input type="text" v-model="form.dating_label" placeholder="Ex: Década de 1970, Séc. XIX" />
        </div>
      </div>

      <div class="form-group">
        <label>Categorias</label>
        <div class="categories-list">
          <label v-for="cat in availableCategories" :key="cat.id">
            <input type="checkbox" :value="cat.id" v-model="form.category_ids" />
            {{ cat.name }} ({{ cat.type }})
          </label>
        </div>
      </div>

      <div class="form-group">
        <label>Descrição e Contexto Histórico *</label>
        <RichTextEditor v-model="form.historical_description" />
      </div>

      <h3 class="section-title">Ficha Técnica (Opcional)</h3>
      <div class="form-row">
        <div class="form-group">
          <label>Material / Técnica</label>
          <input type="text" v-model="form.material" placeholder="Ex: Ferro fundido e esmalte" />
        </div>
        <div class="form-group">
          <label>Dimensões</label>
          <input type="text" v-model="form.dimensions" placeholder="Ex: 30cm x 40cm x 20cm" />
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Procedência / Doação</label>
          <input type="text" v-model="form.provenance" placeholder="Doado por João da Silva" />
        </div>
        <div class="form-group">
          <label>Estado de Conservação</label>
          <select v-model="form.conservation_state">
            <option value="">Não informado</option>
            <option value="otimo">Ótimo</option>
            <option value="bom">Bom</option>
            <option value="regular">Regular</option>
            <option value="ruim">Ruim / precisa de restauro</option>
          </select>
        </div>
      </div>

      <h3 class="section-title">Imagem Principal</h3>
      <div class="form-group">
        <label>Foto de Capa <span v-if="!isEditing">*</span></label>
        <div v-if="existingMainImage" class="current-image">
          <img :src="existingMainImage" alt="Capa Atual" />
          <p>Para alterar, selecione um novo arquivo abaixo.</p>
        </div>
        <input type="file" ref="mainImageInput" accept="image/*" />
      </div>
      <div class="form-group">
        <label>Legenda da Foto Principal</label>
        <input type="text" v-model="form.main_image_caption" placeholder="Opcional" />
      </div>

      <div class="form-actions">
        <button type="submit" class="btn-primary" :disabled="saving">
          {{ saving ? 'Salvando...' : 'Salvar Item' }}
        </button>
      </div>
    </form>

    <div v-if="isEditing" class="gallery-section">
      <h3 class="section-title">Galeria de Fotos (Item Físico)</h3>
      <p class="help-text">Adicione mais fotos deste item. (Opcional)</p>
      
      <div class="upload-box">
        <input type="file" ref="extraImageInput" accept="image/*" />
        <input type="text" v-model="newImageCaption" placeholder="Legenda (Opcional)" />
        <button @click="uploadExtraImage" class="btn-secondary" :disabled="uploadingImage">
          {{ uploadingImage ? 'Enviando...' : 'Adicionar Foto' }}
        </button>
      </div>

      <div class="gallery-grid" v-if="extraImages.length">
        <div class="gallery-item" v-for="img in extraImages" :key="img.id">
          <img :src="img.thumbnail_url" alt="" />
          <div class="gallery-item-actions">
            <input type="text" v-model="img.caption" placeholder="Legenda..." />
            <div class="btn-group">
              <button @click="updateImageCaption(img)" class="btn-small">Salvar Legenda</button>
              <button @click="setAsMainImage(img.id)" class="btn-small">Usar como Capa</button>
              <button @click="deleteExtraImage(img.id)" class="btn-small btn-danger">Excluir</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../../api';
import RichTextEditor from '../../components/RichTextEditor.vue';

const route = useRoute();
const router = useRouter();

const isEditing = computed(() => !!route.params.id);
const itemId = computed(() => route.params.id);

const error = ref('');
const success = ref('');
const saving = ref(false);

const availableCategories = ref([]);

const mainImageInput = ref(null);
const existingMainImage = ref(null);

const form = ref({
  title: '',
  inventory_number: '',
  year: '',
  dating_label: '',
  category_ids: [],
  historical_description: '',
  material: '',
  dimensions: '',
  provenance: '',
  conservation_state: '',
  main_image_caption: ''
});

// Extra images state
const extraImages = ref([]);
const extraImageInput = ref(null);
const newImageCaption = ref('');
const uploadingImage = ref(false);

const loadCategories = async () => {
  try {
    const response = await api.get('/categories');
    availableCategories.value = response.data;
  } catch (e) {
    console.error('Erro ao carregar categorias', e);
  }
};

const loadItem = async () => {
  if (!isEditing.value) return;
  try {
    const response = await api.get(`/memorial/${itemId.value}`);
    const data = response.data;
    
    form.value = {
      title: data.title || '',
      inventory_number: data.inventory_number || '',
      year: data.year || '',
      dating_label: data.dating_label || '',
      historical_description: data.historical_description || '',
      material: data.material || '',
      dimensions: data.dimensions || '',
      provenance: data.provenance || '',
      conservation_state: data.conservation_state || '',
      main_image_caption: data.main_image_caption || '',
      category_ids: data.categories.map(c => c.id)
    };
    
    existingMainImage.value = data.main_thumbnail_url;
    extraImages.value = data.images || [];
  } catch (e) {
    error.value = 'Erro ao carregar os dados do item.';
  }
};

const saveItem = async () => {
  error.value = '';
  success.value = '';
  saving.value = true;
  
  try {
    const formData = new FormData();
    Object.keys(form.value).forEach(key => {
      if (key === 'category_ids') {
        form.value[key].forEach(id => formData.append('category_ids[]', id));
      } else if (form.value[key] !== null && form.value[key] !== '') {
        formData.append(key, form.value[key]);
      }
    });

    if (mainImageInput.value && mainImageInput.value.files[0]) {
      formData.append('main_image', mainImageInput.value.files[0]);
    }

    if (isEditing.value) {
      // Usar _method=PUT em envio de FormData
      formData.append('_method', 'PUT');
      await api.post(`/memorial/${itemId.value}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
      success.value = 'Item atualizado com sucesso!';
      loadItem(); // recarrega para atualizar a capa atual
    } else {
      const res = await api.post('/memorial', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
      router.push({ name: 'EditMemorial', params: { id: res.data.id } });
    }
  } catch (e) {
    error.value = e.response?.data?.error || 'Erro ao salvar o item.';
  } finally {
    saving.value = false;
  }
};

const uploadExtraImage = async () => {
  if (!extraImageInput.value || !extraImageInput.value.files[0]) {
    alert('Selecione uma imagem.');
    return;
  }
  uploadingImage.value = true;
  try {
    const formData = new FormData();
    formData.append('image', extraImageInput.value.files[0]);
    if (newImageCaption.value) formData.append('caption', newImageCaption.value);
    
    const response = await api.post(`/memorial/${itemId.value}/images`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
    extraImages.value.push(response.data.image);
    
    extraImageInput.value.value = '';
    newImageCaption.value = '';
  } catch (e) {
    alert('Erro ao enviar imagem. Verifique tamanho/formato.');
  } finally {
    uploadingImage.value = false;
  }
};

const updateImageCaption = async (img) => {
  try {
    await api.put(`/memorial/${itemId.value}/images/${img.id}`, { caption: img.caption });
    alert('Legenda salva com sucesso!');
  } catch (e) {
    alert('Erro ao salvar legenda.');
  }
};

const setAsMainImage = async (imageId) => {
  if (confirm('Deseja tornar esta imagem a capa do item? A capa atual será movida para a galeria.')) {
    try {
      await api.put(`/memorial/${itemId.value}/images/${imageId}/main`);
      loadItem(); // Reload the whole item to swap the arrays visually
    } catch (e) {
      alert('Erro ao definir capa.');
    }
  }
};

const deleteExtraImage = async (imageId) => {
  if (confirm('Tem certeza que deseja remover esta foto?')) {
    try {
      await api.delete(`/memorial/${itemId.value}/images/${imageId}`);
      extraImages.value = extraImages.value.filter(i => i.id !== imageId);
    } catch (e) {
      alert('Erro ao remover imagem.');
    }
  }
};

onMounted(() => {
  loadCategories();
  loadItem();
});
</script>

<style scoped>
.form-container { max-width: 1000px; margin: 0 auto; padding-bottom: 3rem; }
.header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
h2 { margin: 0; color: var(--color-primary, #0B1C3D); }
.section-title { margin-top: 2rem; margin-bottom: 1rem; color: #444; border-bottom: 1px solid #eee; padding-bottom: 0.5rem; }

.form-card { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 2rem; }

.form-group { margin-bottom: 1.5rem; }
.form-group label { display: block; font-weight: 500; margin-bottom: 0.5rem; }
.form-group input[type="text"], .form-group input[type="number"], .form-group select { width: 100%; padding: 0.75rem; border: 1px solid #ced4da; border-radius: 4px; font-family: inherit; }

.form-row { display: flex; gap: 1rem; }
.form-row .form-group { flex: 1; }

.categories-list { display: flex; flex-wrap: wrap; gap: 1rem; }
.categories-list label { font-weight: normal; display: flex; align-items: center; gap: 0.5rem; }

.current-image { margin-bottom: 1rem; }
.current-image img { max-width: 200px; max-height: 200px; border-radius: 4px; border: 1px solid #ddd; padding: 4px; }

.form-actions { margin-top: 2rem; padding-top: 1rem; border-top: 1px solid #eee; text-align: right; }
.btn-primary { background: var(--color-primary, #0B1C3D); color: white; padding: 0.75rem 1.5rem; border-radius: 4px; font-weight: 500; border: none; cursor: pointer; }
.btn-primary:disabled { opacity: 0.7; cursor: not-allowed; }
.btn-secondary { background: #6c757d; color: white; padding: 0.5rem 1rem; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; }
.btn-small { background: #f8f9fa; border: 1px solid #ddd; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; cursor: pointer; }
.btn-danger { color: #dc3545; border-color: #dc3545; background: white; }

.alert { padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem; }
.alert.error { background: #f8d7da; color: #842029; border: 1px solid #f5c2c7; }
.alert.success { background: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; }

.gallery-section { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
.help-text { color: #666; font-size: 0.9rem; margin-bottom: 1rem; }

.upload-box { display: flex; gap: 1rem; align-items: center; background: #f8f9fa; padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem; }
.upload-box input[type="text"] { flex: 1; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; }

.gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; }
.gallery-item { border: 1px solid #eee; border-radius: 4px; overflow: hidden; background: #fafafa; }
.gallery-item img { width: 100%; height: 150px; object-fit: cover; }
.gallery-item-actions { padding: 0.5rem; display: flex; flex-direction: column; gap: 0.5rem; }
.gallery-item-actions input { width: 100%; padding: 0.25rem; font-size: 0.85rem; border: 1px solid #ccc; border-radius: 2px; }
.btn-group { display: flex; gap: 0.25rem; justify-content: space-between; }
</style>
