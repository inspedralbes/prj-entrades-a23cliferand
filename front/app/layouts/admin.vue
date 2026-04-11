<template>
    <div class="admin-layout">
        <AdminSidebar />
        <main class="admin-layout-content">
            <slot />
        </main>
    </div>
</template>

<style scoped>
.admin-layout {
    width: 100%;
}

.admin-layout-content {
    margin-left: var(--admin-sidebar-width, 240px);
    min-height: calc(100vh - var(--main-navbar-height, 64px));
}

@media (max-width: 768px) {
    .admin-layout-content {
        margin-left: 0;
    }
}
</style>
