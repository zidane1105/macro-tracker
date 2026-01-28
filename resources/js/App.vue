<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

/* ================= STATE ================= */
const entries = ref([])
const newFood = ref('')
const newCalories = ref('')
const newProtein = ref('')
const searchKeyword = ref('')

/* ================= API BASE ================= */
// kalau pakai Vite + Laravel
axios.defaults.baseURL = 'http://localhost:8000'

/* ================= CRUD ================= */
const fetchEntries = async () => {
  const res = await axios.get('/api/food-entries')
  entries.value = res.data
}

const addEntry = async () => {
  if (!newFood.value || !newCalories.value) return

  const res = await axios.post('/api/food-entries', {
    name: newFood.value,
    calories: newCalories.value,
    protein: newProtein.value || 0
  })

  entries.value.unshift(res.data)
  newFood.value = ''
  newCalories.value = ''
  newProtein.value = ''
}

const deleteEntry = async (id) => {
  if (!confirm('Yakin mau hapus?')) return

  await axios.delete(`/api/food-entries/${id}`)
  entries.value = entries.value.filter(item => item.id !== id)
}

/* ================= ALGORITMA (API) ================= */

// SORTING (Bubble Sort di Backend)
const sortByCalories = async () => {
  const res = await axios.get('/api/food-entries/sort/calories')
  entries.value = res.data
}

// SEARCHING (Manual Search di Backend)
const searchFood = async () => {
  if (!searchKeyword.value) {
    fetchEntries()
    return
  }
  const res = await axios.get(
    `/api/food-entries/search/${searchKeyword.value}`
  )
  entries.value = res.data
}

/* ================= AGREGASI ================= */
const totalCalories = computed(() =>
  entries.value.reduce((sum, item) => sum + Number(item.calories), 0)
)

const totalProtein = computed(() =>
  entries.value.reduce((sum, item) => sum + Number(item.protein), 0)
)

onMounted(fetchEntries)
</script>

<template>
  <div class="app-container">
    <div class="main-card">
      <h1>🍽️ Macro Tracker</h1>
      <p class="subtitle">Client–Server Food Tracker</p>

      <!-- SUMMARY -->
      <div class="stats">
        <div>🔥 {{ totalCalories }} kcal</div>
        <div>💪 {{ totalProtein }} g protein</div>
      </div>

      <!-- INPUT -->
      <input v-model="newFood" placeholder="Nama makanan" />
      <input v-model="newCalories" type="number" placeholder="Kalori" />
      <input v-model="newProtein" type="number" placeholder="Protein" />
      <button @click="addEntry">Tambah</button>

      <!-- SEARCH & SORT -->
      <input
        v-model="searchKeyword"
        placeholder="Cari makanan..."
        @input="searchFood"
      />
      <button @click="sortByCalories">Urutkan Kalori</button>

      <!-- LIST -->
      <ul>
        <li v-for="item in entries" :key="item.id">
          <span>
            {{ item.name }} —
            {{ item.calories }} kcal —
            {{ item.protein }} g
          </span>
          <button @click="deleteEntry(item.id)">❌</button>
        </li>
      </ul>

      <p v-if="entries.length === 0">Belum ada data</p>
    </div>
  </div>
</template>

<style scoped>
.app-container {
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background: #eef2ff;
}
.main-card {
  background: white;
  padding: 20px;
  width: 400px;
  border-radius: 12px;
}
h1 {
  margin-bottom: 0;
}
.subtitle {
  font-size: 12px;
  color: #666;
  margin-bottom: 15px;
}
.stats {
  display: flex;
  justify-content: space-between;
  margin-bottom: 10px;
}
input {
  width: 100%;
  padding: 8px;
  margin: 4px 0;
}
button {
  width: 100%;
  margin-top: 6px;
  padding: 8px;
  background: #6366f1;
  color: white;
  border: none;
  border-radius: 6px;
}
ul {
  margin-top: 15px;
  padding: 0;
  list-style: none;
}
li {
  display: flex;
  justify-content: space-between;
  padding: 6px 0;
  border-bottom: 1px solid #eee;
}
</style>