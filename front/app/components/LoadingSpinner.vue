<template>
    <div class="spinner" />
</template>

<script setup>
</script>

<style scoped>
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
</style>
