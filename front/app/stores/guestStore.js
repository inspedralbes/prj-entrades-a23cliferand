import { defineStore } from 'pinia'

export const useGuestStore = defineStore('guestStore', {
  state: () => ({
    userId: null,
    guestId: null
  }),
  actions: {
    initGuest() {
      if (globalThis.window) {
        // Intentem obtenir l'usuari_id (usuaris logueats)
        const storedUserId = localStorage.getItem('usuari_id')
        
        if (storedUserId && storedUserId !== '1') {
          // Usuari autenticat
          this.userId = storedUserId
        } else {
          // Usuari no autenticat - generem un guest_id únic
          let guestId = localStorage.getItem('guest_id')
          
          if (!guestId) {
            guestId = 'guest_' + crypto.randomUUID()
            localStorage.setItem('guest_id', guestId)
          }
          
          this.guestId = guestId
        }
      }
    },
    
    setUserId(id) {
      this.userId = id
      this.guestId = null
      localStorage.setItem('usuari_id', id)
      localStorage.removeItem('guest_id')
    },
    
    getIdentifier() {
      return this.userId || this.guestId
    },
    
    isAuthenticated() {
      return !!this.userId
    }
  }
})
