# admin-pelicules-crud Specification

## Purpose
TBD - created by archiving change admin-crud-screens. Update Purpose after archive.
## Requirements
### Requirement: Admin can view Pel·lícules list
The admin SHALL display a list of all movies fetched from Redis.

#### Scenario: Admin accesses Pel·lícules page
- **WHEN** admin navigates to `/admin/pelicules`
- **THEN** the system displays a table with columns: IMDb ID, Títol, Any, Gènere, Rating

### Requirement: Admin can sync a single Pel·lícula by IMDb ID
The admin SHALL provide a form to enter an IMDb ID and trigger synchronization.

#### Scenario: Admin syncs a single movie
- **WHEN** admin enters an IMDb ID and clicks "Sincronitzar"
- **THEN** the system sends POST to `/api/pelicules/sync/{imdbId}`
- **AND** the system displays a success or error message
- **AND** the movie list refreshes with updated data

### Requirement: Admin can sync all Pel·lícules
The admin SHALL provide a button to synchronize all movies from existing sessions.

#### Scenario: Admin triggers full sync
- **WHEN** admin clicks "Sincronitzar Tot"
- **THEN** the system sends POST to `/api/pelicules/sync/all`
- **AND** the system displays progress or completion message
- **AND** the movie list refreshes with all movies

### Requirement: Admin can view Pel·lícula details
The admin SHALL display detailed information for a selected movie.

#### Scenario: Admin views movie details
- **WHEN** admin clicks on a movie row
- **THEN** the system displays modal with: Títol, Títol Original, Any, Durada, Gènere, Sinopsi, Cartell

