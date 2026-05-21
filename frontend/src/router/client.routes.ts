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
    path: 'booking/showtime/:movieId',
    name: 'booking-showtime',
    component: () => import('@/features/client/views/booking/ShowtimeView.vue'),
    meta: {
      title: 'Cinema - Chọn suất chiếu',
    },
  },
  {
    path: 'booking/seats/:showtimeId',
    name: 'booking-seats',
    component: () => import('@/features/client/views/booking/SeatView.vue'),
    meta: {
      title: 'Cinema - Chọn ghế',
    },
  },
  {
    path: 'booking/combo',
    name: 'booking-combo',
    component: () => import('@/features/client/views/booking/ComboView.vue'),
    meta: {
      title: 'Cinema - Chọn combo',
    },
  },
  {
    path: 'booking/checkout',
    name: 'booking-checkout',
    component: () => import('@/features/client/views/booking/CheckoutView.vue'),
    meta: {
      title: 'Cinema - Thanh toán',
    },
  },
  {
    path: 'booking/payment-result',
    name: 'booking-payment-result',
    component: () => import('@/features/client/views/booking/PaymentResultView.vue'),
    meta: {
      title: 'Cinema - Kết quả thanh toán',
    },
  },
  {
    path: 'login',
    name: 'login',
    component: () => import('@/features/shared/views/LoginView.vue'),
    meta: {
      title: 'Cinema - Đăng nhập',
      guestOnly: true
    },
  },
  {
    path: 'register',
    name: 'register',
    component: () => import('@/features/shared/views/RegisterView.vue'),
    meta: {
      title: 'Cinema - Đăng ký',
      guestOnly: true
    },
  },
  {
    path: 'register-success',
    name: 'register-success',
    component: () => import('@/features/shared/views/RegisterSuccessView.vue'),
    meta: {
      title: 'Cinema - Đăng ký thành công',
      guestOnly: true
    },
  }
]
