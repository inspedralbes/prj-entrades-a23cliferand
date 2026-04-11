<template>
  <div class="admin-page">
    <AdminSidebar />
    <div class="admin-content">
      <div class="page-header">
        <h1 class="page-title">Sessions</h1>
        <button @click="openCreateModal" class="btn btn-primary">
          + Nova Sessió
        </button>
      </div>

      <AdminTable :data="sessions" :columns="columns" :loading="loading" :error="error" @retry="fetchAll">
        <template #cell-pelicula="{ item }">
          {{ getPeliculaTitle(item.peliculaId) }}
        </template>
        <template #cell-sala="{ item }">
          {{ item.sala || '—' }}
        </template>
        <template #cell-tarifa="{ item }">
          {{ item.tarifa || '—' }}
        </template>
        <template #cell-data_hora="{ item }">
          {{ formatDateTime(item.dataHora) }}
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

      <AdminModal v-model="showModal" :title="isEditing ? 'Editar Sessió' : 'Nova Sessió'" size="large">
        <form @submit.prevent="handleSubmit" class="admin-form">
          <AdminFormField v-model="form.pelicula_id" label="Pel·lícula" type="select" :options="peliculaOptions"
            placeholder="Selecciona una pel·lícula" required :error="errors.pelicula_id" />
          <AdminFormField v-model="form.sala_id" label="Sala" type="select" :options="salaOptions"
            placeholder="Selecciona una sala" required :error="errors.sala_id" />
          <AdminFormField v-model="form.tarifa_id" label="Tarifa" type="select" :options="tarifaOptions"
            placeholder="Selecciona una tarifa" required :error="errors.tarifa_id" />

          <div v-if="!isEditing" class="session-mode-selector">
            <label class="mode-option">
              <input type="radio" v-model="sessionMode" value="single" />
              <span>Sessió única</span>
            </label>
            <label class="mode-option">
              <input type="radio" v-model="sessionMode" value="range" />
              <span>Rang de dates</span>
            </label>
          </div>

          <div v-if="sessionMode === 'single' || isEditing" class="form-section">
            <AdminFormField v-model="form.data_hora" label="Data i hora" type="datetime-local" required
              :error="errors.data_hora" />
          </div>

          <div v-if="sessionMode === 'range' && !isEditing" class="form-section">
            <div class="form-row">
              <div class="date-field">
                <label class="field-label">Data inici *</label>
                <input type="date" v-model="form.data_inici" class="form-input"
                  :class="{ 'has-error': errors.data_inici }" />
                <span v-if="errors.data_inici" class="field-error">{{ errors.data_inici }}</span>
              </div>
              <div class="date-field">
                <label class="field-label">Data fi *</label>
                <input type="date" v-model="form.data_fi" class="form-input" :class="{ 'has-error': errors.data_fi }" />
                <span v-if="errors.data_fi" class="field-error">{{ errors.data_fi }}</span>
              </div>
            </div>

            <div class="form-section">
              <label class="field-label">Dies de la setmana *</label>
              <div class="weekday-selector">
                <label v-for="day in weekDays" :key="day.value" class="weekday-option">
                  <input type="checkbox" v-model="form.dies_setmana" :value="day.value" />
                  <span>{{ day.label }}</span>
                </label>
              </div>
              <span v-if="errors.dies_setmana" class="field-error">{{ errors.dies_setmana }}</span>
            </div>

            <div class="form-section">
              <label class="field-label">Hores *</label>
              <div class="time-list">
                <div v-for="(hora, index) in form.hores" :key="index" class="time-row">
                  <input type="time" v-model="form.hores[index]" class="form-input time-input" />
                  <button type="button" v-if="form.hores.length > 1" @click="removeTime(index)" class="btn-remove-time">
                    ✕
                  </button>
                </div>
              </div>
              <button type="button" @click="addTime" class="btn-add-time">+ Afegir hora</button>
              <span v-if="errors.hores" class="field-error">{{ errors.hores }}</span>
            </div>

            <div v-if="createdCount > 0" class="created-preview">
              Es crearan <strong>{{ createdCount }}</strong> sessions
            </div>
          </div>
        </form>
        <template #footer>
          <button type="button" @click="showModal = false" class="btn btn-secondary">
            Cancel·lar
          </button>
          <button type="button" @click="handleSubmit" class="btn btn-primary" :disabled="submitting">
            {{ submitting ? (sessionMode === 'range' ? 'Creant...' : 'Guardant...') : (sessionMode === 'range' ? 'Crear Sessions' : 'Guardar') }}
          </button>
        </template>
      </AdminModal>

      <AdminModal v-model="showDeleteModal" title="Confirmar eliminació" size="small">
        <p class="delete-message">
          Estàs segur d'eliminar la sessió del <strong>{{ getPeliculaTitle(itemToDelete?.pelliculaId) }}</strong>?
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
import { ref, reactive, computed, onMounted, watch } from 'vue'
import {
  getSessionsAll,
  createSession,
  updateSession,
  deleteSession,
  getPeliculesAll,
  getSalesAll,
  getTarifesAll
} from '~/services/communicationManager'

