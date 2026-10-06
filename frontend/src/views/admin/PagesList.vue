<template>
  <div class="list-container">
    <div class="header-actions">
      <h2>Gerenciar Páginas Institucionais</h2>
      <router-link to="/admin/paginas/nova" class="btn-primary">Criar Nova Página</router-link>
    </div>
    
    <div class="card table-container">
      <div v-if="loading" class="loading-state">Carregando páginas...</div>
      
      <table v-else-if="pages.length > 0" class="data-table">
        <thead>
          <tr>
            <th>Título</th>
            <th>Link (Slug)</th>
            <th width="100">Menu</th>
            <th width="120">Status</th>
            <th width="160">Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="page in pages" :key="page.id">
            <td>
              <strong>{{ page.title }}</strong>
            </td>
            <td class="slug-cell">/pagina/{{ page.slug }}</td>
            <td>
              <span class="badge" :class="page.show_in_menu ? 'badge-success' : 'badge-default'">
                {{ page.show_in_menu ? 'Sim' : 'Não' }}
              </span>
            </td>
            <td>
              <span class="badge" :class="page.is_published ? 'badge-success' : 'badge-draft'">
                {{ page.is_published ? 'Publicada' : 'Rascunho' }}
              </span>
            </td>
            <td class="actions">
              <router-link :to="{ name: 'NewPage', query: { id: page.id } }" class="btn-action edit" title="Editar">
                ✎
              </router-link>
              <a :href="'/pagina/' + page.slug" target="_blank" class="btn-action view" title="Ver no site">
                👁
              </a>
              <button @click="deletePage(page.id)" class="btn-action delete" title="Excluir">
                🗑
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      
      <div v-else class="empty-state">
        Nenhuma página institucional encontrada.
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../api';

const pages = ref([]);
const loading = ref(true);

const fetchPages = async () => {
  loading.value = true;
  try {
    const res = await api.get('/pages');
    // Assume-se que /pages retorna um array, não paginado
    pages.value = Array.isArray(res.data) ? res.data : [];
  } catch (err) {
    alert('Erro ao carregar páginas institucionais.');
    console.error(err);
  } finally {
    loading.value = false;
  }
};

const deletePage = async (id) => {
  if (confirm('Tem certeza que deseja excluir esta página? Essa ação não pode ser desfeita.')) {
    try {
      await api.delete(`/pages/${id}`);
      fetchPages();
    } catch (e) {
      alert('Erro ao excluir página.');
    }
  }
};

onMounted(() => {
  fetchPages();
});
</script>

<style scoped>
.list-container { max-width: 1000px; margin: 0 auto; padding-bottom: 2rem; }
.header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
h2 { margin: 0; color: var(--color-primary, #0B1C3D); }
.btn-primary { background: var(--color-primary, #0B1C3D); color: white; padding: 0.75rem 1.5rem; text-decoration: none; border-radius: 4px; font-weight: 500; }

.card { background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 1.5rem; }

.data-table { width: 100%; border-collapse: collapse; }
.data-table th { text-align: left; padding: 1rem; background: #f8f9fa; border-bottom: 2px solid #dee2e6; color: #495057; font-weight: 600; }
.data-table td { padding: 1rem; border-bottom: 1px solid #e9ecef; vertical-align: middle; }

.slug-cell { color: #6c757d; font-family: monospace; }

.badge { display: inline-block; padding: 0.25rem 0.6rem; border-radius: 12px; font-size: 0.8rem; font-weight: 500; }
.badge-success { background: #e8f5e9; color: #1b5e20; }
.badge-draft { background: #fff3e0; color: #e65100; }
.badge-default { background: #f8f9fa; color: #6c757d; border: 1px solid #dee2e6; }

.actions { display: flex; gap: 0.5rem; }
.btn-action { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 4px; text-decoration: none; cursor: pointer; border: 1px solid transparent; font-size: 1rem; }
.btn-action.edit { background: #e9ecef; color: #495057; }
.btn-action.view { background: #e3f2fd; color: #1976d2; }
.btn-action.delete { background: #ffebee; color: #d32f2f; }
.btn-action:hover { opacity: 0.8; }

.loading-state, .empty-state { text-align: center; padding: 3rem 0; color: #6c757d; }
</style>
