<template>
  <div class="page-detail">
    <div class="page-header">
      <div class="container">
        <h1>{{ page?.title || 'Carregando...' }}</h1>
      </div>
    </div>

    <div class="container">
      <div v-if="loading" class="loading-state">
        Carregando conteúdo...
      </div>
      
      <div v-else-if="error" class="error-state">
        {{ error }}
      </div>
      
      <div v-else class="rich-text-content content-formatted" v-html="page.content"></div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import api from '../../api';

const route = useRoute();
const page = ref(null);
const loading = ref(true);
const error = ref('');

const fetchPage = async (slug) => {
  loading.value = true;
  error.value = '';
  try {
    const res = await api.get(`/pages/slug/${slug}`);
    page.value = res.data;
  } catch (err) {
    error.value = 'Página não encontrada ou indisponível.';
    console.error(err);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  if (route.params.slug) {
    fetchPage(route.params.slug);
  }
});

// React to route changes if navigating between different pages in the footer
watch(() => route.params.slug, (newSlug) => {
  if (newSlug) {
    fetchPage(newSlug);
  }
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
  margin: 0;
  color: white;
  font-size: 2.5rem;
}

.rich-text-content {
  max-width: 800px;
  margin: 0 auto 5rem;
  font-size: 1.15rem;
  line-height: 1.8;
  color: #333;
}

.loading-state, .error-state {
  text-align: center;
  padding: 4rem 0;
  color: #666;
  font-size: 1.1rem;
}
.error-state { color: #d32f2f; }
</style>
