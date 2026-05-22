<script setup lang="ts">
import { ref, reactive, computed, watch, nextTick } from 'vue'
import type { FormInst, FormRules, SelectOption } from 'naive-ui'
import { useMessage } from 'naive-ui'
import { showtimeService } from '@/features/admin/services/showtime.service'
import { seatTypeService } from '@/features/admin/services/seat-type.service'
import { ApiError } from '@/plugins/axios'
import type { Showtime, Prices } from '@/features/admin/types/showtime.type'
import type { SeatType } from '@/features/admin/types/seat-type.type'

interface PriceRow {
  seat_type_id: string
  price: number | null
}

interface ShowtimeForm {
  movie_id: string | null
  room_id: string | null
  show_date: string | null
  start_time: string | null
  base_price: number | null
  prices: PriceRow[]
}

const props = defineProps<{
  showtime?: Showtime | null
  movies: SelectOption[]
  rooms: SelectOption[]
}>()

const emit = defineEmits<{
  (e: 'success'): void
}>()

const message = useMessage()
const showModal = defineModel<boolean>('show')
const formRef = ref<FormInst | null>(null)
const formLoading = ref(false)
const isEdit = ref(false)

const seatTypes = ref<SeatType[]>([])
const loadingOptions = ref(false)

const formData = reactive<ShowtimeForm>({
  movie_id: null,
  room_id: null,
  show_date: null,
  start_time: null,
  base_price: null,
  prices: [],
})

const backendErrors = reactive<Record<string, string>>({})

const seatTypeOptions = computed(() =>
  seatTypes.value.map((st) => ({ label: st.name, value: st.id })),
)

const usedSeatTypeIds = computed(() => formData.prices.map((p) => p.seat_type_id).filter(Boolean))
const canAddPriceRow = computed(
  () => !loadingOptions.value && seatTypes.value.length > 0 && formData.prices.length < seatTypes.value.length,
)

function getSeatTypeOptionsForRow(index: number) {
  const currentId = formData.prices[index]?.seat_type_id
  return seatTypeOptions.value.filter(
    (opt) => opt.value === currentId || !usedSeatTypeIds.value.includes(opt.value),
  )
}

const formRules: FormRules = {
  movie_id: [{ required: true, message: 'Vui lòng chọn phim', trigger: 'change' }],
  room_id: [{ required: true, message: 'Vui lòng chọn phòng chiếu', trigger: 'change' }],
  show_date: [{ required: true, message: 'Vui lòng chọn ngày chiếu', trigger: 'change' }],
  start_time: [{ required: true, message: 'Vui lòng chọn giờ bắt đầu', trigger: 'change' }],
  base_price: [
    { required: true, type: 'number', message: 'Vui lòng nhập giá cơ bản', trigger: 'blur' },
  ],
}

function clearFieldError(field: string) {
  if (backendErrors[field]) delete backendErrors[field]
}

function addPriceRow() {
  if (!canAddPriceRow.value) return
  formData.prices.push({ seat_type_id: '', price: null })
}

function removePriceRow(index: number) {
  formData.prices.splice(index, 1)
}

function buildPayload(): Partial<Showtime> {
  return {
    movie_id: formData.movie_id!,
    room_id: formData.room_id!,
    show_date: formData.show_date!,
    start_time: `${formData.show_date} ${formData.start_time}:00`,
    base_price: formData.base_price ?? 0,
    prices: formData.prices
      .filter((p) => p.seat_type_id && p.price !== null)
      .map((p) => ({ seat_type_id: p.seat_type_id, price: p.price as number })) as Prices[],
  }
}

function validatePrices(): boolean {
  for (let i = 0; i < formData.prices.length; i++) {
    const row = formData.prices[i]
    if (!row) continue

    if (!row.seat_type_id) {
      message.warning(`Dòng giá ${i + 1}: Vui lòng chọn loại ghế`)
      return false
    }
    if (row.price === null || row.price < 0) {
      message.warning(`Dòng giá ${i + 1}: Vui lòng nhập giá hợp lệ`)
      return false
    }
  }
  return true
}

