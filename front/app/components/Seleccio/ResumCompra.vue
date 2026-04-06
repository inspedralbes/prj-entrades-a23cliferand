<template>
    <div class="resum-compra">
        <h2 class="resum-titol">Resum de la teva compra</h2>

        <div class="resum-llista">
            <div v-for="(seient, index) in seients" :key="seient.id" class="resum-item">
                <div class="item-info">
                    <span class="item-seient">Fila {{ seient.fila }}, Seient {{ seient.numero }}</span>
                </div>

                <div class="item-tarifa">
                    <label :for="'tarifa-' + seient.id">Tipus d'entrada:</label>
                    <select :id="'tarifa-' + seient.id" v-model="seient.tipus_client_id"
                        @change="actualitzarPreu(seient)" class="form-control select-tarifa">
                        <option v-for="preu in preusTarifa" :key="preu.tipus_client_id" :value="preu.tipus_client_id">
                            {{ preu.tipus_client.nom }} ({{ formatPreu(preu.preu) }})
                        </option>
                    </select>
                </div>

                <div class="item-preu">
                    {{ formatPreu(seient.preu_aplicat || defaultPreu) }}
                </div>
            </div>
        </div>

        <div class="resum-total">
            <div class="total-fila">
                <span>Total:</span>
                <span class="total-valor">{{ formatPreu(total) }}</span>
            </div>
        </div>

        <div class="resum-final">
            <div class="camp-grup">
                <label for="email" class="form-label">Correu electrònic per rebre les entrades:</label>
                <input type="email" id="email" v-model="email" placeholder="el-teu@email.com" class="form-control"
                    required />
            </div>

            <div class="accions-finals">
                <button type="button" @click="$emit('enrere')" class="btn btn-secondary">
                    Canviar seients
                </button>
                <button type="button" @click="finalitzarCompra" :disabled="!isEmailValid || processant"
                    class="btn btn-primary btn-comprar">
                    {{ processant ? 'Processant...' : 'Confirmar i Comprar' }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const props = defineProps({
    seients: {
        type: Array,
        required: true
    },
    preusTarifa: {
        type: Array,
        required: true
    },
    defaultPreu: {
        type: Number,
        default: 0
    }
})

const emit = defineEmits(['enrere', 'completat'])

const email = ref('')
const processant = ref(false)

onMounted(() => {
    props.seients.forEach(seient => {
        if (!seient.tipus_client_id) {
            seient.tipus_client_id = 1
        }
        actualitzarPreu(seient)
    })
})

function actualitzarPreu(seient) {
    const preuTrobat = props.preusTarifa.find(p => p.tipus_client_id === seient.tipus_client_id)
    if (preuTrobat) {
        seient.preu_aplicat = parseFloat(preuTrobat.preu)
    } else {
        seient.preu_aplicat = props.defaultPreu
    }
}

const total = computed(() => {
    return props.seients.reduce((sum, s) => sum + (s.preu_aplicat || props.defaultPreu), 0)
})

const isEmailValid = computed(() => {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    return re.test(email.value)
})

function formatPreu(preu) {
    return new Intl.NumberFormat('ca-ES', { style: 'currency', currency: 'EUR' }).format(preu)
}

function finalitzarCompra() {
    if (!isEmailValid.value) return

    processant.value = true
    // Emetem tota la informació necessària per finalitzar la compra al servidor
    emit('completat', {
        email: email.value,
        seients: props.seients,
        total: total.value
    })
}
</script>

<style scoped>
.resum-compra {
    background: var(--color-surface);
    padding: 2rem;
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-card);
    border: 1px solid var(--color-border);
}

.resum-titol {
    margin-bottom: 2rem;
    color: var(--color-text);
    text-align: center;
    font-size: 1.8rem;
}

.resum-llista {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 2rem;
}

.resum-item {
    display: grid;
    grid-template-columns: 1fr 2fr 100px;
    align-items: center;
    padding: 1rem;
    background: var(--color-surface2);
    border-radius: var(--radius-sm);
    border: 1px solid var(--color-border);
    gap: 1rem;
}

.item-seient {
    font-weight: 700;
    color: var(--color-text);
}

.item-tarifa {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.item-tarifa label {
    font-size: 0.9rem;
    color: var(--color-muted);
    white-space: nowrap;
}

.item-preu {
    text-align: right;
    font-weight: 700;
    font-size: 1.1rem;
    color: var(--color-accent);
}

.resum-total {
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 2px solid var(--color-border);
}

.total-fila {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 2rem;
    font-size: 1.5rem;
    font-weight: 800;
}

.total-valor {
    color: var(--color-accent);
}

.resum-final {
    margin-top: 3rem;
    background: var(--color-surface2);
    padding: 2rem;
    border-radius: var(--radius-md);
    border: 1px solid var(--color-border);
}

.camp-grup {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    margin-bottom: 2rem;
}

.camp-grup label {
    font-weight: 600;
    color: var(--color-text);
}

.accions-finals {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
}

.btn-comprar {
    flex: 2;
    font-size: 1.2rem;
    padding: 1rem;
}

@media (max-width: 768px) {
    .resum-item {
        grid-template-columns: 1fr;
        gap: 0.5rem;
    }

    .item-preu {
        text-align: left;
    }

    .accions-finals {
        flex-direction: column-reverse;
    }
}
</style>
