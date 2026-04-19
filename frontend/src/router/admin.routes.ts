import type { RouteRecordRaw } from 'vue-router'

export const adminRoutes: RouteRecordRaw[] = [
  {
    path: 'doashboard',
    name: 'dashboard',
    component: () => import('@/features/admin/views/dashboard/DashboardView.vue'),
    meta: {
      title: 'Cinema - Admin Dashboard',
      breadcrumb: [{ label: 'Dashboard' }],
    },
  },
  {
    path: 'cinema-chains',
    name: 'admin-cinema-chains',
    component: () => import('@/features/admin/views/cinema-chain/CinemaChainView.vue'),
    meta: {
      title: 'Cinema - Admin Cinema Chains',
      breadcrumb: [
        { label: 'Dashboard', name: 'dashboard' },
        { label: 'Chuỗi rạp' },
      ],
    },
  },
  {
    path: 'cinemas',
    name: 'admin-cinemas',
    component: () => import('@/features/admin/views/cinema/CinemaView.vue'),
    meta: {
      title: 'Cinema - Admin Cinemas',
      breadcrumb: [
        { label: 'Dashboard', name: 'dashboard' },
        { label: 'Rạp chiếu phim' },
        { label: 'Danh sách rạp' },
      ],
    },
  },
  {
    path: 'rooms',
    name: 'admin-rooms',
    component: () => import('@/features/admin/views/room/RoomView.vue'),
    meta: {
      title: 'Cinema - Admin Rooms',
      breadcrumb: [
        { label: 'Dashboard', name: 'dashboard' },
        { label: 'Rạp chiếu phim' },
        { label: 'Phòng chiếu' },
      ],
    },
  },
  {
    path: 'seat-types',
    name: 'admin-seat-types',
    component: () => import('@/features/admin/views/seat-type/SeatTypeView.vue'),
    meta: {
      title: 'Cinema - Admin Seat Types',
      breadcrumb: [
        { label: 'Dashboard', name: 'dashboard' },
        { label: 'Rạp chiếu phim' },
        { label: 'Loại ghế' },
      ],
    },
  },
  {
    path: 'showtimes',
    name: 'admin-showtimes',
    component: () => import('@/features/admin/views/showtime/ShowtimeView.vue'),
    meta: {
      title: 'Cinema - Admin Showtimes',
      breadcrumb: [
        { label: 'Dashboard', name: 'dashboard' },
        { label: 'Rạp chiếu phim' },
        { label: 'Lịch chiếu' },
      ],
    },
  },
  {
    path: 'movies',
    name: 'admin-movies',
    component: () => import('@/features/admin/views/movie/MovieView.vue'),
    meta: {
      title: 'Cinema - Admin Movies',
      breadcrumb: [
        { label: 'Dashboard', name: 'dashboard' },
        { label: 'Phim' },
        { label: 'Danh sách phim' },
      ],
    },
  },
  {
    path: 'genres',
    name: 'admin-genres',
    component: () => import('@/features/admin/views/genre/GenreView.vue'),
    meta: {
      title: 'Cinema - Admin Genres',
      breadcrumb: [
        { label: 'Dashboard', name: 'dashboard' },
        { label: 'Phim' },
        { label: 'Thể loại phim' },
      ],
    },
  },
  {
    path: 'cities',
    name: 'admin-cities',
    component: () => import('@/features/admin/views/city/CityView.vue'),
    meta: {
      title: 'Cinema - Admin Cities',
      breadcrumb: [
        { label: 'Dashboard', name: 'dashboard' },
        { label: 'Khu vực' },
        { label: 'Thành phố' },
      ],
    },
  },
  {
    path: 'roles',
    name: 'admin-roles',
    component: () => import('@/features/admin/views/role/RoleView.vue'),
    meta: {
      title: 'Cinema - Admin Roles',
      breadcrumb: [
        { label: 'Dashboard', name: 'dashboard' },
        { label: 'Phân quyền' },
        { label: 'Vai trò' },
      ],
    },
  },
  {
    path: 'permissions',
    name: 'admin-permissions',
    component: () => import('@/features/admin/views/permission/PermissionView.vue'),
    meta: {
      title: 'Cinema - Admin Permissions',
      breadcrumb: [
        { label: 'Dashboard', name: 'dashboard' },
        { label: 'Phân quyền' },
        { label: 'Quyền' },
      ],
    },
  },
  {
    path: 'ations',
    name: 'admin-actions',
    component: () => import('@/features/admin/views/action/ActionView.vue'),
    meta: {
      title: 'Cinema - Admin Actions',
      breadcrumb: [
        { label: 'Dashboard', name: 'dashboard' },
        { label: 'Phân quyền' },
        { label: 'Hành động' },
      ],
    },
  }
]