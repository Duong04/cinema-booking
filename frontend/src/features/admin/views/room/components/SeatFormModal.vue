<script setup lang="ts">
import { ref, reactive, watch, computed } from 'vue'
import { useMessage, useDialog } from 'naive-ui'
import { seatService } from '@/features/admin/services/seat.service'
import { ApiError } from '@/plugins/axios'
import type { Seat, SeatRow } from '@/features/admin/types/seat.type'
import type { Room } from '@/features/admin/types/room.type'
import { seatTypeService } from '@/features/admin/services/seat-type.service'

interface SeatTypeOption {
  label: string
  value: string
}

interface EditingRow {
  label: string
  seat_type_id: string
  seats_per_row: number
}

const props = defineProps<{
  room: Room | null
}>()

const showModal = defineModel<boolean>('show')
const message = useMessage()
const dialog = useDialog()

const seatMap = ref<Record<string, Seat[]> | null>(null)
const loadingSeats = ref(false)
const submitting = ref(false)
const activeTab = ref<'view' | 'create'>('view')
const seatTypeOptions = ref<SeatTypeOption[]>([])
const newRows = reactive<SeatRow[]>([{ label: '', seats_per_row: 10, seat_type_id: '' }])

// Edit state
const editingLabel = ref<string | null>(null)
const editingRow = reactive<EditingRow>({ label: '', seat_type_id: '', seats_per_row: 10 })
const savingRow = ref(false)
const deletingLabel = ref<string | null>(null)

const sortedRowLabels = computed(() => (seatMap.value ? Object.keys(seatMap.value).sort() : []))
const hasSeats = computed(() => sortedRowLabels.value.length > 0)

async function fetchSeats() {
  if (!props.room?.id) return
  loadingSeats.value = true
  try {
    const res = await seatService.getSeatByRoomId(props.room.id)
    seatMap.value = res.data
  } catch {
    message.error('Không thể tải dữ liệu ghế')
  } finally {
    loadingSeats.value = false
  }
}

async function fetchOptions() {
  const seatTypesRes = await seatTypeService.getAllSeatTypes()
  seatTypeOptions.value = seatTypesRes.data.map((seatType) => ({
    label: seatType.name,
    value: seatType.id,
  }))
}

function startEdit(rowLabel: string) {
  const seats = seatMap.value?.[rowLabel] ?? []
  editingLabel.value = rowLabel
  editingRow.label = rowLabel
  editingRow.seat_type_id = seats[0]?.seat_type_id ?? ''
  editingRow.seats_per_row = seats.length
}

function cancelEdit() {
  editingLabel.value = null
}

async function handleSaveRow() {
  if (!props.room?.id || !editingLabel.value) return
  savingRow.value = true
  try {
    await seatService.updateRow(props.room.id, editingLabel.value, {
      seat_type_id: editingRow.seat_type_id,
      seats_per_row: editingRow.seats_per_row,
    })
    message.success(`Đã cập nhật hàng ${editingLabel.value}`)
    editingLabel.value = null
    await fetchSeats()
  } catch (err) {
    if (err instanceof ApiError) message.error(err.message)
    else message.error('Đã có lỗi xảy ra')
  } finally {
    savingRow.value = false
  }
}

function confirmDeleteRow(rowLabel: string) {
  dialog.warning({
    title: `Xóa hàng ${rowLabel}`,
    content: `Bạn có chắc muốn xóa toàn bộ ghế hàng ${rowLabel}? Hành động này không thể hoàn tác.`,
    positiveText: 'Xóa',
    negativeText: 'Hủy',
    onPositiveClick: () => handleDeleteRow(rowLabel),
  })
}

async function handleDeleteRow(rowLabel: string) {
  if (!props.room?.id) return
  deletingLabel.value = rowLabel
  try {
    await seatService.deleteRow(props.room.id, rowLabel)
    message.success(`Đã xóa hàng ${rowLabel}`)
    await fetchSeats()
  } catch (err) {
    if (err instanceof ApiError) message.error(err.message)
    else message.error('Đã có lỗi xảy ra')
  } finally {
    deletingLabel.value = null
  }
}

function addRow() {
  newRows.push({ label: '', seats_per_row: 10, seat_type_id: '' })
}

function removeRow(index: number) {
  newRows.splice(index, 1)
}

