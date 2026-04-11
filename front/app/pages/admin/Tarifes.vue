<template>
  <div class="admin-page">
    <AdminSidebar />
    <div class="admin-content">
      <div class="page-header">
        <h1 class="page-title">Tarifes</h1>
        <button @click="openCreateModal" class="btn btn-primary">
          + Nova Tarifa
        </button>
      </div>

      <AdminTable :data="tarifes" :columns="columns" :loading="loading" :error="error" @retry="fetchTarifes">
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

      <AdminModal v-model="showModal" :title="isEditing ? 'Editar Tarifa' : 'Nova Tarifa'" size="medium">
        <form @submit.prevent="handleSubmit" class="admin-form">
          <AdminFormField v-model="form.nom" label="Nom" placeholder="Nom de la tarifa (ex: Tarifa Estàndard)" required
            :error="errors.nom" />

          <div class="preus-section">
            <h4 class="preus-title">Preus per tipus de client</h4>
            <div class="preus-grid">
              <div v-for="tc in tipusClients" :key="tc.id" class="preu-field">
                <label class="preu-label">{{ tc.nom }}</label>
                <div class="preu-input-wrapper">
                  <input type="number" v-model.number="form.preus.find(p => p.tipus_client_id === tc.id).preu"
                    class="preu-input" min="0" step="0.01" placeholder="0.00" />
                  <span class="preu-suffix">€</span>
                </div>
              </div>
            </div>
            <p v-if="errors.preus" class="error-text">{{ errors.preus }}</p>
          </div>
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
          Estàs segur d'eliminar la tarifa <strong>{{ itemToDelete?.nom }}</strong>?
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
import {
  getTarifesAll,
  getTipusClientAll,
  createTarifa,
  updateTarifa,
  deleteTarifa
} from '~/services/communicationManager'
import { useAppConstants } from '~/composables/useAppConstants'

definePageMeta({
  middleware: 'admin'
})

const { appName } = useAppConstants()

useHead({
  title: `Tarifes — ${appName}`,
})

const tarifes = ref([])
const tipusClients = ref([])
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
  preus: []
})

const errors = reactive({
  nom: '',
  preus: ''
})

const columns = [
  { key: 'id', label: 'ID', width: '60px' },
  { key: 'nom', label: 'Nom' },
  { key: 'preus', label: 'Preus', format: getPreusText }
]

function getPreusText(item) {
  if (!item.preus || item.preus.length === 0) return '-'
  return item.preus.map(p => `${p.tipus_client?.nom || '?'}: ${p.preu}€`).join(', ')
}

function resetForm() {
  form.nom = ''
  form.preus = tipusClients.value.map(tc => ({
    tipus_client_id: tc.id,
    preu: 0
  }))
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
  form.nom = item.nom || ''

  const existingPreus = item.preus || []
  form.preus = tipusClients.value.map(tc => {
    const existing = existingPreus.find(p => p.tipus_client_id === tc.id)
    return {
      tipus_client_id: tc.id,
      preu: existing ? parseFloat(existing.preu) : 0
    }
  })
  showModal.value = true
}

function validateForm() {
  let valid = true
  Object.keys(errors).forEach(key => errors[key] = '')

  if (!form.nom.trim()) {
    errors.nom = 'El nom és obligatori'
    valid = false
  }

  const hasValidPreus = form.preus.some(p => p.preu > 0)
  if (!hasValidPreus) {
    errors.preus = 'Cal introduir almenys un preu'
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
      preus: form.preus.filter(p => p.preu > 0)
    }

    if (isEditing.value) {
      await updateTarifa(editingId.value, data)
    } else {
      await createTarifa(data)
    }

    showModal.value = false
    await fetchTarifes()
  } catch (err) {
    errors.nom = err.message || 'Error en desar'
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
    await deleteTarifa(itemToDelete.value.id)
    showDeleteModal.value = false
    itemToDelete.value = null
    await fetchTarifes()
  } catch (err) {
    alert('Error en eliminar: ' + err.message)
  } finally {
    deleting.value = false
  }
}

async function fetchTarifes() {
  loading.value = true
  error.value = null
  try {
    tarifes.value = await getTarifesAll()
  } catch (err) {
    error.value = err.message || 'Error en carregar tarifes'
  } finally {
    loading.value = false
  }
}

async function fetchTipusClient() {
  try {
    tipusClients.value = await getTipusClientAll()
  } catch (err) {
    console.error('Error carregant tipus client:', err)
  }
}

onMounted(() => {
  fetchTipusClient().then(() => fetchTarifes())
})
</script>

<style scoped>
.preus-section {
  margin-top: 20px;
  padding-top: 16px;
  border-top: 1px solid var(--color-border);
}

.preus-title {
  margin: 0 0 12px 0;
  font-size: 1rem;
  color: var(--color-text);
  font-weight: 600;
}

.preus-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
}

.preu-field {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.preu-label {
  font-size: 0.85rem;
  color: var(--color-text);
  font-weight: 500;
}

.preu-input-wrapper {
  display: flex;
  align-items: center;
  background: var(--color-surface2);
  border-radius: var(--radius-sm);
  border: 1px solid var(--color-border);
}

.preu-input-wrapper:focus-within {
  border-color: var(--color-accent);
}

.preu-input {
  flex: 1;
  padding: 8px 10px;
  border: none;
  background: transparent;
  font-size: 0.95rem;
  color: var(--color-text);
  outline: none;
}

.preu-input::-webkit-outer-spin-button,
.preu-input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

.preu-suffix {
  padding: 0 10px 0 0;
  color: var(--color-muted);
  font-size: 0.9rem;
}

.error-text {
  color: #e74c3c;
  font-size: 0.85rem;
  margin-top: 8px;
}
</style>
