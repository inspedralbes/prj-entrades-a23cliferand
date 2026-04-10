## ADDED Requirements

### Requirement: Admin can view Sales list
The admin SHALL display a list of all cinema halls (sales).

#### Scenario: Admin accesses Sales page
- **WHEN** admin navigates to `/admin/sales`
- **THEN** the system displays a table with columns: ID, Nom, Capacitat, Files, Columnes

### Requirement: Admin can create a new Sala
The admin SHALL provide a form to create a new cinema hall.

#### Scenario: Admin creates sala
- **WHEN** admin clicks "Nova Sala" button
- **THEN** the system displays a form with fields: Nom, Capacitat, Files Màx, Columnes Màx
- **AND** admin clicks "Guardar"
- **THEN** the system sends POST to `/api/sales`
- **AND** the sala list refreshes

### Requirement: Admin can edit a Sala
The admin SHALL provide a form to edit an existing sala.

#### Scenario: Admin edits sala
- **WHEN** admin clicks "Editar" on a sala row
- **THEN** the system displays a form pre-filled with sala data
- **AND** admin modifies fields and clicks "Guardar"
- **THEN** the system sends PUT to `/api/sales/{id}`
- **AND** the sala list refreshes

### Requirement: Admin can delete a Sala
The admin SHALL provide a button to delete a sala.

#### Scenario: Admin deletes sala
- **WHEN** admin clicks "Eliminar" on a sala row
- **THEN** the system displays a confirmation dialog
- **AND** admin confirms deletion
- **THEN** the system sends DELETE to `/api/sales/{id}`
- **AND** the sala list refreshes
