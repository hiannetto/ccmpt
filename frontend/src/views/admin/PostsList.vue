<template>
  <div class="list-container">
    <div class="header-actions">
      <h2>Gerenciar Notícias e Eventos</h2>
      <router-link to="/admin/posts/novo" class="btn-primary">Criar Nova Notícia</router-link>
    </div>
    
    <div class="card">
      <p>Em breve: Tabela com a lista de notícias e eventos publicados.</p>
    </div>
  </div>
</template>

<style scoped>
.list-container { max-width: 1000px; margin: 0 auto; }
.header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
h2 { margin: 0; color: var(--color-primary, #0B1C3D); }
.btn-primary { background: var(--color-primary, #0B1C3D); color: white; padding: 0.75rem 1.5rem; text-decoration: none; border-radius: 4px; font-weight: 500; }
.card { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
</style>
