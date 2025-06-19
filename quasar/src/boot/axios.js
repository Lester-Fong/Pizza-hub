import axios from 'axios'
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

export { registerUser, loginUser }
