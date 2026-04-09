<template>
  <div>
    <div v-if="error" class="container api-error api-error--centered">
      <small>Error al servidor</small>
    </div>

    <!-- Banner amb una peli -->
    <Banner v-if="!carregant && pelicules.length > 0" :id="peliculaDestacada.id" :titol="peliculaDestacada.titol"
      :descripcio="peliculaDestacada.sinopsi" :imatge-fons="peliculaDestacada.backdrop || peliculaDestacada.poster"
      :genere="peliculaDestacada.generes[0] ?? ''" :durada="peliculaDestacada.duracio"
      :puntuacio="peliculaDestacada.puntuacio" puntuacio="+16" />

    <div v-if="carregant" class="transicio banner-transicio" />

    <!-- Cartellera -->
    <section class="cartelera section-spacing">
      <div class="container">
        <div class="section-header">
          <div>
            <h2 class="section-titol">Cartellera</h2>
          </div>
          <!-- Filtres -->
          <div v-if="!carregant" class="cartelera_filters">
            <button v-for="genere in generesDisponibles" :key="genere" class="filter-btn"
              :class="{ 'filter-btn--active': genereActiu === genere }" @click="genereActiu = genere">
              {{ genere }}
            </button>
          </div>
        </div>

        <!-- Animació mentre carrega -->
        <div v-if="carregant" class="cartelera_grid">
          <div v-for="i in 6" :key="i" class="transicio transicio-card" />
        </div>

        <!-- Grid de pel·lícules -->
        <div v-else class="cartelera_grid">
          <TarjetaPelicula v-for="pelicula in peliculesFiltrades" :key="pelicula.id" :pelicula="pelicula" />
          <p v-if="peliculesFiltrades.length === 0 && !carregant" class="cartelera_empty">
            No hi ha pel·lícules.
          </p>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { getPeliculesAll } from '~/services/communicationManager'
import { useAppConstants } from '~/composables/useAppConstants'

const { appName } = useAppConstants()

useHead({
  title: `${appName} — Cartellera`,
  meta: [
    { name: 'description', content: 'Consulta la cartellera del cinema Cinema Paradise. Compra les teves entrades en línia per les millors pel·lícules.' }
  ]
})

const pelicules = ref([])
const carregant = ref(true)
const error = ref(null)

onMounted(async () => {
  try {
    carregant.value = true
    error.value = null
    pelicules.value = await getPeliculesAll()
  } catch (err) {
    error.value = err.message ?? 'Error en carregar les dades del servidor.'
    console.error('[index.vue] Error API:', err)
  } finally {
    carregant.value = false
  }
})

// Pel·lícula del banner
const peliculaDestacada = computed(() => pelicules.value[0])

// Filtre
const generesDisponibles = computed(() => {
  const tots = new Set(['Tots'])
  pelicules.value.forEach(p => p.generes.forEach(g => tots.add(g)))
  return Array.from(tots)
})

const genereActiu = ref('Tots')

const peliculesFiltrades = computed(() =>
  genereActiu.value === 'Tots'
    ? pelicules.value
    : pelicules.value.filter(p => p.generes.includes(genereActiu.value))
)
</script>

<style scoped>
.section-spacing {
  padding: 56px 0;
}

/* Section header con filtros */
.section-header {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
  margin-bottom: 32px;
}

/* Filters */
.cartelera_filters {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.filter-btn {
  padding: 6px 16px;
  border-radius: 50px;
  border: 1px solid var(--color-border);
  background: var(--color-surface2);
  color: var(--color-muted);
  font-size: 0.82rem;
  font-weight: 500;
  transition: border-color var(--transition), color var(--transition), background var(--transition);
}

.filter-btn:hover {
  border-color: rgba(232, 39, 46, 0.4);
  color: var(--color-text);
}

.filter-btn--active {
  background: rgba(232, 39, 46, 0.15);
  border-color: var(--color-accent);
  color: var(--color-accent);
}

.cartelera_grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 20px;
}

.cartelera_empty {
  grid-column: 1 / -1;
  text-align: center;
  color: var(--color-muted);
  padding: 48px 0;
  font-size: 0.95rem;
}

.banner-transicio {
  height: 520px;
  border-radius: 0;
}

.transicio-card {
  aspect-ratio: 2 / 3;
  border: 1px solid var(--color-border);
}



@media (max-width: 480px) {
  .cartelera_grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
  }

  .section-header {
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>
