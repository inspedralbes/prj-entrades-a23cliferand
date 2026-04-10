## Context

El projecte és una aplicació de cinema feta amb Nuxt 3 (frontend) i Laravel API (backend). Ja existeix un dashboard d'admin bàsic a `/admin` amb sidebar de navegació. Ara cal implementar les pantalles CRUD funcionals.

L'API ja té endpoints REST per a tots els recursos (usuaris, pel·lícules, sessions, sales, reserves). Les pel·lícules es sincronitzen des d'IMDb a Redis.

## Goals / Non-Goals

**Goals:**
- Implementar CRUD complet per: Usuaris, Sessions, Sales
- Implementar vista de Reserves (només lectura, no edició)
- Implementar gestió de Pel·lícules amb sincronització IMDb
- Afegir controls de reset/sync de la base de dades
- Mantenir coherència visual amb l'estil existent de l'admin

**Non-Goals:**
- Implementar autenticació d'admin (s'assumeix que `/admin` està protegida)
- Modificar esquemes de base de dades
- Implementar paginació avançada o filtres complexos (només llistes bàsiques)

## Decisions

### 1. Estructura de CRUD: Llista + Formulari

Cada pàgina CRUD tindrà:
- Vista de llista amb taula (id, dades principals, accions)
- Modal o pàgina separada per crear/editar
- Botons de CRUD segons permisos

### 2. Components reutilitzables

Crear components Vue reutilitzables:
- `AdminTable.vue`: Taula genèrica per mostrar llistes
- `AdminModal.vue`: Modal per formularis
- `AdminForm.vue`: Formulari CRUD base

### 3. Pel·lícules: Sincronització IMDb

- Formulari que accepta IMDb ID
- Botó per fer sync individual (`POST /api/pelicules/sync/{imdbId}`)
- Botó per fer sync complet (`POST /api/pelicules/sync/all`)
- La llista mostra dades de Redis (no es modifiquen directament)

### 4. Reserves: Vista de només lectura

- Només operacions de lectura (GET)
- Sense botons de crear/editar/eliminar
- Mostra informació de reserva, sessió, usuari, seients

### 5. Sessions: Dropdown de Pel·lícules

- Quan es crea/edita una sessió, seleccionar pel·lícula des de dropdown
- Dropdown carrega dades de `/api/pelicules`

### 6. API Backend

- Crear AdminController amb mètodes CRUD
- Afegir mètodes de sync per a pel·lícules si cal
- Mantindre les rutes existents a `api.php`

## Risks / Trade-offs

- [Risk] Les pel·lícules a Redis poden tenir formats diferents → Verificar compatibilitat del frontend
- [Risk] Dropdown de pel·lícules pot ser lent amb moltes entrades → Afegir cerca/filtre si cal
- [Risk] Caldrà gestionar errors d'API de sincronització IMDb → Mostrar missatges clars a l'usuari

## Migration Plan

1. Crear components base (AdminTable, AdminModal)
2. Implementar pàgina Usuaris (model bàsic CRUD)
3. Implementar pàgina Sales (model bàsic CRUD)
4. Implementar pàgina Sessions (CRUD amb dropdown de pel·lícules)
5. Implementar pàgina Pel·lícules (vista + sync IMDb)
6. Implementar pàgina Reserves (només lectura)
7. Afegir funcionalitat de reset/sync a la pàgina de Pel·lícules o Settings
8. Verificar que totes les rutes de sidebar funcionen
