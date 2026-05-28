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
import { ADMIN_PERMISSIONS } from './access-control.config'

export type AdminMenuOption = MenuOption & {
  permissionKey?: string
  children?: AdminMenuOption[]
}

const renderIcon = (icon: Component) => {
  return () => h(NIcon, null, { default: () => h(icon) })
}

export const handleMenuSelect = (key: string) => {
  router.push({ name: key })
}

export const menuOptions: AdminMenuOption[] = [
  {
    label: 'Dashboard',
    key: 'dashboard',
    icon: renderIcon(BookIcon),
    permissionKey: ADMIN_PERMISSIONS.DASHBOARD,
  },
  {
    label: 'Rạp chiếu phim',
    key: 'cinema-group',
    icon: renderIcon(NuclearSharpIcon),
    children: [
      {
        label: 'Danh sách rạp',
        key: 'admin-cinemas',
        permissionKey: ADMIN_PERMISSIONS.CINEMAS,
      },
      {
        label: 'Phòng chiếu',
        key: 'admin-rooms',
        permissionKey: ADMIN_PERMISSIONS.ROOMS,
      },
      {
        label: 'Loại ghế',
        key: 'admin-seat-types',
        permissionKey: ADMIN_PERMISSIONS.SEATS,
      },
      {
        label: 'Lịch chiếu',
        key: 'admin-showtimes',
        permissionKey: ADMIN_PERMISSIONS.SHOWTIMES,
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
        permissionKey: ADMIN_PERMISSIONS.GENRES,
      },
      {
        label: 'Danh sách phim',
        key: 'admin-movies',
        permissionKey: ADMIN_PERMISSIONS.MOVIES,
      }
    ],
  },
  {
    label: 'Chuỗi rạp',
    key: 'admin-cinema-chains',
    icon: renderIcon(ColorFilterOutlineIcon),
    permissionKey: ADMIN_PERMISSIONS.CINEMAS,
  },
  {
    label: 'Combo bắp nước',
    key: 'admin-combos',
    icon: renderIcon(ComboIcon),
    permissionKey: ADMIN_PERMISSIONS.COMBOS,
  },
  {
    label: 'Khuyến mãi',
    key: 'admin-promotions',
    icon: renderIcon(PromotionIcon),
    permissionKey: ADMIN_PERMISSIONS.PROMOTIONS,
  },
  {
    label: 'Khu vực',
    key: 'location-group',
    icon: renderIcon(LocationOutlineIcon),
    children: [
      {
        label: 'Thành phố',
        key: 'admin-cities',
        permissionKey: ADMIN_PERMISSIONS.CINEMAS,
      },
    ],
  },
  {
    label: 'Nhân sự',
    key: 'admin-users',
    icon: renderIcon(UserIcon),
    permissionKey: ADMIN_PERMISSIONS.USERS,
  },
  {
    label: 'Khách hàng',
    key: 'admin-customers',
    icon: renderIcon(PeopleIcon),
    permissionKey: ADMIN_PERMISSIONS.USERS,
  },
  {
    label: 'Phân quyền',
    key: 'permission-group',
    icon: renderIcon(ShieldCheckmarkIcon),
    children: [
      {
        label: 'Vai trò',
        key: 'admin-roles',
        permissionKey: ADMIN_PERMISSIONS.ROLES,
      },
      {
        label: 'Quyền',
        key: 'admin-permissions',
        permissionKey: ADMIN_PERMISSIONS.ROLES,
      },
      {
        label: 'Hành động',
        key: 'admin-actions',
        permissionKey: ADMIN_PERMISSIONS.ROLES,
      }
    ],
  }
]
