import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

let currentUser = JSON.parse(window.localStorage.getItem('user'))

export default new Vuex.Store({
    state: {
        base_url: BASE_URL,
        user: currentUser || {},
        token: window.localStorage.getItem('token'),
        is_logged_in: !!currentUser,
        agentList: [],
        cityList: [],
        companyList: [],
        customerList: [],
        deliveryTypeList: [],
        serviceTypeList: [],
        deliveryStatusList: [],
        userList: [],
        roleList: [],
    },
    mutations: {
        getAgentList(state) {
            axios.get('/agent/getList').then(r => {
                state.agentList = r.data
            }).catch(e => console.log(e))
        },
        getCompanyList(state) {
            axios.get('/company/getList').then(r => {
                state.companyList = r.data
            }).catch(e => console.log(e))
        },
        getCityList(state) {
            axios.get('/city/getList').then(r => {
                state.cityList = r.data
            }).catch(e => console.log(e))
        },
        getCustomerList(state) {
            axios.get('/customer/getList').then(r => {
                state.customerList = r.data
            }).catch(e => console.log(e))
        },
        getDeliveryTypeList(state) {
            axios.get('/deliveryType/getList').then(r => {
                state.deliveryTypeList = r.data
            }).catch(e => console.log(e))
        },
        getServiceTypeList(state) {
            axios.get('/serviceType/getList').then(r => {
                state.serviceTypeList = r.data
            }).catch(e => console.log(e))
        },
        getDeliveryStatusList(state) {
            axios.get('/deliveryStatus/getList').then(r => {
                state.deliveryStatusList = r.data
            }).catch(e => console.log(e))
        },
        getUserList(state) {
            axios.get('/user/getList').then(r => {
                state.userList = r.data
            }).catch(e => console.log(e))
        },
        getRoleList(state) {
            axios.get('/user/getRoleList').then(r => {
                state.roleList = r.data
            }).catch(e => console.log(e))
        },
    }
})
