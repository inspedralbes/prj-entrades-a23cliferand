<template>
    <div class="compra-page">
        <!-- Header -->
        <div class="compra-header">
            <div class="container compra-header_content">
                <a @click.prevent="$router.back()" href="#" class="compra-back">
                    ← Tornar Info Pelicula
                </a>
                <h1 v-if="sessio" class="section-titol">
                    Selecciona els teus seients
                </h1>
            </div>
        </div>

        <!-- Contingut principal -->
        <div class="container compra-main">
            <div v-if="carregant" class="carregant">
                <div class="spinner" />
                <p>Carregant seients...</p>
            </div>

            <div v-else-if="error" class="api-error api-error--centered">
                <p>{{ error }}</p>
                <NuxtLink to="/" class="btn btn-primary">Tornar a cartellera</NuxtLink>
            </div>

            <!-- Contingut -->
            <div v-else-if="sessio" class="compra-content">
                <!-- Informació de la sessió -->
                <div class="sessio-info">
                    <div class="info-blok">
                        <span class="info-label">Data i hora</span>
                        <span class="info-valor">{{ formatDataHora(sessio.data_hora) }}</span>
                    </div>
                    <div class="info-blok">
                        <span class="info-label">Sala</span>
                        <span class="info-valor">{{ sessio.sala_nom }}</span>
                    </div>
                    <div class="info-blok">
                        <span class="info-label">Pelicula</span>
                        <span class="info-valor">{{ sessio.pelicula_nom }}</span>
                    </div>
                </div>

                <!-- Seient -->
                <Seient v-if="pas === 1" />

                <!-- Selector de seients -->
                <div v-if="pas === 1">
                    <SalaSeients v-if="seients.length > 0" :seients="seients" :sessio-id="sessioId"
                        @seients-changed="seientSeleccionats = $event" @reserva-creada="manejarReservaCreada" />

                    <div v-if="seientSeleccionats.length > 0" class="compra-actions-floating">
                        <div class="container">
                            <div class="actions-content">
                                <div class="resum-breu">
                                    <span class="label">Seients:</span>
                                    <span class="valor">{{ seientSeleccionats.length }}</span>
                                    <span class="separador">|</span>
                                    <span class="label">Total aprox:</span>
                                    <span class="valor">{{ formatPreu(totalAproximat) }}</span>
                                </div>
                                <button @click="anarAResum" class="btn btn-primary">
                                    Continuar al resum →
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resum de compra -->
                <ResumCompra v-if="pas === 2" :seients="seientSeleccionats" :preus-tarifa="infoTarifa.preus"
                    :default-preu="tarifaDefault" @enrere="pas = 1" @completat="finalitzarCompraTotal" />

            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { getSeientsSessio, getTarifaById, confirmarReservaFinal } from '~/services/communicationManager'
import { setupSeientsListeners, netejarSeientsListeners } from '~/services/socket'
import SalaSeients from '~/components/Seleccio/SalaSeients.vue'
import Seient from '~/components/Seleccio/Seient.vue'
import ResumCompra from '~/components/Seleccio/ResumCompra.vue'
import { useGuestStore } from '~/stores/guestStore'

const route = useRoute()
const router = useRouter()
const sessioId = route.params.id

const pas = ref(1) // 1: Seients, 2: Resum
const sessio = ref(null)
const seients = ref([])
const seientSeleccionats = ref([])
const carregant = ref(true)
const processant = ref(false)
const error = ref(null)

const usuariId = ref(null)
const tarifaDefault = ref(10)
const infoTarifa = ref(null)

useHead({
    title: 'Entrades'
})

const totalAproximat = computed(() => {
    return seientSeleccionats.value.length * tarifaDefault.value
})

onMounted(async () => {
    try {
        const dades = await getSeientsSessio(sessioId)

        sessio.value = {
            id: dades.sessio_id,
            sala_id: dades.sala_id,
            sala_nom: dades.sala_nom,
            tarifa_id: dades.tarifa_id || 1, // Ens cal l'ID per buscar els preus
            tarifa_nom: dades.tarifa_nom,
            pelicula_nom: dades.pelicula_nom,
            data_hora: dades.data_hora
        }

        // Convertim seients a un array
        seients.value = dades.seients.flat()
        tarifaDefault.value = dades.preu_tarifa || 10

        // Carreguem tota la informació de la tarifa
        const tarifaId = dades.tarifa_id || 1
        infoTarifa.value = await getTarifaById(tarifaId)

        const token = localStorage.getItem('auth_token')
        if (token) {
            usuariId.value = localStorage.getItem('usuari_id')
        }

    } catch (err) {
        error.value = err.message || 'Error al carregar els seients'
    } finally {
        carregant.value = false
    }

    // Configurem els listeners de sockets
    try {
        setupSeientsListeners(
            sessioId,
            seients.value,
            (data) => {
                console.log(' Seients actualitzats:', data);
            },
            (data) => {
                console.log(' Seients alliberats:', data);
            }
        );
    } catch (err) {
        console.warn('Error configurant els listeners de Socket:', err);
    }
})

