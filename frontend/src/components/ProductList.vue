<template>
  <div class="product-list-container">
    <div class="header-bar">
      <h2>Produtos Cadastrados</h2>
      <router-link to="/novo" class="btn-add">Novo Produto</router-link>
    </div>
    <div v-if="loading" class="loading">Carregando...</div>
    <div v-else>
      <div v-if="products.length === 0" class="empty">Nenhum produto cadastrado.</div>
      <div class="product-list">
        <div v-for="product in products" :key="product.id" class="product-card">
          <div class="product-info">
            <h3>{{ product.name }}</h3>
            <p class="desc">{{ product.description }}</p>
            <div class="details">
              <span>Preço: <b>R$ {{ product.price.toFixed(2) }}</b></span>
              <span>Qtd: <b>{{ product.quantity }}</b></span>
            </div>
          </div>
          <div class="actions">
            <router-link :to="`/produto/${product.id}`" class="btn-view">Visualizar</router-link>
            <router-link :to="`/editar/${product.id}`" class="btn-edit">Editar</router-link>
            <button @click="deleteProduct(product.id)" class="btn-delete">Excluir</button>
          </div>
        </div>
      </div>
    </div>
    <div v-if="success" class="success-message">Produto excluído com sucesso!</div>
    <div v-if="error" class="error-message">Erro ao excluir produto.</div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const products = ref([])
const loading = ref(true)
const success = ref(false)
const error = ref(false)
const router = useRouter()

const fetchProducts = async () => {
  loading.value = true
  try {
    const res = await axios.get('http://localhost:8000/api/products')
    products.value = res.data.data
  } catch (e) {
    products.value = []
  }
  loading.value = false
}

const deleteProduct = async (id) => {
  if (!confirm('Tem certeza que deseja excluir este produto?')) return
  try {
    await axios.delete(`http://localhost:8000/api/products/${id}`)
    products.value = products.value.filter(p => p.id !== id)
    success.value = true
    setTimeout(() => success.value = false, 2000)
  } catch (e) {
    error.value = true
    setTimeout(() => error.value = false, 2000)
  }
}

onMounted(fetchProducts)
</script>

<style scoped>
.product-list-container {
  max-width: 900px;
  margin: 0 auto;
  padding: 2rem 1rem;
}
.header-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}
.header-bar h2 {
  color: #000;
}
.btn-add {
  background: #FFD500;
  color: #000;
  padding: 0.7rem 1.5rem;
  border-radius: 4px;
  font-weight: bold;
  text-decoration: none;
  transition: background 0.2s;
}
.btn-add:hover {
  background: #e6c000;
}
.product-list {
  display: flex;
  flex-wrap: wrap;
  gap: 1.5rem;
}
.product-card {
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  padding: 1.5rem;
  flex: 1 1 260px;
  min-width: 260px;
  max-width: 320px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}
.product-info h3 {
  color: #FFD500;
  margin-bottom: 0.5rem;
}
.product-info .desc {
  color: #333;
  font-size: 0.98rem;
  margin-bottom: 1rem;
}
.details {
  display: flex;
  justify-content: space-between;
  color: #000;
  font-size: 1rem;
}
.actions {
  display: flex;
  gap: 0.5rem;
  margin-top: 1rem;
}
.btn-view, .btn-edit, .btn-delete {
  padding: 0.5rem 1rem;
  border-radius: 4px;
  font-weight: 500;
  border: none;
  cursor: pointer;
  text-decoration: none;
  transition: background 0.2s;
}
.btn-view {
  background: #FFD500;
  color: #000;
}
.btn-edit {
  background: #000;
  color: #FFD500;
}
.btn-delete {
  background: #dc3545;
  color: #fff;
}
.btn-view:hover {
  background: #e6c000;
}
.btn-edit:hover {
  background: #222;
}
.btn-delete:hover {
  background: #b52a37;
}
.loading, .empty {
  text-align: center;
  color: #000;
  margin-top: 2rem;
}
.success-message {
  margin-top: 1rem;
  background: #28a745;
  color: #fff;
  padding: 0.7rem;
  border-radius: 4px;
  text-align: center;
}
.error-message {
  margin-top: 1rem;
  background: #dc3545;
  color: #fff;
  padding: 0.7rem;
  border-radius: 4px;
  text-align: center;
}
@media (max-width: 700px) {
  .product-list {
    flex-direction: column;
    gap: 1rem;
  }
  .product-card {
    max-width: 100%;
    min-width: 0;
  }
  .header-bar {
    flex-direction: column;
    gap: 1rem;
  }
}
</style> 