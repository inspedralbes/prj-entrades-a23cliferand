<template>
  <div class="detall_meta-wrap">
    <div class="detall_badges">
      <span v-for="g in pelicula.generes" :key="g" class="insignia insignia-genere">{{ g }}</span>
      <span v-if="pelicula.tipus" class="insignia insignia-rated">{{ pelicula.tipus }}</span>
    </div>

    <h1 class="detall_title">{{ pelicula.titol }}</h1>
    <p v-if="pelicula.titolOriginal && pelicula.titolOriginal !== pelicula.titol" class="detall_original">
      {{ pelicula.titolOriginal }}
    </p>

    <div class="detall_stats">
      <div class="stat">
        <span class="stat_icon"><svg-icon type="mdi" :path="mdiStar"></svg-icon></span>
        <span class="stat_value">{{ pelicula.puntuacio }}</span>
        <span class="stat_label">Puntuació</span>
      </div>
      <div class="stat-divider" />
      <div class="stat">
        <span class="stat_icon"><svg-icon type="mdi" :path="mdiClockOutline"></svg-icon></span>
        <span class="stat_value">{{ pelicula.duracio }}'</span>
        <span class="stat_label">Durada</span>
      </div>
      <div class="stat-divider" />
      <div class="stat">
        <span class="stat_icon"><svg-icon type="mdi" :path="mdiCalendarBlank"></svg-icon></span>
        <span class="stat_value">{{ pelicula.any }}</span>
        <span class="stat_label">Any</span>
      </div>
      <div v-if="pelicula.vots" class="stat-divider" />
      <div v-if="pelicula.vots" class="stat">
        <span class="stat_icon"><svg-icon type="mdi" :path="mdiVote"></svg-icon></span>
        <span class="stat_value">{{ formatVots(pelicula.vots) }}</span>
        <span class="stat_label">Vots</span>
      </div>
    </div>

    <p class="detall_sinopsi">{{ pelicula.sinopsi }}</p>
  </div>
</template>

<script setup>
import SvgIcon from '@jamescoyle/vue-icon'
import { mdiStar, mdiClockOutline, mdiCalendarBlank, mdiVote } from '@mdi/js'

defineProps({
  pelicula: {
    type: Object,
    required: true
  }
})

function formatVots(n) {
  if (n >= 1_000_000) return (n / 1_000_000).toFixed(1) + 'M'
  if (n >= 1_000) return (n / 1_000).toFixed(0) + 'K'
  return String(n)
}
</script>

<style scoped>
.detall_meta-wrap {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.detall_badges {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}

.detall_title {
  font-family: var(--font-title);
  font-size: 4rem;
  letter-spacing: 3px;
  line-height: 1;
  color: var(--color-text);
}

.detall_original {
  font-size: 0.88rem;
  color: var(--color-muted);
  font-style: italic;
}

.detall_stats {
  display: flex;
  align-items: center;
  gap: 20px;
  flex-wrap: wrap;
}

.stat {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 2px;
}

.stat_icon {
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--color-muted);
}
.stat_icon svg {
  width: 24px;
  height: 24px;
}

.stat_value {
  font-size: 1.3rem;
  font-weight: 800;
  color: var(--color-text);
}

.stat_label {
  font-size: 0.7rem;
  color: var(--color-muted);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.stat-divider {
  width: 1px;
  height: 36px;
  background: var(--color-border);
}

.detall_sinopsi {
  font-size: 0.97rem;
  color: rgba(240, 240, 245, 0.82);
  line-height: 1.75;
  max-width: 600px;
}

@media (max-width: 768px) {
  .detall_title {
    font-size: 2.6rem;
  }
}
</style>
