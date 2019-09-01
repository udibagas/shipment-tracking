/*
ROLE USER:
11 : SUPER ADMIN
21 : ADMIN
31 : OPERATOR
41 : CUSTOMER
51 : AGENT
*/

import Vue from 'vue'
import VueRouter from 'vue-router'
import { Message } from 'element-ui';
import Home from './pages/Home'
import Customer from './pages/Customer'
import Agent from './pages/Agent'
import DomesticDelivery from './pages/DomesticDelivery'
import MasterData from './pages/MasterData'
import Report from './pages/Report'

Vue.use(VueRouter)

const router = new VueRouter({
    routes: [
        {
            path: '/',
            component: Home,
            name: 'home',
            meta: {
                roles: [11, 21, 31, 41, 51]
            }
        },
        {
            path: '/agent',
            component: Agent,
            name: 'agent',
            meta: {
                roles: [11, 21, 31] }
        },
        {
            path: '/customer',
            component: Customer,
            name: 'customer',
            meta: {
                roles: [11, 21, 31]
            }
        },
        {
            path: '/domestic-delivery',
            component: DomesticDelivery,
            name: 'domestic-delivery',
            meta: {
                roles: [11, 21, 31]
            }
        },
        {
            path: '/report',
            component: Report,
            name: 'report',
            meta: {
                roles: [11, 21, 31]
            }
        },
        {
            path: '/master-data',
            component: MasterData,
            name: 'master-data',
            meta: {
                roles : [11, 21]
            }
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
