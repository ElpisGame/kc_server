<template>
    <div>
        <div style="background: #f9f9f9;width:350px;padding: 20px;margin: 10px;">
<el-form :model="formData" label-width="80px">
    <el-form-item label="账号角色">
        <el-input v-model="formData.username" class="form-control" autocomplete="off" />
    </el-form-item>

    <el-form-item label="套餐">
        <el-select v-model="formData.goodsid" placeholder="请选择">
            <el-option v-for="item in charges" :key="item.value" :label="item.label" :value="item.value">
            </el-option>
        </el-select>
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

    export default {
        name: "Charge",
        data: () => {
            return {
                formData: {
                    username: '',
                    goodsid: ''
                },
                charges: [
                    {
                        value: 40,
                        label: '满级VIP'
                    },
                    {
                        value: 19,
                        label: '月卡'
                    }
                    , {
                        value: 38,
                        label: '终身卡'
                    }
                    , {
                        value: 2,
                        label: '2000元宝'
                    }

                    , {
                        value: 1,
                        label: '首充 10元'
                    }
                    , {
                        value: 3,
                        label: '首充 30元'
                    }
                    , {
                        value: 5,
                        label: '首充 100元'
                    }
                    , {
                        value: 6,
                        label: '首充 200元'
                    }
                    , {
                        value: 41,
                        label: '守护礼包'
                    }
                    , {
                        value: 42,
                        label: '神兵礼包'
                    }
                    , {
                        value: 43,
                        label: '坐骑礼包'
                    }
                    , {
                        value: 44,
                        label: '翅膀礼包'
                    }
                    , {
                        value: 45,
                        label: '仙侣礼包'
                    }
                    , {
                        value: 46,
                        label: '法阵礼包'
                    }
                    , {
                        value: 47,
                        label: '仙位礼包'
                    }
                    , {
                        value: 48,
                        label: '玄女礼包'
                    }
                    , {
                        value: 49,
                        label: '花辇礼包'
                    }
                    , {
                        value: 50,
                        label: '灵气礼包'
                    }
                    , {
                        value: 51,
                        label: '宠物礼包'
                    }
                    , {
                        value: 52,
                        label: '通灵礼包'
                    }
                    , {
                        value: 53,
                        label: '兽魂礼包'
                    }

                ]
            }
        },

        mounted() {
        },

        methods: {

            submitForm: function () {
                console.log(this.formData)
                https.post('/api/game/charge', this.formData).then(resp => {
                    console.log(resp.data);
                    if (resp.data === 0)
                        this.$message.error("执行失败");
                    else
                        this.$message.success("充值成功");
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