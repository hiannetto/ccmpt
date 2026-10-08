<template>
  <footer class="main-footer">
    <div class="container footer-content">
      
      <!-- Coluna 1: Sobre / Institucional -->
      <div class="footer-col">
        <h3>{{ siteSettings.footer.aboutTitle }}</h3>
        <p>{{ siteSettings.footer.aboutText }}</p>
      </div>

      <!-- Coluna 2: Páginas Institucionais (Dinâmico) -->
      <div class="footer-col">
        <h3>Páginas Institucionais</h3>
        <ul class="footer-nav">
          <li v-for="page in pages" :key="page.id">
            <router-link :to="{ name: 'PageDetail', params: { slug: page.slug } }">
              &rsaquo; {{ page.title }}
            </router-link>
          </li>
          <li v-if="pages.length === 0 && !loadingPages">
            <span style="opacity: 0.6">Nenhuma página disponível</span>
          </li>
        </ul>
      </div>

      <!-- Coluna 3: Links Rápidos -->
      <div class="footer-col">
        <h3>Acesso Rápido</h3>
        <ul class="footer-nav">
          <li><router-link to="/memorial">&rsaquo; Acervo Histórico</router-link></li>
          <li><router-link to="/galeria">&rsaquo; Galeria de Fotos</router-link></li>
          <li><router-link to="/noticias">&rsaquo; Notícias e Eventos</router-link></li>
          <li><router-link to="/contato">&rsaquo; Fale Conosco</router-link></li>
        </ul>
      </div>
    </div>

    <div class="footer-bottom">
      <div class="container">
        <p>&copy; {{ new Date().getFullYear() }} {{ siteSettings.general.siteName }}. Todos os direitos reservados.</p>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { siteSettings } from '../../config/siteSettings';
import api from '../../api';

const pages = ref([]);
const loadingPages = ref(true);

onMounted(async () => {
  try {
    const res = await api.get('/pages');
    // Pegamos todas as páginas públicas (o backend já filtra as publicadas para visitantes)
    pages.value = Array.isArray(res.data) ? res.data : [];
  } catch (err) {
    console.error('Erro ao buscar páginas institucionais para o rodapé:', err);
  } finally {
    loadingPages.value = false;
  }
});
</script>

<style scoped>
.main-footer {
  background-color: var(--color-primary);
  color: white;
  padding-top: 4rem;
}

.footer-content {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 3rem;
  margin-bottom: 3rem;
}

.footer-col h3 {
  color: var(--color-secondary);
  font-size: 1.25rem;
  margin-top: 0;
  margin-bottom: 1.5rem;
  position: relative;
  padding-bottom: 0.5rem;
}

.footer-col h3::after {
  content: '';
  position: absolute;
  left: 0;
  bottom: 0;
  width: 50px;
  height: 3px;
  background-color: var(--color-secondary);
}

.footer-col p {
  color: var(--text-gray, #e2e8f0);
  line-height: 1.6;
  font-size: 0.95rem;
}

.footer-nav {
  list-style: none;
  padding: 0;
  margin: 0;
}

.footer-nav li {
  margin-bottom: 0.75rem;
}

.footer-nav a {
  color: var(--text-gray, #e2e8f0);
  text-decoration: none;
  font-size: 0.95rem;
  transition: all 0.3s ease;
  display: inline-block;
}

.footer-nav a:hover {
  color: white;
  transform: translateX(5px);
}

.footer-bottom {
  background-color: rgba(0, 0, 0, 0.3);
  padding: 1.5rem 0;
  text-align: center;
  font-size: 0.9rem;
  color: rgba(255,255,255,0.7);
}

.footer-bottom p {
  margin: 0;
}
</style>
