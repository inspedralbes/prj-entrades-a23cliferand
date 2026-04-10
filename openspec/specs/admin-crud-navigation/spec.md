# admin-crud-navigation Specification

## Purpose
TBD - created by archiving change admin-dashboard. Update Purpose after archive.
## Requirements
### Requirement: Sidebar navigation links
The sidebar SHALL provide clickable navigation links that route to the respective CRUD pages for managing data.

#### Scenario: Admin clicks navigation link
- **WHEN** admin clicks on "Pel·lícules" in the sidebar
- **THEN** the system navigates to `/admin/pelicules`

#### Scenario: Admin clicks Sessions link
- **WHEN** admin clicks on "Sessions" in the sidebar
- **THEN** the system navigates to `/admin/sessions`

#### Scenario: Admin clicks Sales link
- **WHEN** admin clicks on "Sales" in the sidebar
- **THEN** the system navigates to `/admin/sales`

#### Scenario: Admin clicks Reserves link
- **WHEN** admin clicks on "Reserves" in the sidebar
- **THEN** the system navigates to `/admin/reserves`

### Requirement: Dashboard link in sidebar
The sidebar SHALL include a link back to the main dashboard.

#### Scenario: Admin clicks Dashboard link
- **WHEN** admin clicks on "Dashboard" in the sidebar
- **THEN** the system navigates to `/admin`

### Requirement: Active state indication
The sidebar SHALL highlight the currently active navigation item.

#### Scenario: Active page is highlighted
- **WHEN** admin is on `/admin/pelicules`
- **THEN** the "Pel·lícules" link in sidebar shows active state styling