async function handleSubmit() {
  try {
    await formRef.value?.validate()
    if (!validatePrices()) return

    formLoading.value = true
    Object.keys(backendErrors).forEach((k) => delete backendErrors[k])

    const payload = buildPayload()

    if (isEdit.value && props.showtime?.id) {
      await showtimeService.updateShowtime(props.showtime.id, payload as Showtime)
      message.success('Cập nhật suất chiếu thành công')
    } else {
      await showtimeService.createShowtime(payload as Showtime)
      message.success('Tạo suất chiếu mới thành công')
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

function parseTime(datetimeStr?: string | null): string | null {
  if (!datetimeStr) return null
  const timePart = datetimeStr.split(' ')[1]
  return timePart ? timePart.slice(0, 5) : null
}

function resetForm() {
  formData.movie_id = null
  formData.room_id = null
  formData.show_date = null
  formData.start_time = null
  formData.base_price = null
  formData.prices = []
}

async function fetchSeatTypes() {
  if (seatTypes.value.length > 0 || loadingOptions.value) return

  loadingOptions.value = true
  try {
    const res = await seatTypeService.getAllSeatTypes({ limit: 100 })
    seatTypes.value = res.data
  } catch {
    message.error('Không tải được danh sách loại ghế')
  } finally {
    loadingOptions.value = false
  }
}

function syncFormWithProps() {
  if (props.showtime) {
    isEdit.value = true
    formData.movie_id = props.showtime.movie_id
    formData.room_id = props.showtime.room_id
    formData.show_date = props.showtime.show_date 
    formData.start_time = parseTime(props.showtime.start_time)
    formData.base_price = Number(props.showtime.base_price)
    formData.prices = (props.showtime.prices ?? []).map((p) => ({
      seat_type_id: p.seat_type_id,
      price: Number(p.price),
    }))
  } else {
    isEdit.value = false
    resetForm()
  }

  Object.keys(backendErrors).forEach((key) => delete backendErrors[key])
  nextTick(() => formRef.value?.restoreValidation())
}

watch(() => props.showtime, syncFormWithProps, { immediate: true })
watch(showModal, (visible) => {
  if (visible) fetchSeatTypes()
})
</script>

<template>
  <n-modal
    v-model:show="showModal"
    preset="dialog"
    :title="isEdit ? 'Chỉnh sửa suất chiếu' : 'Tạo suất chiếu mới'"
    :show-icon="false"
    style="width: 680px"
    @after-leave="syncFormWithProps"
  >
    <n-spin :show="loadingOptions">
      <n-form
        ref="formRef"
        :model="formData"
        :rules="formRules"
        label-placement="top"
        require-mark-placement="right-hanging"
        class="mt-4"
      >
        <!-- Phim -->
        <n-form-item
          label="Phim"
          path="movie_id"
          :validation-status="backendErrors.movie_id ? 'error' : undefined"
          :feedback="backendErrors.movie_id"
        >
          <n-select
            v-model:value="formData.movie_id"
            :options="movies"
            placeholder="Chọn phim"
            filterable
            @update:value="clearFieldError('movie_id')"
          />
        </n-form-item>

        <!-- Phòng chiếu -->
        <n-form-item
          label="Phòng chiếu"
          path="room_id"
          :validation-status="backendErrors.room_id ? 'error' : undefined"
          :feedback="backendErrors.room_id"
        >
          <n-select
            v-model:value="formData.room_id"
            :options="rooms"
            placeholder="Chọn phòng chiếu"
            filterable
            @update:value="clearFieldError('room_id')"
          />
        </n-form-item>

        <!-- Ngày + Giờ -->
        <n-grid :cols="2" :x-gap="16">
          <n-form-item-gi
            label="Ngày chiếu"
            path="show_date"
            :validation-status="backendErrors.show_date ? 'error' : undefined"
            :feedback="backendErrors.show_date"
          >
            <n-date-picker
              v-model:formatted-value="formData.show_date"
              type="date"
              value-format="yyyy-MM-dd"
              placeholder="Chọn ngày chiếu"
              style="width: 100%"
              @update:formatted-value="clearFieldError('show_date')"
            />
          </n-form-item-gi>

          <n-form-item-gi
            label="Giờ bắt đầu"
            path="start_time"
            :validation-status="backendErrors.start_time ? 'error' : undefined"
            :feedback="backendErrors.start_time"
          >
            <n-time-picker
              v-model:formatted-value="formData.start_time"
              format="HH:mm"
              value-format="HH:mm"
              placeholder="Chọn giờ bắt đầu"
              style="width: 100%"
              @update:formatted-value="clearFieldError('start_time')"
            />
          </n-form-item-gi>
        </n-grid>

        <!-- Giá cơ bản -->
        <n-form-item
          label="Giá cơ bản (VNĐ)"
          path="base_price"
          :validation-status="backendErrors.base_price ? 'error' : undefined"
          :feedback="backendErrors.base_price"
        >
          <n-input
            :value="
              formData.base_price != null
                ? new Intl.NumberFormat('vi-VN').format(formData.base_price)
                : ''
            "
            placeholder="Giá (VNĐ)"
            style="flex: 1"
            @input="
              (v: string) =>
                (formData.base_price = Number(v.replace(/\./g, '').replace(/[^\d]/g, '')) || null)
            "
          />
        </n-form-item>

        <!-- Giá theo loại ghế -->
        <n-divider title-placement="left">
          <span class="text-sm text-gray-500">Giá theo loại ghế</span>
        </n-divider>

        <div
          v-for="(priceRow, index) in formData.prices"
          :key="index"
          class="flex items-start gap-3 mb-3"
        >
          <n-select
            v-model:value="priceRow.seat_type_id"
            :options="getSeatTypeOptionsForRow(index)"
            placeholder="Loại ghế"
            style="flex: 1"
          />
          <n-input
            :value="
              priceRow.price != null ? new Intl.NumberFormat('vi-VN').format(priceRow.price) : ''
            "
            placeholder="Giá (VNĐ)"
            style="flex: 1"
            @input="
              (v: string) =>
                (priceRow.price = Number(v.replace(/\./g, '').replace(/[^\d]/g, '')) || null)
            "
          />
          <n-button quaternary type="error" @click="removePriceRow(index)">
            <template #icon
              ><n-icon><i class="i-mdi-trash-can-outline" /></n-icon
            ></template>
          </n-button>
        </div>

        <n-button
          dashed
          block
          :loading="loadingOptions"
          :disabled="!canAddPriceRow"
          @click="addPriceRow"
        >
          {{ seatTypes.length === 0 && !loadingOptions ? 'Chưa có loại ghế' : '+ Thêm giá loại ghế' }}
        </n-button>
      </n-form>
    </n-spin>

    <template #action>
      <n-button ghost @click="showModal = false">Hủy bỏ</n-button>
      <n-button
        type="primary"
        :loading="formLoading"
        :disabled="loadingOptions"
        @click="handleSubmit"
      >
        {{ isEdit ? 'Cập nhật ngay' : 'Thêm suất chiếu' }}
      </n-button>
    </template>
  </n-modal>
</template>
