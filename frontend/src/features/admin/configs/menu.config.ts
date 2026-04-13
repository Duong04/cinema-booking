import type { MenuOption } from 'naive-ui'
import type { Component } from 'vue'
import { h } from 'vue'
import {
  BookOutline as BookIcon,
  ShieldCheckmarkOutline as ShieldCheckmarkIcon,
  LocationOutline as LocationOutlineIcon,
  ColorFilterOutline as ColorFilterOutlineIcon,
  NuclearSharp as NuclearSharpIcon,
  TvOutline as TvOutlineIcon
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
        key: 'cinemas',
      },
      {
        label: 'Phòng chiếu',
        key: 'rooms',
      },
    ],
  },
  {
    label: 'Phim',
    key: 'movie-group',
    icon: renderIcon(TvOutlineIcon),
    children: [
      {
        label: 'Thể loại phim',
        key: 'genres',
      },
      {
        label: 'Danh sách phim',
        key: 'movies',
      }
    ],
  },
  {
    label: 'Chuỗi rạp',
    key: 'cinema-chains',
    icon: renderIcon(ColorFilterOutlineIcon),
  },
  {
    label: 'Khu vực',
    key: 'location-group',
    icon: renderIcon(LocationOutlineIcon),
    children: [
      {
        label: 'Thành phố',
        key: 'cities',
      },
    ],
  },
  {
    label: 'Phân quyền',
    key: 'permission-group',
    icon: renderIcon(ShieldCheckmarkIcon),
    children: [
      {
        label: 'Vai trò',
        key: 'roles',
      },
      {
        label: 'Quyền',
        key: 'permissions',
      },
      {
        label: 'Hành động',
        key: 'actions',
      }
    ],
  }
]