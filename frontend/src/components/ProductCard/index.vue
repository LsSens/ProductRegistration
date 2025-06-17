<template>
  <div class="product-card">
    <div class="product-header">
      <h3 :title="product.name">{{ formatText(product.name, 20) }}</h3>
      <span class="product-id">#{{ product.id }}</span>
    </div>
    <div class="product-info">
      <p class="desc" :title="product.description">{{ formatText(product.description, 60) }}</p>
      <div class="details">
        <span>Preço: <b>{{ formatPrice(product.price) }}</b></span>
        <span>Qtd: <b>{{ product.quantity }}</b></span>
      </div>
    </div>
    <div class="actions">
      <button @click="$emit('edit', product)" class="btn-edit">
        <i class="fas fa-edit"></i> Editar
      </button>
      <button @click="$emit('delete', product)" class="btn-delete">
        <i class="fas fa-trash"></i> Excluir
      </button>
    </div>
  </div>
</template>

<script setup>
import { formatPrice, formatText } from '@/utils/formatters'

defineProps({
  product: {
    type: Object,
    required: true
  }
})

defineEmits(['edit', 'delete'])
</script>

<style src="./styles.css" scoped></style>