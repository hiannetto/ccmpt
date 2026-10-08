<template>
  <header class="main-header">
    <div class="container header-container">
      <div class="logo">
        <router-link to="/">
          <img v-if="siteSettings.general.logoUrl" :src="siteSettings.general.logoUrl" :alt="siteSettings.general.siteName" class="logo-img" />
          <h1 v-else>{{ siteSettings.general.siteName }}</h1>
        </router-link>
      </div>
      
      <!-- Desktop Navigation -->
      <nav class="main-nav desktop-only">
        <router-link to="/">Início</router-link>
        <router-link to="/memorial">Acervo Histórico</router-link>
        <router-link to="/galeria">Galeria de Fotos</router-link>
        <router-link to="/noticias">Notícias e Eventos</router-link>
        <router-link to="/contato">Contato</router-link>
      </nav>

      <!-- Mobile Hamburger -->
      <button class="mobile-toggle" @click="toggleMenu" aria-label="Abrir Menu">
        <span class="hamburger" :class="{ 'is-active': isMenuOpen }"></span>
      </button>

      <!-- Mobile Drawer -->
      <div class="mobile-drawer" :class="{ 'is-open': isMenuOpen }">
        <div class="drawer-header">
          <button class="close-btn" @click="toggleMenu" aria-label="Fechar Menu">&times;</button>
        </div>
        <nav class="mobile-nav">
          <router-link to="/" @click="toggleMenu">Início</router-link>
          <router-link to="/memorial" @click="toggleMenu">Acervo Histórico</router-link>
          <router-link to="/galeria" @click="toggleMenu">Galeria de Fotos</router-link>
          <router-link to="/noticias" @click="toggleMenu">Notícias</router-link>
          <router-link to="/contato" @click="toggleMenu">Contato</router-link>
        </nav>
      </div>
      <!-- Overlay -->
      <div class="drawer-overlay" v-if="isMenuOpen" @click="toggleMenu"></div>
    </div>
  </header>
</template>

<script setup>
import { ref } from 'vue';
import { siteSettings } from '../../config/siteSettings';

const isMenuOpen = ref(false);
const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value;
  // Prevenir scroll do body quando menu aberto
  document.body.style.overflow = isMenuOpen.value ? 'hidden' : '';
};
</script>

<style scoped>
.main-header {
  background-color: var(--color-primary);
  color: white;
  padding: 1rem 0;
  border-bottom: 4px solid var(--color-secondary);
  position: relative;
  z-index: 100;
}

.header-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.logo a {
  display: flex;
  align-items: center;
  text-decoration: none;
  color: white;
}

.logo-img {
  max-height: 50px;
  object-fit: contain;
}

.logo h1 {
  font-size: 1.25rem;
  margin: 0;
  color: white;
}

.main-nav {
  display: flex;
  gap: 1.25rem;
  align-items: center;
}

.main-nav a {
  color: #e2e8f0;
  text-decoration: none;
  font-weight: 500;
  font-size: 0.95rem;
  transition: color 0.3s ease;
}

.main-nav a:hover, .main-nav a.router-link-active {
  color: var(--color-secondary);
}

.mobile-toggle {
  display: none;
  background: none;
  border: none;
  cursor: pointer;
  padding: 0.5rem;
}

.hamburger {
  display: block;
  width: 24px;
  height: 2px;
  background-color: white;
  position: relative;
  transition: all 0.3s;
}

.hamburger::before, .hamburger::after {
  content: '';
  position: absolute;
  width: 24px;
  height: 2px;
  background-color: white;
  left: 0;
  transition: all 0.3s;
}

.hamburger::before { top: -6px; }
.hamburger::after { bottom: -6px; }

.hamburger.is-active { background-color: transparent; }
.hamburger.is-active::before { top: 0; transform: rotate(45deg); }
.hamburger.is-active::after { bottom: 0; transform: rotate(-45deg); }

.mobile-drawer {
  position: fixed;
  top: 0;
  right: -100%;
  width: 80%;
  max-width: 300px;
  height: 100vh;
  background-color: var(--color-primary);
  z-index: 200;
  transition: right 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: -5px 0 15px rgba(0,0,0,0.5);
  display: flex;
  flex-direction: column;
}

.mobile-drawer.is-open {
  right: 0;
}

.drawer-header {
  padding: 1.5rem;
  display: flex;
  justify-content: flex-end;
}

.close-btn {
  background: none;
  border: none;
  color: white;
  font-size: 2rem;
  cursor: pointer;
  line-height: 1;
}

.mobile-nav {
  display: flex;
  flex-direction: column;
  padding: 0 1.5rem;
}

.mobile-nav a {
  color: white;
  text-decoration: none;
  padding: 1rem 0;
  border-bottom: 1px solid rgba(255,255,255,0.1);
  font-size: 1.1rem;
}

.mobile-nav a:hover, .mobile-nav a.router-link-active {
  color: var(--color-secondary);
}

.drawer-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background-color: rgba(0,0,0,0.5);
  z-index: 150;
}

@media (max-width: 1024px) {
  .desktop-only { display: none; }
  .mobile-toggle { display: block; }
}
</style>
