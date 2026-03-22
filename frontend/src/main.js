import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import vuetify from './plugins/vuetify'
import router from './router'
import axios from 'axios'
import api from './services/api'

const app = createApp(App)
const pinia = createPinia()

app.config.globalProperties.$axios = api

app.use(pinia)
app.use(router)
app.use(vuetify)

app.mount('#app')
