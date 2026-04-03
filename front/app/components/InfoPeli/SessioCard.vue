<template>
  <div class="sessio-card">
    <div class="sessio-card_hora">
      <span class="hora_data">{{ sessio.diaSemana }} {{ sessio.numDia }} {{ sessio.mesSessio }}</span>
      <span class="hora_time">{{ sessio.hora }}</span>
    </div>

    <div class="sessio-card_info">
      <span class="sessio-card_sala">{{ sessio.sala }}</span>
      <div class="sessio-card_badges">
        <span v-if="sessio.idioma" class="insignia insignia-genere">{{ sessio.idioma }}</span>
        <span v-if="sessio.subTipus" class="insignia insignia-rated">{{ sessio.subTipus }}</span>
      </div>
    </div>

    <!-- Barra d'ocupació -->
    <div v-if="sessio.ocupacioPercent !== null" class="sessio-card_ocupacio">
      <div class="bar">
        <div class="bar_fill" :class="barClass(sessio.ocupacioPercent)"
          :style="{ width: sessio.ocupacioPercent + '%' }" />
      </div>
      <div class="bar_info">
        <span class="bar_label">{{ sessio.placesLliures }} llocs · {{ sessio.ocupacioPercent }}%</span>
        <span class="bar_details" v-if="sessio.totalSeients">
          {{ sessio.seientVenuts }} venuts · {{ sessio.seientReservats }} reservats
        </span>
      </div>
    </div>

    <button class="btn btn-primary sessio-card_btn" :disabled="sessio.esPassat"
      :class="{ 'sessio-card_btn--disabled': sessio.esPassat }">
      {{ sessio.esPassat ? 'No disponible' : 'Comprar' }}
    </button>
  </div>
</template>

<script setup>
defineProps({
  sessio: {
    type: Object,
    required: true
  }
})

function barClass(pct) {
  if (pct >= 80) return 'bar_fill--full'
  if (pct >= 50) return 'bar_fill--mid'
  return 'bar_fill--low'
}
</script>

<style scoped>
.sessio-card {
  display: flex;
  align-items: center;
  gap: 20px;
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  padding: 18px 22px;
  transition: border-color var(--transition), transform var(--transition);
}

.sessio-card:hover {
  border-color: rgba(232, 39, 46, 0.35);
  transform: translateX(4px);
}

.sessio-card_hora {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 2px;
  min-width: 70px;
}

.hora_data {
  font-size: 0.7rem;
  font-weight: 600;
  color: var(--color-muted);
  letter-spacing: 0.5px;
}

.hora_time {
  font-size: 1.6rem;
  font-weight: 800;
  color: var(--color-text);
  font-family: var(--font-title);
  letter-spacing: 1px;
}

.sessio-card_info {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.sessio-card_sala {
  font-size: 0.9rem;
  color: var(--color-muted);
}

.sessio-card_badges {
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
}

.sessio-card_ocupacio {
  display: flex;
  flex-direction: column;
  gap: 6px;
  flex: 1;
  min-width: 150px;
}

.bar {
  height: 6px;
  border-radius: 3px;
  background: var(--color-border);
  overflow: hidden;
}

.bar_fill {
  height: 100%;
  border-radius: 3px;
  transition: width 0.4s ease;
}

.bar_fill--low {
  background: #22c55e;
}

.bar_fill--mid {
  background: var(--color-gold);
}

.bar_fill--full {
  background: var(--color-accent);
}

.bar_info {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.bar_label {
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--color-text);
}

.bar_details {
  font-size: 0.65rem;
  color: var(--color-muted);
}

.sessio-card_btn {
  flex-shrink: 0;
}

@media (max-width: 768px) {
  .sessio-card {
    flex-wrap: wrap;
    gap: 12px;
  }

  .sessio-card_hora {
    font-size: 1.3rem;
    min-width: auto;
  }
}
</style>
