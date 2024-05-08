<template>
  <Navbar></Navbar>
  <div class="about">
    <h1 class="text-primary fw-bold container text-center">Rozvrh hodín</h1>
  </div>

  <div class="container d-flex justify-content-evenly mb-3 w-100">
    <div>
      <h3 class="text-primary">Vyber si program</h3>
      <select class="w-100" v-model="selectedOption" @change="filterDataTable">
        <option disabled value="">Vyberte si Program</option>
        <option v-for="item in uniqueData" :key="item">
          {{ item }}
        </option>
      </select>
    </div>

    <button @click="resetFilter" class="btn btn-primary">Reset Filter</button>

    <div>
      <h3 class="text-primary">Vyber si veduceho</h3>
      <select class="w-100" v-model="selectedOptionVeduci" @change="filterDataTableVeduci">
        <option disabled value="">Vyberte si Veduceho</option>
        <option v-for="item in uniqueDataVeduci" :key="item">
          {{ item }}
        </option>
      </select>
    </div>
  </div>

  <DataTable class="display w-75">
    <thead>
      <tr>
        <td>Ústav</td>
        <td>Typ</td>
        <td>Názov</td>
        <td>Školiteľ</td>
        <td>Program</td>
      </tr>
    </thead>
  </DataTable>
</template>

<style>
@import 'datatables.net-dt';

.dt-search {
  margin-right: 22%;
}

.dt-length {
  margin-left: 30%;
}

.dt-info {
  margin-left: 40%;
}

.dt-paging {
  margin-right: 17%;
}
</style>

<script setup>
import Navbar from '../components/Navbar.vue';
import { ref, onMounted, computed } from 'vue';
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net';

DataTable.use(DataTablesCore);

const data = ref([]);

function getData() {

  fetch('http://node64.webte.fei.stuba.sk:8181/curlBp.php?option=1')
    .then(r => r.json())
    .then((res) => {
      res.map(row => {
        data.value.push(
          [row.garpra, row.typ, row.nazov, row.veduci, row.program]
        )

      })
      DataTablesCore.$('table.display').DataTable().clear();
      DataTablesCore.$('table.display').DataTable().rows.add(data.value);
      DataTablesCore.$('table.display').DataTable().draw();
      console.log(data.value);
    });


  fetch('http://node64.webte.fei.stuba.sk:8181/curlBp.php?option=2')
    .then(r => r.json())
    .then((res) => {
      res.map(row => {
        data.value.push(
          [row.garpra, row.typ, row.nazov, row.veduci, row.program]
        )

      })
      DataTablesCore.$('table.display').DataTable().clear();
      DataTablesCore.$('table.display').DataTable().rows.add(data.value);
      DataTablesCore.$('table.display').DataTable().draw();
      console.log(data.value);
    });

  fetch('http://node64.webte.fei.stuba.sk:8181/curlBp.php?option=3')
    .then(r => r.json())
    .then((res) => {
      res.map(row => {
        data.value.push(
          [row.garpra, row.typ, row.nazov, row.veduci, row.program]
        )

      })
      DataTablesCore.$('table.display').DataTable().clear();
      DataTablesCore.$('table.display').DataTable().rows.add(data.value);
      DataTablesCore.$('table.display').DataTable().draw();
      console.log(data.value);
    });

  fetch('http://node64.webte.fei.stuba.sk:8181/curlBp.php?option=4')
    .then(r => r.json())
    .then((res) => {
      res.map(row => {
        data.value.push(
          [row.garpra, row.typ, row.nazov, row.veduci, row.program]
        )

      })
      DataTablesCore.$('table.display').DataTable().clear();
      DataTablesCore.$('table.display').DataTable().rows.add(data.value);
      DataTablesCore.$('table.display').DataTable().draw();
      console.log(data.value);
    });

}


function getUniqueValuesProgram(array) {
  const uniqueSet = new Set(array.map(item => item[4]));
  return Array.from(uniqueSet);
}

const uniqueData = computed(() => getUniqueValuesProgram(data.value));

function getUniqueValuesVeduci(array) {
  const uniqueSet = new Set(array.map(item => item[3]));
  return Array.from(uniqueSet);
}

const uniqueDataVeduci = computed(() => getUniqueValuesVeduci(data.value));

let selectedOption = ref(null);

const filterDataTable = () => {
  DataTablesCore.$('table.display').DataTable()
    .column(4)
    .search(selectedOption.value)
    .draw();
};

let selectedOptionVeduci = ref(null);

const filterDataTableVeduci = () => {
  DataTablesCore.$('table.display').DataTable()
    .column(3)
    .search(selectedOptionVeduci.value)
    .draw();
};

const resetFilter = () => {
  const table = DataTablesCore.$('table.display').DataTable();
  table.column(3).search(''); // reset filter for 4th column
  table.column(4).search(''); // reset filter for 5th column
  table.draw();
};

function handleClick(rowData) {
  // Access data from the clicked row
  console.log('Clicked row data:', rowData);

  // You can perform actions based on the clicked row data here
  // (e.g., navigate to a detail page, display additional information)
}

onMounted(() => {
  getData();

})
</script>