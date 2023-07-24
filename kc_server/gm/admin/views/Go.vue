<template>
    <div>
        <div style="background: #f9f9f9;width:350px;padding: 20px;margin: 10px;">
            <el-form :model="formData" :rules="formRules" label-width="80px">
                <el-form-item label="账号角色" ref="username">
                    <input v-model="formData.username" class="form-control" autocomplete="off"/>
                </el-form-item>

                <el-form-item label="关卡" ref="num">
                    <input type="number" v-model="formData.chapterlevel" placeholder="关卡" class="form-control"
                           autocomplete="off"/>
                </el-form-item>
                <el-form-item>
                    <el-button size="small" type="primary" plain @click="submitForm()">
                        跳转
                    </el-button>
                </el-form-item>
            </el-form>
        </div>
    </div>
</template>

<script>
import https from "../https";
import { constants } from 'os';

export default {
    name: "Go",
    data: () => {
        return {
            formData: {
                username: '',
                chapterlevel: 1
            },
            formRules: {}
        }
    },

    mounted() {
    },

    methods: {

        submitForm: function () {
            https.post('/api/game/go', this.formData).then(resp => {
                console.log("---收到回调---");
                console.log(resp.data);
                this.$message.success("跳转成功");
            })
        },

    }
}
</script>

<style scoped>
.form-control {
    padding: 5px;
    font-size: 12px;
    width: 200px;
    box-sizing: content-box;
}
</style>
