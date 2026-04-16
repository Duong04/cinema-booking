<script setup lang="ts">
import { ref, reactive, watch, nextTick, computed, onMounted } from 'vue'
import type { FormInst, FormRules, UploadFileInfo, SelectOption } from 'naive-ui'
import { useMessage } from 'naive-ui'
import { movieService } from '@/features/admin/services/movie.service'
import { genreService } from '@/features/admin/services/genre.service'
import { uploadService } from '@/features/shared/services/upload.service'
import { ApiError } from '@/plugins/axios'
import type { Movie, Status, MoviePayload } from '@/features/admin/types/movie.type'
import type { Genre } from '@/features/admin/types/genre.type'

interface MovieForm {
  title: string
  duration_minutes: number | null
  poster_url: string
  trailer_url: string
  description: string
  content: string
  release_date: string
  language: string
  status: Status
  genres: string[]
}

const props = defineProps<{
  movie?: Movie | null
}>()

const emit = defineEmits<{
  (e: 'success'): void
}>()

const message = useMessage()
const showModal = defineModel<boolean>('show')
const formRef = ref<FormInst | null>(null)
const formLoading = ref(false)
const isEdit = ref(false)
const genreOptions = ref<SelectOption[]>([])
// Upload states
const posterUploading = ref(false)
const trailerUploading = ref(false)

const statusOptions: SelectOption[] = [
  { label: 'Sắp chiếu', value: 'coming_soon' },
  { label: 'Đang chiếu', value: 'now_showing' },
  { label: 'Ngừng chiếu', value: 'ended' },
  { label: 'Đã hủy', value: 'cancelled' },
]

const formData = reactive<MovieForm>({
  title: '',
  duration_minutes: null,
  poster_url: '',
  trailer_url: '',
  description: '',
  content: '',
  release_date: '',
  language: '',
  status: 'coming_soon',
  genres: [],
})

const backendErrors = reactive<Record<string, string>>({})

const formRules: FormRules = {
  title: [{ required: true, message: 'Vui lòng nhập tên phim', trigger: 'blur' }],
  duration_minutes: [
    {
      required: true,
      type: 'number',
      message: 'Vui lòng nhập thời lượng phim',
      trigger: ['blur', 'change'],
    },
    {
      type: 'number',
      min: 1,
      message: 'Thời lượng phải lớn hơn 0',
      trigger: ['blur', 'change'],
    },
  ],
  poster_url: [
    { required: true, message: 'Vui lòng tải lên poster phim', trigger: ['blur', 'change'] },
  ],
  release_date: [
    { required: true, message: 'Vui lòng chọn ngày khởi chiếu', trigger: ['blur', 'change'] },
  ],
  status: [{ required: true, message: 'Vui lòng chọn trạng thái', trigger: ['blur', 'change'] }],
  description: [{ required: true, message: 'Vui lòng nhập mô tả', trigger: 'blur' }],
  content: [{ required: true, message: 'Vui lòng nhập nội dung phim', trigger: 'blur' }],
}

function clearFieldError(field: keyof MovieForm) {
  if (backendErrors[field]) {
    delete backendErrors[field]
  }
}

async function handlePosterUpload({ file }: { file: UploadFileInfo }) {
  if (!file.file) return
  try {
    posterUploading.value = true
    const res = await uploadService.uploadImage(file.file, 'posters')
    formData.poster_url = res.data.url
    clearFieldError('poster_url')
    message.success('Tải poster thành công')
  } catch {
    message.error('Tải poster thất bại')
  } finally {
    posterUploading.value = false
  }
  return false
}

async function handleTrailerUpload({ file }: { file: UploadFileInfo }) {
  if (!file.file) return
  try {
    trailerUploading.value = true
    const res = await uploadService.uploadImage(file.file, 'trailers')
    formData.trailer_url = res.data.url
    clearFieldError('trailer_url')
    message.success('Tải ảnh trailer thành công')
  } catch {
    message.error('Tải ảnh trailer thất bại')
  } finally {
    trailerUploading.value = false
  }
  return false
}

