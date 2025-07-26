import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [
        {
            name: 'laravel',
            configureServer(server) {
                server.middlewares.use('/build', (req, res, next) => {
                    if (req.url.endsWith('.js') || req.url.endsWith('.css')) {
                        res.setHeader('Access-Control-Allow-Origin', '*');
                    }
                    next();
                });
            }
        }
    ],
    build: {
        manifest: true,
        outDir: 'public/build',
        rollupOptions: {
            input: {
                app: 'resources/js/app.js',
                css: 'resources/css/app.css'
            }
        }
    },
    server: {
        hmr: {
            host: 'localhost',
        },
    }
});
