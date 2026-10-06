import { createRouter, createWebHistory } from 'vue-router';
import { useAuth } from '../store/auth.js';

import PublicLayout from '../layouts/PublicLayout.vue';
import AdminLayout from '../layouts/AdminLayout.vue';

// Public Views
import Home from '../views/public/Home.vue';
import Gallery from '../views/public/Gallery.vue';
import Posts from '../views/public/Posts.vue';
import Contact from '../views/public/Contact.vue';
import Memorial from '../views/public/Memorial.vue';
import MemorialItem from '../views/public/MemorialItem.vue';
import GalleryDetail from '../views/public/GalleryDetail.vue';
import PostDetail from '../views/public/PostDetail.vue';
import PageDetail from '../views/public/PageDetail.vue';

// Admin Views
import Login from '../views/admin/Login.vue';
import Dashboard from '../views/admin/Dashboard.vue';
import GalleriesList from '../views/admin/GalleriesList.vue';
import GalleryEdit from '../views/admin/GalleryEdit.vue';
import PostsList from '../views/admin/PostsList.vue';
import PostForm from '../views/admin/PostForm.vue';
import PagesList from '../views/admin/PagesList.vue';
import PageForm from '../views/admin/PageForm.vue';
import MemorialList from '../views/admin/MemorialList.vue';
import MemorialForm from '../views/admin/MemorialForm.vue';
import ContactsList from '../views/admin/ContactsList.vue';
import UsersList from '../views/admin/UsersList.vue';

const routes = [
  {
    path: '/',
    component: PublicLayout,
    children: [
      { path: '', name: 'Home', component: Home },
      { path: 'galeria', name: 'Gallery', component: Gallery },
      { path: 'galeria/:id', name: 'GalleryDetail', component: GalleryDetail },
      { path: 'memorial', name: 'Memorial', component: Memorial },
      { path: 'memorial/:id', name: 'MemorialItem', component: MemorialItem },
      { path: 'noticias', name: 'Posts', component: Posts },
      { path: 'noticias/:slug', name: 'PostDetail', component: PostDetail },
      { path: 'pagina/:slug', name: 'PageDetail', component: PageDetail },
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
      { path: 'memorial/novo', name: 'NewMemorial', component: MemorialForm },
      { path: 'memorial/:id/editar', name: 'EditMemorial', component: MemorialForm },
      { path: 'galeria', name: 'AdminGallery', component: GalleriesList },
      { path: 'galeria/:id/editar', name: 'EditGallery', component: GalleryEdit },
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
