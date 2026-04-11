<template>
  <div class="admin-page">
    <AdminSidebar />
    <div class="admin-content">
      <div class="page-header">
        <h1 class="page-title">Reserves</h1>
        <p class="page-subtitle">Vista de només lectura</p>
      </div>

      <AdminTable :data="reserves" :columns="columns" :loading="loading" :error="error" @retry="fetchReserves">
        <template #cell-usuari="{ item }">
          <span v-if="item.usuari">{{ item.usuari.nom }}</span>
          <span v-else-if="item.email" class="guest-email">{{ item.email }}</span>
          <span v-else class="unknown">—</span>
        </template>
        <template #cell-sessio_id="{ item }">
          {{ item.sessio_id || '—' }}
        </template>
        <template #cell-data_hora="{ item }">
          {{ formatDateTime(item.data_hora) }}
        </template>
        <template #cell-estat="{ item }">
          <span class="badge" :class="`badge-${item.estat}`">
            {{ formatEstat(item.estat) }}
          </span>
        </template>
        <template #cell-preu_total="{ item }">
          <span v-if="item.preu_total">{{ item.preu_total }}€</span>
          <span v-else>—</span>
        </template>
        <template #cell-created_at="{ item }">
          {{ formatDate(item.created_at) }}
        </template>
        <template #actions="{ item }">
          <button @click="showDetails(item)" class="btn btn-icon btn-view" title="Veure detalls">
            Veure
          </button>
        </template>
      </AdminTable>

      <AdminModal v-model="showDetailsModal" title="Detalls de la Reserva" size="large">
        <div v-if="selectedReserva" class="reserva-details">
          <div class="detail-section">
            <h3 class="detail-title">Informació de la Reserva</h3>
            <div class="detail-grid">
              <div class="detail-item">
                <span class="detail-label">ID:</span>
                <span class="detail-value">{{ selectedReserva.id }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Estat:</span>
                <span class="badge" :class="`badge-${selectedReserva.estat}`">
                  {{ formatEstat(selectedReserva.estat) }}
                </span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Preu Total:</span>
                <span class="detail-value">{{ selectedReserva.preu_total || '—' }}€</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Data Creació:</span>
                <span class="detail-value">{{ formatDate(selectedReserva.created_at) }}</span>
              </div>
            </div>
          </div>

          <div class="detail-section">
            <h3 class="detail-title">Usuari</h3>
            <div class="detail-grid">
              <div v-if="selectedReserva.usuari" class="detail-item full-width">
                <span class="detail-label">Nom:</span>
                <span class="detail-value">{{ selectedReserva.usuari.nom }}</span>
              </div>
              <div v-if="selectedReserva.email" class="detail-item full-width">
                <span class="detail-label">Email:</span>
                <span class="detail-value">{{ selectedReserva.email }}</span>
              </div>
              <div v-if="selectedReserva.guest_id" class="detail-item full-width">
                <span class="detail-label">Guest ID:</span>
                <span class="detail-value guest-id">{{ selectedReserva.guest_id }}</span>
              </div>
            </div>
          </div>

          <div v-if="selectedReserva.sessio_id" class="detail-section">
            <h3 class="detail-title">Sessió</h3>
            <div class="detail-grid">
              <div class="detail-item">
                <span class="detail-label">ID Sessió:</span>
                <span class="detail-value">{{ selectedReserva.sessio_id }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Sala:</span>
                <span class="detail-value">{{ getSalaNom(selectedReserva.sala_id) }}</span>
              </div>
              <div class="detail-item full-width">
                <span class="detail-label">Data/Hora:</span>
                <span class="detail-value">{{ formatDateTime(selectedReserva.data_hora) }}</span>
              </div>
            </div>
          </div>

          <div v-if="selectedReserva.seients?.length" class="detail-section">
            <h3 class="detail-title">Seients Reservats</h3>
            <div class="seients-list">
              <span v-for="seient in selectedReserva.seients" :key="seient.id" class="seient-badge">
                {{ seient.fila }}{{ seient.numero }}
              </span>
            </div>
          </div>
        </div>
      </AdminModal>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { getReservesAll } from '~/services/communicationManager'

definePageMeta({
  middleware: 'admin'
})

const reserves = ref([])
const loading = ref(true)
const error = ref(null)

const showDetailsModal = ref(false)
const selectedReserva = ref(null)

const columns = [
  { key: 'id', label: 'ID', width: '60px' },
  { key: 'usuari', label: 'Usuari/Email' },
  { key: 'sessio_id', label: 'ID Sessió', width: '100px' },
  { key: 'data_hora', label: 'Data Sessió', width: '180px' },
  { key: 'estat', label: 'Estat', width: '120px' },
  { key: 'preu_total', label: 'Preu', width: '80px' },
  { key: 'created_at', label: 'Data Reserva', width: '150px' }
]

function formatEstat(estat) {
  const estats = {
    'pendent': 'Pendent',
    'confirmada': 'Confirmada',
    'caducada': 'Caducada'
  }
  return estats[estat] || estat || '—'
}

function formatDate(dateStr) {
  if (!dateStr) return '—'
  const date = new Date(dateStr)
  return date.toLocaleDateString('ca-ES')
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

function formatSessio(reserva) {
  if (!reserva.data_hora) return '—'
  return formatDateTime(reserva.data_hora)
}

function getPeliculaTitle(id) {
  return id || '—'
}

function getSalaNom(id) {
  return id ? `Sala ${id}` : '—'
}

function showDetails(reserva) {
  selectedReserva.value = reserva
  showDetailsModal.value = true
}

async function fetchReserves() {
  loading.value = true
  error.value = null
  try {
    reserves.value = await getReservesAll()
  } catch (err) {
    error.value = err.message || 'Error en carregar reserves'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchReserves()
})
</script>

<style scoped>
.page-subtitle {
  color: var(--color-muted);
  font-size: 0.9rem;
  margin: 4px 0 0 0;
}

.guest-email {
  color: var(--color-muted);
  font-style: italic;
}

.unknown {
  color: var(--color-muted);
}

.reserva-details {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.detail-section {
  border-bottom: 1px solid var(--color-border);
  padding-bottom: 16px;
}

.detail-section:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.detail-title {
  font-family: var(--font-title);
  font-size: 1rem;
  color: var(--color-text);
  margin: 0 0 12px 0;
}

.detail-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
}

.detail-item {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.detail-item.full-width {
  grid-column: 1 / -1;
}

.detail-label {
  font-size: 0.8rem;
  color: var(--color-muted);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.detail-value {
  color: var(--color-text);
}

.guest-id {
  font-family: monospace;
  font-size: 0.85rem;
  color: var(--color-muted);
}

.seients-list {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.seient-badge {
  background: var(--color-accent);
  color: white;
  padding: 6px 12px;
  border-radius: var(--radius-sm);
  font-weight: 500;
}
</style>
