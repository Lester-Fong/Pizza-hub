<template>
  <div class="min-vh-100">
    <q-layout class="shadow-2 rounded-borders">
      <q-header elevated class="flex justify-between align-center w-auto" :class="$q.dark.isActive ? 'bg-secondary' : 'bg-black'">
        <q-toolbar class="w-auto">
          <q-btn flat @click="drawer = !drawer" round dense icon="menu" />
          <q-toolbar-title>Pizza Hub</q-toolbar-title>
        </q-toolbar>

        <q-btn flat @click="handleLogout" class="q-ml-auto" icon="logout" label="Logout" />
      </q-header>

      <q-drawer v-model="drawer" show-if-above :width="200" :breakpoint="500" bordered :class="$q.dark.isActive ? 'bg-grey-9' : 'bg-grey-3'">
        <q-scroll-area class="fit">
          <q-list class="">
            <template v-for="(menuItem, index) in menuList" :key="index">
              <router-link class="text-decoration-none" :to="{ name: menuItem.namePath }">
                <q-item clickable :active="menuItem.label === 'Outbox'" v-ripple>
                  <q-item-section avatar>
                    <q-icon :name="menuItem.icon" />
                  </q-item-section>
                  <q-item-section>
                    {{ menuItem.label }}
                  </q-item-section>
                </q-item>
              </router-link>
              <q-separator :key="'sep' + index" v-if="menuItem.separator" />
            </template>
          </q-list>
        </q-scroll-area>
      </q-drawer>

      <q-page-container>
        <router-view :user="props.user" />
      </q-page-container>
    </q-layout>
  </div>
</template>
<script setup>
import { ref } from 'vue'
const drawer = ref(false)
import { useQuasar } from 'quasar'
import { useRouter } from 'vue-router'
import { handleLogoutProcess } from 'src/boot/axios'

const $q = useQuasar()
const $router = useRouter()
const menuList = [
  {
    label: 'dashboard',
    icon: 'dashboard',
    separator: true,
    namePath: 'DashboardPage',
  },
]

let props = defineProps({
  user: {
    type: Object,
    default: () => ({}),
  },
})

const handleLogout = () => {
  $q.dialog({
    title: 'Logout',
    message: 'Are you sure you want to logout?',
    cancel: true,
    persistent: true,
    ok: {
      push: true,
      label: 'Yes',
      color: 'primary',
    },
  }).onOk(async () => {
    await handleLogoutProcess()
    $router.push({ name: 'LoginPage' })
  })
}
</script>

<style scoped>
a {
  text-decoration: none;
  color: black;
}
a:hover {
  text-decoration: none;
  color: black;
}
</style>