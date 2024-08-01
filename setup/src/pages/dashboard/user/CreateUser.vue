<template>
  <div>
    <q-form @submit="createData">
      <q-card style="min-width: 400px">
        <q-card-section class="row items-center q-py-sm">
          <div class="text-h6">Tambah Pengguna</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>

        <q-separator />

        <q-card-section class="scroll" style="height: 77vh">
          <div class="row justify-center">
            <!-- Avatar card -->
            <div class="col-md-3 q-pa-md">
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
            <div class="col-md-8 q-pa-md">
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

                    <!-- Password -->
                    <div class="col-md-5 col-xs-10 q-pa-sm">
                      <div class="text-bold">Kata Sandi</div>
                      <q-input v-model="data.password" type="password" placeholder="********" :rules="rules.password" outlined dense required />
                    </div>

                    <!-- Confirm Password -->
                    <div class="col-md-5 col-xs-10 q-pa-sm">
                      <div class="text-bold">Konfirmasi Kata</div>
                      <q-input v-model="data.confirm_password" type="password" placeholder="********" :rules="rules.confirm_password" outlined dense required />
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

                    <!-- Role -->
                    <div class="col-md-3 col-sm-5 col-xs-10 q-pa-sm">
                      <div class="text-bold">Role</div>
                      <q-btn-toggle
                        v-model="data.role"
                        toggle-color="primary"
                        class="q-mx-xs"
                        :options="[
                          { label: 'Admin', value: 'Admin' },
                          { label: 'Member', value: 'Member' }
                        ]"
                        push
                        glossy
                        required
                      />
                    </div>

                    <!-- Status -->
                    <div class="col-md-3 col-sm-5 col-xs-10 q-pa-sm">
                      <div class="text-bold">Status</div>
                      <q-btn v-model="data.status" color="green" label="Aktif" :outline="data.status == 0" class="q-mx-xs" @click="data.status = 1" />
                      <q-btn v-model="data.status" color="red" label="Banned" :outline="data.status == 1" class="q-mx-xs" @click="data.status = 0" />
                    </div>

                    <!-- Bio -->
                    <div class="col-10 q-pa-sm">
                      <div class="text-bold">Bio</div>
                      <q-input v-model="data.bio" type="textarea" placeholder="Masukkan bio anda" outlined dense />
                    </div>
                  </div>
                </q-card-section>
              </q-card>
            </div>
          </div>
        </q-card-section>

        <q-separator />

        <q-card-actions align="right" class="text-primary">
          <q-btn color="primary" label="Batal" flat v-close-popup />
          <q-btn type="submit" color="primary" label="Tambah" :loading="loading" :disable="disabledButton">
            <template v-slot:loading>
              <q-spinner-gears />
            </template>
          </q-btn>
        </q-card-actions>
      </q-card>
    </q-form>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useQuasar } from 'quasar'
import { url } from '/src/boot/axios'
import { useUserStore } from '/src/stores/user-store'

const $q = useQuasar()
const emits = defineEmits(['created'])
const data = ref({
  avatar: 'user-profile-default.jpg',
  username: '',
  name: '',
  email: '',
  password: '',
  confirm_password: '',
  role: 'Admin',
  phone_number: '',
  domicile: '',
  gender: '',
  pronouns: '',
  bio: '',
  status: 1
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
  data.value.avatar = e.target.files[0]
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
  confirm_password: [(v) => !!v || 'Konfirmasi Kata Sandi harus diisi', (v) => v === data.value.password || 'Kata Sandi tidak sama']
})

// Disabled Button
const loading = ref(false)
const disabledButton = computed(() => loading.value || !data.value.username || !data.value.name || !data.value.email || !data.value.password || !data.value.confirm_password)

// Create
const createData = async () => {
  loading.value = true
  try {
    await useUserStore().create(data.value)

    $q.notify({
      message: 'Pengguna berhasil dibuat',
      icon: 'check',
      color: 'positive'
    })
    emits('created')
  } catch (error) {
    console.error('Error submitting form:', error)

    $q.notify({
      message: error.response.data.message || 'Pengguna gagal dibuat, Silahkan coba lagi nanti',
      icon: 'warning',
      color: 'negative'
    })
  }
  loading.value = false
}
</script>
