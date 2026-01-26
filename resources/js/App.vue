<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

// --- STATE (DATA) ---
const entries = ref([]);
const newFood = ref('');
const newCalories = ref('');
const newProtein = ref('');

// --- LOGIC (OTAK) ---
// 1. Ambil data dari server saat web dibuka
const fetchEntries = async () => {
  try {
    const response = await axios.get('/api/food-entries');
    entries.value = response.data;
  } catch (error) {
    console.error("Gagal ambil data:", error);
  }
};

// 2. Kirim data baru ke server
const addEntry = async () => {
  if (!newFood.value || !newCalories.value) return; // Jangan kirim kalau kosong

  try {
    const response = await axios.post('/api/food-entries', {
      name: newFood.value,
      calories: newCalories.value,
      protein: newProtein.value || 0
    });
    // Masukkan data baru ke daftar (biar langsung muncul tanpa refresh)
    entries.value.unshift(response.data);
    
    // Reset form jadi kosong lagi
    newFood.value = '';
    newCalories.value = '';
    newProtein.value = '';
  } catch (error) {
    console.error("Gagal simpan:", error);
    alert("Gagal menyimpan data. Cek koneksi internet.");
  }
};

// 3. Hitung Total (Fitur Tambahan Biar Keren)
const totalCalories = computed(() => {
  return entries.value.reduce((sum, item) => sum + Number(item.calories), 0);
});

// Jalankan fetch saat pertama kali
onMounted(fetchEntries);
</script>

<template>
  <div class="app-container">
    <div class="main-card">
      <div class="header">
        <h1>🍽️ Macro Tracker</h1>
        <p class="subtitle">Catat makro harianmu</p>
      </div>

      <div class="summary-box">
        <span class="label">Total Kalori Hari Ini</span>
        <span class="value">{{ totalCalories }} kcal</span>
      </div>

      <div class="input-section">
        <div class="input-group">
          <input v-model="newFood" placeholder="Nama Makanan (mis: Nasi Goreng)" type="text" />
        </div>
        <div class="row-inputs">
          <input v-model="newCalories" placeholder="Kalori" type="number" />
          <input v-model="newProtein" placeholder="Protein (g)" type="number" />
        </div>
        <button @click="addEntry" class="btn-save">
          + Tambah Makanan
        </button>
      </div>

      <div class="list-section">
        <h3>Riwayat Makan</h3>
        <ul v-if="entries.length > 0">
          <li v-for="entry in entries" :key="entry.id" class="food-item">
            <div class="food-info">
              <span class="food-name">{{ entry.name }}</span>
              <span class="food-detail">{{ entry.protein }}g Protein</span>
            </div>
            <div class="food-calories">
              {{ entry.calories }} kcal
            </div>
          </li>
        </ul>
        <p v-else class="empty-state">Belum ada data makanan hari ini.</p>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* BACKGROUND UTAMA */
.app-container {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); /* Warna Ungu Modern */
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 20px;
  font-family: 'Segoe UI', sans-serif;
}

/* KARTU UTAMA (KOTAK PUTIH) */
.main-card {
  background: white;
  width: 100%;
  max-width: 450px; /* Ukuran mirip layar HP */
  border-radius: 20px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.2);
  overflow: hidden;
  padding: 25px;
}

/* HEADER */
.header {
  text-align: center;
  margin-bottom: 20px;
}
.header h1 {
  margin: 0;
  font-size: 24px;
  color: #333;
}
.subtitle {
  color: #666;
  font-size: 14px;
  margin-top: 5px;
}

/* SUMMARY BOX */
.summary-box {
  background: #f0f4f8;
  padding: 15px;
  border-radius: 12px;
  text-align: center;
  margin-bottom: 25px;
  border: 1px solid #d1d9e6;
}
.summary-box .label {
  display: block;
  font-size: 12px;
  color: #555;
  text-transform: uppercase;
  letter-spacing: 1px;
}
.summary-box .value {
  font-size: 28px;
  font-weight: bold;
  color: #764ba2;
}

/* INPUT FORM */
.input-section {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-bottom: 30px;
}
input {
  width: 100%;
  padding: 12px;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 14px;
  outline: none;
  transition: border-color 0.3s;
}
input:focus {
  border-color: #764ba2;
}
.row-inputs {
  display: flex;
  gap: 10px;
}

/* TOMBOL */
.btn-save {
  background: #764ba2;
  color: white;
  border: none;
  padding: 12px;
  border-radius: 8px;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.3s;
}
.btn-save:hover {
  background: #5b3780;
}

/* LIST MAKANAN */
h3 {
  font-size: 16px;
  color: #333;
  margin-bottom: 10px;
  border-bottom: 2px solid #f0f0f0;
  padding-bottom: 5px;
}
ul {
  list-style: none;
  padding: 0;
  margin: 0;
}
.food-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
  border-bottom: 1px solid #eee;
}
.food-info {
  display: flex;
  flex-direction: column;
}
.food-name {
  font-weight: 600;
  color: #333;
}
.food-detail {
  font-size: 12px;
  color: #888;
}
.food-calories {
  font-weight: bold;
  color: #e74c3c; /* Merah untuk angka kalori */
  background: #fdeaea;
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 12px;
}
.empty-state {
  text-align: center;
  color: #999;
  font-style: italic;
  margin-top: 20px;
}
</style>