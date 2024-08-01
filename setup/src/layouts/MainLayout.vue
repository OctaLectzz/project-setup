<template>
  <q-layout view="lHh Lpr lFf">
    <!-- Large Screen -->
    <q-header class="q-pa-md">
      <div class="row items-center justify-between">
        <!-- Left -->
        <div class="q-mx-lg">
          <q-img :src="url + '/settings/' + setting.logo" :style="'width:' + setting.logo_size + 'px'" />
        </div>

        <!-- Right -->
        <div class="col-auto q-my-sm">
          <!-- Loading -->
          <q-skeleton v-if="loading" width="160px" height="46px" />

          <!-- Dashboard -->
          <q-btn v-if="token && profile.role == 'Admin'" :to="{ name: 'dashboard.home' }" color="white" size="lg" label="Dashboard" class="q-mx-xs" flat no-caps dense />

          <!-- Logout -->
          <q-btn v-if="token && !loading" color="red" icon="power_settings_new" class="q-mx-xs" @click="logout" flat round dense />

          <!-- Register -->
          <q-btn v-if="!token" color="white" size="lg" label="Daftar" class="q-mx-xs" @click="email ? (verifyDialog = true) : (registerDialog = true)" flat no-caps dense>
            <q-dialog v-model="registerDialog" persistent>
              <RegisterDialog @verify="openVerify" @login="openLogin" />
            </q-dialog>
            <q-dialog v-model="verifyDialog" persistent>
              <VerifyDialog @login="openLogin" />
            </q-dialog>
          </q-btn>

          <!-- Login -->
          <q-btn v-if="!token" color="green" size="lg" label="Masuk" class="q-mx-xs" @click="loginDialog = true" flat no-caps dense>
            <q-dialog v-model="loginDialog" persistent>
              <LoginDialog @register="openRegister" />
            </q-dialog>
          </q-btn>
        </div>
      </div>
    </q-header>

    <!-- Pages -->
    <q-page-container>
      <router-view v-slot="{ Component }">
        <keep-alive>
          <component :is="Component" />
        </keep-alive>
      </router-view>
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import { url, token } from '/src/boot/axios'
import { useSettingStore } from '/src/stores/setting-store'
import { useProfileStore } from '/src/stores/profile-store'
import { useAuthStore } from '/src/stores/auth-store'
import LoginDialog from '/src/components/LoginDialog.vue'
import RegisterDialog from '/src/components/RegisterDialog.vue'
import VerifyDialog from '/src/components/VerifyDialog.vue'

const $q = useQuasar()
const navlink = ref(false)

// Get Setting
const setting = ref({})
const getSetting = async () => {
  try {
    const res = await useSettingStore().show(1)

    setting.value = res.data.data
  } catch (error) {
    console.error('Error fetching data:', error)
  }
}
onMounted(() => {
  getSetting()
})

// Auth
const email = localStorage.getItem('email')
const registerDialog = ref(false)
const openRegister = () => {
  loginDialog.value = false
  registerDialog.value = true
}
const verifyDialog = ref(false)
const openVerify = () => {
  registerDialog.value = false
  verifyDialog.value = true
}
const loginDialog = ref(false)
const openLogin = () => {
  registerDialog.value = false
  loginDialog.value = true
}

// Profile
const profile = ref({})
const loading = ref(false)
const getProfile = async () => {
  loading.value = true
  try {
    const res = await useProfileStore().profile()

    profile.value = res.data.data
  } catch (error) {
    console.error('Error fetching data:', error)

    $q.notify({
      message: 'Sesi Habis',
      icon: 'warning',
      color: 'negative'
    })
    localStorage.removeItem('token')
    localStorage.removeItem('role')
    window.location.reload()
  }
  loading.value = false
}
onMounted(() => {
  if (token) {
    getProfile()
  }
})

// Logout
const logout = async () => {
  $q.dialog({
    title: 'Keluar',
    message: 'Apakah anda yakin?',
    cancel: true,
    persistent: true
  }).onOk(async () => {
    try {
      await useAuthStore().logout()

      $q.notify({
        message: 'Logout Berhasil',
        icon: 'check',
        color: 'positive'
      })
    } catch (error) {
      console.error('Error submitting form:', error)

      $q.notify({
        message: error.response.data.message || 'Logout Gagal',
        icon: 'warning',
        color: 'negative'
      })
    }
  })
}
</script>
