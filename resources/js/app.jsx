import './bootstrap';
import React from 'react';
import { createRoot } from 'react-dom/client';

const pageLoaders = {
    landing: () => import('./react/pages/LandingPage.jsx'),
    dashboard: () => import('./react/pages/DashboardPage.jsx'),
    'admin-dashboard': () => import('./react/pages/AdminDashboardPage.jsx'),
    'doctor-dashboard': () => import('./react/pages/DoctorDashboardPage.jsx'),
};

const rootElement = document.getElementById('react-root');

if (rootElement) {
    const pageName = rootElement.dataset.page;
    const propsElement = document.getElementById('page-props');
    const pageProps = propsElement ? JSON.parse(propsElement.textContent) : {};
    const loadPage = pageLoaders[pageName];

    if (!loadPage) {
        throw new Error(`Unknown React page: ${pageName}`);
    }

    loadPage().then(({ default: Page }) => {
        createRoot(rootElement).render(
            <React.StrictMode>
                <Page {...pageProps} />
            </React.StrictMode>,
        );
    });
}
