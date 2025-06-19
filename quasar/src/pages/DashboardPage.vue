<template>
  <q-page class="flex flex-center">
    <q-card class="q-pa-md" style="max-width: 400px; width: 100%">
      <q-card-section>
        <div class="text-h6">Dashboard</div>
        <div class="text-subtitle2">Welcome to your dashboard!</div>
      </q-card-section>

      <q-card-section>
        <p>This is a placeholder for your dashboard content.</p>
        <p>You can add charts, tables, or any other components you need.</p>
      </q-card-section>

      <q-card-actions align="right">
        <q-btn color="primary" label="Go to Profile" @click="$router.push('/profile')" />
      </q-card-actions>
    </q-card>
  </q-page>
</template>

<script setup></script>