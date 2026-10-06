<template>
  <div class="login-container">
    <div class="login-box">
      <div class="login-header">
        <div class="logo-placeholder">CCMPT</div>
        <h2>Acesso Restrito</h2>
        <p>Painel de Gestão do Memorial</p>
      </div>
      
      <form @submit.prevent="handleLogin" class="login-form">
        <div v-if="error" class="error-alert">{{ error }}</div>
        
        <div class="form-group">
          <label for="email">E-mail</label>
          <input type="email" id="email" v-model="email" required autofocus placeholder="admin@ccmpt.com.br" />
        </div>
        
        <div class="form-group">
          <label for="password">Senha</label>
          <input type="password" id="password" v-model="password" required />
        </div>
        
        <button type="submit" class="btn-primary" :disabled="loading">
          {{ loading ? 'Entrando...' : 'Entrar' }}
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuth } from '../../store/auth';
import api from '../../api';

const email = ref('');
const password = ref('');
const error = ref('');
const loading = ref(false);
const router = useRouter();
const { setAuth } = useAuth();

const handleLogin = async () => {
  error.value = '';
  loading.value = true;
  
  try {
    const response = await api.post('/login', {
      email: email.value,
      password: password.value
    });
    
    const { token, user } = response.data;
    setAuth(token, user);
    
    router.push('/admin');
  } catch (err) {
    if (err.response && err.response.data && err.response.data.error) {
      error.value = err.response.data.error;
    } else {
      error.value = 'Erro ao conectar com o servidor.';
    }
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.login-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #f0f2f5;
  background-image: linear-gradient(135deg, var(--color-primary, #0B1C3D) 0%, #1a365d 100%);
}

.login-box {
  background: white;
  padding: 2.5rem;
  border-radius: 8px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
  width: 100%;
  max-width: 400px;
}

.login-header {
  text-align: center;
  margin-bottom: 2rem;
}

.logo-placeholder {
  width: 64px;
  height: 64px;
  background-color: var(--color-secondary, #D4AF37);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: 1.2rem;
  margin: 0 auto 1rem;
}

.login-header h2 {
  margin: 0;
  color: #333;
  font-size: 1.5rem;
}

.login-header p {
  margin: 0.5rem 0 0;
  color: #666;
  font-size: 0.9rem;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

label {
  font-weight: 500;
  color: #444;
  font-size: 0.9rem;
}

input {
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
  transition: border-color 0.2s;
}

input:focus {
  outline: none;
  border-color: var(--color-secondary, #D4AF37);
  box-shadow: 0 0 0 2px rgba(212, 175, 55, 0.2);
}

.btn-primary {
  background-color: var(--color-primary, #0B1C3D);
  color: white;
  border: none;
  padding: 0.85rem;
  border-radius: 4px;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;
  margin-top: 0.5rem;
}

.btn-primary:hover:not(:disabled) {
  background-color: #1a365d;
}

.btn-primary:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.error-alert {
  background-color: #fee2e2;
  color: #b91c1c;
  padding: 0.75rem;
  border-radius: 4px;
  font-size: 0.9rem;
  border-left: 4px solid #ef4444;
}
</style>
