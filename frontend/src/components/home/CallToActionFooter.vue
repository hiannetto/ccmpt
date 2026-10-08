<template>
  <section class="cta-section">
    <div class="container cta-container">
      <div class="cta-content">
        <h2>{{ siteSettings.ctaFooter.title }}</h2>
        <p>{{ siteSettings.ctaFooter.subtitle }}</p>
      </div>
      <div class="cta-action">
        <router-link :to="siteSettings.ctaFooter.ctaLink" class="btn btn-primary btn-large">
          {{ siteSettings.ctaFooter.ctaText }}
        </router-link>
      </div>
    </div>
  </section>
</template>

<script setup>
import { siteSettings } from '../../config/siteSettings';
</script>

<style scoped>
.cta-section {
  background-color: var(--color-secondary);
  padding: 4rem 0;
  color: var(--color-primary);
}

.cta-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 2rem;
}

.cta-content {
  flex: 1;
  min-width: 300px;
}

.cta-content h2 {
  color: var(--color-primary);
  font-size: 2.2rem;
  margin-top: 0;
  margin-bottom: 0.5rem;
}

.cta-content p {
  font-size: 1.2rem;
  margin: 0;
  opacity: 0.9;
  font-weight: 500;
}

.btn-primary {
  background-color: var(--color-primary);
  color: white;
  padding: 1rem 2.5rem;
  font-size: 1.1rem;
  font-weight: 700;
  border-radius: 4px;
  text-decoration: none;
  display: inline-block;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.btn-primary:hover {
  background-color: #15274d; /* slightly lighter navy */
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(0,0,0,0.15);
  color: white;
}

@media (max-width: 768px) {
  .cta-container {
    flex-direction: column;
    text-align: center;
  }
}
</style>
