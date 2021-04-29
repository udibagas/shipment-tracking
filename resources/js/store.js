import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

let currentUser = JSON.parse(window.localStorage.getItem('user'))

export default new Vuex.Store({
    state: {
        base_url: BASE_URL,
        user: currentUser || {},
        is_logged_in: !!currentUser,
        agentList: [],
        cityList: [],
        companyList: [],
        customerList: [],
        delayCauseList: [],
        deliveryTypeList: [],
        serviceTypeList: [],
        vehicleTypeList: [],
        deliveryStatusList: [{
                id: null,
                name: 'DRAFT',
                type: 'info'
            },
            {
                id: 0,
                name: 'TERDAFTAR',
                type: 'info'
            },
            {
                id: 1,
                name: 'SIAP KIRIM',
                type: 'warning'
            },
            {
                id: 2,
                name: 'DALAM PENGIRIMAN',
                type: 'primary'
            },
            {
                id: 3,
                name: 'TERKIRIM',
                type: 'success'
            },
            {
                id: 4,
                name: 'STT DITERIMA',
                type: 'success'
            },
        ],
        userList: [],
        roleList: [],
        navigation: [],
        filterYearList: [],
    },
    mutations: {
        getAgentList(state) {
            axios.get('/agent').then(r => {
                state.agentList = r.data
            }).catch(e => console.log(e))
        },
        getCompanyList(state) {
            axios.get('/company').then(r => {
                state.companyList = r.data
            }).catch(e => console.log(e))
        },
        getCityList(state) {
            axios.get('/city').then(r => {
                state.cityList = r.data
            }).catch(e => console.log(e))
        },
        getCustomerList(state) {
            axios.get('/customer').then(r => {
                state.customerList = r.data
            }).catch(e => console.log(e))
        },
        getDeliveryTypeList(state) {
            axios.get('/deliveryType').then(r => {
                state.deliveryTypeList = r.data
            }).catch(e => console.log(e))
        },
        getDelayCauseList(state) {
            axios.get('/delayCause').then(r => {
                state.delayCauseList = r.data
            }).catch(e => console.log(e))
        },
        getServiceTypeList(state) {
            axios.get('/serviceType').then(r => {
                state.serviceTypeList = r.data
            }).catch(e => console.log(e))
        },
        getVehicleTypeList(state) {
            axios.get('/vehicleType').then(r => {
                state.vehicleTypeList = r.data
            }).catch(e => console.log(e))
        },
        // getDeliveryStatusList(state) {
        //     axios.get('/deliveryStatus/getList').then(r => {
        //         state.deliveryStatusList = r.data
        //     }).catch(e => console.log(e))
        // },
        getUserList(state) {
            axios.get('/user').then(r => {
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
        }
    }
})
