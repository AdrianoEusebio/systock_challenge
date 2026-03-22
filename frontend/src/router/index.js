import { createRouter, createWebHistory } from 'vue-router'
import Login from '@/views/login/Login.vue'
import Register from '@/views/register/Register.vue'
import Dashboard from '@/views/dashboard/Dashboard.vue'
import Users from '@/views/users/Users.vue'
import Reports from '@/views/reports/Reports.vue'
import { useAuthStore } from '@/stores/auth'

const routes = [
  { path: '/', redirect: '/dashboard' },
  { path: '/login',    name: 'Login',    component: Login,    meta: { guest: true } },
  { path: '/register', name: 'Register', component: Register, meta: { guest: true } },
  { path: '/dashboard', name: 'Dashboard', component: Dashboard, meta: { requiresAuth: true } },
  { path: '/users', name: 'Users', component: Users, meta: { requiresAuth: true } },
  { path: '/reports', name: 'Reports', component: Reports, meta: { requiresAuth: true, requiresAdmin: true } },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/login')
  } else if (to.meta.guest && authStore.isAuthenticated) {
    next('/dashboard')
  } else if (to.meta.requiresAdmin && authStore.user?.tipo_usuario !== 'Administrador') {
    next('/dashboard')
  } else {
    next()
  }
})

export default router
