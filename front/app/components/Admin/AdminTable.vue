<template>
  <div class="admin-table-container">
    <div v-if="loading" class="loading-state">
      <LoadingSpinner />
      <p>Carregant...</p>
    </div>

    <div v-else-if="error" class="error-state">
      <p class="error-message">{{ error }}</p>
      <button @click="$emit('retry')" class="btn btn-secondary">Reintentar</button>
    </div>

    <div v-else-if="data.length === 0" class="empty-state">
      <p class="empty-message">{{ emptyMessage }}</p>
    </div>

    <table v-else class="admin-table">
      <thead>
        <tr>
          <th v-for="column in columns" :key="column.key" :style="{ width: column.width }">
            {{ column.label }}
          </th>
          <th v-if="$slots.actions" class="actions-header">Accions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in data" :key="getItemKey(item, index)">
          <td v-for="column in columns" :key="column.key">
            <slot :name="`cell-${column.key}`" :item="item" :value="getNestedValue(item, column.key)">
              {{ column.format ? column.format(item) : getNestedValue(item, column.key) }}
            </slot>
          </td>
          <td v-if="$slots.actions" class="actions-cell">
            <slot name="actions" :item="item" />
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import LoadingSpinner from '~/components/LoadingSpinner.vue'

const props = defineProps({
  data: {
    type: Array,
    required: true
  },
  columns: {
    type: Array,
    required: true
  },
  loading: {
    type: Boolean,
    default: false
  },
  error: {
    type: String,
    default: null
  },
  emptyMessage: {
    type: String,
    default: 'No hi ha dades disponibles'
  },
  rowKey: {
    type: String,
    default: 'id'
  }
})

defineEmits(['retry'])

function getNestedValue(obj, path) {
  return path.split('.').reduce((current, key) => current?.[key], obj)
}

function getItemKey(item, index) {
  return item[props.rowKey] ?? index
}
</script>

<style scoped>
/* Estils compartits a main.css */
</style>
