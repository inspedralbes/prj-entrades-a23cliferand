<template>
    <div class="auth-page">
        <div class="auth-container">
            <div class="auth-card">
                <h1 class="auth-title">Inicia sessió</h1>
                <p class="auth-subtitle">Cinema Paradise</p>

                <!-- Missatge d'error -->
                <div v-if="error" class="alert alert-error">
                    {{ error }}
                </div>

                <!-- Formulari -->
                <form @submit.prevent="handleLogin" class="auth-form">
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
                            placeholder="Introdueix la teva contrasenya" :disabled="loading" required />
                    </div>

                    <!-- Botó -->
                    <button type="submit" class="btn btn-primary btn-full" :disabled="loading">
                        {{ loading ? "S'està autenticant..." : "Inicia sessió" }}
                    </button>
                </form>

                <!-- Link de registre -->
                <div class="auth-footer">
                    <p class="auth-text">
                        No tens compte?
                        <NuxtLink to="/auth/register" class="auth-link">Registra't</NuxtLink>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { login } from "~/services/communicationManager";
import { useGuestStore } from "~/stores/guestStore";

const router = useRouter();
const guestStore = useGuestStore();

const form = ref({
    email: "",
    password: "",
});

const loading = ref(false);
const error = ref("");

const handleLogin = async () => {
    error.value = "";
    loading.value = true;

    try {
        const response = await login(form.value.email, form.value.password);

        // Verificar si la resposta conté el token
        if (response.token) {
            // Guardem les dades a la base de dades local i al store
            const userId = response.user?.id;
            const nom = response.user?.nom;

            guestStore.setAuthData(userId, nom, response.token);

            // Redirigeix a la pàgina principal
            router.push("/");
        } else if (response.ok === false) {
            error.value =
                response.error ||
                response.message ||
                "Error al iniciar sessió. Verifica les teves credencials.";
        } else {
            error.value =
                "Error desconegut. Contacta amb l'administrador.";
        }
    } catch (err) {
        console.error("Error de login:", err);
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
