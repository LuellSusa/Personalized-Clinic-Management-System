# TitaClinic frontend guide

Laravel still handles routes, authentication, validation, and MySQL access. React renders
the modern pages and receives its initial data from the Laravel controller.

## Page files

| Screen | React component | Dedicated CSS |
| --- | --- | --- |
| Landing page | `resources/js/react/pages/LandingPage.jsx` | `resources/css/pages/landing.css` |
| Parent dashboard | `resources/js/react/pages/DashboardPage.jsx` | `resources/css/pages/dashboard.css` |
| Admin dashboard | `resources/js/react/pages/AdminDashboardPage.jsx` | `resources/css/pages/admin-dashboard.css` |

Styles are imported by their page component and compiled into separate chunks. Editing
`landing.css` does not change either dashboard, and editing `dashboard.css` does not
change the landing page or admin dashboard.

## Development

Run Laravel:

```powershell
php artisan serve
```

Run Vite in a second terminal:

```powershell
npm.cmd run dev
```

For a production build:

```powershell
npm.cmd run build
```

## Adding another React page

1. Add a component under `resources/js/react/pages`.
2. Add its stylesheet under `resources/css/pages`.
3. Import the stylesheet at the top of only that component.
4. Register a dynamic loader in `resources/js/app.jsx`.
5. Use a small Blade mount view with the matching `data-page` value.
6. Have the Laravel controller pass an explicit `pageProps` array.

Do not send full Eloquent models to React. Map only the fields the page needs, as the
dashboard controllers currently do.
