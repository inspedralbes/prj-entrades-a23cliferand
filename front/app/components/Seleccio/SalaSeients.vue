<template>
    <div class="seients-contenidor">
        <div class="pantalla">PANTALLA</div>

        <div class="sala-seients">
            <div v-for="(filSeients, index) in seientsPerFila" :key="index" class="fila">
                <span class="fila-numero">{{ filSeients[0].fila }}</span>

                <div class="seients-fila">
                    <button v-for="seient in filSeients" :key="seient.id" class="seient" :class="clasesSeient(seient)"
                        :disabled="isSeientDisabled(seient)" @click="toggleSeient(seient)">
                        <span class="seient-numero">{{ seient.numero }}</span>
                    </button>
                </div>

                <span class="fila-numero">{{ filSeients[0].fila }}</span>
            </div>
        </div>

        <div class="seients-info">
            <div v-if="seientSeleccionats.length > 0" class="seleccionats">
                <h3>Seients seleccionats ({{ seientSeleccionats.length }})</h3>
                <div class="seients-llistat">
                    <span v-for="seient in seientSeleccionats" :key="seient.id" class="seient-tag">
                        {{ seient.fila }}{{ seient.numero }}
                        <button type="button" class="eliminar-btn" @click="desseleccionarSeient(seient)">×</button>
                    </span>
                </div>
            </div>
            <div v-else class="sense-seleccio">
                <p>Selecciona els seients que desitges comprar</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { crearReservaSeient, desocuparSeients, lesMevesReserves } from '~/services/communicationManager'
import { useGuestStore } from '~/stores/guestStore'

const emit = defineEmits(['seients-changed', 'reserva-creada'])

const seientSeleccionats = ref([])
const seientReservant = ref(null)

const props = defineProps({
    seients: {
        type: Array,
        required: true
    },
    sessioId: {
        type: [String, Number],
        required: true
    }
})

const seientsPerFila = computed(() => {
    return organitzarSeientsPerFila(props.seients)
})

onMounted(async () => {
    try {
        const usuarioId = obtenirIdUsuariValid()

        const reserves = await lesMevesReserves(usuarioId, props.sessioId)

        if (reserves.length > 0) {
            seientSeleccionats.value = reserves
            sincronitzarAMemoria()
            emit('seients-changed', seientSeleccionats.value)
        }
    } catch (err) {
        console.warn('Error carregant reserves previes:', err.message)
    }
});

// Desa els seients a la memòria del navegador (per si de cas)
function sincronitzarAMemoria() {
    localStorage.setItem(`reserves_sessio_${props.sessioId}`, JSON.stringify(seientSeleccionats.value));
}

// Per tenir-ho tot ordenat
function organitzarSeientsPerFila(seientsPlana) {
    const agrupats = {}

    seientsPlana.forEach(seient => {
        if (!agrupats[seient.fila]) {
            agrupats[seient.fila] = []
        }
        agrupats[seient.fila].push(seient)
    })

    return Object.values(agrupats).map(fila => {
        return fila.sort((a, b) => a.numero - b.numero)
    })
}

function isSeientDisabled(seient) {
    const esNostre = seientSeleccionats.value.some(s => s.id === seient.id)
    // Si està ocupat al servidor I NO el tenim a la llista local, llavors si que el bloquegem
    return seient.estat !== 'lliure' && !esNostre
}

function clasesSeient(seient) {
    const esSeleccionat = seientSeleccionats.value.some(s => s.id === seient.id)
    const esReservant = seientReservant.value === seient.id

    // Si és el nostre, donem prioritat visual a 'seleccionat' i amaguem que el servidor diu 'reservat'
    const overridesEstat = esSeleccionat ? 'seleccionat' : seient.estat

    return {
        'seient--lliure': overridesEstat === 'lliure',
        'seient--reservat': overridesEstat === 'reservat',
        'seient--venut': overridesEstat === 'venut',
        'seient--seleccionat': esSeleccionat,
        'seient--reservant': esReservant
    }
}

async function toggleSeient(seient) {
    const jaTincAquestSeient = seientSeleccionats.value.some(s => s.id === seient.id)

    // Si algú altre l'està usant (no és lliure I no està a la nostra pròpia llista), avortem
    if (seient.estat !== 'lliure' && !jaTincAquestSeient) return

    if (jaTincAquestSeient) {
        desseleccionarSeient(seient)
    } else {
        await crearReservaClick(seient)
        emit('seients-changed', seientSeleccionats.value)
    }
}

