# admin-data-sync Specification

## Purpose
TBD - created by archiving change admin-crud-screens. Update Purpose after archive.
## Requirements
### Requirement: Admin can reset database and reload all data
The admin SHALL provide a function to reset the database and reload all movies from IMDb.

#### Scenario: Admin triggers full database reset
- **WHEN** admin clicks "Reset BD" button
- **THEN** the system displays a confirmation dialog warning about data loss
- **AND** admin confirms
- **THEN** the system runs database migrations fresh
- **AND** the system runs seeders to populate initial data
- **AND** the system triggers full movie sync

### Requirement: Admin can reload individual movies
The admin SHALL provide a way to reload a specific movie by IMDb ID.

#### Scenario: Admin reloads single movie
- **WHEN** admin enters an IMDb ID in the sync form
- **AND** admin clicks "Sincronitzar"
- **THEN** the system sends POST to `/api/pelicules/sync/{imdbId}`
- **AND** the system displays success or error message

### Requirement: Admin can trigger mass sync of all movies
The admin SHALL provide a button to sync all movies from existing sessions.

#### Scenario: Admin triggers mass sync
- **WHEN** admin clicks "Sincronitzar Tot"
- **THEN** the system sends POST to `/api/pelicules/sync/all`
- **AND** the system displays progress indicator
- **AND** the system displays completion message with sync count

