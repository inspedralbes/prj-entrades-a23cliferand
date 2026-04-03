<template>
  <header class="navbar">
    <div class="container navbar_inner">
      <!-- Logo -->
      <NuxtLink to="/" class="navbar_logo">
        <span class="navbar_logo-text">Cinema Paradise</span>
      </NuxtLink>

      <!-- Actions -->
      <div class="navbar_actions">
        <button class="btn btn-primary">Inicia sessió</button>
      </div>
    </div>
  </header>
</template>

<script setup>
// Na mas
</script>

<style scoped>
.navbar {
  position: sticky;
  top: 0;
  z-index: 100;
  background: rgba(13, 13, 15, 0.92);
  backdrop-filter: blur(12px);
  border-bottom: 1px solid var(--color-border);
}

.navbar_inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 64px;
}

/* Logo */
.navbar_logo {
  display: flex;
  align-items: center;
  gap: 10px;
  text-decoration: none;
}

.navbar_logo-icon {
  font-size: 1.5rem;
}

.navbar_logo-text {
  font-family: var(--font-title);
  font-size: 1.6rem;
  letter-spacing: 3px;
  color: var(--color-accent);
}

/* Links */
.navbar_links {
  display: flex;
  gap: 8px;
}

.navbar_link {
  padding: 6px 16px;
  border-radius: var(--radius-sm);
  font-size: 0.9rem;
  font-weight: 500;
  color: var(--color-muted);
  transition: color var(--transition), background var(--transition);
}

.navbar_link:hover,
.navbar_link--active {
  color: var(--color-text);
  background: var(--color-surface2);
}

@media (max-width: 640px) {
  .navbar_links {
    display: none;
  }
}
</style>
