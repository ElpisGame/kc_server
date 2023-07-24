import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

const routes = [
    {path: '/', name: 'Dashboard', component: () => import('../views/Dashboard')},
    {path: '/players', name: 'Player', component: () => import('../views/Player')},
    {path: '/mails', name: 'Mail', component: () => import('../views/Mail')},
    {path: '/onekey', name: 'Onekey', component: () => import('../views/Onekey')},
    {path: '/gmcmd', name: 'Gmcmd', component: () => import('../views/Gmcmd')},
    {path: '/go', name: 'Go', component: () => import('../views/Go')},
    {path: '/shell', name: 'Shell', component: () => import('../views/Shell')},
    {path: '/charge', name: 'Charge', component: () => import('../views/Charge')},
    {path: '/sendgiftpack', name: 'SendGiftPack', component: () => import('../views/SendGiftPack')},
    {path: '/editgiftpack', name: 'EditGiftPack', component: () => import('../views/EditGiftPack')},
    {path: '/silent', name: 'Silent', component: () => import('../views/Silent')},
    {path: '/broadCast', name: 'BroadCast', component: () => import('../views/BroadCast')},

];

const router = new VueRouter({
    routes
});

export default router
