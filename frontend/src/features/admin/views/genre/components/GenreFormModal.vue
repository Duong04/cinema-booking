<script setup lang="ts">
import { ref, reactive, watch, nextTick } from 'vue'
import type { FormInst, FormRules } from 'naive-ui'
import { useMessage } from 'naive-ui'
import { genreService } from '@/features/admin/services/genre.service'
import { ApiError } from '@/plugins/axios'
import type { Genre } from '@/features/admin/types/genre.type'

interface GenreForm {
  name: string
}

const props = defineProps<{
  genre?: Genre | null
}>()

const emit = defineEmits<{
  (e: 'success'): void
}>()

const message = useMessage()
const showModal = defineModel<boolean>('show')
const formRef = ref<FormInst | null>(null)
const formLoading = ref(false)
const isEdit = ref(false)

const formData = reactive<GenreForm>({
  name: ''
})

const backendErrors = reactive<Record<string, string>>({})

const formRules: FormRules = {
  name: [
    { required: true, message: 'Vui lòng nhập tên thể loại', trigger: 'blur' }
  ]
}

function clearFieldError(field: keyof GenreForm) {
  if (backendErrors[field]) {
    delete backendErrors[field]
  }
}

function syncFormWithProps() {
  if (props.genre) {
    isEdit.value = true
    formData.name = props.genre.name
  } else {
    isEdit.value = false
    formData.name = ''
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

    if (isEdit.value && props.genre?.id) {
      await genreService.updateGenre(props.genre.id, payload as Genre)
      message.success('Cập nhật thể loại thành công')
    } else {
      await genreService.createGenre(payload as Genre)
      message.success('Tạo thể loại mới thành công')
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

watch(() => props.genre, syncFormWithProps, { immediate: true })
</script>

<template>
  <n-modal
    v-model:show="showModal"
    preset="dialog"
    :title="isEdit ? 'Chỉnh sửa thể loại' : 'Tạo thể loại mới'"
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
        label="Tên thể loại"
        path="name"
        :validation-status="backendErrors.name ? 'error' : undefined"
        :feedback="backendErrors.name"
      >
        <n-input
          v-model:value="formData.name"
          placeholder="Ví dụ: Tình cảm, Hành động, Kinh dị..."
          @input="clearFieldError('name')"
        />
      </n-form-item>
    </n-form>

    <template #action>
      <n-button ghost @click="showModal = false">
        Hủy bỏ
      </n-button>
      <n-button 
        type="primary" 
        :loading="formLoading" 
        @click="handleSubmit"
      >
        {{ isEdit ? 'Cập nhật ngay' : 'Thêm thể loại' }}
      </n-button>
    </template>
  </n-modal>
</template>