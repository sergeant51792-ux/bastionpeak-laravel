import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';
export default defineConfig({
  build: { outDir: 'build-test-min', emptyOutDir: true, rollupOptions: { input: 'test-min.css' } },
  plugins: [tailwindcss()],
  logLevel: 'info',
});
