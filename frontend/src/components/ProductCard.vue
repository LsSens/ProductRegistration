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

<style scoped>
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
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.product-info .desc {
  color: #333;
  font-size: 0.98rem;
  margin-bottom: 1rem;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
  min-height: 2.8em;
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

@media (max-width: 700px) {
  .product-card {
    max-width: 100%;
    min-width: 0;
  }
}
</style> 