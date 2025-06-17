<template>
  <div class="container">
    <div class="header-bar">
      <h2>Produtos Cadastrados</h2>
      <button @click="openCreateModal" class="btn-add">
        <i class="fas fa-plus"></i> Novo Produto
      </button>
    </div>

    <div v-if="initialLoading" class="product-list">
      <ProductSkeleton v-for="n in 3" :key="n" />
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
        @edit="editProduct"
        @delete="confirmDelete"
      />
      <CircularProgress v-if="loadingMore" />
    </div>

    <div v-if="showCreateModal || showEditModal" class="modal-overlay">
      <div class="modal-content">
        <div class="modal-header">
          <h2>{{ showEditModal ? 'Editar Produto' : 'Novo Produto' }}</h2>
          <button @click="closeModal" class="close-button">&times;</button>
        </div>
        <div class="modal-body">
          <div v-if="loading" class="loading">
            <ModalSkeleton />
          </div>
          <form v-else @submit.prevent="handleSubmit" class="form">
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
                {{ loading ? 'Excluindo...' : 'Confirmar' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useToast } from 'vue-toastification'
import ProductSkeleton from '../ProductSkeleton/index.vue'
import ModalSkeleton from '../ModalSkeleton/index.vue'
import ProductCard from '../ProductCard/index.vue'
import PriceInput from '../PriceInput/index.vue'
import CircularProgress from '../CircularProgress/index.vue'
import { productService } from '@/services/productService'
import * as Yup from 'yup'

const toast = useToast()

const products = ref([])
const loading = ref(false)
const initialLoading = ref(true)
const loadingMore = ref(false)
const currentPage = ref(1)
const hasMorePages = ref(true)
const perPage = 10
const showCreateModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)
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

const handleScroll = async () => {
  if (loading.value || !hasMorePages.value) return

  const scrollPosition = window.innerHeight + window.scrollY
  const documentHeight = document.documentElement.scrollHeight

  if (scrollPosition >= documentHeight - 100) {
    await loadMoreProducts()
  }
}

const loadMoreProducts = async () => {
  if (loadingMore.value || !hasMorePages.value) return

  loadingMore.value = true
  try {
    const response = await productService.getProducts(currentPage.value, perPage)
    const newProducts = response.data

    if (newProducts.length === 0) {
      hasMorePages.value = false
      return
    }

    products.value = [...products.value, ...newProducts]
    currentPage.value++
  } catch (e) {
    toast.error("Erro ao carregar mais produtos")
  } finally {
    loadingMore.value = false
  }
}

const fetchProducts = async () => {
  initialLoading.value = true
  currentPage.value = 1
  hasMorePages.value = true
  try {
    const response = await productService.getProducts(currentPage.value, perPage)
    products.value = response.data
    currentPage.value++
  } catch (e) {
    toast.error("Erro ao carregar produtos")
  } finally {
    initialLoading.value = false
  }
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
      await productService.updateProduct(productToDelete.value.id, formData)
      toast.success("Produto atualizado com sucesso!")
    } else {
      await productService.createProduct(formData)
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

const editProduct = (product) => {
  productToDelete.value = product
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
    await productService.deleteProduct(productToDelete.value.id)
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
  showDeleteModal.value = false
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

onMounted(() => {
  fetchProducts()
  window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<style src="./styles.css" scoped></style>