definePageMeta({
  middleware: 'admin'
})

const sessions = ref([])
const pelicules = ref([])
const sales = ref([])
const tarifes = ref([])
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
  pellicula_id: '',
  sala_id: '',
  tarifa_id: '',
  data_hora: '',
  preu_base: 8,
  data_inici: '',
  data_fi: '',
  dies_setmana: [],
  hores: ['17:00']
})

const errors = reactive({
  pellicula_id: '',
  sala_id: '',
  tarifa_id: '',
  data_hora: '',
  data_inici: '',
  data_fi: '',
  dies_setmana: '',
  hores: ''
})

const weekDays = [
  { value: 0, label: 'Diumenge' },
  { value: 1, label: 'Dilluns' },
  { value: 2, label: 'Dimarts' },
  { value: 3, label: 'Dimecres' },
  { value: 4, label: 'Dijous' },
  { value: 5, label: 'Divendres' },
  { value: 6, label: 'Dissabte' }
]

const sessionMode = ref('single')
const createdCount = ref(0)

const peliculaOptions = computed(() =>
  pelicules.value.map(p => ({
    value: p.id,
    label: p.titol || p.id
  }))
)

const salaOptions = computed(() =>
  sales.value.map(s => ({
    value: String(s.id),
    label: s.nom
  }))
)

const tarifaOptions = computed(() =>
  tarifes.value.map(t => ({
    value: String(t.id),
    label: t.nom || `Tarifa ${t.id}`
  }))
)

const columns = [
  { key: 'id', label: 'ID', width: '60px' },
  { key: 'pelicula', label: 'Pel·lícula' },
  { key: 'sala', label: 'Sala', width: '100px' },
  { key: 'tarifa', label: 'Tarifa', width: '100px' },
  { key: 'data_hora', label: 'Data/Hora', width: '180px' }
]

function getPeliculaTitle(id) {
  const p = pelicules.value.find(p => p.id === id)
  return p?.titol || id || '—'
}

