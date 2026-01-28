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
// sesuaikan dengan port backend laravel kamu
axios.defaults.baseURL = 'http://localhost:8000'

/* ================= CRUD ================= */
const fetchEntries = async () => {
  try {
    const res = await axios.get('/api/food-entries')
    entries.value = res.data
  } catch (e) { console.error(e) }
}

const addEntry = async () => {
  if (!newFood.value || !newCalories.value) return

  try {
    const res = await axios.post('/api/food-entries', {
      name: newFood.value,
      calories: newCalories.value,
      protein: newProtein.value || 0
    })

    entries.value.unshift(res.data)
    newFood.value = ''
    newCalories.value = ''
    newProtein.value = ''
  } catch (e) { console.error(e) }
}

const deleteEntry = async (id) => {
  if (!confirm('Yakin mau hapus?')) return

  try {
    await axios.delete(`/api/food-entries/${id}`)
    entries.value = entries.value.filter(item => item.id !== id)
  } catch (e) { console.error(e) }
}

/* ================= ALGORITMA (API) ================= */
// SORTING (Bubble Sort di Backend)
const sortByCalories = async () => {
  try {
    const res = await axios.get('/api/food-entries/sort/calories')
    entries.value = res.data
  } catch (e) { console.error(e) }
}

// SEARCHING (Manual Search di Backend)
const searchFood = async () => {
  if (!searchKeyword.value) {
    fetchEntries()
    return
  }
  try {
    const res = await axios.get(`/api/food-entries/search/${searchKeyword.value}`)
    entries.value = res.data
  } catch (e) { console.error(e) }
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
    <div class="glass-card">
      <div class="header">
        <h1>🍽️ Macro Tracker</h1>
        <p class="subtitle">Monitor nutrisi harianmu</p>
      </div>

      <div class="stats-container">
        <div class="stat-box cal">
          <span class="label">Total Kalori</span>
          <span class="value">🔥 {{ totalCalories }}</span>
        </div>
        <div class="stat-box pro">
          <span class="label">Total Protein</span>
          <span class="value">💪 {{ totalProtein }}g</span>
        </div>
      </div>

      <div class="input-section">
        <div class="input-group">
          <input v-model="newFood" placeholder="Makan apa tadi?" class="input-main" />
        </div>
        <div class="input-row">
          <input v-model="newCalories" type="number" placeholder="Kalori (kcal)" />
          <input v-model="newProtein" type="number" placeholder="Protein (g)" />
        </div>
        <button @click="addEntry" class="btn-primary">
          + Tambah Catatan
        </button>
      </div>

      <div class="toolbar">
        <div class="search-wrapper">
          <span class="search-icon">🔍</span>
          <input 
            v-model="searchKeyword" 
            placeholder="Cari riwayat..." 
            @input="searchFood" 
            class="input-search"
          />
        </div>
        <button @click="sortByCalories" class="btn-secondary">
          ⚡ Urutkan Kalori
        </button>
      </div>

      <div class="list-container">
        <ul v-if="entries.length > 0">
          <transition-group name="list">
            <li v-for="item in entries" :key="item.id" class="list-item">
              <div class="item-info">
                <span class="item-name">{{ item.name }}</span>
                <span class="item-details">
                  <span class="pill cal">{{ item.calories }} kcal</span>
                  <span class="pill pro">{{ item.protein }}g pro</span>
                </span>
              </div>
              <button @click="deleteEntry(item.id)" class="btn-delete" title="Hapus">
                ✕
              </button>
            </li>
          </transition-group>
        </ul>

        <div v-else class="empty-state">
          <p>📭 Belum ada data makanan</p>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* --- LAYOUT & BACKGROUND --- */
.app-container {
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  padding: 20px;
}

.glass-card {
  background: #ffffff;
  width: 100%;
  max-width: 480px;
  border-radius: 20px;
  padding: 30px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

/* --- HEADER --- */
.header {
  text-align: center;
  margin-bottom: 25px;
}
h1 {
  margin: 0;
  font-size: 28px;
  color: #333;
}
.subtitle {
  margin-top: 5px;
  font-size: 14px;
  color: #888;
}

/* --- STATS --- */
.stats-container {
  display: flex;
  gap: 15px;
  margin-bottom: 25px;
}
.stat-box {
  flex: 1;
  padding: 15px;
  border-radius: 12px;
  text-align: center;
  display: flex;
  flex-direction: column;
}
.stat-box.cal { background: #fff1f2; color: #e11d48; border: 1px solid #ffe4e6; }
.stat-box.pro { background: #f0fdf4; color: #16a34a; border: 1px solid #dcfce7; }

.stat-box .label { font-size: 11px; text-transform: uppercase; font-weight: bold; opacity: 0.7; }
.stat-box .value { font-size: 18px; font-weight: bold; margin-top: 5px; }

/* --- INPUTS --- */
.input-section {
  background: #f8fafc;
  padding: 15px;
  border-radius: 12px;
  margin-bottom: 25px;
  border: 1px solid #e2e8f0;
}
.input-group { margin-bottom: 10px; }
.input-row { display: flex; gap: 10px; margin-bottom: 10px; }

input {
  width: 100%;
  padding: 12px 15px;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  font-size: 14px;
  outline: none;
  transition: all 0.3s;
}
input:focus {
  border-color: #764ba2;
  box-shadow: 0 0 0 3px rgba(118, 75, 162, 0.1);
}

.btn-primary {
  width: 100%;
  padding: 12px;
  background: #764ba2;
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.3s;
}
.btn-primary:hover { background: #5b3a85; }

/* --- TOOLBAR --- */
.toolbar {
  display: flex;
  gap: 10px;
  margin-bottom: 15px;
}
.search-wrapper {
  position: relative;
  flex: 2;
}
.search-icon {
  position: absolute;
  left: 10px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 12px;
}
.input-search {
  padding-left: 30px;
  font-size: 13px;
}
.btn-secondary {
  flex: 1;
  background: white;
  border: 1px solid #764ba2;
  color: #764ba2;
  font-size: 12px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.2s;
}
.btn-secondary:hover { background: #f3e8ff; }

/* --- LIST --- */
ul { padding: 0; list-style: none; margin: 0; }
.list-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: white;
  padding: 12px;
  margin-bottom: 8px;
  border-radius: 10px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.05);
  border: 1px solid #f1f5f9;
}
.item-info { display: flex; flex-direction: column; gap: 4px; }
.item-name { font-weight: 600; color: #333; }
.item-details { display: flex; gap: 8px; font-size: 12px; }

.pill { padding: 2px 8px; border-radius: 20px; font-weight: 500; }
.pill.cal { background: #ffe4e6; color: #be123c; }
.pill.pro { background: #dcfce7; color: #15803d; }

.btn-delete {
  background: #fef2f2;
  color: #ef4444;
  border: none;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 16px;
  transition: all 0.2s;
}
.btn-delete:hover { background: #ef4444; color: white; }

.empty-state {
  text-align: center;
  color: #94a3b8;
  padding: 20px;
  border: 2px dashed #e2e8f0;
  border-radius: 10px;
}

/* --- ANIMATIONS --- */
.list-enter-active,
.list-leave-active {
  transition: all 0.4s ease;
}
.list-enter-from,
.list-leave-to {
  opacity: 0;
  transform: translateX(30px);
}
</style>