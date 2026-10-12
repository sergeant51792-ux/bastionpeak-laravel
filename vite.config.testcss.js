import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';
export default defineConfig({
  build: { outDir: 'build-test-css', emptyOutDir: true, rollupOptions: { input: 'resources/css/app.css' } },
  plugins: [tailwindcss()],
  logLevel: 'info',
});
