<template>
  <q-page class="flex flex-center fit auth">
    <div v-if="is_calling_api" class="app-loader"></div>
    <q-card class="app-card-glass absolute-center text-black w-30 q-pa-md h-60 flex column justify-between">
      <q-card-section class="text-h6 text-center"> Login </q-card-section>

      <div class="my-auto">
        <q-card-section>
          <div class="mb-2">
            <q-input v-model="credentials.email" label="Email" type="email" outlined standout="bg-teal text-white" class="auth-input" />
            <span class="text-red-5">{{ errors.email }}</span>
          </div>

          <div class="mb-2">
            <q-input v-model="credentials.password" label="Password" type="password" outlined standout="bg-teal text-white" class="auth-input" />
            <span class="text-red-5">{{ errors.password }}</span>
          </div>
        </q-card-section>
        <q-card-actions class="justify-end pe-1">
          <q-btn @click="login" size="lg" color="primary" class="full-width font-md" label="Login" />
        </q-card-actions>
        <div class="flex justify-end items-center mb-2 me-2 mt-2">
          <span class="text-grey text-sm">Don't have an account?</span>
          <router-link :to="{ name: 'SignUpPage' }" class="text-accent text-sm ms-1">Sign Up</router-link>
        </div>
      </div>
    </q-card>
  </q-page>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { loginUser } from '../../boot/axios'
import { useQuasar } from 'quasar'
import { useRouter } from 'vue-router'

const $router = useRouter()
const $q = useQuasar()

const is_calling_api = ref(false)
const credentials = reactive({
  email: '',
  password: '',
})

const errors = reactive({
  email: '',
  password: '',
})

const isFieldsValid = () => {
  errors.email = credentials.email ? '' : 'Email is required'
  errors.password = credentials.password ? '' : 'Password is required'

  return !errors.email && !errors.password
}

const handleErrorResponse = (errorData) => {
  if (errorData.email) {
    errors.email = errorData.email[0]
  } else {
    errors.email = ''
  }

  if (errorData.password) {
    errors.password = errorData.password[0]
  } else {
    errors.password = ''
  }
}

const clearErrorsAndFields = () => {
  errors.email = ''
  errors.password = ''
  credentials.email = ''
  credentials.password = ''
}

const login = async () => {
  if (!isFieldsValid()) {
    return
  }
  is_calling_api.value = true

  try {
    const response = await loginUser(credentials)

    is_calling_api.value = false
    clearErrorsAndFields()

    $q.localStorage.set('access_token', response.token)
    $q.dialog({
      title: 'Login Successful',
      message: response.message || 'You have successfully logged in.',
      ok: true,
    }).onOk(() => {
      $router.push({ name: 'DashboardPage' })
    })
  } catch (error) {
    is_calling_api.value = false
    if (error.response && error.response.data) {
      handleErrorResponse(error.response.data)
    } else {
      clearErrorsAndFields()
      $q.dialog({
        title: 'Error',
        message: 'An unexpected error occurred. Please try again later.',
        ok: true,
      })
    }
  }
}
</script>