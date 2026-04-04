import type { RouteRecordRaw } from 'vue-router'

export const clientRoutes: RouteRecordRaw[] = [
  {
    path: '',
    name: 'home',
    component: () => import('@/features/client/views/home/HomeView.vue'),
    meta: {
      title: 'Cinema - Trang chủ',
    },
  },
  {
    path: 'movies',
    name: 'movies',
    component: () => import('@/features/client/views/movie/MovieView.vue'),
    meta: {
      title: 'Cinema - Danh sách phim',
    },
  },
  {
    path: 'movies/:id',
    name: 'movie-detail',
    component: () => import('@/features/client/views/movie/MovieDetailView.vue'),
    meta: {
      title: 'Cinema - Chi tiết phim',
    },
  },
  {
    path: 'cinemas',
    name: 'cinemas',
    component: () => import('@/features/client/views/cinema/CinemaView.vue'),
    meta: {
      title: 'Cinema - Danh sách rạp phim',
    },
  },
  {
    path: 'schedule',
    name: 'schedule',
    component: () => import('@/features/client/views/schedule/ScheduleView.vue'),
    meta: {
      title: 'Cinema - Lịch chiếu',
    },
  },
  {
    path: 'wishlist',
    name: 'wishlist',
    component: () => import('@/features/client/views/wishlist/WishlistView.vue'),
    meta: {
      title: 'Cinema - Danh sách yêu thích',
    },
  },
  {
    path: 'notifications',
    name: 'notifications',
    component: () => import('@/features/client/views/notification/NotificationView.vue'),
    meta: {
      title: 'Cinema - Thông báo',
    },
  },
  {
    path: 'login',
    name: 'login',
    component: () => import('@/features/shared/auth/views/LoginView.vue'),
    meta: {
      title: 'Cinema - Đăng nhập',
      guestOnly: true
    },
  },
  {
    path: 'register',
    name: 'register',
    component: () => import('@/features/shared/auth/views/RegisterView.vue'),
    meta: {
      title: 'Cinema - Đăng ký',
      guestOnly: true
    },
  }
]
