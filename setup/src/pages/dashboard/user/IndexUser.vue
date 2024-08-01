<template>
  <q-page class="q-pa-sm">
    <q-table
      v-model:pagination="pagination"
      :rows-per-page-options="[10, 20, 30]"
      :table-header-class="$q.dark.isActive ? 'bg-blue-grey-10' : 'bg-grey-2'"
      :class="$q.dark.isActive ? 'text-grey-4' : 'text-grey-9'"
      :rows="currencyData"
      :columns="currencyColumns"
      :hide-header="grid"
      :grid="grid"
      :filter="filter"
      :separator="tableseparator"
      title="Pengguna"
      row-key="__index"
      class="dashboard-table"
      virtual-scroll
      flat
      bordered
    >
      <!-- Top -->
      <template v-slot:top-right="props">
        <!-- Table Type -->
        <q-option-group
          v-model="tableseparator"
          size="xs"
          :options="[
            { label: 'Horizontal', value: 'horizontal' },
            { label: 'Vertical', value: 'vertical' },
            { label: 'Cell', value: 'cell' },
            { label: 'None', value: 'none' }
          ]"
          class="q-mx-md"
          inline
        />

        <!-- Fullscreen -->
        <q-btn color="primary" :icon="props.inFullscreen ? 'fullscreen_exit' : 'fullscreen'" @click="setFs(props)" flat round dense>
          <q-tooltip>{{ props.inFullscreen ? 'Keluar Layar Penuh' : 'Beralih ke Layar Penuh' }}</q-tooltip>
        </q-btn>

        <!-- Grid Switch -->
        <q-btn color="primary" :icon="grid ? 'list' : 'grid_on'" @click="grid = !grid" class="q-mr-sm" flat round dense>
          <q-tooltip>{{ grid ? 'List' : 'Grid' }}</q-tooltip>
        </q-btn>

        <!-- Search -->
        <q-input v-model="filter" placeholder="Cari..." debounce="300" outlined dense>
          <template v-slot:append>
            <q-icon name="search" />
          </template>
        </q-input>
      </template>

      <!-- Create -->
      <template v-slot:top-left>
        <q-btn color="primary" label="Tambah Pengguna" class="shadow-3 q-my-sm" @click="createItemDialog = true" no-caps />
        <q-dialog v-model="createItemDialog" transition-show="slide-up" transition-hide="slide-down" full-width full-height persistent>
          <CreateItem @created="itemCreated" />
        </q-dialog>
      </template>

      <!-- Table -->
      <!-- No Data -->
      <template v-slot:no-data>
        <div class="text-body2 text-bold text-center q-pa-md">
          <q-icon name="warning_amber" size="25px" class="q-mx-sm" />
          Tidak ada data yang tersedia.
        </div>
      </template>

      <!-- ID -->
      <template #body-cell-id="props">
        <q-td :props="props">
          {{ props.rowIndex + 1 }}
        </q-td>
      </template>

      <!-- Avatar -->
      <template #body-cell-avatar="props">
        <q-td :props="props">
          <q-avatar size="60px" v-viewer>
            <img :src="url + '/avatars/' + props.row.avatar" class="dashboard-image" />
          </q-avatar>
        </q-td>
      </template>

      <!-- User -->
      <template #body-cell-user="props">
        <q-td :props="props">
          <div class="text-bold">{{ props.row.name }}</div>
          <div>{{ props.row.username }}</div>
        </q-td>
      </template>

      <!-- Role -->
      <template #body-cell-role="props">
        <q-td :props="props">
          <div class="bg-blue-grey-2 rounded-borders">
            <div class="text-blue-grey-8 text-bold q-pa-xs">{{ props.row.role }}</div>
          </div>
        </q-td>
      </template>

      <!-- Status -->
      <template #body-cell-status="props">
        <q-td :props="props">
          <q-chip v-if="props.row.status == 1" color="green" text-color="white" icon="task_alt" label="Aktif" />
          <q-chip v-if="props.row.status == 0" color="red" text-color="white" icon="warning" label="Banned" />
        </q-td>
      </template>

      <!-- Action -->
      <template #body-cell-action="props">
        <q-td :props="props">
          <q-btn color="warning" field="edit" icon="edit" class="q-mx-xs" @click="props.row.editItemDialog = true" dense round>
            <q-dialog v-model="props.row.editItemDialog" transition-show="slide-up" transition-hide="slide-down" full-width full-height persistent>
              <EditItem @edited="itemEdited(props.row)" :item="props.row" />
            </q-dialog>
          </q-btn>
          <q-btn color="red" field="delete" icon="delete" class="q-mx-xs" @click="deleteItemDialog(props.row)" dense round />
        </q-td>
      </template>

      <!-- Grid -->
      <template v-slot:item="props">
        <div class="dashboard-card q-pa-md col-xs-12 col-sm-6 col-md-6 col-lg-6 grid-style-transition" :style="props.selected ? 'transform: scale(0.95);' : ''">
          <q-card class="dashboard-card q-pa-md">
            <q-list dense>
              <q-item v-for="col in props.cols" :key="col.id">
                <q-item-section>
                  <q-item-label>{{ col.label }}</q-item-label>
                </q-item-section>

                <q-item-section side>
                  <!-- ID -->
                  <div v-if="col.name === 'id'">
                    {{ props.rowIndex + 1 }}
                  </div>

                  <!-- Avatar -->
                  <div v-else-if="col.name === 'avatar'">
                    <q-avatar size="60px" v-viewer>
                      <img :src="url + '/avatars/' + props.row.avatar" class="dashboard-image" />
                    </q-avatar>
                  </div>

                  <!-- User -->
                  <div v-else-if="col.name === 'user'">
                    <div>
                      <span class="text-bold">{{ props.row.name && props.row.name.length > 10 ? props.row.name.substring(0, 10) + '...' : props.row.name }}</span>
                      ({{ props.row.username && props.row.username.length > 10 ? props.row.username.substring(0, 10) + '...' : props.row.username }})
                    </div>
                  </div>

                  <!-- Role -->
                  <div v-else-if="col.name === 'role'">
                    <div class="bg-blue-grey-2 rounded-borders">
                      <div class="text-blue-grey-8 text-bold q-pa-xs">{{ props.row.role }}</div>
                    </div>
                  </div>

                  <!-- Status -->
                  <div v-else-if="col.name === 'status'">
                    <q-chip v-if="props.row.status == 1" color="green" text-color="white" icon="task_alt" label="Aktif" />
                    <q-chip v-if="props.row.status == 0" color="red" text-color="white" icon="warning" label="Banned" />
                  </div>

                  <!-- Action -->
                  <div v-else-if="col.name === 'action'">
                    <q-btn color="warning" field="edit" icon="edit" class="q-mx-xs" @click="props.row.editItemDialog = true" dense round>
                      <q-dialog v-model="props.row.editItemDialog" transition-show="slide-up" transition-hide="slide-down" full-width full-height persistent>
                        <EditItem @edited="itemEdited(props.row)" :item="props.row" />
                      </q-dialog>
                    </q-btn>
                    <q-btn color="red" field="delete" icon="delete" class="q-mx-xs" @click="deleteItemDialog(props.row)" dense round />
                  </div>

                  <!-- DLL -->
                  <q-item-label v-else :class="col.classes ? col.classes : ''" caption>{{ col.value }}</q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </q-card>
        </div>
      </template>
    </q-table>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { url } from '/src/boot/axios'
