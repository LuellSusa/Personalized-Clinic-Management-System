# React page structure

Each React-rendered screen has its own component and dedicated stylesheet:

- `LandingPage.jsx` → `resources/css/pages/landing.css`
- `DashboardPage.jsx` → `resources/css/pages/dashboard.css`
- `AdminDashboardPage.jsx` → `resources/css/pages/admin-dashboard.css`

To add another React page:

1. Create `NewPage.jsx` in this folder.
2. Create `resources/css/pages/new-page.css`.
3. Import that CSS only from `NewPage.jsx`.
4. Register the page loader in `resources/js/app.jsx`.
5. Give the Blade mount element the matching `data-page` value.

Dynamic imports ensure a page's CSS chunk is loaded only when that page is rendered.
