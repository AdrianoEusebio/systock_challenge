<template>
  <v-app>
    <!-- Premium loading screen -->
    <div v-if="loading" class="app-loading">
      <div class="loading-blobs">
        <div class="loading-blob lb-1"></div>
        <div class="loading-blob lb-2"></div>
      </div>
      <div class="loading-card">
        <div class="loading-logo">
          <v-icon size="40" color="white">mdi-package-variant-closed</v-icon>
        </div>
        <span class="loading-brand">SYSTOCK</span>
        <div class="loading-bar">
          <div class="loading-bar-fill"></div>
        </div>
        <span class="loading-text">Inicializando sistema...</span>
      </div>
    </div>

    <router-view v-else></router-view>
  </v-app>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const loading = ref(true)

onMounted(async () => {
  if (authStore.token) {
    try {
      await authStore.fetchMe()
    } catch (e) {
      console.error('Sessão inválida')
    }
  }
  // Small delay for smooth UX
  setTimeout(() => { loading.value = false }, 600)
})
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

*, *::before, *::after {
  box-sizing: border-box;
}

html, body, #app {
  margin: 0;
  padding: 0;
  font-family: 'Inter', sans-serif !important;
  -webkit-font-smoothing: antialiased;
}

/* Remove Vuetify default app background */
.v-application {
  background: transparent !important;
}

/* Loading screen */
.app-loading {
  position: fixed;
  inset: 0;
  background: #f8fafc;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}
.loading-blobs {
  position: absolute;
  inset: 0;
  pointer-events: none;
}
.loading-blob {
  position: absolute;
  border-radius: 50%;
  filter: blur(80px);
  opacity: 0.2;
  animation: blobFloat 6s ease-in-out infinite alternate;
}
.lb-1 {
  width: 400px; height: 400px;
  background: radial-gradient(circle, #bfdbfe, #93c5fd);
  top: -100px; left: -80px;
}
.lb-2 {
  width: 350px; height: 350px;
  background: radial-gradient(circle, #c7d2fe, #a5b4fc);
  bottom: -80px; right: -60px;
  animation-delay: -3s;
}
@keyframes blobFloat {
  from { transform: scale(1) translate(0, 0); }
  to   { transform: scale(1.08) translate(20px, -20px); }
}

.loading-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 16px;
  position: relative;
  z-index: 1;
}
.loading-logo {
  width: 72px;
  height: 72px;
  border-radius: 20px;
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 20px 40px rgba(37,99,235,0.25);
  animation: pulse 2s ease-in-out infinite;
}
@keyframes pulse {
  0%, 100% { box-shadow: 0 20px 40px rgba(37,99,235,0.25); }
  50% { box-shadow: 0 20px 50px rgba(37,99,235,0.4); }
}
.loading-brand {
  font-size: 1.4rem;
  font-weight: 900;
  letter-spacing: 4px;
  color: #1e293b;
}
.loading-bar {
  width: 200px;
  height: 4px;
  border-radius: 99px;
  background: #e2e8f0;
  overflow: hidden;
}
.loading-bar-fill {
  height: 100%;
  border-radius: 99px;
  background: linear-gradient(90deg, #60a5fa, #2563eb);
  animation: bar 1.5s ease-in-out infinite;
}
@keyframes bar {
  0% { width: 0%; margin-left: 0; }
  50% { width: 80%; margin-left: 0; }
  100% { width: 0%; margin-left: 100%; }
}
.loading-text {
  font-size: 0.78rem;
  color: #64748b;
  font-weight: 500;
  letter-spacing: 0.05em;
}
</style>
