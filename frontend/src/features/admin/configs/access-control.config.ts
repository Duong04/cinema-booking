export const ADMIN_PERMISSIONS = {
  DASHBOARD: 'dashboard',
  USERS: 'users',
  ROLES: 'roles',
  CINEMAS: 'cinemas',
  ROOMS: 'rooms',
  SEATS: 'seats',
  MOVIES: 'movies',
  GENRES: 'genres',
  SHOWTIMES: 'showtimes',
  BOOKINGS: 'bookings',
  PAYMENTS: 'payments',
  COMBOS: 'combos',
  PROMOTIONS: 'promotions',
  REPORTS: 'reports',
} as const

export const ADMIN_ACTIONS = {
  VIEW: 'view',
  CREATE: 'create',
  UPDATE: 'update',
  DELETE: 'delete',
  APPROVE: 'approve',
  EXPORT: 'export',
} as const

export type AdminPermissionKey = (typeof ADMIN_PERMISSIONS)[keyof typeof ADMIN_PERMISSIONS]
export type AdminActionKey = (typeof ADMIN_ACTIONS)[keyof typeof ADMIN_ACTIONS] | string
