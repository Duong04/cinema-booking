<script setup lang="ts">
import { ref, reactive, watch, nextTick } from 'vue'
import type { FormInst, FormRules, SelectOption } from 'naive-ui'
import { useMessage } from 'naive-ui'
import { roomService } from '@/features/admin/services/room.service'
import { ApiError } from '@/plugins/axios'
import type { Room, RoomType } from '@/features/admin/types/room.type'
import { ROOM_TYPES } from '@/features/admin/types/room.type'

interface RoomForm {
  name: string
  type: RoomType
  cinema_id: string | null
}

const props = defineProps<{
  room?: Room | null
  cinemas: SelectOption[]
}>()

const emit = defineEmits<{
  (e: 'success'): void
}>()

const message = useMessage()
const showModal = defineModel<boolean>('show')
const formRef = ref<FormInst | null>(null)
const formLoading = ref(false)
const isEdit = ref(false)
const roomTypeOptions = ROOM_TYPES.map((type) => ({
  label: type,
  value: type,
}))

const formData = reactive<RoomForm>({
  name: '',
  type: '2D',
  cinema_id: null,
})

const backendErrors = reactive<Record<string, string>>({})

const formRules: FormRules = {
  name: [{ required: true, message: 'Vui lòng nhập tên phòng', trigger: 'blur' }],
  type: [{ required: true, message: 'Vui lòng chọn loại phòng', trigger: ['blur', 'change'] }],
  cinema_id: [{ required: true, message: 'Vui lòng chọn rạp phim', trigger: ['blur', 'change'] }],
}

function clearFieldError(field: keyof RoomForm) {
  if (backendErrors[field]) {
    delete backendErrors[field]
  }
}

function syncFormWithProps() {
  if (props.room) {
    isEdit.value = true
    formData.name = props.room.name
    formData.type = props.room.type
    formData.cinema_id = props.room.cinema_id
  } else {
    isEdit.value = false
    formData.name = ''
    formData.type = '2D'
    formData.cinema_id = null
  }

  Object.keys(backendErrors).forEach((key) => delete backendErrors[key])
  nextTick(() => formRef.value?.restoreValidation())
}

async function handleSubmit() {
  try {
    await formRef.value?.validate()

    formLoading.value = true
    Object.keys(backendErrors).forEach((k) => delete backendErrors[k])

    const payload = { ...formData }

    if (isEdit.value && props.room?.id) {
      await roomService.updateRoom(props.room.id, payload as Room)
      message.success('Cập nhật phòng thành công')
    } else {
      await roomService.createRoom(payload as Room)
      message.success('Tạo phòng mới thành công')
    }

    showModal.value = false
    emit('success')
  } catch (err) {
    if (err instanceof ApiError && err.status === 422 && err.errors) {
      Object.entries(err.errors).forEach(([field, messages]) => {
        backendErrors[field] = (messages as string[])[0] ?? ''
      })
      message.error('Dữ liệu không hợp lệ, vui lòng kiểm tra lại')
    } else {
      message.error(err instanceof ApiError ? err.message : 'Đã có lỗi hệ thống xảy ra')
    }
  } finally {
    formLoading.value = false
  }
}

watch(() => props.room, syncFormWithProps, { immediate: true })
</script>

<template>
  <n-modal
    v-model:show="showModal"
    preset="dialog"
    :title="isEdit ? 'Chỉnh sửa phòng' : 'Tạo phòng mới'"
    :show-icon="false"
    style="width: 550px"
    @after-leave="syncFormWithProps"
  >
    <n-form
      ref="formRef"
      :model="formData"
      :rules="formRules"
      label-placement="top"
      require-mark-placement="right-hanging"
      class="mt-4"
    >
      <n-form-item
        label="Tên phòng"
        path="name"
        :validation-status="backendErrors.name ? 'error' : undefined"
        :feedback="backendErrors.name"
      >
        <n-input
          v-model:value="formData.name"
          placeholder="Ví dụ: Room 1, Room 2."
          @input="clearFieldError('name')"
        />
      </n-form-item>
      <n-form-item
        label="Loại phòng"
        path="type"
        :validation-status="backendErrors.type ? 'error' : undefined"
        :feedback="backendErrors.type"
      >
        <n-select
          v-model:value="formData.type"
          :options="roomTypeOptions"
          placeholder="Chọn loại phòng"
          @update:value="clearFieldError('type')"
        />
      </n-form-item>
      <n-form-item
        label="Rạp phim"
        path="cinema_id"
        :validation-status="backendErrors.cinema_id ? 'error' : undefined"
        :feedback="backendErrors.cinema_id"
      >
        <n-select
          v-model:value="formData.cinema_id"
          :options="cinemas"
          placeholder="Chọn rạp phim"
          filterable
          clearable
          @update:value="clearFieldError('cinema_id')"
        />
      </n-form-item>
    </n-form>

    <template #action>
      <n-button ghost @click="showModal = false"> Hủy bỏ </n-button>
      <n-button type="primary" :loading="formLoading" @click="handleSubmit">
        {{ isEdit ? 'Cập nhật ngay' : 'Thêm phòng' }}
      </n-button>
    </template>
  </n-modal>
</template>
