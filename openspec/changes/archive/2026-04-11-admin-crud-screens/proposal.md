## Why

The admin dashboard needs functional CRUD screens to manage the application's core data. Currently there is no admin interface for managing pel·lícules, sessions, sales, reserves (view-only), and usuaris. Additionally, the movie synchronization system requires admin controls to reset and reload data from IMDb.

## What Changes

- Add admin CRUD pages for managing Pel·lícules (with IMDb ID autofill)
- Add admin CRUD page for Sessions linked to Pel·lícules
- Add admin CRUD page for Sales (cinema halls)
- Add admin view-only page for Reserves (read-only information display)
- Add admin CRUD page for Usuaris (users)
- Add database reset functionality to reload all movies
- Add individual movie sync button per Pel·lícula

## Capabilities

### New Capabilities

- `admin-pelicules-crud`: Admin page for managing Pel·lícules with IMDb ID sync
- `admin-sessions-crud`: Admin page for managing Sessions linked to Pel·lícules
- `admin-sales-crud`: Admin page for managing Sales (cinema halls)
- `admin-reserves-view`: Admin page for viewing Reserves (read-only)
- `admin-usuaris-crud`: Admin page for managing Usuaris
- `admin-data-sync`: Admin controls for database reset and movie synchronization

### Modified Capabilities

- `admin-crud-navigation`: Update sidebar links to include Usuaris page

## Impact

- **Frontend**: New pages at `/admin/pelicules`, `/admin/sessions`, `/admin/sales`, `/admin/reserves`, `/admin/usuaris`
- **Backend**: New admin API endpoints for CRUD operations and sync
- **Database**: No schema changes, uses existing tables (usuaris, pellicules, sessions_cine, sales, reserva)
