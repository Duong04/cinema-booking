<script setup lang="ts">
import { nextTick, reactive, ref, watch } from 'vue'
import type { FormInst, FormRules } from 'naive-ui'
import { useMessage } from 'naive-ui'
import { ApiError } from '@/plugins/axios'
import { promotionService } from '@/features/admin/services/promotion.service'
import type {
  Promotion,
  PromotionApplicableTo,
  PromotionDiscountType,
  PromotionPayload,
  PromotionStatus,
} from '@/features/admin/types/promotion.type'

interface PromotionForm {
  code: string
  description: string
  discount_type: PromotionDiscountType
  discount_value: number | null
  start_date: string | null
  end_date: string | null
  usage_limit: number | null
  per_user_limit: number | null
  applicable_to: PromotionApplicableTo
  status: PromotionStatus
}

const props = defineProps<{
  promotion?: Promotion | null
}>()

const emit = defineEmits<{
  (e: 'success'): void
}>()

const message = useMessage()
const showModal = defineModel<boolean>('show')
const formRef = ref<FormInst | null>(null)
const formLoading = ref(false)
const isEdit = ref(false)

const formData = reactive<PromotionForm>({
  code: '',
  description: '',
  discount_type: 'percentage',
  discount_value: null,
  start_date: null,
  end_date: null,
  usage_limit: null,
  per_user_limit: null,
  applicable_to: 'booking',
  status: 'active',
})

const backendErrors = reactive<Record<string, string>>({})

const discountTypeOptions = [
  { label: 'Phần trăm', value: 'percentage' },
  { label: 'Số tiền cố định', value: 'fixed_amount' },
]

const applicableOptions = [
  { label: 'Toàn bộ booking', value: 'booking' },
  { label: 'Chỉ tiền vé', value: 'ticket' },
  { label: 'Chỉ combo', value: 'combo' },
]

const statusOptions = [
  { label: 'Hoạt động', value: 'active' },
  { label: 'Tạm dừng', value: 'paused' },
  { label: 'Hết hạn', value: 'expired' },
]

const formRules: FormRules = {
  code: [{ required: true, message: 'Vui lòng nhập mã khuyến mãi', trigger: 'blur' }],
  discount_type: [{ required: true, message: 'Vui lòng chọn loại giảm giá', trigger: 'change' }],
  discount_value: [
    { required: true, type: 'number', message: 'Vui lòng nhập giá trị giảm', trigger: ['blur', 'change'] },
  ],
  start_date: [{ required: true, message: 'Vui lòng chọn ngày bắt đầu', trigger: 'change' }],
  end_date: [{ required: true, message: 'Vui lòng chọn ngày kết thúc', trigger: 'change' }],
  applicable_to: [{ required: true, message: 'Vui lòng chọn phạm vi áp dụng', trigger: 'change' }],
  status: [{ required: true, message: 'Vui lòng chọn trạng thái', trigger: 'change' }],
}

function clearFieldError(field: keyof PromotionForm) {
  if (backendErrors[field]) delete backendErrors[field]
}

function normalizeDateTime(value?: string | null) {
  if (!value) return null
  return value.replace('T', ' ').slice(0, 19)
}

function syncFormWithProps() {
  if (props.promotion) {
    isEdit.value = true
    formData.code = props.promotion.code
    formData.description = props.promotion.description ?? ''
    formData.discount_type = props.promotion.discount_type
    formData.discount_value = Number(props.promotion.discount_value ?? 0)
    formData.start_date = normalizeDateTime(props.promotion.start_date)
    formData.end_date = normalizeDateTime(props.promotion.end_date)
    formData.usage_limit = props.promotion.usage_limit ?? null
    formData.per_user_limit = props.promotion.per_user_limit ?? null
    formData.applicable_to = props.promotion.applicable_to
    formData.status = props.promotion.status
  } else {
    isEdit.value = false
    formData.code = ''
    formData.description = ''
    formData.discount_type = 'percentage'
    formData.discount_value = null
    formData.start_date = null
    formData.end_date = null
    formData.usage_limit = null
    formData.per_user_limit = null
    formData.applicable_to = 'booking'
    formData.status = 'active'
  }

  Object.keys(backendErrors).forEach((key) => delete backendErrors[key])
  nextTick(() => formRef.value?.restoreValidation())
}

function buildPayload(): PromotionPayload {
  return {
    code: formData.code.trim().toUpperCase(),
    description: formData.description.trim() || null,
    discount_type: formData.discount_type,
    discount_value: Number(formData.discount_value ?? 0),
    start_date: formData.start_date ?? '',
    end_date: formData.end_date ?? '',
    usage_limit: formData.usage_limit,
    per_user_limit: formData.per_user_limit,
    applicable_to: formData.applicable_to,
    status: formData.status,
  }
}

