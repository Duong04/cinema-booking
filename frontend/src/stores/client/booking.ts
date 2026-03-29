import { defineStore } from 'pinia';
import type { Booking } from '@/data/mockData';

export const useBookingStore = defineStore('booking', {
  state: () => ({
    currentBooking: null as Partial<Booking> | null,
    bookings: [] as Booking[],
    wishlist: [] as string[],
  }),
  actions: {
    setCurrentBooking(booking: Partial<Booking> | null) {
      this.currentBooking = booking;
    },
    addBooking(booking: Booking) {
      this.bookings.unshift(booking);
    },
    toggleWishlist(movieId: string) {
      const index = this.wishlist.indexOf(movieId);
      if (index === -1) {
        this.wishlist.push(movieId);
      } else {
        this.wishlist.splice(index, 1);
      }
    }
  }
});
