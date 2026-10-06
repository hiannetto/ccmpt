<template>
  <div class="quill-editor-wrapper">
    <QuillEditor 
      ref="quillEditor"
      theme="snow" 
      v-model:content="content" 
      contentType="html" 
      class="editor-content"
      placeholder="Escreva o conteúdo aqui..."
      :toolbar="[
        [{ 'header': [2, 3, false] }],
        ['bold', 'italic', 'underline'],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        ['link', 'image'],
        ['clean']
      ]"
      @ready="onEditorReady"
    />
    <input type="file" ref="fileInput" style="display: none" accept="image/*" @change="uploadImage" />
  </div>
</template>

<script setup>
import { computed, ref, toRaw } from 'vue';
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import api from '../api';

const props = defineProps({
  modelValue: {
    type: String,
    default: '',
  },
});

const emit = defineEmits(['update:modelValue']);

const quillEditor = ref(null);
const fileInput = ref(null);
let quillInstance = null;

const content = computed({
  get() {
    return props.modelValue;
  },
  set(value) {
    emit('update:modelValue', value);
  }
});

const onEditorReady = (quill) => {
  quillInstance = toRaw(quill);
  const toolbar = quillInstance.getModule('toolbar');
  toolbar.addHandler('image', selectLocalImage);
};

const selectLocalImage = () => {
  if (fileInput.value) {
    fileInput.value.click();
  }
};

const uploadImage = async (event) => {
  const file = event.target.files[0];
  if (!file) return;
  
  try {
    const formData = new FormData();
    formData.append('image', file);
    
    const response = await api.post('/uploads/image', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
    
    const imageUrl = response.data.url;
    
    if (quillInstance) {
      const range = quillInstance.getSelection(true);
      quillInstance.insertEmbed(range.index, 'image', imageUrl);
      quillInstance.setSelection(range.index + 1);
    }
  } catch (err) {
    console.error('Erro no upload de imagem:', err);
    alert('Erro ao enviar imagem. Verifique o formato e o tamanho (máx 40MB).');
  } finally {
    event.target.value = '';
  }
};
</script>

<style>
.quill-editor-wrapper {
  background: white;
  border-radius: 4px;
  display: flex;
  flex-direction: column;
}

.editor-content {
  min-height: 250px;
  font-family: inherit;
  font-size: 1rem;
}

/* Ajustes de estilo para integrar com o design system */
.ql-toolbar.ql-snow {
  border-color: #ced4da;
  background-color: #f8f9fa;
  border-top-left-radius: 4px;
  border-top-right-radius: 4px;
  padding: 0.5rem;
}

.ql-container.ql-snow {
  border-color: #ced4da;
  border-bottom-left-radius: 4px;
  border-bottom-right-radius: 4px;
  font-family: inherit;
}

.ql-editor {
  min-height: 250px;
  padding: 1rem;
}

.ql-editor p {
  margin-bottom: 1rem;
}

.ql-editor h2, .ql-editor h3 {
  color: var(--color-primary, #0B1C3D);
  margin-top: 1.5rem;
  margin-bottom: 0.5rem;
}
</style>
