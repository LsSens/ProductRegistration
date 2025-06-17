import axios from 'axios';

const API_URL = 'http://localhost:8000/api';

export const productService = {
  async getProducts(page = 1, perPage = 10) {
    try {
      const response = await axios.get(`${API_URL}/products`, {
        params: {
          page,
          per_page: perPage
        }
      });
      return response.data;
    } catch (error) {
      throw error;
    }
  },

  async createProduct(productData) {
    try {
      const response = await axios.post(`${API_URL}/products`, productData);
      return response.data;
    } catch (error) {
      throw error;
    }
  },

  async updateProduct(id, productData) {
    try {
      const response = await axios.put(`${API_URL}/products/${id}`, productData);
      return response.data;
    } catch (error) {
      throw error;
    }
  },

  async deleteProduct(id) {
    try {
      const response = await axios.delete(`${API_URL}/products/${id}`);
      return response.data;
    } catch (error) {
      throw error;
    }
  }
}; 