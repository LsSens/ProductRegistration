<template>
  <div class="view-container">
    <div class="view-card" v-if="product">
      <h2>{{ product.name }}</h2>
      <p class="desc">{{ product.description }}</p>
      <div class="details">
        <span>Preço: <b>R$ {{ product.price?.toFixed(2) }}</b></span>
        <span>Quantidade: <b>{{ product.quantity }}</b></span>
      </div>
      <router-link to="/" class="btn-back">Voltar</router-link>
    </div>
    <div v-else class="loading">Carregando...</div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const product = ref(null)

const fetchProduct = async () => {
  const id = route.params.id
  const res = await axios.get(`http://localhost:8000/api/products/${id}`)
  product.value = res.data.data
}

onMounted(fetchProduct)
</script>

<style scoped>
.view-container {
  max-width: 500px;
  margin: 2rem auto;
  padding: 2rem 1rem;
}
.view-card {
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  padding: 2rem;
  text-align: center;
}
.view-card h2 {
  color: #FFD500;
  margin-bottom: 1rem;
}
.desc {
  color: #333;
  margin-bottom: 1.5rem;
}
.details {
  color: #000;
  font-size: 1.1rem;
  margin-bottom: 2rem;
}
.btn-back {
  background: #FFD500;
  color: #000;
  padding: 0.7rem 1.5rem;
  border-radius: 4px;
  font-weight: bold;
  text-decoration: none;
  transition: background 0.2s;
}
.btn-back:hover {
  background: #e6c000;
}
.loading {
  text-align: center;
  color: #000;
  margin-top: 2rem;
}
</style> 