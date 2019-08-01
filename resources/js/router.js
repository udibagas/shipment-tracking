import Vue from 'vue'
import VueRouter from 'vue-router'
import { Message } from 'element-ui';
import Home from './pages/Home'
import Customer from './pages/Customer'
import Company from './pages/Company'
import User from './pages/User'
import Agent from './pages/Agent'
import DeliveryStatus from './pages/DeliveryStatus'
import ServiceType from './pages/ServiceType'
import DeliveryType from './pages/DeliveryType'
import DomesticDelivery from './pages/DomesticDelivery'

Vue.use(VueRouter)

const router = new VueRouter({
    routes: [
        { path: '/', component: Home, name: 'home' },
        { path: '/agent', component: Agent, name: 'agent' },
        { path: '/customer', component: Customer, name: 'customer' },
        { path: '/company', component: Company, name: 'company' },
        { path: '/delivery-status', component: DeliveryStatus, name: 'delivery-status' },
        { path: '/delivery-type', component: DeliveryType, name: 'delivery-type' },
        { path: '/domestic-delivery', component: DomesticDelivery, name: 'domestic-delivery' },
        { path: '/service-type', component: ServiceType, name: 'service-type' },
        { path: '/user', component: User, name: 'user' },
        { path: '*', component: Home },
    ]
})

router.beforeEach((to, from, next) => {
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
});

export default router
