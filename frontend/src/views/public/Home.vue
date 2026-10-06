<template>
  <div class="home">
    <section class="hero">
      <div class="container">
        <h2>Preservando a História e a Fé</h2>
        <p>Conheça o legado do Padre Tiago e os projetos sociais mantidos pela instituição.</p>
        <div class="actions">
          <router-link to="/galeria" class="btn">Explorar o Acervo</router-link>
          <router-link to="/noticias" class="btn btn-outline">Últimas Notícias</router-link>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped>
.hero {
  background-color: var(--color-primary, #0B1C3D);
  color: white;
  padding: 4rem 2rem;
  text-align: center;
  /* background-image: url('/placeholder-hero.jpg'); */
  background-size: cover;
  background-position: center;
}
.hero h2 {
  font-size: 2.5rem;
  margin-bottom: 1rem;
  color: var(--color-secondary, #D4AF37);
}
.hero p {
  font-size: 1.2rem;
  max-width: 600px;
  margin: 0 auto 2rem;
  opacity: 0.9;
}
.actions {
  display: flex;
  gap: 1rem;
  justify-content: center;
}
.btn {
  padding: 0.75rem 1.5rem;
  background-color: var(--color-secondary, #D4AF37);
  color: var(--color-primary, #0B1C3D);
  text-decoration: none;
  font-weight: bold;
  border-radius: 4px;
}
.btn-outline {
  background-color: transparent;
  border: 2px solid var(--color-secondary, #D4AF37);
  color: var(--color-secondary, #D4AF37);
}
</style>
