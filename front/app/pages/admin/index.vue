<template>
  <div class="admin-page">
    <AdminSidebar />
    <div class="admin-dashboard">
      <h1 class="dashboard-title">Dashboard</h1>

      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-value">{{ stats.sales }}</div>
          <div class="stat-label">Reserves d'avui</div>
        </div>
        <div class="stat-card">
          <div class="stat-value">{{ stats.sessions }}</div>
          <div class="stat-label">Sessions d'avui</div>
        </div>
        <div class="stat-card">
          <div class="stat-value">{{ stats.pelicules }}</div>
          <div class="stat-label">Pel·lícules</div>
        </div>
        <div class="stat-card">
          <div class="stat-value">{{ stats.reserves }}</div>
          <div class="stat-label">Reserves</div>
        </div>
      </div>

      <div class="charts-grid" v-if="hasData">
        <div class="chart-card">
          <h3 class="chart-title">Entrades per dia (últims 7 dies)</h3>
          <div class="chart-container">
            <Bar :data="barChartData" :options="chartOptions" />
          </div>
        </div>
        <div class="chart-card">
          <h3 class="chart-title">Reserves per estat</h3>
          <div class="chart-container">
            <Doughnut :data="doughnutChartData" :options="chartOptions" />
          </div>
        </div>
      </div>

      <div class="no-data" v-else-if="!loading && !hasData">
        <p class="no-data-text">No hi ha dades disponibles</p>
      </div>

      <div class="loading" v-if="loading">
        <LoadingSpinner />
        <p>Carregant...</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Bar, Doughnut } from 'vue-chartjs'
import LoadingSpinner from '~/components/LoadingSpinner.vue'
import { getAdminStats } from '~/services/communicationManager'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
  ArcElement
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement)

definePageMeta({
  middleware: 'admin'
})

const stats = ref({
  sales: 0,
  sessions: 0,
  pelicules: 0,
  reserves: 0
})

const dailyReserves = ref([])
const reservesByState = ref({ pendent: 0, confirmada: 0, caducada: 0 })
const loading = ref(true)
const error = ref(null)

const hasData = computed(() => {
  return stats.value.sales > 0 ||
    stats.value.sessions > 0 ||
    stats.value.pelicules > 0 ||
    stats.value.reserves > 0 ||
    dailyReserves.value.length > 0
})

const barChartData = computed(() => ({
  labels: dailyReserves.value.map(d => d.day),
  datasets: [{
    label: 'Entrades',
    data: dailyReserves.value.map(d => d.count),
    backgroundColor: '#e8272e',
    borderColor: '#e8272e',
    borderRadius: 6
  }]
}))

const doughnutChartData = computed(() => ({
  labels: ['Pendent', 'Confirmada', 'Caducada'],
  datasets: [{
    data: [
      reservesByState.value.pendent,
      reservesByState.value.confirmada,
      reservesByState.value.caducada
    ],
    backgroundColor: ['#f5c842', '#4caf50', '#999999'],
    borderWidth: 0
  }]
}))

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      labels: {
        color: '#f0f0f5'
      }
    }
  },
  scales: {
    x: {
      ticks: { color: '#8a8a9a' },
      grid: { color: '#2a2a35' }
    },
    y: {
      ticks: { color: '#8a8a9a' },
      grid: { color: '#2a2a35' }
    }
  }
}

const fetchStats = async () => {
  try {
    const data = await getAdminStats()

    if (!data || typeof data !== 'object') {
      throw new Error('Resposta de estadístiques invàlida')
    }

    stats.value = data.stats || { sales: 0, sessions: 0, pelicules: 0, reserves: 0 }
    dailyReserves.value = Array.isArray(data.daily_reserves) ? data.daily_reserves : []
    reservesByState.value = data.reserves_by_state || { pendent: 0, confirmada: 0, caducada: 0 }

    console.log('Estadístiques carregades (raw):', JSON.stringify(data))
  } catch (err) {
    error.value = err.message
    console.error('Error loading stats:', err)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchStats()
})
</script>

<style scoped>
.admin-dashboard {
  padding: 20px;
  min-width: 0;
}

.dashboard-title {
  font-family: var(--font-title);
  font-size: 2rem;
  color: var(--color-text);
  margin-bottom: 24px;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 16px;
  margin-bottom: 32px;
}

.stat-card {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  padding: 20px;
  text-align: center;
}

.stat-value {
  font-size: 2.5rem;
  font-weight: 700;
  color: var(--color-accent);
}

.stat-label {
  font-size: 0.9rem;
  color: var(--color-muted);
  margin-top: 4px;
}

.charts-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
  gap: 24px;
}

.chart-card {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  padding: 20px;
}

.chart-title {
  font-family: var(--font-title);
  font-size: 1.1rem;
  color: var(--color-text);
  margin-bottom: 16px;
}

.chart-container {
  height: 300px;
}

.no-data {
  text-align: center;
  padding: 40px;
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
}

.no-data-text {
  color: var(--color-muted);
  font-size: 1.1rem;
}

.loading {
  text-align: center;
  padding: 40px;
  color: var(--color-muted);
}
</style>