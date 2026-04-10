## ADDED Requirements

### Requirement: Admin can view Sessions list
The admin SHALL display a list of all sessions with related movie and sala information.

#### Scenario: Admin accesses Sessions page
- **WHEN** admin navigates to `/admin/sessions`
- **THEN** the system displays a table with columns: ID, Pel·lícula, Sala, Data/Hora, Tarifa

### Requirement: Admin can create a new Session
The admin SHALL provide a form to create a new session linked to a Pel·lícula and Sala.

#### Scenario: Admin creates session
- **WHEN** admin clicks "Nou" button
- **THEN** the system displays a form with fields: Pel·lícula (dropdown), Sala (dropdown), Data/Hora (datetime picker), Tarifa (dropdown)
- **AND** admin clicks "Guardar"
- **THEN** the system sends POST to `/api/sessions`
- **AND** the session list refreshes

### Requirement: Admin can edit a Session
The admin SHALL provide a form to edit an existing session.

#### Scenario: Admin edits session
- **WHEN** admin clicks "Editar" on a session row
- **THEN** the system displays a form pre-filled with session data
- **AND** admin modifies fields and clicks "Guardar"
- **THEN** the system sends PUT to `/api/sessions/{id}`
- **AND** the session list refreshes

### Requirement: Admin can delete a Session
The admin SHALL provide a button to delete a session.

#### Scenario: Admin deletes session
- **WHEN** admin clicks "Eliminar" on a session row
- **THEN** the system displays a confirmation dialog
- **AND** admin confirms deletion
- **THEN** the system sends DELETE to `/api/sessions/{id}`
- **AND** the session list refreshes

### Requirement: Pel·lícula dropdown loads from API
The admin SHALL fetch available movies from `/api/pelicules` for session creation/editing.

#### Scenario: Dropdown populates with movies
- **WHEN** admin opens the Pel·lícula dropdown in the session form
- **THEN** the system loads and displays all movies from Redis
