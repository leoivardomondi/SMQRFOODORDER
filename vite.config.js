import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

const lazyRouteComponents = () => ({
    name: 'lazy-route-components',
    enforce: 'pre',
    transform(code, id) {
        const normalizedId = id.replace(/\\/g, '/');
        if (!normalizedId.includes('/resources/js/router/modules/') || !normalizedId.endsWith('.js')) {
            return null;
        }

        const transformed = code.replace(
            /import\s+([A-Za-z0-9_]+Component)\s+from\s+['"]([^'"]+)['"];?/g,
            (statement, componentName, componentPath) => {
                const resolvedPath = componentPath.endsWith('.vue') ? componentPath : `${componentPath}.vue`;
                return `const ${componentName} = () => import('${resolvedPath}');`;
            }
        );

        return transformed === code ? null : { code: transformed, map: null };
    },
});

export default defineConfig({
    plugins: [
        lazyRouteComponents(),
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            'vue': 'vue/dist/vue.esm-bundler.js',
        },
        extensions: ['.mjs', '.js', '.ts', '.jsx', '.tsx', '.json', '.vue'],
    },
    optimizeDeps: {
        include: ['quill'],
    },
    server: {
        watch: {
            ignored: ['**/public/**'],
        },
    },
});
