<template>
    <div class="seient-legenda card">
        <div class="seient_item">
            <div class="seient-status seient-status--lliure" />
            <span>Disponible</span>
        </div>
        <div class="seient_item">
            <div class="seient-status seient-status--reservat" />
            <span>Reservat</span>
        </div>
        <div class="seient_item">
            <div class="seient-status seient-status--venut" />
            <span>Venut</span>
        </div>
        <div class="seient_item">
            <div class="seient-status seient-status--seleccionat" />
            <span>Seleccionat</span>
        </div>
    </div>
</template>

<style scoped>
.seient-legenda {
    display: flex;
    gap: 2rem;
    justify-content: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

.seient_item {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    font-size: 0.95rem;
    font-weight: 500;
    color: var(--color-text);
}
</style>
