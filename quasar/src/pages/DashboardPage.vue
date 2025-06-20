<template>
  <div v-if="is_calling_api" class="app-loader">
    <q-spinner-hourglass color="purple" size="5em" />
  </div>
  <div v-else>
    <DashboardCSVForm v-if="isEmptyDB" @setIsEmptyDB="setIsEmptyDB" />

    <div v-else class="q-pa-md min-vh-92">
      <q-card class="bg-teal-6 shadow-2 rounded-borders h-100">
        <q-card-section class="text-white m-0">
          <div class="text-weight-light text-subtitle1">Hi {{ props?.user?.name }}, Welcome! 👋</div>
          <div class="text-h5">Your Dashboard Today</div>
        </q-card-section>
        <q-card-section>
          <div class="row justify-between mb-2 gap-2 q-col-gutter-x-md">
            <div class="col-3">
              <q-card class="bg-white text-black shadow-2 rounded-borders">
                <q-card-section class="text-center">
                  <div class="text-h6">Total Orders</div>
                  <div class="text-h4">{{ total_orders }}</div>
                </q-card-section>
              </q-card>
            </div>
            <div class="col-3">
              <q-card class="bg-white text-black shadow-2 rounded-borders">
                <q-card-section class="text-center">
                  <div class="text-h6">Total Pizza</div>
                  <div class="text-h4">{{ total_pizzas }}</div>
                </q-card-section>
              </q-card>
            </div>
            <div class="col-3">
              <q-card class="bg-white text-black shadow-2 rounded-borders">
                <q-card-section class="text-center">
                  <div class="text-h6">Total Pizza Types</div>
                  <div class="text-h4">{{ total_pizza_types }}</div>
                </q-card-section>
              </q-card>
            </div>
            <div class="col-3">
              <q-card class="bg-white text-black shadow-2 rounded-borders">
                <q-card-section class="text-center">
                  <div class="text-h6">Total Sales</div>
                  <div class="text-h4">${{ total_sales.toFixed(2) }}</div>
                </q-card-section>
              </q-card>
            </div>
          </div>

          <div class="row justify-between mb-2 gap-2 q-col-gutter-x-md">
            <!-- Total Revenue vs Orders (Doughnut Chart) -->
            <div class="col-12 col-md-6">
              <q-card class="h-100">
                <q-card-section>
                  <div class="text-h6">Revenue vs Orders</div>
                </q-card-section>
                <q-card-section>
                  <Chart v-if="revenueOrdersData" :data="revenueOrdersData" :options="chartOptions" type="doughnut" />
                </q-card-section>
              </q-card>
            </div>
            <!-- Top 5 Pizzas (Bar Chart) -->
            <div class="col-12 col-md-6">
              <q-card class="">
                <q-card-section>
                  <div class="text-h6">Top 5 Pizzas by Quantity</div>
                </q-card-section>
                <q-card-section>
                  <Chart v-if="topPizzasData" :data="topPizzasData" :options="chartOptions" style="height: 310px" type="bar" />
                </q-card-section>
              </q-card>
            </div>
          </div>

          <!-- Daily Sales Trend (Line Chart) -->
          <q-card>
            <q-card-section>
              <div class="text-h6">Daily Sales Trend</div>
            </q-card-section>
            <q-card-section>
              <Chart v-if="dailySalesData" :data="dailySalesData" :options="chartOptions" style="height: 350px" type="line" />
            </q-card-section>
          </q-card>
        </q-card-section>
      </q-card>
    </div>
  </div>
</template>

<script setup>
import DashboardCSVForm from 'src/components/DashboardCSVForm.vue'
import { onMounted, ref } from 'vue'
import { getSummaryData, getTrendResponse, getDashboardData } from 'src/boot/axios'
import { Chart } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement, BarElement, BarController, CategoryScale, LinearScale, PointElement, LineElement, LineController } from 'chart.js'
import { formatDate } from 'src/utils/helper'

ChartJS.register(Title, Tooltip, Legend, ArcElement, BarElement, BarController, CategoryScale, LinearScale, PointElement, LineElement, LineController)
const isEmptyDB = ref(true)
const revenueOrdersData = ref(null)
const topPizzasData = ref(null)
const dailySalesData = ref(null)
const is_calling_api = ref(true)
const pizza_arr = ref([])
const pizza_types_arr = ref([])
const orders_arr = ref([])
const order_details_arr = ref([])
const total_pizzas = ref(0)
const total_pizza_types = ref(0)
const total_orders = ref(0)
const total_sales = ref(0)
const props = defineProps({
  user: {
    type: Object,
    default: () => ({}),
  },
})

onMounted(async () => {
  try {
    // Check if the database is empty
    const response = await getDashboardData()

    pizza_arr.value = response[0]
    pizza_types_arr.value = response[1]
    orders_arr.value = response[2]
    order_details_arr.value = response[3]

    isEmptyDB.value = !(pizza_arr.value.length > 0 && pizza_types_arr.value.length > 0 && orders_arr.value.length > 0 && order_details_arr.value.length > 0)
    if (!isEmptyDB.value) {
      await onPopulateDashboardData()
      total_pizzas.value = pizza_arr.value.length
      total_pizza_types.value = pizza_types_arr.value.length
      total_orders.value = orders_arr.value.length
    }
    is_calling_api.value = false
  } catch (error) {
    console.error('Error fetching dashboard data:', error)
    isEmptyDB.value = true
  }
})

const onPopulateDashboardData = async () => {
  is_calling_api.value = true
  const response = await getSummaryData()
  const { total_orders, total_revenue, top_pizzas } = response

  total_sales.value = total_revenue || 0
  revenueOrdersData.value = {
    labels: ['Revenue', 'Orders'],
    datasets: [{ data: [total_revenue || 0, total_orders || 0], backgroundColor: ['#FF6384', '#36A2EB'] }],
  }

  topPizzasData.value = {
    labels: top_pizzas.map((p) => p.name),
    datasets: [{ label: 'Quantity Sold', data: top_pizzas.map((p) => p.total_quantity), backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'] }],
  }

  const trendResponse = await getTrendResponse()
  const trendData = trendResponse

  dailySalesData.value = {
    labels: trendData.map((d) => formatDate(d.date)),
    datasets: [{ label: 'Sales', data: trendData.map((d) => d.sales), borderColor: '#36A2EB', fill: false }],
  }

  isEmptyDB.value = false
  is_calling_api.value = false
}

const setIsEmptyDB = (value) => {
  isEmptyDB.value = value
}

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
}
</script>