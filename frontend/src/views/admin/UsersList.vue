<template>
  <div class="list-container">
    <div class="header-actions">
      <h2>Gestão de Usuários</h2>
      <button class="btn-primary" @click="openModal()">Novo Usuário</button>
    </div>

    <div v-if="loading" class="loading-state">Carregando usuários...</div>
    <div v-else-if="error" class="alert error">{{ error }}</div>
    
    <div v-else class="card">
      <table class="data-table">
        <thead>
          <tr>
            <th>Nome</th>
            <th>E-mail</th>
            <th>Perfil</th>
            <th class="actions-col">Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users" :key="user.id">
            <td>{{ user.name }}</td>
            <td>{{ user.email }}</td>
            <td>
              <span :class="['role-badge', user.role]">{{ user.role === 'admin' ? 'Administrador' : 'Editor' }}</span>
            </td>
            <td class="actions-col">
              <button class="btn-icon" @click="openModal(user)" title="Editar">✏️</button>
              <button class="btn-icon text-danger" @click="deleteUser(user)" :disabled="user.id === currentUser.id" title="Excluir">🗑️</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal de Formulário -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal-content">
        <h3>{{ form.id ? 'Editar Usuário' : 'Novo Usuário' }}</h3>
        
        <form @submit.prevent="saveUser">
          <div v-if="formError" class="alert error">{{ formError }}</div>
          
          <div class="form-group">
            <label>Nome</label>
            <input type="text" v-model="form.name" required />
          </div>
          
          <div class="form-group">
            <label>E-mail</label>
            <input type="email" v-model="form.email" required />
          </div>
          
          <div class="form-group">
            <label>Perfil</label>
            <select v-model="form.role" required>
              <option value="editor">Editor (Restrito)</option>
              <option value="admin">Administrador (Total)</option>
            </select>
          </div>
          
          <div class="form-group">
            <label>{{ form.id ? 'Nova Senha (deixe em branco para manter)' : 'Senha' }}</label>
            <input type="password" v-model="form.password" :required="!form.id" minlength="8" />
          </div>
          
          <div class="modal-actions">
            <button type="button" class="btn-outline" @click="closeModal">Cancelar</button>
            <button type="submit" class="btn-primary" :disabled="saving">
              {{ saving ? 'Salvando...' : 'Salvar' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../api';
import { useAuth } from '../../store/auth';

const auth = useAuth();
const currentUser = auth.state.user;

const users = ref([]);
const loading = ref(true);
const error = ref('');

const showModal = ref(false);
const saving = ref(false);
const formError = ref('');

const form = ref({
  id: null,
  name: '',
  email: '',
  role: 'editor',
  password: ''
});

const loadUsers = async () => {
  try {
    loading.value = true;
    const res = await api.get('/users');
    users.value = res.data;
  } catch (err) {
    error.value = 'Erro ao carregar usuários.';
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  loadUsers();
});

const openModal = (user = null) => {
  formError.value = '';
  if (user) {
    form.value = { ...user, password: '' };
  } else {
    form.value = { id: null, name: '', email: '', role: 'editor', password: '' };
  }
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};

const saveUser = async () => {
  formError.value = '';
  saving.value = true;
  
  try {
    const payload = { ...form.value };
    if (!payload.password) delete payload.password; // Não envia senha se estiver vazia (edição)
    
    if (payload.id) {
      await api.put(`/users/${payload.id}`, payload);
    } else {
      await api.post('/users', payload);
    }
    
    await loadUsers();
    closeModal();
  } catch (err) {
    formError.value = err.response?.data?.error || 'Erro ao salvar o usuário.';
  } finally {
    saving.value = false;
  }
};

const deleteUser = async (user) => {
  if (confirm(`Tem certeza que deseja remover o acesso de ${user.name}?`)) {
    try {
      await api.delete(`/users/${user.id}`);
      await loadUsers();
    } catch (err) {
      alert(err.response?.data?.error || 'Erro ao excluir usuário.');
    }
  }
};
</script>

<style scoped>
.list-container { max-width: 1000px; margin: 0 auto; }
.header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
h2 { margin: 0; color: var(--color-primary, #0B1C3D); }
.btn-primary { background: var(--color-primary, #0B1C3D); color: white; padding: 0.75rem 1.5rem; text-decoration: none; border-radius: 4px; font-weight: 500; border: none; cursor: pointer;}
.btn-primary:disabled { opacity: 0.7; cursor: not-allowed; }
.btn-outline { padding: 0.75rem 1.5rem; border: 1px solid #ccc; background: white; border-radius: 4px; cursor: pointer; }

.card { background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden; }

.data-table { width: 100%; border-collapse: collapse; text-align: left; }
.data-table th, .data-table td { padding: 1rem 1.5rem; border-bottom: 1px solid #eee; }
.data-table th { background: #f8f9fa; color: #555; font-weight: 600; }
.actions-col { width: 100px; text-align: right; }

.btn-icon { background: none; border: none; font-size: 1.25rem; cursor: pointer; padding: 0.25rem; opacity: 0.7; transition: opacity 0.2s;}
.btn-icon:hover { opacity: 1; }
.btn-icon:disabled { opacity: 0.2; cursor: not-allowed; }

.role-badge { padding: 0.25rem 0.75rem; border-radius: 16px; font-size: 0.85rem; font-weight: 500; }
.role-badge.admin { background: #e0e7ff; color: #3730a3; }
.role-badge.editor { background: #fef3c7; color: #92400e; }

.alert { padding: 1rem; border-radius: 4px; margin-bottom: 1rem; }
.alert.error { background: #f8d7da; color: #842029; border-left: 4px solid #dc3545; }

.modal-overlay { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 100; }
.modal-content { background: white; width: 100%; max-width: 500px; border-radius: 8px; padding: 2rem; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
.modal-content h3 { margin-top: 0; color: var(--color-primary, #0B1C3D); margin-bottom: 1.5rem; }

.form-group { margin-bottom: 1.25rem; display: flex; flex-direction: column; gap: 0.5rem; }
.form-group label { font-weight: 500; color: #444; font-size: 0.95rem; }
.form-group input, .form-group select { padding: 0.75rem; border: 1px solid #ced4da; border-radius: 4px; font-size: 1rem; }

.modal-actions { display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #eee; }
</style>
