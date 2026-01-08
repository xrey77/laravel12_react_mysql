import './bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import DefaultLayout from './Layouts/DefaultLayout';
import React, { ReactNode } from 'react';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: async (name) => {
        // 1. Resolve the page component
        const page = await resolvePageComponent(
            `./pages/${name}.tsx`, 
            import.meta.glob('./pages/**/*.tsx')
        );

        // 2. Extract the default export (the actual component)
        const module = page.default;

        // 3. Assign a default layout if the page doesn't have one defined
        // Use ReactNode for proper TypeScript typing of the children
        module.layout = module.layout || ((page: ReactNode) => <DefaultLayout>{page}</DefaultLayout>);

        return module;
    },
    setup({ el, App, props }) {
        const root = createRoot(el);
        root.render(<App {...props} />);
    },
    progress: {
        color: '#4B5563',
    },
});


// import './bootstrap';
// import 'bootstrap/dist/css/bootstrap.min.css';
// import 'bootstrap/dist/js/bootstrap.bundle.min.js';
// import { createInertiaApp } from '@inertiajs/react';
// import { createRoot } from 'react-dom/client';
// import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
// import DefaultLayout from './Layouts/DefaultLayout';
// import React from 'react';

// // import '../css/app.css';


// const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// createInertiaApp({
//     title: (title) => `${title} - ${appName}`,
//     resolve: async (name) => {
//         const page = (await resolvePageComponent(`./pages/${name}.tsx`, import.meta.glob('./pages/**/*.tsx'))).default;
        
//         // If the page component has a layout, use it; otherwise, use the default layout
//         page.layout = page.layout || ((page: React.ReactNode) => <DefaultLayout>{page}</DefaultLayout>);
        
//         return page;
//     },
//     setup({ el, App, props }) {
//         const root = createRoot(el);

//         root.render(<App {...props} />);
//     },
//     progress: {
//         color: '#4B5563',
//     },
// });

// // const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
// // const appName ="Reynald";
// // createInertiaApp({
// //     title: (title) => `${title} - ${appName}`,
// //     resolve: (name) => 
// //         resolvePageComponent(
// //             `./pages/${name}.tsx`,
// //             import.meta.glob('./pages/**/*.tsx'),
// //         ),
// //     setup({ el, App, props }) {
// //         const root = createRoot(el);

// //         root.render(<App {...props} />);
// //     },
// //     progress: {
// //         color: '#4B5563',
// //     },
// //         // page.layout = page.layout || ((page: React.ReactNode) => <DefaultLayout>{page}</DefaultLayout>);        
// //         // return page;

// // });

