<template>
  <div class="post-detail-page">
    <div v-if="loading" class="loading-state">
      <div class="container">Carregando publicação...</div>
    </div>
    
    <div v-else-if="error" class="error-state">
      <div class="container">{{ error }}</div>
    </div>
    
    <article v-else-if="post" class="post-content">
      <div class="page-header">
        <div class="container">
          <div class="breadcrumb">
            <router-link to="/noticias">&laquo; Voltar para Notícias e Eventos</router-link>
          </div>
          
          <div class="meta">
            <span class="badge" :class="post.type === 'event' ? 'event-badge' : 'news-badge'">
              {{ post.type === 'event' ? 'Evento' : 'Notícia' }}
            </span>
            <span class="date" v-if="post.type === 'event' && post.event_date">
              Marcado para: {{ formatDateTime(post.event_date) }}
            </span>
            <span class="date" v-else>
              Publicado em: {{ formatDate(post.created_at) }}
            </span>
          </div>

          <h1>{{ post.title }}</h1>
        </div>
      </div>

      <div class="container">
        <div class="post-layout">
          <div v-if="post.cover_image_url" class="cover-image">
            <img :src="post.cover_image_url" :alt="post.title" />
          </div>
          
          <div class="rich-text-content content-formatted" v-html="post.content"></div>
        </div>
      </div>
    </article>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import api from '../../api';

const route = useRoute();
const post = ref(null);
const loading = ref(true);
const error = ref('');

onMounted(async () => {
  try {
    const res = await api.get(`/posts/slug/${route.params.slug}`);
    post.value = res.data;
  } catch (err) {
    error.value = 'Publicação não encontrada ou indisponível.';
    console.error(err);
  } finally {
    loading.value = false;
  }
});

const formatDateTime = (dateString) => {
  if (!dateString) return '';
  const d = new Date(dateString.replace(' ', 'T'));
  return d.toLocaleString('pt-BR', { day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' }).replace(' de ', '/');
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  const d = new Date(dateString);
  return d.toLocaleDateString('pt-BR', { day: '2-digit', month: 'long', year: 'numeric' });
};
</script>

<style scoped>
.page-header {
  background-color: var(--color-primary, #0B1C3D);
  color: white;
  padding: 3rem 0;
  margin-bottom: 3rem;
  border-bottom: 4px solid var(--color-secondary, #D4AF37);
  text-align: center;
}

.breadcrumb {
  margin-bottom: 2rem;
}
.breadcrumb a {
  color: #adb5bd;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.2s;
}
.breadcrumb a:hover {
  color: white;
}

.meta {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.badge {
  padding: 0.4rem 0.8rem;
  border-radius: 4px;
  font-weight: bold;
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.news-badge {
  background: white;
  color: var(--color-primary, #0B1C3D);
}

.event-badge {
  background: var(--color-secondary, #D4AF37);
  color: var(--color-primary, #0B1C3D);
}

.date {
  color: #dee2e6;
  font-size: 0.95rem;
}

.page-header h1 {
  margin: 0;
  font-size: 2.5rem;
  max-width: 800px;
  margin: 0 auto;
  line-height: 1.3;
}

.post-layout {
  max-width: 800px;
  margin: 0 auto 5rem;
}

.cover-image {
  margin-bottom: 3rem;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.cover-image img {
  width: 100%;
  height: auto;
  display: block;
}

.rich-text-content {
  font-size: 1.15rem;
  line-height: 1.8;
  color: #333;
}

.loading-state, .error-state {
  text-align: center; padding: 4rem 0; font-size: 1.1rem; color: #666;
}
</style>