async function handleSubmit() {
  if (!props.room?.id) return
  const invalid = newRows.some((r) => !r.label.trim() || !r.seat_type_id || r.seats_per_row < 1)
  if (invalid) {
    message.warning('Vui lòng điền đầy đủ thông tin cho tất cả các hàng')
    return
  }
  submitting.value = true
  try {
    await seatService.createSeatByRoomId(props.room.id, { rows: [...newRows] })
    message.success('Tạo ghế thành công')
    resetNewRows()
    activeTab.value = 'view'
    await fetchSeats()
  } catch (err) {
    if (err instanceof ApiError) message.error(err.message)
    else message.error('Đã có lỗi hệ thống xảy ra')
  } finally {
    submitting.value = false
  }
}

function resetNewRows() {
  newRows.splice(0, newRows.length, { label: '', seats_per_row: 10, seat_type_id: '' })
}

watch(
  () => showModal.value,
  (val) => {
    if (val && props.room?.id) {
      activeTab.value = 'view'
      resetNewRows()
      fetchSeats()
      fetchOptions()
    }
  },
)
</script>

<template>
  <n-modal
    v-model:show="showModal"
    preset="card"
    :title="`Quản lý ghế — ${room?.name ?? ''}`"
    style="width: 820px"
    :segmented="{ content: true, footer: 'soft' }"
    @after-leave="resetNewRows"
  >
    <n-tabs v-model:value="activeTab" type="line" animated>
      <!-- ── Tab: Sơ đồ ghế ── -->
      <n-tab-pane name="view" tab="Sơ đồ ghế">
        <n-spin :show="loadingSeats">
          <div v-if="!loadingSeats && !hasSeats" class="empty-state">
            <n-empty description="Phòng này chưa có ghế nào" size="large">
              <template #extra>
                <n-button type="primary" @click="activeTab = 'create'">+ Tạo ghế ngay</n-button>
              </template>
            </n-empty>
          </div>

          <div v-else class="seat-map">
            <div class="screen-bar"><span>MÀN HÌNH</span></div>

            <div v-for="rowLabel in sortedRowLabels" :key="rowLabel" class="seat-row-wrapper">
              <!-- Inline edit form -->
              <div v-if="editingLabel === rowLabel" class="edit-row-form">
                <n-card size="small" :title="`Sửa hàng ${rowLabel}`">
                  <n-grid :cols="2" :x-gap="12">
                    <n-gi>
                      <n-form-item label="Loại ghế" required>
                        <n-select
                          v-model:value="editingRow.seat_type_id"
                          :options="seatTypeOptions"
                          placeholder="Chọn loại ghế"
                        />
                      </n-form-item>
                    </n-gi>
                    <n-gi>
                      <n-form-item label="Số ghế / hàng" required>
                        <n-input-number
                          v-model:value="editingRow.seats_per_row"
                          :min="1"
                          :max="50"
                          style="width: 100%"
                        />
                      </n-form-item>
                    </n-gi>
                  </n-grid>
                  <template #footer>
                    <n-space justify="end">
                      <n-button size="small" @click="cancelEdit">Hủy</n-button>
                      <n-button
                        size="small"
                        type="primary"
                        :loading="savingRow"
                        @click="handleSaveRow"
                      >
                        Lưu
                      </n-button>
                    </n-space>
                  </template>
                </n-card>
              </div>

              <!-- Normal seat row -->
              <div v-else class="seat-row">
                <span class="row-label">{{ rowLabel }}</span>
                <div class="seats">
                  <n-tooltip
                    v-for="seat in seatMap?.[rowLabel] ?? []"
                    :key="seat.id"
                    trigger="hover"
                    placement="top"
                  >
                    <template #trigger>
                      <div class="seat" :class="seat.seat_type?.name?.toLowerCase()">
                        {{ seat.seat_number }}
                      </div>
                    </template>
                    <span>
                      {{ rowLabel }}{{ seat.seat_number }} · {{ seat.seat_type?.name }} (×{{
                        seat.seat_type?.base_multiplier
                      }})
                    </span>
                  </n-tooltip>
                </div>
                <span class="row-label">{{ rowLabel }}</span>

                <!-- Action buttons -->
                <div class="row-actions">
                  <n-button size="tiny" quaternary type="primary" @click="startEdit(rowLabel)">
                    ✏️ Sửa
                  </n-button>
                  <n-button
                    size="tiny"
                    quaternary
                    type="error"
                    :loading="deletingLabel === rowLabel"
                    @click="confirmDeleteRow(rowLabel)"
                  >
                    🗑️ Xóa
                  </n-button>
                </div>
              </div>
            </div>

            <!-- Legend -->
            <div class="legend">
              <div class="legend-item">
                <div class="seat standard" style="pointer-events: none" />
                <span>Standard</span>
              </div>
              <div class="legend-item">
                <div class="seat vip" style="pointer-events: none" />
                <span>VIP</span>
              </div>
              <div class="legend-item">
                <div class="seat imax" style="pointer-events: none" />
                <span>IMAX</span>
              </div>
            </div>
          </div>
        </n-spin>
      </n-tab-pane>

      <!-- ── Tab: Tạo hàng mới ── -->
      <n-tab-pane name="create" tab="Tạo hàng ghế mới">
        <div class="create-section">
          <div v-for="(row, index) in newRows" :key="index" class="row-form-item">
            <n-card size="small" :title="`Hàng ${index + 1}`">
              <template #header-extra>
                <n-button
                  v-if="newRows.length > 1"
                  text
                  type="error"
                  size="small"
                  @click="removeRow(index)"
                >
                  Xóa
                </n-button>
              </template>
              <n-grid :cols="3" :x-gap="12">
                <n-gi>
                  <n-form-item label="Ký hiệu hàng" required>
                    <n-input
                      v-model:value="row.label"
                      placeholder="VD: A, B, C..."
                      :maxlength="2"
                      style="text-transform: uppercase"
                    />
                  </n-form-item>
                </n-gi>
                <n-gi>
                  <n-form-item label="Số ghế / hàng" required>
                    <n-input-number
                      v-model:value="row.seats_per_row"
                      :min="1"
                      :max="50"
                      style="width: 100%"
                    />
                  </n-form-item>
                </n-gi>
                <n-gi>
                  <n-form-item label="Loại ghế" required>
                    <n-select
                      v-model:value="row.seat_type_id"
                      :options="seatTypeOptions"
                      placeholder="Chọn loại ghế"
                    />
                  </n-form-item>
                </n-gi>
              </n-grid>
            </n-card>
          </div>
          <n-button dashed block @click="addRow">+ Thêm hàng</n-button>
        </div>
      </n-tab-pane>
    </n-tabs>

    <template #footer>
      <n-space justify="end">
        <n-button @click="showModal = false">Đóng</n-button>
        <n-button
          v-if="activeTab === 'create'"
          type="primary"
          :loading="submitting"
          @click="handleSubmit"
        >
          Lưu ghế
        </n-button>
      </n-space>
    </template>
  </n-modal>
