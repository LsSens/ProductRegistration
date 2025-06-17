import './assets/main.css'

import { createApp } from 'vue'
import App from './App.vue'

import Toast from 'vue-toastification'
import 'vue-toastification/dist/index.css'
import money3 from 'v-money3'

const app = createApp(App)

app.use(money3)
app.use(Toast, {
  transition: "Vue-Toastification__bounce",
  maxToasts: 3,
  newestOnTop: true
})
app.mount('#app')
