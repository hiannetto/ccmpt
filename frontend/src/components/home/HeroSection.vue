<template>
  <section class="hero-section" :style="{ backgroundImage: `linear-gradient(rgba(10, 17, 40, 0.7), rgba(10, 17, 40, 0.8)), url(${siteSettings.hero.backgroundImage})` }">
    <div class="container hero-content">
      <h1 class="hero-title">{{ siteSettings.hero.title }}</h1>
      <p class="hero-subtitle">{{ siteSettings.hero.subtitle }}</p>
      <div class="hero-actions">
        <router-link :to="siteSettings.hero.cta1Link" class="btn btn-primary btn-large">
          {{ siteSettings.hero.cta1Text }}
        </router-link>
        <router-link :to="siteSettings.hero.cta2Link" class="btn btn-outline btn-large">
          {{ siteSettings.hero.cta2Text }}
        </router-link>
      </div>
    </div>
  </section>
</template>

<script setup>
import { siteSettings } from '../../config/siteSettings';
</script>

<style scoped>
.hero-section {
  min-height: 80vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background-size: cover;
  background-position: center;
  color: white;
  text-align: center;
  padding: 4rem 2rem;
}

.hero-content {
  max-width: 800px;
  animation: fadeIn 1s ease-out forwards;
}

.hero-title {
  font-size: clamp(2.5rem, 5vw, 4rem);
  font-weight: 700;
  color: white;
  margin-bottom: 1.5rem;
  line-height: 1.1;
  text-shadow: 0 4px 10px rgba(0,0,0,0.5);
}

.hero-subtitle {
  font-size: clamp(1.1rem, 2vw, 1.5rem);
  color: #f8f9fa;
  margin-bottom: 2.5rem;
  line-height: 1.5;
  text-shadow: 0 2px 5px rgba(0,0,0,0.5);
}

.hero-actions {
  display: flex;
  gap: 1.5rem;
  justify-content: center;
  flex-wrap: wrap;
}

.btn-large {
  padding: 1rem 2rem;
  font-size: 1.1rem;
}

.btn-primary {
  background-color: var(--color-secondary);
  color: var(--color-primary);
  border: 2px solid var(--color-secondary);
}

.btn-primary:hover {
  background-color: #f5cc47; /* lighter gold */
  border-color: #f5cc47;
  color: var(--color-primary);
}

.btn-outline {
  background-color: transparent;
  border: 2px solid white;
  color: white;
  padding: 1rem 2rem;
  border-radius: 4px;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.3s ease;
}

.btn-outline:hover {
  background-color: white;
  color: var(--color-primary);
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

@media (max-width: 600px) {
  .hero-actions {
    flex-direction: column;
    width: 100%;
  }
  .btn-large {
    width: 100%;
  }
}
</style>
