<template>
  <div class="form-container">
    <div class="header-actions">
      <h2>{{ isEditing ? 'Editar Publicação' : 'Nova Publicação' }}</h2>
      <router-link to="/admin/posts" class="btn-outline">Voltar</router-link>
    </div>

    <form @submit.prevent="savePost" class="post-form">
      <div v-if="error" class="alert error">{{ error }}</div>
      <div v-if="success" class="alert success">{{ success }}</div>

      <!-- Tipo -->
      <div class="form-group radio-group">
        <label>Tipo de Publicação:</label>
        <div class="options">
          <label><input type="radio" v-model="form.type" value="news" /> Notícia</label>
          <label><input type="radio" v-model="form.type" value="event" /> Evento</label>
        </div>
      </div>

      <div class="form-group">
        <label for="title">Título</label>
        <input type="text" id="title" v-model="form.title" required />
        <small class="hint">O link (slug) será gerado automaticamente a partir do título.</small>
      </div>

      <div class="grid-2">
        <!-- Data do evento (apenas se for evento) -->
        <div class="form-group" v-if="form.type === 'event'">
          <label for="event_date">Data do Evento</label>
          <input type="datetime-local" id="event_date" v-model="form.event_date" required />
        </div>

        <!-- Capa -->
        <div class="form-group">
          <label for="cover">Imagem de Capa</label>
          <input type="file" id="cover" accept="image/*" @change="handleFileChange" />
          <div v-if="currentCoverUrl && !coverImageFile" class="current-cover">
            <img :src="currentCoverUrl" alt="Capa atual" />
            <label class="remove-cover"><input type="checkbox" v-model="removeCover" /> Remover capa atual</label>
          </div>
        </div>
      </div>

      <div class="form-group">
        <label>Conteúdo</label>
        <RichTextEditor v-model="form.content" />
      </div>

      <div class="checkbox-group">
        <label>
          <input type="checkbox" v-model="form.is_published" />
          Publicar imediatamente
        </label>
      </div>

      <div class="actions">
        <button type="submit" class="btn-primary" :disabled="loading">
          {{ loading ? 'Salvando...' : 'Salvar Publicação' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import api from '../../api';
import RichTextEditor from '../../components/RichTextEditor.vue';

const router = useRouter();
const route = useRoute();

const loading = ref(false);
const error = ref('');
const success = ref('');
const isEditing = ref(false);
const postId = ref(null);

const form = ref({
  type: 'news',
  title: '',
  content: '',
  event_date: '',
  is_published: true
});

const coverImageFile = ref(null);
const currentCoverUrl = ref('');
const removeCover = ref(false);

onMounted(async () => {
  if (route.query.id) {
    isEditing.value = true;
    postId.value = route.query.id;
    loading.value = true;
    try {
      const res = await api.get(`/posts/${postId.value}`);
      const data = res.data;
      form.value = {
        type: data.type,
        title: data.title,
        content: data.content,
        event_date: data.event_date ? new Date(data.event_date).toISOString().slice(0, 16) : '',
        is_published: !!data.is_published
      };
      if (data.cover_image_url) {
        currentCoverUrl.value = data.cover_image_url;
      }
    } catch (e) {
      error.value = 'Erro ao carregar os dados da publicação.';
    } finally {
      loading.value = false;
    }
  }
});

const handleFileChange = (e) => {
  if (e.target.files.length > 0) {
    coverImageFile.value = e.target.files[0];
    removeCover.value = false;
  } else {
    coverImageFile.value = null;
  }
};

const savePost = async () => {
  error.value = '';
  success.value = '';
  loading.value = true;

  try {
    const formData = new FormData();
    formData.append('type', form.value.type);
    formData.append('title', form.value.title);
    formData.append('content', form.value.content);
    formData.append('is_published', form.value.is_published ? 1 : 0);
    
    if (form.value.type === 'event' && form.value.event_date) {
      // Ajuste para formato SQL YYYY-MM-DD HH:MM:SS
      const d = new Date(form.value.event_date);
      const sqlDate = d.toISOString().slice(0, 19).replace('T', ' ');
      formData.append('event_date', sqlDate);
    }
    
    if (coverImageFile.value) {
      formData.append('cover_image', coverImageFile.value);
    }
    if (removeCover.value) {
      formData.append('remove_cover', '1');
    }

    if (isEditing.value) {
      // Usar _method=PUT para o axios conseguir enviar arquivos via POST
      formData.append('_method', 'PUT');
      await api.post(`/posts/${postId.value}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
      success.value = 'Publicação atualizada com sucesso!';
    } else {
      await api.post('/posts', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
      success.value = 'Publicação criada com sucesso!';
    }
    
    setTimeout(() => {
      router.push('/admin/posts');
    }, 1500);
  } catch (err) {
    error.value = err.response?.data?.error || 'Erro ao salvar publicação.';
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.form-container {
  max-width: 900px;
  margin: 0 auto;
  background: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  margin-bottom: 2rem;
}

.header-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

h2 {
  margin: 0;
  color: var(--color-primary, #0B1C3D);
}

.btn-outline {
  padding: 0.5rem 1rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  text-decoration: none;
  color: #555;
  transition: background 0.2s;
}

.btn-outline:hover {
  background: #f8f9fa;
}

.form-group {
  margin-bottom: 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.grid-2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
}

label {
  font-weight: 500;
  color: #333;
}

.hint {
  color: #6c757d;
  font-size: 0.85rem;
}

input[type="text"], input[type="datetime-local"], input[type="file"] {
  padding: 0.75rem;
  border: 1px solid #ced4da;
  border-radius: 4px;
  font-size: 1rem;
}

.radio-group {
  flex-direction: row;
  align-items: center;
  gap: 2rem;
  background: #f8f9fa;
  padding: 1rem;
  border-radius: 4px;
}
.radio-group .options {
  display: flex;
  gap: 1.5rem;
}
.radio-group .options label {
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.current-cover {
  margin-top: 1rem;
}
.current-cover img {
  max-width: 200px;
  border-radius: 4px;
  display: block;
  margin-bottom: 0.5rem;
}
.remove-cover {
  font-size: 0.85rem;
  color: #dc3545;
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.checkbox-group {
  display: flex;
  gap: 2rem;
  margin-bottom: 1.5rem;
}
.checkbox-group label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: normal;
  cursor: pointer;
}

.actions {
  display: flex;
  justify-content: flex-end;
  margin-top: 2rem;
  padding-top: 1rem;
  border-top: 1px solid #eee;
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

.alert {
  padding: 1rem;
  border-radius: 4px;
  margin-bottom: 1.5rem;
}
.alert.success { background: #d1e7dd; color: #0f5132; border-left: 4px solid #198754; }
.alert.error { background: #f8d7da; color: #842029; border-left: 4px solid #dc3545; }
</style>
