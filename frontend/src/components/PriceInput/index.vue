<template>
  <input
    ref="inputRef"
    type="text"
    v-model="formattedValue"
    :class="['form-control', { 'error': isInvalid }]"
    @blur="handleBlur"
  />
</template>

<script>
import { computed, ref, onMounted } from 'vue'
import { useCurrencyInput } from 'vue-currency-input'

const touched = ref(false)

const options = {
  "locale": "pt-BR",
  "currency": "BRL",
  "currencyDisplay": "symbol",
  "hideCurrencySymbolOnFocus": false,
  "hideGroupingSeparatorOnFocus": false,
  "hideNegligibleDecimalDigitsOnFocus": false,
  "autoDecimalDigits": true,
  "useGrouping": true,
  "accountingSign": true,
  "prefix": "R$ ",
  "precision": 2,
}

export default {
  name: 'CurrencyInput',
  props: {
    modelValue: {
      type: [Number, String],
      default: 0
    },
    error: Boolean
  },
  setup(props, { emit }) {
    const inputValue = ref('')
    const { inputRef, formattedValue } = useCurrencyInput({
      ...options,
      modelValue: inputValue,
      onInput: (value) => {
        emit('update:modelValue', value)
        touched.value = true
      }
    })

    // Sincroniza o valor inicial após a montagem do componente
    onMounted(() => {
      try {
        const numValue = typeof props.modelValue === 'string' 
          ? parseFloat(props.modelValue) 
          : props.modelValue
        
        if (!isNaN(numValue)) {
          inputValue.value = numValue.toString()
        }
      } catch (error) {
        console.error('Erro ao inicializar valor:', error)
      }
    })

    const handleBlur = () => {
      try {
        emit('blur')
        touched.value = true
      } catch (error) {
        console.error('Erro ao processar blur:', error)
      }
    }

    const isInvalid = computed(() => {
      try {
        const numValue = typeof props.modelValue === 'string' 
          ? parseFloat(props.modelValue) 
          : props.modelValue
        return (isNaN(numValue) || numValue < 0) && touched.value
      } catch (error) {
        console.error('Erro ao validar valor:', error)
        return false
      }
    })

    return { 
      inputRef, 
      formattedValue,
      handleBlur,
      isInvalid
    }
  }
}
</script>