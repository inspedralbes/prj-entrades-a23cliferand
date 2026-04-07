<template>
  <section class="banner" :style="{ backgroundImage: `url(${imatgeFons})` }">
    <div class="banner_overlay"></div>
    <div class="container banner_content">
      <h1 class="banner_title">{{ titol }}</h1>
      <p class="banner_desc">{{ descripcio }}</p>
      <div class="banner_meta">
        <span class="banner_meta-item"><svg-icon type="mdi" :path="mdiClockOutline" size="18"></svg-icon> {{ durada }}
          min</span>
        <span class="banner_meta-dot">·</span>
        <span class="banner_meta-item"><svg-icon type="mdi" :path="mdiStar" size="18"></svg-icon> {{ puntuacio }}</span>
        <span class="banner_meta-dot">·</span>
        <span class="banner_meta-item insignia insignia-rated">{{ puntuacio }}</span>
      </div>
      <NuxtLink :to="`/pelicula/${id}`" class="btn btn-primary banner_cta">Comprar entrades</NuxtLink>
    </div>
  </section>
</template>

<script setup>
import SvgIcon from '@jamescoyle/vue-icon'
import { mdiStar, mdiClockOutline } from '@mdi/js'
defineProps({
  id: String,
  titol: String,
  descripcio: String,
  imatgeFons: String,
  genere: String,
  durada: Number,
  puntuacio: String
})
</script>

<style scoped>
.banner {
  position: relative;
  height: 520px;
  background-size: cover;
  background-position: center 20%;
  display: flex;
  align-items: flex-end;
}

.banner_overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top,
      var(--color-bg) 0%,
      rgba(13, 13, 15, 0.7) 50%,
      rgba(13, 13, 15, 0.1) 100%);
}

.banner_content {
  position: relative;
  padding-bottom: 52px;
  display: flex;
  flex-direction: column;
  gap: 12px;
  max-width: 600px;
}

.banner_title {
  font-family: var(--font-title);
  font-size: 3.8rem;
  letter-spacing: 3px;
  line-height: 1.05;
}

.banner_desc {
  font-size: 0.95rem;
  color: rgba(240, 240, 245, 0.8);
  line-height: 1.7;
}

.banner_meta {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 0.88rem;
  color: var(--color-muted);
}

.banner_meta-item {
  color: var(--color-muted);
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 4px;
}

.banner_meta-dot {
  color: var(--color-border);
}

.banner_cta {
  margin-top: 8px;
  align-self: flex-start;
  padding: 12px 32px;
  font-size: 1.05rem;
}
</style>
