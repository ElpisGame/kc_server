<template>
    <el-card class="box-card">
        <div slot="header" class="clearfix">
            <span>登录</span>
            <el-button type="text" style="float: right;padding:3px 0px;" @click="loginType=!loginType">
                {{loginType?'账号登录':'扫码登录'}}
            </el-button>
        </div>
        <!--扫码登录-->
        <div v-if="loginType">
            <div v-loading="loading"
                 element-loading-text="拼命加载中..."
                 element-loading-spinner="el-icon-loading"
                 element-loading-background="rgba(0, 0, 0, 0.8)" class="qrcode">
                <img :src="qrcode" style="width: 100%">
            </div>
            <div class="text">
                微信扫一扫登录
            </div>
        </div>
        <!--账号密码登录-->
        <div v-if="!loginType">
            <el-form ref="loginForm" :rules="loginFormRules" :model="user">
                <el-form-item prop="phone">
                    <el-input type="text" v-model="user.phone" prefix-icon="el-icon-user" placeholder="账号"
                              autocomplete="off"></el-input>
                </el-form-item>
                <el-form-item prop="password">
                    <el-input type="password" v-model="user.password" prefix-icon="el-icon-unlock" placeholder="密码"
                              autocomplete="off"></el-input>
                </el-form-item>
                <!--
                <el-row>
                    <el-col :span="12">
                        <el-form-item prop="captcha">
                            <el-input type="text" prefix-icon="el-icon-refresh" placeholder="验证码"
                                      autocomplete="off"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-image
                                src="https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=2142985517,724352710&fm=26&gp=0.jpg"
                                fit="cover"
                        ></el-image>
                    </el-col>
                </el-row>-->
                <el-form-item>
                    <el-button style="width:100%" type="primary"
                               @click="submitForm('loginForm')">登录
                    </el-button>
                </el-form-item>
            </el-form>
        </div>
    </el-card>
</template>

<script>
    import https from "../https";
    // import md5 from 'js-md5';

    export default {
        name: 'Login',
        data() {
            return {
                it: null,
                qrcode: '',
                uuid: '',
                expire: 10000,
                loading: true,
                /*登录方式 二维码登录=true 账号登录=false */
                loginType: true,
                user: {},
                loginFormRules: {
                    phone: [
                        {required: true, message: '手机必须输入', trigger: 'blur'}
                    ],
                    password: [
                        {required: true, message: '密码必须输入', trigger: 'blur'}
                    ]
                },
            }
        },
        methods: {
            submitForm: function (formName) {
                this.$refs[formName].validate(valid => {
                    if (!valid) return;
                    let params = {
                        phone: this.user.phone,
                        password: this.user.password
                    };
                    https.post("/api/login/password/verify", params).then(resp => {
                        window.console.log("resp", resp);
                        if (resp.data.token) {
                            clearInterval(this.it);
                            window.console.clear();
                            window.localStorage.setItem("user", JSON.stringify(resp.data.user));
                            window.localStorage.setItem("token", resp.data.token);
                            this.$store.commit("login");
                        } else {
                            this.$message.error("验证失败");
                        }
                    }).catch(error => {
                        this.$message.error(error.message);
                    });
                });
            }
        },
        mounted: function () {
            let token = window.localStorage.getItem("token");
            if (token) return;
            https.get('/api/auth/wechat/qrcode').then(response => {
                this.loading = false;
                let json = response.data;
                this.qrcode = json.url;
                this.uuid = json.uuid;
                this.expire = Date.now() / 1000 + json.expire;
                this.it = setInterval(() => {
                    window.console.log(parseInt(this.expire - Date.now() / 1000), "秒后二维码过期");
                    if (Date.now() / 1000 >= this.expire) {
                        clearInterval(this.it);
                        this.loading = true;
                    } else {
                        let params = {uuid: this.uuid};
                        https.get("/api/auth/wechat/qrcode/status", params).then(resp => {
                            if (resp.data.token) {
                                clearInterval(this.it);
                                window.console.clear();
                                window.localStorage.setItem("user", JSON.stringify(resp.data.user));
                                window.localStorage.setItem("token", resp.data.token);
                                this.$store.commit("login");
                            }
                        }).catch(error => {
                            this.$message.error(error.message);
                        });
                    }
                }, 2000)
            }).catch(error => {
                this.$message.error(error.message);
            });
        }
    }
</script>

<style scoped>
    .box-card {
        width: 300px;
        margin: 100px auto;
    }

    .qrcode {
        border: 2px dotted #ccc;
        border-radius: 3px;
        margin: 18px auto;
        display: block;
        width: 200px;
        height: 200px;
    }

    .text {
        text-align: center;
        margin-top: 10px;
    }
</style>
