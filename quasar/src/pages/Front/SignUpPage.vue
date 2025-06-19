<template>
  <q-page class="flex flex-center fit auth">
    <div v-if="is_calling_api" class="app-loader">
      <q-spinner-hourglass color="purple" size="5em" />
    </div>
    <q-card class="app-card-glass absolute-center text-black w-30 q-pa-md h-65 flex column justify-between">
      <q-card-section class="text-h6 text-center"> Sign Up </q-card-section>

      <div class="my-auto">
        <q-card-section>
          <div class="mb-2">
            <q-input v-model="credentials.firstname" label="Firstname" outlined standout="text-white" class="auth-input text-white" />
            <span class="text-red-5">{{ errors.firstname }}</span>
          </div>

          <div class="mb-2">
            <q-input v-model="credentials.lastname" label="Lastname" outlined standout="text-white" class="auth-input text-white" />
            <span class="text-red-5">{{ errors.lastname }}</span>
          </div>

          <div class="mb-2">
            <q-input v-model="credentials.email" label="Email" type="email" outlined standout="text-white" class="auth-input text-white" />
            <span class="text-red-5">{{ errors.email }}</span>
          </div>

          <div class="mb-2">
            <q-input v-model="credentials.password" label="Password" type="password" outlined standout="text-white" class="auth-input text-white" />
            <span class="text-red-5">{{ errors.password }}</span>
          </div>

          <div class="mb-2">
            <q-input v-model="credentials.confirm_password" label="Confirm Password" type="password" outlined standout="text-white" class="auth-input text-white" />
            <span class="text-red-5">{{ errors.confirm_password }}</span>
          </div>
        </q-card-section>
        <q-card-actions class="justify-end pe-1">
          <q-btn @click="handleRegister" size="lg" color="primary" class="full-width font-md" label="Sign Up" />
        </q-card-actions>
        <div class="flex justify-end items-center mb-2 me-2 mt-2">
          <span class="text-grey text-sm">Already have an account?</span>
          <router-link :to="{ name: 'LoginPage' }" class="text-accent text-sm ms-1">Login</router-link>
        </div>
      </div>
    </q-card>
  </q-page>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { registerUser } from '../../boot/axios'
const credentials = reactive({
  firstname: '',
  lastname: '',
  email: '',
  password: '',
  confirm_password: '',
})

const is_calling_api = ref(false)
import { useQuasar } from 'quasar'
import { useRouter } from 'vue-router'

const $router = useRouter()
const $q = useQuasar()

// Errors
const errors = ref({
  firstname: '',
  lastname: '',
  email: '',
  password: '',
  confirm_password: '',
})

const isFieldsValid = () => {
  let isValid = true
  if (!credentials.firstname) {
    errors.value.firstname = 'Firstname is required'
    isValid = false
  }
  if (!credentials.lastname) {
    errors.value.lastname = 'Lastname is required'
    isValid = false
  }
  if (!credentials.email) {
    errors.value.email = 'Email is required'
    isValid = false
  }
  if (!credentials.password) {
    errors.value.password = 'Password is required'
    isValid = false
  }
  if (!credentials.confirm_password) {
    errors.value.confirm_password = 'Confirm Password is required'
    isValid = false
  }
  if (credentials.password !== credentials.confirm_password) {
    errors.value.confirm_password = 'Passwords do not match'
    isValid = false
  }
  return isValid
}

const clearErrorsAndFields = () => {
  errors.value.firstname = ''
  errors.value.lastname = ''
  errors.value.email = ''
  errors.value.password = ''
  errors.value.confirm_password = ''
  credentials.firstname = ''
  credentials.lastname = ''
  credentials.email = ''
  credentials.password = ''
  credentials.confirm_password = ''
}

const handleErrorResponse = (errorData) => {
  if (errorData.errors) {
    errors.value.firstname = errorData.errors.firstname ? errorData.errors.firstname[0] : ''
    errors.value.lastname = errorData.errors.lastname ? errorData.errors.lastname[0] : ''
    errors.value.email = errorData.errors.email ? errorData.errors.email[0] : ''
    errors.value.password = errorData.errors.password ? errorData.errors.password[0] : ''
    errors.value.confirm_password = errorData.errors.confirm_password ? errorData.errors.confirm_password[0] : ''
  } else {
    alert('Registration failed: ' + errorData.message)
  }
}

const handleRegister = async () => {
  if (isFieldsValid()) {
    is_calling_api.value = true
    try {
      const response = await registerUser(credentials)

      is_calling_api.value = false
      clearErrorsAndFields()

      // Show success dialog
      $q.dialog({
        title: 'Registration Successful',
        message: response.message || 'You have successfully registered.',
        ok: true,
      }).onOk(() => {
        $router.push({ name: 'LoginPage' })
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
}
</script>