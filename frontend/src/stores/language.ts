import { defineStore } from 'pinia';

export type Language = 'en' | 'vi';

export const useLanguageStore = defineStore('language', {
  state: () => ({
    language: 'vi' as Language,
    translations: {
      en: {
        'nav.home': 'Home',
        'nav.movies': 'Movies',
        'nav.cinemas': 'Cinemas',
        'nav.schedule': 'Schedule',
        'nav.search': 'Search',
        'nav.profile': 'Profile',
        'home.hero.title': 'Experience Cinema Like Never Before',
        'home.hero.subtitle': 'Book your tickets for the latest blockbusters in premium theaters.',
        'home.now_playing': 'Now Playing',
        'home.coming_soon': 'Coming Soon',
        'home.top_movies': 'Top 5 Movies',
        'home.news': 'Cinema News',
        'home.promotions': 'Special Offers',
        'movie.book_now': 'Book Now',
        'movie.details': 'Details',
        'movie.duration': 'min',
        'booking.select_showtime': 'Select Showtime',
        'booking.select_seats': 'Select Seats',
        'booking.checkout': 'Checkout',
        'profile.personal_info': 'Personal Information',
        'profile.change_password': 'Change Password',
        'profile.history': 'Booking History',
        'common.total': 'Total',
        'search.placeholder': 'Search for movies...',
        'wishlist.title': 'My Wishlist',
        'notification.title': 'Notifications',
        'movie.synopsis': 'Synopsis',
        'common.confirm': 'Confirm',
        '404.title': 'Lost in the Cosmos?',
        '404.subtitle': 'Even in the vastness of the galaxy, this scene could not be found.',
        '404.back_home': 'Return to Earth',
        'register.success.title': 'Registration Successful!',
        'register.success.subtitle': 'We have sent an activation link to your email address.',
        'register.success.instruction': 'Please check your inbox (and spam folder) to activate your account and start your cinematic journey.',
        'register.success.back_to_login': 'Back to Login',
      },
      vi: {
        'nav.home': 'Trang chủ',
        'nav.movies': 'Phim',
        'nav.cinemas': 'Rạp',
        'nav.schedule': 'Lịch chiếu',
        'nav.search': 'Tìm kiếm',
        'nav.profile': 'Tài khoản',
        'home.hero.title': 'Trải Nghiệm Điện Ảnh Đỉnh Cao',
        'home.hero.subtitle': 'Đặt vé xem những bộ phim bom tấn mới nhất tại các rạp chiếu phim cao cấp.',
        'home.now_playing': 'Phim Đang Chiếu',
        'home.coming_soon': 'Phim Sắp Chiếu',
        'home.top_movies': 'Top 5 Phim Hay',
        'home.news': 'Tin Tức Điện Ảnh',
        'home.promotions': 'Ưu Đãi Đặc Biệt',
        'movie.book_now': 'Đặt Vé Ngay',
        'movie.details': 'Chi tiết',
        'movie.duration': 'phút',
        'booking.select_showtime': 'Chọn Suất Chiếu',
        'booking.select_seats': 'Chọn Ghế',
        'booking.checkout': 'Thanh Toán',
        'profile.personal_info': 'Thông Tin Cá Nhân',
        'profile.change_password': 'Đổi Mật Khẩu',
        'profile.history': 'Lịch Sử Đặt Vé',
        'common.total': 'Tổng cộng',
        'search.placeholder': 'Tìm phim...',
        'wishlist.title': 'Danh sách yêu thích',
        'notification.title': 'Thông báo',
        'movie.synopsis': 'Nội dung phim',
        'common.confirm': 'Xác nhận',
        '404.title': 'Lạc giữa Thiên hà?',
        '404.subtitle': 'Ngay cả trong sự bao la của vũ trụ, cảnh quay này cũng không thể tìm thấy.',
        '404.back_home': 'Trở về Trái đất',
        'register.success.title': 'Đăng ký thành công!',
        'register.success.subtitle': 'Chúng tôi đã gửi liên kết kích hoạt đến địa chỉ email của bạn.',
        'register.success.instruction': 'Vui lòng kiểm tra hộp thư đến (và cả thư rác) để kích hoạt tài khoản và bắt đầu hành trình điện ảnh của bạn.',
        'register.success.back_to_login': 'Quay lại Đăng nhập',
      }
    } as Record<Language, Record<string, string>>
  }),
  actions: {
    setLanguage(lang: Language) {
      this.language = lang;
    },
    t(key: string) {
      return this.translations[this.language][key] || key;
    }
  }
});
