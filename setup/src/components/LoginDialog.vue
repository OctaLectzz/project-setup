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

      <div class="text-h5 text-bold text-center q-mb-lg">Selamat Datang di {{ setting.title }}</div>

      <q-form @submit="submitForm">
        <div class="row justify-center">
          <!-- Email -->
          <div class="col-sm-8 col-xs-11">
            <label for="email">Email</label>
            <q-input type="email" v-model="email" label="Email" :rules="rules.email" v-lowercase outlined dense />
          </div>

          <!-- Password -->
          <div class="col-sm-8 col-xs-11">
            <label for="password">Kata Sandi</label>
            <q-input type="password" v-model="password" label="Kata Sandi" :rules="rules.password" outlined dense />
          </div>

          <!-- Submit -->
          <div class="col-sm-8 col-xs-11 q-mt-sm">
            <q-btn type="submit" color="primary" label="Masuk" class="full-width q-mt-sm" :loading="loading" :disable="loading" @click="submitForm">
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

      <!-- Register -->
      <div class="q-mt-lg text-center" style="font-size: 12px">
        Belum bergabung di {{ setting.title }}?
        <span class="permission-link text-primary text-bold cursor-pointer" @click="openRegisterDialog">Daftar</span>
      </div>
    </q-card-section>
  </q-card>
</template>

<script setup>
import { ref, defineEmits, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import { url } from '/src/boot/axios'
import { useAuthStore } from '/src/stores/auth-store'
import { useSettingStore } from '/src/stores/setting-store'

const $q = useQuasar()
const emits = defineEmits(['register'])

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
const email = ref('')
const password = ref('')

// Validate
const rules = {
  email: [(v) => !!v || 'Email harus diisi', (v) => /.+@.+/.test(v) || 'Email tidak valid'],
  password: [(v) => !!v || 'Kata Sandi harus diisi', (v) => v.length >= 8 || 'Kata Sandi minimal 8 karakter']
}

// Login
const loading = ref(false)
const submitForm = async () => {
  loading.value = true
  try {
    const res = await useAuthStore().login(email.value, password.value)

    localStorage.setItem('token', res.data.data.token)
    localStorage.setItem('role', res.data.data.user.role)
    $q.notify({
      message: 'Login Berhasil',
      icon: 'check',
      color: 'positive'
    })
    window.location.reload()
  } catch (error) {
    console.error('Error submitting form:', error)

    $q.notify({
      message: error.response.data.message || 'Format Email dan Kata Sandi masih salah',
      icon: 'warning',
      color: 'negative'
    })
  }
  loading.value = false
}

// Register
const openRegisterDialog = () => {
  emits('register')
}
</script>