</template>

<style scoped>
.seat-map {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  padding: 16px 0;
}

.screen-bar {
  width: 70%;
  background: linear-gradient(to bottom, #d0d0d0, #f0f0f0);
  border-radius: 4px 4px 0 0;
  text-align: center;
  padding: 6px 0;
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 4px;
  color: #666;
  margin-bottom: 16px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.seat-row-wrapper {
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.seat-row {
  display: flex;
  align-items: center;
  gap: 8px;
}

.row-actions {
  display: flex;
  gap: 2px;
  margin-left: 8px;
  opacity: 0;
  transition: opacity 0.15s;
}

.seat-row:hover .row-actions {
  opacity: 1;
}

.edit-row-form {
  width: 90%;
  margin-bottom: 4px;
  animation: slideIn 0.2s ease;
}

.row-label {
  width: 20px;
  font-size: 12px;
  font-weight: 700;
  color: #999;
  text-align: center;
}

.seats {
  display: flex;
  gap: 5px;
  flex-wrap: wrap;
}

.seat {
  width: 28px;
  height: 28px;
  border-radius: 4px 4px 6px 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 9px;
  font-weight: 600;
  cursor: default;
  border-bottom: 3px solid transparent;
  transition: transform 0.15s;
}

.seat:hover {
  transform: scale(1.15);
}

.seat.standard {
  background: #e8f4fd;
  color: #2080f0;
  border-bottom-color: #2080f0;
}
.seat.vip {
  background: #fff0f0;
  color: #d03050;
  border-bottom-color: #d03050;
}
.seat.imax {
  background: #f0fff0;
  color: #18a058;
  border-bottom-color: #18a058;
}

.legend {
  display: flex;
  gap: 16px;
  margin-top: 16px;
  padding-top: 12px;
  border-top: 1px dashed #eee;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  color: #666;
}

.create-section {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.row-form-item,
.edit-row-form {
  animation: slideIn 0.2s ease;
}

.empty-state {
  padding: 40px 0;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(-8px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
