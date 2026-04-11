<template>
    <div class="meves-reserves-container">
        <div class="content-wrapper">
            <div class="page-header">
                <NuxtLink to="/" class="btn-tornar">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Tornar
                </NuxtLink>
                <h1 class="page-title">Les meves entrades</h1>
                <p class="page-subtitle">Historial de les teves compres</p>
            </div>

            <div v-if="carregant" class="loading-state">
                <LoadingSpinner />
                <p>Carregant les teves entrades...</p>
            </div>

            <div v-else-if="error" class="error-state">
                <p>{{ error }}</p>
                <button @click="fetchEntrades" class="btn btn-primary">Tornar a intentar</button>
            </div>

            <div v-else-if="entrades.length === 0" class="empty-state">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.5">
                    <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                </svg>
                <p>No tens cap entrada encara</p>
                <NuxtLink to="/" class="btn btn-primary">Veure pel·lícules</NuxtLink>
            </div>

            <div v-else class="entrades-list">
                <div v-for="entrada in entrades" :key="entrada.id" class="entrada-card">
                    <div class="entrada-header">
                        <span class="entrada-id">#{{ entrada.id }}</span>
                        <span class="entrada-estat" :class="entrada.estat">{{ formatEstat(entrada.estat) }}</span>
                    </div>

                    <div class="entrada-body">
                        <div v-if="entrada.pelicula" class="pelicula-info">
                            <img v-if="entrada.pelicula.cartell" :src="entrada.pelicula.cartell"
                                :alt="entrada.pelicula.titol" class="pelicula-cartell" />
                            <div class="pelicula-details">
                                <h3>{{ entrada.pelicula.titol }}</h3>
                            </div>
                        </div>
                        <div v-else class="pelicula-info">
                            <div class="pelicula-details">
                                <h3>{{ entrada.sessio?.pellicula_id || 'Pel·lícula' }}</h3>
                            </div>
                        </div>

                        <div class="sessio-info">
                            <p v-if="entrada.sessio">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                {{ formatDataHora(entrada.sessio.data_hora) }}
                            </p>
                            <p v-if="entrada.sessio?.sala">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M3 21h18"></path>
                                    <path d="M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16"></path>
                                    <circle cx="15" cy="12" r="1"></circle>
                                </svg>
                                {{ entrada.sessio.sala.nom }}
                            </p>
                        </div>

                        <div class="seients-info">
                            <span class="seient-label">Seients:</span>
                            <div class="seients-list">
                                <span v-for="seient in entrada.seientsSessio" :key="seient.id" class="seient-badge">
                                    {{ seient.fila }}{{ seient.numero }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="entrada-footer">
                        <div class="preu">
                            <span class="preu-label">Total:</span>
                            <span class="preu-value">{{ formatPreu(entrada.preu_total) }}</span>
                        </div>
                        <button @click="descarregarEntrada(entrada.id)" class="btn btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="7 10 12 15 17 10"></polyline>
                                <line x1="12" y1="15" x2="12" y2="3"></line>
                            </svg>
                            Descarregar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { getLesMevesEntrades, descarregarEntrada } from '~/services/communicationManager'
import LoadingSpinner from '~/components/LoadingSpinner.vue'
import { useAppConstants } from '~/composables/useAppConstants'

const entrades = ref([])
const carregant = ref(true)
const error = ref(null)
const { appName } = useAppConstants()

useHead({
    title: `Les meves entrades — ${appName}`,
})

definePageMeta({
    middleware: 'auth'
})

async function fetchEntrades() {
    carregant.value = true
    error.value = null
    try {
        entrades.value = await getLesMevesEntrades()
    } catch (err) {
        error.value = err.message || 'Error en carregar les teves entrades'
    } finally {
        carregant.value = false
    }
}

function formatEstat(estat) {
    const estats = {
        'confirmada': 'Confirmada',
        'pendent': 'Pendent',
        'caducada': 'Caducada'
    }
    return estats[estat] || estat || '—'
}

function formatDataHora(dataHora) {
    if (!dataHora) return '—'
    const data = new Date(dataHora)
    return data.toLocaleString('ca-ES', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        hour: '2-digit',
        minute: '2-digit'
    })
}