async function crearReservaClick(seient) {
    // Evitem que es processi dues vegades al mateix temps
    if (seientReservant.value) return

    try {
        seientReservant.value = seient.id
        const usuariId = obtenirIdUsuariValid()
        const reserva = await crearReservaSeient(props.sessioId, seient.id, usuariId)
        aplicarReservaCompletada(seient, reserva)
    } catch (err) {
        console.error('Error creant reserva:', err.message)
        alert(`Error: ${err.message}`)
    } finally {
        seientReservant.value = null
    }
}

// Retorna l'ID correcte i de pas assegura cridar 'init' si ens havíem oblidat
function obtenirIdUsuariValid() {
    const guestStore = useGuestStore()
    if (!guestStore.guestId) {
        guestStore.initGuest()
    }
    return guestStore.guestId
}

// Emmagatzema objectes al array limitant manipulacions en altres blocs
function aplicarReservaCompletada(seient, reserva) {
    seient.reserva_id = reserva.id
    seientSeleccionats.value.push(seient)

    sincronitzarAMemoria()

    emit('reserva-creada', { reserva, seient })
}

function desseleccionarSeient(seient) {
    const index = seientSeleccionats.value.findIndex(s => s.id === seient.id)
    if (index !== -1) {
        seientSeleccionats.value.splice(index, 1)
        seient.estat = 'lliure'
        sincronitzarAMemoria()

        desocuparSeients(props.sessioId, [seient.id]).catch(err => {
            console.error("No s'ha pogut desocupar el seient", err)
        })

        emit('seients-changed', seientSeleccionats.value)
    }
}
</script>

<style scoped>
.seients-contenidor {
    display: flex;
    flex-direction: column;
    gap: 2rem;
    padding: 2rem;
    background: var(--color-surface);
    border-radius: var(--radius-md);
    color: var(--color-text);
    border: 1px solid var(--color-border);
    box-shadow: var(--shadow-card);
}

.pantalla {
    text-align: center;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--color-muted);
    margin-bottom: 2rem;
    border-top: 3px solid var(--color-muted);
    padding-top: 1rem;
    letter-spacing: 2px;
}

.sala-seients {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    align-items: center;
}

.fila {
    display: flex;
    gap: 1rem;
    align-items: center;
    justify-content: center;
}

.fila-numero {
    font-weight: 700;
    color: var(--color-muted);
    min-width: 1.5rem;
    text-align: center;
    font-size: 0.9rem;
}

.seients-fila {
    display: flex;
    gap: 0.5rem;
}

.seient {
    width: 2.5rem;
    height: 2.5rem;
    border: 2px solid transparent;
    border-radius: var(--radius-sm);
    cursor: pointer;
    font-weight: 700;
    font-size: 0.8rem;
    transition: all var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.seient--lliure {
    background-color: var(--color-disponible);
    color: white;
}

.seient--lliure:hover:not(:disabled) {
    background-color: #3d8b40;
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
}

.seient--reservat {
    background-color: var(--color-reservat);
    color: #000;
    cursor: not-allowed;
    opacity: 0.7;
}

.seient--venut {
    background-color: var(--color-venut);
    color: var(--color-text);
    cursor: not-allowed;
    opacity: 0.4;
}

.seient--seleccionat {
    background-color: var(--color-seleccionat);
    color: white;
    border-color: white;
    border-width: 3px;
    box-shadow: 0 0 12px rgba(232, 39, 46, 0.6);
}

.seient:disabled:not(.seient--seleccionat) {
    cursor: not-allowed;
}

.seient-numero {
    pointer-events: none;
}

.seients-info {
    padding: 1.5rem;
    background-color: var(--color-surface2);
    border-radius: var(--radius-md);
    border: 1px solid var(--color-border);
}

.seleccionats h3 {
    margin: 0 0 1rem 0;
    font-size: 1.1rem;
    color: var(--color-text);
}

.seients-llistat {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.seient-tag {
    background-color: var(--color-accent);
    color: white;
    padding: 0.4rem 0.8rem;
    border-radius: var(--radius-sm);
    font-size: 0.9rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
}

.eliminar-btn {
    background: none;
    border: none;
    color: white;
    font-size: 1.2rem;
    cursor: pointer;
    padding: 0;
    line-height: 1;
    transition: opacity var(--transition);
}

.eliminar-btn:hover {
    opacity: 0.8;
}

.preu-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 1.1rem;
    font-weight: 700;
    padding-top: 1rem;
    border-top: 1px solid var(--color-border);
    color: var(--color-text);
}

.sense-seleccio {
    text-align: center;
    color: var(--color-muted);
    padding: 1rem;
}

@media (max-width: 640px) {
    .seient {
        width: 2rem;
        height: 2rem;
        font-size: 0.7rem;
    }

    .seients-fila {
        gap: 0.35rem;
    }

    .fila {
        gap: 0.5rem;
    }
}
</style>