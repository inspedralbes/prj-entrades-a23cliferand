<template>
    <div class="compra-page">
        <!-- Header -->
        <div class="compra-header">
            <div class="container compra-header_content">
                <a @click.prevent="$router.back()" href="#" class="compra-back">
                    ← Tornar Enrera
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
                <Seient />

                <!-- Selector de seients -->
                <SalaSeients v-if="seients.length > 0" :seients="seients" :sessio-id="sessioId"
                    @seients-changed="seientSeleccionats = $event" @reserva-creada="manejarReservaCreada" />

            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { getSeientsSessio } from '~/services/communicationManager'
import { setupSeientsListeners, netejarSeientsListeners } from '~/services/socket'
import SalaSeients from '~/components/Seleccio/SalaSeients.vue'
import Seient from '~/components/Seleccio/Seient.vue'

const route = useRoute()
const router = useRouter()
const sessioId = route.params.id

const sessio = ref(null)
const seients = ref([])
const seientSeleccionats = ref([])
const carregant = ref(true)
const processant = ref(false)
const error = ref(null)

const usuariId = ref(null)
const tarifaData = ref(null)

useHead({
    title: 'Entrades'
})

onMounted(async () => {
    try {
        const dades = await getSeientsSessio(sessioId)

        sessio.value = {
            id: dades.sessio_id,
            sala_id: dades.sala_id,
            sala_nom: dades.sala_nom,
            tarifa_nom: dades.tarifa_nom,
            pelicula_nom: dades.pelicula_nom,
            data_hora: dades.data_hora
        }

        // Convertim seients a un array
        seients.value = dades.seients.flat()

        tarifaData.value = dades.preu_tarifa || 10

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
    // Eliminem els listeners de sockets al desmuntar
    netejarSeientsListeners();
})

// Aquesta funció s'executa quan el component fill (SalaSeients) ha creat una reserva
function manejarReservaCreada(data) {
    console.log('Reserva creada:', data.reserva)

    // Redirigim a login si és usuari convidat/guest (usuari_id = 1) o no autenticat del tot
    const usuariId = localStorage.getItem('usuari_id')
    if (usuariId === '1') {
        router.push('/login')
    }
}

// Funció per mostrar la data i l'hora de manera entenedora
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
</script>

<style scoped>
.compra-page {
    min-height: 100vh;
    background-color: var(--color-bg);
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

.compra-main {
    padding-bottom: 3rem;
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

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}

.compra-content {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.aviso-temporal {
    background-color: rgba(245, 200, 66, 0.1);
    border: 1px solid var(--color-reservat);
    border-radius: var(--radius-md);
    padding: 1rem;
    color: #000;
    font-size: 0.95rem;
    text-align: center;
}

.aviso-temporal p {
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

/* Responsive */
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
