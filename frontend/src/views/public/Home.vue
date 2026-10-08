<template>
  <div class="home">
    <HeroSection />
    <InstitutionalSummary />
    <ProjectsHighlight />
    <FatherTiagoLegacy />
    <LatestNews />
    <PhotoGalleryPreview />
    <CallToActionFooter />
  </div>
</template>

<script setup>
import HeroSection from '../../components/home/HeroSection.vue';
import InstitutionalSummary from '../../components/home/InstitutionalSummary.vue';
import ProjectsHighlight from '../../components/home/ProjectsHighlight.vue';
import FatherTiagoLegacy from '../../components/home/FatherTiagoLegacy.vue';
import LatestNews from '../../components/home/LatestNews.vue';
import PhotoGalleryPreview from '../../components/home/PhotoGalleryPreview.vue';
import CallToActionFooter from '../../components/home/CallToActionFooter.vue';
</script>
