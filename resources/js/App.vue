<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const entries = ref([]);
const newFood = ref('');
const newCalories = ref('');
const newProtein = ref('');

// --- LOGIC ---
const fetchEntries = async () => {
  try {
    const response = await axios.get('/api/food-entries');
    entries.value = response.data;
  } catch (error) {
    console.error("Error:", error);
  }
};

const addEntry = async () => {
  if (!newFood.value || !newCalories.value) return;
  try {
    const response = await axios.post('/api/food-entries', {
      name: newFood.value,
      calories: newCalories.value,
      protein: newProtein.value || 0
    });
    entries.value.unshift(response.data);
    newFood.value = ''; newCalories.value = ''; newProtein.value = '';
  } catch (error) {
    alert("Gagal simpan. Cek koneksi.");
  }
};

// FUNGSI HAPUS (BARU)
const deleteEntry = async (id) => {
  if(!confirm("Yakin mau hapus?")) return; // Konfirmasi dulu
  
  try {
    await axios.delete(`/api/food-entries/${id}`);
    // Hapus dari layar tanpa refresh
    entries.value = entries.value.filter(item => item.id !== id);
  } catch (error) {
    alert("Gagal menghapus data.");
  }
};

// HITUNG TOTAL (Updated)
const totalCalories = computed(() => {
  return entries.value.reduce((sum, item) => sum + Number(item.calories), 0);
});
const totalProtein = computed(() => {
  return entries.value.reduce((sum, item) => sum + Number(item.protein), 0);
});

onMounted(fetchEntries);
</script>

<template>
  <div class="app-container">
    <div class="main-card">
      <div class="header">
        <h1>🍽️ Macro Tracker</h1>
        <p class="subtitle">Catat makro harianmu</p>
      </div>

      <div class="stats-container">
        <div class="summary-box cal">
          <span class="label">Total Kalori</span>
          <span class="value">{{ totalCalories }} kcal</span>
        </div>
        <div class="summary-box pro">
          <span class="label">Total Protein</span>
          <span class="value">{{ totalProtein }} g</span>
        </div>
      </div>

      <div class="input-section">
        <div class="input-group">
          <input v-model="newFood" placeholder="Nama Makanan (mis: Dada Ayam)" type="text" />
        </div>
        <div class="row-inputs">
          <input v-model="newCalories" placeholder="Kalori" type="number" />
          <input v-model="newProtein" placeholder="Protein (g)" type="number" />
        </div>
        <button @click="addEntry" class="btn-save">+ Tambah Makanan</button>
      </div>

      <div class="list-section">
        <h3>Riwayat Makan</h3>
        <ul v-if="entries.length > 0">
          <li v-for="entry in entries" :key="entry.id" class="food-item">
            <div class="food-info">
              <span class="food-name">{{ entry.name }}</span>
              <span class="food-detail">{{ entry.protein }}g Protein</span>
            </div>
            <div class="right-side">
              <span class="food-calories">{{ entry.calories }} kcal</span>
              <button @click="deleteEntry(entry.id)" class="btn-delete">×</button>
            </div>
          </li>
        </ul>
        <p v-else class="empty-state">Belum ada data makanan hari ini.</p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.app-container {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex; justify-content: center; align-items: center; padding: 20px;
  font-family: 'Segoe UI', sans-serif;
}
.main-card {
  background: white; width: 100%; max-width: 450px;
  border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);
  padding: 25px;
}
.header { text-align: center; margin-bottom: 20px; }
.header h1 { margin: 0; font-size: 24px; color: #333; }
.subtitle { color: #666; font-size: 14px; }

/* STATS CONTAINER (BARU) */
.stats-container { display: flex; gap: 10px; margin-bottom: 25px; }
.summary-box {
  flex: 1; padding: 15px; border-radius: 12px; text-align: center;
}
.summary-box.cal { background: #e0f2fe; color: #0284c7; }
.summary-box.pro { background: #dcfce7; color: #16a34a; }

.label { display: block; font-size: 11px; text-transform: uppercase; margin-bottom: 5px; opacity: 0.8; }
.value { font-size: 24px; font-weight: bold; }

.input-section { display: flex; flex-direction: column; gap: 10px; margin-bottom: 30px; }
input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; outline: none; }
input:focus { border-color: #764ba2; }
.row-inputs { display: flex; gap: 10px; }
.btn-save { background: #764ba2; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: bold; cursor: pointer; }

h3 { font-size: 16px; margin-bottom: 10px; border-bottom: 2px solid #f0f0f0; padding-bottom: 5px; }
ul { list-style: none; padding: 0; margin: 0; }
.food-item { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #eee; }
.food-info { display: flex; flex-direction: column; }
.food-name { font-weight: 600; color: #333; }
.food-detail { font-size: 12px; color: #888; }
.right-side { display: flex; align-items: center; gap: 10px; }
.food-calories { font-weight: bold; color: #555; font-size: 13px; }

/* TOMBOL DELETE (BARU) */
.btn-delete {
  background: #fee2e2; color: #ef4444; border: none;
  width: 30px; height: 30px; border-radius: 50%;
  font-size: 18px; cursor: pointer; display: flex; justify-content: center; align-items: center;
  transition: all 0.2s;
}
.btn-delete:hover { background: #ef4444; color: white; }
.empty-state { text-align: center; color: #999; margin-top: 20px; font-style: italic;}
</style>