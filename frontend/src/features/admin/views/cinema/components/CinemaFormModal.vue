<script setup lang="ts">
import { ref, reactive, watch, nextTick, onMounted } from 'vue'
import type { FormInst, FormRules, SelectOption } from 'naive-ui'
import { useMessage } from 'naive-ui'
import { cinemaService } from '@/features/admin/services/cinema.service'
import { cityService } from '@/features/admin/services/city.service'
import { cinemaChainService } from '@/features/admin/services/cinema-chain.service'
import { ApiError } from '@/plugins/axios'
import type { Cinema } from '@/features/admin/types/cinema.type'

interface CinemaForm {
  name: string
  address: string
  city_id: string
  cinema_chain_id: string
}

const props = defineProps<{
  cinema?: Cinema | null
}>()

const emit = defineEmits<{
  (e: 'success'): void
}>()

const message = useMessage()
const showModal = defineModel<boolean>('show')
const formRef = ref<FormInst | null>(null)
const formLoading = ref(false)
const isEdit = ref(false)

const cityOptions = ref<SelectOption[]>([])
const cinemaChainOptions = ref<SelectOption[]>([])

const formData = reactive<CinemaForm>({
  name: '',
  address: '',
  city_id: '',
  cinema_chain_id: '',
})

const backendErrors = reactive<Record<string, string>>({})

const formRules: FormRules = {
  name: [{ required: true, message: 'Vui lòng nhập tên rạp phim', trigger: 'blur' }],
  address: [{ required: true, message: 'Vui lòng nhập địa chỉ rạp phim', trigger: 'blur' }],
  city_id: [{ required: true, message: 'Vui lòng chọn thành phố', trigger: ['blur', 'change'] }],
  cinema_chain_id: [
    { required: true, message: 'Vui lòng chọn chuỗi rạp phim', trigger: ['blur', 'change'] },
  ],
}

function clearFieldError(field: keyof CinemaForm) {
  if (backendErrors[field]) {
    delete backendErrors[field]
  }
}

async function fetchOptions() {
  const [citiesRes, chainsRes] = await Promise.all([
    cityService.getAllCities({ limit: 100 }),
    cinemaChainService.getAllCinemaChains({ limit: 100 }),
  ])
  cityOptions.value = citiesRes.data.map((c) => ({ label: c.name, value: c.id }))
  cinemaChainOptions.value = chainsRes.data.map((c) => ({ label: c.name, value: c.id }))
}

function syncFormWithProps() {
  if (props.cinema) {
    isEdit.value = true
    formData.name = props.cinema.name
    formData.address = props.cinema.address
    formData.city_id = props.cinema.city_id
    formData.cinema_chain_id = props.cinema.cinema_chain_id
  } else {
    isEdit.value = false
    formData.name = ''
    formData.address = ''
    formData.city_id = ''
    formData.cinema_chain_id = ''
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

    if (isEdit.value && props.cinema?.id) {
      await cinemaService.updateCinema(props.cinema.id, payload as Cinema)
      message.success('Cập nhật rạp phim thành công')
    } else {
      await cinemaService.createCinema(payload as Cinema)
      message.success('Tạo rạp phim mới thành công')
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

onMounted(fetchOptions)
watch(() => props.cinema, syncFormWithProps, { immediate: true })
</script>

<template>
  <n-modal
    v-model:show="showModal"
    preset="dialog"
    :title="isEdit ? 'Chỉnh sửa rạp phim' : 'Tạo rạp phim mới'"
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
        label="Tên rạp phim"
        path="name"
        :validation-status="backendErrors.name ? 'error' : undefined"
        :feedback="backendErrors.name"
      >
        <n-input
          v-model:value="formData.name"
          placeholder="Ví dụ: CGV Vincom, Lotte Cinema..."
          @input="clearFieldError('name')"
        />
      </n-form-item>

      <n-form-item
        label="Địa chỉ"
        path="address"
        :validation-status="backendErrors.address ? 'error' : undefined"
        :feedback="backendErrors.address"
      >
        <n-input
          v-model:value="formData.address"
          placeholder="Ví dụ: 123 Nguyễn Huệ, Quận 1..."
          type="textarea"
          :rows="2"
          @input="clearFieldError('address')"
        />
      </n-form-item>

      <n-form-item
        label="Thành phố"
        path="city_id"
        :validation-status="backendErrors.city_id ? 'error' : undefined"
        :feedback="backendErrors.city_id"
      >
        <n-select
          v-model:value="formData.city_id"
          :options="cityOptions"
          placeholder="Chọn thành phố"
          filterable
          @update:value="clearFieldError('city_id')"
        />
      </n-form-item>

      <n-form-item
        label="Chuỗi rạp phim"
        path="cinema_chain_id"
        :validation-status="backendErrors.cinema_chain_id ? 'error' : undefined"
        :feedback="backendErrors.cinema_chain_id"
      >
        <n-select
          v-model:value="formData.cinema_chain_id"
          :options="cinemaChainOptions"
          placeholder="Chọn chuỗi rạp"
          filterable
          @update:value="clearFieldError('cinema_chain_id')"
        />
      </n-form-item>
    </n-form>

    <template #action>
      <n-button ghost @click="showModal = false">Hủy bỏ</n-button>
      <n-button type="primary" :loading="formLoading" @click="handleSubmit">
        {{ isEdit ? 'Cập nhật ngay' : 'Thêm rạp phim' }}
      </n-button>
    </template>
  </n-modal>
</template>
