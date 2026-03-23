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

    <!-- Main content -->
    <div class="main-area" @click="drawer = false">

      <!-- Topbar -->
      <header class="topbar">
        <button class="toggle-btn" @click.stop="drawer = !drawer">
          <v-icon size="22">{{ drawer ? 'mdi-menu-open' : 'mdi-menu' }}</v-icon>
        </button>
        <div class="topbar-title">
          <h2 class="page-title">Usuários</h2>
          <span class="page-breadcrumb">Dashboard / Usuários</span>
        </div>
        <div class="topbar-actions">
          <div class="search-box">
            <v-icon size="18" class="search-icon">mdi-magnify</v-icon>
            <input v-model="search" placeholder="Buscar usuário..." class="search-input" />
          </div>
          <div class="topbar-avatar">{{ userInitials }}</div>
        </div>
      </header>

      <!-- Content -->
      <main class="content">

        <!-- Bottom row: form + table -->
        <div class="bottom-grid">

          <!-- User form (Admins only) -->
          <div class="form-card" v-if="isAdmin">
            <div class="card-header">
              <div class="card-header-icon">
                <v-icon size="16" color="white">{{ editingId ? 'mdi-pencil' : 'mdi-plus' }}</v-icon>
              </div>
              <h3 class="card-title">{{ editingId ? 'Editar Usuário' : 'Novo Usuário' }}</h3>
            </div>

            <form @submit.prevent="handleSubmit" class="product-form">
              <div class="form-row">
                <div class="field-group">
                  <label class="field-label">Nome do Usuário</label>
                  <div class="field-wrapper">
                    <input v-model="form.nome" placeholder="Ex: João da Silva" class="form-input" required />
                  </div>
                </div>

                <div class="field-group">
                  <label class="field-label">Email</label>
                  <div class="field-wrapper">
                    <input type="email" v-model="form.email" placeholder="usuario@exemplo.com" class="form-input" required />
                  </div>
                </div>
                
                <div class="field-group">
                  <label class="field-label">CPF</label>
                  <div class="field-wrapper">
                    <input v-model="form.cpf" @input="formatCpfInput" placeholder="000.111.222-33" class="form-input" required maxlength="14" />
                  </div>
                </div>

                <div class="field-group">
                  <label class="field-label">Tipo de Usuário</label>
                  <div class="field-wrapper">
                    <select v-model="form.tipo_usuario" class="form-input" required>
                      <option value="1">Administrador</option>
                      <option value="2">Cliente</option>
                    </select>
                  </div>
                </div>

                <div class="field-group">
                  <label class="field-label">{{ editingId ? 'Nova Senha (opcional)' : 'Senha' }}</label>
                  <div class="field-wrapper">
                    <input type="password" v-model="form.senha" placeholder="******" class="form-input" :required="!editingId" minlength="6" />
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

          <!-- Users table -->
          <div class="table-card">
            <div class="card-header">
              <div class="card-header-icon">
                <v-icon size="16" color="white">mdi-account-group</v-icon>
              </div>
              <h3 class="card-title">Lista de Usuários</h3>
              <button class="refresh-btn" @click="fetchUsers" :disabled="loading">
                <v-icon size="16" :class="{ 'rotating': loading }">mdi-refresh</v-icon>
              </button>
            </div>

            <!-- Empty state -->
            <div v-if="!loading && filteredUsers.length === 0" class="empty-state">
              <p class="empty-title">{{ search ? 'Nenhum usuário encontrado' : 'Nenhum usuário cadastrado.' }}</p>
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
            <div v-else class="table-wrapper">
              <table class="product-table">
                <thead>
                  <tr>
                    <th>Nome</th>
                    <th v-if="isAdmin">E-mail</th>
                    <th v-if="isAdmin">CPF</th>
                    <th v-if="isAdmin">Perfil</th>

                    <th v-if="isAdmin">Ações</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="userItem in filteredUsers"
                    :key="userItem.id"
                    class="table-row"
                    :class="{ 'table-row--editing': editingId === userItem.id }"
                  >
                    <td>
                      <div class="product-name-cell">
                        <div class="product-avatar">{{ userItem.nome[0]?.toUpperCase() }}</div>
                        <span class="product-name">{{ userItem.nome }}</span>
                      </div>
                    </td>
                    <td v-if="isAdmin" class="product-desc">{{ userItem.email }}</td>
                    <td v-if="isAdmin">
                      <span class="price-badge">{{ formatCPF(userItem.cpf) }}</span>
                    </td>
                    <td v-if="isAdmin">{{ userItem.tipo_usuario }}</td>
                    <td v-if="isAdmin">
                      <div class="action-btns">
                        <button class="action-btn action-btn--edit" @click="editUser(userItem)" title="Editar">
                          <v-icon size="15">mdi-pencil-outline</v-icon>
                        </button>
                        <button class="action-btn action-btn--delete" @click="confirmDeleteUser(userItem.id)" title="Remover">
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
            <p>Tem certeza que deseja remover este usuário? Esta ação não pode ser desfeita.</p>
          </div>
          <div class="modal-footer">
            <button class="btn-ghost" @click="deleteDialog.show = false">Cancelar</button>
            <button class="btn-primary" style="background: #ef4444; box-shadow: 0 4px 14px rgba(239,68,68,0.3);" @click="executeDeleteUser" :disabled="loadingActions">Remover</button>
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
import '../dashboard/dashboard.css'

