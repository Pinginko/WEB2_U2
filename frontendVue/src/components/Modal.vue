<template>
    <div class="modal-dialog w-100 d-flex allign-items-center">
        <div class="modal-content">
            <form>
                <div class="modal-header">
                    <h5 class="modal-title text-primary">Vytvor novú akciu</h5>
                </div>
                <div class="modal-body">
                    <div class="input-group mb-3">
                        <select name="day" v-model="day">
                            <option v-for="item in dataSelect['day']" :value="item[0]">
                                {{ item[0] }}
                            </option>
                        </select>
                    </div>
                    <div class="input-group mb-3">
                        <select name="subject" v-model="subject">
                            <option v-for="id in dataSelect['subject']" :value="id[0]">
                                {{ id[1] }}
                            </option>
                        </select>
                    </div>
                    <div class="input-group mb-3">
                        <select name="room" v-model="room">
                            <option v-for="item in dataSelect['room']" :value="item[0]">
                                {{ item[1] }}
                            </option>
                        </select>
                    </div>

                    <div class="input-group mb-3">
                        <select name="type" v-model="type">
                            <option v-for="item in dataSelect['type']" :value="item[0]">
                                {{ item[1] }}
                            </option>
                        </select>
                    </div>

                </div>
                <div class="modal-footer">
                    <button @click="closeModal" type="button" class="btn btn-secondary "
                        data-bs-dismiss="modal">Close</button>
                    <button @click="submitForm" type="button" class="btn btn-primary ms-2">Save changes</button>

                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
form {
    display: flex;
    flex-direction: column;
    width: 100%;
    align-items: center;
    justify-content: center;
}

select {
    width: 100%;
    border-radius: 10px;
}

.modal-body {

    width: 40%;

}
</style>


<script setup>

import { ref, defineEmits, onMounted } from 'vue';

const isVisible = ref(false);

const closeModal = () => {
    isVisible.value = false;
    closeModalEvent('close-modal');
};

const closeModalEvent = defineEmits(['close-modal'])
const dataSelect = ref([]);

async function fetchData() {
    fetch('http://node64.webte.fei.stuba.sk:8181/modalSelect.php')
        .then(response => response.json())
        .then(data => {
            dataSelect.value = data;
            //console.log(dataSelect.value);

        })
        .catch(error => {
            console.error('Error fetching data:', error);
        });
}

const day = ref('');
const subject = ref('');
const room = ref('');
const type = ref('');

const submitForm = async () => {
    const formData = new FormData();
    formData.append('day', day.value);
    formData.append('subject', subject.value);
    formData.append('room', room.value);
    formData.append('type', type.value);

    const response = await fetch('http://node64.webte.fei.stuba.sk:8181/apiTimetable.php', {
        method: 'POST',
        body: formData
    });

    if (!response.ok) {
        console.error('Failed to submit form:', response.statusText);
        return;
    }

    const result = await response.json();
    console.log(result);
};




onMounted(async () => {

    await fetchData();

});



</script>