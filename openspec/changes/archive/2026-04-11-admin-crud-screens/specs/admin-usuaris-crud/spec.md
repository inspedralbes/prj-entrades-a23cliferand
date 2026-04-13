## ADDED Requirements

### Requirement: Admin can view Usuaris list
The admin SHALL display a list of all users.

#### Scenario: Admin accesses Usuaris page
- **WHEN** admin navigates to `/admin/usuaris`
- **THEN** the system displays a table with columns: ID, Nom, Email, Rol, Data Registre

### Requirement: Admin can create a new Usuari
The admin SHALL provide a form to create a new user.

#### Scenario: Admin creates usuari
- **WHEN** admin clicks "Nou Usuari" button
- **THEN** the system displays a form with fields: Nom, Email, Password, Rol (dropdown: admin/client)
- **AND** admin clicks "Guardar"
- **THEN** the system sends POST to `/api/usuaris`
- **AND** the usuari list refreshes

### Requirement: Admin can edit a Usuari
The admin SHALL provide a form to edit an existing user.

#### Scenario: Admin edits usuari
- **WHEN** admin clicks "Editar" on a usuari row
- **THEN** the system displays a form pre-filled with user data
- **AND** admin modifies fields and clicks "Guardar"
- **THEN** the system sends PUT to `/api/usuaris/{id}`
- **AND** the usuari list refreshes

### Requirement: Admin can delete a Usuari
The admin SHALL provide a button to delete a user.

#### Scenario: Admin deletes usuari
- **WHEN** admin clicks "Eliminar" on a usuari row
- **THEN** the system displays a confirmation dialog
- **AND** admin confirms deletion
- **THEN** the system sends DELETE to `/api/usuaris/{id}`
- **AND** the usuari list refreshes
