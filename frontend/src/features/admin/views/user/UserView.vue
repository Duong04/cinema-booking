<script setup lang="ts">
import type { DataTableColumns, DataTableRowKey } from 'naive-ui'
import { computed, h, onMounted, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { NAvatar, NButton, NEllipsis, NIcon, NSpace, NTag, useMessage } from 'naive-ui'
import { CallOutline, MailOutline, PersonCircleOutline, Search as SearchIcon } from '@vicons/ionicons5'
import { Ticket } from 'lucide-vue-next'
import { useUser } from '../../composables/useUser'
import { useRole } from '../../composables/useRole'
import UserFormModal from './components/UserFormModal.vue'
import type { User } from '../../types/user.type'
import { formatDateTime } from '@/shared/utils/formatDate'
import { userService } from '../../services/user.service'
import { useAdminPermission } from '../../composables/useAdminPermission'
import { ADMIN_ACTIONS, ADMIN_PERMISSIONS } from '@/features/admin/configs/access-control.config'

const { data, loading, filters, pagination, fetchUsers } = useUser()
const { data: roleData, fetchRoles } = useRole()
const { can } = useAdminPermission()
const message = useMessage()
const route = useRoute()
const showModal = ref(false)
const selectedUser = ref<User | null>(null)
const checkedRowKeysRef = ref<DataTableRowKey[]>([])

const userScope = computed(() => (route.meta.userScope === 'customer' ? 'customer' : 'staff'))
const isCustomerScope = computed(() => userScope.value === 'customer')
const customerRole = computed(() =>
  roleData.value?.find((role) => role.name.toLowerCase() === 'customer') ?? null,
)
const pageTitle = computed(() => (isCustomerScope.value ? 'khách hàng' : 'nhân sự'))
const createButtonLabel = computed(() => (isCustomerScope.value ? '+ Tạo khách hàng' : '+ Tạo nhân sự'))
const canCreate = computed(() => can(ADMIN_PERMISSIONS.USERS, ADMIN_ACTIONS.CREATE))
const canUpdate = computed(() => can(ADMIN_PERMISSIONS.USERS, ADMIN_ACTIONS.UPDATE))

function getMembershipMeta(tier?: NonNullable<User['membership']>['tier']) {
  if (tier === 'platinum') {
    return {
      label: 'Platinum',
      badge: 'background:#ecfeff;color:#0e7490;border:1px solid #67e8f9',
      dot: '#06b6d4',
    }
  }

  if (tier === 'gold') {
    return {
      label: 'Gold',
      badge: 'background:#fffbeb;color:#b45309;border:1px solid #fcd34d',
      dot: '#f59e0b',
    }
  }

  if (tier === 'silver') {
    return {
      label: 'Silver',
      badge: 'background:#f8fafc;color:#475569;border:1px solid #cbd5e1',
      dot: '#94a3b8',
    }
  }

  return {
    label: 'Bronze',
    badge: 'background:#fff7ed;color:#c2410c;border:1px solid #fdba74',
    dot: '#f97316',
  }
}

function getGenderMeta(gender?: User['gender']) {
  if (gender === 'male') return { label: 'Nam', type: 'info' as const }
  if (gender === 'female') return { label: 'Nữ', type: 'error' as const }
  return { label: 'Khác', type: 'warning' as const }
}

function createColumns(): DataTableColumns<User> {
  const columns: DataTableColumns<User> = [
    { type: 'selection', width: 48 },
    {
      title: 'Người dùng',
      key: 'name',
      width: 260,
      fixed: 'left',
      render: (row) =>
        h(
          'div',
          { style: 'display:flex;align-items:center;gap:10px;min-width:0' },
          [
            h(
              NAvatar,
              {
                src: row.avatar,
                size: 36,
                round: true,
                objectFit: 'cover',
              },
            ),
            h(
              'div',
              { style: 'display:flex;flex-direction:column;gap:2px;min-width:0' },
              [
                h(
                  NEllipsis,
                  {
                    style: 'font-weight:600;color:#1f2937;max-width:180px',
                    tooltip: true,
                  },
                  { default: () => row.name || 'N/A' },
                ),
                h(
                  'div',
                  {
                    style:
                      'display:flex;align-items:center;gap:4px;color:#6b7280;font-size:12px;min-width:0',
                  },
                  [
                    h(
                      NIcon,
                      { color: '#427AB5', size: 14 },
                      { default: () => h(CallOutline) },
                    ),
                    h(
                      NEllipsis,
                      { style: 'max-width:150px', tooltip: true },
                      { default: () => row.phone || 'Chưa có SĐT' },
                    ),
                  ],
                ),
              ],
            ),
          ],
        ),
    },
    {
      title: 'Email',
      key: 'email',
      width: 260,
      render: (row) =>
        h('div', { style: 'display:flex;align-items:center;gap:6px;min-width:0' }, [
          h(NIcon, { color: '#18a058', size: 18 }, { default: () => h(MailOutline) }),
          h(
            NEllipsis,
            { style: 'max-width:210px', tooltip: true },
            { default: () => row.email || 'N/A' },
          ),
        ]),
    },
    ...(isCustomerScope.value
      ? [
          {
            title: 'Membership',
            key: 'membership',
            width: 180,
            render: (row: User) => {
              const membership = row.membership
              const tier = getMembershipMeta(membership?.tier)

              return h('div', { style: 'display:flex;align-items:center;gap:8px;min-width:0' }, [
                h(
                  'span',
                  {
                    style:
                      'display:inline-flex;align-items:center;gap:6px;width:max-content;border-radius:999px;padding:4px 10px;font-size:12px;font-weight:700;line-height:1;' +
                      tier.badge,
                  },
                  [
                    h('span', {
                      style: `width:7px;height:7px;border-radius:999px;background:${tier.dot};box-shadow:0 0 0 3px rgb(255 255 255 / 0.7)`,
                    }),
                    tier.label,
                  ],
                ),
                h(
                  'span',
                  { style: 'color:#6b7280;font-size:12px;white-space:nowrap' },
                  `${(membership?.points ?? 0).toLocaleString('vi-VN')} điểm`,
                ),
              ])
            },
          },
          {
            title: 'Số vé mua',
            key: 'tickets_purchased_count',
            width: 120,
            align: 'center' as const,
            render: (row: User) =>
              h(
                'span',
                {
                  style:
                    'display:inline-flex;align-items:center;justify-content:center;gap:6px;border-radius:999px;padding:5px 10px;background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;font-size:12px;font-weight:800;line-height:1;min-width:58px',
                },
                [
                  h(Ticket, { size: 14, strokeWidth: 2.4 }),
                  (row.tickets_purchased_count ?? 0).toLocaleString('vi-VN'),
                ],
              ),
          },
        ]
      : []),
    {
      title: 'Vai trò',
      key: 'role',
      width: 150,
      render: (row) =>
        h(
          NTag,
          { size: 'small', bordered: false, type: 'info' },
          {
            icon: () => h(NIcon, null, { default: () => h(PersonCircleOutline) }),
            default: () => row.role?.name || 'N/A',
          },
        ),
    },
    {
      title: 'Trạng thái',
      key: 'status',
      width: 150,
      render: (row) =>
        h(
          NTag,
          {
            size: 'small',
            bordered: false,
            type: Boolean(row.is_active) ? 'success' : 'error',
          },
          { default: () => (Boolean(row.is_active) ? 'Hoạt động' : 'Không hoạt động') },
        ),
    },
    {
      title: 'Giới tính',
      key: 'gender',
      width: 120,
      render: (row) => {
        const gender = getGenderMeta(row.gender)
        return h(
          NTag,
          { size: 'small', bordered: false, type: gender.type },
          { default: () => gender.label },
        )
      },
    },
    {
      title: 'Ngày tạo',
      key: 'created_at',
      width: 150,
      render: (row) => h('span', formatDateTime(row.created_at)),
    },
    {
      title: 'Cập nhật',
      key: 'updated_at',
      width: 150,
      render: (row) => h('span', formatDateTime(row.updated_at)),
    },
    {
      title: 'Thao tác',
      key: 'actions',
      width: 180,
      fixed: 'right',
      render: (row) =>
        h('div', { style: 'display:flex;justify-content:flex-end;gap:8px' }, [
          ...(canUpdate.value
            ? [
                h(
                  NButton,
                  {
                    size: 'small',
                    type: Boolean(row.is_active) ? 'warning' : 'success',
                    secondary: true,
                    onClick: () => handleToggleStatus(row),
                  },
                  { default: () => (Boolean(row.is_active) ? 'Lock' : 'Unlock') },
                ),
                h(
                  NButton,
                  {
                    size: 'small',
                    type: 'primary',
                    secondary: true,
                    onClick: () => openEditModal(row),
                  },
                  { default: () => 'Edit' },
                ),
              ]
            : []),
        ]),
    },
  ]

  return columns
}

const columns = computed(() => createColumns())

const roleOptions = computed(() => {
  const customerRoleId = customerRole.value?.id

  return (
    roleData.value
      ?.filter((role) => {
        if (!customerRoleId) return true
        return isCustomerScope.value ? role.id === customerRoleId : role.id !== customerRoleId
      })
      .map((role) => ({ label: role.name, value: role.id })) || []
  )
})

function openCreateModal() {
  selectedUser.value = null
  showModal.value = true
}

function openEditModal(row: User) {
  selectedUser.value = row
  showModal.value = true
}

function handleCheckedRowKeys(keys: DataTableRowKey[]) {
  checkedRowKeysRef.value = keys
}

function applyUserScopeFilters() {
  const customerRoleId = customerRole.value?.id

  if (!customerRoleId) {
    return
  }

  pagination.page = 1

  if (isCustomerScope.value) {
    filters.role_id = customerRoleId
    filters.ignore_role_id = null
  } else {
    filters.role_id = null
    filters.ignore_role_id = customerRoleId
  }

  fetchUsers()
}

async function handleToggleStatus(row: User) {
  try {
    await userService.updateUser(row.id, {
      is_active: !Boolean(row.is_active),
    })
    message.success(Boolean(row.is_active) ? 'Đã khóa người dùng' : 'Đã mở hoạt động người dùng')
    fetchUsers()
  } catch {
    message.error('Cập nhật trạng thái người dùng thất bại')
  }
}

watch(() => [route.name, customerRole.value?.id], applyUserScopeFilters)

onMounted(fetchRoles)
</script>

<template>
  <n-space justify="space-between" class="mb-4">
    <n-space>
      <n-input
        v-model:value="filters.search"
        :placeholder="`Tìm kiếm ${pageTitle} theo tên...`"
        clearable
        style="width: 300px"
      >
        <template #suffix>
          <n-icon>
            <SearchIcon />
          </n-icon>
        </template>
      </n-input>
      <n-select
        v-if="!isCustomerScope"
        v-model:value="filters.role_id"
        placeholder="Lọc theo vai trò"
        :options="roleOptions"
        style="width: 200px"
        clearable
      />
      <n-select
        v-model:value="filters.is_active"
        placeholder="Lọc theo trạng thái"
        :options="[
          { label: 'Hoạt động', value: 1 },
          { label: 'Không hoạt động', value: 0 },
        ]"
        style="width: 200px"
        clearable
      />
    </n-space>
    <n-button v-if="canCreate" type="primary" @click="openCreateModal">{{ createButtonLabel }}</n-button>
  </n-space>

  <n-data-table
    :columns="columns"
    :data="data"
    :loading="loading"
    :pagination="pagination"
    :row-key="(row: User) => row.id"
    :scroll-x="isCustomerScope ? 1770 : 1470"
    remote
    @update:checked-row-keys="handleCheckedRowKeys"
  />

  <UserFormModal v-model:show="showModal" :roles="roleOptions" :user="selectedUser" @success="fetchUsers" />
</template>
