<template>
    <div class="confirmacio-container">
        <div class="confirmacio-content">
            <div v-if="cargant" class="cargant">
                <p>Carregant informació de la comanda...</p>
            </div>

            <div v-else>
                <div class="confirmacio-icone">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>

                <h1 class="confirmacio-titol">Compra Confirmada!</h1>

                <div class="confirmacio-missatge">
                    <p class="missatge-principal">
                        La teva comanda s'ha processat correctament.
                    </p>
                    <p class="missatge-secundari">
                        Rebràs les entrades al correu electrònic que vares indicar.
                    </p>
                </div>

                <!-- Informació de la película i sessió -->
                <div class="confirmacio-pelicula" v-if="pelicula">
                    <img v-if="pelicula.cartell" :src="pelicula.cartell" :alt="pelicula.titol"
                        class="pelicula-cartell" />
                    <div class="pelicula-info">
                        <h2>{{ pelicula.titol }}</h2>
                        <p v-if="sessioData">
                            <strong>Sala:</strong> {{ sessioData.sala_nom }}<br />
                            <strong>Data i hora:</strong> {{ formatDataHora(sessioData.data_hora) }}
                        </p>
                    </div>
                </div>

                <div class="confirmacio-detalls">
                    <div class="detall-item">
                        <span class="detall-label">Número de comanda:</span>
                        <span class="detall-valor">#{{ reservaId }}</span>
                    </div>
                    <div class="detall-item" v-if="email">
                        <span class="detall-label">Correu electrònic:</span>
                        <span class="detall-valor">{{ email }}</span>
                    </div>
                    <div class="detall-item" v-if="seients.length > 0">
                        <span class="detall-label">Seients:</span>
                        <span class="detall-valor">{{ seients.join(', ') }}</span>
                    </div>
                    <div class="detall-item" v-if="total">
                        <span class="detall-label">Total pagat:</span>
                        <span class="detall-valor">{{ formatPreu(total) }}</span>
                    </div>
                </div>

                <div class="confirmacio-accions">
                    <NuxtLink to="/" class="btn btn-primary">
                        Tornar a l'inici
                    </NuxtLink>
                </div>

                <div class="confirmacio-avise">
                    <p>Si no reps les entrades en els pròxims minuts, revisa la carpeta de SPAM del teu correu
                        electrònic.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { getReservaById } from '~/services/communicationManager'

const route = useRoute()
const cargant = ref(true)
const reservaId = ref(null)
const email = ref('')
const seients = ref([])
const total = ref(0)
const pelicula = ref(null)
const sessioData = ref(null)

onMounted(async () => {
    try {
        const id = route.params.id

        if (!id) {
            cargant.value = false
            return
        }

        reservaId.value = id

        // Carregam les dades de la reserva
        const reserva = await getReservaById(id)

        email.value = reserva.email || ''
        total.value = Number.parseFloat(reserva.preu_total) || 0

        // Dades de la sessió
        if (reserva.sessio_data) {
            sessioData.value = reserva.sessio_data
        }

        // Dades de la película
        if (reserva.pelicula) {
            pelicula.value = reserva.pelicula
        }

        // Extraem els seients
        const seientsRelacion = reserva.seients_sessio || reserva.seientsSessio || []
        if (seientsRelacion && seientsRelacion.length > 0) {
            seients.value = seientsRelacion.map(s => `${s.fila}${s.numero}`)
        }
    } catch (err) {
        console.error('Error:', err)
    } finally {
        cargant.value = false
    }
})

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
.confirmacio-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    background: linear-gradient(135deg, var(--color-surface) 0%, var(--color-background) 100%);
}

.confirmacio-content {
    background: var(--color-surface);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg);
    padding: 3rem 2rem;
    max-width: 600px;
    width: 100%;
    text-align: center;
}

.confirmacio-icone {
    width: 80px;
    height: 80px;
    margin: 0 auto 2rem;
    background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: scaleIn 0.5s ease-out;
}

.confirmacio-icone svg {
    width: 48px;
    height: 48px;
    color: white;
    stroke-width: 3;
}

@keyframes scaleIn {
    from {
        transform: scale(0);
    }

    to {
        transform: scale(1);
    }
}

.confirmacio-titol {
    font-size: 2rem;
    font-weight: bold;
    color: var(--color-text);
    margin-bottom: 1rem;
}

.confirmacio-missatge {
    margin-bottom: 2rem;
}

.missatge-principal {
    font-size: 1.1rem;
    color: var(--color-text);
    margin-bottom: 0.5rem;
}

.missatge-secundari {
    font-size: 0.95rem;
    color: var(--color-text-secondary);
}

.confirmacio-pelicula {
    display: flex;
    gap: 1.5rem;
    background: var(--color-background);
    border-radius: var(--radius-md);
    padding: 1.5rem;
    margin-bottom: 2rem;
    align-items: flex-start;
}

.pelicula-cartell {
    width: 80px;
    height: 120px;
    border-radius: var(--radius-sm);
    object-fit: cover;
    flex-shrink: 0;
}

.pelicula-info {
    text-align: left;
    flex: 1;
}

.pelicula-info h2 {
    margin: 0 0 0.5rem 0;
    font-size: 1.2rem;
    color: var(--color-text);
}

.pelicula-info p {
    margin: 0;
    font-size: 0.9rem;
    color: var(--color-text-secondary);
    line-height: 1.6;
}

.confirmacio-detalls {
    background: var(--color-background);
    border-radius: var(--radius-md);
    padding: 1.5rem;
    margin-bottom: 2rem;
    text-align: left;
}

.detall-item {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--color-border);
}

.detall-item:last-child {
    border-bottom: none;
}

.detall-label {
    font-weight: 600;
    color: var(--color-text-secondary);
}

.detall-valor {
    color: var(--color-text);
    word-break: break-word;
}

.confirmacio-accions {
    margin-bottom: 2rem;
}

.btn {
    display: inline-block;
    padding: 1rem 2rem;
    border-radius: var(--radius-md);
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    font-size: 1rem;
}

.btn-primary {
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.confirmacio-avise {
    background: var(--color-warning-light, rgba(255, 152, 0, 0.1));
    border-left: 4px solid var(--color-warning, #FF9800);
    padding: 1rem;
    border-radius: var(--radius-sm);
    text-align: center;
    font-size: 0.9rem;
    color: var(--color-text-secondary);
}

.cargant {
    padding: 2rem;
    text-align: center;
    color: var(--color-text-secondary);
}

.error-message {
    padding: 2rem;
    text-align: center;
}

.error-message p {
    color: #d32f2f;
    margin-bottom: 1.5rem;
    font-size: 1.1rem;
}

.error-details {
    font-size: 0.85rem;
    color: #666;
    background: #f5f5f5;
    padding: 1rem;
    border-radius: var(--radius-sm);
    text-align: left;
    margin-bottom: 1.5rem;
    max-height: 200px;
    overflow: auto;
    word-break: break-all;
}

@media (max-width: 768px) {
    .confirmacio-content {
        padding: 2rem 1rem;
    }

    .confirmacio-titol {
        font-size: 1.5rem;
    }

    .confirmacio-icone {
        width: 60px;
        height: 60px;
    }

    .confirmacio-icone svg {
        width: 36px;
        height: 36px;
    }

    .confirmacio-pelicula {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .pelicula-info {
        text-align: center;
    }

    .detall-item {
        flex-direction: column;
        gap: 0.25rem;
    }

    .detall-label {
        margin-bottom: 0.25rem;
    }
}
</style>
