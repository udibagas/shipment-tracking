<template>
    <div>
        <el-page-header @back="$emit('back')" content="PENGIRIMAN DOMESTIK"> </el-page-header>
        <el-divider></el-divider>

        <Table />
    </div>
</template>

<script>

import Table from './domestic-delivery/Table'

export default {
    components: { Table },
    computed: {
        statuses() {
            let statuses = JSON.parse(JSON.stringify(this.$store.state.deliveryStatusList))
            statuses.push({ id: '-1', name: 'Summary' })
            return statuses
        }
    },
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
