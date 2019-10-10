<template>
    <div>
        <el-page-header @back="$emit('back')" content="PENGIRIMAN DOMESTIK"> </el-page-header>
        <el-divider></el-divider>
        <Customer v-if="$store.state.user.role == 41" />
        <Table v-else />
    </div>
</template>

<script>

import Table from './domestic-delivery/Table'
import Customer from './domestic-delivery/Customer'

export default {
    components: { Table, Customer },
    mounted() {
        this.$store.commit('getCityList');
        this.$store.commit('getDeliveryTypeList');
        this.$store.commit('getServiceTypeList');
        this.$store.commit('getAgentList');
        this.$store.commit('getCustomerList')
        this.$store.commit('getVehicleTypeList')
    }
}
</script>
