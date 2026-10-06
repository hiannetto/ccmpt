<template>
  <div class="contact-page">
    <div class="page-header">
      <div class="container">
        <h1>Fale Conosco</h1>
        <p>Entre em contato para dúvidas, doações de acervo, agendamento de visitas ou para participar dos nossos projetos sociais.</p>
      </div>
    </div>

    <div class="container">
      <div class="contact-grid">
        <div class="contact-info">
          <h2>Informações de Contato</h2>
          <div class="info-item">
            <div class="icon"><span class="material-symbols-outlined">location_on</span></div>
            <div>
              <h3>Endereço</h3>
              <p>Rua Exemplo, 123 - Centro<br>Cidade - Estado, 00000-000</p>
            </div>
          </div>
          
          <div class="info-item">
            <div class="icon"><span class="material-symbols-outlined">call</span></div>
            <div>
              <h3>Telefone</h3>
              <p>(00) 0000-0000</p>
            </div>
          </div>
          
          <div class="info-item">
            <div class="icon"><span class="material-symbols-outlined">mail</span></div>
            <div>
              <h3>E-mail</h3>
              <p>contato@ccmpt.com.br</p>
            </div>
          </div>
          
          <div class="info-item">
            <div class="icon"><span class="material-symbols-outlined">schedule</span></div>
            <div>
              <h3>Horário de Funcionamento</h3>
              <p>Segunda a Sexta: 08h às 18h<br>Sábados: 08h às 12h</p>
            </div>
          </div>
        </div>

        <div class="contact-form-wrapper">
          <h2>Envie uma Mensagem</h2>
          
          <form @submit.prevent="submitForm" class="contact-form">
            <div v-if="success" class="alert success">{{ success }}</div>
            <div v-if="error" class="alert error">{{ error }}</div>

            <div class="form-group">
              <label for="name">Nome Completo</label>
              <input type="text" id="name" v-model="form.name" required placeholder="Seu nome" />
            </div>

            <div class="form-row">
              <div class="form-group half">
                <label for="email">E-mail</label>
                <input type="email" id="email" v-model="form.email" required placeholder="seu@email.com" />
              </div>
              <div class="form-group half">
                <label for="phone">Telefone (Opcional)</label>
                <input type="tel" id="phone" v-model="form.phone" placeholder="(00) 00000-0000" />
              </div>
            </div>

            <div class="form-group">
              <label for="subject">Assunto</label>
              <input type="text" id="subject" v-model="form.subject" required placeholder="Qual o motivo do contato?" />
            </div>

            <div class="form-group">
              <label for="message">Mensagem</label>
              <textarea id="message" v-model="form.message" rows="5" required placeholder="Como podemos ajudar?"></textarea>
            </div>

            <button type="submit" class="btn-primary" :disabled="loading">
              {{ loading ? 'Enviando...' : 'Enviar Mensagem' }}
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import api from '../../api';

const form = ref({
  name: '',
  email: '',
  phone: '',
  subject: '',
  message: ''
});

const loading = ref(false);
const success = ref('');
const error = ref('');

const submitForm = async () => {
  loading.value = true;
  success.value = '';
  error.value = '';

  try {
    await api.post('/contacts', form.value);
    success.value = 'Sua mensagem foi enviada com sucesso! Entraremos em contato em breve.';
    // Limpa o formulário
    form.value = {
      name: '',
      email: '',
      phone: '',
      subject: '',
      message: ''
    };
  } catch (err) {
    error.value = err.response?.data?.error || 'Ocorreu um erro ao enviar a mensagem. Tente novamente mais tarde.';
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0');

.page-header {
  background-color: var(--color-primary, #0B1C3D);
  color: white;
  padding: 4rem 0;
  text-align: center;
  margin-bottom: 3rem;
  border-bottom: 4px solid var(--color-secondary, #D4AF37);
}

.page-header h1 {
  margin: 0 0 1rem;
  color: white;
  font-size: 2.5rem;
}

.page-header p {
  font-size: 1.1rem;
  opacity: 0.9;
  max-width: 600px;
  margin: 0 auto;
}

.contact-grid {
  display: grid;
  grid-template-columns: 1fr 2fr;
  gap: 3rem;
  margin-bottom: 4rem;
}

@media (max-width: 768px) {
  .contact-grid {
    grid-template-columns: 1fr;
  }
}

.contact-info h2, .contact-form-wrapper h2 {
  color: var(--color-primary, #0B1C3D);
  margin-top: 0;
  margin-bottom: 1.5rem;
  font-size: 1.5rem;
}

.info-item {
  display: flex;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.info-item .icon {
  color: var(--color-secondary, #D4AF37);
  background: #f8f9fa;
  width: 48px;
  height: 48px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.info-item .icon .material-symbols-outlined {
  font-size: 24px;
}

.info-item h3 {
  margin: 0 0 0.25rem;
  font-size: 1rem;
  color: #333;
}

.info-item p {
  margin: 0;
  color: #666;
  font-size: 0.95rem;
  line-height: 1.5;
}

.contact-form-wrapper {
  background: white;
  padding: 2.5rem;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0,0,0,0.05);
  border-top: 4px solid var(--color-primary, #0B1C3D);
}

.contact-form {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.form-row {
  display: flex;
  gap: 1.25rem;
}

.form-group.half {
  flex: 1;
}

@media (max-width: 600px) {
  .form-row {
    flex-direction: column;
  }
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

label {
  font-weight: 500;
  color: #444;
  font-size: 0.95rem;
}

input, select, textarea {
  padding: 0.75rem;
  border: 1px solid #ced4da;
  border-radius: 4px;
  font-size: 1rem;
  font-family: inherit;
  transition: border-color 0.2s;
}

input:focus, select:focus, textarea:focus {
  outline: none;
  border-color: var(--color-secondary, #D4AF37);
  box-shadow: 0 0 0 2px rgba(212, 175, 55, 0.2);
}

.btn-primary {
  margin-top: 0.5rem;
  align-self: flex-start;
  padding: 0.85rem 2rem;
  font-size: 1.05rem;
}

.alert {
  padding: 1rem;
  border-radius: 4px;
  margin-bottom: 1rem;
}

.alert.success {
  background: #d1e7dd;
  color: #0f5132;
  border-left: 4px solid #198754;
}

.alert.error {
  background: #f8d7da;
  color: #842029;
  border-left: 4px solid #dc3545;
}
</style>