async function handleSubmit() {
  try {
    await formRef.value?.validate()

    formLoading.value = true
    Object.keys(backendErrors).forEach((key) => delete backendErrors[key])

    const payload = buildPayload()

    if (isEdit.value && props.promotion?.id) {
      await promotionService.updatePromotion(props.promotion.id, payload)
      message.success('Cập nhật mã khuyến mãi thành công')
    } else {
      await promotionService.createPromotion(payload)
      message.success('Tạo mã khuyến mãi thành công')
    }

    showModal.value = false
    emit('success')
  } catch (err) {
    if (err instanceof ApiError && err.status === 422 && err.errors) {
      Object.entries(err.errors).forEach(([field, messages]) => {
        backendErrors[field] = messages[0] ?? ''
      })
      message.error('Dữ liệu không hợp lệ, vui lòng kiểm tra lại')
    } else {
      message.error(err instanceof ApiError ? err.message : 'Đã có lỗi hệ thống xảy ra')
    }
  } finally {
    formLoading.value = false
  }
}

watch(() => props.promotion, syncFormWithProps, { immediate: true })
</script>

<template>
  <n-modal
    v-model:show="showModal"
    preset="dialog"
    :title="isEdit ? 'Chỉnh sửa mã khuyến mãi' : 'Tạo mã khuyến mãi'"
    :show-icon="false"
    style="width: 720px"
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
      <n-grid :cols="2" :x-gap="16">
        <n-form-item-gi
          label="Mã khuyến mãi"
          path="code"
          :validation-status="backendErrors.code ? 'error' : undefined"
          :feedback="backendErrors.code"
        >
          <n-input
            v-model:value="formData.code"
            placeholder="Ví dụ: SALE10"
            @input="clearFieldError('code')"
          />
        </n-form-item-gi>

        <n-form-item-gi
          label="Phạm vi áp dụng"
          path="applicable_to"
          :validation-status="backendErrors.applicable_to ? 'error' : undefined"
          :feedback="backendErrors.applicable_to"
        >
          <n-select
            v-model:value="formData.applicable_to"
            :options="applicableOptions"
            placeholder="Chọn phạm vi"
            @update:value="clearFieldError('applicable_to')"
          />
        </n-form-item-gi>

        <n-form-item-gi
          label="Loại giảm giá"
          path="discount_type"
          :validation-status="backendErrors.discount_type ? 'error' : undefined"
          :feedback="backendErrors.discount_type"
        >
          <n-select
            v-model:value="formData.discount_type"
            :options="discountTypeOptions"
            placeholder="Chọn loại giảm"
            @update:value="clearFieldError('discount_type')"
          />
        </n-form-item-gi>

        <n-form-item-gi
          label="Giá trị giảm"
          path="discount_value"
          :validation-status="backendErrors.discount_value ? 'error' : undefined"
          :feedback="backendErrors.discount_value"
        >
          <n-input-number
            v-model:value="formData.discount_value"
            :min="0"
            :max="formData.discount_type === 'percentage' ? 100 : undefined"
            :step="formData.discount_type === 'percentage' ? 1 : 1000"
            placeholder="Nhập giá trị"
            style="width: 100%"
            @update:value="clearFieldError('discount_value')"
          />
        </n-form-item-gi>

        <n-form-item-gi
          label="Ngày bắt đầu"
          path="start_date"
          :validation-status="backendErrors.start_date ? 'error' : undefined"
          :feedback="backendErrors.start_date"
        >
          <n-date-picker
            v-model:formatted-value="formData.start_date"
            type="datetime"
            value-format="yyyy-MM-dd HH:mm:ss"
            placeholder="Chọn ngày bắt đầu"
            style="width: 100%"
            @update:formatted-value="clearFieldError('start_date')"
          />
        </n-form-item-gi>

        <n-form-item-gi
          label="Ngày kết thúc"
          path="end_date"
          :validation-status="backendErrors.end_date ? 'error' : undefined"
          :feedback="backendErrors.end_date"
        >
          <n-date-picker
            v-model:formatted-value="formData.end_date"
            type="datetime"
            value-format="yyyy-MM-dd HH:mm:ss"
            placeholder="Chọn ngày kết thúc"
            style="width: 100%"
            @update:formatted-value="clearFieldError('end_date')"
          />
        </n-form-item-gi>

        <n-form-item-gi
          label="Tổng lượt dùng"
          path="usage_limit"
          :validation-status="backendErrors.usage_limit ? 'error' : undefined"
          :feedback="backendErrors.usage_limit"
        >
          <n-input-number
            v-model:value="formData.usage_limit"
            :min="1"
            clearable
            placeholder="Không giới hạn"
            style="width: 100%"
            @update:value="clearFieldError('usage_limit')"
          />
        </n-form-item-gi>

        <n-form-item-gi
          label="Lượt dùng mỗi user"
          path="per_user_limit"
          :validation-status="backendErrors.per_user_limit ? 'error' : undefined"
          :feedback="backendErrors.per_user_limit"
        >
          <n-input-number
            v-model:value="formData.per_user_limit"
            :min="1"
            clearable
            placeholder="Không giới hạn"
            style="width: 100%"
            @update:value="clearFieldError('per_user_limit')"
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
          placeholder="Nhập mô tả mã khuyến mãi..."
          :autosize="{ minRows: 3, maxRows: 5 }"
          @input="clearFieldError('description')"
        />
      </n-form-item>
    </n-form>

    <template #action>
      <n-button ghost @click="showModal = false">Hủy bỏ</n-button>
      <n-button type="primary" :loading="formLoading" @click="handleSubmit">
        {{ isEdit ? 'Cập nhật ngay' : 'Thêm mã' }}
      </n-button>
    </template>
  </n-modal>
</template>
