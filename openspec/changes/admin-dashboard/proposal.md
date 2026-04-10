## Why

El projecte actualment no disposa d'una interfície d'administració per gestionar les dades de pel·lícules, sessions, sales i reserves. Necessitem una pàgina d'admin que permeti fer CRUDs sobre les dades i visualitzar estadístiques gràfiques per entendre l'estat del negoci.

## What Changes

- **Nou**: Pàgina principal d'admin amb sidebar de navegació
- **Nou**: Sidebar amb enllaços als diferents CRUDs (pel·lícules, sessions, sales, reserves/usuaris)
- **Nou**: Dashboard principal amb gràfics vue-chartjs per mostrar estadístiques bàsiques
- **Nou**: Estil CSS natiu que segueixi el disseny actual del projecte

## Capabilities

### New Capabilities
- **admin-dashboard**: Pàgina principal d'administració amb sidebar i gràfics estadístics
- **admin-crud-navigation**: Sistema de navegació entre els diferents CRUDs des del sidebar
- **admin-chart-widgets**: Widgets de gràfics per visualitzar dades del negoci

## Impact

- Nou component Vue: pàgina d'admin amb sidebar
- Inclusió de llibreria vue-chartjs al projecte
- Estils CSS nadius al fitxer main.css