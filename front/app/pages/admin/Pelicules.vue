<template>
  <div class="admin-page">
    <AdminSidebar />
    <div class="admin-content">
      <div class="page-header">
        <h1 class="page-title">Pel·lícules</h1>
      </div>

      <div class="sync-section">
        <h2 class="section-title">Sincronització IMDb</h2>
        <div class="sync-controls">
          <div class="sync-single">
            <input v-model="syncImdbId" type="text" placeholder="IMDb ID (ex: tt1234567)" class="sync-input" />
            <button @click="syncSingle" class="btn btn-primary" :disabled="syncingSingle || !syncImdbId">
              {{ syncingSingle ? 'Sincronitzant...' : 'Sincronitzar' }}
            </button>
          </div>
          <button @click="syncAll" class="btn btn-primary" :disabled="syncingAll">
            {{ syncingAll ? 'Sincronitzant totes...' : 'Sincronitzar Tot' }}
          </button>
        </div>
        <p v-if="syncMessage" class="sync-message" :class="{ 'sync-error': syncError }">
          {{ syncMessage }}
        </p>
      </div>

      <AdminTable :data="pelicules" :columns="columns" :loading="loading" :error="error" @retry="fetchPelicules">
        <template #cell-imdb_id="{ item }">
          <span class="imdb-id">{{ item.id }}</span>
        </template>
        <template #cell-titol="{ item }">
          <div class="movie-cell">
            <img v-if="item.poster" :src="item.poster" class="movie-poster" />
            <span>{{ item.titol || item.id }}</span>
          </div>
        </template>
        <template #cell-any="{ item }">
          {{ item.any || '—' }}
        </template>
        <template #cell-generes="{ item }">
          <span class="genres">{{ formatGeneres(item.generes) }}</span>
        </template>
        <template #cell-rating="{ item }">
          <span v-if="item.puntuacio" class="rating">{{ item.puntuacio }}</span>
        </template>
        <template #actions="{ item }">
          <div class="action-buttons">
            <button @click="syncSingleMovie(item)" class="btn btn-icon btn-sync" :disabled="syncingId === item.id"
              :title="'Sincronitzar ' + item.id">
              Sync
            </button>
            <button @click="openEditModal(item)" class="btn btn-icon btn-edit" title="Editar">
              Editar
            </button>
            <button @click="showDetails(item)" class="btn btn-icon btn-view" title="Veure detalls">
              Veure
            </button>
            <button @click="confirmDelete(item)" class="btn btn-icon btn-delete" title="Eliminar">
              Eliminar
            </button>
          </div>
        </template>
      </AdminTable>

      <AdminModal v-model="showEditModal" :title="'Editar: ' + (editForm.titol || editForm.id)" size="large">
        <form @submit.prevent="handleUpdate" class="admin-form">
          <div class="form-row">
            <AdminFormField v-model="editForm.titol" label="Títol" placeholder="Títol de la pel·lícula" required />
            <AdminFormField v-model="editForm.titol_original" label="Títol Original" placeholder="Títol original" />
          </div>
          <div class="form-row">
            <AdminFormField v-model="editForm.any" label="Any" type="number" placeholder="Any d'estrena" />
            <AdminFormField v-model="editForm.duracio" label="Duració (min)" type="number"
              placeholder="Duració en minuts" />
            <AdminFormField v-model="editForm.rating" label="Rating" placeholder="Puntuació IMDb" />
          </div>
          <AdminFormField v-model="editForm.generes" label="Gèneres" placeholder="Gèneres separats per comes" />
          <AdminFormField v-model="editForm.sinopsi" label="Sinopsi" type="textarea"
            placeholder="Sinopsi de la pel·lícula" />
          <AdminFormField v-model="editForm.cartell" label="URL Cartell" placeholder="URL de la imatge del cartell" />
        </form>
        <template #footer>
          <button type="button" @click="showEditModal = false" class="btn btn-secondary">
            Cancel·lar
          </button>
          <button type="button" @click="handleUpdate" class="btn btn-primary" :disabled="updating">
            {{ updating ? 'Guardant...' : 'Guardar' }}
          </button>
        </template>
      </AdminModal>

      <AdminModal v-model="showDeleteModal" title="Confirmar eliminació" size="small">
        <p class="delete-message">
          Estàs segur d'eliminar la pel·lícula <strong>{{ itemToDelete?.titol || itemToDelete?.id }}</strong>?
        </p>
        <p class="delete-warning">Aquesta acció eliminarà la pel·lícula de Redis.</p>
        <template #footer>
          <button type="button" @click="showDeleteModal = false" class="btn btn-secondary">
            Cancel·lar
          </button>
          <button type="button" @click="handleDelete" class="btn btn-danger" :disabled="deleting">
            {{ deleting ? 'Eliminant...' : 'Eliminar' }}
          </button>
        </template>
      </AdminModal>

      <AdminModal v-model="showDetailsModal" title="Detalls de la Pel·lícula" size="large">
        <div v-if="selectedPelicula" class="movie-details">
          <div class="movie-poster-large">
            <img v-if="selectedPelicula.poster" :src="selectedPelicula.poster" :alt="selectedPelicula.titol" />
            <div v-else class="poster-placeholder">Sense cartell</div>
          </div>
          <div class="movie-info">
            <h3 class="movie-title">{{ selectedPelicula.titol }}</h3>
            <p v-if="selectedPelicula.titolOriginal" class="movie-original">
              {{ selectedPelicula.titolOriginal }}
            </p>
            <div class="movie-meta">
              <span v-if="selectedPelicula.any">{{ selectedPelicula.any }}</span>
              <span v-if="selectedPelicula.duracio">{{ selectedPelicula.duracio }} min</span>
              <span v-if="selectedPelicula.puntuacio">{{ selectedPelicula.puntuacio }}</span>
            </div>
            <p v-if="selectedPelicula.generes?.length" class="movie-genres">
              <strong>Gèneres:</strong> {{ formatGeneres(selectedPelicula.generes) }}
            </p>
            <p v-if="selectedPelicula.sinopsi" class="movie-synopsis">
              <strong>Sinopsi:</strong><br />
              {{ selectedPelicula.sinopsi }}
            </p>
            <p class="movie-imdb">
              <strong>IMDb ID:</strong> {{ selectedPelicula.id }}
            </p>
          </div>
        </div>
      </AdminModal>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import {
  getPeliculesAll,
  syncPeliculaSingle,
  syncPeliculesAll,
  updatePelicula,
  deletePelicula
} from '~/services/communicationManager'

