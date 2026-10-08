<template>
  <router-view></router-view>
</template>

<script setup>
import { watchEffect } from 'vue';
import { siteSettings } from './config/siteSettings';

watchEffect(() => {
  let link = document.querySelector("link[rel~='icon']");
  if (!link) {
    link = document.createElement('link');
    link.rel = 'icon';
    document.getElementsByTagName('head')[0].appendChild(link);
  }
  link.href = siteSettings.general.faviconUrl;
});
</script>

<style>
/* Global styles in style.css */
</style>
