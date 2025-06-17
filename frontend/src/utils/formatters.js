/**
 * Formata um texto limitando seu tamanho e adicionando reticências
 * @param {string} text - Texto a ser formatado
 * @param {number} maxLength - Tamanho máximo do texto
 * @returns {string} Texto formatado
 */
export const formatText = (text, maxLength) => {
  if (!text) return ''
  return text.length > maxLength ? text.substring(0, maxLength) + '...' : text
}

/**
 * Formata um valor numérico para o formato de moeda brasileira
 * @param {number} price - Valor a ser formatado
 * @returns {string} Valor formatado em reais
 */
export const formatPrice = (price) => {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  }).format(price)
} 