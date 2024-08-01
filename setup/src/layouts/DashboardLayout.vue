<template>
  <q-layout view="lHh Lpr lFf">
    <q-header :class="$q.dark.isActive ? 'bg-dark text-white' : ''" class="text-dark q-py-sm" style="background-color: #ececf1" elevated>
      <q-toolbar>
        <q-btn flat dense round @click="toggleDrawer" icon="menu" aria-label="Menu" />

        <q-space />

        <div class="q-gutter-sm row items-center no-wrap">
          <!-- Switch Mode -->
          <q-btn color="primary" :icon="$q.dark.isActive ? 'brightness_7' : 'brightness_4'" class="q-mx-sm" @click="toggleSwitchMode" flat round dense />

          <!-- Refresh -->
          <q-btn color="primary" icon="refresh" class="q-mx-sm" @click="$router.go()" round dense flat />

          <!-- FullScreen -->
          <q-btn v-if="$q.screen.gt.sm" color="primary" :icon="$q.fullscreen.isActive ? 'fullscreen_exit' : 'fullscreen'" class="q-mx-sm" @click="$q.fullscreen.toggle()" round dense flat />

          <!-- Profile Menu -->
          <q-btn-dropdown color="primary" class="q-mx-sm" rounded dense flat push no-caps>
            <template v-slot:label>
              <q-skeleton v-if="loading" type="QAvatar" size="30px" />

              <q-avatar v-else size="30px">
                <img :src="url + '/avatars/' + profile.avatar" />
              </q-avatar>
            </template>
            <div>
              <q-list class="nav-profile">
                <q-item class="q-pr-lg" @click="navigateTo('home')" clickable v-close-popup dense>
                  <q-item-section>
                    <div>
                      <q-icon name="home" size="15px" class="q-pb-xs q-mx-xs" />
                      Beranda
                    </div>
                  </q-item-section>
                </q-item>

                <q-item class="q-pr-lg" @click="navigateTo('dashboard.profile')" clickable v-close-popup dense>
                  <q-item-section>
                    <div>
                      <q-icon name="manage_accounts" size="15px" class="q-pb-xs q-mx-xs" />
                      Edit Profile
                    </div>
                  </q-item-section>
                </q-item>

                <q-item class="q-pr-lg" @click="navigateTo('dashboard.changepassword')" clickable v-close-popup dense>
                  <q-item-section>
                    <div>
                      <q-icon name="key" size="15px" class="q-pb-xs q-mx-xs" />
                      Ganti Kata Sandi
                    </div>
                  </q-item-section>
                </q-item>

                <q-separator />

                <q-item class="q-pr-lg" @click="logout" clickable v-close-popup dense>
                  <q-item-section>
                    <div class="text-red">
                      <q-icon name="power_settings_new" size="15px" class="q-pb-xs q-mx-xs" />
                      Logout
                    </div>
                  </q-item-section>
                </q-item>
              </q-list>
            </div>
          </q-btn-dropdown>
        </div>
      </q-toolbar>
    </q-header>

    <q-drawer v-model="leftDrawerOpen" :mini="miniState" :width="250" :class="$q.dark.isActive ? 'bg-dark' : 'bg-grey-10'" class="text-white" bordered show-if-above>
      <q-list>
        <!-- Brand -->
        <q-item class="row justify-center">
          <q-item-section avatar>
            <div v-if="!miniState" class="text-h4 text-center text-bold q-py-lg" style="letter-spacing: 5px">{{ setting.title }}</div>
            <img v-if="miniState" :src="url + '/settings/' + setting.logo" width="50" class="q-pa-xs" />
          </q-item-section>
        </q-item>

        <q-separator dark />

        <q-scroll-area style="height: 86vh">
          <!-- MAIN -->
          <q-separator class="q-mb-md" />
          <div class="menu-text-header q-mx-md q-mb-sm" style="font-size: 11px"><span v-if="!miniState">MAIN</span></div>

          <!-- Home -->
          <q-item :to="{ name: 'dashboard.home' }" active-class="q-item-no-link-highlighting menu-active" class="menu-click menu-text q-py-md">
            <q-item-section avatar>
              <q-icon name="home" size="20px" />
            </q-item-section>
            <q-item-section>Beranda</q-item-section>
          </q-item>

          <!-- User -->
          <q-item :to="{ name: 'dashboard.user' }" active-class="q-item-no-link-highlighting menu-active" class="menu-click menu-text q-py-md">
            <q-item-section avatar>
              <q-icon name="account_circle" size="20px" />
            </q-item-section>
            <q-item-section>Pengguna</q-item-section>
          </q-item>

          <!-- PENGATURAN -->
          <q-separator class="q-mb-md q-mt-sm" />
          <div class="menu-text-header q-mx-md q-mb-sm" style="font-size: 11px"><span v-if="!miniState">PENGATURAN</span></div>

          <!-- Website -->
          <q-item :to="{ name: 'dashboard.setting' }" active-class="q-item-no-link-highlighting menu-active" class="menu-click menu-text q-py-md">
            <q-item-section avatar>
              <q-icon name="language" size="20px" />
            </q-item-section>
            <q-item-section>Website</q-item-section>
          </q-item>

          <!-- LAINNYA -->
          <q-separator class="q-mb-md q-mt-sm" />
          <div class="menu-text-header q-mx-md q-mb-sm" style="font-size: 11px"><span v-if="!miniState">LAINNYA</span></div>

          <!-- Back -->
          <q-item :to="{ name: 'home' }" active-class="q-item-no-link-highlighting menu-active" class="menu-click menu-text q-py-md">
            <q-item-section avatar>
              <q-icon name="reply" size="20px" />
            </q-item-section>
            <q-item-section>Lihat Website</q-item-section>
          </q-item>
        </q-scroll-area>
      </q-list>
    </q-drawer>

    <q-page-container :class="$q.dark.isActive ? 'bg-grey-10' : 'bg-grey-3'" style="min-height: 100vh">
      <router-view v-slot="{ Component }">
        <keep-alive>
          <component :is="Component" />
        </keep-alive>
      </router-view>
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { url } from '/src/boot/axios'
import { useSettingStore } from '/src/stores/setting-store'
import { useProfileStore } from '/src/stores/profile-store'
import { useAuthStore } from '/src/stores/auth-store'

const $q = useQuasar()
const router = useRouter()

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
  getProfile()
})

// Sidebar
const leftDrawerOpen = ref(false)
const miniState = ref(false)
const desktop = ref(false)
const detectDesktop = () => {
  desktop.value = window.innerWidth > 1023
}
const toggleDrawer = () => {
  if (desktop.value) {
    miniState.value = !miniState.value
  } else {
    leftDrawerOpen.value = !leftDrawerOpen.value
  }
}
onMounted(() => {
  detectDesktop()
  window.addEventListener('resize', detectDesktop)
})
onBeforeUnmount(() => {
  window.removeEventListener('resize', detectDesktop)
})

// Switch Mode
const darkmode = localStorage.getItem('darkmode') || 'false'
const isDarkMode = ref(JSON.parse(darkmode))
$q.dark.set(isDarkMode.value)
const toggleSwitchMode = () => {
  const newMode = !isDarkMode.value
  $q.dark.set(newMode)

  isDarkMode.value = newMode
  localStorage.setItem('darkmode', newMode)
}

// Navigate
const navigateTo = (name, params) => {
  router.push({ name: name, params: params })
}

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
