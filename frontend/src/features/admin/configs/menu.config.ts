import type { MenuOption } from 'naive-ui'
import type { Component } from 'vue'
import { h } from 'vue'
import {
  BookOutline as BookIcon,
  PersonOutline as PersonIcon,
  WineOutline as WineIcon,
  ShieldCheckmarkOutline as ShieldCheckmarkIcon,
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
    label: 'Movies',
    key: 'movies',
    icon: renderIcon(BookIcon),
    children: [
      {
        label: 'Rat',
        key: 'rat',
      },
    ],
  },
  {
    label: 'Cinemas',
    key: 'cinemas',
    icon: renderIcon(BookIcon),
  },
  {
    label: 'Phân quyền',
    key: 'permissions',
    icon: renderIcon(ShieldCheckmarkIcon),
    children: [
      {
        label: 'Vai trò',
        key: 'role',
      },
      {
        label: 'Quyền',
        key: 'permission',
      },
      {
        label: 'Hành động',
        key: 'action',
      }
    ],
  },
  {
    label: 'Dance Dance Dance',
    key: 'Dance Dance Dance',
    icon: renderIcon(BookIcon),
    children: [
      {
        type: 'group',
        label: 'People',
        key: 'people',
        children: [
          {
            label: 'Narrator',
            key: 'narrator',
            icon: renderIcon(PersonIcon),
          },
          {
            label: 'Sheep Man',
            key: 'sheep-man',
            icon: renderIcon(PersonIcon),
          },
        ],
      },
      {
        label: 'Beverage',
        key: 'beverage',
        icon: renderIcon(WineIcon),
        children: [
          {
            label: 'Whisky',
            key: 'whisky',
          },
        ],
      },
      {
        label: 'Food',
        key: 'food',
        children: [
          {
            label: 'Sandwich',
            key: 'sandwich',
          },
        ],
      },
      {
        label: 'The past increases. The future recedes.',
        key: 'the-past-increases-the-future-recedes',
      },
    ],
  },
]