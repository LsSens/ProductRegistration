<template>
  <div class="price-input">
    <div class="input-wrapper">
      <span class="currency-symbol">R$</span>
      <input
        type="text"
        :value="formattedValue"
        @input="handleInput"
        @blur="handleBlur"
        :class="['form-control', { 'error': error }]"
        placeholder="0,00"
      />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  error: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:modelValue', 'blur'])

const formatCurrency = (value) => {
  if (!value) return ''
  
  const numericValue = value.toString().replace(/\D/g, '')
  
  const cents = numericValue.padStart(3, '0')
  const reais = cents.slice(0, -2)
  const centavos = cents.slice(-2)
  
  const formattedReais = reais.replace(/\B(?=(\d{3})+(?!\d))/g, '.')
  
  return `${formattedReais},${centavos}`
}

const parseCurrency = (value) => {
  if (!value) return ''
  
  const numericValue = value.toString().replace(/\D/g, '')
  
  const number = parseFloat(numericValue) / 100
  
  return number
}

const formattedValue = computed(() => {
  return formatCurrency(props.modelValue)
})

const handleInput = (event) => {
  const value = event.target.value
  const parsedValue = parseCurrency(value)
  emit('update:modelValue', parsedValue)
}

const handleBlur = () => {
  if (props.modelValue) {
    const value = parseFloat(props.modelValue)
    emit('update:modelValue', value.toFixed(2))
  }
  emit('blur')
}
</script>

<style scoped>
.price-input {
  width: 100%;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.currency-symbol {
  position: absolute;
  left: 10px;
  color: #666;
  font-weight: 500;
}

input {
  width: 100%;
  padding: 8px 8px 8px 30px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
  transition: all 0.2s ease;
}

input:focus {
  outline: none;
  border-color: #4a90e2;
  box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.2);
}

input.error {
  border-color: #dc3545;
  background-color: #fff8f8;
}

input.error:focus {
  box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.2);
}

input::placeholder {
  color: #999;
}
</style> 