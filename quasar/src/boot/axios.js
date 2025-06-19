import axios from 'axios'
import { LocalStorage } from 'quasar'
axios.defaults.withCredentials = true
axios.defaults.withXSRFToken = true

const $user_request = axios.create({
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
})

const registerUser = async (credentials) => {
  // Use relative path to enable proxy
  const response = await $user_request.post('/api/register', credentials)
  return response.data
}

const loginUser = async (credentials) => {
  const creds = {
    email: credentials.email,
    password: credentials.password,
  }

  const res = await axios.get('/sanctum/csrf-cookie')
  if (res.status !== 200) {
    throw new Error('Failed to get CSRF cookie')
  }

  const response = await $user_request.post('/api/login', creds)
  return response.data
}

const getUser = async () => {
  const response = await $user_request.get('/api/profile', {
    headers: { Authorization: `Bearer ${LocalStorage.getItem('access_token')}` },
  })
  return response.data
}

const handlePromiseFiles = async (importFiles) => {
  const promises = importFiles.map(({ file, endpoint }) => {
    if (!file) throw new Error(`No file selected for ${endpoint}`)
    const formData = new FormData()
    formData.append('csv_file', file) // Append file as 'csv_file'
    return $user_request.post(endpoint, formData, {
      headers: {
        Authorization: `Bearer ${LocalStorage.getItem('access_token')}`,
        'Content-Type': 'multipart/form-data', // Explicitly set content type
      },
    })
  })
  const responses = await Promise.all(promises)
  return responses.map((response) => response.data)
}

// Check if (pizza, pizza type, orders, order details) tables are empty
const getDashboardData = async () => {
  const accessToken = LocalStorage.getItem('access_token')
  const headers = {
    Authorization: `Bearer ${accessToken}`,
  }
  const endpoints = ['/api/pizza', '/api/pizza-types', '/api/orders', '/api/order-details']
  const promises = endpoints.map((endpoint) => $user_request.get(endpoint, { headers }))
  const responses = await Promise.all(promises)
  return responses.map((response) => response.data)
}

const getSummaryData = async () => {
  const response = await axios.get('/api/sales-summary', {
    headers: { Authorization: `Bearer ${LocalStorage.getItem('access_token')}` },
  })
  return response.data
}

const getTrendResponse = async () => {
  const response = await axios.get('/api/daily-sales-trend', {
    headers: { Authorization: `Bearer ${LocalStorage.getItem('access_token')}` },
  })
  return response.data
}

const handleLogoutProcess = async () => {
  const response = await $user_request.post(
    '/api/logout',
    {},
    {
      headers: { Authorization: `Bearer ${LocalStorage.getItem('access_token')}` },
    },
  )
  if (response.status === 200) {
    LocalStorage.remove('access_token')
    return true
  } else {
    throw new Error('Logout failed')
  }
}

export { registerUser, loginUser, handlePromiseFiles, getDashboardData, getSummaryData, getTrendResponse, getUser, handleLogoutProcess }
