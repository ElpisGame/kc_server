<template>
    <el-container>
        <el-header style="padding: 0px;">
            <Nav/>
        </el-header>
        <el-container>
            <el-main style="padding: 0px;">
                <router-view/>
            </el-main>
        </el-container>
    </el-container>
</template>

<script>
    import Login from "./components/Login";
    import Nav from "./components/Nav";

    export default {
        name: "App",
        components: {Nav, Login},
        data: () => {
            return {};
        },
        beforeCreate() {

        },
        mounted: function () {
        },
        methods: {}
    }
</script>

<style>
    .header {
        padding: 10px;
        background-color: rgb(238, 241, 246);
    }

    .dialog-body {
        height: 500px;
        overflow-y: auto;
    }

    body, * {
        font-family: "Helvetica Neue", Helvetica, "PingFang SC", "Hiragino Sans GB", "Microsoft YaHei", "微软雅黑", Arial, sans-serif;
    }
</style>
