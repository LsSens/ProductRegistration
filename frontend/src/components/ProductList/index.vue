<template>
  <div class="container">
    <div class="header-bar">
      <h2>Produtos Cadastrados</h2>
      <button @click="openCreateModal" class="btn-add">
        <i class="fas fa-plus"></i> Novo Produto
      </button>
    </div>

    <div v-if="loading" class="product-list">
      <ProductSkeleton v-for="n in 6" :key="n" />
    </div>
    <div v-else-if="products.length === 0" class="empty-state">
      <p>Nenhum produto cadastrado</p>
      <button @click="openCreateModal" class="btn-create">Cadastrar Produto</button>
    </div>
    <div v-else class="product-list">
      <ProductCard
        v-for="product in products"
        :key="product.id"
        :product="product"
        @view="viewProduct"
        @edit="editProduct"
        @delete="confirmDelete"
      />
    </div>

    <div v-if="showCreateModal || showEditModal" class="modal-overlay">
      <div class="modal-content">
        <div class="modal-header">
          <h2>{{ showEditModal ? 'Editar Produto' : 'Novo Produto' }}</h2>
          <button @click="closeModal" class="close-button">&times;</button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="handleSubmit" class="form">
            <div class="form-group">
              <label for="name">Nome do Produto *</label>
              <input
                type="text"
                id="name"
                v-model="formValues.name"
                :class="['form-control', { 'error': errors.name && touched.name }]"
                @change="e => handleChange('name', e.target.value)"
                @blur="() => handleBlur('name')"
              />
              <span v-if="errors.name && touched.name" class="error-message">{{ errors.name }}</span>
            </div>

            <div class="form-group">
              <label for="description">Descrição</label>
              <textarea
                id="description"
                v-model="formValues.description"
                rows="3"
                maxlength="500"
                resize="none"
                :class="['form-control', { 'error': errors.description && touched.description }]"
                @change="e => handleChange('description', e.target.value)"
                @blur="() => handleBlur('description')"
              ></textarea>
              <div class="textarea-footer">
                <span v-if="errors.description && touched.description" class="error-message">{{ errors.description }}</span>
                <span class="char-count">{{ (formValues.description || '').length }}/500</span>
              </div>
            </div>

            <div class="form-group">
              <label for="price">Preço *</label>
              <PriceInput
                v-model="formValues.price"
                :error="errors.price && touched.price"
                @update:modelValue="value => handleChange('price', value)"
                @blur="() => handleBlur('price')"
              />
              <span v-if="errors.price && touched.price" class="error-message">{{ errors.price }}</span>
            </div>

            <div class="form-group">
              <label for="quantity">Quantidade *</label>
              <input
                type="number"
                id="quantity"
                v-model="formValues.quantity"
                min="0"
                :class="['form-control', { 'error': errors.quantity && touched.quantity }]"
                @change="e => handleChange('quantity', e.target.value)"
                @blur="() => handleBlur('quantity')"
              />
              <span v-if="errors.quantity && touched.quantity" class="error-message">{{ errors.quantity }}</span>
            </div>

            <div class="modal-actions">
              <button type="button" @click="closeModal" class="btn-cancel">Cancelar</button>
              <button type="submit" :disabled="isSubmitting" class="btn-submit">
                {{ isSubmitting ? 'Salvando...' : (showEditModal ? 'Atualizar' : 'Cadastrar') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div v-if="showViewModal" class="modal-overlay">
      <div class="modal-content">
        <div class="modal-header">
          <h2>Detalhes do Produto</h2>
          <button @click="closeModal" class="close-button">&times;</button>
        </div>
        <div class="modal-body">
          <div v-if="loading" class="loading">
            <ModalSkeleton />
          </div>
          <div v-else class="product-details">
            <div class="detail-group">
              <label>Nome:</label>
              <p>{{ selectedProduct.name }}</p>
            </div>
            <div class="detail-group">
              <label>Descrição:</label>
              <p>{{ selectedProduct.description || 'Sem descrição' }}</p>
            </div>
            <div class="detail-group">
              <label>Preço:</label>
              <p>{{ formatPrice(selectedProduct.price) }}</p>
            </div>
            <div class="detail-group">
              <label>Quantidade:</label>
              <p>{{ selectedProduct.quantity }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="showDeleteModal" class="modal-overlay">
      <div class="modal-content">
        <div class="modal-header">
          <h2>Confirmar Exclusão</h2>
          <button @click="closeModal" class="close-button">&times;</button>
        </div>
        <div class="modal-body">
          <div v-if="loading" class="loading">
            <ModalSkeleton />
          </div>
          <div v-else>
            <p>Tem certeza que deseja excluir o produto "{{ productToDelete.name }}"?</p>
            <div class="modal-actions">
              <button @click="closeModal" class="btn-cancel">Cancelar</button>
              <button @click="deleteProduct" :disabled="loading" class="btn-delete">
                {{ loading ? 'Excluindo...' : 'Excluir' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useToast } from 'vue-toastification'
import ProductSkeleton from '../ProductSkeleton/index.vue'
import ModalSkeleton from '../ModalSkeleton/index.vue'
import ProductCard from '../ProductCard/index.vue'
import PriceInput from '../PriceInput/index.vue'
import * as Yup from 'yup'

const toast = useToast()

const products = ref([])
const loading = ref(true)
const showCreateModal = ref(false)
const showEditModal = ref(false)
const showViewModal = ref(false)
const showDeleteModal = ref(false)
const selectedProduct = ref(null)
const productToDelete = ref(null)
const formValues = ref({
  name: '',
  description: '',
  price: '',
  quantity: ''
})
const errors = ref({})
const touched = ref({})
const isSubmitting = ref(false)

const validationSchema = Yup.object().shape({
  name: Yup.string()
    .required('O nome do produto é obrigatório')
    .max(255, 'O nome não pode ter mais de 255 caracteres'),
  description: Yup.string()
    .max(500, 'A descrição não pode ter mais de 500 caracteres'),
  price: Yup.number()
    .typeError('O preço deve ser um número válido')
    .required('O preço é obrigatório')
    .min(0, 'O preço deve ser maior ou igual a zero'),
  quantity: Yup.number()
    .typeError('A quantidade deve ser um número inteiro')
    .required('A quantidade é obrigatória')
    .min(0, 'A quantidade não pode ser negativa')
    .integer('A quantidade deve ser um número inteiro')
})

const validateField = async (field, value) => {
  try {
    await validationSchema.validateAt(field, { [field]: value })
    errors.value[field] = ''
  } catch (err) {
    errors.value[field] = err.message
  }
}

const handleChange = (field, value) => {
  formValues.value[field] = value
  validateField(field, value)
}

const handleBlur = (field) => {
  touched.value[field] = true
  validateField(field, formValues.value[field])
}

const handleSubmit = async () => {
  isSubmitting.value = true
  try {
    Object.keys(formValues.value).forEach(field => {
      touched.value[field] = true
    })

    await validationSchema.validate(formValues.value, { abortEarly: false })
    
    const formData = {
      name: formValues.value.name.trim(),
      description: formValues.value.description.trim(),
      price: parseFloat(formValues.value.price),
      quantity: parseInt(formValues.value.quantity)
    }

    if (showEditModal.value) {
      await axios.put(`http://localhost:8000/api/products/${selectedProduct.value.id}`, formData)
      toast.success("Produto atualizado com sucesso!")
    } else {
      await axios.post('http://localhost:8000/api/products', formData)
      toast.success("Produto cadastrado com sucesso!")
    }
    closeModal()
    fetchProducts()
  } catch (error) {
    if (error.inner) {
      const newErrors = {}
      error.inner.forEach(err => {
        newErrors[err.path] = err.message
      })
      errors.value = newErrors
    } else if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
      toast.error("Por favor, corrija os erros no formulário")
    } else {
      toast.error("Erro ao salvar produto")
    }
  } finally {
    isSubmitting.value = false
  }
}

const viewProduct = (product) => {
  selectedProduct.value = product
  showViewModal.value = true
}

const editProduct = (product) => {
  selectedProduct.value = product
  formValues.value = {
    ...product,
    description: product.description || ''
  }
  showEditModal.value = true
}

const confirmDelete = (product) => {
  productToDelete.value = product
  showDeleteModal.value = true
}

const deleteProduct = async () => {
  try {
    await axios.delete(`http://localhost:8000/api/products/${productToDelete.value.id}`)
    products.value = products.value.filter(p => p.id !== productToDelete.value.id)
    showDeleteModal.value = false
    toast.success("Produto excluído com sucesso!")
  } catch (e) {
    toast.error("Erro ao excluir produto")
  }
}

const closeModal = () => {
  showCreateModal.value = false
  showEditModal.value = false
  showViewModal.value = false
  showDeleteModal.value = false
  selectedProduct.value = null
  productToDelete.value = null
  formValues.value = {
    name: '',
    description: '',
    price: '',
    quantity: ''
  }
  errors.value = {}
  touched.value = {}
}

const fetchProducts = async () => {
  loading.value = true
  try {
    const res = await axios.get('http://localhost:8000/api/products')
    products.value = res.data.data
  } catch (e) {
    toast.error("Erro ao carregar produtos")
  }
  loading.value = false
}

const openCreateModal = () => {
  formValues.value = {
    name: '',
    description: '',
    price: '',
    quantity: ''
  }
  errors.value = {}
  touched.value = {}
  showCreateModal.value = true
}

function formatPrice(value) {
  if (value == null || value === '') return 'R$ 0,00';
  return Number(value).toLocaleString('pt-BR', {
    style: 'currency',
    currency: 'BRL',
    minimumFractionDigits: 2
  });
}

onMounted(fetchProducts)
</script>

<style src="./styles.css" scoped></style>