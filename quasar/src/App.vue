<template>
  <div v-if="is_calling_api" class="app-loader">
    <q-spinner-hourglass color="purple" size="5em" />
  </div>
  <div v-else>
    <router-view :user="user" />
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useQuasar } from 'quasar'
import { getUser } from 'src/boot/axios'
const $q = useQuasar()
const is_calling_api = ref(true)

const user = ref(null)

onMounted(async () => {
  const accessToken = $q.localStorage.getItem('access_token')

  if (accessToken) {
    is_calling_api.value = true
    const response = await getUser()
    if (response) {
      user.value = response
      is_calling_api.value = false
    } else {
      console.error('Failed to fetch user data')
      is_calling_api.value = false
    }
  }
})
</script>
