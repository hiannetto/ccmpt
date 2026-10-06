<template>
  <div class="quill-editor-wrapper">
    <QuillEditor 
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
    />
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';

const props = defineProps({
  modelValue: {
    type: String,
    default: '',
  },
});

const emit = defineEmits(['update:modelValue']);

const content = computed({
  get() {
    return props.modelValue;
  },
  set(value) {
    emit('update:modelValue', value);
  }
});
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
