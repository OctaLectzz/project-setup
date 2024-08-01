<template>
  <q-card style="border-radius: 30px">
    <q-card-section class="row items-center q-pb-none">
      <q-space />
      <q-btn icon="close" flat round dense v-close-popup />
    </q-card-section>

    <q-card-section class="scroll q-pb-xl" style="max-height: 77vh">
      <div class="row justify-center q-my-sm">
        <q-img :src="url + '/settings/' + setting.logo" :style="'width:' + setting.logo_size + 'px'" />
      </div>

      <div class="text-h5 text-bold text-center q-mb-lg">Selamat datang di {{ setting.title }}</div>

      <q-form @submit="register">
        <div class="row justify-center">
          <!-- Username -->
          <div class="col-sm-5 col-xs-10 q-pa-sm">
            <label for="username">Username</label>
            <q-input v-model="username" id="username" label="Username" :rules="rules.username" outlined dense />
          </div>

          <!-- Name -->
          <div class="col-sm-5 col-xs-10 q-pa-sm">
            <label for="name">Nama</label>
            <q-input v-model="name" id="name" label="Nama" :rules="rules.name" outlined dense />
          </div>

          <!-- Email -->
          <div class="col-10 q-pa-sm">
            <label for="email">Email</label>
            <q-input type="email" v-model="email" id="email" label="Email" :rules="rules.email" outlined dense />
          </div>

          <!-- Kata Sandi -->
          <div class="col-sm-5 col-xs-10 q-pa-sm">
            <label for="password">Kata Sandi</label>
            <q-input type="password" v-model="password" id="password" label="Kata Sandi" :rules="rules.password" outlined dense />
          </div>

          <!-- Confirm Password -->
          <div class="col-sm-5 col-xs-10 q-pa-sm">
            <label for="confirm_password">Konfirmasi Kata Sandi</label>
            <q-input type="password" v-model="confirm_password" id="confirm_password" label="Konfirmasi Kata Sandi" :rules="rules.confirm_password" outlined dense />
          </div>

          <!-- Submit -->
          <div class="col-10 q-pa-sm">
            <q-btn type="submit" color="primary" label="Daftar" class="full-width q-mt-sm" :loading="loading" :disable="loading" rounded>
              <template v-slot:loading>
                <q-spinner-hourglass class="on-left" />
                Loading...
              </template>
            </q-btn>
          </div>
        </div>
      </q-form>

      <!-- Terms -->
      <div class="q-mt-lg text-center text-grey-7" style="font-size: 11px">
        <div>Dengan melanjutkan, Anda setuju dengan</div>
        <div>
          <a href="#" target="_blank" rel="noopener noreferrer" class="permission-link text-primary">Terms of Service</a>
          dan menyadari bahwa Anda telah membaca
        </div>
        <div>
          <a href="#" target="_blank" rel="noopener noreferrer" class="permission-link text-primary">Privacy Policy</a>
          .
          <a href="#" target="_blank" rel="noopener noreferrer" class="permission-link text-primary">Notice at collection</a>
          .
        </div>
      </div>

      <!-- Login -->
      <div class="q-mt-lg text-center" style="font-size: 12px">
        Sudah menjadi anggota?
        <span class="permission-link text-primary text-bold cursor-pointer" @click="openLoginDialog">Masuk</span>
      </div>
    </q-card-section>
  </q-card>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import { url } from '/src/boot/axios'
import { useAuthStore } from '/src/stores/auth-store'
import { useUserStore } from '/src/stores/user-store'
import { useSettingStore } from '/src/stores/setting-store'

const $q = useQuasar()
const emits = defineEmits(['login', 'verify'])

// Get Setting
const setting = ref({})
const getSetting = async () => {
  try {
    const res = await useSettingStore().show(1)

    setting.value = res.data.data
  } catch (error) {
    console.error('Error fetching data:', error)

    if (error.response.data.status === 'failed') {
      router.push('/notfound')
    }
  }
}
onMounted(() => {
  getSetting()
})

// Data
const username = ref('')
const name = ref('')
const email = ref('')
const password = ref('')
const confirm_password = ref('')

// Get User
const users = ref([])
const getUser = async () => {
  try {
    const res = await useUserStore().all()

    users.value = res.data.data
  } catch (error) {
    console.error('Error fetching data:', error)
  }
}
onMounted(() => {
  getUser()
})

// Validate
const rules = ref({
  username: [
    (v) => !!v || 'Username harus diisi',
    (v) => v.length <= 20 || 'Username maksimal 20 karakter',
    (v) => {
      if (typeof v === 'string') {
        if (users.value) {
          return !users.value.some((user) => user.username.toLowerCase() === v.toLowerCase()) || 'Username sudah ada'
        }
      }
      return true
    }
  ],
  name: [(v) => !!v || 'Nama harus diisi', (v) => v.length <= 50 || 'Nama maksimal 50 karakter'],
  email: [
    (v) => !!v || 'Email harus diisi',
    (v) => /.+@.+/.test(v) || 'Email tidak valid',
    (v) => {
      if (typeof v === 'string') {
        if (users.value) {
          return !users.value.some((user) => user.email.toLowerCase() === v.toLowerCase()) || 'Email sudah ada'
        }
      }
      return true
    }
  ],
  password: [(v) => !!v || 'Kata Sandi harus diisi', (v) => v.length >= 8 || 'Kata Sandi minimal 8 karakter'],
  confirm_password: [(v) => !!v || 'Konfirmasi Kata Sandi harus diisi', (v) => v === password.value || 'Kata Sandi tidak sama']
})

// Register
const loading = ref(false)
const register = async () => {
  loading.value = true
  try {
    await useAuthStore().register(username.value, name.value, email.value, password.value, confirm_password.value)

    $q.notify({
      message: 'Registrasi Berhasil, Silakan verifikasi email Anda',
      icon: 'check',
      color: 'positive'
    })
    emits('verify')
  } catch (error) {
    console.error('Error submitting form:', error)

    $q.notify({
      message: error.response.data.message || 'Registrasi gagal, Silahkan coba lagi nanti',
      icon: 'warning',
      color: 'negative'
    })
  }
  loading.value = false
}

// Login
const openLoginDialog = () => {
  emits('login')
}
</script>
