<template>
  <q-card style="border-radius: 30px">
    <q-card-section class="row items-center q-pb-none">
      <q-space />
      <q-btn icon="close" flat round dense v-close-popup />
    </q-card-section>

    <q-card-section class="scroll q-pb-xl" style="max-height: 77vh">
      <div class="row justify-center">
        <div class="row justify-center q-my-sm">
          <q-img :src="url + '/settings/' + setting.logo" :style="'width:' + setting.logo_size + 'px'" />
        </div>
      </div>

      <div class="text-h5 text-bold text-center q-mb-lg">Selamat datang di {{ setting.title }}</div>

      <q-form @submit="register">
        <div class="row justify-center">
          <!-- Email -->
          <div class="col-12 q-pa-md">
            <div class="text-body2 text-center">
              Silahkan buka Email
              <span class="text-bold">{{ email }}</span>
              untuk mendapatkan Kode Verifiasi
            </div>
          </div>

          <!-- Verification Code -->
          <div class="col-10 q-pa-md">
            <label for="verification_code">Kode Verifikasi</label>
            <q-input v-model="verification_code" id="verification_code" label="Kode Verifikasi" mask="######" :rules="rules.verification_code" outlined dense />
          </div>
        </div>

        <q-btn type="submit" color="primary" label="Verifikasi" class="full-width q-mt-sm" :loading="loading" :disable="loading" rounded>
          <template v-slot:loading>
            <q-spinner-hourglass class="on-left" />
            Loading...
          </template>
        </q-btn>
      </q-form>

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
import { useSettingStore } from '/src/stores/setting-store'

const $q = useQuasar()

// Data
const email = localStorage.getItem('email') || ''
const verification_code = ref('')

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

// Validate
const rules = ref({
  verification_code: [(v) => !!v || 'Kode Verifikasi harus diisi']
})

// Verify
const loading = ref(false)
const register = async () => {
  loading.value = true
  try {
    await useAuthStore().verify(email, verification_code.value)

    $q.notify({
      message: 'Verifikasi Email berhasil',
      icon: 'check',
      color: 'positive'
    })
    localStorage.removeItem('email')
    window.location.reload()
  } catch (error) {
    console.error('Error submitting form:', error)

    $q.notify({
      message: error.response.data.message || 'Verifikasi gagal, Silahkan coba lagi nanti',
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
