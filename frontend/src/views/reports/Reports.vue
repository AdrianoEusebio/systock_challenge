<template>
  <div class="dashboard-root">

    <!-- Sidebar -->
    <aside class="sidebar" :class="{ 'sidebar-collapsed': !drawer }">
      <div class="sidebar-logo">
        <div class="logo-icon">
          <v-icon size="28" color="white">mdi-package-variant-closed</v-icon>
        </div>
        <transition name="fade-text">
          <div class="logo-text" v-show="drawer">
            <span class="logo-title">SYSTOCK</span>
            <span class="logo-sub">Gestão de Estoque</span>
          </div>
        </transition>
      </div>

      <nav class="sidebar-nav">
        <div class="nav-section-label" v-show="drawer">MENU</div>
        <div class="nav-item" :class="{ 'nav-item--active': $route.path === '/dashboard' }" @click="router.push('/dashboard')">
          <div class="nav-icon"><v-icon size="20">mdi-view-dashboard-outline</v-icon></div>
          <span class="nav-label" v-show="drawer">Produtos</span>
          <div class="nav-indicator" v-show="$route.path === '/dashboard' && drawer"></div>
        </div>
        <div class="nav-item" :class="{ 'nav-item--active': $route.path === '/users' }" @click="router.push('/users')">
          <div class="nav-icon"><v-icon size="20">mdi-account-group-outline</v-icon></div>
          <span class="nav-label" v-show="drawer">Usuários</span>
          <div class="nav-indicator" v-show="$route.path === '/users' && drawer"></div>
        </div>
        <div class="nav-item" :class="{ 'nav-item--active': $route.path === '/reports' }" @click="router.push('/reports')" v-if="isAdmin">
          <div class="nav-icon"><v-icon size="20">mdi-chart-bar</v-icon></div>
          <span class="nav-label" v-show="drawer">Relatórios</span>
          <div class="nav-indicator" v-show="$route.path === '/reports' && drawer"></div>
        </div>
      </nav>

      <div class="sidebar-footer">
        <div class="user-card" v-show="drawer">
          <div class="user-avatar">{{ userInitials }}</div>
          <div class="user-info">
            <div class="user-name">{{ firstName }}</div>
            <div class="user-role">{{ authStore.user?.tipo_usuario || 'Cliente' }}</div>
          </div>
        </div>
        <button class="logout-btn" @click="handleLogout" :title="!drawer ? 'Sair' : ''">
          <v-icon size="18">mdi-logout</v-icon>
          <span v-show="drawer">Sair</span>
        </button>
      </div>
    </aside>

    <div class="main-area" @click="drawer = false">

      <header class="topbar">
        <button class="toggle-btn" @click.stop="drawer = !drawer">
          <v-icon size="22">{{ drawer ? 'mdi-menu-open' : 'mdi-menu' }}</v-icon>
        </button>
        <div class="topbar-title">
          <h2 class="page-title">Relatórios</h2>
          <span class="page-breadcrumb">Dashboard / Relatórios</span>
        </div>
        <div class="topbar-actions">
          <div class="topbar-avatar">{{ userInitials }}</div>
        </div>
      </header>

      <!-- Content -->
      <main class="content">
        <div class="reports-grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;" v-if="loading">
          <div class="table-card" style="padding: 20px;">
            <div class="skeleton-box" style="height: 40px; width: 60%; margin-bottom: 24px;"></div>
            <div class="skeleton-box" style="height: 52px; width: 100%;"></div>
            <div class="skeleton-box" style="height: 52px; width: 100%; opacity: 0.85;"></div>
            <div class="skeleton-box" style="height: 52px; width: 100%; opacity: 0.7;"></div>
            <div class="skeleton-box" style="height: 52px; width: 100%; opacity: 0.5;"></div>
            <div class="skeleton-box" style="height: 52px; width: 100%; opacity: 0.3;"></div>
          </div>
          <div style="display: flex; flex-direction: column; gap: 20px;">
             <div class="table-card" style="padding: 20px;">
               <div class="skeleton-box" style="height: 30px; width: 70%; margin-bottom: 20px;"></div>
               <div class="skeleton-box" style="height: 45px; width: 100%;"></div>
               <div class="skeleton-box" style="height: 45px; width: 100%; opacity: 0.7;"></div>
             </div>
             <div class="table-card" style="padding: 20px;">
               <div class="skeleton-box" style="height: 30px; width: 70%; margin-bottom: 20px;"></div>
               <div class="skeleton-box" style="height: 45px; width: 100%;"></div>
               <div class="skeleton-box" style="height: 45px; width: 100%; opacity: 0.7;"></div>
             </div>
          </div>
        </div>
        
        <div class="reports-grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;" v-else>
          
          <!-- PRODUTOS MAIS CAROS (Left, Larger) -->
          <div class="table-card" style="padding: 10px;">
            <div class="card-header">
              <div class="card-header-icon"><v-icon size="16" color="white">mdi-star</v-icon></div>
              <h3 class="card-title">Produtos Mais Caros por Usuário</h3>
            </div>
            <div class="table-wrapper">
              <table class="product-table">
                <thead>
                  <tr>
                    <th>Usuário</th>
                    <th>Produto Top #1</th>
                    <th>Preço Máximo</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in produtosCaros" :key="index">
                    <td>{{ item.nome_usuario }}</td>
                    <td>{{ item.nome_produto }}</td>
                    <td><span class="price-badge">{{ formatCurrency(item.preco) }}</span></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Section for smaller reports (Right, Stacked) -->
          <div style="display: flex; flex-direction: column; gap: 20px;">
            
            <!-- MAIORES ESTOQUES -->
            <div class="table-card" style="padding: 10px;">
              <div class="card-header">
                <div class="card-header-icon"><v-icon size="16" color="white">mdi-poll</v-icon></div>
                <h3 class="card-title">Maiores Estoques</h3>
              </div>
              <div class="table-wrapper">
                <table class="product-table">
                  <thead>
                    <tr>
                      <th>Nome (Usuário)</th>
                      <th>Total de Produtos</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="item in maioresEstoques" :key="item.id">
                      <td>{{ item.nome }}</td>
                      <td><span class="price-badge" style="background: #e0e7ff; color: #3730a3">{{ item.total_produtos }} unid.</span></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- FAIXAS DE PREÇO -->
            <div class="table-card" style="padding: 10px;">
              <div class="card-header">
                <div class="card-header-icon"><v-icon size="16" color="white">mdi-chart-pie</v-icon></div>
                <h3 class="card-title">Faixas de Preço</h3>
              </div>
              <div class="table-wrapper">
                <table class="product-table">
                  <thead>
                    <tr>
                      <th>Faixa de Preço</th>
                      <th>Quantidade</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(item, index) in faixas" :key="index">
                      <td>{{ item.faixa_preco }}</td>
                      <td><span class="price-badge" style="background: #dbeafe; color: #1e40af">{{ item.quantidade }} un.</span></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            
          </div>
          
        </div>
      </main>
    </div>

    <!-- Toast notification -->
    <transition name="toast">
      <div v-if="toast.show" class="toast" :class="`toast--${toast.type}`">
        <v-icon size="18">{{ toast.type === 'success' ? 'mdi-check-circle' : 'mdi-alert-circle' }}</v-icon>
        <span>{{ toast.text }}</span>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import api from '@/services/api'
