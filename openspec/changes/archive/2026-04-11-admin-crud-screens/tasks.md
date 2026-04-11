## 1. Base Components

- [x] 1.1 Create AdminTable.vue reusable component for listing data
- [x] 1.2 Create AdminModal.vue reusable component for forms
- [x] 1.3 Create AdminFormField.vue component for form inputs

## 2. Admin Usuaris CRUD

- [x] 2.1 Create `/admin/usuaris` page with list view
- [x] 2.2 Add "Nou Usuari" button and create modal form
- [x] 2.3 Implement create usuari (POST /api/usuaris)
- [x] 2.4 Implement edit usuari (PUT /api/usuaris/{id})
- [x] 2.5 Implement delete usuari (DELETE /api/usuaris/{id}) with confirmation
- [x] 2.6 Add role dropdown (admin/client) to form

## 3. Admin Sales CRUD

- [x] 3.1 Create `/admin/sales` page with list view
- [x] 3.2 Add "Nova Sala" button and create modal form
- [x] 3.3 Implement create sala (POST /api/sales)
- [x] 3.4 Implement edit sala (PUT /api/sales/{id})
- [x] 3.5 Implement delete sala (DELETE /api/sales/{id}) with confirmation

## 4. Admin Sessions CRUD

- [x] 4.1 Create `/admin/sessions` page with list view
- [x] 4.2 Add Pel·lícula dropdown that fetches from /api/pelicules
- [x] 4.3 Add Sala dropdown that fetches from /api/sales
- [x] 4.4 Add Tarifa dropdown that fetches from /api/tarifes
- [x] 4.5 Add "Nova Sessió" button and create modal form with single session and date range modes
- [x] 4.6 Implement create session (POST /api/sessions) - single and multiple sessions
- [x] 4.7 Implement edit session (PUT /api/sessions/{id})
- [x] 4.8 Implement delete session (DELETE /api/sessions/{id}) with confirmation

## 5. Admin Pel·lícules Management

- [x] 5.1 Create `/admin/pelicules` page with list view from Redis
- [x] 5.2 Add IMDb ID input field for single sync
- [x] 5.3 Implement single movie sync (POST /api/pelicules/sync/{imdbId})
- [x] 5.4 Add "Sincronitzar Tot" button
- [x] 5.5 Implement full movie sync (POST /api/pelicules/sync/all)
- [x] 5.6 Add movie details modal with poster and synopsis
- [x] 5.7 Add per-movie sync button in actions column
- [x] 5.8 Add delete button to remove movie from Redis
- [x] 5.9 Add edit button and modal to manually update movie fields in Redis

## 6. Admin Reserves View (Read-only)

- [x] 6.1 Create `/admin/reserves` page with list view
- [x] 6.2 Display reserve details: usuari, sessió, seients, preu
- [x] 6.3 Remove all create/edit/delete buttons from reserves view
- [x] 6.4 Add view details modal for each reserve

## 7. Admin Data Sync / Reset

- [x] 7.1 Add "Reset Base de Dades" button to Pel·lícules page
- [x] 7.2 Implement confirmation dialog with warning
- [x] 7.3 Create backend endpoint for database reset (POST /admin/reset-db)
- [x] 7.4 Run migrations fresh and re-seed data
- [x] 7.5 Trigger full movie sync after reset

## 8. Sidebar Navigation Update

- [x] 8.1 Verify AdminSidebar has all required navigation links
- [x] 8.2 Ensure "Usuaris" link routes to /admin/usuaris
- [x] 8.3 Test active state highlighting for all pages
- [x] 8.4 Add Tarifes link to sidebar

## 9. Admin Tarifes CRUD

- [x] 9.1 Create `/admin/tarifes` page with list view
- [x] 9.2 Add "Nova Tarifa" button and create modal form
- [x] 9.3 Implement create tarifa (POST /api/tarifes)
- [x] 9.4 Implement edit tarifa (PUT /api/tarifes/{id})
- [x] 9.5 Implement delete tarifa (DELETE /api/tarifes/{id}) with confirmation
- [x] 9.6 Add activa/inactiva toggle for tariffs
