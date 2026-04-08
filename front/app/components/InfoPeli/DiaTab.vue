<template>
  <button class="dia-tab" :class="{
    'dia-tab--active': actiu,
    'dia-tab--disabled': dia.esPassat
  }" :disabled="dia.esPassat" @click="$emit('select', dia.valor)">
    <span class="dia-tab_label">{{ dia.label }}</span>
    <span class="dia-tab_num">{{ dia.num }}</span>
    <span class="dia-tab_mes">{{ dia.mes }}</span>
  </button>
</template>

<script setup>
defineProps({
  dia: Object,
  actiu: Boolean
})

defineEmits(['select'])
</script>

<style scoped>
.dia-tab {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 2px;
  padding: 12px 22px;
  border-radius: var(--radius-md);
  border: 1px solid var(--color-border);
  background: var(--color-surface2);
  cursor: pointer;
  transition: border-color var(--transition), background var(--transition);
  position: relative;
}

.dia-tab--active {
  border-color: var(--color-accent);
  background: rgba(232, 39, 46, 0.1);
}

.dia-tab_label {
  font-size: 0.72rem;
  color: var(--color-muted);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.dia-tab_num {
  font-size: 1.4rem;
  font-weight: 800;
  color: var(--color-text);
  line-height: 1;
}

.dia-tab_mes {
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--color-muted);
  text-transform: capitalize;
}

.dia-tab--active .dia-tab_label,
.dia-tab--active .dia-tab_num,
.dia-tab--active .dia-tab_mes {
  color: var(--color-accent);
}

.dia-tab--disabled {
  opacity: 0.5;
  cursor: not-allowed;
  pointer-events: none;
  background: transparent;
  border-color: rgba(255, 255, 255, 0.05);
}

.dia-tab--disabled .dia-tab_count {
  background: var(--color-muted);
}
</style>
