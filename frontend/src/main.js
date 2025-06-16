import './assets/main.css'

import { createApp } from 'vue'
import App from './App.vue'
import { createRouter, createWebHistory } from 'vue-router'
import ProductList from './components/ProductList.vue'
import Toast from 'vue-toastification'
import 'vue-toastification/dist/index.css'

const routes = [
  { path: '/', component: ProductList }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

const app = createApp(App)
app.use(router)
app.use(Toast, {
  transition: "Vue-Toastification__bounce",
  maxToasts: 3,
  newestOnTop: true
})
app.mount('#app')
