import './assets/main.css'

import { createApp } from 'vue'
import App from './App.vue'
import { createRouter, createWebHistory } from 'vue-router'
import ProductList from './components/ProductList.vue'
import ProductForm from './components/ProductForm.vue'
import ProductView from './components/ProductView.vue'
import ProductEdit from './components/ProductEdit.vue'

const routes = [
  { path: '/', component: ProductList },
  { path: '/novo', component: ProductForm },
  { path: '/produto/:id', component: ProductView, props: true },
  { path: '/editar/:id', component: ProductEdit, props: true },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

const app = createApp(App)
app.use(router)
app.mount('#app')