function syncFormWithProps() {
  if (props.movie) {
    isEdit.value = true
    formData.title = props.movie.title ?? ''
    formData.duration_minutes = props.movie.duration_minutes ?? null
    formData.poster_url = props.movie.poster_url ?? ''
    formData.trailer_url = props.movie.trailer_url ?? ''
    formData.description = props.movie.description ?? ''
    formData.content = props.movie.content ?? ''
    formData.release_date = props.movie.release_date ?? ''
    formData.language = props.movie.language ?? ''
    formData.status = props.movie.status ?? 'coming_soon'
    formData.genres = Array.isArray(props.movie.genres)
      ? props.movie.genres.map((g: Genre) => (typeof g === 'string' ? g : g.id))
      : []
  } else {
    isEdit.value = false
    formData.title = ''
    formData.duration_minutes = null
    formData.poster_url = ''
    formData.trailer_url = ''
    formData.description = ''
    formData.content = ''
    formData.release_date = ''
    formData.language = ''
    formData.status = 'coming_soon'
    formData.genres = []
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

    if (isEdit.value && props.movie?.id) {
      await movieService.updateMovie(props.movie.id, payload as MoviePayload)
      message.success('Cập nhật phim thành công')
    } else {
      await movieService.createMovie(payload as MoviePayload)
      message.success('Tạo phim mới thành công')
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

const releaseDateTs = computed({
  get() {
    if (!formData.release_date) return null
    const ts = new Date(formData.release_date).getTime()
    return isNaN(ts) ? null : ts
  },
  set(val: number | null) {
    if (!val) {
      formData.release_date = ''
      return
    }
    const d = new Date(val)
    const yyyy = d.getFullYear()
    const mm = String(d.getMonth() + 1).padStart(2, '0')
    const dd = String(d.getDate()).padStart(2, '0')
    formData.release_date = `${yyyy}-${mm}-${dd}`
    clearFieldError('release_date')
  },
})

async function fetchOptions() {
  const genreRes = await genreService.getAllGenres({ limit: 100 })

  genreOptions.value = genreRes.data.map((c) => ({ label: c.name, value: c.id }))
}

async function handleDeletePoster() {
  if (!formData.poster_url) return
  try {
    await uploadService.deleteFile(formData.poster_url)
    message.success('Đã xóa poster')
  } catch {
    message.warning('Xóa trên cloud thất bại, vẫn xóa khỏi form')
  } finally {
    formData.poster_url = ''
  }
}

async function handleDeleteTrailer() {
  if (!formData.trailer_url) return
  try {
    await uploadService.deleteFile(formData.trailer_url)
    message.success('Đã xóa ảnh trailer')
  } catch {
    message.warning('Xóa trên cloud thất bại, vẫn xóa khỏi form')
  } finally {
    formData.trailer_url = ''
  }
}

onMounted(fetchOptions)
watch(() => props.movie, syncFormWithProps, { immediate: true })
</script>

<template>
  <n-modal
    v-model:show="showModal"
    preset="card"
    :title="isEdit ? 'Chỉnh sửa phim' : 'Tạo phim mới'"
    style="width: 700px; max-width: 95vw"
    :segmented="{ content: true, footer: true }"
    @after-leave="syncFormWithProps"
  >
    <n-scrollbar style="max-height: 70vh" trigger="none">
      <n-form
        ref="formRef"
        :model="formData"
        :rules="formRules"
        label-placement="top"
        require-mark-placement="right-hanging"
        class="px-1 py-2"
      >
        <n-grid :cols="2" :x-gap="16">
          <!-- Tên phim -->
          <n-form-item-gi
            label="Tên phim"
            path="title"
            :validation-status="backendErrors.title ? 'error' : undefined"
            :feedback="backendErrors.title"
          >
            <n-input
              v-model:value="formData.title"
              placeholder="Nhập tên phim..."
              @input="clearFieldError('title')"
            />
          </n-form-item-gi>
          <n-form-item-gi
            label="Thời lượng (phút)"
            path="duration_minutes"
            :validation-status="backendErrors.duration_minutes ? 'error' : undefined"
            :feedback="backendErrors.duration_minutes"
          >
            <n-input-number
              v-model:value="formData.duration_minutes"
              placeholder="VD: 120"
              :min="1"
              :max="600"
              style="width: 100%"
              @update:value="clearFieldError('duration_minutes')"
            />
          </n-form-item-gi>
        </n-grid>

        <!-- Ngày khởi chiếu + Ngôn ngữ -->
        <n-grid :cols="2" :x-gap="16">
          <n-form-item-gi
            label="Ngày khởi chiếu"
            path="release_date"
            :validation-status="backendErrors.release_date ? 'error' : undefined"
            :feedback="backendErrors.release_date"
          >
            <n-date-picker
              v-model:value="releaseDateTs"
              type="date"
              placeholder="Chọn ngày"
              style="width: 100%"
            />
          </n-form-item-gi>

          <n-form-item-gi
            label="Ngôn ngữ"
            path="language"
            :validation-status="backendErrors.language ? 'error' : undefined"
            :feedback="backendErrors.language"
          >
            <n-input
              v-model:value="formData.language"
              placeholder="VD: Tiếng Việt, English..."
              @input="clearFieldError('language')"
            />
          </n-form-item-gi>
        </n-grid>

        <!-- Trạng thái + Thể loại -->
        <n-grid :cols="2" :x-gap="16">
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

          <n-form-item-gi
            label="Thể loại"
            path="genres"
            :validation-status="backendErrors.genres ? 'error' : undefined"
            :feedback="backendErrors.genres"
          >
            <n-select
              v-model:value="formData.genres"
              :options="genreOptions"
              placeholder="Chọn thể loại"
              multiple
              clearable
              @update:value="clearFieldError('genres')"
            />
          </n-form-item-gi>
        </n-grid>

        <!-- Poster Upload -->
        <n-form-item
          label="Poster phim"
          path="poster_url"
          :validation-status="backendErrors.poster_url ? 'error' : undefined"
          :feedback="backendErrors.poster_url"
        >
          <n-space vertical style="width: 100%">
            <n-upload
              accept="image/*"
              :max="1"
              :show-file-list="false"
              :custom-request="handlePosterUpload"
            >
              <n-button :loading="posterUploading" secondary>
                <template #icon>
                  <n-icon>
                    <!-- upload icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                      <path
                        d="M19.35 10.04A7.49 7.49 0 0 0 12 4C9.11 4 6.6 5.64 5.35 8.04A5.994 5.994 0 0 0 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM14 13v4h-4v-4H7l5-5 5 5h-3z"
                      />
                    </svg>
                  </n-icon>
                </template>
                Tải lên poster
              </n-button>
            </n-upload>

            <!-- Poster preview -->
            <div
              v-if="formData.poster_url"
              style="display: flex; align-items: flex-start; gap: 8px"
            >
              <img
                :src="formData.poster_url"
                alt="Poster preview"
                style="
                  width: 90px;
                  height: 130px;
                  object-fit: cover;
                  border-radius: 6px;
                  border: 1px solid #e0e0e0;
                "
              />
              <n-button
                size="small"
                quaternary
                type="error"
                style="margin-top: 4px"
                @click="handleDeletePoster"
              >
                Xóa
              </n-button>
            </div>

            <!-- Manual URL input -->
            <n-input
              v-model:value="formData.poster_url"
              placeholder="Hoặc nhập URL poster..."
              size="small"
              @input="clearFieldError('poster_url')"
            />
          </n-space>
        </n-form-item>

        <!-- Trailer URL Upload -->
        <n-form-item
          label="Ảnh thumbnail trailer"
          path="trailer_url"
          :validation-status="backendErrors.trailer_url ? 'error' : undefined"
          :feedback="backendErrors.trailer_url"
        >
          <n-space vertical style="width: 100%">
            <n-upload
              accept="image/*"
              :max="1"
              :show-file-list="false"
              :custom-request="handleTrailerUpload"
            >
              <n-button :loading="trailerUploading" secondary>
                <template #icon>
                  <n-icon>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                      <path
                        d="M19.35 10.04A7.49 7.49 0 0 0 12 4C9.11 4 6.6 5.64 5.35 8.04A5.994 5.994 0 0 0 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM14 13v4h-4v-4H7l5-5 5 5h-3z"
                      />
                    </svg>
                  </n-icon>
                </template>
                Tải lên thumbnail trailer
              </n-button>
            </n-upload>

            <!-- Trailer preview -->
            <div
              v-if="formData.trailer_url"
              style="display: flex; align-items: flex-start; gap: 8px"
            >
              <img
                :src="formData.trailer_url"
                alt="Trailer preview"
                style="
                  width: 140px;
                  height: 80px;
                  object-fit: cover;
                  border-radius: 6px;
                  border: 1px solid #e0e0e0;
                "
              />
              <n-button
                size="small"
                quaternary
                type="error"
                style="margin-top: 4px"
                @click="handleDeleteTrailer"
              >
                Xóa
              </n-button>
            </div>

            <!-- Manual URL input -->
            <n-input
              v-model:value="formData.trailer_url"
              placeholder="Hoặc nhập URL trailer / YouTube embed..."
              size="small"
              @input="clearFieldError('trailer_url')"
            />
          </n-space>
        </n-form-item>

        <!-- Mô tả -->
        <n-form-item
          label="Mô tả"
          path="description"
          :validation-status="backendErrors.description ? 'error' : undefined"
          :feedback="backendErrors.description"
        >
          <n-input
            v-model:value="formData.description"
            type="textarea"
            placeholder="Nhập mô tả ngắn về phim..."
            :rows="3"
            @input="clearFieldError('description')"
          />
        </n-form-item>

        <!-- Nội dung -->
        <n-form-item
          label="Nội dung phim"
          path="content"
          :validation-status="backendErrors.content ? 'error' : undefined"
          :feedback="backendErrors.content"
        >
          <n-input
            v-model:value="formData.content"
            type="textarea"
            placeholder="Nhập nội dung chi tiết về phim..."
            :rows="4"
            @input="clearFieldError('content')"
          />
        </n-form-item>
      </n-form>
    </n-scrollbar>

    <template #footer>
      <n-space justify="end">
        <n-button ghost @click="showModal = false">Hủy bỏ</n-button>
        <n-button type="primary" :loading="formLoading" @click="handleSubmit">
          {{ isEdit ? 'Cập nhật ngay' : 'Thêm phim' }}
        </n-button>
      </n-space>
    </template>
  </n-modal>
</template>
