<template>
  <div>
    <NavBar />
    <NuxtPage />
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useGuestStore } from '~/stores/guestStore'
import { socket } from '~/services/socket'

const guestStore = useGuestStore()

onMounted(() => {
  guestStore.initGuest()
  console.log('[App.vue] Socket conectado desde inicio:', socket.connected)
})
</script>
