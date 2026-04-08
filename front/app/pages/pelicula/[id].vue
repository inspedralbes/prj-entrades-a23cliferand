<template>
  <div class="detall">

    <!-- Peli Header -->
    <div class="peli-header"
      :style="pelicula ? { backgroundImage: `url(${pelicula.backdrop || pelicula.poster})` } : {}">
      <div class="peli-header-overlay" />

      <!-- Botó tornar -->
      <div class="container detall_topbar">
        <NuxtLink to="/" class="detall_back">
          ← Cartellera
        </NuxtLink>
      </div>

      <!-- Transició  -->
      <div v-if="carregant" class="container detall_main detall_main--transicio">
        <div class="transicio transicio_poster" />
        <div class="detall_meta-wrap">
          <div class="transicio transicio_badge" />
          <div class="transicio transicio_title" />
          <div class="transicio transicio_subtitle" />
          <div class="transicio transicio_text" />
          <div class="transicio transicio_text transicio_text-short" />
        </div>
      </div>

      <!-- Contingut -->
      <div v-else-if="pelicula" class="container detall_main">
        <img class="detall_poster" :src="pelicula.poster" :alt="`Pòster de ${pelicula.titol}`" />

        <Metadades :pelicula="pelicula" />
      </div>
    </div>

    <!-- Sessions -->
    <section class="sessions-section container">
      <h2 class="section-titol">Sessions disponibles</h2>
      <p class="section-subtitol">Selecciona el dia i compra la teva entrada</p>

      <div v-if="errorSessions" class="api-error">
        {{ errorSessions }}
      </div>

      <div v-else-if="carregantSessions" class="sessions-transicio">
        <div v-for="i in 3" :key="i" class="transicio transicio_session-card" />
      </div>

      <div v-else-if="sessions.length === 0" class="sessions-empty">
        <p>No hi ha sessions programades per a aquesta pel·lícula.</p>
      </div>

      <!-- Sessions agrupades per dia -->
      <template v-else>
        <!-- Selector de dia -->
        <div class="dia-tabs">
          <DiaTab v-for="dia in diesDisponibles" :key="dia.valor" :dia="dia" :actiu="diaActiu === dia.valor"
            @select="diaActiu = $event" />
        </div>

        <div class="sessions-grid">
          <SessioCard v-for="sessio in sessionsDiaActiu" :key="sessio.id" :sessio="sessio" />

          <p v-if="sessionsDiaActiu.length === 0" class="sessions-empty_day">
            No hi ha sessions per a aquest dia.
          </p>
        </div>
      </template>
    </section>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch, onUnmounted } from 'vue'
import { getPeliculesById, getSessionsByPelicula } from '~/services/communicationManager'
import { socket } from '~/services/socket'
import SessioCard from '~/components/InfoPeli/SessioCard.vue'
import DiaTab from '~/components/InfoPeli/DiaTab.vue'
import Metadades from '~/components/InfoPeli/Metadades.vue'

const route = useRoute()
const id = route.params.id

const pelicula = ref(null)
const sessions = ref([])
const carregant = ref(true)
const carregantSessions = ref(true)
const errorSessions = ref(null)

useHead({
  title: computed(() => pelicula.value ? `${pelicula.value.titol} — Cinema Paradise` : 'Cinema Paradise'),
  meta: [
    {
      name: 'description',
      content: computed(() => pelicula.value?.sinopsi ?? 'Detalls i sessions de la pel·lícula')
    }
  ]
})

onMounted(async () => {
  // Pel·lícula
  try {
    pelicula.value = await getPeliculesById(id)
  } catch (e) {
    console.error('Error carregant pel·lícula:', e)
  } finally {
    carregant.value = false
  }

  // Sessions
  try {
    sessions.value = await getSessionsByPelicula(id)
  } catch (e) {
    errorSessions.value = e.message ?? 'Error en carregar les sessions.'
  } finally {
    carregantSessions.value = false
  }

  // Configurar listeners de socket per a actualitzacions
  setupSocketListeners()
})

function setupSocketListeners() {
  socket.on('seats-updated', (data) => {
    console.log('Actualització de seients detectada:', data)
    // Validem que sigui de la sessió actual
    if (data.session_id) {
      recarregarSessions()
    }
  })

  socket.on('seats-released', (data) => {
    console.log('Seients alliberats detectats:', data)
    // Validem que sigui de la sessió actual
    if (data.session_id) {
      recarregarSessions()
    }
  })
}


