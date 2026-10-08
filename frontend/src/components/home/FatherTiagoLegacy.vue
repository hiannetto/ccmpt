<template>
  <section class="legacy-section" :style="{ backgroundImage: `linear-gradient(rgba(10, 17, 40, 0.85), rgba(10, 17, 40, 0.95)), url(${siteSettings.legacy.backgroundImage})` }">
    <div class="container">
      <div class="legacy-content">
        <h2 class="section-title">{{ siteSettings.legacy.title }}</h2>
        <div class="quote-mark">"</div>
        <p class="quote">{{ siteSettings.legacy.quote }}</p>
        <p class="description">{{ siteSettings.legacy.description }}</p>
        <router-link :to="siteSettings.legacy.ctaLink" class="btn btn-outline-gold">
          {{ siteSettings.legacy.ctaText }}
        </router-link>
      </div>
    </div>
  </section>
</template>

<script setup>
import { siteSettings } from '../../config/siteSettings';
</script>

<style scoped>
.legacy-section {
  padding: 6rem 0;
  background-size: cover;
  background-position: center;
  background-attachment: fixed;
  color: white;
  text-align: center;
}

.legacy-content {
  max-width: 800px;
  margin: 0 auto;
}

.section-title {
  color: var(--color-secondary);
  font-size: 2rem;
  margin-bottom: 2rem;
  text-transform: uppercase;
  letter-spacing: 2px;
}

.quote-mark {
  font-size: 6rem;
  line-height: 1;
  color: var(--color-secondary);
  opacity: 0.5;
  font-family: serif;
  margin-bottom: -2rem;
}

.quote {
  font-size: 2.5rem;
  font-weight: 300;
  font-style: italic;
  margin-bottom: 2rem;
  line-height: 1.2;
}

.description {
  font-size: 1.2rem;
  color: #e2e8f0;
  margin-bottom: 3rem;
  line-height: 1.8;
}

.btn-outline-gold {
  display: inline-block;
  padding: 1rem 2.5rem;
  background: transparent;
  color: var(--color-secondary);
  border: 2px solid var(--color-secondary);
  border-radius: 4px;
  font-weight: 600;
  font-size: 1.1rem;
  text-decoration: none;
  transition: all 0.3s ease;
}

.btn-outline-gold:hover {
  background: var(--color-secondary);
  color: var(--color-primary);
}

@media (max-width: 768px) {
  .quote {
    font-size: 1.8rem;
  }
}
</style>
