<template>
  <section class="projects-section">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">{{ siteSettings.projects.title }}</h2>
        <p class="section-subtitle">{{ siteSettings.projects.subtitle }}</p>
      </div>

      <div class="projects-grid">
        <div v-for="item in siteSettings.projects.items" :key="item.id" class="project-card">
          <div class="icon-wrapper">
            <component :is="getIcon(item.icon)" :size="40" weight="light" />
          </div>
          <h3 class="project-title">{{ item.title }}</h3>
          <p class="project-desc">{{ item.description }}</p>
        </div>
      </div>

      <div class="section-actions">
        <router-link :to="siteSettings.projects.ctaLink" class="btn btn-primary">
          {{ siteSettings.projects.ctaText }}
        </router-link>
      </div>
    </div>
  </section>
</template>

<script setup>
import { siteSettings } from '../../config/siteSettings';
import { 
  PhMusicNotes, 
  PhCrown, 
  PhPersonArmsSpread, 
  PhSneaker, 
  PhSoccerBall, 
  PhBookOpenText,
  PhStar
} from '@phosphor-icons/vue';

// Mapa de ícones
const iconMap = {
  'MusicNotes': PhMusicNotes,
  'Crown': PhCrown,
  'PersonArmsSpread': PhPersonArmsSpread,
  'Sneaker': PhSneaker,
  'SoccerBall': PhSoccerBall,
  'BookOpenText': PhBookOpenText
};

const getIcon = (iconName) => {
  return iconMap[iconName] || PhStar;
};
</script>

<style scoped>
.projects-section {
  padding: 6rem 0;
  background-color: white;
}

.section-header {
  text-align: center;
  margin-bottom: 4rem;
}

.section-title {
  font-size: 2.2rem;
  margin-bottom: 1rem;
}

.section-subtitle {
  font-size: 1.1rem;
  color: var(--text-secondary);
  max-width: 600px;
  margin: 0 auto;
}

.projects-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 2rem;
  margin-bottom: 4rem;
}

.project-card {
  background-color: var(--bg-light);
  border-radius: 12px;
  padding: 2.5rem 1.5rem;
  text-align: center;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  border: 1px solid #edf2f7;
}

.project-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 30px rgba(10, 17, 40, 0.08);
  border-color: var(--color-secondary);
}

.icon-wrapper {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background-color: rgba(212, 175, 55, 0.1); /* Light gold */
  color: var(--color-secondary);
  margin-bottom: 1.5rem;
  transition: all 0.3s ease;
}

.project-card:hover .icon-wrapper {
  background-color: var(--color-secondary);
  color: var(--color-primary);
}

.project-title {
  font-size: 1.3rem;
  margin-bottom: 0.75rem;
  color: var(--color-primary);
}

.project-desc {
  color: var(--text-secondary);
  line-height: 1.5;
  font-size: 0.95rem;
}

.section-actions {
  text-align: center;
}

.btn-primary {
  padding: 1rem 2rem;
  font-size: 1.1rem;
  display: inline-block;
  text-decoration: none;
}
</style>
