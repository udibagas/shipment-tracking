<template>
    <div>
        <el-page-header @back="$emit('back')" content="SETTINGS"> </el-page-header>
        <el-divider></el-divider>
        <el-tabs type="card">
            <!-- <el-tab-pane lazy label="Delivery Statuses"><DeliveryStatus /></el-tab-pane> -->
            <el-tab-pane lazy label="Delivery Services"><ServiceType /></el-tab-pane>
            <el-tab-pane lazy label="Delivery Types"><DeliveryType /></el-tab-pane>
            <el-tab-pane lazy label="Delay Causes"><DelayCause /></el-tab-pane>
        </el-tabs>

    </div>
</template>

<script>
// import DeliveryStatus from './DeliveryStatus'
import ServiceType from './ServiceType'
import DeliveryType from './DeliveryType'
import DelayCause from './DelayCause'

export default {
    components: { ServiceType, DeliveryType, DelayCause },
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
