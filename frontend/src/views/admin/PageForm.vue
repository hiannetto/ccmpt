<template>
  <div class="form-container">
    <div class="header-actions">
      <h2>Nova Página Institucional</h2>
      <router-link to="/admin/paginas" class="btn-outline">Voltar</router-link>
    </div>

    <form @submit.prevent="savePage" class="page-form">
      <div v-if="error" class="alert error">{{ error }}</div>
      <div v-if="success" class="alert success">{{ success }}</div>

      <div class="form-group">
        <label for="title">Título da Página</label>
        <input type="text" id="title" v-model="form.title" required />
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
        <label>
          <input type="checkbox" v-model="form.show_in_menu" />
          Mostrar no menu principal
        </label>
      </div>

      <div class="actions">
        <button type="submit" class="btn-primary" :disabled="loading">
          {{ loading ? 'Salvando...' : 'Salvar Página' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '../../api';
import RichTextEditor from '../../components/RichTextEditor.vue';

const router = useRouter();
const loading = ref(false);
const error = ref('');
const success = ref('');

const form = ref({
  title: '',
  content: '',
  is_published: true,
  show_in_menu: true
});

const savePage = async () => {
  error.value = '';
  success.value = '';
  loading.value = true;

  try {
    const payload = {
      ...form.value,
      is_published: form.value.is_published ? 1 : 0,
      show_in_menu: form.value.show_in_menu ? 1 : 0
    };
    
    await api.post('/pages', payload);
    success.value = 'Página criada com sucesso!';
    setTimeout(() => {
      router.push('/admin'); // Temporário até ter listagem de páginas
    }, 1500);
  } catch (err) {
    error.value = err.response?.data?.error || 'Erro ao salvar a página.';
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

label {
  font-weight: 500;
  color: #333;
}

input[type="text"] {
  padding: 0.75rem;
  border: 1px solid #ced4da;
  border-radius: 4px;
  font-size: 1rem;
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
