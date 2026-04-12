<script setup lang="ts">
import { ref, reactive, watch, nextTick } from 'vue'
import type { FormInst, FormRules, UploadCustomRequestOptions } from 'naive-ui'
import { useMessage, NAvatar } from 'naive-ui'
import { cinemaChainService } from '@/features/admin/services/cinema-chain.service'
import { uploadService } from '@/features/shared/services/upload.service'
import { ApiError } from '@/plugins/axios'
import type { CinemaChain } from '@/features/admin/types/cinema-chain.type'

interface CinemaChainForm {
  name: string
  logo?: string
}

const props = defineProps<{
  cinemaChain?: CinemaChain | null
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
const previewUrl = ref<string>('')

const formData = reactive<CinemaChainForm>({
  name: '',
  logo: ''
})

const backendErrors = reactive<Record<string, string>>({})

const formRules: FormRules = {
  name: [
    { required: true, message: 'Vui lòng nhập tên chuỗi rạp', trigger: 'blur' }
  ]
}

function clearFieldError(field: keyof CinemaChainForm) {
  if (backendErrors[field]) {
    delete backendErrors[field]
  }
}

function syncFormWithProps() {
  if (props.cinemaChain) {
    isEdit.value = true
    formData.name = props.cinemaChain.name
    formData.logo = props.cinemaChain.logo ?? ''
    previewUrl.value = props.cinemaChain.logo ?? ''
  } else {
    isEdit.value = false
    formData.name = ''
    formData.logo = ''
    previewUrl.value = ''
  }

  Object.keys(backendErrors).forEach((key) => delete backendErrors[key])
  nextTick(() => formRef.value?.restoreValidation())
}

async function handleCustomUpload({ file, onFinish, onError }: UploadCustomRequestOptions) {
  try {
    uploadLoading.value = true
    const res = await uploadService.uploadImage(file.file as File, 'cinema-chains')
    formData.logo = res.data.url 
    previewUrl.value = formData.logo
    onFinish()
    message.success('Tải ảnh lên thành công')
  } catch {
    onError()
    message.error('Tải ảnh lên thất bại')
  } finally {
    uploadLoading.value = false
  }
}

async function handleRemoveLogo() {
  if (!formData.logo) return
  try {
    await uploadService.deleteFile(formData.logo)
  } catch {
  } finally {
    formData.logo = ''
    previewUrl.value = ''
  }
}

async function handleSubmit() {
  try {
    await formRef.value?.validate()

    formLoading.value = true
    Object.keys(backendErrors).forEach((k) => delete backendErrors[k])

    const payload = { ...formData }

    if (isEdit.value && props.cinemaChain?.id) {
      await cinemaChainService.updateCinemaChain(props.cinemaChain.id, payload as CinemaChain)
      message.success('Cập nhật chuỗi rạp thành công')
    } else {
      await cinemaChainService.createCinemaChain(payload as CinemaChain)
      message.success('Tạo chuỗi rạp mới thành công')
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

watch(() => props.cinemaChain, syncFormWithProps, { immediate: true })
</script>

<template>
  <n-modal
    v-model:show="showModal"
    preset="dialog"
    :title="isEdit ? 'Chỉnh sửa chuỗi rạp' : 'Tạo chuỗi rạp mới'"
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
      <!-- Logo -->
      <n-form-item label="Logo" path="logo">
        <div class="flex items-center gap-4">
          <!-- Preview -->
          <n-avatar
            :src="previewUrl || undefined"
            round
            :size="64"
            object-fit="cover"
          >
            <template v-if="!previewUrl" #default>
              <span class="text-xs text-gray-400">Logo</span>
            </template>
          </n-avatar>

          <!-- Actions -->
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

            <n-button
              v-if="previewUrl"
              size="small"
              type="error"
              ghost
              @click="handleRemoveLogo"
            >
              Xóa ảnh
            </n-button>
          </div>
        </div>
      </n-form-item>

      <!-- Tên -->
      <n-form-item
        label="Tên chuỗi rạp"
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
    </n-form>

    <template #action>
      <n-button ghost @click="showModal = false">Hủy bỏ</n-button>
      <n-button type="primary" :loading="formLoading" @click="handleSubmit">
        {{ isEdit ? 'Cập nhật ngay' : 'Thêm chuỗi rạp' }}
      </n-button>
    </template>
  </n-modal>
</template>