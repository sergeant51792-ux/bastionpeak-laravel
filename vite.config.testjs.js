import { defineConfig } from 'vite';
export default defineConfig({
  build: { outDir: 'build-test-js', emptyOutDir: true },
  plugins: [],
  logLevel: 'info',
});
