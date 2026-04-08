import { defineStore } from "pinia";

export const useGuestStore = defineStore("guestStore", {
  state: () => ({
    userId: null,
    nom: null,
    token: null,
    guestId: null,
  }),
  actions: {
    initGuest() {
      if (globalThis.window) {
        // Intentem obtenir l'usuari_id (usuaris logueats)
        const storedUserId = localStorage.getItem("usuari_id");

        if (storedUserId && storedUserId !== "1") {
          // Usuari autenticat
          this.userId = storedUserId;
        } else {
          // Usuari no autenticat - generem un guest_id únic
          let guestId = localStorage.getItem("guest_id");

          if (!guestId) {
            guestId = "guest_" + crypto.randomUUID();
            localStorage.setItem("guest_id", guestId);
          }

          this.guestId = guestId;
        }
      }
    },

    setUserId(id) {
      this.userId = id;
      this.guestId = null;
      localStorage.setItem("usuari_id", id);
      localStorage.removeItem("guest_id");
    },

    setAuthData(userId, nom, token) {
      this.userId = userId;
      this.nom = nom;
      this.token = token;
      this.guestId = null;

      localStorage.setItem("usuari_id", userId);
      localStorage.setItem("auth_token", token);
      localStorage.setItem("usuari_nom", nom);
      localStorage.removeItem("guest_id");
    },

    loadAuthData() {
      if (globalThis.window) {
        const storedToken = localStorage.getItem("auth_token");
        const storedUserId = localStorage.getItem("usuari_id");
        const storedNom = localStorage.getItem("usuari_nom");

        if (storedToken && storedUserId) {
          this.token = storedToken;
          this.userId = storedUserId;
          this.nom = storedNom;
          this.guestId = null;
        }
      }
    },

    clearAuthData() {
      this.userId = null;
      this.nom = null;
      this.token = null;

      localStorage.removeItem("usuari_id");
      localStorage.removeItem("auth_token");
      localStorage.removeItem("usuari_nom");
    },

    getIdentifier() {
      return this.userId || this.guestId;
    },

    isAuthenticated() {
      return !!this.userId;
    },
  },
});
