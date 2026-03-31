import instance, { csrfClient } from '@/plugins/axios'
import type { AxiosRequestConfig } from 'axios'

const api = {
  csrf() {
    return csrfClient.get('/csrf-cookie')
  },

  get<T>(url: string, config?: AxiosRequestConfig) {
    return instance.get<T>(url, config).then(r => r.data)
  },

  post<T>(url: string, data?: unknown, config?: AxiosRequestConfig) {
    return instance.post<T>(url, data, config).then(r => r.data)
  },

  put<T>(url: string, data?: unknown, config?: AxiosRequestConfig) {
    return instance.put<T>(url, data, config).then(r => r.data)
  },

  patch<T>(url: string, data?: unknown, config?: AxiosRequestConfig) {
    return instance.patch<T>(url, data, config).then(r => r.data)
  },

  delete<T>(url: string, config?: AxiosRequestConfig) {
    return instance.delete<T>(url, config).then(r => r.data)
  },
}

export default api