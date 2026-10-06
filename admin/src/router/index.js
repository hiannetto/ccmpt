import { createRouter, createWebHistory } from 'vue-router';

// Rascunho das rotas
const routes = [
  {
    path: '/login',
    name: 'Login',
    component: () => import('../views/Login.vue'),
    meta: { guest: true }
  },
  {
    path: '/',
    name: 'Dashboard',
    component: () => import('../views/Dashboard.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/posts',
    name: 'Posts',
    component: () => import('../views/Posts.vue'),
    meta: { requiresAuth: true } // Administrador e Editor acessam
  },
  {
    path: '/memorial',
    name: 'Memorial',
    component: () => import('../views/Memorial.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/pages',
    name: 'Pages',
    component: () => import('../views/Pages.vue'),
    meta: { requiresAuth: true, role: 'admin' } // Apenas Admin
  },
  {
    path: '/contacts',
    name: 'Contacts',
    component: () => import('../views/Contacts.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/galleries/upload',
    name: 'GalleryUpload',
    component: () => import('../views/GalleryUpload.vue'),
    meta: { requiresAuth: true }
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

// Route Guards: Bloqueio de não autenticados e checagem de nível de acesso
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token');
  // Em uma app real, o usuário estaria em Vuex/Pinia ou decodificado do JWT
  const userRole = localStorage.getItem('role') || 'editor'; 

  // Se a rota exige autenticação e não há token
  if (to.matched.some(record => record.meta.requiresAuth)) {
    if (!token) {
      return next({ name: 'Login' });
    }
    
    // Se a rota exige um perfil específico (ex: admin)
    if (to.meta.role && to.meta.role !== userRole) {
      // Redireciona para um 'Não autorizado' ou Dashboard
      return next({ name: 'Dashboard' }); 
    }
  }

  // Se o usuário está logado e tenta acessar o Login, vai pro Dashboard
  if (to.matched.some(record => record.meta.guest)) {
    if (token) {
      return next({ name: 'Dashboard' });
    }
  }

  next();
});

export default router;
