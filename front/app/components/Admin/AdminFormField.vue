<template>
  <div class="form-field" :class="{ 'has-error': error }">
    <label v-if="label" :for="inputId" class="form-label">
      {{ label }}
      <span v-if="required" class="required-mark">*</span>
    </label>

    <select
      v-if="type === 'select'"
      :id="inputId"
      :value="modelValue"
      :required="required"
      :disabled="disabled"
      class="form-input form-select"
      @change="$emit('update:modelValue', $event.target.value)"
    >
      <option v-if="placeholder" value="" disabled>{{ placeholder }}</option>
      <option v-for="option in options" :key="option.value" :value="option.value">
        {{ option.label }}
      </option>
    </select>

    <textarea
      v-else-if="type === 'textarea'"
      :id="inputId"
      :value="modelValue"
      :placeholder="placeholder"
      :required="required"
      :disabled="disabled"
      class="form-input form-textarea"
      rows="4"
      @input="$emit('update:modelValue', $event.target.value)"
    />

    <input
      v-else
      :id="inputId"
      :type="type"
      :value="modelValue"
      :placeholder="placeholder"
      :required="required"
      :disabled="disabled"
      :min="min"
      :max="max"
      class="form-input"
      @input="$emit('update:modelValue', $event.target.value)"
    />

    <p v-if="error" class="form-error">{{ error }}</p>
    <p v-else-if="hint" class="form-hint">{{ hint }}</p>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  label: {
    type: String,
    default: ''
  },
  type: {
    type: String,
    default: 'text'
  },
  placeholder: {
    type: String,
    default: ''
  },
  required: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  error: {
    type: String,
    default: ''
  },
  hint: {
    type: String,
    default: ''
  },
  options: {
    type: Array,
    default: () => []
  },
  min: {
    type: [String, Number],
    default: undefined
  },
  max: {
    type: [String, Number],
    default: undefined
  }
})

defineEmits(['update:modelValue'])

const inputId = computed(() => `field-${Math.random().toString(36).substr(2, 9)}`)
</script>

<style scoped>
/* Estils compartits a main.css */
</style>
