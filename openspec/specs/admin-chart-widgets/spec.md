# admin-chart-widgets Specification

## Purpose
TBD - created by archiving change admin-dashboard. Update Purpose after archive.
## Requirements
### Requirement: Bar chart for daily tickets
The dashboard SHALL display a bar chart showing the number of tickets sold per day for the past week.

#### Scenario: Bar chart displays ticket data
- **WHEN** admin dashboard loads
- **THEN** a bar chart shows mock data for daily ticket sales (e.g., Mon-Sun with values)

### Requirement: Doughnut chart for movie genres
The dashboard SHALL display a doughnut/pie chart showing the distribution of movies by genre.

#### Scenario: Doughnut chart displays genre distribution
- **WHEN** admin dashboard loads
- **THEN** a doughnut chart shows mock data for movie genres (Acció, Comèdia, Drama, etc.)

### Requirement: Chart components are responsive
The chart widgets SHALL be responsive and adapt to different screen sizes.

#### Scenario: Charts resize on window resize
- **WHEN** browser window is resized
- **THEN** charts resize appropriately to maintain visibility

### Requirement: Chart color scheme matches app theme
The charts SHALL use colors that match the application's dark theme.

#### Scenario: Chart colors are consistent
- **WHEN** charts are rendered
- **THEN** they use accent colors from the app's CSS variables

