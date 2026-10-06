import { createRouter, createWebHistory } from 'vue-router';
import { useAuth } from '../store/auth.js';

import PublicLayout from '../layouts/PublicLayout.vue';
import AdminLayout from '../layouts/AdminLayout.vue';

// Public Views
import Home from '../views/public/Home.vue';
import Gallery from '../views/public/Gallery.vue';
import Posts from '../views/public/Posts.vue';
import Contact from '../views/public/Contact.vue';

// Admin Views
import Login from '../views/admin/Login.vue';
import Dashboard from '../views/admin/Dashboard.vue';
import GalleryUpload from '../views/admin/GalleryUpload.vue';
import PostsList from '../views/admin/PostsList.vue';
import PostForm from '../views/admin/PostForm.vue';
import PagesList from '../views/admin/PagesList.vue';
import PageForm from '../views/admin/PageForm.vue';
import MemorialList from '../views/admin/MemorialList.vue';
import ContactsList from '../views/admin/ContactsList.vue';
import UsersList from '../views/admin/UsersList.vue';

const routes = [
  {
    path: '/',
    component: PublicLayout,
    children: [
      { path: '', name: 'Home', component: Home },
      { path: 'galeria', name: 'Gallery', component: Gallery },
      { path: 'noticias', name: 'Posts', component: Posts },
      { path: 'contato', name: 'Contact', component: Contact },
    ]
  },
  {
    path: '/admin/login',
    name: 'Login',
    component: Login,
    meta: { requiresGuest: true }
  },
  {
    path: '/admin',
    component: AdminLayout,
    meta: { requiresAuth: true },
    children: [
      { path: '', name: 'Dashboard', component: Dashboard },
      { path: 'memorial', name: 'AdminMemorial', component: MemorialList },
      { path: 'galeria', name: 'AdminGallery', component: GalleryUpload },
      { path: 'posts', name: 'PostsList', component: PostsList },
      { path: 'posts/novo', name: 'NewPost', component: PostForm },
      { path: 'paginas', name: 'PagesList', component: PagesList },
      { path: 'paginas/nova', name: 'NewPage', component: PageForm },
      { path: 'contatos', name: 'ContactsList', component: ContactsList },
      { path: 'usuarios', name: 'UsersList', component: UsersList },
      // Outras rotas serão adicionadas conforme formos implementando
    ]
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

router.beforeEach((to, from) => {
  const { isAuthenticated } = useAuth();
  
  if (to.meta.requiresAuth && !isAuthenticated()) {
    return { name: 'Login' };
  } else if (to.meta.requiresGuest && isAuthenticated()) {
    return { name: 'Dashboard' };
  }
});

export default router;
