<template>
    <div>
        <div style="background: #f9f9f9;width:350px;padding: 20px;margin: 10px;">
            <el-form :inline="true" :rules="formRules" label-width="80px">
                <el-form-item label="执行命令">
                    <el-select v-model="cmd" placeholder="请选择">
                        <el-option
                            v-for="item in commands"
                            :key="item.value"
                            :label="item.label"
                            :value="item.value">
                        </el-option>
                    </el-select>
                    &nbsp;
                    <el-button size="" type="primary" plain @click="execute()">
                        执行
                    </el-button>
                </el-form-item>
            </el-form>
        </div>
    </div>
</template>

<script>
import https from "../https";
import {Loading} from 'element-ui';

export default {
    name: "Shell",
    data: () => {
        return {
            commands: [
                {label: '更新资源', value: 'updateall'},
                // {label: '重启', value: 'restartbase'},
                // {label: '启动游戏', value: 'startbase'},
                // {label: '停止游戏', value: 'forcestopall'},
            ],
            cmd: '',
            output: '',
            formRules: {}
        }
    },

    mounted() {
    },

    methods: {

        execute: function () {
            let loadingInstance = Loading.service({fullscreen: true});
            https.get('/api/game/shell', {cmd: this.cmd}).then(resp => {
                console.log(resp)
                if (resp.data === 0)
                    this.$message.error("执行失败");
                else
                    this.$message.success("执行成功");
            }).finally(() => {
                loadingInstance.close()
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
