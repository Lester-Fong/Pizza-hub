<template>
  <q-page class="flex flex-center fit auth">
    <q-card class="app-card-glass absolute-center text-black w-30 q-pa-md h-60 flex column justify-between">
      <q-card-section class="text-h6 text-center"> Login </q-card-section>

      <div class="my-auto">
        <q-card-section>
          <q-input v-model="username" label="Username" outlined standout="bg-teal text-white" class="mb-2 auth-input" />
          <q-input v-model="password" type="password" standout="bg-teal text-white" class="auth-input" label="Password" outlined />
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
import { ref } from 'vue'
const username = ref('')
const password = ref('')

const login = () => {
  if (username.value && password.value) {
    console.log(`Logging in with username: ${username.value} and password: ${password.value}`)
  } else {
    console.error('Username and password are required')
  }
}
</script>