import Vue from 'vue'
import Vuex from 'vuex'
import https from "../https";

Vue.use(Vuex)

export default new Vuex.Store({
    state: {
        isLogin: false,
        user: null,
        datePickerOptions: {
            shortcuts: [
                {
                    text: '今天',
                    onClick(picker) {
                        const end = new Date();
                        const start = new Date();
                        end.setTime(start.getTime() + 3600 * 1000 * 24);
                        // start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
                        picker.$emit('pick', [start, end]);
                    }
                },
                {
                    text: '昨天',
                    onClick(picker) {
                        const end = new Date();
                        const start = new Date();
                        start.setTime(start.getTime() - 3600 * 1000 * 24);
                        picker.$emit('pick', [start, end]);
                    }
                },
                {
                    text: '最近一周',
                    onClick(picker) {
                        const end = new Date();
                        const start = new Date();
                        start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
                        picker.$emit('pick', [start, end]);
                    }
                },
                {
                    text: '最近一月',
                    onClick(picker) {
                        const end = new Date();
                        const start = new Date();
                        start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
                        picker.$emit('pick', [start, end]);
                    }
                },
                {
                    text: '最近三月',
                    onClick(picker) {
                        const end = new Date();
                        const start = new Date();
                        start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
                        picker.$emit('pick', [start, end]);
                    }
                }
            ]
        }
    },
    getters: {
        getUser: state => {
            if (state.user)
                return state.user;
            let user = window.localStorage.getItem("user");
            if (user) {
                state.user = JSON.parse(user);
                return state.user;
            }
            return null;
        }
    },
    mutations: {
        login: function (state) {
            state.isLogin = true;
        },
        logout: function (state) {
            window.localStorage.clear();
            state.isLogin = false;
        }
    },
    actions: {},
    modules: {}
})
