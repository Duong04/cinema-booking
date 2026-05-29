import axios, { type AxiosInstance } from 'axios'
import router from '@/router'

const baseURL = import.meta.env.VITE_API_URL || 'http://localhost'

export class ApiError extends Error {
  constructor(
    public readonly status: number,
    public readonly errors?: Record<string, string[]>,
    message?: string,
  ) {
    super(message ?? 'Đã có lỗi xảy ra')
    this.name = 'ApiError'
  }
}

const instance: AxiosInstance = axios.create({
  baseURL: `${baseURL}/api/v1`,
  timeout: 10000,
  withCredentials: true,
  withXSRFToken: true,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
})

export const csrfClient = axios.create({
  baseURL: `${baseURL}/api`,
  withCredentials: true,
})

instance.interceptors.response.use(
  (response) => response,
  (error) => {
    if (!axios.isAxiosError(error)) return Promise.reject(error)

    const status = error.response?.status ?? 0

    if (status === 401) router.push({ name: 'login' })
    if (status === 403) router.push({ name: 'home' })
    if (status >= 500) console.error('Server error:', error.response?.data?.message)

    return Promise.reject(
      new ApiError(
        status,
        error.response?.data?.errors,
        error.response?.data?.message,
      )
    )
  }
)

export default instance
