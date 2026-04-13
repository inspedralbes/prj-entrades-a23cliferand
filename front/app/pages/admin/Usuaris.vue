<template>
  <div class="admin-page">
    <AdminSidebar />
    <div class="admin-content">
      <div class="page-header">
        <h1 class="page-title">Usuaris</h1>
        <button @click="openCreateModal" class="btn btn-primary">
          + Nou Usuari
        </button>
      </div>

      <AdminTable :data="usuaris" :columns="columns" :loading="loading" :error="error" @retry="fetchUsuaris">
        <template #cell-email="{ item }">
          <span class="email-cell">{{ item.email }}</span>
        </template>
        <template #cell-rol="{ item }">
          <span class="badge" :class="item.rol === 'admin' ? 'badge-admin' : 'badge-client'">
            {{ item.rol }}
          </span>
        </template>
        <template #cell-data_creacio="{ item }">
          {{ formatDate(item.data_creacio) }}
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

      <AdminModal v-model="showModal" :title="isEditing ? 'Editar Usuari' : 'Nou Usuari'">
        <form @submit.prevent="handleSubmit" class="admin-form">
          <AdminFormField v-model="form.nom" label="Nom" placeholder="Nom de l'usuari" required :error="errors.nom" />
          <AdminFormField v-model="form.email" label="Email" type="email" placeholder="email@example.com" required
            :error="errors.email" />
          <AdminFormField v-model="form.password"
            :label="isEditing ? 'Contrasenya (deixa buit per no canviar)' : 'Contrasenya'" type="password"
            :placeholder="isEditing ? '••••••••' : 'Contrasenya'" :required="!isEditing" :error="errors.password" />
          <AdminFormField v-model="form.rol" label="Rol" type="select" :options="rolOptions" required
            :error="errors.rol" />
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
          Estàs segur d'eliminar l'usuari <strong>{{ itemToDelete?.nom }}</strong>?
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
import { getUsuarisAll, createUsuari, updateUsuari, deleteUsuari } from '~/services/communicationManager'
import { useAppConstants } from '~/composables/useAppConstants'

definePageMeta({
  middleware: 'admin'
})

const { appName } = useAppConstants()

useHead({
  title: `Usuaris — ${appName}`,
})

const usuaris = ref([])
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
  email: '',
  password: '',
  rol: 'client'
})

const errors = reactive({
  nom: '',
  email: '',
  password: '',
  rol: ''
})

const rolOptions = [
  { value: 'admin', label: 'Administrador' },
  { value: 'client', label: 'Client' }
]

const columns = [
  { key: 'id', label: 'ID', width: '60px' },
  { key: 'nom', label: 'Nom' },
  { key: 'email', label: 'Email' },
  { key: 'rol', label: 'Rol', width: '100px' }
]

function formatDate(dateStr) {
  if (!dateStr) return '—'
  const date = new Date(dateStr)
  return date.toLocaleDateString('ca-ES')
}

function resetForm() {
  form.nom = ''
  form.email = ''
  form.password = ''
  form.rol = 'client'
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
  form.email = item.email
  form.rol = item.rol
  showModal.value = true
}

function validateForm() {
  let valid = true
  Object.keys(errors).forEach(key => errors[key] = '')

  if (!form.nom.trim()) {
    errors.nom = 'El nom és obligatori'
    valid = false
  }
  if (!form.email.trim()) {
    errors.email = 'L\'email és obligatori'
    valid = false
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
    errors.email = 'Email invàlid'
    valid = false
  }
  if (!isEditing.value && !form.password) {
    errors.password = 'La contrasenya és obligatòria'
    valid = false
  }
  if (!form.rol) {
    errors.rol = 'El rol és obligatori'
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
      email: form.email.trim(),
      rol: form.rol
    }
    if (form.password) {
      data.password = form.password
    }

    if (isEditing.value) {
      await updateUsuari(editingId.value, data)
    } else {
      await createUsuari(data)
    }

    showModal.value = false
    await fetchUsuaris()
  } catch (err) {
    errors.email = err.message || 'Error en desar'
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
    await deleteUsuari(itemToDelete.value.id)
    showDeleteModal.value = false
    itemToDelete.value = null
    await fetchUsuaris()
  } catch (err) {
    alert('Error en eliminar: ' + err.message)
  } finally {
    deleting.value = false
  }
}

async function fetchUsuaris() {
  loading.value = true
  error.value = null
  try {
    usuaris.value = await getUsuarisAll()
  } catch (err) {
    error.value = err.message || 'Error en carregar usuaris'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchUsuaris()
})
</script>

<style scoped>
.email-cell {
  color: var(--color-muted);
}

.badge {
  text-transform: capitalize;
}
</style>