definePageMeta({
  middleware: 'admin'
})

const pelicules = ref([])
const loading = ref(true)
const error = ref(null)

const syncImdbId = ref('')
const syncingSingle = ref(false)
const syncingAll = ref(false)
const syncMessage = ref('')
const syncError = ref(false)

const showDetailsModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)
const selectedPelicula = ref(null)
const editingId = ref(null)
const editForm = ref({})
const itemToDelete = ref(null)
const deleting = ref(false)
const updating = ref(false)
const syncingId = ref(null)

const columns = [
  { key: 'imdb_id', label: 'IMDb ID', width: '120px' },
  { key: 'titol', label: 'Títol' },
  { key: 'any', label: 'Any', width: '80px' },
  { key: 'generes', label: 'Gèneres' },
  { key: 'rating', label: 'Rating', width: '100px' }
]

function formatGeneres(generes) {
  if (!generes) return '—'
  if (typeof generes === 'string') {
    return generes.split(',').slice(0, 3).join(', ')
  }
  if (Array.isArray(generes)) {
    return generes.slice(0, 3).join(', ')
  }
  return '—'
}

async function fetchPelicules() {
  loading.value = true
  error.value = null
  try {
    pelicules.value = await getPeliculesAll()
  } catch (err) {
    error.value = err.message || 'Error en carregar pel·lícules'
  } finally {
    loading.value = false
  }
}

function showDetails(pelicula) {
  selectedPelicula.value = pelicula
  showDetailsModal.value = true
}

function openEditModal(pelicula) {
  editingId.value = pelicula.id
  editForm.value = {
    id: pelicula.id,
    titol: pelicula.titol || '',
    titol_original: pelicula.titolOriginal || '',
    any: pelicula.any || '',
    duracio: pelicula.duracio || '',
    rating: pelicula.puntuacio || '',
    generes: Array.isArray(pelicula.generes) ? pelicula.generes.join(', ') : (pelicula.generes || ''),
    sinopsi: pelicula.sinopsi || '',
    cartell: pelicula.cartell || ''
  }
  showEditModal.value = true
}

async function handleUpdate() {
  updating.value = true
  try {
    await updatePelicula(editingId.value, editForm.value)
    showEditModal.value = false
    await fetchPelicules()
  } catch (err) {
    alert('Error en actualitzar: ' + err.message)
  } finally {
    updating.value = false
  }
}

