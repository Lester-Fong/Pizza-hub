<template>
  <q-page class="flex flex-center">
    <div v-if="is_calling_api" class="app-loader">
      <q-spinner-hourglass color="purple" size="5em" />
    </div>
    <q-card class="q-pa-md" style="max-width: 400px; width: 100%">
      <q-card-section>
        <div class="text-h6">Dashboard</div>
        <div class="text-subtitle2">Welcome to your dashboard!</div>
      </q-card-section>

      <q-card-section>
        <p class="mb-0 text-body2 text-weight-medium">To start, please import the necessary CSV files</p>
        <sub class="text-weight-medium">(eg: Pizza, Pizza Types, Orders and Order Details)</sub>
      </q-card-section>

      <q-card-actions>
        <div class="w-100 mb-3">
          <q-file filled bottom-slots v-model="pizza_type_file" label="Pizza Types" counter accept=".csv">
            <template v-slot:prepend>
              <q-icon name="cloud_upload" @click.stop.prevent />
            </template>
            <template v-slot:append>
              <q-icon name="close" @click.stop.prevent="pizza_type_file = null" class="cursor-pointer" />
            </template>
            <template v-slot:hint>Pizza Type</template>
          </q-file>
        </div>
        <div class="w-100 mb-2">
          <q-file filled bottom-slots v-model="pizza_file" label="Pizza" counter accept=".csv">
            <template v-slot:prepend>
              <q-icon name="cloud_upload" @click.stop.prevent />
            </template>
            <template v-slot:append>
              <q-icon name="close" @click.stop.prevent="pizza_file = null" class="cursor-pointer" />
            </template>
            <template v-slot:hint>Pizza</template>
          </q-file>
        </div>
        <div class="w-100 mb-2">
          <q-file filled bottom-slots v-model="orders_file" label="Orders" counter accept=".csv">
            <template v-slot:prepend>
              <q-icon name="cloud_upload" @click.stop.prevent />
            </template>
            <template v-slot:append>
              <q-icon name="close" @click.stop.prevent="orders_file = null" class="cursor-pointer" />
            </template>
            <template v-slot:hint>Orders</template>
          </q-file>
        </div>
        <div class="w-100 mb-2">
          <q-file filled bottom-slots v-model="order_details_file" label="Order Details" counter accept=".csv">
            <template v-slot:prepend>
              <q-icon name="cloud_upload" @click.stop.prevent />
            </template>
            <template v-slot:append>
              <q-icon name="close" @click.stop.prevent="order_details_file = null" class="cursor-pointer" />
            </template>
            <template v-slot:hint>Order Details</template>
          </q-file>
        </div>
      </q-card-actions>

      <q-card-actions>
        <q-btn color="primary" label="Import" class="full-width" @click="handleAllImportProcess" :disabled="!pizza_type_file || !pizza_file || !orders_file || !order_details_file" />
        <div class="text-center q-mt-md" v-if="statusMessage">{{ statusMessage }}</div>
      </q-card-actions>
    </q-card>
  </q-page>
</template>

<script setup>
import { ref } from 'vue'
import { handlePromiseFiles } from 'boot/axios'
import { useQuasar } from 'quasar'

const $q = useQuasar()
const pizza_type_file = ref(null)
const pizza_file = ref(null)
const orders_file = ref(null)
const order_details_file = ref(null)
const statusMessage = ref('')
const is_calling_api = ref(false)

const emit = defineEmits(['setIsEmptyDB'])

const handleAllImportProcess = async () => {
  const importFiles = [
    { file: pizza_type_file.value, endpoint: '/api/import-pizza-types' },
    { file: pizza_file.value, endpoint: '/api/import-pizza' },
    { file: orders_file.value, endpoint: '/api/import-orders' },
    { file: order_details_file.value, endpoint: '/api/import-order-details' },
  ]

  $q.notify({ message: 'Starting imports...', color: 'positive' })
  try {
    is_calling_api.value = true
    const response = await handlePromiseFiles(importFiles)
    const pizza = response[0] || null
    const pizza_type = response[1] || null
    const orders = response[2] || null
    const order_details = response[3] || null
    if (pizza || pizza_type || orders || order_details) {
      $q.notify({ message: 'Import successful!', color: 'positive' })
      handleClearFiles()
      is_calling_api.value = false
      emit('setIsEmptyDB', false) // Notify parent component that DB is no longer empty
    } else {
      $q.notify({ message: 'No data imported. Please try again.', color: 'negative' })
    }
  } catch (error) {
    statusMessage.value = `Import failed: ${error.response?.data?.message || error.message}`
    $q.notify({ message: statusMessage.value, color: 'negative' })
  }
}

const handleClearFiles = () => {
  pizza_type_file.value = null
  pizza_file.value = null
  orders_file.value = null
  order_details_file.value = null
}
</script>


<style>
.q-field__label {
  font-family: 'Montserrat', sans-serif;
  font-weight: 500;
  color: #1f0404 !important;
}
</style>