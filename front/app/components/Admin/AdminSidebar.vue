<template>
  <aside class="admin-sidebar">
    <div class="sidebar-header">
      <NuxtLink to="/admin" class="sidebar-logo">
        <span class="sidebar-logo-text">Admin</span>
      </NuxtLink>
    </div>
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
import { computed } from 'vue'
import { useRoute } from 'vue-router'
const route = useRoute()

const navItems = [
  { path: '/admin', label: 'Dashboard', icon: 'X' },
  { path: '/admin/pelicules', label: 'Pel·lícules', icon: 'X' },
  { path: '/admin/sessions', label: 'Sessions', icon: 'X' },
  { path: '/admin/sales', label: 'Sales', icon: 'X' },
  { path: '/admin/reserves', label: 'Reserves', icon: 'X' },
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
  position: fixed;
  top: 0;
  left: 0;
  width: 240px;
  height: 100vh;
  background: var(--color-surface);
  border-right: 1px solid var(--color-border);
  display: flex;
  flex-direction: column;
  z-index: 100;
}

.sidebar-header {
  padding: 20px;
  border-bottom: 1px solid var(--color-border);
}

.sidebar-logo {
  display: block;
  text-decoration: none;
}

.sidebar-logo-text {
  font-family: var(--font-title);
  font-size: 1.5rem;
  font-weight: 700;
  letter-spacing: 2px;
  color: var(--color-accent);
}

.sidebar-nav {
  padding: 16px 12px;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.sidebar-link {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
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
  background: rgba(232, 39, 46, 0.1);
  color: var(--color-accent);
}

.sidebar-link-icon {
  font-size: 1.1rem;
}

.sidebar-link-text {
  font-size: 0.95rem;
  font-weight: 500;
}

@media (max-width: 768px) {
  .admin-sidebar {
    transform: translateX(-100%);
    transition: transform var(--transition);
  }

  .admin-sidebar.open {
    transform: translateX(0);
  }
}
</style>