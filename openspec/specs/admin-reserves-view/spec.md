# admin-reserves-view Specification

## Purpose
TBD - created by archiving change admin-crud-screens. Update Purpose after archive.
## Requirements
### Requirement: Admin can view Reserves list
The admin SHALL display a read-only list of all reservations.

#### Scenario: Admin accesses Reserves page
- **WHEN** admin navigates to `/admin/reserves`
- **THEN** the system displays a table with columns: ID, Usuari/Email, Sessió, Estat, Preu Total, Data

### Requirement: Admin can view Reserve details
The admin SHALL display detailed information for a selected reservation.

#### Scenario: Admin views reservation details
- **WHEN** admin clicks on a reserve row
- **THEN** the system displays a modal with: Usuari, Email, Sessió (movie, sala, datetime), Estat, Seients reservats, Preu total

### Requirement: Reserves page is read-only
The admin SHALL NOT have create, edit, or delete options for reserves.

#### Scenario: No modification options visible
- **WHEN** admin is on the Reserves page
- **THEN** there are no buttons for creating, editing, or deleting reserves

