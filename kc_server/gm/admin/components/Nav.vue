<template>
    <div type="flex">
        <el-menu
            :default-active="activeIndex"
            class="el-menu-demo"
            mode="horizontal"
            @select="handleSelect"
            background-color="#545c64"
            text-color="#fff"
            active-text-color="#ffd04b"
        >
            <el-menu-item index="Dashboard">Dashboard</el-menu-item>
            <el-menu-item index="Player">账号列表</el-menu-item>
            <el-menu-item index="Gmcmd">发送邮件</el-menu-item>
            <el-menu-item index="Go">跳转关卡</el-menu-item>
            <el-menu-item index="Shell">更新资源</el-menu-item>
            <el-menu-item index="Charge">充值</el-menu-item>

            <el-submenu index="2">
                <template slot="title">礼包</template>
                <el-menu-item index="SendGiftPack">一键发放</el-menu-item>
                <el-menu-item index="EditGiftPack">编辑礼包</el-menu-item>
            </el-submenu>
            <el-menu-item index="Silent">禁言&解禁</el-menu-item>
            <el-menu-item index="BroadCast">游戏内广播</el-menu-item>

           <el-submenu index="3">
               <template slot="title">账号封禁</template>
               <el-menu-item index="3-1">封号&解封</el-menu-item>
               <el-menu-item index="Silent">禁言&解禁</el-menu-item>
           </el-submenu>
                       <el-menu-item index="4">GM禁发物品</el-menu-item>

            <el-menu-item style="float: right;">
                <el-select v-model="server" placeholder="请选择" @change="changeServer">
                    <el-option
                        v-for="item in servers"
                        :key="item.value"
                        :label="item.label"
                        :value="item.value">
                    </el-option>
                </el-select>
            </el-menu-item>
        </el-menu>
    </div>
</template>

<script>
import https from "../https";

export default {
    name: "Nav",
    data() {
        return {
            activeIndex: "Dashboard",
            server: 'http://47.118.55.226:90',
            servers: [
                {label: '测试服', value: 'http://47.118.55.226:90'},
                {label: '正式服:卧虎藏龙', value: 'http://124.71.61.137:90'},                
                {label: 'localhost', value: 'http://127.0.0.1:90'},
            ]
        };
    },
    mounted() {
        this.active();
    },
    methods: {
        active: function () {
            let activeRoute = this.$router.options.routes.find((item) => {
                if ("#" + item.path == window.location.hash) {
                    return item;
                }
            });
            console.log("activeRoute", activeRoute);
            this.activeIndex = activeRoute.name;
        },
        handleSelect: function (key, keyPath) {
            if (key == "logout") {
                this.logout();
            } else {
                if (this.$route.name != key) {
                    this.$router.push({
                        name: key,
                    });
                }
            }
        },
        logout: function () {
            this.$store.commit("logout");
            this.$router.push("/");
        },
        changeServer: function () {
            console.log(this.server);
            https.setBaseUrl(this.server);
        }
    },
};
</script>

<style scoped>
</style>
