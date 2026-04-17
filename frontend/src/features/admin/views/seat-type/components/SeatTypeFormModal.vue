<script setup lang="ts">
import { ref, reactive, watch, nextTick } from 'vue'
import type { FormInst, FormRules } from 'naive-ui'
import { useMessage } from 'naive-ui'
import { seatTypeService } from '@/features/admin/services/seat-type.service'
import { ApiError } from '@/plugins/axios'
import type { SeatType } from '@/features/admin/types/seat-type.type'

interface SeatTypeForm {
  name: string
  base_multiplier: number
}

const props = defineProps<{
  seatType?: SeatType | null
}>()

const emit = defineEmits<{
  (e: 'success'): void
}>()

const message = useMessage()
const showModal = defineModel<boolean>('show')
const formRef = ref<FormInst | null>(null)
const formLoading = ref(false)
const isEdit = ref(false)

const formData = reactive<SeatTypeForm>({
  name: '',
  base_multiplier: 0,
})

const backendErrors = reactive<Record<string, string>>({})

const formRules: FormRules = {
  name: [{ required: true, message: 'Vui lòng nhập tên loại ghế', trigger: 'blur' }],
  base_multiplier: [
    { required: true, message: 'Vui lòng nhập số', trigger: 'blur' }
  ],
}

function clearFieldError(field: keyof SeatTypeForm) {
  if (backendErrors[field]) {
    delete backendErrors[field]
  }
}

function syncFormWithProps() {
  if (props.seatType) {
    isEdit.value = true
    formData.name = props.seatType.name
    formData.base_multiplier = props.seatType.base_multiplier ?? 0
  } else {
    isEdit.value = false
    formData.name = ''
    formData.base_multiplier = 0
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

    if (isEdit.value && props.seatType?.id) {
      await seatTypeService.updateSeatType(props.seatType.id, payload as SeatType)
      message.success('Cập nhật loại ghế thành công')
    } else {
      await seatTypeService.createSeatType(payload as SeatType)
      message.success('Tạo loại ghế mới thành công')
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

watch(() => props.seatType, syncFormWithProps, { immediate: true })
</script>

<template>
  <n-modal
    v-model:show="showModal"
    preset="dialog"
    :title="isEdit ? 'Chỉnh sửa loại ghế' : 'Tạo loại ghế mới'"
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
      <!-- Tên -->
      <n-form-item
        label="Tên loại ghế"
        path="name"
        :validation-status="backendErrors.name ? 'error' : undefined"
        :feedback="backendErrors.name"
      >
        <n-input
          v-model:value="formData.name"
          placeholder="Ví dụ: CGV, Lotte Cinema..."
          @input="clearFieldError('name')"
        />
      </n-form-item>
      <n-form-item
        label="Hệ số nhân"
        path="base_multiplier"
        :validation-status="backendErrors.base_multiplier ? 'error' : undefined"
        :feedback="backendErrors.base_multiplier"
      >
        <n-input
          v-model:value="formData.base_multiplier"
          placeholder="Ví dụ: 1.5, 2.5"
          @input="clearFieldError('base_multiplier')"
        />
      </n-form-item>
    </n-form>

    <template #action>
      <n-button ghost @click="showModal = false">Hủy bỏ</n-button>
      <n-button type="primary" :loading="formLoading" @click="handleSubmit">
        {{ isEdit ? 'Cập nhật ngay' : 'Thêm loại ghế' }}
      </n-button>
    </template>
  </n-modal>
</template>
