import type { RouteRecordRaw } from 'vue-router'

export const adminRoutes: RouteRecordRaw[] = [
  {
    path: 'dashboard',
    name: 'dashboard',
    component: () => import('@/features/admin/views/dashboard/DashboardView.vue'),
    meta: {
      title: 'Cinema - Admin Dashboard',
      breadcrumb: [{ label: 'Dashboard' }],
    },
  },
  {
    path: 'profile',
    name: 'admin-profile',
    component: () => import('@/features/admin/views/profile/AdminProfileView.vue'),
    meta: {
      title: 'Cinema - Admin Profile',
      breadcrumb: [
        { label: 'Dashboard', name: 'dashboard' },
        { label: 'Hồ sơ quản trị' },
      ],
    },
  },
  {
    path: 'settings',
    name: 'admin-settings',
    component: () => import('@/features/admin/views/profile/AdminProfileView.vue'),
    meta: {
      title: 'Cinema - Admin Settings',
      breadcrumb: [
        { label: 'Dashboard', name: 'dashboard' },
        { label: 'Cài đặt bảo mật' },
      ],
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
    path: 'combos',
    name: 'admin-combos',
    component: () => import('@/features/admin/views/combo/ComboView.vue'),
    meta: {
      title: 'Cinema - Admin Combos',
      breadcrumb: [
        { label: 'Dashboard', name: 'dashboard' },
        { label: 'Rạp chiếu phim' },
        { label: 'Combo bắp nước' },
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
    path: 'users',
    name: 'admin-users',
    component: () => import('@/features/admin/views/user/UserView.vue'),
    meta: {
      title: 'Cinema - Admin Staff',
      userScope: 'staff',
      breadcrumb: [
        { label: 'Dashboard', name: 'dashboard' },
        { label: 'Nhân sự' },
      ],
    },
  },
  {
    path: 'customers',
    name: 'admin-customers',
    component: () => import('@/features/admin/views/user/UserView.vue'),
    meta: {
      title: 'Cinema - Admin Customers',
      userScope: 'customer',
      breadcrumb: [
        { label: 'Dashboard', name: 'dashboard' },
        { label: 'Khách hàng' },
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
    path: 'actions',
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
