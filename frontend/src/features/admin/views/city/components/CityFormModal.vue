<script setup lang="ts">
import { ref, reactive, watch, nextTick } from 'vue'
import type { FormInst, FormRules } from 'naive-ui'
import { useMessage } from 'naive-ui'
import { cityService } from '@/features/admin/services/city.service'
import { ApiError } from '@/plugins/axios'
import type { City } from '@/features/admin/types/city.type'

interface CityForm {
  name: string
  latitude: number | null
  longitude: number | null
}

const props = defineProps<{
  city?: City | null
}>()

const emit = defineEmits<{
  (e: 'success'): void
}>()

const message = useMessage()
const showModal = defineModel<boolean>('show')
const formRef = ref<FormInst | null>(null)
const formLoading = ref(false)
const isEdit = ref(false)

const formData = reactive<CityForm>({
  name: '',
  latitude: null,
  longitude: null,
})

const backendErrors = reactive<Record<string, string>>({})

const formRules: FormRules = {
  name: [
    { required: true, message: 'Vui lòng nhập tên thành phố', trigger: 'blur' }
  ]
}

function clearFieldError(field: keyof CityForm) {
  if (backendErrors[field]) {
    delete backendErrors[field]
  }
}

function syncFormWithProps() {
  if (props.city) {
    isEdit.value = true
    formData.name = props.city.name
    formData.latitude = props.city.latitude == null ? null : Number(props.city.latitude)
    formData.longitude = props.city.longitude == null ? null : Number(props.city.longitude)
  } else {
    isEdit.value = false
    formData.name = ''
    formData.latitude = null
    formData.longitude = null
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

    if (isEdit.value && props.city?.id) {
      await cityService.updateCity(props.city.id, payload as City)
      message.success('Cập nhật thành phố thành công')
    } else {
      await cityService.createCity(payload as City)
      message.success('Tạo thành phố mới thành công')
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

watch(() => props.city, syncFormWithProps, { immediate: true })
</script>

<template>
  <n-modal
    v-model:show="showModal"
    preset="dialog"
    :title="isEdit ? 'Chỉnh sửa thành phố' : 'Tạo thành phố mới'"
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
        label="Tên thành phố"
        path="name"
        :validation-status="backendErrors.name ? 'error' : undefined"
        :feedback="backendErrors.name"
      >
        <n-input
          v-model:value="formData.name"
          placeholder="Ví dụ: Da Nang, Ha Noi..."
          @input="clearFieldError('name')"
        />
      </n-form-item>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <n-form-item
          label="Vĩ độ"
          path="latitude"
          :validation-status="backendErrors.latitude ? 'error' : undefined"
          :feedback="backendErrors.latitude"
        >
          <n-input-number
            v-model:value="formData.latitude"
            clearable
            :min="-90"
            :max="90"
            :precision="7"
            placeholder="Ví dụ: 10.7769"
            class="w-full"
            @update:value="clearFieldError('latitude')"
          />
        </n-form-item>

        <n-form-item
          label="Kinh độ"
          path="longitude"
          :validation-status="backendErrors.longitude ? 'error' : undefined"
          :feedback="backendErrors.longitude"
        >
          <n-input-number
            v-model:value="formData.longitude"
            clearable
            :min="-180"
            :max="180"
            :precision="7"
            placeholder="Ví dụ: 106.7009"
            class="w-full"
            @update:value="clearFieldError('longitude')"
          />
        </n-form-item>
      </div>
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
        {{ isEdit ? 'Cập nhật ngay' : 'Thêm thành phố' }}
      </n-button>
    </template>
  </n-modal>
</template>
