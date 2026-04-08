<template>
    <div class="auth-page">
        <div class="auth-container">
            <div class="auth-card">
                <h1 class="auth-title">Registra't</h1>

                <!-- Missatge d'error -->
                <div v-if="error" class="alert alert-error">
                    {{ error }}
                </div>

                <!-- Missatge d'èxit -->
                <div v-if="success" class="alert alert-success">
                    {{ success }}
                </div>

                <!-- Formulari -->
                <form @submit.prevent="handleRegister" class="auth-form" v-if="!success">
                    <!-- Nom -->
                    <div class="form-group">
                        <label for="nom" class="form-label">Nom</label>
                        <input id="nom" v-model="form.nom" type="text" class="form-input"
                            placeholder="Introdueix el teu nom" :disabled="loading" required />
                    </div>
                    <!-- Email -->
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" v-model="form.email" type="email" class="form-input"
                            placeholder="Introdueix el teu email" :disabled="loading" required />
                    </div>

                    <!-- Contrasenya -->
                    <div class="form-group">
                        <label for="password" class="form-label">Contrasenya</label>
                        <input id="password" v-model="form.password" type="password" class="form-input"
                            placeholder="Almenys 8 caràcters" :disabled="loading" required />
                    </div>

                    <!-- Confirmar contrasenya -->
                    <div class="form-group">
                        <label for="password-confirm" class="form-label">
                            Confirma la contrasenya
                        </label>
                        <input id="password-confirm" v-model="form.passwordConfirm" type="password" class="form-input"
                            placeholder="Confirma la contrasenya" :disabled="loading" required />
                        <small v-if="passwordMismatch" class="form-error">
                            Les contrasenyes no coincideixen
                        </small>
                    </div>

                    <!-- Botó -->
                    <button type="submit" class="btn btn-primary btn-full" :disabled="loading || passwordMismatch">
                        {{ loading ? "S'està registrant..." : "Registra't" }}
                    </button>
                </form>

                <!-- Link de login -->
                <div class="auth-footer">
                    <p class="auth-text">
                        Ja tens compte?
                        <NuxtLink :to="loginLink" class="auth-link">Inicia sessió</NuxtLink>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { useRouter, useRoute } from "vue-router";
import { register, transferirReservesGuest } from "~/services/communicationManager";
import { useGuestStore } from "~/stores/guestStore";

const router = useRouter();
const route = useRoute();
const guestStore = useGuestStore();

const form = ref({
    nom: "",
    email: "",
    password: "",
    passwordConfirm: "",
});

const loading = ref(false);
const error = ref("");
const success = ref("");

const passwordMismatch = computed(
    () => form.value.password && form.value.passwordConfirm && form.value.password !== form.value.passwordConfirm
);

const redirectPath = computed(() => (typeof route.query.redirect === "string" ? route.query.redirect : null));

const loginLink = computed(() => {
    if (!redirectPath.value) return "/auth/login";
    return {
        path: "/auth/login",
        query: {
            redirect: redirectPath.value,
        },
    };
});

const handleRegister = async () => {
    error.value = "";
    success.value = "";
    loading.value = true;

    // Validacions addicionals
    if (!form.value.email || form.value.email.length === 0) {
        error.value = "Si us plau, introdueix un email vàlid.";
        loading.value = false;
        return;
    }

    if (form.value.password.length < 8) {
        error.value = "La contrasenya ha de tenir almenys 8 caràcters.";
        loading.value = false;
        return;
    }

    if (form.value.password !== form.value.passwordConfirm) {
        error.value = "Les contrasenyes no coincideixen.";
        loading.value = false;
        return;
    }

    try {
        // Capturem el guest_id ANTES de registrar-se (si n'hi ha)
        const guestIdAbansRegistre = guestStore.guestId;
        const response = await register(form.value.nom, form.value.email, form.value.password);

        // Verificar si el registre ha anat bé
        if (response.token) {
            // Si ja retorna token, significa que s'ha registrat i autenticat
            const userId = response.user?.id;
            const nom = response.user?.nom;
            const email = response.user?.email;

            guestStore.setAuthData(userId, nom, response.token, email);

            // Transferim les reserves del guest al nou usuari autenticat
            if (guestIdAbansRegistre) {
                try {
                    await transferirReservesGuest(guestIdAbansRegistre);
                } catch (transferError) {
                    console.warn("No s'han pogut transferir les reserves del guest", transferError);
                }
            }

            success.value = "Registre completat correctament! Redirigint...";
            setTimeout(() => router.push(redirectPath.value || "/"), 1000);
        } else if (response.ok === false || response.error) {
            error.value =
                response.error ||
                response.message ||
                "Error al registrar-se. Verifica que l'email no està en ús.";
        } else if (response.message) {
            success.value = response.message;
            setTimeout(() => router.push("/auth/login"), 2000);
        } else {
            error.value = "Error desconegut. Contacta amb l'administrador.";
        }
    } catch (err) {
        console.error("Error de registre:", err);
        error.value = "Error de connexió amb el servidor.";
    } finally {
        loading.value = false;
    }
};
</script>

<style scoped>
@media (max-width: 640px) {
    .auth-card {
        padding: 30px 20px;
    }

    .auth-title {
        font-size: 1.5rem;
    }
}
</style>
