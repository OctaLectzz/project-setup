<template>
  <q-page class="q-my-xl">
    <hr class="q-my-md" />

    <!-- Loading -->
    <q-form v-if="loading" @submit="editProfile">
      <div class="row justify-center">
        <!-- Avatar card -->
        <div class="col-md-3 col-xs-12 q-ma-md">
          <q-skeleton width="100%" height="500px" />
        </div>

        <!-- Account details card -->
        <div class="col-md-8 col-xs-12 q-ma-md">
          <q-skeleton width="100%" height="500px" />
        </div>
      </div>

      <!-- Save -->
      <q-skeleton width="90px" height="38px" class="float-right q-mx-xl" />
      <hr class="q-my-md" />
    </q-form>

    <!-- Form -->
    <q-form v-else @submit="editProfile">
      <div class="row justify-center">
        <!-- Avatar card -->
        <div class="col-md-3 col-xs-10 q-pa-md">
          <q-card class="q-pb-xl">
            <q-card-section class="bg-blue-grey-1">
              <div class="text-body1 text-bold">Foto profil</div>
            </q-card-section>

            <q-separator />

            <q-card-section class="text-center q-pa-md">
              <!-- Avatar -->
              <q-avatar size="100px" class="q-my-md">
                <img ref="avatar" :src="url + '/avatars/' + data.avatar" />
              </q-avatar>
              <div class="text-subtitle2 text-grey-7">JPG atau PNG tidak lebih besar dari 5 MB</div>
              <q-btn color="primary" class="q-my-md" label="Unggah Foto baru" @click="$refs.avatarInput.click()" no-caps />
              <input type="file" ref="avatarInput" id="avatarInput" style="display: none" accept="image/*" @change="avatarChange" />
            </q-card-section>
          </q-card>
        </div>

        <!-- Account details card -->
        <div class="col-md-8 col-xs-10 q-pa-md">
          <q-card class="q-pb-xl" style="height: 100%">
            <q-card-section class="bg-blue-grey-1">
              <div class="text-body1 text-bold">Detail Akun</div>
            </q-card-section>
            <q-separator />

            <q-card-section class="q-pa-sm">
              <div class="row justify-center">
                <!-- Username -->
                <div class="col-md-5 col-xs-10 q-pa-sm">
                  <div class="text-bold">Username</div>
                  <q-input v-model="data.username" placeholder="octalectzz" :rules="rules.username" v-lowercase outlined dense required autofocus />
                </div>

                <!-- Name -->
                <div class="col-md-5 col-xs-10 q-pa-sm">
                  <div class="text-bold">Nama</div>
                  <q-input v-model="data.name" placeholder="Octavyan Putra" :rules="rules.name" outlined dense required />
                </div>

                <!-- Email -->
                <div class="col-md-5 col-xs-10 q-pa-sm">
                  <div class="text-bold">Email</div>
                  <q-input v-model="data.email" type="email" placeholder="example@gmail.com" :rules="rules.email" v-lowercase outlined dense required />
                </div>

                <!-- Phone Number -->
                <div class="col-md-5 col-xs-10 q-pa-sm">
                  <div class="text-bold">Nomor Telepon</div>
                  <q-input v-model="data.phone_number" placeholder="0897 - 1234 - 4444" mask="#### - #### - ######" outlined dense />
                </div>

                <!-- Pronouns -->
                <div class="col-md-5 col-xs-10 q-pa-sm">
                  <div class="text-bold">Pronouns</div>
                  <q-select v-model="data.pronouns" :options="['ey/em', 'he/him', 'ne/nem', 'she/her', 'they/them', 've/ver', 'xe/xem', 'xie/xem', 'ze/zir']" placeholder="he/him" outlined dense />
                </div>

                <!-- Domicile -->
                <div class="col-md-5 col-xs-10 q-pa-sm">
                  <div class="text-bold">Domisili</div>
                  <q-input v-model="data.domicile" placeholder="Surakarta" outlined dense />
                </div>

                <!-- Gender -->
                <div class="col-md-3 col-sm-5 col-xs-10 q-pa-sm">
                  <div class="text-bold">Jenis Kelamin</div>
                  <q-option-group
                    v-model="data.gender"
                    size="xs"
                    :options="[
                      { label: 'Laki - Laki', value: 'Laki - Laki' },
                      { label: 'Perempuan', value: 'Perempuan' }
                    ]"
                    inline
                  />
                </div>

                <!-- Bio -->
                <div class="col-md-7 col-sm-5 col-xs-10 q-pa-sm">
                  <div class="text-bold">Bio</div>
                  <q-input v-model="data.bio" type="textarea" placeholder="Masukkan bio anda" outlined dense />
                </div>
              </div>
            </q-card-section>
          </q-card>
        </div>
      </div>

      <!-- Save -->
      <q-btn type="submit" label="Simpan" color="primary" class="float-right q-mx-xl" :loading="saveloading" :disable="disabledButton">
        <template v-slot:loading>
          <q-spinner-gears />
        </template>
      </q-btn>
      <hr class="q-my-md" />
    </q-form>
  </q-page>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { url } from '/src/boot/axios'
import { useProfileStore } from '/src/stores/profile-store'
import { useUserStore } from '/src/stores/user-store'

const $q = useQuasar()
const router = useRouter()
const loading = ref(true)
const data = ref({
  id: null,
  avatar: '',
  username: '',
  name: '',
  email: '',
  phone_number: ''
})

// Profile
const getProfile = async () => {
  loading.value = true
  try {
    const res = await useProfileStore().profile()

    data.value = res.data.data
  } catch (error) {
    console.error('Error fetching data:', error)
  }
  loading.value = false
}
onMounted(() => {
  getProfile()
})

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

// Avatar
const avatar = ref(null)
const avatarChange = async (e) => {
  e.preventDefault()

  const avatarImage = e.target.files[0]
  data.value.avatar = avatarImage
  if (avatarImage) {
    const reader = new FileReader()
    reader.onload = () => {
      avatar.value.src = reader.result
    }
    reader.readAsDataURL(avatarImage)
  }
}

// Validate
const rules = ref({
  username: [
    (v) => !!v || 'Username harus diisi',
    (v) => v.length <= 20 || 'Username maksimal 20 karakter',
    (v) => {
      if (typeof v === 'string') {
        if (users.value) {
          return !users.value.some((user) => user.username.toLowerCase() === v.toLowerCase() && user.id !== data.value.id) || 'Username sudah ada'
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
          return !users.value.some((user) => user.email.toLowerCase() === v.toLowerCase() && user.id !== data.value.id) || 'Email sudah ada'
        }
      }
      return true
    }
  ]
})

// Disabled Button
const saveloading = ref(false)
const disabledButton = computed(() => saveloading.value || !data.value.name || !data.value.email)

// Edit
const editProfile = async () => {
  saveloading.value = true
  try {
    await useProfileStore().editprofile(data.value)

    $q.notify({
      message: 'Profil berhasil diedit',
      icon: 'check',
      color: 'positive'
    })
    router.back()
    setTimeout(() => {
      window.location.reload()
    }, 100)
  } catch (error) {
    console.error('Error submitting form:', error)
    $q.notify({
      message: error.response.data.message || 'Profil gagal diedit, Silahkan coba lagi nanti',
      icon: 'warning',
      color: 'negative'
    })
  }
  saveloading.value = false
}
</script>
