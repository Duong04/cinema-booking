<script setup lang="ts">
import type { FormInst } from 'naive-ui'
import { ref, reactive, watch } from 'vue'
import { useMessage } from 'naive-ui'
import { roleService } from '../../services/role.service'
import type { Role } from '../../types/role.type';

const props = defineProps<{ role?: Role | null }>()
const emit = defineEmits<{ (e: 'success'): void }>()

const message = useMessage()
const showModal = defineModel<boolean>('show')
const formRef = ref<FormInst | null>(null)
const formLoading = ref(false)
const isEdit = ref(false)
const formData = reactive({ name: '', description: '' })
const formRules = {
  name: [{ required: true, message: 'Name is required', trigger: 'blur' }],
}

watch(() => props.role, (role) => {
  if (role) {
    isEdit.value = true
    formData.name = role.name
    formData.description = role.description ?? ''
  } else {
    isEdit.value = false
    formData.name = ''
    formData.description = ''
  }
})

async function handleSubmit() {
  formRef.value?.validate(async (errors) => {
    if (errors) return
    formLoading.value = true
    try {
      if (isEdit.value && props.role) {
        await roleService.updateRole(props.role.id, formData as Role)
        message.success('Role updated successfully')
      } else {
        await roleService.createRole(formData as Role)
        message.success('Role created successfully')
      }
      showModal.value = false
      emit('success')
    } finally {
      formLoading.value = false
    }
  })
}
</script>

<template>
  <n-modal
    v-model:show="showModal"
    preset="dialog"
    :title="isEdit ? 'Edit Role' : 'Create Role'"
    style="width: 500px"
  >
    <n-form ref="formRef" :model="formData" :rules="formRules" label-placement="top">
      <n-form-item label="Name" path="name">
        <n-input v-model:value="formData.name" placeholder="Enter role name" />
      </n-form-item>
      <n-form-item label="Description" path="description">
        <n-input v-model:value="formData.description" type="textarea" placeholder="Enter description" :rows="3" />
      </n-form-item>
    </n-form>
    <template #action>
      <n-button @click="showModal = false">Cancel</n-button>
      <n-button type="primary" :loading="formLoading" @click="handleSubmit">
        {{ isEdit ? 'Update' : 'Create' }}
      </n-button>
    </template>
  </n-modal>
</template>