<template>
    <div>
        <select>
            <option v-for="item in items" :value="item.value" :key="item.value">
                {{ item.value }} {{ item.label }}
            </option>
        </select>
    </div>
</template>

<script>
import https from "../https";

export default {
    name: "Onekey",
    data: function () {
        return {
            formData: {},
            items: []
        }
    },
    mounted() {
        this.init();
    },
    methods: {
        init: function () {
            https.get('/api/game/items').then(resp => {
                this.items = resp;
                console.log(this.items)
            });
        }
    }
}
</script>

<style scoped>

</style>

