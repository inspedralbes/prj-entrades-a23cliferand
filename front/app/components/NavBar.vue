<template>
    <header class="navbar">
        <div class="container navbar_inner">
            <!-- Logo -->
            <NuxtLink to="/" class="navbar_logo">
                <span class="navbar_logo-text">Cinema Paradise</span>
            </NuxtLink>

            <!-- Actions -->
            <div class="navbar_actions">
                <template v-if="!isInAuthPage">
                    <template v-if="guestStore.isAuthenticated()">
                        <div class="navbar_user-info">
                            <span class="navbar_greeting">Hola, {{ guestStore.nom }}</span>
                            <button class="btn btn-logout" @click="handleLogout">Sortir</button>
                        </div>
                    </template>
                    <template v-else>
                        <button class="btn btn-primary" @click="router.push('/auth/login')">Inicia sessió</button>
                    </template>
                </template>
            </div>
        </div>
    </header>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useGuestStore } from '~/stores/guestStore'
import { logout } from '~/services/communicationManager'

const router = useRouter()
const route = useRoute()
const guestStore = useGuestStore()

// Detectar si estem a una pàgina d'autenticació
const isInAuthPage = computed(() => {
    return route.path.startsWith('/auth/')
})

// Funció per tancar sessió
const handleLogout = () => {
    logout()
    guestStore.clearAuthData()
    router.push('/')
}

// Carregar dades de autenticació
guestStore.loadAuthData()
</script>

<style scoped>
.navbar {
    position: sticky;
    top: 0;
    z-index: 100;
    background: rgba(13, 13, 15, 0.92);
    backdrop-filter: blur(12px);
    border-bottom: 1px solid var(--color-border);
}

.navbar_inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 64px;
}

.navbar_logo {
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
}

.navbar_logo-icon {
    font-size: 1.5rem;
}

.navbar_logo-text {
    font-family: var(--font-title);
    font-size: 1.6rem;
    letter-spacing: 3px;
    color: var(--color-accent);
}

.navbar_actions {
    display: flex;
    align-items: center;
    gap: 16px;
}

.navbar_user-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.navbar_greeting {
    font-size: 0.9rem;
    color: var(--color-text);
    font-weight: 500;
}

.navbar_links {
    display: flex;
    gap: 8px;
}

.navbar_link {
    padding: 6px 16px;
    border-radius: var(--radius-sm);
    font-size: 0.9rem;
    font-weight: 500;
    color: var(--color-muted);
    transition: color var(--transition), background var(--transition);
}

.navbar_link:hover,
.navbar_link--active {
    color: var(--color-text);
    background: var(--color-surface2);
}

.btn-logout {
    background: transparent;
    border: 1px solid var(--color-border);
    color: var(--color-text);
    padding: 6px 16px;
    border-radius: var(--radius-sm);
    font-size: 0.9rem;
    font-weight: 500;
    transition: color var(--transition), background var(--transition);
}

@media (max-width: 640px) {
    .navbar_links {
        display: none;
    }
}
</style>
