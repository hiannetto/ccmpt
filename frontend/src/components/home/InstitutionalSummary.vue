<template>
  <section class="institutional-section">
    <div class="container">
      <div class="institutional-card">
        <h2 class="section-title">{{ siteSettings.institutional.title }}</h2>
        <p class="summary-text">{{ siteSettings.institutional.summary }}</p>
        <router-link :to="siteSettings.institutional.ctaLink" class="btn-link">
          {{ siteSettings.institutional.ctaText }} <span class="arrow">&rarr;</span>
        </router-link>
      </div>
    </div>
  </section>
</template>

<script setup>
import { siteSettings } from '../../config/siteSettings';
</script>

<style scoped>
.institutional-section {
  padding: 6rem 0;
  background-color: var(--bg-light);
}

.institutional-card {
  max-width: 800px;
  margin: 0 auto;
  text-align: center;
  background: white;
  padding: 4rem;
  border-radius: 12px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.05);
  border-top: 5px solid var(--color-secondary);
}

.section-title {
  margin-top: 0;
  font-size: 2rem;
  margin-bottom: 1.5rem;
}

.summary-text {
  font-size: 1.15rem;
  line-height: 1.8;
  color: var(--text-secondary);
  margin-bottom: 2rem;
}

.btn-link {
  display: inline-flex;
  align-items: center;
  color: var(--color-primary);
  font-weight: 600;
  text-decoration: none;
  font-size: 1.1rem;
  transition: color 0.3s ease;
}

.btn-link:hover {
  color: var(--color-secondary);
}

.btn-link .arrow {
  margin-left: 0.5rem;
  transition: transform 0.3s ease;
}

.btn-link:hover .arrow {
  transform: translateX(5px);
}

@media (max-width: 768px) {
  .institutional-card {
    padding: 2.5rem 1.5rem;
  }
}
</style>