const router = useRouter()
const authStore = useAuthStore()

const drawer = ref(localStorage.getItem('sys_drawer') !== 'false')
watch(drawer, (val) => localStorage.setItem('sys_drawer', String(val)))
const users = ref([])
const loading = ref(false)
const submitting = ref(false)
const editingId = ref(null)
const search = ref('')

const currentPage = ref(1)
const lastPage = ref(1)

const isAdmin = computed(() => authStore.user?.tipo_usuario === 'Administrador')

const form = reactive({ nome: '', email: '', cpf: '', senha: '', tipo_usuario: '2' })

const formatCPF = (cpf) => {
  if (!cpf) return ''
  const val = cpf.replace(/\D/g, '')
  if (val.length === 11) {
    return val.replace(/^(\d{3})(\d{3})(\d{3})(\d{2})$/, '$1.$2.$3-$4')
  }
  return cpf
}

const formatCpfInput = () => {
  let val = form.cpf.replace(/\D/g, '')
  if (val.length > 11) val = val.slice(0, 11)
  
  if (val.length > 9) {
    val = val.replace(/^(\d{3})(\d{3})(\d{3})(\d{1,2}).*/, '$1.$2.$3-$4')
  } else if (val.length > 6) {
    val = val.replace(/^(\d{3})(\d{3})(\d{1,3}).*/, '$1.$2.$3')
  } else if (val.length > 3) {
    val = val.replace(/^(\d{3})(\d{1,3}).*/, '$1.$2')
  }
  form.cpf = val
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

const filteredUsers = computed(() => {
  if (!search.value) return users.value
  const q = search.value.toLowerCase()
  return users.value.filter(u =>
    u.nome?.toLowerCase().includes(q) ||
    u.email?.toLowerCase().includes(q) ||
    u.cpf?.includes(q) ||
    formatCPF(u.cpf).includes(q)
  )
})

const changePage = (page) => {
  if (page < 1 || page > lastPage.value) return
  currentPage.value = page
  fetchUsers()
}

const fetchUsers = async () => {
  loading.value = true
  try {
    const response = await api.get('usuarios', {
      params: { page: currentPage.value, limit: 15 }
    })
    const payload = response.data.data || response.data
    users.value = Array.isArray(payload.data) ? payload.data : payload
    
    // Pagination data
    currentPage.value = payload.current_page || 1
    lastPage.value = payload.last_page || 1
  } catch {
    showToast('Erro ao carregar usuários.', 'error')
  } finally {
    loading.value = false
  }
}

const handleSubmit = async () => {
  if (!form.nome || !form.email || !form.cpf) return
  submitting.value = true
  try {
    const payload = { ...form }
    if (!payload.senha) delete payload.senha
    payload.tipo_usuario = parseInt(payload.tipo_usuario)
    
    // Format CPF (remove non digits)
    payload.cpf = payload.cpf.replace(/\D/g, '')

    if (editingId.value) {
      await api.put(`usuarios/${editingId.value}`, payload)
      showToast('Usuário atualizado com sucesso!')
    } else {
      await api.post('usuarios', payload)
      showToast('Usuário cadastrado com sucesso!')
    }
    resetForm()
    fetchUsers()
  } catch (err) {
    if (err.response?.status === 422) {
      showToast('Erro de validação: Verifique os dados (Email ou CPF já em uso).', 'error')
    } else {
      showToast('Erro ao salvar usuário.', 'error')
    }
  } finally {
    submitting.value = false
  }
}

const editUser = (userItem) => {
  editingId.value = userItem.id
  form.nome = userItem.nome
  form.email = userItem.email
  form.cpf = formatCPF(userItem.cpf)
  form.tipo_usuario = userItem.tipo_usuario === 'Administrador' ? '1' : '2'
  form.senha = ''
}

const confirmDeleteUser = (id) => {
  if (id === authStore.user?.id) {
    showToast('Erro: Você não pode remover sua própria conta logada.', 'error')
    return;
  }
  deleteDialog.id = id
  deleteDialog.show = true
}

const executeDeleteUser = async () => {
  const id = deleteDialog.id
  if (!id) return
  
  loadingActions.value = true
  try {
    await api.delete(`usuarios/${id}`)
    showToast('Usuário removido.')
    deleteDialog.show = false
    fetchUsers()
  } catch {
    showToast('Erro ao remover usuário.', 'error')
  } finally {
    loadingActions.value = false
  }
}

const resetForm = () => {
  editingId.value = null
  form.nome = ''
  form.email = ''
  form.cpf = ''
  form.senha = ''
  form.tipo_usuario = '2'
}

const handleLogout = () => {
  authStore.logout()
  router.push('/login')
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
  fetchUsers()
})
</script>
