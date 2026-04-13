# admin-dashboard Specification

## Purpose
TBD - created by archiving change admin-dashboard. Update Purpose after archive.
## Requirements
### Requirement: Admin dashboard page
The system SHALL provide a main admin dashboard page accessible at `/admin` that serves as the central hub for administrators to view statistics and navigate to CRUD operations.

#### Scenario: Admin accesses dashboard
- **WHEN** an administrator navigates to `/admin`
- **THEN** the system displays the admin dashboard with sidebar navigation and chart widgets

### Requirement: Admin layout with sidebar
The admin section SHALL use a dedicated layout that includes a persistent sidebar for navigation between different admin sections.

#### Scenario: Sidebar displays navigation options
- **WHEN** admin dashboard is loaded
- **THEN** sidebar shows links to: Dashboard, Pel·lícules, Sessions, Sales, Reserves

### Requirement: Chart integration with vue-chartjs
The dashboard SHALL integrate vue-chartjs to display statistical charts showing business data.

#### Scenario: Charts render on dashboard
- **WHEN** admin dashboard loads
- **THEN** vue-chartjs charts are rendered showing sample/mock data for demonstration

### Requirement: Consistent styling with main app
The admin section SHALL use the same CSS variables and styling conventions as the main application to maintain visual consistency.

#### Scenario: Admin styles match main app
- **WHEN** admin dashboard is rendered
- **THEN** it uses existing CSS variables from main.css (dark theme, Sansation font, etc.)

