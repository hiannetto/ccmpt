<template>
  <div class="memorial-page">
    <div class="page-header">
      <div class="container">
        <h1>Acervo Histórico</h1>
        <p>Conheça os objetos, documentos e relíquias que marcam a trajetória do Padre Tiago e da nossa comunidade.</p>
      </div>
    </div>

    <div class="container">
      <div class="search-bar">
        <input 
          type="text" 
          v-model="searchQuery" 
          placeholder="Pesquisar por título, inventário, proveniência ou descrição..."
          @keyup.enter="search"
        />
        <button @click="search" class="btn-primary">Buscar</button>
      </div>

      <div v-if="loading" class="loading-state">
        Carregando acervo...
      </div>
      
      <div v-else-if="error" class="error-state">
        {{ error }}
      </div>
      
      <div v-else-if="items.length === 0" class="empty-state">
        Nenhum item encontrado no acervo.
      </div>
      
      <div v-else>
        <div class="items-grid">
          <router-link 
            v-for="item in items" 
            :key="item.id" 
            :to="{ name: 'MemorialItem', params: { id: item.id } }"
            class="item-card"
          >
            <div class="img-wrapper">
              <img v-if="item.main_thumbnail_url" :src="item.main_thumbnail_url" :alt="item.title" />
              <div v-else class="no-image">Sem Foto</div>
            </div>
            <div class="item-info">
              <h3>{{ item.title }}</h3>
              <p v-if="item.dating_label || item.year" class="item-date">
                {{ item.dating_label || item.year }}
              </p>
              <p class="item-excerpt">{{ item.excerpt }}</p>
            </div>
          </router-link>
        </div>

        <!-- Paginação Simples -->
        <div class="pagination" v-if="totalPages > 1">
          <button @click="changePage(page - 1)" :disabled="page === 1">&laquo; Anterior</button>
          <span>Página {{ page }} de {{ totalPages }}</span>
          <button @click="changePage(page + 1)" :disabled="page === totalPages">Próxima &raquo;</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../api';

const items = ref([]);
const loading = ref(true);
const error = ref('');
const searchQuery = ref('');
const page = ref(1);
const totalPages = ref(1);

const fetchItems = async () => {
  loading.value = true;
  error.value = '';
  try {
    const res = await api.get('/memorial', {
      params: { q: searchQuery.value, page: page.value }
    });
    items.value = res.data.data;
    totalPages.value = res.data.last_page;
  } catch (err) {
    error.value = 'Ocorreu um erro ao carregar o acervo histórico.';
    console.error(err);
  } finally {
    loading.value = false;
  }
};

const search = () => {
  page.value = 1;
  fetchItems();
};

const changePage = (newPage) => {
  if (newPage >= 1 && newPage <= totalPages.value) {
    page.value = newPage;
    fetchItems();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
};

onMounted(() => {
  fetchItems();
});
</script>

<style scoped>
.page-header {
  background-color: var(--color-primary, #0B1C3D);
  color: white;
  padding: 4rem 0;
  text-align: center;
  margin-bottom: 3rem;
  border-bottom: 4px solid var(--color-secondary, #D4AF37);
}

.page-header h1 {
  margin: 0 0 1rem;
  font-size: 2.5rem;
}

.page-header p {
  font-size: 1.1rem;
  opacity: 0.9;
  max-width: 600px;
  margin: 0 auto;
}

.search-bar {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
  max-width: 600px;
  margin-left: auto;
  margin-right: auto;
}

.search-bar input {
  flex: 1;
  padding: 0.75rem 1rem;
  border: 1px solid #ced4da;
  border-radius: 4px;
  font-size: 1rem;
}

.btn-primary {
  background: var(--color-primary, #0B1C3D);
  color: white;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
}

.btn-primary:hover {
  background: var(--color-secondary, #D4AF37);
}

.items-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 2rem;
  margin-bottom: 3rem;
}

.item-card {
  background: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 4px 6px rgba(0,0,0,0.05);
  text-decoration: none;
  color: inherit;
  display: flex;
  flex-direction: column;
  transition: transform 0.2s, box-shadow 0.2s;
}

.item-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 15px rgba(0,0,0,0.1);
}

.img-wrapper {
  height: 200px;
  background: #f8f9fa;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.img-wrapper img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.no-image {
  color: #adb5bd;
  font-size: 0.9rem;
}

.item-info {
  padding: 1.5rem;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.item-info h3 {
  margin: 0 0 0.5rem;
  color: var(--color-primary, #0B1C3D);
  font-size: 1.25rem;
}

.item-date {
  color: var(--color-secondary, #D4AF37);
  font-weight: 500;
  font-size: 0.9rem;
  margin: 0 0 1rem;
}

.item-excerpt {
  color: #6c757d;
  font-size: 0.95rem;
  line-height: 1.5;
  margin: 0;
  flex: 1;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1.5rem;
  margin: 2rem 0 4rem;
}

.pagination button {
  padding: 0.5rem 1rem;
  border: 1px solid #ced4da;
  background: white;
  border-radius: 4px;
  cursor: pointer;
}

.pagination button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.loading-state, .error-state, .empty-state {
  text-align: center;
  padding: 4rem 0;
  color: #6c757d;
  font-size: 1.1rem;
}
</style>