onUnmounted(() => {
    netejarSeientsListeners();
})

function manejarReservaCreada(data) {
    console.log('Reserva creada:', data.reserva)
}

function anarAResum() {
    window.scrollTo({ top: 0, behavior: 'smooth' })
    pas.value = 2
}

async function finalitzarCompraTotal(dades) {
    processant.value = true
    try {
        const guestStore = useGuestStore()
        const usuariIdIdent = guestStore.getIdentifier()

        const body = {
            sessio_id: sessioId,
            usuari_id: usuariIdIdent,
            email: dades.email,
            seients: dades.seients.map(s => ({
                id: s.id,
                tipus_client_id: s.tipus_client_id,
                preu_aplicat: s.preu_aplicat
            })),
            total: dades.total
        }

        const response = await confirmarReservaFinal(body)

        // Redirigim a la pàgina de confirmació amb l'ID de la reserva
        const reservaId = response.id
        router.push(`/confirmacio/${reservaId}`)
    } catch (err) {
        alert('Error al finalitzar la compra: ' + err.message)
    } finally {
        processant.value = false
    }
}

function formatDataHora(dataHora) {
    const data = new Date(dataHora)
    const opcions = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }
    return data.toLocaleDateString('ca-ES', opcions)
}

function formatPreu(preu) {
    return new Intl.NumberFormat('ca-ES', { style: 'currency', currency: 'EUR' }).format(preu)
}
</script>

<style scoped>
.compra-page {
    min-height: 100vh;
    background-color: var(--color-bg);
}

.compra-main {
    padding-bottom: 3rem;
}

.compra-content {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.compra-header {
    background: linear-gradient(135deg, var(--color-accent) 0%, var(--color-accent2) 100%);
    color: var(--color-text);
    padding: 2rem 0;
    margin-bottom: 2rem;
    border-bottom: 1px solid var(--color-border);
}

.compra-header_content {
    display: flex;
    align-items: center;
    gap: 2rem;
}

.compra-back {
    color: var(--color-text);
    text-decoration: none;
    font-size: 0.95rem;
    transition: opacity var(--transition);
}

.compra-back:hover {
    opacity: 0.8;
}

.carregant {
    text-align: center;
    padding: 4rem 2rem;
}

.spinner {
    width: 3rem;
    height: 3rem;
    border: 4px solid var(--color-border);
    border-top: 4px solid var(--color-accent);
    border-radius: 50%;
    margin: 0 auto 1rem;
    animation: spin 1s linear infinite;
}

.avis-temporal {
    background-color: rgba(245, 200, 66, 0.1);
    border: 1px solid var(--color-reservat);
    border-radius: var(--radius-md);
    padding: 1rem;
    color: #000;
    font-size: 0.95rem;
    text-align: center;
}

.avis-temporal p {
    margin: 0;
}

.sessio-info {
    background: var(--color-surface);
    padding: 1.5rem;
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-card);
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    border: 1px solid var(--color-border);
}

.info-blok {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.info-label {
    font-size: 0.8rem;
    color: var(--color-muted);
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.5px;
}

.info-valor {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--color-text);
}

.compra-actions {
    text-align: center;
    padding: 2rem;
    background: var(--color-surface);
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-card);
    border: 1px solid var(--color-border);
}

.info-compra {
    color: var(--color-muted);
    margin: 0;
    font-size: 0.95rem;
}

.compra-actions-floating {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: var(--color-surface);
    padding: 1.5rem 0;
    box-shadow: 0 -10px 25px rgba(0, 0, 0, 0.1);
    border-top: 1px solid var(--color-border);
    z-index: 100;
}

.actions-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.resum-breu {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    font-size: 1.1rem;
}

.resum-breu .label {
    color: var(--color-muted);
    font-weight: 600;
}

.resum-breu .valor {
    color: var(--color-text);
    font-weight: 800;
}

.resum-breu .separador {
    color: var(--color-border);
    margin: 0 0.5rem;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}

@media (max-width: 640px) {
    .compra-header_content {
        flex-direction: column;
        align-items: flex-start;
    }

    .sessio-info {
        grid-template-columns: 1fr;
    }
}
</style>
