import axios from 'axios'

const instance = axios.create({
    baseURL: import.meta.env.VITE_API_URL || 'http://localhost',
    withCredentials: true,
    withXSRFToken: true,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    }
})

// Request interceptor
instance.interceptors.request.use(
    (config) => config,
    (error) => Promise.reject(error)
)

// // Response interceptor
// instance.interceptors.response.use(
//     (response) => response,
//     (error) => {
//         if (error.response?.status === 401) {
//             window.location.href = '/login'
//         }
//         return Promise.reject(error)
//     }
// )

export default instance