<template>
  <div class="public-layout">
    <header class="main-header">
      <div class="container header-container">
        <div class="logo">
          <!-- Placeholder para logo -->
          <h1>Centro Cultural e Memorial Padre Tiago</h1>
        </div>
        <nav class="main-nav">
          <router-link to="/">Início</router-link>
          <router-link to="/memorial">Acervo Histórico</router-link>
          <router-link to="/galeria">Galeria de Fotos</router-link>
          <router-link to="/noticias">Notícias</router-link>
          <router-link to="/contato">Contato</router-link>
          <router-link to="/admin/login" class="login-link">Restrito</router-link>
        </nav>
      </div>
    </header>

    <main class="main-content">
      <router-view></router-view>
    </main>

    <footer class="main-footer">
      <div class="container footer-content">
        
        <!-- Coluna 1: Sobre / Institucional -->
        <div class="footer-col">
          <h3>Sobre o Centro Cultural</h3>
          <p>O Centro Cultural e Memorial Padre Tiago é o coração vivo da comunidade em Muriaé. Nascido para perpetuar o legado de amor e ação social do Padre Tiago, a instituição é um polo vibrante de cidadania, arte e acolhimento.</p>
        </div>

        <!-- Coluna 2: Menu Institucional (Dinâmico) -->
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
          <p>&copy; {{ new Date().getFullYear() }} Centro Cultural e Memorial Padre Tiago. Todos os direitos reservados.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../api';

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
.public-layout {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.main-header {
  background-color: var(--color-primary, #0B1C3D); /* Navy Blue */
  color: white;
  padding: 1rem 0;
  border-bottom: 4px solid var(--color-secondary, #D4AF37); /* Gold */
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
}

.header-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
}

.logo h1 {
  font-size: 1.5rem;
  margin: 0;
  font-weight: 600;
  color: #fff;
}

.main-nav {
  display: flex;
  gap: 1.5rem;
  align-items: center;
}

.main-nav a {
  color: white;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.3s ease;
}

.main-nav a:hover, .main-nav a.router-link-active {
  color: var(--color-secondary, #D4AF37);
}

.login-link {
  font-size: 0.85rem;
  opacity: 0.7;
}

.main-content {
  flex: 1;
  background-color: #f8f9fa; /* Off-white / light gray */
}

/* FOOTER MODERNIZADO */
.main-footer {
  background-color: var(--color-primary, #0B1C3D);
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
  color: var(--color-secondary, #D4AF37);
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
  background-color: var(--color-secondary, #D4AF37);
}

.footer-col p {
  color: #dee2e6;
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
  color: #dee2e6;
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
  background-color: rgba(0, 0, 0, 0.2);
  padding: 1.5rem 0;
  text-align: center;
  font-size: 0.9rem;
  color: #adb5bd;
}

.footer-bottom p {
  margin: 0;
}

@media (max-width: 768px) {
  .header-container {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }
  
  .main-nav {
    flex-wrap: wrap;
    justify-content: center;
  }
}
</style>
