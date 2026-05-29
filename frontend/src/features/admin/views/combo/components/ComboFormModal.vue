<script setup lang="ts">
import { nextTick, reactive, ref, watch } from 'vue'
import type { FormInst, FormRules, UploadCustomRequestOptions } from 'naive-ui'
import { NAvatar, useMessage } from 'naive-ui'
import { ApiError } from '@/plugins/axios'
import { comboService } from '@/features/admin/services/combo.service'
import { uploadService } from '@/features/shared/services/upload.service'
import type { Cinema } from '@/features/admin/types/cinema.type'
import type { Combo, ComboPayload, ComboStatus } from '@/features/admin/types/combo.type'

interface ComboForm {
  name: string
  description: string
  price: number | null
  status: ComboStatus
  image: string
  cinema_id: string | null
}

const props = defineProps<{
  combo?: Combo | null
  cinemas: Cinema[]
}>()

const emit = defineEmits<{
  (e: 'success'): void
}>()

const message = useMessage()
const showModal = defineModel<boolean>('show')
const formRef = ref<FormInst | null>(null)
const formLoading = ref(false)
const uploadLoading = ref(false)
const isEdit = ref(false)
const previewUrl = ref('')

const formData = reactive<ComboForm>({
  name: '',
  description: '',
  price: null,
  status: 'active',
  image: '',
  cinema_id: null,
})

const backendErrors = reactive<Record<string, string>>({})

const statusOptions = [
  { label: 'Hoạt động', value: 'active' },
  { label: 'Không hoạt động', value: 'inactive' },
]

const formRules: FormRules = {
  name: [{ required: true, message: 'Vui lòng nhập tên combo', trigger: 'blur' }],
  price: [
    { required: true, type: 'number', message: 'Vui lòng nhập giá combo', trigger: ['blur', 'change'] },
  ],
  status: [{ required: true, message: 'Vui lòng chọn trạng thái', trigger: ['blur', 'change'] }],
  cinema_id: [{ required: true, message: 'Vui lòng chọn rạp phim', trigger: ['blur', 'change'] }],
}

function clearFieldError(field: keyof ComboForm) {
  if (backendErrors[field]) {
    delete backendErrors[field]
  }
}

function syncFormWithProps() {
  if (props.combo) {
    isEdit.value = true
    formData.name = props.combo.name
    formData.description = props.combo.description ?? ''
    formData.price = Number(props.combo.price ?? 0)
    formData.status = props.combo.status
    formData.image = props.combo.image ?? ''
    formData.cinema_id = props.combo.cinema_id
    previewUrl.value = props.combo.image ?? ''
  } else {
    isEdit.value = false
    formData.name = ''
    formData.description = ''
    formData.price = null
    formData.status = 'active'
    formData.image = ''
    formData.cinema_id = null
    previewUrl.value = ''
  }

  Object.keys(backendErrors).forEach((key) => delete backendErrors[key])
  nextTick(() => formRef.value?.restoreValidation())
}

async function handleCustomUpload({ file, onFinish, onError }: UploadCustomRequestOptions) {
  try {
    uploadLoading.value = true
    const res = await uploadService.uploadImage(file.file as File, 'combos')
    formData.image = res.data.url
    previewUrl.value = formData.image
    onFinish()
    message.success('Tải ảnh lên thành công')
  } catch {
    onError()
    message.error('Tải ảnh lên thất bại')
  } finally {
    uploadLoading.value = false
  }
}

async function handleRemoveImage() {
  if (!formData.image) return
  try {
    await uploadService.deleteFile(formData.image)
  } catch {
  } finally {
    formData.image = ''
    previewUrl.value = ''
  }
}

function buildPayload(): ComboPayload {
  return {
    name: formData.name.trim(),
    description: formData.description.trim(),
    price: Number(formData.price ?? 0),
    status: formData.status,
    image: formData.image,
    cinema_id: formData.cinema_id ?? '',
  }
}

