import api from '@/shared/api/api'

export const uploadService = {
  uploadImage: (file: File, folder: string): Promise<{ data: { url: string } }> => {
    const formData = new FormData()
    formData.append('file', file)
    formData.append('folder', folder)
    return api.post('/upload/image', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
  },

  uploadFile: (file: File, folder: string) => {
    const formData = new FormData()
    formData.append('file', file)
    formData.append('folder', folder)
    return api.post('/upload/file', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
  },

  uploadMultiple: (files: File[], folder: string) => {
    const formData = new FormData()
    files.forEach(file => formData.append('files[]', file))
    formData.append('folder', folder)
    return api.post('/upload/multiple', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
  },

  deleteFile: (url: string) =>
    api.delete('/upload', { data: { url } }),

  deleteMultiple: (urls: string[]) =>
    api.delete('/upload/multiple', { data: { urls } }),
}