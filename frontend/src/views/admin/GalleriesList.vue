<template>
  <div class="list-container">
    <div class="header-actions">
      <h2>Gerenciar Arquivo Fotográfico</h2>
      <button class="btn-primary" @click="showCreateModal = true">Nova Galeria</button>
    </div>
    
    <div class="card">
      <table v-if="galleries.length > 0" class="data-table">
        <thead>
          <tr>
            <th>Capa</th>
            <th>Título</th>
            <th>Descrição</th>
            <th>Fotos</th>
            <th>Criado em</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="gallery in galleries" :key="gallery.id">
            <td>
              <img v-if="gallery.cover_thumbnail_url" :src="gallery.cover_thumbnail_url" class="thumb" />
              <div v-else class="thumb-placeholder">Sem Capa</div>
            </td>
            <td><strong>{{ gallery.title }}</strong></td>
            <td>{{ gallery.description || '-' }}</td>
            <td>{{ gallery.photos_count || 0 }}</td>
            <td>{{ new Date(gallery.created_at).toLocaleDateString('pt-BR') }}</td>
            <td class="actions">
              <router-link :to="{ name: 'EditGallery', params: { id: gallery.id } }" class="btn-edit">Editar Fotos</router-link>
              <button @click="confirmDelete(gallery.id)" class="btn-delete">Excluir</button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-else-if="loading" class="loading">Carregando galerias...</div>
      <div v-else class="empty-state">Nenhuma galeria encontrada.</div>
      
      <div class="pagination" v-if="totalPages > 1">
        <button :disabled="page === 1" @click="changePage(page - 1)">Anterior</button>
        <span>Página {{ page }} de {{ totalPages }}</span>
        <button :disabled="page === totalPages" @click="changePage(page + 1)">Próxima</button>
      </div>
    </div>

    <!-- Modal Nova Galeria -->
    <div v-if="showCreateModal" class="modal-overlay">
      <div class="modal-content">
        <h3>Nova Galeria</h3>
        <form @submit.prevent="createGallery">
          <div class="form-group">
            <label>Título *</label>
            <input type="text" v-model="newGallery.title" required />
          </div>
          <div class="form-group">
            <label>Descrição</label>
            <textarea v-model="newGallery.description" rows="3"></textarea>
          </div>
          <div class="modal-actions">
            <button type="button" @click="showCreateModal = false" class="btn-secondary">Cancelar</button>
            <button type="submit" class="btn-primary" :disabled="creating">Salvar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '../../api';

const router = useRouter();
const galleries = ref([]);
const totalItems = ref(0);
const page = ref(1);
const totalPages = ref(1);
const loading = ref(false);

const showCreateModal = ref(false);
const creating = ref(false);
const newGallery = ref({ title: '', description: '' });

const fetchGalleries = async () => {
  loading.value = true;
  try {
    const response = await api.get('/galleries');
    galleries.value = response.data || [];
    totalItems.value = galleries.value.length;
    totalPages.value = 1; // No backend pagination currently
  } catch (error) {
    console.error('Erro ao buscar galerias', error);
  } finally {
    loading.value = false;
  }
};

const changePage = (newPage) => {
  if (newPage >= 1 && newPage <= totalPages.value) {
    page.value = newPage;
    fetchGalleries();
  }
};

const createGallery = async () => {
  creating.value = true;
  try {
    const res = await api.post('/galleries', newGallery.value);
    showCreateModal.value = false;
    newGallery.value = { title: '', description: '' };
    router.push({ name: 'EditGallery', params: { id: res.data.id } });
  } catch (e) {
    alert('Erro ao criar galeria.');
  } finally {
    creating.value = false;
  }
};

const confirmDelete = async (id) => {
  if (confirm('Tem certeza que deseja excluir esta galeria e todas as suas fotos?')) {
    try {
      await api.delete(`/galleries/${id}`);
      fetchGalleries();
    } catch (error) {
      alert('Erro ao excluir galeria.');
    }
  }
};

onMounted(() => fetchGalleries());
</script>

<style scoped>
.list-container { max-width: 1200px; margin: 0 auto; }
.header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
h2 { margin: 0; color: var(--color-primary, #0B1C3D); }
.btn-primary { background: var(--color-primary, #0B1C3D); color: white; padding: 0.75rem 1.5rem; text-decoration: none; border-radius: 4px; font-weight: 500; border: none; cursor: pointer;}
.btn-secondary { background: #6c757d; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 4px; cursor: pointer; }
.card { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th, .data-table td { padding: 1rem; border-bottom: 1px solid #eee; text-align: left; }
.thumb { width: 60px; height: 60px; object-fit: cover; border-radius: 4px; }
.thumb-placeholder { width: 60px; height: 60px; background: #eee; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; color: #999; text-align: center; }
.actions { display: flex; gap: 0.5rem; }
.btn-edit { background: #ffc107; color: #000; padding: 0.5rem 1rem; text-decoration: none; border-radius: 4px; font-size: 0.875rem; }
.btn-delete { background: #dc3545; color: white; padding: 0.5rem 1rem; border: none; border-radius: 4px; font-size: 0.875rem; cursor: pointer; }
.loading, .empty-state { text-align: center; padding: 2rem; color: #666; }
.pagination { display: flex; justify-content: center; align-items: center; gap: 1rem; margin-top: 2rem; }
.pagination button { padding: 0.5rem 1rem; border: 1px solid #ddd; background: white; border-radius: 4px; cursor: pointer; }
.pagination button:disabled { opacity: 0.5; cursor: not-allowed; }

/* Modal */
.modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 1000; }
.modal-content { background: white; padding: 2rem; border-radius: 8px; width: 100%; max-width: 500px; }
.modal-content h3 { margin-top: 0; margin-bottom: 1.5rem; }
.form-group { margin-bottom: 1rem; }
.form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
.form-group input, .form-group textarea { width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 4px; font-family: inherit; }
.modal-actions { display: flex; justify-content: flex-end; gap: 1rem; margin-top: 1.5rem; }
</style>
