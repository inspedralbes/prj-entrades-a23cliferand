import { useGuestStore } from "~/stores/guestStore";

export default defineNuxtRouteMiddleware(() => {
  const guestStore = useGuestStore();

  if (process.client) {
    guestStore.loadAuthData();
  }

  if (!guestStore.isAuthenticated()) {
    return navigateTo("/auth/login");
  }

  if (!guestStore.isAdmin()) {
    return navigateTo("/");
  }
});