function formatPreu(preu) {
    if (!preu) return '—'
    return new Intl.NumberFormat('ca-ES', { style: 'currency', currency: 'EUR' }).format(preu)
}
onMounted(() => {
    fetchEntrades()
})
</script>

<style scoped>
.meves-reserves-container {
    min-height: 100vh;
    padding: 2rem;
    background: linear-gradient(135deg, var(--color-surface) 0%, var(--color-background) 100%);
}

.content-wrapper {
    max-width: 800px;
    margin: 0 auto;
}

.page-header {
    margin-bottom: 2rem;
}

.page-title {
    font-size: 2rem;
    font-weight: bold;
    color: var(--color-text);
    margin: 0;
}

.page-subtitle {
    color: var(--color-text-secondary);
    margin: 0.5rem 0 0 0;
}

.loading-state,
.error-state,
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: var(--color-text-secondary);
}

.empty-state svg {
    width: 64px;
    height: 64px;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state p {
    margin-bottom: 1.5rem;
}

.entrades-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.entrada-card {
    background: var(--color-surface);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    overflow: hidden;
}

.entrada-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    background: var(--color-background);
    border-bottom: 1px solid var(--color-border);
}

.entrada-id {
    font-family: monospace;
    color: var(--color-text-secondary);
}

.entrada-estat {
    padding: 0.25rem 0.75rem;
    border-radius: var(--radius-sm);
    font-size: 0.85rem;
    font-weight: 600;
}

.entrada-estat.confirmada {
    background: #e8f5e9;
    color: #2e7d32;
}

.entrada-estat.pendent {
    background: #fff3e0;
    color: #ef6c00;
}

.entrada-estat.caducada {
    background: #ffebee;
    color: #c62828;
}

.entrada-body {
    padding: 1.5rem;
}

.pelicula-info {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}

.pelicula-cartell {
    width: 60px;
    height: 90px;
    object-fit: cover;
    border-radius: var(--radius-sm);
}

.pelicula-details h3 {
    margin: 0;
    font-size: 1.25rem;
    color: var(--color-text);
}

.sessio-info {
    display: flex;
    gap: 1.5rem;
    margin-bottom: 1rem;
    color: var(--color-text-secondary);
    font-size: 0.9rem;
}

.sessio-info p {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0;
}

.sessio-info svg {
    width: 16px;
    height: 16px;
}

.seients-info {
    margin-top: 1rem;
}

.seient-label {
    font-size: 0.85rem;
    color: var(--color-text-secondary);
    display: block;
    margin-bottom: 0.5rem;
}

.seients-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.seient-badge {
    background: var(--color-primary);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: var(--radius-sm);
    font-size: 0.85rem;
    font-weight: 500;
}

.entrada-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    background: var(--color-background);
    border-top: 1px solid var(--color-border);
}

.preu-label {
    color: var(--color-text-secondary);
    margin-right: 0.5rem;
}

.preu-value {
    font-weight: bold;
    font-size: 1.1rem;
    color: var(--color-text);
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: var(--radius-md);
    text-decoration: none;
    font-weight: 600;
    transition: all 0.2s;
    border: none;
    cursor: pointer;
    font-size: 0.9rem;
}

.btn svg {
    width: 16px;
    height: 16px;
}

.btn-primary {
    background: var(--color-primary);
    color: white;
}

.btn-primary:hover {
    background: var(--color-primary-dark);
}

.btn-secondary {
    background: var(--color-background);
    color: var(--color-text);
    border: 1px solid var(--color-border);
}

.btn-secondary:hover {
    background: var(--color-surface);
}

@media (max-width: 600px) {
    .meves-reserves-container {
        padding: 1rem;
    }

    .entrada-footer {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }

    .sessio-info {
        flex-direction: column;
        gap: 0.5rem;
    }
}
</style>