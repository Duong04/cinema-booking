<script setup lang="ts">
import type { DataTableColumns, DataTableRowKey } from 'naive-ui'
import { computed, h, onMounted, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { NAvatar, NButton, NEllipsis, NIcon, NSpace, NTag, useMessage } from 'naive-ui'
import { CallOutline, MailOutline, PersonCircleOutline, Search as SearchIcon } from '@vicons/ionicons5'
import { useUser } from '../../composables/useUser'
import { useRole } from '../../composables/useRole'
import UserFormModal from './components/UserFormModal.vue'
import type { User } from '../../types/user.type'
import { formatDate } from '@/shared/utils/formatDate'
import { userService } from '../../services/user.service'

const { data, loading, filters, pagination, fetchUsers } = useUser()
const { data: roleData, fetchRoles } = useRole()
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

function getGenderMeta(gender?: User['gender']) {
  if (gender === 'male') return { label: 'Nam', type: 'info' as const }
  if (gender === 'female') return { label: 'Nữ', type: 'error' as const }
  return { label: 'Khác', type: 'warning' as const }
}

function createColumns(): DataTableColumns<User> {
  return [
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
      render: (row) => h('span', formatDate(row.created_at)),
    },
    {
      title: 'Cập nhật',
      key: 'updated_at',
      width: 150,
      render: (row) => h('span', formatDate(row.updated_at)),
    },
    {
      title: 'Thao tác',
      key: 'actions',
      width: 180,
      fixed: 'right',
      render: (row) =>
        h('div', { style: 'display:flex;justify-content:flex-end;gap:8px' }, [
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
        ]),
    },
  ]
}

const columns = createColumns()

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
    <n-button type="primary" @click="openCreateModal">{{ createButtonLabel }}</n-button>
  </n-space>

  <n-data-table
    :columns="columns"
    :data="data"
    :loading="loading"
    :pagination="pagination"
    :row-key="(row: User) => row.id"
    :scroll-x="1470"
    remote
    @update:checked-row-keys="handleCheckedRowKeys"
  />

  <UserFormModal v-model:show="showModal" :roles="roleOptions" :user="selectedUser" @success="fetchUsers" />
</template>
