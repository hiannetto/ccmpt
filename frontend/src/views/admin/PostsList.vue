<template>
  <div class="list-container">
    <div class="header-actions">
      <h2>Gerenciar Notícias e Eventos</h2>
      <router-link to="/admin/posts/novo" class="btn-primary">Criar Publicação</router-link>
    </div>
    
    <!-- Filtros -->
    <div class="card filters">
      <div class="filter-group">
        <label>Buscar</label>
        <input type="text" v-model="filters.q" placeholder="Buscar no título..." @keyup.enter="fetchPosts" />
      </div>
      <div class="filter-group">
        <label>Tipo</label>
        <select v-model="filters.type" @change="fetchPosts">
          <option value="">Todos</option>
          <option value="news">Notícias</option>
          <option value="event">Eventos</option>
        </select>
      </div>
      <button class="btn-secondary btn-search" @click="fetchPosts">Buscar</button>
    </div>

    <!-- Tabela de Publicações -->
    <div class="card table-container">
      <div v-if="loading" class="loading-state">Carregando publicações...</div>
      
      <table v-else-if="posts.length > 0" class="data-table">
        <thead>
          <tr>
            <th>Título</th>
            <th width="100">Tipo</th>
            <th width="100">Status</th>
            <th width="150">Data</th>
            <th width="180">Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="post in posts" :key="post.id">
            <td>
              <strong>{{ post.title }}</strong>
            </td>
            <td>
              <span class="badge" :class="post.type === 'event' ? 'badge-event' : 'badge-news'">
                {{ post.type === 'event' ? 'Evento' : 'Notícia' }}
              </span>
            </td>
            <td>
              <span class="badge" :class="post.is_published ? 'badge-success' : 'badge-draft'">
                {{ post.is_published ? 'Publicado' : 'Rascunho' }}
              </span>
            </td>
            <td>{{ formatDate(post.created_at) }}</td>
            <td class="actions">
              <router-link :to="{ name: 'NewPost', query: { id: post.id } }" class="btn-action edit" title="Editar">
                ✎
              </router-link>
              <a :href="'/noticias/' + post.slug" target="_blank" class="btn-action view" title="Ver no site">
                👁
              </a>
              <button @click="deletePost(post.id)" class="btn-action delete" title="Excluir">
                🗑
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      
      <div v-else class="empty-state">
        Nenhuma publicação encontrada.
      </div>

      <!-- Paginação -->
      <div class="pagination" v-if="totalPages > 1">
        <button @click="changePage(page - 1)" :disabled="page === 1">&laquo;</button>
        <span>Página {{ page }} de {{ totalPages }}</span>
        <button @click="changePage(page + 1)" :disabled="page === totalPages">&raquo;</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../api';

const posts = ref([]);
const loading = ref(true);
const page = ref(1);
const totalPages = ref(1);

const filters = ref({
  q: '',
  type: ''
});

const fetchPosts = async () => {
  loading.value = true;
  try {
    const res = await api.get('/posts', {
      params: { 
        page: page.value, 
        q: filters.value.q,
        type: filters.value.type
      }
    });
    posts.value = res.data.data;
    totalPages.value = res.data.last_page;
  } catch (err) {
    alert('Erro ao carregar publicações.');
    console.error(err);
  } finally {
    loading.value = false;
  }
};

const deletePost = async (id) => {
  if (confirm('Tem certeza que deseja excluir esta publicação?')) {
    try {
      await api.delete(`/posts/${id}`);
      fetchPosts();
    } catch (e) {
      alert('Erro ao excluir publicação.');
    }
  }
};

const changePage = (p) => {
  if (p >= 1 && p <= totalPages.value) {
    page.value = p;
    fetchPosts();
  }
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  const d = new Date(dateString);
  return d.toLocaleDateString('pt-BR');
};

onMounted(() => {
  fetchPosts();
});
</script>

<style scoped>
.list-container { max-width: 1000px; margin: 0 auto; padding-bottom: 2rem; }
.header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
h2 { margin: 0; color: var(--color-primary, #0B1C3D); }
.btn-primary { background: var(--color-primary, #0B1C3D); color: white; padding: 0.75rem 1.5rem; text-decoration: none; border-radius: 4px; font-weight: 500; }
.btn-secondary { background: #6c757d; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 4px; cursor: pointer; }

.card { background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 1.5rem; }

.filters { display: flex; gap: 1rem; align-items: flex-end; }
.filter-group { flex: 1; display: flex; flex-direction: column; gap: 0.5rem; }
.filter-group label { font-size: 0.9rem; font-weight: 500; }
.filter-group input, .filter-group select { padding: 0.6rem; border: 1px solid #ced4da; border-radius: 4px; }
.btn-search { flex: 0 0 auto; }

.data-table { width: 100%; border-collapse: collapse; }
.data-table th { text-align: left; padding: 1rem; background: #f8f9fa; border-bottom: 2px solid #dee2e6; color: #495057; font-weight: 600; }
.data-table td { padding: 1rem; border-bottom: 1px solid #e9ecef; vertical-align: middle; }

.badge { display: inline-block; padding: 0.25rem 0.6rem; border-radius: 12px; font-size: 0.8rem; font-weight: 500; }
.badge-news { background: #e3f2fd; color: #0d47a1; }
.badge-event { background: #f3e5f5; color: #4a148c; }
.badge-success { background: #e8f5e9; color: #1b5e20; }
.badge-draft { background: #fff3e0; color: #e65100; }

.actions { display: flex; gap: 0.5rem; }
.btn-action { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 4px; text-decoration: none; cursor: pointer; border: 1px solid transparent; font-size: 1rem; }
.btn-action.edit { background: #e9ecef; color: #495057; }
.btn-action.view { background: #e3f2fd; color: #1976d2; }
.btn-action.delete { background: #ffebee; color: #d32f2f; }
.btn-action:hover { opacity: 0.8; }

.pagination { display: flex; justify-content: center; align-items: center; gap: 1rem; margin-top: 1.5rem; }
.pagination button { padding: 0.5rem 1rem; border: 1px solid #ced4da; background: white; border-radius: 4px; cursor: pointer; }
.pagination button:disabled { opacity: 0.5; cursor: not-allowed; }

.loading-state, .empty-state { text-align: center; padding: 3rem 0; color: #6c757d; }
</style>
