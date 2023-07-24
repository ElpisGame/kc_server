<template>
    <div>
        <div style="background: #f9f9f9;width:350px;padding: 20px;margin: 10px;">
            <el-form :model="formData" label-width="80px">
                <el-form-item label="账号角色">
                    <el-input v-model="formData.username" class="form-control" autocomplete="off"/>
                </el-form-item>

                <el-form-item label="礼包套餐">
                    <el-select v-model="formData.giftPack" placeholder="请选择" @change="changeGiftPack">
                        <el-option
                            v-for="item in giftPacks"
                            :key="item.value"
                            :label="item.label"
                            :value="item.value">
                        </el-option>
                    </el-select>
                </el-form-item>

                <el-form-item v-if="selectedGift" label="套餐内容">
                    <div v-for="(item,index) in selectedGift.gifts" :key="index"
                         style="line-height: 1.5em;font-size: 12px;color:red;">
                        {{ item }}
                    </div>
                </el-form-item>

                <el-form-item>
                    <el-button size="small" type="primary" plain @click="submitForm()">
                        充值
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
    name: "SendGiftPack",
    data: () => {
        return {
            formData: {
                username: '',
                giftPack: ''
            },
            giftPacks: [],
            selectedGift: null,
        }
    },

    mounted() {
        this.init();
    },

    methods: {
        init: function () {
            let loadingInstance = Loading.service({fullscreen: true});
            https.get('/api/game/giftPacks').then(resp => {
                this.giftPacks = resp.data;
            }).finally(() => {
                loadingInstance.close()
            });
        },
        submitForm: function () {
            console.log(this.formData)
            https.post('/api/game/sendGiftPack', this.formData).then(resp => {
                console.log(resp.data);
                if (resp.data === 0)
                    this.$message.error("执行失败");
                else
                    this.$message.success("礼包发放成功");
            })
        },
        changeGiftPack: function (value) {
            this.giftPacks.forEach((item) => {
                if (item.value === value) {
                    this.selectedGift = item;
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
