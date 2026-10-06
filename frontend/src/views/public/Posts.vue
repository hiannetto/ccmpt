<template>
  <div class="posts-page">
    <div class="page-header">
      <div class="container">
        <h1>Notícias e Eventos</h1>
        <p>Acompanhe as últimas novidades e as ações sociais do Centro Cultural e Memorial Padre Tiago.</p>
      </div>
    </div>

    <div class="container">
      <div v-if="loading" class="loading-state">
        Carregando notícias...
      </div>
      
      <div v-else-if="error" class="error-state">
        {{ error }}
      </div>
      
      <div v-else-if="posts.length === 0" class="empty-state">
        Nenhuma notícia ou evento publicado no momento.
      </div>
      
      <div v-else class="posts-grid">
        <article v-for="post in posts" :key="post.id" class="post-card">
          <div class="post-content">
            <div class="post-meta">
              <span class="date">{{ formatDate(post.created_at) }}</span>
              <span v-if="post.event_date" class="event-badge">Evento: {{ formatDate(post.event_date) }}</span>
            </div>
            <h2>{{ post.title }}</h2>
            <div class="post-excerpt" v-html="getExcerpt(post.content)"></div>
          </div>
        </article>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../api';
import DOMPurify from 'dompurify';

const posts = ref([]);
const loading = ref(true);
const error = ref('');

onMounted(async () => {
  try {
    const response = await api.get('/posts');
    // Em um cenário real, poderíamos filtrar para exibir apenas os publicados (is_published)
    // A API deve preferencialmente já fazer esse filtro na rota pública.
    posts.value = response.data;
  } catch (err) {
    error.value = 'Ocorreu um erro ao carregar as notícias. Tente novamente mais tarde.';
    console.error(err);
  } finally {
    loading.value = false;
  }
});

const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('pt-BR', {
    day: '2-digit',
    month: 'long',
    year: 'numeric'
  }).format(date);
};

const getExcerpt = (htmlContent) => {
  // Limpa o HTML e pega apenas um trecho para o card
  const cleanHtml = DOMPurify.sanitize(htmlContent, { ALLOWED_TAGS: [] });
  if (cleanHtml.length > 150) {
    return cleanHtml.substring(0, 150) + '...';
  }
  return cleanHtml;
};
</script>

<style scoped>
.page-header {
  background-color: var(--color-primary, #0B1C3D);
  color: white;
  padding: 4rem 0;
  text-align: center;
  margin-bottom: 3rem;
}

.page-header h1 {
  margin: 0 0 1rem;
  color: var(--color-secondary, #D4AF37);
  font-size: 2.5rem;
}

.page-header p {
  font-size: 1.1rem;
  opacity: 0.9;
  max-width: 600px;
  margin: 0 auto;
}

.loading-state, .error-state, .empty-state {
  text-align: center;
  padding: 4rem 0;
  color: #666;
  font-size: 1.1rem;
}

.error-state {
  color: #dc3545;
}

.posts-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 2rem;
  margin-bottom: 4rem;
}

.post-card {
  background: white;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0,0,0,0.05);
  overflow: hidden;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  border-top: 4px solid var(--color-primary, #0B1C3D);
}

.post-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 15px rgba(0,0,0,0.1);
  border-top-color: var(--color-secondary, #D4AF37);
}

.post-content {
  padding: 1.5rem;
}

.post-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
  font-size: 0.85rem;
  color: #777;
}

.event-badge {
  background-color: #e6f4ea;
  color: #137333;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-weight: 500;
}

.post-card h2 {
  font-size: 1.25rem;
  margin: 0 0 1rem;
  color: var(--color-primary, #0B1C3D);
  line-height: 1.4;
}

.post-excerpt {
  color: #555;
  line-height: 1.6;
  font-size: 0.95rem;
}
</style>
