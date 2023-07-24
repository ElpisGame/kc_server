<template>
    <div>
        <div style="background: #f9f9f9;width:350px;padding: 20px;margin: 10px;">
            <!--发送邮件-->
            <el-form :model="formData" ref="mail" :rules="formRules" label-width="80px">
                <el-form-item label="账号角色" ref="username">
                    <input v-model="formData.username" class="form-control" autocomplete="off"/>
                </el-form-item>

                <el-form-item label="邮件类型" ref="itemtype">
                    <select class="form-control" v-model="formData.itemtype">
                        <option value="0">货币</option>
                        <option value="1">物品</option>
                    </select>
                </el-form-item>

                <el-form-item label="货币类型" v-if="formData.itemtype==0" ref="item">
                    <select class="form-control" v-model="formData.item">
                        <option value="0">经验</option>
                        <option value="1">金币</option>
                        <option value="2">元宝</option>
                        <option value="3">绑定元宝</option>
                    </select>
                </el-form-item>

                <el-form-item label="物品名称" v-if="formData.itemtype==1" ref="item">
                    <div style="position: relative">
                        <select class="form-control" v-model="formData.item">
                            <option v-for="item in itemOptions" :value="item.value" :key="item.value">
                                {{ item.label }}
                            </option>
                        </select>
                        <input placeholder="物品名称过滤"
                               autocomplete="off"
                               @input="filterItem"
                               class="form-control"
                               style="background: #ddffbe"/>
                    </div>
                </el-form-item>

                <el-form-item label="数量" ref="num">
                    <input type="number" v-model="formData.num" placeholder="数量" class="form-control"
                           autocomplete="off"/>
                </el-form-item>
                <el-form-item>
                    <el-button size="small" type="primary" plain @click="submitForm('mail')">
                        发送邮件
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
    name: "Gmcmd",
    data: () => {
        return {
            items: [],
            itemOptions: [],
            formData: {
                username: '',
                itemtype: 1,
                item: 0,
                num: 1,
            },
            formRules: {
                username: [{required: true}]
            }
        }
    },

    mounted() {
        this.init();
    },

    methods: {
        init: function () {
            let loadingInstance = Loading.service({fullscreen: true});
            https.get('/api/game/items').then(resp => {
                this.itemOptions = this.items = resp.data;
                if (this.itemOptions.length !== 0) {
                    this.formData.item = this.itemOptions[0].value;
                }
            }).finally(() => {
                loadingInstance.close()
            });
        },
        submitForm: function (formName) {
            console.log(formName);
            switch (formName) {
                case "mail":
                    this.sendMail();
                    break;
            }
        },
        filterItem: function (e) {
            if (e.target.value === '') {
                this.itemOptions = this.items;
                this.formData.item = '';
                return;
            }
            this.itemOptions = this.items.filter(item => {
                if (item.label.indexOf(e.target.value) !== -1) {
                    return true;
                }
            });
            if (this.itemOptions.length !== 0) {
                this.formData.item = this.itemOptions[0].value;
            }
        }
        ,
        sendMail: function () {
            if (this.formData.username.trim() === '') {
                this.$message.error("账号为空");
                return;
            }
            https.post("/api/game/sendMail", this.formData).then(resp => {
                if (resp.data === 0) {
                    this.$message.error("邮件发送失败");
                } else {
                    this.$message.success("邮件发送成功");
                }
            })
        }
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
