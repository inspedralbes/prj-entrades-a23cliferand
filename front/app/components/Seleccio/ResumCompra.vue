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
                        @change="actualitzarPreu(seient)" class="select-tarifa">
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
            <div v-if="!isAuthenticated" class="camp-grup">
                <label for="email">Correu electrònic per rebre les entrades:</label>
                <input type="email" id="email" v-model="email" placeholder="el-teu@email.com" class="input-email"
                    required />
            </div>

            <div v-else class="compra-auth-ok">
                <strong>Compraràs amb el teu compte iniciat.</strong>
                <div class="info-usuari">
                    <p v-if="usuariNom" class="info-fila">
                        <span class="info-label">Nom:</span>
                        <span class="info-valor">{{ usuariNom }}</span>
                    </p>
                    <p v-if="usuariEmail" class="info-fila">
                        <span class="info-label">Email:</span>
                        <span class="info-valor">{{ usuariEmail }}</span>
                    </p>
                </div>
            </div>

            <div v-if="!isAuthenticated" class="compra-auth-opcio">
                <span>O compra directament amb el teu compte.</span>
                <div class="auth-botones">
                    <button type="button" class="btn btn-enrere" @click="$emit('anar-login')">
                        Inicia sessió
                    </button>
                    <button type="button" class="btn btn-enrere" @click="$emit('anar-registre')">
                        Registra't
                    </button>
                </div>
            </div>

            <div class="accions-finals">
                <button type="button" @click="$emit('enrere')" class="btn btn-enrere">
                    Canviar seients
                </button>
                <button type="button" @click="finalitzarCompra" :disabled="!canPurchase || processant"
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
    },
    isAuthenticated: {
        type: Boolean,
        default: false
    },
    usuariNom: {
        type: String,
        default: null
    },
    usuariEmail: {
        type: String,
        default: null
    }
})

const emit = defineEmits(['enrere', 'completat', 'anar-login', 'anar-registre'])

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
        seient.preu_aplicat = Number.parseFloat(preuTrobat.preu)
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

const canPurchase = computed(() => {
    return props.seients.length > 0 && (props.isAuthenticated || isEmailValid.value)
})

function formatPreu(preu) {
    return new Intl.NumberFormat('ca-ES', { style: 'currency', currency: 'EUR' }).format(preu)
}

function finalitzarCompra() {
    if (!canPurchase.value) return

    processant.value = true
    // Emetem tota la informació necessària per finalitzar la compra al servidor
    emit('completat', {
        email: props.isAuthenticated ? '' : email.value,
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

.select-tarifa {
    flex: 1;
    padding: 0.5rem;
    border-radius: var(--radius-sm);
    border: 1px solid var(--color-border);
    background: var(--color-surface);
    color: var(--color-text);
    font-size: 0.95rem;
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

.compra-auth-ok {
    margin-bottom: 1.5rem;
    padding: 1rem;
    border-radius: var(--radius-sm);
    border: 1px solid var(--color-border);
    background: var(--color-surface);
}

.compra-auth-ok strong {
    display: block;
    margin-bottom: 0.75rem;
}

.info-usuari {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.info-fila {
    display: flex;
    gap: 0.75rem;
    margin: 0;
    font-size: 0.95rem;
}

.info-label {
    font-weight: 600;
    color: var(--color-muted);
    min-width: 60px;
}

.info-valor {
    color: var(--color-text);
    word-break: break-all;
}

.compra-auth-opcio {
    display: flex;
    justify-content: center;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
    text-align: center;
}

.auth-botones {
    display: flex;
    gap: 1rem;
    justify-content: center;
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

.btn-enrere {
    background: var(--color-accent);
    color: #fff;
    border: none;
    transition: background-color var(--transition);
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
