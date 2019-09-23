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
        delayCauseList: [],
        deliveryTypeList: [],
        serviceTypeList: [],
        vehicleTypeList: [],
        deliveryStatusList: [
            {id: 0, name: 'Registered' },
            {id: 1, name: 'Ready For Delivery' },
            {id: 2, name: 'On Delivery' },
            {id: 3, name: 'Delivered' },
            {id: 4, name: 'Received' },
            {id: 5, name: 'Invoice Sent' },
            {id: 6, name: 'Invoice Paid' }
        ],
        userList: [],
        roleList: [],
        navigation: [],
        filterYearList: []
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
        getDelayCauseList(state) {
            axios.get('/delayCause/getList').then(r => {
                state.delayCauseList = r.data
            }).catch(e => console.log(e))
        },
        getServiceTypeList(state) {
            axios.get('/serviceType/getList').then(r => {
                state.serviceTypeList = r.data
            }).catch(e => console.log(e))
        },
        getVehicleTypeList(state) {
            axios.get('/vehicleType/getList').then(r => {
                state.vehicleTypeList = r.data
            }).catch(e => console.log(e))
        },
        // getDeliveryStatusList(state) {
        //     axios.get('/deliveryStatus/getList').then(r => {
        //         state.deliveryStatusList = r.data
        //     }).catch(e => console.log(e))
        // },
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
        getFilterYear(state) {
            axios.get('/report/getFilterYear').then(r => {
                state.filterYearList = r.data
            }).catch(e => console.log(e))
        },
        getNavigation(state) {
            axios.get('/getNavigation').then(r => {
                state.navigation = r.data
            }).catch(e => console.log(e))
        },
    }
})
