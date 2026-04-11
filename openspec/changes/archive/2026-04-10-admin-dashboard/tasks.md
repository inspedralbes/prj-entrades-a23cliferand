## 1. Setup

- [x] 1.1 Install vue-chartjs and chart.js dependencies
- [x] 1.2 Create admin layout file `layouts/admin.vue` with sidebar structure

## 2. Sidebar Component

- [x] 2.1 Create AdminSidebar component with navigation links
- [x] 2.2 Add CSS styling for sidebar using existing CSS variables
- [x] 2.3 Implement active state highlighting based on current route

## 3. Dashboard Page

- [x] 3.1 Create admin dashboard page at `pages/admin/index.vue`
- [x] 3.2 Add dashboard layout that uses admin layout with sidebar
- [x] 3.3 Create dashboard content with statistics cards

## 4. Chart Components

- [x] 4.1 Create BarChart component for daily tickets
- [x] 4.2 Create DoughnutChart component for movie genres
- [x] 4.3 Add mock data for chart demonstration
- [x] 4.4 Style charts to match app theme colors

## 5. Styling

- [x] 5.1 Add admin-specific CSS styles to main.css if needed
- [x] 5.2 Ensure responsive design for sidebar and charts
- [x] 5.3 Verify visual consistency with main app theme

## 6. Backend API Stats

- [x] 6.1 Create AdminController in Laravel with stats endpoint
- [x] 6.2 Add route `GET /api/admin/stats` 
- [x] 6.3 Implement stats: reserves per dia (últims 7 dies)
- [x] 6.4 Implement stats: reserves per estat (pendent/confirmada/caducada)
- [x] 6.5 Implement stats: nombre de pel·lícules a Redis
- [x] 6.6 Implement stats: nombre de sales i sessions actives

## 7. Navbar Admin Link

- [x] 7.1 Check guestStore for user role (admin vs client)
- [x] 7.2 Add "Admin" link in NavBar visible only for admin users
- [x] 7.3 Style admin link consistently with other nav elements

## 8. Chart Empty State

- [x] 8.1 Handle API error response in dashboard
- [x] 8.2 Display "No hi ha dades disponibles" when API returns empty
- [x] 8.3 Style empty state message to match app theme