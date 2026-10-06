<template>
  <div class="public-layout">
    <header class="main-header">
      <div class="container">
        <div class="logo">
          <!-- Placeholder para logo -->
          <h1>Centro Cultural e Memorial Padre Tiago</h1>
        </div>
        <nav class="main-nav">
          <router-link to="/">Início</router-link>
          <router-link to="/galeria">Acervo e Galeria</router-link>
          <router-link to="/noticias">Notícias</router-link>
          <router-link to="/contato">Contato</router-link>
          <router-link to="/admin/login" class="login-link">Restrito</router-link>
        </nav>
      </div>
    </header>

    <main class="main-content">
      <router-view></router-view>
    </main>

    <footer class="main-footer">
      <div class="container">
        <p>&copy; {{ new Date().getFullYear() }} Centro Cultural e Memorial Padre Tiago. Todos os direitos reservados.</p>
      </div>
    </footer>
  </div>
</template>

<style scoped>
.public-layout {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.main-header {
  background-color: var(--color-primary, #0B1C3D); /* Navy Blue */
  color: white;
  padding: 1rem 0;
  border-bottom: 4px solid var(--color-secondary, #D4AF37); /* Gold */
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
}

.logo h1 {
  font-size: 1.5rem;
  margin: 0;
  font-weight: 600;
  color: #fff;
}

.main-nav {
  display: flex;
  gap: 1.5rem;
  align-items: center;
}

.main-nav a {
  color: white;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.3s ease;
}

.main-nav a:hover, .main-nav a.router-link-active {
  color: var(--color-secondary, #D4AF37);
}

.login-link {
  font-size: 0.85rem;
  opacity: 0.7;
}

.main-content {
  flex: 1;
  background-color: #f8f9fa; /* Off-white / light gray */
}

.main-footer {
  background-color: var(--color-primary, #0B1C3D);
  color: white;
  text-align: center;
  padding: 1.5rem 0;
  font-size: 0.9rem;
}
</style>
