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
import ProductSkeleton from './ProductSkeleton.vue'
import ModalSkeleton from './ModalSkeleton.vue'
import ProductCard from './ProductCard.vue'
import PriceInput from './PriceInput.vue'
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

<style scoped>
.container {
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
  border: none;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-add:hover {
  background: #e6c000;
}

.product-list {
  display: flex;
  flex-wrap: wrap;
  gap: 1.5rem;
  justify-content: center;
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

.loading, .empty-state {
  text-align: center;
  color: #000;
  margin-top: 2rem;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 8px;
  width: 90%;
  max-width: 500px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.modal-header {
  padding: 1rem;
  border-bottom: 1px solid #eee;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header h2 {
  margin: 0;
  font-size: 1.5rem;
  color: #333;
}

.close-button {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: #666;
  padding: 0;
  line-height: 1;
}

.modal-body {
  padding: 1rem;
}

.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  color: #333;
  font-weight: 500;
}

.form-control {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
  transition: all 0.2s ease;
}

.form-control:focus {
  outline: none;
  border-color: #4a90e2;
  box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.2);
}

.form-control.error {
  border-color: #dc3545;
  background-color: #fff8f8;
}

.form-control.error:focus {
  box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.2);
}

.error-message {
  color: #dc3545;
  font-size: 0.875rem;
  margin-top: 0.25rem;
  display: block;
}

.textarea-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 0.25rem;
}

.char-count {
  font-size: 0.875rem;
  color: #666;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1.5rem;
}

.btn-cancel {
  padding: 0.75rem 1.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  background: white;
  color: #666;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-cancel:hover {
  background: #f5f5f5;
}

.btn-submit {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 4px;
  background: #4a90e2;
  color: white;
  font-size: 1rem;
  cursor: pointer;
  transition: background-color 0.2s;
}

.btn-submit:hover:not(:disabled) {
  background: #357abd;
}

.btn-submit:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.product-details {
  padding: 1rem 0;
}

.detail-group {
  margin-bottom: 1rem;
}

.detail-group label {
  font-weight: 500;
  color: #666;
  margin-bottom: 0.25rem;
  display: block;
}

.detail-group p {
  margin: 0;
  color: #333;
  font-size: 1.1rem;
}

.loading {
  padding: 2rem;
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
  .modal-content {
    width: 95%;
    margin: 1rem;
  }
}
</style> 