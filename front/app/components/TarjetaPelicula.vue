<template>
  <NuxtLink :to="`/pelicula/${pelicula.id}`" class="tarjeta">
    <!-- Poster -->
    <div class="tarjeta_poster-wrap">
      <img class="tarjeta_poster aspect-poster" :src="pelicula.poster" :alt="`Pòster de ${pelicula.titol}`" loading="lazy" />
      <!-- Overlay hover -->
      <div class="tarjeta_overlay">
        <span class="btn btn-primary tarjeta_btn-sessions">Veure detalls</span>
      </div>
      <!-- Rating  -->
      <span class="tarjeta_rating"><svg-icon type="mdi" :path="mdiStar" size="14" class="icon"></svg-icon> {{ pelicula.puntuacio }}</span>
    </div>

    <!-- Info -->
    <div class="tarjeta_info">
      <div class="tarjeta_genres">
        <span v-for="genere in pelicula.generes" :key="genere" class="insignia insignia-genere">{{ genere }}</span>
      </div>
      <h3 class="tarjeta_title">{{ pelicula.titol }}</h3>
      <p class="tarjeta_meta">
        <span class="meta-item"><svg-icon type="mdi" :path="mdiClockOutline" size="14" class="icon"></svg-icon> {{ pelicula.duracio }} min</span>
        <span class="tarjeta_dot">·</span>
        <span>{{ pelicula.any }}</span>
      </p>
    </div>
  </NuxtLink>
</template>

<script setup>
import SvgIcon from '@jamescoyle/vue-icon'
import { mdiStar, mdiClockOutline } from '@mdi/js'

defineProps({
  pelicula: {
    type: Object,
    required: true
  }
})
</script>

<style scoped>
.tarjeta {
  background: var(--color-surface);
  border-radius: var(--radius-md);
  overflow: hidden;
  border: 1px solid var(--color-border);
  cursor: pointer;
  transition: transform var(--transition), box-shadow var(--transition), border-color var(--transition);
  display: flex;
  flex-direction: column;
}

.tarjeta:hover {
  transform: translateY(-6px);
  box-shadow: var(--shadow-card);
  border-color: rgba(232, 39, 46, 0.4);
}

/* Poster */
.tarjeta_poster-wrap {
  position: relative;
  overflow: hidden;
}

.tarjeta_poster {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.4s ease;
}

.tarjeta:hover .tarjeta_poster {
  transform: scale(1.06);
}

/* Overlay */
.tarjeta_overlay {
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.55);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity var(--transition);
}

.tarjeta:hover .tarjeta_overlay {
  opacity: 1;
}

.tarjeta_btn-sessions {
  font-size: 0.9rem;
}

/* Rating badge */
.tarjeta_rating {
  position: absolute;
  top: 10px;
  right: 10px;
  background: rgba(13, 13, 15, 0.85);
  backdrop-filter: blur(4px);
  color: var(--color-gold);
  padding: 4px 8px;
  border-radius: var(--radius-sm);
  font-size: 0.85rem;
  font-weight: 700;
  z-index: 2;
  display: flex;
  align-items: center;
  gap: 4px;
}

/* Info section */
.tarjeta_info {
  padding: 14px 16px 18px;
  display: flex;
  flex-direction: column;
  gap: 6px;
  flex: 1;
}

.tarjeta_genres {
  display: flex;
  flex-wrap: wrap;
  gap: 5px;
}

.tarjeta_title {
  font-size: 1rem;
  font-weight: 700;
  color: var(--color-text);
  line-height: 1.3;
  margin-top: 2px;
}

.tarjeta_meta {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.82rem;
  color: var(--color-muted);
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 4px;
}

.tarjeta_dot {
  color: var(--color-border);
}
</style>
