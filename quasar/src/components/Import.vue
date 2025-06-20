<!-- src/pages/ImportPage.vue -->
<template>
  <q-page class="q-pa-md">
    <h4>Import CSV Files</h4>
    <q-form @submit="handleImport" class="q-gutter-md">
      <q-file v-model="file" label="Select CSV File" accept=".csv" />
      <q-select v-model="selectedEndpoint" :options="endpointOptions" label="Select Import Type" />
      <q-btn label="Upload" type="submit" color="primary" />
    </q-form>
    <div class="q-mt-md">
      <p>Status: {{ status }}</p>
      <q-linear-progress v-if="progress > 0" :value="progress / 100" color="primary" />
      <p v-if="error">{{ error }}</p>
    </div>
  </q-page>
</template>

<script>
import { ref } from 'vue'
import { api } from 'src/boot/axios'
import { useQuasar } from 'quasar'

export default {
  name: 'ImportPage',
  setup() {
    const $q = useQuasar()
    const file = ref(null)
    const selectedEndpoint = ref(null)
    const status = ref('Waiting...')
    const progress = ref(0)
    const error = ref(null)
    const jobId = ref(null)

    const endpointOptions = [
      { label: 'Orders', value: '/api/import-orders' },
      { label: 'Order Details', value: '/api/import-order-details' },
      { label: 'Pizzas', value: '/api/import-pizza' },
      { label: 'Pizza Types', value: '/api/import-pizza-types' },
    ]

    const handleImport = async () => {
      if (!file.value || !selectedEndpoint.value) {
        $q.notify({ message: 'Please select a file and import type', color: 'negative' })
        return
      }

      const formData = new FormData()
      formData.append('csv_file', file.value)

      try {
        const response = await api.post(selectedEndpoint.value, formData, {
          headers: { Authorization: `Bearer ${localStorage.getItem('authToken')}` },
        })
        jobId.value = response.data.job_id
        status.value = 'Processing...'
        await checkStatus()
      } catch (err) {
        error.value = err.response?.data?.message || 'Import failed'
        $q.notify({ message: error.value, color: 'negative' })
      }
    }

    const checkStatus = async () => {
      if (!jobId.value) return

      while (true) {
        const response = await api.get(`/api/import-status/${jobId.value}`, {
          headers: { Authorization: `Bearer ${localStorage.getItem('authToken')}` },
        })
        const { status: jobStatus, progress: jobProgress } = response.data
        status.value = `Status: ${jobStatus}`
        progress.value = parseFloat(jobProgress)
        if (jobStatus === 'completed') {
          $q.notify({ message: 'Import completed!', color: 'positive' })
          break
        } else if (jobStatus === 'failed') {
          error.value = 'Import failed'
          $q.notify({ message: error.value, color: 'negative' })
          break
        }
        await new Promise((resolve) => setTimeout(resolve, 1000)) // Poll every second
      }
    }

    return { file, selectedEndpoint, status, progress, error, handleImport, endpointOptions }
  },
}
</script>

<style scoped>
.q-linear-progress {
  height: 10px;
  width: 300px;
}
</style>