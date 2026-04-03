<template>
    <div class="seient">
        <div class="seient_item">
            <div class="seient_color seient_color--lliure" />
            <span>Disponible</span>
        </div>
        <div class="seient_item">
            <div class="seient_color seient_color--reservat" />
            <span>Reservat</span>
        </div>
        <div class="seient_item">
            <div class="seient_color seient_color--venut" />
            <span>Venut</span>
        </div>
        <div class="seient_item">
            <div class="seient_color seient_color--seleccionat" />
            <span>Seleccionat</span>
        </div>
    </div>
</template>

<style scoped>
.seient {
    display: flex;
    gap: 2rem;
    justify-content: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    padding: 1.5rem;
    background: var(--color-surface);
    border-radius: var(--radius-md);
    border: 1px solid var(--color-border);
    box-shadow: var(--shadow-card);
}

.seient_item {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    font-size: 0.95rem;
    font-weight: 500;
    color: var(--color-text);
}

.seient_color {
    width: 1.2rem;
    height: 1.2rem;
    border-radius: var(--radius-sm);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    transition: transform var(--transition);
}

.seient_color:hover {
    transform: scale(1.1);
}

.seient_color--lliure {
    background-color: var(--color-disponible);
    box-shadow: 0 2px 8px rgba(76, 175, 80, 0.4);
}

.seient_color--reservat {
    background: linear-gradient(135deg, var(--color-reservat) 0%, #e6b600 100%);
    box-shadow: 0 2px 8px rgba(245, 200, 66, 0.4);
}

.seient_color--venut {
    background: linear-gradient(135deg, var(--color-venut) 0%, #666 100%);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}

.seient_color--seleccionat {
    background: linear-gradient(135deg, var(--color-seleccionat) 0%, #ff3d3d 100%);
    box-shadow: 0 2px 12px rgba(232, 39, 46, 0.5);
}
</style>