import '../dashboard/dashboard.css'

const router = useRouter()
const authStore = useAuthStore()

const drawer = ref(localStorage.getItem('sys_drawer') !== 'false')
watch(drawer, (val) => localStorage.setItem('sys_drawer', String(val)))
const loading = ref(false)

const maioresEstoques = ref([])
const produtosCaros = ref([])
const faixas = ref([])

const isAdmin = computed(() => authStore.user?.tipo_usuario === 'Administrador')

const toast = reactive({ show: false, text: '', type: 'success' })

const userInitials = computed(() => {
  const nome = authStore.user?.nome || ''
  return nome.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2) || 'U'
})

const firstName = computed(() => {
  return authStore.user?.nome?.split(' ')[0] || 'Usuário'
})

const fetchReports = async () => {
  loading.value = true
  try {
    const [estoqueRes, carosRes, faixasRes] = await Promise.all([
      api.get('relatorio/maiores-estoques'),
      api.get('relatorio/produtos-mais-caros'),
      api.get('relatorio/faixas-precos')
    ])
    
    maioresEstoques.value = estoqueRes.data.data
    produtosCaros.value = carosRes.data.data
    faixas.value = faixasRes.data.data
  } catch {
    showToast('Erro ao carregar os relatórios. Sem permissão ou erro interno.', 'error')
  } finally {
    loading.value = false
  }
}

const handleLogout = () => {
  authStore.logout()
  router.push('/login')
}

const formatCurrency = (value) => {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value)
}

let toastTimer = null
const showToast = (text, type = 'success') => {
  toast.text = text
  toast.type = type
  toast.show = true
  clearTimeout(toastTimer)
  toastTimer = setTimeout(() => { toast.show = false }, 3500)
}

onMounted(() => {
  if (!isAdmin.value) {
    router.push('/dashboard')
    return
  }
  fetchReports()
})
</script>