async function syncSingleMovie(pelicula) {
  syncingId.value = pelicula.id
  try {
    await syncPeliculaSingle(pelicula.id)
    await fetchPelicules()
  } catch (err) {
    console.error('Error sincronitzant:', err)
  } finally {
    syncingId.value = null
  }
}

function confirmDelete(pelicula) {
  itemToDelete.value = pelicula
  showDeleteModal.value = true
}

async function handleDelete() {
  if (!itemToDelete.value) return

  deleting.value = true
  try {
    await deletePelicula(itemToDelete.value.id)
    showDeleteModal.value = false
    itemToDelete.value = null
    await fetchPelicules()
  } catch (err) {
    alert('Error en eliminar: ' + err.message)
  } finally {
    deleting.value = false
  }
}

async function syncSingle() {
  if (!syncImdbId.value.trim()) return

  syncingSingle.value = true
  syncMessage.value = ''
  syncError.value = false

  try {
    const result = await syncPeliculaSingle(syncImdbId.value.trim())
    syncMessage.value = result.message || 'Sincronització completada'
    syncImdbId.value = ''
    await fetchPelicules()
  } catch (err) {
    syncError.value = true
    syncMessage.value = err.message || 'Error en sincronitzar'
  } finally {
    syncingSingle.value = false
  }
}

async function syncAll() {
  syncingAll.value = true
  syncMessage.value = 'Sincronitzant totes les pel·lícules...'
  syncError.value = false

  try {
    const result = await syncPeliculesAll()
    syncMessage.value = `Sincronització completada: ${result.synced_count || 0} pel·lícules`
    if (result.errors?.length) {
      syncMessage.value += ` (${result.errors.length} errors)`
    }
    await fetchPelicules()
  } catch (err) {
    syncError.value = true
    syncMessage.value = err.message || 'Error en sincronitzar'
  } finally {
    syncingAll.value = false
  }
}

onMounted(() => {
  fetchPelicules()
})
</script>

<style scoped>
.sync-section {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  padding: 20px;
  margin-bottom: 24px;
}

.section-title {
  font-family: var(--font-title);
  font-size: 1.1rem;
  color: var(--color-text);
  margin: 0 0 16px 0;
}

.sync-controls {
  display: flex;
  gap: 16px;
  align-items: flex-start;
  flex-wrap: wrap;
}

.sync-single {
  display: flex;
  gap: 12px;
  flex: 1;
  min-width: 300px;
}

.sync-input {
  flex: 1;
  padding: 10px 12px;
  border: 1px solid var(--color-border);
  border-radius: var(--radius-sm);
  background: var(--color-surface2);
  color: var(--color-text);
  font-size: 0.95rem;
}

.sync-input:focus {
  outline: none;
  border-color: var(--color-accent);
}

.sync-message {
  margin-top: 12px;
  padding: 10px;
  border-radius: var(--radius-sm);
  background: rgba(76, 175, 80, 0.1);
  color: #4caf50;
  font-size: 0.9rem;
}

.sync-error {
  background: rgba(231, 76, 60, 0.1);
  color: #e74c3c;
}

.imdb-id {
  font-family: monospace;
  color: var(--color-muted);
  font-size: 0.85rem;
}

.movie-cell {
  display: flex;
  align-items: center;
  gap: 12px;
}

.movie-poster {
  width: 40px;
  height: 60px;
  object-fit: cover;
  border-radius: var(--radius-sm);
}

.genres {
  color: var(--color-muted);
  font-size: 0.85rem;
}

.rating {
  color: #f1c40f;
}

.movie-details {
  display: grid;
  grid-template-columns: 200px 1fr;
  gap: 24px;
}

.movie-poster-large img {
  width: 100%;
  border-radius: var(--radius-md);
}

.poster-placeholder {
  width: 100%;
  aspect-ratio: 2/3;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--color-surface2);
  border-radius: var(--radius-md);
  color: var(--color-muted);
}

.movie-title {
  font-family: var(--font-title);
  font-size: 1.5rem;
  color: var(--color-text);
  margin: 0 0 8px 0;
}

.movie-original {
  color: var(--color-muted);
  margin: 0 0 16px 0;
}

.movie-meta {
  display: flex;
  gap: 16px;
  margin-bottom: 16px;
  color: var(--color-muted);
}

.movie-genres,
.movie-synopsis,
.movie-imdb {
  margin: 0 0 12px 0;
  color: var(--color-text);
  line-height: 1.6;
}

.movie-synopsis {
  color: var(--color-muted);
}
</style>
