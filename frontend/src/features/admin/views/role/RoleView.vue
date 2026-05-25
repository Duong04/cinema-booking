<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRole } from '../../composables/useRole'
import RoleFormModal from './components/RoleFormModal.vue'
import type { Role } from '../../types/role.type'
import { Search as SearchIcon, TrashOutline } from '@vicons/ionicons5'
import { NIcon } from 'naive-ui'
import { useMessage, useDialog } from 'naive-ui'

const { data, loading, filters, pagination, fetchRoles, deleteRole } = useRole()

const message = useMessage()
const dialog = useDialog()
const showModal = ref(false)
const selectedRole = ref<Role | null>(null)

const visibleAvatarCount = 3

function openCreateModal() {
  selectedRole.value = null
  showModal.value = true
}

function openEditModal(row: Role) {
  selectedRole.value = row
  showModal.value = true
}

function handleDelete(row: Role) {
  dialog.warning({
    title: 'Xác nhận xóa',
    content: `Bạn có chắc chắn muốn xóa vai trò "${row.name}" không?`,
    positiveText: 'Xóa',
    negativeText: 'Hủy',
    onPositiveClick: async () => {
      try {
        await deleteRole(row.id)
        message.success('Xóa vai trò thành công')
        fetchRoles()
      } catch {
        message.error('Đã có lỗi xảy ra khi xóa vai trò')
      }
    },
  })
}

function userCountLabel(role: Role) {
  const count = role.user_count ?? role.users?.length ?? 0
  return `Tổng ${count} người dùng`
}

function overflowCount(role: Role) {
  const count = role.user_count ?? role.users?.length ?? 0
  return Math.max(count - visibleAvatarCount, 0)
}

onMounted(fetchRoles)
</script>

<template>
  <n-space justify="space-between" class="mb-4">
    <n-space>
      <n-input
        v-model:value="filters.search"
        placeholder="Tìm kiếm theo tên..."
        clearable
        style="width: 300px"
      >
        <template #suffix>
          <n-icon>
            <SearchIcon />
          </n-icon>
        </template>
      </n-input>
    </n-space>
    <n-button type="primary" @click="openCreateModal">+ Tạo vai trò</n-button>
  </n-space>

  <n-spin :show="loading">
    <div v-if="data.length" class="role-grid">
      <article
        v-for="role in data"
        :key="role.id"
        class="role-card"
      >
        <div class="role-card__top">
          <span class="role-card__count">{{ userCountLabel(role) }}</span>

          <div class="avatar-stack">
            <n-tooltip
              v-for="user in role.users?.slice(0, visibleAvatarCount)"
              :key="user.id"
              trigger="hover"
              placement="top"
            >
              <template #trigger>
                <span class="avatar-stack__trigger">
                  <n-avatar
                    :src="user.avatar || undefined"
                    round
                    :size="44"
                    object-fit="cover"
                    class="avatar-stack__item"
                  />
                </span>
              </template>
              {{ user.name }}
            </n-tooltip>
            <div
              v-if="overflowCount(role)"
              class="avatar-stack__more"
            >
              +{{ overflowCount(role) }}
            </div>
          </div>
        </div>

        <div class="role-card__body">
          <h3>{{ role.name }}</h3>
          <p v-if="role.description">{{ role.description }}</p>
        </div>

        <div class="role-card__footer">
          <button class="role-card__edit" type="button" @click="openEditModal(role)">
            Edit Role
          </button>
          <button class="role-card__delete" type="button" @click="handleDelete(role)">
            <n-icon size="24">
              <TrashOutline />
            </n-icon>
          </button>
        </div>
      </article>
    </div>

    <n-empty v-else description="Chưa có vai trò nào" />
  </n-spin>

  <n-pagination
    v-if="pagination.itemCount > pagination.pageSize"
    v-model:page="pagination.page"
    v-model:page-size="pagination.pageSize"
    :item-count="pagination.itemCount"
    :page-sizes="pagination.pageSizes"
    show-size-picker
    class="role-pagination"
    @update:page="pagination.onChange"
    @update:page-size="pagination.onUpdatePageSize"
  />

  <RoleFormModal v-model:show="showModal" :role="selectedRole" @success="fetchRoles" />
</template>

<style scoped>
.role-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  gap: 24px;
}

.role-card {
  min-height: 204px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  border: 1px solid rgba(15, 23, 42, 0.06);
  border-radius: 8px;
  background: #fff;
  box-shadow: 0 10px 24px rgba(15, 23, 42, 0.11);
  padding: 26px;
}

.role-card__top,
.role-card__footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
}

.role-card__count {
  color: #6f6b7d;
  font-size: 16px;
  font-weight: 500;
}

.avatar-stack {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  min-width: 112px;
}

.avatar-stack__trigger,
.avatar-stack__more {
  margin-left: -12px;
}

.avatar-stack__trigger {
  display: inline-flex;
  position: relative;
  transition: transform 160ms ease, z-index 160ms ease;
}

.avatar-stack__trigger:first-child {
  margin-left: 0;
}

.avatar-stack__trigger:hover {
  z-index: 3;
  transform: translateY(-5px);
}

.avatar-stack__item,
.avatar-stack__more {
  border: 2px solid #fff;
  box-shadow: 0 2px 6px rgba(15, 23, 42, 0.12);
  transition: box-shadow 160ms ease, transform 160ms ease;
}

.avatar-stack__trigger:hover .avatar-stack__item {
  transform: scale(1.04);
  box-shadow: 0 8px 16px rgba(15, 23, 42, 0.18);
}

.avatar-stack__more {
  width: 44px;
  height: 44px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 999px;
  background: #f0eef5;
  color: #5d596c;
  font-weight: 700;
}

.role-card__body {
  min-height: 58px;
}

.role-card__body h3 {
  margin: 0 0 8px;
  color: #3f3b52;
  font-size: 22px;
  font-weight: 700;
}

.role-card__body p {
  margin: 0;
  color: #8a8599;
  font-size: 14px;
}

.role-card__edit,
.role-card__delete {
  border: 0;
  background: transparent;
  cursor: pointer;
}

.role-card__edit {
  padding: 0;
  color: #4f63ff;
  font-size: 16px;
  font-weight: 500;
}

.role-card__delete {
  display: inline-flex;
  width: 34px;
  height: 34px;
  align-items: center;
  justify-content: center;
  color: #ff4c51;
  border-radius: 8px;
}

.role-card__delete:hover {
  background: rgba(255, 76, 81, 0.1);
}

.role-pagination {
  margin-top: 24px;
  justify-content: flex-end;
}
</style>
