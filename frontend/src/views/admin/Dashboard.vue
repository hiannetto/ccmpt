<template>
  <div>
    <h1>Dashboard</h1>
    <div class="stats-grid">
      <div class="stat-card">
        <h3>Total de Fotos</h3>
        <div class="stat-value">{{ stats.photos }}</div>
      </div>
      <div class="stat-card">
        <h3>Itens no Acervo</h3>
        <div class="stat-value">{{ stats.memorial_items }}</div>
      </div>
      <div class="stat-card">
        <h3>Notícias / Eventos</h3>
        <div class="stat-value">{{ stats.posts }}</div>
      </div>
      <div class="stat-card">
        <h3>Páginas</h3>
        <div class="stat-value">{{ stats.pages }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../api';

const stats = ref({
  photos: 0,
  memorial_items: 0,
  posts: 0,
  pages: 0
});

onMounted(async () => {
  try {
    const res = await api.get('/stats');
    stats.value = res.data;
  } catch (err) {
    console.error('Erro ao carregar estatísticas:', err);
  }
});
</script>

<style scoped>
h1 {
  margin-top: 0;
  margin-bottom: 2rem;
  color: #1e293b;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.5rem;
}

.stat-card {
  background: white;
  padding: 1.5rem;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  border-left: 4px solid var(--color-secondary, #D4AF37);
}

.stat-card h3 {
  margin: 0 0 0.5rem;
  font-size: 1rem;
  color: #64748b;
  font-weight: 500;
}

.stat-value {
  font-size: 2rem;
  font-weight: bold;
  color: var(--color-primary, #0B1C3D);
}
</style>
