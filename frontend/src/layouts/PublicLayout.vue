<template>
  <div class="public-layout">
    <AppHeader />

    <main class="main-content">
      <router-view></router-view>
    </main>

    <AppFooter />
  </div>
</template>

<script setup>
import AppHeader from '../components/layout/Header.vue';
import AppFooter from '../components/layout/Footer.vue';
</script>

<style scoped>
.public-layout {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.main-content {
  flex: 1;
  background-color: var(--bg-light);
}
</style>
