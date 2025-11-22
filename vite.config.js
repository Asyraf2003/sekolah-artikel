import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    server: {
        host: '0.0.0.0',
        port: 5173,
	strictPort: true,
        hmr: {
             host: 'school.local',
             protocol: 'http',
	     port: 5173,
        }
    },    
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: false,
	    hotFile: 'storage/vite.hot',
        }),
    ],
});
