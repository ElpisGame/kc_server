<template>
    <div>
        <!--        <vue-json-editor-->
        <!--            v-model="json"-->
        <!--            :expandedOnStart="true"-->
        <!--            :show-btns="true"-->
        <!--            :mode="'form'"-->
        <!--            lang="zh"-->
        <!--            @json-change="onJsonChange"-->
        <!--            @json-save="onJsonSave"-->
        <!--            @has-error="onError"></vue-json-editor>-->
    </div>
</template>

<script>
import https from "../https";
import vueJsonEditor from 'vue-json-editor';

export default {
    name: "Dashboard",
    data: function () {
        return {
            directories: [],
            file: {},
            rows: [],
            json: {
                msg: 'demo of jsoneditor'
            }
        }
    },
    components: {
        vueJsonEditor
    },
    mounted() {

    },
    methods: {
        onJsonChange(value) {
            console.log('value:', value);
        },
        onJsonSave(value) {
            console.log('value:', value);
        },
        onError(value) {
            console.log('value:', value);
        }
    },
};
</script>

<style>
.file {
    border-bottom: 1px solid #f0f0f0;
    cursor: pointer;
}

.file:hover {
    background: yellow;
}

.lua {
    width: 100%;
    margin: 0;
    padding: 0;
    height: 200px;
    background: #333;
    color: #fff;
    display: block;
    overflow-y: scroll;
}

.row {
    border: 1px solid red;
}

.objectTable {
    border-collapse: collapse;

}

.objectTable td {
    border: 1px solid #ccc;
}

.jsoneditor-vue {
    height: 800px;
}
</style>
