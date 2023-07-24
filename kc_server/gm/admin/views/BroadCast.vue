<template>
    <div>
        <div style="background: #f9f9f9;width:1280px;padding: 20px;margin: 10px;">
            
            <el-form :model="formData" :rules="formRules" label-width="10px">
                <!-- <el-form-item label="账号角色" ref="username">
                    <input v-model="formData.username" class="form-control" autocomplete="off"/>
                </el-form-item> -->

                <el-form-item label-width = "5px">
                <!-- <el-form-item label="内容" ref="num" label-width = "10px"> -->
                    <input type="textarea" v-model="formData.noticeContent" placeholder="内容" class="form-control"
                           autocomplete="off"

                        style= "width:1200px;height:50px;font-size:15px;text-align:center;background-color:lightblue"
                           />
                    <p>{{formData.noticeContent}}</p>
                </el-form-item>

                  <el-select v-model="formData.noticeType" placeholder="请选择">
                        <el-option
                            v-for="item in commands"
                            :key="item.value"
                            :label="item.label"
                            :value="item.value">
                        </el-option>
                </el-select>
                    &nbsp;
                <el-form-item>
                    <el-button size="small" type="primary" style="width:100px;margin-top:30px " plain @click="submitForm()">
                        广播
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
    name: "BroadCast",
    data: () => {
        return {
             commands: [
                {label: '类型1（滚动,显示在系统聊天）', value: 1},
                {label: '类型2（系统公告显示2s）', value: 2},
                {label: '类型3（滚动,不显示在系统聊天）', value: 3},
            ],
            formData: {
                noticeContent:"",
                noticeType:1,
            },
            formRules: {}
        }
    },

    mounted() {
    },

    methods: {
        submitForm: function () {
            console.log(this.formData);
            if(this.formData.noticeContent=="")
            {
                this.$message.success("广播失败,内容为空！");
                return;
            }
            https.post('/api/game/broadCast', this.formData).then(resp => {
                console.log(resp.data);
                this.$message.success("广播成功");
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