function formatDateTime(dateStr) {
  if (!dateStr) return '—'
  const date = new Date(dateStr)
  return date.toLocaleString('ca-ES', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

function formatDateForLaravel(dateStr) {
  const date = new Date(dateStr)
  const year = date.getFullYear()
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')
  const hours = String(date.getHours()).padStart(2, '0')
  const minutes = String(date.getMinutes()).padStart(2, '0')
  const seconds = '00'
  return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`
}

function resetForm() {
  form.pelicula_id = ''
  form.sala_id = ''
  form.tarifa_id = ''
  form.data_hora = ''
  form.data_inici = ''
  form.data_fi = ''
  form.dies_setmana = []
  form.hores = ['17:00']
  sessionMode.value = 'single'
  createdCount.value = 0
  Object.keys(errors).forEach(key => errors[key] = '')
}

function openCreateModal() {
  resetForm()
  isEditing.value = false
  editingId.value = null
  showModal.value = true
}

function addTime() {
  form.hores.push('19:00')
}

function removeTime(index) {
  form.hores.splice(index, 1)
}

function calculateCreatedCount() {
  if (sessionMode.value !== 'range' || !form.data_inici || !form.data_fi || form.dies_setmana.length === 0 || form.hores.length === 0) {
    createdCount.value = 0
    return
  }

  const start = new Date(form.data_inici)
  const end = new Date(form.data_fi)
  let count = 0

  for (let d = new Date(start); d <= end; d.setDate(d.getDate() + 1)) {
    if (form.dies_setmana.includes(d.getDay())) {
      count += form.hores.length
    }
  }

  createdCount.value = count
}

function openEditModal(item) {
  resetForm()
  isEditing.value = true
  editingId.value = item.id
  form.pelicula_id = item.peliculaId
  form.sala_id = String(item.salaId)
  form.tarifa_id = String(item.tarifaId)
  if (item.dataHora) {
    const date = new Date(item.dataHora)
    form.data_hora = date.toISOString().slice(0, 16)
  }
  showModal.value = true
}

function validateForm() {
  let valid = true
  Object.keys(errors).forEach(key => errors[key] = '')

  if (!form.pelicula_id) {
    errors.pelicula_id = 'La pel·lícula és obligatòria'
    valid = false
  }
  if (!form.sala_id) {
    errors.sala_id = 'La sala és obligatòria'
    valid = false
  }
  if (!form.tarifa_id) {
    errors.tarifa_id = 'La tarifa és obligatòria'
    valid = false
  }

  if (sessionMode.value === 'single' || isEditing.value) {
    if (!form.data_hora) {
      errors.data_hora = 'La data i hora és obligatòria'
      valid = false
    }
  } else {
    if (!form.data_inici) {
      errors.data_inici = 'La data d\'inici és obligatòria'
      valid = false
    }
    if (!form.data_fi) {
      errors.data_fi = 'La data de fi és obligatòria'
      valid = false
    }
    if (form.dies_setmana.length === 0) {
      errors.dies_setmana = 'Selecciona almenys un dia'
      valid = false
    }
    if (form.hores.length === 0 || form.hores.some(h => !h)) {
      errors.hores = 'Afegir almenys una hora'
      valid = false
    }
  }

  return valid
}

async function handleSubmit() {
  if (!validateForm()) return

  submitting.value = true
  try {
    if (isEditing.value) {
      const data = {
        pellicula_id: form.pelicula_id,
        sala_id: parseInt(form.sala_id),
        tarifa_id: parseInt(form.tarifa_id),
        data_hora: formatDateForLaravel(form.data_hora)
      }
      await updateSession(editingId.value, data)
    } else if (sessionMode.value === 'single') {
      const data = {
        pellicula_id: form.pelicula_id,
        sala_id: parseInt(form.sala_id),
        tarifa_id: parseInt(form.tarifa_id),
        data_hora: formatDateForLaravel(form.data_hora),
        preu_base: form.preu_base
      }
      await createSession(data)
    } else {
      const sessions = []
      const start = new Date(form.data_inici)
      const end = new Date(form.data_fi)

      for (let d = new Date(start); d <= end; d.setDate(d.getDate() + 1)) {
        if (form.dies_setmana.includes(d.getDay())) {
          for (const hora of form.hores) {
            const [hours, minutes] = hora.split(':')
            const dataHora = new Date(d)
            dataHora.setHours(parseInt(hours), parseInt(minutes), 0, 0)
            sessions.push({
              pellicula_id: form.pelicula_id,
              sala_id: parseInt(form.sala_id) || form.sala_id,
              tarifa_id: parseInt(form.tarifa_id) || form.tarifa_id,
              data_hora: dataHora.toISOString().slice(0, 19).replace('T', ' '),
              preu_base: form.preu_base
            })
          }
        }
      }

      await createSession({ sessions })
    }

    showModal.value = false
    await fetchSessions()
  } catch (err) {
    errors.pelicula_id = err.message || 'Error en desar'
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
    await deleteSession(itemToDelete.value.id)
    showDeleteModal.value = false
    itemToDelete.value = null
    await fetchSessions()
  } catch (err) {
    alert('Error en eliminar: ' + err.message)
  } finally {
    deleting.value = false
  }
}

async function fetchSessions() {
  try {
    sessions.value = await getSessionsAll()
  } catch (err) {
    error.value = err.message || 'Error en carregar sessions'
  }
}

async function fetchDropdownData() {
  try {
    const [p, s, t] = await Promise.all([
      getPeliculesAll(),
      getSalesAll(),
      getTarifesAll()
    ])
    pelicules.value = p
    sales.value = s
    tarifes.value = t
  } catch (err) {
    console.error('Error carregant dades per dropdowns:', err)
  }
}

async function fetchAll() {
  loading.value = true
  error.value = null
  try {
    await Promise.all([fetchSessions(), fetchDropdownData()])
  } finally {
    loading.value = false
  }
}

watch(
  () => [form.data_inici, form.data_fi, form.dies_setmana, form.hores, sessionMode.value],
  () => {
    if (sessionMode.value === 'range') {
      calculateCreatedCount()
    }
  },
  { deep: true }
)

onMounted(() => {
  fetchAll()
})
</script>

<style scoped>
.session-mode-selector {
  display: flex;
  gap: 20px;
  margin-bottom: 20px;
  padding: 12px;
  background: var(--color-surface2);
  border-radius: var(--radius-sm);
}

.mode-option {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
}

.mode-option input[type="radio"] {
  accent-color: var(--color-accent);
}

.form-section {
  margin-top: 16px;
  padding-top: 16px;
  border-top: 1px solid var(--color-border);
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.date-field {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.field-label {
  font-weight: 500;
  color: var(--color-text);
  font-size: 0.9rem;
}

.form-input {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid var(--color-border);
  border-radius: var(--radius-sm);
  background: var(--color-surface2);
  color: var(--color-text);
  font-size: 0.95rem;
  box-sizing: border-box;
}

.form-input:focus {
  outline: none;
  border-color: var(--color-accent);
}

.form-input.has-error {
  border-color: #e74c3c;
}

.field-error {
  font-size: 0.8rem;
  color: #e74c3c;
}

.weekday-selector {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  margin-top: 8px;
}

.weekday-option {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 12px;
  background: var(--color-surface2);
  border-radius: var(--radius-sm);
  cursor: pointer;
  font-size: 0.9rem;
  transition: all var(--transition);
}

.weekday-option:has(input:checked) {
  background: rgba(232, 39, 46, 0.15);
  color: var(--color-accent);
}

.weekday-option input[type="checkbox"] {
  accent-color: var(--color-accent);
}

.time-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-top: 8px;
}

.time-row {
  display: flex;
  gap: 8px;
  align-items: center;
}

.time-input {
  flex: 1;
  max-width: 150px;
}

.btn-remove-time {
  padding: 6px 10px;
  background: transparent;
  border: 1px solid var(--color-border);
  border-radius: var(--radius-sm);
  color: var(--color-muted);
  cursor: pointer;
  font-size: 0.9rem;
}

.btn-remove-time:hover {
  background: rgba(231, 76, 60, 0.1);
  border-color: #e74c3c;
  color: #e74c3c;
}

.btn-add-time {
  margin-top: 8px;
  padding: 8px 16px;
  background: var(--color-surface2);
  border: 1px dashed var(--color-border);
  border-radius: var(--radius-sm);
  color: var(--color-muted);
  cursor: pointer;
  font-size: 0.9rem;
  transition: all var(--transition);
}

.btn-add-time:hover {
  border-color: var(--color-accent);
  color: var(--color-accent);
}

.created-preview {
  margin-top: 16px;
  padding: 12px;
  background: rgba(76, 175, 80, 0.1);
  border-radius: var(--radius-sm);
  color: #4caf50;
  text-align: center;
}
</style>
