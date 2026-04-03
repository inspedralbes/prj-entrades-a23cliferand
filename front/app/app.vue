<template>
  <div>
    <NavBar />
    <NuxtPage />
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useGuestStore } from '~/stores/guestStore'

const guestStore = useGuestStore()

onMounted(() => {
  guestStore.initGuest()
})
</script>
