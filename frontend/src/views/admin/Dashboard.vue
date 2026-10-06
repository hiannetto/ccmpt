<template>
  <div>
    <h1>Dashboard</h1>
    
    <h2 class="section-title">Visão Geral</h2>
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
        <h3>Notícias</h3>
        <div class="stat-value">{{ stats.news }}</div>
      </div>
      <div class="stat-card">
        <h3>Eventos Futuros</h3>
        <div class="stat-value">{{ stats.events_upcoming }}</div>
      </div>
      <div class="stat-card">
        <h3>Páginas Estáticas</h3>
        <div class="stat-value">{{ stats.pages }}</div>
      </div>
    </div>

    <h2 class="section-title">Contatos e Mensagens</h2>
    <div class="stats-grid">
      <div class="stat-card contacts">
        <h3>Contatos dos últimos 7 dias</h3>
        <div class="stat-value">{{ stats.contacts_last_7_days }}</div>
      </div>
      <div class="stat-card contacts unread">
        <h3>Mensagens não lidas</h3>
        <div class="stat-value">{{ stats.contacts_unread }}</div>
      </div>
      <div class="stat-card contacts replied">
        <h3>Mensagens respondidas</h3>
        <div class="stat-value">{{ stats.contacts_replied }}</div>
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
  news: 0,
  events_upcoming: 0,
  pages: 0,
  contacts_last_7_days: 0,
  contacts_unread: 0,
  contacts_replied: 0
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

.section-title {
  font-size: 1.25rem;
  color: #475569;
  margin: 2rem 0 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid #e2e8f0;
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

.stat-card.contacts { border-left-color: #3b82f6; }
.stat-card.contacts.unread { border-left-color: #ef4444; }
.stat-card.contacts.unread .stat-value { color: #ef4444; }
.stat-card.contacts.replied { border-left-color: #10b981; }
</style>
