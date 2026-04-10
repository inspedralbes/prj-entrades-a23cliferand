## 1. Setup

- [ ] 1.1 Install vue-chartjs and chart.js dependencies
- [ ] 1.2 Create admin layout file `layouts/admin.vue` with sidebar structure

## 2. Sidebar Component

- [ ] 2.1 Create AdminSidebar component with navigation links
- [ ] 2.2 Add CSS styling for sidebar using existing CSS variables
- [ ] 2.3 Implement active state highlighting based on current route

## 3. Dashboard Page

- [ ] 3.1 Create admin dashboard page at `pages/admin/index.vue`
- [ ] 3.2 Add dashboard layout that uses admin layout with sidebar
- [ ] 3.3 Create dashboard content with statistics cards

## 4. Chart Components

- [ ] 4.1 Create BarChart component for daily tickets
- [ ] 4.2 Create DoughnutChart component for movie genres
- [ ] 4.3 Add mock data for chart demonstration
- [ ] 4.4 Style charts to match app theme colors

## 5. Styling

- [ ] 5.1 Add admin-specific CSS styles to main.css if needed
- [ ] 5.2 Ensure responsive design for sidebar and charts
- [ ] 5.3 Verify visual consistency with main app theme

## 6. Backend API Stats

- [ ] 6.1 Create AdminController in Laravel with stats endpoint
- [ ] 6.2 Add route `GET /api/admin/stats` 
- [ ] 6.3 Implement stats: reserves per dia (últims 7 dies)
- [ ] 6.4 Implement stats: reserves per estat (pendent/confirmada/caducada)
- [ ] 6.5 Implement stats: nombre de pel·lícules a Redis
- [ ] 6.6 Implement stats: nombre de sales i sessions actives

## 7. Navbar Admin Link

- [ ] 7.1 Check guestStore for user role (admin vs client)
- [ ] 7.2 Add "Admin" link in NavBar visible only for admin users
- [ ] 7.3 Style admin link consistently with other nav elements

## 8. Chart Empty State

- [ ] 8.1 Handle API error response in dashboard
- [ ] 8.2 Display "No hi ha dades disponibles" when API returns empty
- [ ] 8.3 Style empty state message to match app theme