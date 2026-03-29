import api from '../api'
import type { LoginPayload, LoginResponse, User, RegisterPayload, RegisterResponse } from '@/types/shared/auth'

export const authService = {
  login: async (payload: LoginPayload) => {
    await api.csrf()
    return api.post<LoginResponse>('/auth/login', payload)
  },

  logout: () =>
    api.post('/auth/logout'),

  getMe: () =>
    api.get<User>('/auth/profile'),

  register: async (payload: RegisterPayload) => {
    await api.csrf()
    return api.post<RegisterResponse>('/auth/register', payload)
  },
}