async function recarregarSessions() {
  try {
    sessions.value = await getSessionsByPelicula(id)
  } catch (e) {
    console.error('Error recarregant sessions:', e)
  }
}

onUnmounted(() => {
  socket.off('seats-updated')
  socket.off('seats-released')
})

const diesDisponibles = computed(() => {
  const nomsDia = ['Dg', 'Dl', 'Dm', 'Dc', 'Dj', 'Dv', 'Ds']
  const nomsMes = ['Gen', 'Feb', 'Març', 'Abr', 'Maig', 'Juny', 'Jul', 'Ago', 'Set', 'Oct', 'Nov', 'Des']
  const map = new Map()
  const avuiDate = new Date()
  avuiDate.setHours(0, 0, 0, 0)

  sessions.value.forEach(sessio => {
    if (!sessio.dia) return

    if (!map.has(sessio.dia)) {
      const dataSessio = new Date(sessio.dia)
      dataSessio.setHours(0, 0, 0, 0)
      const diff = Math.round((dataSessio.getTime() - avuiDate.getTime()) / 86_400_000)

      let label = nomsDia[dataSessio.getDay()]
      if (diff === 0) label = 'Avui'
      else if (diff === 1) label = 'Demà'

      map.set(sessio.dia, {
        valor: sessio.dia,
        label,
        num: dataSessio.getDate(),
        mes: nomsMes[dataSessio.getMonth()],
        esPassat: sessio.esPassat,
        count: 0
      })
    }

    map.get(sessio.dia).count++
  })

  // Retornem ordenat per data
  return Array.from(map.values()).sort((a, b) => a.valor.localeCompare(b.valor))
})

const diaActiu = ref('')

// Ajusta el dia actiu al primer dia vàlid
watch(diesDisponibles, (dies) => {
  if (dies.length > 0 && !diaActiu.value) {
    const primerValid = dies.find(d => !d.esPassat) || dies[0]
    diaActiu.value = primerValid.valor
  }
})

const sessionsDiaActiu = computed(() =>
  sessions.value.filter(s => s.dia === diaActiu.value)
)

</script>

<style scoped>
.peli-header {
  position: relative;
  min-height: 520px;
  background-size: cover;
  background-position: center 20%;
  display: flex;
  flex-direction: column;
}

.peli-header-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to bottom,
      rgba(13, 13, 15, 0.55) 0%,
      rgba(13, 13, 15, 0.85) 60%,
      var(--color-bg) 100%);
}

.detall_topbar {
  position: relative;
  padding-top: 24px;
  padding-bottom: 8px;
}

.detall_back {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 0.88rem;
  font-weight: 600;
  color: var(--color-muted);
  letter-spacing: 0.5px;
  transition: color var(--transition);
}

.detall_back:hover {
  color: var(--color-text);
}

/* Main content */
.detall_main {
  position: relative;
  display: flex;
  gap: 40px;
  align-items: flex-end;
  padding-bottom: 48px;
  padding-top: 16px;
  flex: 1;
}

.detall_poster {
  width: 220px;
  flex-shrink: 0;
  border-radius: var(--radius-md);
  box-shadow: var(--shadow-card);
  border: 1px solid var(--color-border);
}

/* Sessions */
.sessions-section {
  padding: 48px 0 80px;
}

.sessions-grid {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.sessions-empty {
  text-align: center;
  padding: 60px 0;
  color: var(--color-muted);
  font-size: 1.05rem;
}

.sessions-empty_day {
  text-align: center;
  color: var(--color-muted);
  padding: 32px 0;
  font-size: 0.95rem;
}

/* Dia tabs */
.dia-tabs {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  margin-bottom: 28px;
}


/* Transicions */
.transicio_poster {
  width: 220px;
  flex-shrink: 0;
  aspect-ratio: 2/3;
}

.transicio_badge {
  width: 80px;
  height: 24px;
}

.transicio_title {
  width: 60%;
  height: 64px;
}

.transicio_subtitle {
  width: 30%;
  height: 18px;
}

.transicio_text {
  width: 100%;
  height: 14px;
}

.transicio_text-short {
  width: 80%;
}

.transicio_session-card {
  width: 100%;
  height: 80px;
  border-radius: var(--radius-md);
}

.detall_main--transicio {
  align-items: flex-start;
  padding-top: 40px;
  gap: 32px;
}

.sessions-transicio {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

@media (max-width: 768px) {
  .detall_main {
    flex-direction: column;
    align-items: flex-start;
    gap: 24px;
  }

}
</style>
