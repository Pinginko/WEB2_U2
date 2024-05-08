<template>

  <Navbar></Navbar>

  <div class="container d-flex justify-content-evenly mb-3 w-100">
    <button @click="getData" class="btn btn-primary">Získaj údaje</button>
    <h1 class="text-primary fw-bold container text-center">Rozvrh hodín</h1>
    <button @click="deleteData" class="btn btn-primary">Zmaž údaje</button>
  </div>

  <div class="container d-flex justify-content-center w-100">
    <button @click="fetchTimetable" class="btn btn-primary ms-2">Ukáž rozvrh</button>
    <button @click="showModalFn" class="btn btn-primary ms-2">Vytvor akciu</button>
    <button class="btn btn-primary ms-2">Modifikuj údaje</button>
  </div>

  <Modal class="mt-3 mb-3" v-if="showModal" @close-modal="handleModalClose" :data="timetable"></Modal>

  <div class="container">
    <table class="table table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Deň</th>
          <th>Predmet</th>
          <th>Miestnosť</th>
          <th>Čas</th>
          <th>Typ</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="row in timetable" :key="row.id" @click="deleteRecord(row.id)">
          <td>{{ row.id }}</td>
          <td>{{ row.day }}</td>
          <td>{{ row.subject_name }}</td>
          <td>{{ row.room_number }}</td>
          <td>{{ convertTime(row.time) + '-' + convertTime(+row.time + 110) }}</td>
          <td>{{ row.type }}</td>
          <td><button class="btn btn-secondary">Delete</button></td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import Modal from '../components/Modal.vue';
import Navbar from '../components/Navbar.vue';

import { onMounted, ref } from 'vue';

async function getData() {
  const response = await fetch('http://node64.webte.fei.stuba.sk:8181/apiButtons.php');
  const data = await response.json();
  console.log(data);
}

async function deleteData() {
  const response = await fetch('http://node64.webte.fei.stuba.sk:8181/apiButtons.php', {
    method: "DELETE",
  });
  const data = await response.json();
  console.log(data);
}

async function deleteRecord(id) {
  const response = await fetch('http://node64.webte.fei.stuba.sk:8181/apiTimetable.php?id=' + id, {
    method: "DELETE",
  });
  const data = await response.json();
  console.log(data);
  fetchTimetable();
}

const timetable = ref([]);
const showModal = ref(false);

async function fetchTimetable() {
  fetch('http://node64.webte.fei.stuba.sk:8181/apiTimetable.php')
    .then(response => response.json())
    .then(data => {
      timetable.value = data;
    })
    .catch(error => {
      console.error('Error fetching data:', error);
    });
}

const convertTime = (timeString) => {
  if (!timeString) return;

  const minutes = parseInt(timeString);
  const hours = Math.floor(minutes / 60);
  const remainingMinutes = minutes % 60;

  return `${hours.toString().padStart(2, '0')}:${remainingMinutes.toString().padStart(2, '0')}`;
}

const showModalFn = () => {
  showModal.value = true;
};

const handleModalClose = () => {
  showModal.value = false;
};

onMounted(() => {
  fetchTimetable();
})

</script>
