<template>
  <div class="container">
    <h1>Tracker Kalori & Protein</h1>

    <form @submit.prevent="addFood" class="input-group">
      <input v-model="form.name" type="text" placeholder="Nama Makanan" required>
      <input v-model="form.calories" type="number" placeholder="Kalori" required>
      <input v-model="form.protein" type="number" placeholder="Protein (g)" required>
      <button type="submit">Tambah</button>
    </form>

    <div class="stats">
      <div class="card">
        <h3>Total Kalori</h3>
        <p>{{ totalCalories }} kkal</p>
      </div>
      <div class="card">
        <h3>Total Protein</h3>
        <p>{{ totalProtein }} g</p>
      </div>
    </div>

    <ul class="food-list">
      <li v-for="item in foods" :key="item.id">
        <span><b>{{ item.name }}</b> ({{ item.calories }} kkal, {{ item.protein }}g protein)</span>
        <button @click="deleteFood(item.id)" class="delete-btn">Hapus</button>
      </li>
      <li v-if="foods.length === 0" style="text-align:center; color:gray;">Belum ada data hari ini.</li>
    </ul>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const foods = ref([]);
const form = ref({ name: '', calories: '', protein: '' });

// Hitung total otomatis
const totalCalories = computed(() => foods.value.reduce((sum, item) => sum + item.calories, 0));
const totalProtein = computed(() => foods.value.reduce((sum, item) => sum + item.protein, 0));

// Ambil data dari Laravel
const fetchFoods = async () => {
  const response = await axios.get('/api/foods');
  foods.value = response.data;
};

// Kirim data ke Laravel
const addFood = async () => {
  const response = await axios.post('/api/foods', form.value);
  foods.value.push(response.data);
  form.value = { name: '', calories: '', protein: '' }; // Reset form
};

// Hapus data
const deleteFood = async (id) => {
  await axios.delete(`/api/foods/${id}`);
  foods.value = foods.value.filter(item => item.id !== id);
};

onMounted(fetchFoods);
</script>

<style>
/* CSS Sederhana Biar Rapi */
.container { max-width: 600px; margin: 50px auto; font-family: sans-serif; }
.input-group { display: flex; gap: 10px; margin-bottom: 20px; }
input { padding: 10px; flex: 1; border: 1px solid #ddd; border-radius: 4px; }
button { padding: 10px 20px; background: #3b82f6; color: white; border: none; border-radius: 4px; cursor: pointer; }
button:hover { background: #2563eb; }
.stats { display: flex; gap: 20px; margin-bottom: 20px; }
.card { flex: 1; background: #f3f4f6; padding: 15px; border-radius: 8px; text-align: center; }
.card h3 { margin: 0 0 5px 0; color: #555; font-size: 14px; }
.card p { margin: 0; font-size: 24px; font-weight: bold; color: #333; }
.food-list { list-style: none; padding: 0; }
.food-list li { display: flex; justify-content: space-between; padding: 10px; border-bottom: 1px solid #eee; }
.delete-btn { background: #ef4444; padding: 5px 10px; font-size: 12px; }
.delete-btn:hover { background: #dc2626; }
</style>