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
        Carregando publicações...
      </div>
      
      <div v-else-if="error" class="error-state">
        {{ error }}
      </div>
      
      <div v-else>
        
        <!-- PRÓXIMOS EVENTOS -->
        <section class="content-section">
          <h2 class="section-title">Próximos Eventos</h2>
          <div v-if="futureEvents.length === 0" class="empty-message">
            Os próximos eventos aparecerão aqui.
          </div>
          <div v-else class="posts-grid">
            <router-link v-for="post in futureEvents" :key="post.id" :to="{ name: 'PostDetail', params: { slug: post.slug } }" class="post-card">
              <div class="img-wrapper">
                <img v-if="post.cover_image_url" :src="post.cover_image_url" :alt="post.title" loading="lazy" />
                <div v-else class="no-image">Sem Imagem</div>
                <div class="date-badge event-badge">
                  <span>{{ formatEventDateShort(post.event_date) }}</span>
                </div>
              </div>
              <div class="post-content">
                <h3>{{ post.title }}</h3>
                <div class="post-excerpt" v-html="getExcerpt(post.content)"></div>
              </div>
            </router-link>
          </div>
        </section>

        <!-- EVENTOS PASSADOS -->
        <section class="content-section" v-if="pastEvents.length > 0">
          <h2 class="section-title">Eventos Passados</h2>
          <div class="posts-grid">
            <router-link v-for="post in pastEvents" :key="post.id" :to="{ name: 'PostDetail', params: { slug: post.slug } }" class="post-card past-event">
              <div class="img-wrapper">
                <img v-if="post.cover_image_url" :src="post.cover_image_url" :alt="post.title" loading="lazy" />
                <div v-else class="no-image">Sem Imagem</div>
                <div class="date-badge event-badge past">
                  <span>{{ formatEventDateShort(post.event_date) }}</span>
                </div>
              </div>
              <div class="post-content">
                <h3>{{ post.title }}</h3>
                <div class="post-excerpt" v-html="getExcerpt(post.content)"></div>
              </div>
            </router-link>
          </div>
        </section>

        <!-- NOTÍCIAS -->
        <section class="content-section" v-if="news.length > 0">
          <h2 class="section-title">Últimas Notícias</h2>
          <div class="posts-grid">
            <router-link v-for="post in news" :key="post.id" :to="{ name: 'PostDetail', params: { slug: post.slug } }" class="post-card">
              <div class="img-wrapper">
                <img v-if="post.cover_image_url" :src="post.cover_image_url" :alt="post.title" loading="lazy" />
                <div v-else class="no-image">Sem Imagem</div>
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
        </section>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import api from '../../api';
import DOMPurify from 'dompurify';

const posts = ref([]);
const loading = ref(true);
const error = ref('');

onMounted(async () => {
  try {
    // Busca apenas posts publicados
    // A API em /posts?published=1 faz o filtro quando não estamos logados
    // O endpoint padrão já filtra publicados para visitantes, mas podemos garantir
    const response = await api.get('/posts?published=1');
    
    // O array real pode estar em response.data ou response.data.data (se paginado)
    posts.value = Array.isArray(response.data) ? response.data : response.data.data || [];
  } catch (err) {
    error.value = 'Ocorreu um erro ao carregar as publicações. Tente novamente mais tarde.';
    console.error(err);
  } finally {
    loading.value = false;
  }
});

const getBrasiliaTime = () => {
  return new Date(new Date().toLocaleString('en-US', { timeZone: 'America/Sao_Paulo' }));
};

const futureEvents = computed(() => {
  const now = getBrasiliaTime();
  return posts.value
    .filter(p => p.type === 'event' && p.event_date && new Date(p.event_date.replace(' ', 'T')) >= now)
    .sort((a, b) => new Date(a.event_date.replace(' ', 'T')) - new Date(b.event_date.replace(' ', 'T'))); // Crescente (mais próximo primeiro)
});

const pastEvents = computed(() => {
  const now = getBrasiliaTime();
  return posts.value
    .filter(p => p.type === 'event' && p.event_date && new Date(p.event_date.replace(' ', 'T')) < now)
    .sort((a, b) => new Date(b.event_date.replace(' ', 'T')) - new Date(a.event_date.replace(' ', 'T'))); // Decrescente (mais recente primeiro)
});

const news = computed(() => {
  return posts.value.filter(p => p.type === 'news');
});

const formatEventDateShort = (dateString) => {
  if (!dateString) return '';
  const d = new Date(dateString.replace(' ', 'T'));
  return d.toLocaleDateString('pt-BR', { day: '2-digit', month: 'short' }).replace(' de ', '/');
};

const formatPublishDateShort = (dateString) => {
  if (!dateString) return '';
  const d = new Date(dateString);
  return d.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric' });
};

const getExcerpt = (htmlContent) => {
  const cleanHtml = DOMPurify.sanitize(htmlContent, { ALLOWED_TAGS: [] });
  if (cleanHtml.length > 120) {
    return cleanHtml.substring(0, 120) + '...';
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

.content-section {
  margin-bottom: 5rem;
}

.section-title {
  color: var(--color-primary, #0B1C3D);
  margin-bottom: 2rem;
  font-size: 1.75rem;
  border-bottom: 2px solid #f1f3f5;
  padding-bottom: 0.5rem;
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

.past-event {
  opacity: 0.85;
}
.past-event:hover {
  opacity: 1;
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
  font-size: 1.1rem;
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

.event-badge {
  background: var(--color-secondary, #D4AF37);
  color: var(--color-primary, #0B1C3D);
}

.event-badge.past {
  background: #6c757d;
  color: white;
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

.loading-state, .error-state, .empty-message {
  text-align: center;
  padding: 3rem 0;
  color: #6c757d;
  font-size: 1.1rem;
}

.empty-message {
  background: #f8f9fa;
  border-radius: 8px;
  font-style: italic;
}
</style>
