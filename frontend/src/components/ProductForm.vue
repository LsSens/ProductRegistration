<template>
  <div class="product-form">
    <h2>Cadastro de Produto</h2>
    <form @submit.prevent="handleSubmit">
      <div class="form-group">
        <label for="name">Nome do Produto *</label>
        <input
          type="text"
          id="name"
          v-model="form.name"
          :class="{ 'error': errors.name }"
          required
        />
        <span class="error-message" v-if="errors.name">{{ errors.name[0] }}</span>
      </div>

      <div class="form-group">
        <label for="description">Descrição</label>
        <textarea
          id="description"
          v-model="form.description"
          rows="3"
        ></textarea>
      </div>

      <div class="form-group">
        <label for="price">Preço *</label>
        <input
          type="number"
          id="price"
          v-model="form.price"
          :class="{ 'error': errors.price }"
          min="0"
          step="0.01"
          required
        />
        <span class="error-message" v-if="errors.price">{{ errors.price[0] }}</span>
      </div>

      <div class="form-group">
        <label for="quantity">Quantidade *</label>
        <input
          type="number"
          id="quantity"
          v-model="form.quantity"
          :class="{ 'error': errors.quantity }"
          min="0"
          required
        />
        <span class="error-message" v-if="errors.quantity">{{ errors.quantity[0] }}</span>
      </div>

      <button type="submit" :disabled="loading">
        {{ loading ? 'Cadastrando...' : 'Cadastrar' }}
      </button>
    </form>

    <div v-if="success" class="success-message">
      Produto cadastrado com sucesso!
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const props = defineProps({
  editId: {
    type: [String, Number],
    default: null
  }
})

const form = reactive({
  name: '',
  description: '',
  price: '',
  quantity: ''
})

const errors = ref({})
const loading = ref(false)
const success = ref(false)
const router = useRouter()

const fetchProduct = async () => {
  if (props.editId) {
    loading.value = true
    try {
      const res = await axios.get(`http://localhost:8000/api/products/${props.editId}`)
      Object.assign(form, res.data.data)
    } catch (e) {}
    loading.value = false
  }
}

const handleSubmit = async () => {
  loading.value = true
  errors.value = {}
  success.value = false

  try {
    if (props.editId) {
      await axios.put(`http://localhost:8000/api/products/${props.editId}`, form)
      success.value = true
      setTimeout(() => router.push('/'), 1200)
    } else {
      await axios.post('http://localhost:8000/api/products', form)
      success.value = true
      form.name = ''
      form.description = ''
      form.price = ''
      form.quantity = ''
    }
  } catch (error) {
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    }
  } finally {
    loading.value = false
  }
}

onMounted(fetchProduct)
watch(() => props.editId, fetchProduct)
</script>

<style scoped>
.product-form {
  max-width: 600px;
  margin: 0 auto;
  padding: 2rem;
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

h2 {
  color: #000;
  margin-bottom: 2rem;
  text-align: center;
}

.form-group {
  margin-bottom: 1.5rem;
}

label {
  display: block;
  margin-bottom: 0.5rem;
  color: #000;
  font-weight: 500;
}

input,
textarea {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
}

input.error,
textarea.error {
  border-color: #dc3545;
}

.error-message {
  color: #dc3545;
  font-size: 0.875rem;
  margin-top: 0.25rem;
}

button {
  width: 100%;
  padding: 0.75rem;
  background-color: #FFD500;
  color: #000;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;
}

button:hover {
  background-color: #e6c000;
}

button:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}

.success-message {
  margin-top: 1rem;
  padding: 1rem;
  background-color: #28a745;
  color: #fff;
  border-radius: 4px;
  text-align: center;
}
</style> 