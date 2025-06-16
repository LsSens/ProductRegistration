<template>
  <div class="product-card">
    <div class="product-info">
      <h3 :title="product.name">{{ formatText(product.name, 20) }}</h3>
      <p class="desc" :title="product.description">{{ formatText(product.description, 60) }}</p>
      <div class="details">
        <span>Preço: <b>{{ formatPrice(product.price) }}</b></span>
        <span>Qtd: <b>{{ product.quantity }}</b></span>
      </div>
    </div>
    <div class="actions">
      <button @click="$emit('view', product)" class="btn-view">Visualizar</button>
      <button @click="$emit('edit', product)" class="btn-edit">Editar</button>
      <button @click="$emit('delete', product)" class="btn-delete">Excluir</button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  product: {
    type: Object,
    required: true
  }
})

defineEmits(['view', 'edit', 'delete'])

const formatText = (text, maxLength) => {
  if (!text) return ''
  return text.length > maxLength ? text.substring(0, maxLength) + '...' : text
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  }).format(price)
}
</script>

<style src="./styles.css" scoped></style>