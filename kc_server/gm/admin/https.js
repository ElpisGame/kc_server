import axios from 'axios'
import qs from 'qs'

axios.defaults.baseURL = 'http://47.118.55.226:90';
axios.defaults.timeout = 1000 * 30; //响应时间
axios.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=UTF-8';        //配置请求头


//POST传参序列化(添加请求拦截器)
axios.interceptors.request.use((config) => {
    let token = window.localStorage.getItem("token");
    if (token) {
        //config.headers['Authorization'] = 'Bearer ' + token;
        //fix ios11 bug
        if (config.params) {
            config.params['token'] = token;
        } else {
            config.params = {token: token};
        }
    }
    //处理POST请求的参数问题
    if (config.method === 'post') {
        config.data = qs.stringify(config.data);
    }
    return config;
}, (error) => {
    window.console.log("错误的传参");
    return Promise.reject(error);
});

//返回状态判断(添加响应拦截器)
axios.interceptors.response.use((res) => {
    return res;
}, (error) => {
    return Promise.reject(error);
});

/**
 * 发送post请求 添加记录
 * @param url
 * @param params
 * @returns {Promise<unknown>}
 */
function post(url, params) {
    return axios.post(url, params);
}

function put(url, params) {
    return axios.put(url, params);
}

/**
 * 发送get请求
 * @param url
 * @param param
 * @returns {Promise<unknown>}
 */
function get(url, param) {
    return axios.get(url, {params: param});
}

function destroy(url, param) {
    return axios.delete(url, {params: param});
}

function setBaseUrl(baseUrl) {
    axios.defaults.baseURL = baseUrl;
}

export default {
    post,
    put,
    get,
    destroy,
    setBaseUrl
}