import { useUserStore } from '/src/stores/user-store'
import CreateItem from './CreateUser.vue'
import EditItem from './EditUser.vue'

const $q = useQuasar()
const router = useRouter()

// Get
const items = ref([])
const getItem = async () => {
  try {
    const res = await useUserStore().dashboard()

    items.value = res.data.data
  } catch (error) {
    console.error('Error fetching data:', error)

    if (error.response.data.status === 'failed') {
      router.push('/notfound')
    }
  }
}
onMounted(() => {
  getItem()
})

// Create
const createItemDialog = ref(false)
const itemCreated = () => {
  createItemDialog.value = false
  getItem()
}

// Edit
const itemEdited = (item) => {
  item.editItemDialog = false
  getItem()
}

// Delete
const deleteItemDialog = (row) => {
  $q.dialog({
    title: 'WARNING!!!',
    message: 'Apakah Anda yakin ingin menghapus data ini?',
    cancel: true,
    persistent: true
  }).onOk(async () => {
    try {
      await useUserStore().delete(row.id)

      $q.notify({
        message: 'Pengguna berhasil dihapus',
        icon: 'check',
        color: 'positive'
      })
      getItem()
    } catch (error) {
      console.error('Error submitting form:', error)
      $q.notify({
        message: error.response.data.message || 'Pengguna gagal dihapus',
        icon: 'warning',
        color: 'negative'
      })
    }
  })
}

// Table
const currencyData = items
const currencyColumns = [
  {
    name: 'id',
    field: 'id',
    label: 'No',
    align: 'center',
    headerStyle: 'font-weight: bolder; font-size: 13px;'
  },
  {
    name: 'avatar',
    field: 'avatar',
    label: 'Foto Profil',
    align: 'center',
    sortable: false,
    headerStyle: 'font-weight: bolder; font-size: 13px;'
  },
  {
    name: 'user',
    field: 'name',
    label: 'Pengguna',
    align: 'left',
    sortable: true,
    headerStyle: 'font-weight: bolder; font-size: 13px;'
  },
  {
    name: 'email',
    field: 'email',
    label: 'Email',
    align: 'left',
    sortable: true,
    headerStyle: 'font-weight: bolder; font-size: 13px;'
  },
  {
    name: 'phone_number',
    field: 'phone_number',
    label: 'Nomor Telepon',
    align: 'center',
    sortable: true,
    headerStyle: 'font-weight: bolder; font-size: 13px;'
  },
  {
    name: 'domicile',
    field: 'domicile',
    label: 'Domisili',
    align: 'center',
    sortable: true,
    headerStyle: 'font-weight: bolder; font-size: 13px;'
  },
  {
    name: 'role',
    field: 'role',
    label: 'Role',
    align: 'center',
    sortable: true,
    headerStyle: 'font-weight: bolder; font-size: 13px;'
  },
  {
    name: 'status',
    field: 'status',
    label: 'Status',
    align: 'center',
    sortable: true,
    headerStyle: 'font-weight: bolder; font-size: 13px;'
  },
  {
    name: 'action',
    field: 'action',
    label: 'Aksi',
    align: 'center'
  }
]
const tableseparator = ref('cell')
const filter = ref('')
const grid = ref(false)
const pagination = ref({})
const setFs = (props) => {
  props.toggleFullscreen()
}
</script>
