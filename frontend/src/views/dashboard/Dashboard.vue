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
        <div class="nav-item" :class="{ 'nav-item--active': $route.path === '/reports' }" @click="router.push('/reports')" v-if="authStore.user?.tipo_usuario === 'Administrador'">
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
          <h2 class="page-title">Produtos</h2>
          <span class="page-breadcrumb">Dashboard / Produtos</span>
        </div>
        <div class="topbar-actions">
          <div class="search-box">
            <v-icon size="18" class="search-icon">mdi-magnify</v-icon>
            <input v-model="search" placeholder="Buscar produto..." class="search-input" />
          </div>
          <div class="topbar-avatar">{{ userInitials }}</div>
        </div>
      </header>

      <!-- Content -->
      <main class="content">

        <!-- Stat cards -->
        <div class="stat-grid">
          <div class="stat-card stat-card--blue">
            <div class="stat-icon"><v-icon size="24" color="white">mdi-package-variant</v-icon></div>
            <div class="stat-info">
              <div class="stat-value">{{ allProducts.length }}</div>
              <div class="stat-label">Total de Produtos</div>
            </div>
            <div class="stat-bg-icon">
              <!-- Using rgba so it matches the light/dark background appropriately -->
              <v-icon size="80" color="rgba(255,255,255,0.15)">mdi-package-variant</v-icon>
            </div>
          </div>
          <div class="stat-card stat-card--purple">
            <div class="stat-icon"><v-icon size="24" color="white">mdi-currency-brl</v-icon></div>
            <div class="stat-info">
              <div class="stat-value">{{ formatCurrency(totalValue) }}</div>
              <div class="stat-label">Valor Total em Estoque</div>
            </div>
            <div class="stat-bg-icon">
              <v-icon size="80" color="rgba(255,255,255,0.15)">mdi-currency-brl</v-icon>
            </div>
          </div>
          <div class="stat-card stat-card--emerald">
            <div class="stat-icon"><v-icon size="24" color="white">mdi-trending-up</v-icon></div>
            <div class="stat-info">
              <div class="stat-value">{{ formatCurrency(avgPrice) }}</div>
              <div class="stat-label">Preço Médio</div>
            </div>
            <div class="stat-bg-icon">
              <v-icon size="80" color="rgba(255,255,255,0.15)">mdi-trending-up</v-icon>
            </div>
          </div>
        </div>

        <!-- Bottom row: form + table -->
        <div class="bottom-grid">

          <!-- Product form -->
          <div class="form-card">
            <div class="card-header">
              <div class="card-header-icon">
                <v-icon size="16" color="white">{{ editingId ? 'mdi-pencil' : 'mdi-plus' }}</v-icon>
              </div>
              <h3 class="card-title">{{ editingId ? 'Editar Produto' : 'Novo Produto' }}</h3>
            </div>

            <form @submit.prevent="handleSubmit" class="product-form">
              <div class="form-row">
                <div class="field-group field-group--name">
                  <label class="field-label">Nome do Produto</label>
                  <div class="field-wrapper">
                    <input
                      v-model="productForm.nome"
                      placeholder="Ex: Notebook"
                      class="form-input"
                      required
                    />
                  </div>
                </div>

                <div class="field-group field-group--price">
                  <label class="field-label">Preço (R$)</label>
                  <div class="field-wrapper">
                    <input
                      :value="formattedPreco"
                      @input="updatePreco"
                      type="text"
                      placeholder="0,00"
                      class="form-input"
                      required
                    />
                  </div>
                </div>

                <div class="field-group field-group--desc">
                  <label class="field-label">Descrição</label>
                  <div class="field-wrapper field-wrapper--textarea">
                    <textarea
                      v-model="productForm.descricao"
                      placeholder="Descrição do produto"
                      class="form-input form-textarea"
                      required
                    ></textarea>
                  </div>
                </div>
              </div>

              <div class="form-actions">
                <button type="submit" class="btn-primary" :disabled="submitting">
                  <span>{{ submitting ? 'Salvando...' : (editingId ? 'Atualizar' : 'Adicionar') }}</span>
                </button>
                <button v-if="editingId" type="button" class="btn-ghost" @click="resetForm">
                  Cancelar
                </button>
              </div>
            </form>
          </div>

          <!-- Product table -->
          <div class="table-card">
            <div class="card-header">
              <div class="card-header-icon">
                <v-icon size="16" color="white">mdi-list-box-outline</v-icon>
              </div>
              <h3 class="card-title">Lista de Produtos</h3>
              <button class="refresh-btn" @click="fetchProducts" :disabled="loading">
                <v-icon size="16" :class="{ 'rotating': loading }">mdi-refresh</v-icon>
              </button>
            </div>

            <!-- Empty state -->
            <div v-if="!loading && filteredProducts.length === 0" class="empty-state">
              <p class="empty-title">{{ search ? 'Nenhum produto encontrado' : 'Nenhum produto cadastrado. Adicione seu primeiro produto acima.' }}</p>
            </div>

            <!-- Loading (Skeleton) -->
            <div v-else-if="loading" style="padding: 20px; display: flex; flex-direction: column; gap: 8px;">
              <div class="skeleton-box" style="height: 52px; width: 100%;"></div>
              <div class="skeleton-box" style="height: 52px; width: 100%; opacity: 0.85;"></div>
              <div class="skeleton-box" style="height: 52px; width: 100%; opacity: 0.7;"></div>
              <div class="skeleton-box" style="height: 52px; width: 100%; opacity: 0.5;"></div>
              <div class="skeleton-box" style="height: 52px; width: 100%; opacity: 0.3;"></div>
            </div>

            <!-- Table -->
            <div v-else class="table-wrapper" style="max-height: 655px; overflow-y: auto;">
              <table class="product-table">
                <thead>
                  <tr>
                    <th>Produto</th>
                    <th v-if="authStore.user?.tipo_usuario === 'Administrador'">Usuário</th>
                    <th>Descrição</th>
                    <th>Preço</th>
                    <th>Ações</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="product in filteredProducts"
                    :key="product.id"
                    class="table-row"
                    :class="{ 'table-row--editing': editingId === product.id }"
                  >
                    <td>
                      <div class="product-name-cell">
                        <div class="product-avatar">{{ product.nome[0]?.toUpperCase() }}</div>
                        <span class="product-name">{{ product.nome }}</span>
                      </div>
                    </td>
                    <td v-if="authStore.user?.tipo_usuario === 'Administrador'">
                      <div style="display: flex; align-items: center; gap: 8px;">
                        <v-icon size="16" color="rgba(30, 41, 59, 0.4)">mdi-account</v-icon>
                        <span style="font-weight: 500; font-size: 0.8rem; color: #475569;">{{ product.usuario?.nome || 'Desconhecido' }}</span>
                      </div>
                    </td>
                    <td class="product-desc">{{ product.descricao }}</td>
                    <td>
                      <span class="price-badge">{{ formatCurrency(product.preco) }}</span>
                    </td>
                    <td>
                      <div class="action-btns">
                        <button class="action-btn action-btn--edit" @click="editProduct(product)" title="Editar">
                          <v-icon size="15">mdi-pencil-outline</v-icon>
                        </button>
                        <button class="action-btn action-btn--delete" @click="confirmDeleteProduct(product.id)" title="Remover">
                          <v-icon size="15">mdi-trash-can-outline</v-icon>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            
            <!-- Pagination Controls -->
            <div class="pagination" v-if="lastPage > 1">
              <button class="btn-ghost" :disabled="currentPage === 1" @click="changePage(currentPage - 1)">Anterior</button>
              <span class="pagination-info">Página {{ currentPage }} de {{ lastPage }}</span>
              <button class="btn-ghost" :disabled="currentPage === lastPage" @click="changePage(currentPage + 1)">Próximo</button>
            </div>
          </div>
        </div>
      </main>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <transition name="toast">
      <div v-if="deleteDialog.show" class="modal-overlay" @click.self="deleteDialog.show = false">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title">
              <v-icon size="20" color="#ef4444">mdi-alert-circle</v-icon>
              Confirmar Exclusão
            </h3>
            <button class="modal-close-btn" @click="deleteDialog.show = false">
              <v-icon size="20">mdi-close</v-icon>
            </button>
          </div>
          <div class="modal-body">
            <p>Tem certeza que deseja remover este produto? Esta ação não pode ser desfeita.</p>
          </div>
          <div class="modal-footer">
            <button class="btn-ghost" @click="deleteDialog.show = false">Cancelar</button>
            <button class="btn-primary" style="background: #ef4444; box-shadow: 0 4px 14px rgba(239,68,68,0.3);" @click="executeDeleteProduct" :disabled="loadingActions">Remover</button>
          </div>
        </div>
      </div>
    </transition>

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
import './dashboard.css'

