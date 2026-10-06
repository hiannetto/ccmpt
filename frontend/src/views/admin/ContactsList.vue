<template>
  <div class="list-container">
    <div class="header-actions">
      <h2>Mensagens de Contato</h2>
      <div class="filters">
        <select v-model="filterStatus" @change="fetchContacts">
          <option value="">Todos</option>
          <option value="pendente">Não Lidos (Pendentes)</option>
          <option value="atendido">Respondidos (Atendidos)</option>
        </select>
      </div>
    </div>
    
    <div class="card table-container">
      <div v-if="loading" class="loading-state">Carregando contatos...</div>
      
      <table v-else-if="contacts.length > 0" class="data-table">
        <thead>
          <tr>
            <th>Data</th>
            <th>Remetente</th>
            <th>Assunto</th>
            <th width="120">Status</th>
            <th width="120">Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="contact in contacts" :key="contact.id" :class="{'unread-row': contact.status === 'pendente'}">
            <td class="date-cell">{{ formatDate(contact.created_at) }}</td>
            <td>
              <strong>{{ contact.name }}</strong>
              <div class="email-hint">{{ contact.email }}</div>
            </td>
            <td>{{ contact.subject || 'Sem assunto' }}</td>
            <td>
              <span class="badge" :class="contact.status === 'pendente' ? 'badge-draft' : 'badge-success'">
                {{ contact.status === 'pendente' ? 'Pendente' : 'Atendido' }}
              </span>
            </td>
            <td class="actions">
              <button @click="openModal(contact)" class="btn-action view" title="Ler Mensagem Completa">
                👁
              </button>
              <button @click="toggleStatus(contact)" class="btn-action toggle" :title="contact.status === 'pendente' ? 'Marcar como atendido' : 'Marcar como pendente'">
                {{ contact.status === 'pendente' ? '✓' : '↺' }}
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      
      <div v-else class="empty-state">
        Nenhuma mensagem encontrada.
      </div>
    </div>

    <!-- Modal para ler mensagem completa -->
    <div v-if="selectedContact" class="modal-overlay" @click.self="closeModal">
      <div class="modal-content">
        <div class="modal-header">
          <h3>Mensagem de {{ selectedContact.name }}</h3>
          <button @click="closeModal" class="btn-close">&times;</button>
        </div>
        
        <div class="modal-body">
          <div class="contact-meta">
            <p><strong>Data:</strong> {{ formatDateTime(selectedContact.created_at) }}</p>
            <p><strong>E-mail:</strong> <a :href="'mailto:' + selectedContact.email">{{ selectedContact.email }}</a></p>
            <p v-if="selectedContact.phone"><strong>Telefone:</strong> {{ selectedContact.phone }}</p>
            <p><strong>Assunto:</strong> {{ selectedContact.subject || 'Sem assunto' }}</p>
          </div>
          
          <div class="contact-message">
            <h4>Mensagem:</h4>
            <div class="message-box">{{ selectedContact.message }}</div>
          </div>
        </div>

        <div class="modal-footer">
          <button @click="toggleStatus(selectedContact)" class="btn-toggle-status" :class="selectedContact.status === 'pendente' ? 'btn-success' : 'btn-outline'">
            {{ selectedContact.status === 'pendente' ? 'Marcar como Atendido' : 'Marcar como Pendente' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../api';

const contacts = ref([]);
const loading = ref(true);
const filterStatus = ref('');
const selectedContact = ref(null);

const fetchContacts = async () => {
  loading.value = true;
  try {
    let url = '/contacts';
    if (filterStatus.value) {
      url += `?status=${filterStatus.value}`;
    }
    const res = await api.get(url);
    // Como a API é paginada, o array está em res.data.data
    contacts.value = res.data.data || [];
  } catch (err) {
    alert('Erro ao carregar mensagens.');
    console.error(err);
  } finally {
    loading.value = false;
  }
};

const toggleStatus = async (contact) => {
  const newStatus = contact.status === 'pendente' ? 'atendido' : 'pendente';
  try {
    await api.put(`/contacts/${contact.id}/status`, { status: newStatus });
    contact.status = newStatus;
  } catch (err) {
    alert('Erro ao alterar status.');
  }
};

const openModal = (contact) => {
  selectedContact.value = contact;
};

const closeModal = () => {
  selectedContact.value = null;
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  const d = new Date(dateString);
  return d.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric' });
};

const formatDateTime = (dateString) => {
  if (!dateString) return '';
  const d = new Date(dateString);
  return d.toLocaleString('pt-BR');
};

onMounted(() => {
  fetchContacts();
});
</script>

<style scoped>
.list-container { max-width: 1000px; margin: 0 auto; padding-bottom: 2rem; }
.header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
h2 { margin: 0; color: var(--color-primary, #0B1C3D); }
.filters select { padding: 0.5rem; border: 1px solid #ced4da; border-radius: 4px; font-size: 1rem; }

.card { background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 1.5rem; }

.data-table { width: 100%; border-collapse: collapse; }
.data-table th { text-align: left; padding: 1rem; background: #f8f9fa; border-bottom: 2px solid #dee2e6; color: #495057; font-weight: 600; }
.data-table td { padding: 1rem; border-bottom: 1px solid #e9ecef; vertical-align: middle; }
.unread-row { background-color: #f8fbff; }
.unread-row strong { color: #000; }

.date-cell { color: #6c757d; font-size: 0.9rem; white-space: nowrap; }
.email-hint { font-size: 0.85rem; color: #6c757d; margin-top: 0.25rem; }

.badge { display: inline-block; padding: 0.25rem 0.6rem; border-radius: 12px; font-size: 0.8rem; font-weight: 500; }
.badge-success { background: #e8f5e9; color: #1b5e20; }
.badge-draft { background: #ffebee; color: #c62828; }

.actions { display: flex; gap: 0.5rem; }
.btn-action { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 4px; text-decoration: none; cursor: pointer; border: 1px solid transparent; font-size: 1rem; }
.btn-action.view { background: #e3f2fd; color: #1976d2; }
.btn-action.toggle { background: #e9ecef; color: #495057; }
.btn-action:hover { opacity: 0.8; }

.loading-state, .empty-state { text-align: center; padding: 3rem 0; color: #6c757d; }

/* MODAL */
.modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 1000; }
.modal-content { background: white; border-radius: 8px; width: 90%; max-width: 600px; max-height: 90vh; display: flex; flex-direction: column; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
.modal-header { padding: 1.5rem; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center; }
.modal-header h3 { margin: 0; color: var(--color-primary, #0B1C3D); }
.btn-close { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #6c757d; }
.modal-body { padding: 1.5rem; overflow-y: auto; flex: 1; }
.contact-meta { background: #f8f9fa; padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem; }
.contact-meta p { margin: 0.25rem 0; color: #495057; font-size: 0.95rem; }
.contact-meta a { color: #1976d2; text-decoration: none; }
.contact-message h4 { margin: 0 0 0.5rem; color: #495057; }
.message-box { padding: 1rem; background: #fff; border: 1px solid #dee2e6; border-radius: 4px; min-height: 150px; white-space: pre-wrap; font-family: inherit; line-height: 1.5; color: #333; }
.modal-footer { padding: 1rem 1.5rem; border-top: 1px solid #dee2e6; display: flex; justify-content: flex-end; gap: 1rem; }

.btn-toggle-status { padding: 0.75rem 1.5rem; border-radius: 4px; font-weight: 600; cursor: pointer; border: none; }
.btn-success { background: #198754; color: white; }
.btn-outline { background: #f8f9fa; color: #495057; border: 1px solid #ced4da; }
</style>
