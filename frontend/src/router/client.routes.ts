import type { RouteRecordRaw } from 'vue-router'

export const clientRoutes: RouteRecordRaw[] = [
  {
    path: '',
    name: 'home',
    component: () => import('@/views/client/HomeView.vue'),
    meta: {
      title: 'Cinema - Trang chủ',
    },
  },
  {
    path: 'movies',
    name: 'movies',
    component: () => import('@/views/client/MovieView.vue'),
    meta: {
      title: 'Cinema - Danh sách phim',
    },
  },
  {
    path: 'movies/:id',
    name: 'movie-detail',
    component: () => import('@/views/client/MovieDetailView.vue'),
    meta: {
      title: 'Cinema - Chi tiết phim',
    },
  },
  {
    path: 'cinemas',
    name: 'cinemas',
    component: () => import('@/views/client/CinemaView.vue'),
    meta: {
      title: 'Cinema - Danh sách rạp phim',
    },
  },
  {
    path: 'schedule',
    name: 'schedule',
    component: () => import('@/views/client/ScheduleView.vue'),
    meta: {
      title: 'Cinema - Lịch chiếu',
    },
  },
  {
    path: 'wishlist',
    name: 'wishlist',
    component: () => import('@/views/client/WishlistView.vue'),
    meta: {
      title: 'Cinema - Danh sách yêu thích',
    },
  },
  {
    path: 'notifications',
    name: 'notifications',
    component: () => import('@/views/client/NotificationView.vue'),
    meta: {
      title: 'Cinema - Thông báo',
    },
  },
  {
    path: 'login',
    name: 'login',
    component: () => import('@/views/shared/auth/LoginView.vue'),
    meta: {
      title: 'Cinema - Đăng nhập',
      guestOnly: true
    },
  },
  {
    path: 'register',
    name: 'register',
    component: () => import('@/views/shared/auth/RegisterView.vue'),
    meta: {
      title: 'Cinema - Đăng ký',
      guestOnly: true
    },
  }
]