const router = useRouter()
const authStore = useAuthStore()

const drawer = ref(localStorage.getItem('sys_drawer') !== 'false')
watch(drawer, (val) => localStorage.setItem('sys_drawer', String(val)))
const products = ref([])
const allProducts = ref([])
const loading = ref(false)
const submitting = ref(false)
const editingId = ref(null)
const search = ref('')

const currentPage = ref(1)
const lastPage = ref(1)

const changePage = (page) => {
  if (page < 1 || page > lastPage.value) return
  currentPage.value = page
  fetchProducts()
}

const productForm = reactive({ nome: '', preco: null, descricao: '' })

const formattedPreco = computed(() => {
  if (productForm.preco === null || productForm.preco === '') return ''
  return new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(productForm.preco)
})

const updatePreco = (event) => {
  let val = event.target.value.replace(/\D/g, '')
  if (!val) {
    productForm.preco = null
    event.target.value = ''
    return
  }
  productForm.preco = Number(val) / 100
}

const toast = reactive({ show: false, text: '', type: 'success' })
const deleteDialog = reactive({ show: false, id: null })
const loadingActions = ref(false)

const userInitials = computed(() => {
  const nome = authStore.user?.nome || ''
  return nome.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2) || 'U'
})

const firstName = computed(() => {
  return authStore.user?.nome?.split(' ')[0] || 'Usuário'
})

