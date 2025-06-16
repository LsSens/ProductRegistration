<template>
  <div class="edit-container">
    <div class="edit-card">
      <h2>Editar Produto</h2>
      <ProductForm :editId="id" />
      <router-link to="/" class="btn-back">Voltar</router-link>
    </div>
  </div>
</template>

<script setup>
import { useRoute } from 'vue-router'
import ProductForm from './ProductForm.vue'

const route = useRoute()
const id = route.params.id
</script>

<style scoped>
.edit-container {
  max-width: 500px;
  margin: 2rem auto;
  padding: 2rem 1rem;
}
.edit-card {
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  padding: 2rem;
  text-align: center;
}
.btn-back {
  background: #FFD500;
  color: #000;
  padding: 0.7rem 1.5rem;
  border-radius: 4px;
  font-weight: bold;
  text-decoration: none;
  transition: background 0.2s;
  display: inline-block;
  margin-top: 1rem;
}
.btn-back:hover {
  background: #e6c000;
}
</style> 