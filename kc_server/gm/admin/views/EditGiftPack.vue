<template>
    <div>
        <div style="background: #f9f9f9;width:550px;padding: 20px;margin: 10px;">
            <el-form :model="formData" label-width="80px">

                <el-form-item label="套餐">
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
                    <el-input
                        type="textarea"
                        :rows="10"
                        v-model="formData.raw">
                    </el-input>
                    <el-form-item>
                        <el-button size="small" type="primary" plain @click="submitForm()">
                            保存
                        </el-button>
                    </el-form-item>
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
                giftPack: '',
                raw: ''
            },
            giftPacks: [],
            selectedGift: null,
            selectedGiftRaw: null,
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
        changeGiftPack: function (value) {
            this.giftPacks.forEach((item) => {
                if (item.value === value) {
                    this.selectedGift = item;
                    this.formData.raw = item.raw;
                }
            })
        },
        submitForm: function () {
            this.formData.raw = this.formData.raw.trim();
            https.post('/api/game/saveGiftPack', this.formData).then(resp => {
                if (resp.data === 1) {
                    this.$message.success("保存成功");
                    this.init();
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
