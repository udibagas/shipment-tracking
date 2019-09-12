import Vue from 'vue'
import VueRouter from 'vue-router'
import { Message } from 'element-ui';
import Home from './pages/Home'
import Customer from './pages/Customer'
import Agent from './pages/Agent'
import DomesticDelivery from './pages/DomesticDelivery'
import Setting from './pages/Setting'
import Report from './pages/Report'
import User from './pages/User'
import Company from './pages/Company'

Vue.use(VueRouter)

const router = new VueRouter({
    routes: [
        {
            path: '/',
            component: Home,
            name: 'home'
        },
        {
            path: '/agent',
            component: Agent,
            name: 'agent'
        },
        {
            path: '/company',
            component: Company,
            name: 'company'
        },
        {
            path: '/customer',
            component: Customer,
            name: 'customer'
        },
        {
            path: '/domestic-delivery',
            component: DomesticDelivery,
            name: 'domestic-delivery'
        },
        {
            path: '/report',
            component: Report,
            name: 'report'
        },
        {
            path: '/setting',
            component: Setting,
            name: 'setting'
        },
        {
            path: '/user',
            component: User,
            name: 'user',
        },
        {
            path: '*',
            component: Home
        },
    ]
})

router.beforeEach((to, from, next) => {
    if (to.path == '/') {
        next()
    }

    else {
        let params = { route: to.path }
        axios.get('/checkAuth', { params: params }).then(r => {
            next()
        }).catch(e => {
            Message({
                message: 'Anda tidak berhak mengakses halaman ini.',
                type: 'error',
                showClose: true,
                duration: 10000
            })
            next(false)
        })
    }

});

export default router
