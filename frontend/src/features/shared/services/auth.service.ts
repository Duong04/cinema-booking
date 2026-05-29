import api from '@/shared/api/api'
import type {
  LoginPayload,
  LoginResponse,
  RegisterPayload,
  RegisterResponse,
  UserProfile,
  UpdateProfilePayload,
  ChangePasswordPayload,
} from '@/shared/types/auth'

export const authService = {
  login: async (payload: LoginPayload) => {
    await api.csrf()
    return api.post<LoginResponse>('/auth/login', payload)
  },

  logout: () => api.post('/auth/logout'),

  getMe: () => api.get<UserProfile>('/auth/profile'),

  updateProfile: (payload: UpdateProfilePayload) => api.put<UserProfile>('/auth/profile', payload),

  changePassword: (payload: ChangePasswordPayload) => api.put('/auth/password', payload),

  register: async (payload: RegisterPayload) => {
    await api.csrf()
    return api.post<RegisterResponse>('/auth/register', payload)
  },
}
