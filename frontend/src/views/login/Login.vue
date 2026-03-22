<template>
  <v-theme-provider theme="light">
  <div class="auth-layout">
    <div class="login-card">

      <h2 class="card-title">Bem-vindo</h2>
      <p class="card-subtitle">Faça login para acessar sua conta</p>

      <v-form ref="formRef" @submit.prevent="handleLogin">

        <div class="field-group">
          <label class="field-label">Email</label>
          <v-text-field
            v-model="email"
            type="email"
            placeholder="seu@email.com"
            variant="outlined"
            density="comfortable"
            prepend-inner-icon="mdi-email-outline"
            :rules="[v => !!v || 'Email é obrigatório']"
            hide-details="auto"
          ></v-text-field>
        </div>

        <div class="field-group">
          <label class="field-label">Senha</label>
          <v-text-field
            v-model="password"
            :type="showPass ? 'text' : 'password'"
            placeholder="Digite sua senha"
            variant="outlined"
            density="comfortable"
            prepend-inner-icon="mdi-lock-outline"
            :append-inner-icon="showPass ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
            @click:append-inner="showPass = !showPass"
            :rules="[v => !!v || 'Senha é obrigatória']"
            hide-details="auto"
          ></v-text-field>
        </div>

        <v-alert
          v-if="error"
          type="error"
          variant="tonal"
          density="compact"
          rounded="lg"
          class="mb-4 mt-2"
          closable
          @click:close="error = ''"
        >
          {{ error }}
        </v-alert>

        <v-btn
          type="submit"
          size="large"
          block
          :loading="loading"
          class="submit-btn"
          elevation="0"
        >
          <span>Entrar</span>
          <v-icon end>mdi-arrow-right</v-icon>
        </v-btn>
      </v-form>

      <div class="card-footer">
        <span class="footer-text">Não tem uma conta?</span>
        <router-link to="/register" class="footer-link">Criar conta grátis</router-link>
      </div>
    </div>

    <!-- Success toast -->
    <transition name="toast">
      <div v-if="showSuccess" class="success-toast">
        <v-icon size="20" color="white">mdi-check-circle</v-icon>
        <span>Login realizado com sucesso!</span>
      </div>
    </transition>
  </div>
  </v-theme-provider>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import './login.css'

const router = useRouter()
const authStore = useAuthStore()

const formRef = ref(null)
const email = ref('')
const password = ref('')
const loading = ref(false)
const error = ref('')
const showPass = ref(false)
const showSuccess = ref(false)

const handleLogin = async () => {
  const { valid } = await formRef.value.validate()
  if (!valid) return

  loading.value = true
  error.value = ''

  try {
    await authStore.login(email.value, password.value)
    showSuccess.value = true
    setTimeout(() => router.push('/dashboard'), 1500)
  } catch (err) {
    error.value = err.response?.data?.message || 'Credenciais inválidas. Tente novamente.'
  } finally {
    loading.value = false
  }
}
</script>
