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
  
  const isDecimal = value.toString().includes('.')
  const valueInCents = isDecimal ? Math.round(parseFloat(value) * 100) : numericValue
  
  const cents = valueInCents.toString().padStart(3, '0')
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

<style src="./styles.css" scoped></style>