<template>
  <div class="admin-layout">
    <aside class="sidebar">
      <div class="sidebar-header">
        <h2>Backoffice</h2>
        <p>Memorial Padre Tiago</p>
      </div>
      <nav class="sidebar-nav">
        <router-link to="/admin">Dashboard</router-link>
        <router-link to="/admin/memorial">Acervo Histórico</router-link>
        <router-link to="/admin/galeria">Arquivo Fotográfico</router-link>
        <router-link to="/admin/posts">Notícias e Eventos</router-link>
        <router-link to="/admin/paginas" v-if="hasRole('admin')">Páginas</router-link>
        <router-link to="/admin/contatos">Mensagens (Contato)</router-link>
        <router-link to="/admin/usuarios" v-if="hasRole('admin')">Usuários</router-link>
      </nav>
      <div class="sidebar-footer">
        <button @click="doLogout" class="btn-logout">Sair</button>
      </div>
    </aside>

    <main class="admin-main">
      <header class="admin-topbar">
        <div class="user-info">
          Bem-vindo, {{ auth.state.user?.name }}
        </div>
        <div>
          <router-link to="/" class="view-site">Ver Site</router-link>
        </div>
      </header>
      <div class="admin-content">
        <router-view></router-view>
      </div>
    </main>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { useAuth } from '../store/auth';

const router = useRouter();
const auth = useAuth();
const { logout, hasRole } = auth;

const doLogout = () => {
  logout();
  router.push('/admin/login');
};
</script>

<style scoped>
.admin-layout {
  display: flex;
  min-height: 100vh;
  background-color: #f4f6f8;
}

.sidebar {
  width: 260px;
  background-color: var(--color-primary, #0B1C3D);
  color: white;
  display: flex;
  flex-direction: column;
}

.sidebar-header {
  padding: 1.5rem;
  border-bottom: 1px solid rgba(255,255,255,0.1);
}

.sidebar-header h2 {
  margin: 0;
  font-size: 1.25rem;
  color: var(--color-secondary, #D4AF37);
}

.sidebar-header p {
  margin: 0.25rem 0 0;
  font-size: 0.85rem;
  opacity: 0.8;
}

.sidebar-nav {
  flex: 1;
  padding: 1.5rem 0;
  display: flex;
  flex-direction: column;
}

.sidebar-nav a {
  padding: 0.75rem 1.5rem;
  color: #e2e8f0;
  text-decoration: none;
  transition: all 0.2s;
  display: flex;
  align-items: center;
}

.sidebar-nav a:hover, .sidebar-nav a.router-link-active {
  background-color: rgba(255,255,255,0.1);
  color: white;
  border-left: 4px solid var(--color-secondary, #D4AF37);
}

.sidebar-footer {
  padding: 1.5rem;
  border-top: 1px solid rgba(255,255,255,0.1);
}

.btn-logout {
  width: 100%;
  padding: 0.75rem;
  background-color: transparent;
  color: #fca5a5;
  border: 1px solid #fca5a5;
  border-radius: 4px;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-logout:hover {
  background-color: #fca5a5;
  color: #7f1d1d;
}

.admin-main {
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.admin-topbar {
  background-color: white;
  padding: 1rem 2rem;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.user-info {
  font-weight: 500;
  color: #334155;
}

.view-site {
  color: var(--color-primary, #0B1C3D);
  text-decoration: none;
  font-weight: 500;
}

.view-site:hover {
  text-decoration: underline;
}

.admin-content {
  flex: 1;
  padding: 2rem;
  overflow-y: auto;
}
</style>
