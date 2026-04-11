<template>
  <div class="admin-page">
    <AdminSidebar />
    <div class="admin-content">
      <div class="page-header">
        <h1 class="page-title">Sales</h1>
        <button @click="openCreateModal" class="btn btn-primary">
          + Nova Sala
        </button>
      </div>

      <AdminTable :data="sales" :columns="columns" :loading="loading" :error="error" @retry="fetchSales">
        <template #cell-capacitat="{ item }">
          {{ item.capacitat || '—' }}
        </template>
        <template #cell-files_max="{ item }">
          {{ item.files_max || '—' }}
        </template>
        <template #cell-columnes_max="{ item }">
          {{ item.columnes_max || '—' }}
        </template>
        <template #actions="{ item }">
          <div class="action-buttons">
            <button @click="openEditModal(item)" class="btn btn-icon btn-edit" title="Editar">
              Editar
            </button>
            <button @click="confirmDelete(item)" class="btn btn-icon btn-delete" title="Eliminar">
              Eliminar
            </button>
          </div>
        </template>
      </AdminTable>

      <AdminModal v-model="showModal" :title="isEditing ? 'Editar Sala' : 'Nova Sala'">
        <form @submit.prevent="handleSubmit" class="admin-form">
          <AdminFormField v-model="form.nom" label="Nom" placeholder="Nom de la sala" required :error="errors.nom" />
          <AdminFormField v-model="form.capacitat" label="Capacitat" type="number" placeholder="Nombre de seients"
            :min="1" required :error="errors.capacitat" />
          <AdminFormField v-model="form.files_max" label="Files màximes" type="number" placeholder="Nombre de files"
            :min="1" :error="errors.files_max" />
          <AdminFormField v-model="form.columnes_max" label="Columnes màximes" type="number"
            placeholder="Nombre de columnes" :min="1" :error="errors.columnes_max" />
        </form>
        <template #footer>
          <button type="button" @click="showModal = false" class="btn btn-secondary">
            Cancel·lar
          </button>
          <button type="button" @click="handleSubmit" class="btn btn-primary" :disabled="submitting">
            {{ submitting ? 'Guardant...' : 'Guardar' }}
          </button>
        </template>
      </AdminModal>

      <AdminModal v-model="showDeleteModal" title="Confirmar eliminació" size="small">
        <p class="delete-message">
          Estàs segur d'eliminar la sala <strong>{{ itemToDelete?.nom }}</strong>?
        </p>
        <p class="delete-warning">Aquesta acció no es pot desfer.</p>
        <template #footer>
          <button type="button" @click="showDeleteModal = false" class="btn btn-secondary">
            Cancel·lar
          </button>
          <button type="button" @click="handleDelete" class="btn btn-danger" :disabled="deleting">
            {{ deleting ? 'Eliminant...' : 'Eliminar' }}
          </button>
        </template>
      </AdminModal>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { getSalesAll, createSala, updateSala, deleteSala } from '~/services/communicationManager'

definePageMeta({
  middleware: 'admin'
})

const sales = ref([])
const loading = ref(true)
const error = ref(null)

const showModal = ref(false)
const showDeleteModal = ref(false)
const isEditing = ref(false)
const submitting = ref(false)
const deleting = ref(false)
const itemToDelete = ref(null)
const editingId = ref(null)

const form = reactive({
  nom: '',
  capacitat: '',
  files_max: '',
  columnes_max: ''
})

const errors = reactive({
  nom: '',
  capacitat: '',
  files_max: '',
  columnes_max: ''
})

const columns = [
  { key: 'id', label: 'ID', width: '60px' },
  { key: 'nom', label: 'Nom' },
  { key: 'capacitat', label: 'Capacitat', width: '100px' },
  { key: 'files_max', label: 'Files', width: '80px' },
  { key: 'columnes_max', label: 'Columnes', width: '100px' }
]

function resetForm() {
  form.nom = ''
  form.capacitat = ''
  form.files_max = ''
  form.columnes_max = ''
  Object.keys(errors).forEach(key => errors[key] = '')
}

function openCreateModal() {
  resetForm()
  isEditing.value = false
  editingId.value = null
  showModal.value = true
}

function openEditModal(item) {
  resetForm()
  isEditing.value = true
  editingId.value = item.id
  form.nom = item.nom
  form.capacitat = item.capacitat || ''
  form.files_max = item.files_max || ''
  form.columnes_max = item.columnes_max || ''
  showModal.value = true
}

function validateForm() {
  let valid = true
  Object.keys(errors).forEach(key => errors[key] = '')

  if (!form.nom.trim()) {
    errors.nom = 'El nom és obligatori'
    valid = false
  }
  if (!form.capacitat || form.capacitat < 1) {
    errors.capacitat = 'La capacitat és obligatòria'
    valid = false
  }

  return valid
}

async function handleSubmit() {
  if (!validateForm()) return

  submitting.value = true
  try {
    const data = {
      nom: form.nom.trim(),
      capacitat: parseInt(form.capacitat),
      files: parseInt(form.files_max) || null,
      columnes: parseInt(form.columnes_max) || null
    }

    if (isEditing.value) {
      await updateSala(editingId.value, data)
    } else {
      await createSala(data)
    }

    showModal.value = false
    await fetchSales()
  } catch (err) {
    errors.capacitat = err.message || 'Error en desar'
  } finally {
    submitting.value = false
  }
}

function confirmDelete(item) {
  itemToDelete.value = item
  showDeleteModal.value = true
}

async function handleDelete() {
  deleting.value = true
  try {
    await deleteSala(itemToDelete.value.id)
    showDeleteModal.value = false
    itemToDelete.value = null
    await fetchSales()
  } catch (err) {
    alert('Error en eliminar: ' + err.message)
  } finally {
    deleting.value = false
  }
}

async function fetchSales() {
  loading.value = true
  error.value = null
  try {
    sales.value = await getSalesAll()
  } catch (err) {
    error.value = err.message || 'Error en carregar sales'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchSales()
})
</script>

<style scoped>
/* No cal cap estil especifique - tot heretat de main.css */
</style>
