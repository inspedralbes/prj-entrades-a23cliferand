## Context

El projecte és una aplicació de cinema/entrades feta amb Nuxt 3 al frontend. Actualment existeixen pàgines públiques per veure pel·lícules, comprar entrades i autenticació. No existeix cap zona d'administració.

El projecte utilitza:
- Nuxt 3 amb Vue 3
- CSS natiu amb variables CSS personalitzades (dark theme)
- Font "Sansation"
- Laravel API al backend

## Goals / Non-Goals

**Goals:**
- Crear pàgina d'admin accessible des del layout existent (o nou layout)
- Implementar sidebar de navegació per accedir als CRUDs
- Integrar vue-chartjs per mostrar estadístiques bàsiques
- Mantenir coherència visual amb l'estil actual del projecte

**Non-Goals:**
- Implementar els CRUDs complets (només navegació i dashboard)
- Implementar autenticació d'admin (assumim que la ruta /admin està protegida)
- Afegir funcions avançades de gràfics (només gràfics bàsics inicials)

## Decisions

1. **Estructura de carpetes**: Crear nova ruta `/admin` amb `pages/admin/index.vue` i components a `components/Admin/`

2. **Layout**: Utilitzar Nuxt layout propi per a admin (`layouts/admin.vue`) que inclogui el sidebar

3. **Sidebar**: Component reutilitzable amb links a:
   - Dashboard (pàgina principal)
   - Pel·lícules (/admin/pelicules)
   - Sessions (/admin/sessions)
   - Sales (/admin/sales)
   - Reserves (/admin/reserves)

4. **Gràfics**: Utilitzar vue-chartjs amb:
   - Chart.js com a dependencia
   - Gràfic de barres per entrades per dia
   - Gràfic circular per gèneres de pel·lícules

5. **Estil CSS**: Utilitzar les variables CSS existents de main.css per mantenir coherència

## Risks / Trade-offs

- [Risk] Caldrà instal·lar vue-chartjs i chart.js → Assegurar que funcionin amb Nuxt 3
- [Risk] Sense dades reals inicialment → Els gràfics mostraran dades d'exemple omock
- [Risk] Caldrà crear layout separat per admin → Evitar copsar estilos del frontend public

## Migration Plan

1. Crear layout `layouts/admin.vue` amb sidebar
2. Crear pàgina `pages/admin/index.vue` amb dashboard i gràfics
3. Afegir dependències de vue-chartjs al package.json
4. Verificar que el CSS de l'admin no afecti les pàgines públiques