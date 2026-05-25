import type { MenuOption } from 'naive-ui'
import type { Component } from 'vue'
import { h } from 'vue'
import {
  BookOutline as BookIcon,
  ShieldCheckmarkOutline as ShieldCheckmarkIcon,
  LocationOutline as LocationOutlineIcon,
  ColorFilterOutline as ColorFilterOutlineIcon,
  NuclearSharp as NuclearSharpIcon,
  TvOutline as TvOutlineIcon,
  PersonCircleSharp as UserIcon,
  PeopleOutline as PeopleIcon,
  FastFoodOutline as ComboIcon,
  PricetagOutline as PromotionIcon,
} from '@vicons/ionicons5'
import { NIcon } from 'naive-ui'
import router from '@/router'

const renderIcon = (icon: Component) => {
  return () => h(NIcon, null, { default: () => h(icon) })
}

export const handleMenuSelect = (key: string) => {
  router.push({ name: key })
}

export const menuOptions: MenuOption[] = [
  {
    label: 'Dashboard',
    key: 'dashboard',
    icon: renderIcon(BookIcon),
  },
  {
    label: 'Rạp chiếu phim',
    key: 'cinema-group',
    icon: renderIcon(NuclearSharpIcon),
    children: [
      {
        label: 'Danh sách rạp',
        key: 'admin-cinemas',
      },
      {
        label: 'Phòng chiếu',
        key: 'admin-rooms',
      },
      {
        label: 'Loại ghế',
        key: 'admin-seat-types',
      },
      {
        label: 'Lịch chiếu',
        key: 'admin-showtimes',
      }
    ],
  },
  {
    label: 'Phim',
    key: 'movie-group',
    icon: renderIcon(TvOutlineIcon),
    children: [
      {
        label: 'Thể loại phim',
        key: 'admin-genres',
      },
      {
        label: 'Danh sách phim',
        key: 'admin-movies',
      }
    ],
  },
  {
    label: 'Chuỗi rạp',
    key: 'admin-cinema-chains',
    icon: renderIcon(ColorFilterOutlineIcon),
  },
  {
    label: 'Combo bắp nước',
    key: 'admin-combos',
    icon: renderIcon(ComboIcon),
  },
  {
    label: 'Khuyến mãi',
    key: 'admin-promotions',
    icon: renderIcon(PromotionIcon),
  },
  {
    label: 'Khu vực',
    key: 'location-group',
    icon: renderIcon(LocationOutlineIcon),
    children: [
      {
        label: 'Thành phố',
        key: 'admin-cities',
      },
    ],
  },
  {
    label: 'Nhân sự',
    key: 'admin-users',
    icon: renderIcon(UserIcon),
  },
  {
    label: 'Khách hàng',
    key: 'admin-customers',
    icon: renderIcon(PeopleIcon),
  },
  {
    label: 'Phân quyền',
    key: 'permission-group',
    icon: renderIcon(ShieldCheckmarkIcon),
    children: [
      {
        label: 'Vai trò',
        key: 'admin-roles',
      },
      {
        label: 'Quyền',
        key: 'admin-permissions',
      },
      {
        label: 'Hành động',
        key: 'admin-actions',
      }
    ],
  }
]