const filteredProducts = computed(() => {
  if (!search.value) return products.value
  const q = search.value.toLowerCase()
  return products.value.filter(p =>
    p.nome?.toLowerCase().includes(q) ||
    p.descricao?.toLowerCase().includes(q)
  )
})

const totalValue = computed(() => allProducts.value.reduce((sum, p) => sum + Number(p.preco || 0), 0))
const avgPrice = computed(() => allProducts.value.length ? totalValue.value / allProducts.value.length : 0)

const fetchProducts = async () => {
  loading.value = true
  try {
    const userId = authStore.user?.id || 0
    
    const [pageRes, allRes] = await Promise.all([
      api.get(`produtos/usuario/${userId}`, {
        params: { page: currentPage.value, limit: 15 }
      }),
      api.get(`produtos/usuario/${userId}`, {
        params: { limit: 999999 }
      })
    ])
    
    const payload = pageRes.data.data || pageRes.data
    products.value = Array.isArray(payload.data) ? payload.data : payload
    
    currentPage.value = payload.current_page || 1
    lastPage.value = payload.last_page || 1

    const allPayload = allRes.data.data || allRes.data
    allProducts.value = Array.isArray(allPayload.data) ? allPayload.data : allPayload
  } catch {
    showToast('Erro ao carregar produtos.', 'error')
  } finally {
    loading.value = false
  }
}

const handleSubmit = async () => {
  if (!productForm.nome || !productForm.preco) return
  submitting.value = true
  try {
    if (editingId.value) {
      await api.put(`produtos/${editingId.value}`, productForm)
      showToast('Produto atualizado com sucesso!')
    } else {
      await api.post('produtos', productForm)
      showToast('Produto cadastrado com sucesso!')
    }
    resetForm()
    fetchProducts()
  } catch {
    showToast('Erro ao salvar produto.', 'error')
  } finally {
    submitting.value = false
  }
}

const editProduct = (product) => {
  editingId.value = product.id
  productForm.nome = product.nome
  productForm.preco = product.preco
  productForm.descricao = product.descricao
}

const confirmDeleteProduct = (id) => {
  deleteDialog.id = id
  deleteDialog.show = true
}

const executeDeleteProduct = async () => {
  const id = deleteDialog.id
  if (!id) return
  
  loadingActions.value = true
  try {
    await api.delete(`produtos/${id}`)
    showToast('Produto removido.')
    deleteDialog.show = false
    fetchProducts()
  } catch {
    showToast('Erro ao remover produto.', 'error')
  } finally {
    loadingActions.value = false
  }
}

const resetForm = () => {
  editingId.value = null
  productForm.nome = ''
  productForm.preco = null
  productForm.descricao = ''
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
  authStore.fetchMe()
  fetchProducts()
})
</script>
