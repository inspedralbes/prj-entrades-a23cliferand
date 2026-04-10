<template>
  <aside class="admin-sidebar">
    <nav class="sidebar-nav">
      <NuxtLink v-for="item in navItems" :key="item.path" :to="item.path" class="sidebar-link"
        :class="{ 'sidebar-link--active': isActive(item.path) }">
        <span class="sidebar-link-icon">{{ item.icon }}</span>
        <span class="sidebar-link-text">{{ item.label }}</span>
      </NuxtLink>
    </nav>
  </aside>
</template>

<script setup>
import { useRoute } from 'vue-router'
const route = useRoute()

const navItems = [
  { path: '/admin', label: 'Dashboard', icon: '🏠' },
  { path: '/admin/pelicules', label: 'Pel·lícules', icon: '🎬' },
  { path: '/admin/sessions', label: 'Sessions', icon: '🕒' },
  { path: '/admin/sales', label: 'Sales', icon: '💳' },
  { path: '/admin/reserves', label: 'Reserves', icon: '📦' },
  { path: '/admin/usuaris', label: 'Usuaris', icon: '👥' }
]

const isActive = (path) => {
  if (path === '/admin') {
    return route.path === '/admin'
  }
  return route.path.startsWith(path)
}
</script>

<style scoped>
.admin-sidebar {
  position: sticky;
  top: var(--main-navbar-height, 64px);
  width: 100%;
  height: calc(100vh - var(--main-navbar-height, 64px));
  background: var(--color-surface);
  border-right: 1px solid var(--color-border);
  display: flex;
  flex-direction: column;
  z-index: 10;
}

.sidebar-logo {
  display: block;
  text-decoration: none;
}

.sidebar-logo-text {
  font-family: var(--font-title);
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--color-accent);
}

.sidebar-nav {
  padding: 12px;
  display: flex;
  flex-direction: column;
  gap: 6px;
  overflow: auto;
}

.sidebar-link {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 12px;
  border-radius: var(--radius-md);
  text-decoration: none;
  color: var(--color-muted);
  transition: all var(--transition);
}

.sidebar-link:hover {
  background: var(--color-surface2);
  color: var(--color-text);
}

.sidebar-link--active {
  background: rgba(232, 39, 46, 0.08);
  color: var(--color-accent);
}

.sidebar-link-icon {
  font-size: 1rem;
}

.sidebar-link-text {
  font-size: 0.95rem;
  font-weight: 500;
}

@media (max-width: 768px) {
  .admin-sidebar {
    position: static;
    top: auto;
    width: 100%;
    height: auto;
    border-right: none;
    border-bottom: 1px solid var(--color-border);
  }
}
</style>