<template>
    <div>
        <div style="background: #f9f9f9;width:350px;padding: 20px;margin: 10px;">
            <el-form label-width="80px">
                <el-form-item label="账号角色" ref="username">
                    <el-input v-model="formData.username" class="form-control" autocomplete="off"/>
                </el-form-item>
                <el-form-item>
                    <el-button size="" type="warning" plain @click="silent">
                        禁言
                    </el-button>
                    <el-button size="" type="danger" plain @click="unsilent">
                        解除禁言
                    </el-button>
                </el-form-item>

                <el-form-item>
                    <el-button size="" type="warning" plain @click="sealed">
                        封禁
                    </el-button>
                    <el-button size="" type="danger" plain @click="unsealed">
                        解除封禁
                    </el-button>
                </el-form-item>
            </el-form>
        </div>
    </div>
</template>

<script>
import https from "../https";

export default {
    name: "Silent",
    data: () => {
        return {
            formData: {
                username: ''
            }
        }
    },

    mounted() {
    },

    methods: {
        silent: function () {
            https.post('/api/game/silent', this.formData).then(resp => {
                console.log(resp.data);
                if (resp.data === 0)
                    this.$message.error("执行失败");
                else
                    this.$message.success("禁言成功");
            })
        },

        unsilent: function () {
            https.post('/api/game/unsilent', this.formData).then(resp => {
                console.log(resp.data);
                if (resp.data === 0)
                    this.$message.error("执行失败");
                else
                    this.$message.success("解除禁言成功");
            })
        },

        sealed: function () {
            https.post('/api/game/sealed', this.formData).then(resp => {
                console.log(resp.data);
                if (resp.data === 0)
                    this.$message.error("执行失败");
                else
                    this.$message.success("封禁成功");
            })
        },

        unsealed: function () {
            https.post('/api/game/unsealed', this.formData).then(resp => {
                console.log(resp.data);
                if (resp.data === 0)
                    this.$message.error("执行失败");
                else
                    this.$message.success("解除封禁成功");
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
