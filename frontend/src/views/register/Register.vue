<template>
  <v-theme-provider theme="light">
  <div class="auth-layout">
    <div class="auth-card">

      <h2 class="card-title">Criar conta</h2>
      <p class="card-subtitle"></p>

      <v-form ref="formRef" @submit.prevent="handleRegister">

        <div class="field-group">
          <label class="field-label">Nome Completo</label>
          <v-text-field
            v-model="name"
            placeholder="Seu nome completo"
            variant="outlined"
            density="comfortable"
            prepend-inner-icon="mdi-account-outline"
            :rules="[v => !!v || 'Nome é obrigatório']"
            hide-details="auto"
          ></v-text-field>
        </div>

        <div class="field-group">
          <label class="field-label">CPF</label>
          <v-text-field
            v-model="formattedCpf"
            placeholder="000.000.000-00"
            variant="outlined"
            density="comfortable"
            prepend-inner-icon="mdi-card-account-details-outline"
            maxlength="14"
            :rules="[v => !!v || 'CPF é obrigatório']"
            hide-details="auto"
            @input="handleCpfInput"
          ></v-text-field>
        </div>

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
            placeholder="Mínimo 6 caracteres"
            variant="outlined"
            density="comfortable"
            prepend-inner-icon="mdi-lock-outline"
            :append-inner-icon="showPass ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
            @click:append-inner="showPass = !showPass"
            :rules="[v => !!v || 'Senha é obrigatória', v => v.length >= 6 || 'Mínimo 6 caracteres']"
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
          <span>Criar Conta</span>
          <v-icon end>mdi-arrow-right</v-icon>
        </v-btn>
      </v-form>

      <div class="card-footer">
        <span class="footer-text">Já tem uma conta?</span>
        <router-link to="/login" class="footer-link">Fazer login</router-link>
      </div>
    </div>

    <!-- Success toast -->
    <transition name="toast">
      <div v-if="showSuccess" class="success-toast">
        <v-icon size="20" color="white">mdi-check-circle</v-icon>
        <span>Conta criada com sucesso!</span>
      </div>
    </transition>
  </div>
  </v-theme-provider>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import './register.css'

const router = useRouter()
const authStore = useAuthStore()

const formRef = ref(null)
const name = ref('')
const cpf = ref('')
const email = ref('')
const password = ref('')
const loading = ref(false)
const error = ref('')
const showPass = ref(false)
const showSuccess = ref(false)

const formattedCpf = computed({
  get: () => cpf.value,
  set: (val) => {
    let v = val.replace(/\D/g, '')
    if (v.length > 11) v = v.substring(0, 11)
    if (v.length > 9)      v = v.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4')
    else if (v.length > 6) v = v.replace(/(\d{3})(\d{3})(\d{3})/,        '$1.$2.$3')
    else if (v.length > 3) v = v.replace(/(\d{3})(\d{3})/,               '$1.$2')
    cpf.value = v
  }
})

const handleCpfInput = (e) => {
  formattedCpf.value = e.target.value
}

const handleRegister = async () => {
  const { valid } = await formRef.value.validate()
  if (!valid) return

  loading.value = true
  error.value = ''

  try {
    const rawCpf = cpf.value.replace(/\D/g, '')
    await authStore.register({
      nome: name.value,
      cpf: rawCpf,
      email: email.value,
      senha: password.value,
      tipo_usuario: 2
    })
    
    // Auto-login after successful registration
    await authStore.login(email.value, password.value)
    
    showSuccess.value = true
    setTimeout(() => router.push('/dashboard'), 1500)
  } catch (err) {
    error.value = err.response?.data?.message || 'Erro ao registrar. Verifique os dados.'
  } finally {
    loading.value = false
  }
}
</script>
