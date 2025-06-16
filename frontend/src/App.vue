<script setup>
import ProductForm from './components/ProductForm.vue'
</script>

<template>
  <div class="app">
    <header>
      <h1>ConstrUP - Cadastro de Produtos</h1>
    </header>
    <main>
      <ProductForm />
    </main>
  </div>
</template>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: Arial, sans-serif;
  background-color: #f5f5f5;
  color: #000;
}

.app {
  min-height: 100vh;
  padding: 2rem;
}

header {
  text-align: center;
  margin-bottom: 2rem;
}

h1 {
  color: #000;
  font-size: 2rem;
}

main {
  max-width: 1200px;
  margin: 0 auto;
}
</style>
