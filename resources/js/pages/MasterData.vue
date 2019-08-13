<template>
    <div>
        <el-page-header @back="$emit('back')" content="MASTER DATA"> </el-page-header>
        <el-divider></el-divider>
        <el-tabs type="card">
            <el-tab-pane label="Companies"><Company /></el-tab-pane>
            <el-tab-pane label="Delivery Statuses"><DeliveryStatus /></el-tab-pane>
            <el-tab-pane label="Delivery Services"><ServiceType /></el-tab-pane>
            <el-tab-pane label="Delivery Types"><DeliveryType /></el-tab-pane>
            <el-tab-pane label="Users"><User /></el-tab-pane>
            <!-- <el-tab-pane label="Setting"><Setting /></el-tab-pane> -->
        </el-tabs>

    </div>
</template>

<script>
import Company from './Company'
import DeliveryStatus from './DeliveryStatus'
import ServiceType from './ServiceType'
import DeliveryType from './DeliveryType'
import User from './User'

export default {
    components: {
        Company, DeliveryStatus, ServiceType,
        DeliveryType, User
    },
    data() {
        return {

        }
    },
    methods: {

    }

}
</script>

<style lang="scss" scoped>

</style>
