<template>
  <section class="news-section">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">{{ siteSettings.news.title }}</h2>
      </div>

      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <p>Carregando novidades...</p>
      </div>
      
      <div v-else-if="error" class="error-state">
        <p>{{ error }}</p>
      </div>

      <div v-else-if="news.length === 0" class="empty-state">
        <p>Nenhuma notícia publicada ainda.</p>
      </div>

      <div v-else class="posts-grid">
        <router-link v-for="post in news" :key="post.id" :to="{ name: 'PostDetail', params: { slug: post.slug } }" class="post-card">
          <div class="img-wrapper">
            <img v-if="post.cover_image_url" :src="post.cover_image_url" :alt="post.title" loading="lazy" />
            <div v-else class="no-image">
              <PhNewspaper :size="48" weight="light" />
            </div>
            <div class="date-badge news-badge">
              <span>{{ formatPublishDateShort(post.created_at) }}</span>
            </div>
          </div>
          <div class="post-content">
            <h3>{{ post.title }}</h3>
            <div class="post-excerpt" v-html="getExcerpt(post.content)"></div>
          </div>
        </router-link>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { siteSettings } from '../../config/siteSettings';
import api from '../../api';
import DOMPurify from 'dompurify';
import { PhNewspaper } from '@phosphor-icons/vue';

const news = ref([]);
const loading = ref(true);
const error = ref(null);

const formatPublishDateShort = (dateString) => {
  if (!dateString) return '';
  const d = new Date(dateString);
  return d.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric' });
};

const getExcerpt = (htmlContent) => {
  if (!htmlContent) return '';
  const cleanHtml = DOMPurify.sanitize(htmlContent, { ALLOWED_TAGS: [] });
  if (cleanHtml.length > 120) {
    return cleanHtml.substring(0, 120) + '...';
  }
  return cleanHtml;
};

onMounted(async () => {
  try {
    // Busca apenas as 3 últimas notícias (já limitadas e filtradas pela API)
    const res = await api.get('/posts?limit=3');
    const data = Array.isArray(res.data) ? res.data : (res.data.data || []);
    // Garantir que peguemos apenas news e no máximo 3
    news.value = data.filter(p => p.type === 'news').slice(0, 3);
  } catch (err) {
    console.error('Erro ao buscar notícias:', err);
    error.value = 'Não foi possível carregar as últimas notícias no momento.';
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
.news-section {
  padding: 6rem 0;
  background-color: var(--bg-light);
}

.section-header {
  text-align: center;
  margin-bottom: 4rem;
}

.section-title {
  font-size: 2.2rem;
  color: var(--color-primary);
}

.posts-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 2rem;
}

.post-card {
  background: white;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0,0,0,0.05);
  overflow: hidden;
  text-decoration: none;
  color: inherit;
  display: flex;
  flex-direction: column;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.post-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 20px rgba(0,0,0,0.1);
}

.img-wrapper {
  position: relative;
  height: 200px;
  background-color: #f8f9fa;
  overflow: hidden;
}

.img-wrapper img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.post-card:hover .img-wrapper img {
  transform: scale(1.05);
}

.no-image {
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #adb5bd;
}

.date-badge {
  position: absolute;
  top: 1rem;
  left: 1rem;
  padding: 0.4rem 0.8rem;
  border-radius: 4px;
  font-weight: bold;
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.news-badge {
  background: var(--color-primary, #0B1C3D);
  color: white;
}

.post-content {
  padding: 1.5rem;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.post-content h3 {
  font-size: 1.25rem;
  margin: 0 0 1rem;
  color: var(--color-primary, #0B1C3D);
  line-height: 1.4;
}

.post-excerpt {
  color: #555;
  line-height: 1.6;
  font-size: 0.95rem;
  flex: 1;
}

/* Loading e Erros */
.loading-state, .error-state, .empty-state {
  text-align: center;
  padding: 3rem;
  color: var(--text-secondary);
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid rgba(10, 17, 40, 0.1);
  border-left-color: var(--color-secondary);
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
