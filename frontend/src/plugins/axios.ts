import axios, { type AxiosInstance } from 'axios'

const instance: AxiosInstance = axios.create({
  baseURL: `${import.meta.env.VITE_API_URL || 'http://localhost'}/api/v1`,
  timeout: 10000,
  withCredentials: true,
  withXSRFToken: true,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
})

instance.interceptors.request.use(
  (config) => config,    
  (error) => Promise.reject(error)
)

instance.interceptors.response.use(
  (response) => response,
  async (error) => {
    if (error.response?.status === 401) {
      window.location.href = '/login'
    }

    if (error.response?.status === 403) {
      window.location.href = '/'
    }

    if (error.response?.status === 422) {
      return Promise.reject(error.response.data)
    }

    if (error.response?.status >= 500) {
      console.error('Server error:', error.response?.data?.message)
    }

    return Promise.reject(error)
  }
)

export default instance