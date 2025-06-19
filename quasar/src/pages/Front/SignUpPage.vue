<template>
  <q-page class="flex flex-center fit auth">
    <q-card class="app-card-glass absolute-center text-black w-30 q-pa-md h-60 flex column justify-between">
      <q-card-section class="text-h6 text-center"> Sign Up </q-card-section>

      <div class="my-auto">
        <q-card-section>
          <q-input v-model="username" label="Username" outlined standout="bg-teal text-white" class="mb-2 auth-input" />
          <q-input v-model="password" type="password" standout="bg-teal text-white" class="mb-2 auth-input" label="Password" outlined />
          <q-input v-model="confirmPassword" type="password" standout="bg-teal text-white" class="auth-input" label="Confirm Password" outlined />
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
import { ref } from 'vue'
import { registerUser } from '.././../boot/axios'
const username = ref('')
const password = ref('')
const confirmPassword = ref('')

// const isFieldsValid = () => {
//   let isValid = true
//   if (!username.value) {
//     console.error('Username is required')
//     isValid = false
//   }
//   if (!password.value) {
//     console.error('Password is required')
//     isValid = false
//   }
//   if (!confirmPassword.value) {
//     console.error('Confirm Password is required')
//     isValid = false
//   }
//   if (password.value !== confirmPassword.value) {
//     console.error('Passwords do not match')
//     isValid = false
//   }
//   return isValid
// }

const handleRegister = async () => {
  //   if (!isFieldsValid()) {
  const response = await registerUser({ username: username.value, password: password.value })
  console.log('Registration response:', response)
  //   } else {
  // console.error('Please fill in all fields correctly')
  //   }
}
</script>