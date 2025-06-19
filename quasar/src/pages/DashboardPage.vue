<template>
  <div v-if="is_calling_api" class="app-loader">
    <q-spinner-hourglass color="purple" size="5em" />
  </div>
  <div v-else>
    <DashboardCSVForm v-if="isEmptyDB" @setIsEmptyDB="setIsEmptyDB" />

    <div v-else class="q-pa-md">
      <h2 class="text-center">Dashboard</h2>
      <p class="text-center q-mb-md">View your sales data</p>

      <div class="row justify-between mb-2 gap-2 q-col-gutter-x-md">
        <!-- Total Revenue vs Orders (Doughnut Chart) -->
        <q-card class="q-mb-md col-12 col-md-6">
          <q-card-section>
            <div class="text-h6">Revenue vs Orders</div>
          </q-card-section>
          <q-card-section>
            <Chart v-if="revenueOrdersData" :data="revenueOrdersData" :options="chartOptions" type="doughnut" />
          </q-card-section>
        </q-card>
        <!-- Top 5 Pizzas (Bar Chart) -->
        <q-card class="q-mb-md col-12 col-md-6">
          <q-card-section>
            <div class="text-h6">Top 5 Pizzas by Quantity</div>
          </q-card-section>
          <q-card-section>
            <Chart v-if="topPizzasData" :data="topPizzasData" :options="chartOptions" style="height: 310px" type="bar" />
          </q-card-section>
        </q-card>
      </div>

      <!-- Daily Sales Trend (Line Chart) -->
      <q-card>
        <q-card-section>
          <div class="text-h6">Daily Sales Trend</div>
        </q-card-section>
        <q-card-section>
          <Chart v-if="dailySalesData" :data="dailySalesData" :options="chartOptions" type="line" />
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