import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { fileURLToPath, URL } from 'node:url'
import vuetify from 'vite-plugin-vuetify'

export default defineConfig({
  root: fileURLToPath(new URL('.', import.meta.url)),
  cacheDir: 'node_modules/.vite',
  plugins: [
    vue(),
    vuetify({ autoImport: true }),
  ],
  build: {
    outDir: 'dist',
    rollupOptions: {
      input: 'index.html'
    }
  },
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    }
  }
})
