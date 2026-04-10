## 1. Base Components

- [ ] 1.1 Create AdminTable.vue reusable component for listing data
- [ ] 1.2 Create AdminModal.vue reusable component for forms
- [ ] 1.3 Create AdminFormField.vue component for form inputs

## 2. Admin Usuaris CRUD

- [ ] 2.1 Create `/admin/usuaris` page with list view
- [ ] 2.2 Add "Nou Usuari" button and create modal form
- [ ] 2.3 Implement create usuari (POST /api/usuaris)
- [ ] 2.4 Implement edit usuari (PUT /api/usuaris/{id})
- [ ] 2.5 Implement delete usuari (DELETE /api/usuaris/{id}) with confirmation
- [ ] 2.6 Add role dropdown (admin/client) to form

## 3. Admin Sales CRUD

- [ ] 3.1 Create `/admin/sales` page with list view
- [ ] 3.2 Add "Nova Sala" button and create modal form
- [ ] 3.3 Implement create sala (POST /api/sales)
- [ ] 3.4 Implement edit sala (PUT /api/sales/{id})
- [ ] 3.5 Implement delete sala (DELETE /api/sales/{id}) with confirmation

## 4. Admin Sessions CRUD

- [ ] 4.1 Create `/admin/sessions` page with list view
- [ ] 4.2 Add Pel·lícula dropdown that fetches from /api/pelicules
- [ ] 4.3 Add Sala dropdown that fetches from /api/sales
- [ ] 4.4 Add Tarifa dropdown that fetches from /api/tarifes
- [ ] 4.5 Add "Nova Sessió" button and create modal form
- [ ] 4.6 Implement create session (POST /api/sessions)
- [ ] 4.7 Implement edit session (PUT /api/sessions/{id})
- [ ] 4.8 Implement delete session (DELETE /api/sessions/{id}) with confirmation

## 5. Admin Pel·lícules Management

- [ ] 5.1 Create `/admin/pelicules` page with list view from Redis
- [ ] 5.2 Add IMDb ID input field for single sync
- [ ] 5.3 Implement single movie sync (POST /api/pelicules/sync/{imdbId})
- [ ] 5.4 Add "Sincronitzar Tot" button
- [ ] 5.5 Implement full movie sync (POST /api/pelicules/sync/all)
- [ ] 5.6 Add movie details modal with poster and synopsis

## 6. Admin Reserves View (Read-only)

- [ ] 6.1 Create `/admin/reserves` page with list view
- [ ] 6.2 Display reserve details: usuari, sessió, seients, preu
- [ ] 6.3 Remove all create/edit/delete buttons from reserves view
- [ ] 6.4 Add view details modal for each reserve

## 7. Admin Data Sync / Reset

- [ ] 7.1 Add "Reset Base de Dades" button to Pel·lícules page
- [ ] 7.2 Implement confirmation dialog with warning
- [ ] 7.3 Create backend endpoint for database reset (if needed)
- [ ] 7.4 Run migrations fresh and re-seed data
- [ ] 7.5 Trigger full movie sync after reset

## 8. Sidebar Navigation Update

- [ ] 8.1 Verify AdminSidebar has all required navigation links
- [ ] 8.2 Ensure "Usuaris" link routes to /admin/usuaris
- [ ] 8.3 Test active state highlighting for all pages
