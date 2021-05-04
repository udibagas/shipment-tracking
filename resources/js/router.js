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
import MasterFare from './pages/MasterFare'
import Invoice from './pages/Invoice'
import CompanyProfile from './pages/CompanyProfile'
import Login from './pages/Login'

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
      path: '/master-fare',
      component: MasterFare,
      name: 'master-fare'
    },
    {
      path: '/report',
      component: Report,
      name: 'report'
    },
    {
      path: '/invoice',
      component: Invoice,
      name: 'invoice'
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
      path: '/company-profile',
      component: CompanyProfile,
      name: 'company-profile',
    },
    {
      path: '/login',
      component: Login,
      name: 'login',
      meta: { layout: 'simple' }
    },
    {
      path: '*',
      component: Home
    },
  ]
})

router.beforeEach((to, from, next) => {
  if (to.path == '/login') {
    next()
  }

  else {
    let params = { route: to.path }
    axios.get('/api/auth/check', { params }).then(r => {
      next()
    }).catch(e => {
      next('/login')
      Message({
        message: 'Anda tidak berhak mengakses halaman ini atau sesi anda telah berakhir. Silakan login ulang.',
        type: 'error',
        showClose: true,
        duration: 10000
      })
    })
  }

});

export default router
