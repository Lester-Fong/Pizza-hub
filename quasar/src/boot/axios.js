import axios from 'axios'

const registerUser = async () => {
  try {
    // Use relative path to enable proxy
    const response = await axios.get('/api/version')
    return response.data
  } catch (error) {
    console.error('Error registering user:', error)
    throw error
  }
}
const loginUser = async (credentials) => {
  try {
    const response = await axios.post('/api/login', credentials)
    return response.data
  } catch (error) {
    console.error('Error logging in user:', error)
    throw error
  }
}

export { registerUser, loginUser }
