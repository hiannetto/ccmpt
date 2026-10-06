<template>
  <div class="list-container">
    <div class="header-actions">
      <h2>Acervo Histórico</h2>
      <router-link :to="{ name: 'NewMemorial' }" class="btn-primary">Registrar Novo Item</router-link>
    </div>
    
    <div class="filters">
      <input type="text" v-model="searchQuery" placeholder="Buscar por título, descrição, inventário..." @keyup.enter="fetchItems" />
      <button @click="fetchItems" class="btn-secondary">Buscar</button>
    </div>
    
    <div class="card">
      <table v-if="items.length > 0" class="data-table">
        <thead>
          <tr>
            <th>Capa</th>
            <th>Título / Nº Inventário</th>
            <th>Datação</th>
            <th>Fotos</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in items" :key="item.id">
            <td>
              <img :src="item.main_thumbnail_url" alt="Capa" class="thumb" />
            </td>
            <td>
              <strong>{{ item.title }}</strong><br>
              <small v-if="item.inventory_number">Nº: {{ item.inventory_number }}</small>
            </td>
            <td>{{ item.year || item.dating_label || 'Não informada' }}</td>
            <td>{{ item.images_count }}</td>
            <td class="actions">
              <router-link :to="{ name: 'EditMemorial', params: { id: item.id } }" class="btn-edit">Editar</router-link>
              <button @click="confirmDelete(item.id)" class="btn-delete">Excluir</button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-else-if="loading" class="loading">Carregando itens...</div>
      <div v-else class="empty-state">Nenhum item encontrado.</div>
      
      <div class="pagination" v-if="totalPages > 1">
        <button :disabled="page === 1" @click="changePage(page - 1)">Anterior</button>
        <span>Página {{ page }} de {{ totalPages }}</span>
        <button :disabled="page === totalPages" @click="changePage(page + 1)">Próxima</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../api';

const items = ref([]);
const totalItems = ref(0);
const page = ref(1);
const totalPages = ref(1);
const searchQuery = ref('');
const loading = ref(false);

const fetchItems = async () => {
  loading.value = true;
  try {
    const response = await api.get('/memorial', {
      params: { q: searchQuery.value, page: page.value }
    });
    items.value = response.data.data;
    totalItems.value = response.data.total;
    totalPages.value = response.data.last_page;
  } catch (error) {
    console.error('Erro ao buscar acervo', error);
  } finally {
    loading.value = false;
  }
};

const changePage = (newPage) => {
  if (newPage >= 1 && newPage <= totalPages.value) {
    page.value = newPage;
    fetchItems();
  }
};

const confirmDelete = async (id) => {
  if (confirm('Tem certeza que deseja excluir este item do acervo?')) {
    try {
      await api.delete(`/memorial/${id}`);
      fetchItems();
    } catch (error) {
      console.error('Erro ao excluir item', error);
      alert('Erro ao excluir item.');
    }
  }
};

onMounted(() => {
  fetchItems();
});
</script>

<style scoped>
.list-container { max-width: 1200px; margin: 0 auto; }
.header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
h2 { margin: 0; color: var(--color-primary, #0B1C3D); }
.btn-primary { background: var(--color-primary, #0B1C3D); color: white; padding: 0.75rem 1.5rem; text-decoration: none; border-radius: 4px; font-weight: 500; border: none; cursor: pointer;}
.btn-secondary { background: #6c757d; color: white; padding: 0.5rem 1rem; border: none; border-radius: 4px; cursor: pointer; }
.filters { display: flex; gap: 1rem; margin-bottom: 1rem; }
.filters input { flex: 1; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px; }
.card { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th, .data-table td { padding: 1rem; border-bottom: 1px solid #eee; text-align: left; }
.thumb { width: 60px; height: 60px; object-fit: cover; border-radius: 4px; }
.actions { display: flex; gap: 0.5rem; }
.btn-edit { background: #ffc107; color: #000; padding: 0.5rem 1rem; text-decoration: none; border-radius: 4px; font-size: 0.875rem; }
.btn-delete { background: #dc3545; color: white; padding: 0.5rem 1rem; border: none; border-radius: 4px; font-size: 0.875rem; cursor: pointer; }
.loading, .empty-state { text-align: center; padding: 2rem; color: #666; }
.pagination { display: flex; justify-content: center; align-items: center; gap: 1rem; margin-top: 2rem; }
.pagination button { padding: 0.5rem 1rem; border: 1px solid #ddd; background: white; border-radius: 4px; cursor: pointer; }
.pagination button:disabled { opacity: 0.5; cursor: not-allowed; }
</style>