async function handleSubmit() {
  try {
    await formRef.value?.validate()

    formLoading.value = true
    Object.keys(backendErrors).forEach((key) => delete backendErrors[key])

    const payload = buildPayload()

    if (isEdit.value && props.combo?.id) {
      await comboService.updateCombo(props.combo.id, payload)
      message.success('Cập nhật combo thành công')
    } else {
      await comboService.createCombo(payload)
      message.success('Tạo combo mới thành công')
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

watch(() => props.combo, syncFormWithProps, { immediate: true })
</script>

<template>
  <n-modal
    v-model:show="showModal"
    preset="dialog"
    :title="isEdit ? 'Chỉnh sửa combo' : 'Tạo combo mới'"
    :show-icon="false"
    style="width: 680px"
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
      <n-form-item label="Ảnh combo" path="image">
        <div class="flex items-center gap-4">
          <n-avatar :src="previewUrl || undefined" :size="86" object-fit="cover" style="border-radius: 8px">
            <template v-if="!previewUrl" #default>
              <span class="text-xs text-gray-400">Combo</span>
            </template>
          </n-avatar>

          <div class="flex flex-col gap-2">
            <n-upload
              accept="image/*"
              :max="1"
              :show-file-list="false"
              :custom-request="handleCustomUpload"
            >
              <n-button size="small" :loading="uploadLoading">
                {{ previewUrl ? 'Đổi ảnh' : 'Tải ảnh lên' }}
              </n-button>
            </n-upload>

            <n-button v-if="previewUrl" size="small" type="error" ghost @click="handleRemoveImage">
              Xóa ảnh
            </n-button>
          </div>
        </div>
      </n-form-item>

      <n-grid :cols="2" :x-gap="16">
        <n-form-item-gi
          label="Tên combo"
          path="name"
          :validation-status="backendErrors.name ? 'error' : undefined"
          :feedback="backendErrors.name"
        >
          <n-input
            v-model:value="formData.name"
            placeholder="Ví dụ: Combo bắp nước đôi"
            @input="clearFieldError('name')"
          />
        </n-form-item-gi>

        <n-form-item-gi
          label="Rạp phim"
          path="cinema_id"
          :validation-status="backendErrors.cinema_id ? 'error' : undefined"
          :feedback="backendErrors.cinema_id"
        >
          <n-select
            v-model:value="formData.cinema_id"
            :options="cinemas.map((cinema) => ({ label: cinema.name, value: cinema.id }))"
            placeholder="Chọn rạp phim"
            filterable
            @update:value="clearFieldError('cinema_id')"
          />
        </n-form-item-gi>

        <n-form-item-gi
          label="Giá"
          path="price"
          :validation-status="backendErrors.price ? 'error' : undefined"
          :feedback="backendErrors.price"
        >
          <n-input-number
            v-model:value="formData.price"
            placeholder="Ví dụ: 89000"
            :min="0"
            :step="1000"
            style="width: 100%"
            @update:value="clearFieldError('price')"
          />
        </n-form-item-gi>

        <n-form-item-gi
          label="Trạng thái"
          path="status"
          :validation-status="backendErrors.status ? 'error' : undefined"
          :feedback="backendErrors.status"
        >
          <n-select
            v-model:value="formData.status"
            :options="statusOptions"
            placeholder="Chọn trạng thái"
            @update:value="clearFieldError('status')"
          />
        </n-form-item-gi>
      </n-grid>

      <n-form-item
        label="Mô tả"
        path="description"
        :validation-status="backendErrors.description ? 'error' : undefined"
        :feedback="backendErrors.description"
      >
        <n-input
          v-model:value="formData.description"
          type="textarea"
          placeholder="Nhập mô tả combo..."
          :autosize="{ minRows: 3, maxRows: 5 }"
          @input="clearFieldError('description')"
        />
      </n-form-item>
    </n-form>

    <template #action>
      <n-button ghost @click="showModal = false">Hủy bỏ</n-button>
      <n-button type="primary" :loading="formLoading" @click="handleSubmit">
        {{ isEdit ? 'Cập nhật ngay' : 'Thêm combo' }}
      </n-button>
    </template>
  </n-modal>
</template>
