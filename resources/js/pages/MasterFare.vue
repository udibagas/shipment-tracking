<template>
    <div>
        <el-page-header @back="$emit('back')" content="MASTER TARIF"> </el-page-header>
        <el-divider></el-divider>

        <el-tabs type="card">
            <el-tab-pane lazy label="KOTA"><City /></el-tab-pane>
            <el-tab-pane lazy label="JENIS ARMADA"><VehicleType /></el-tab-pane>
            <el-tab-pane lazy label="REGULER"><MasterFareReguler /></el-tab-pane>
            <el-tab-pane lazy label="CHARTER"><MasterFareCharter /></el-tab-pane>
            <el-tab-pane lazy label="PACKING PETI"><MasterFarePacking /></el-tab-pane>
        </el-tabs>
    </div>
</template>

<script>
import MasterFareReguler from './MasterFareReguler'
import MasterFareCharter from './MasterFareCharter'
import MasterFarePacking from './MasterFarePacking'
import City from './City'
import VehicleType from './VehicleType'

export default {
    components: { MasterFareReguler, MasterFareCharter, MasterFarePacking, VehicleType, City },
    mounted() {
        this.$store.commit('getCustomerList');
        this.$store.commit('getCityList');
        this.$store.commit('getVehicleTypeList');
    }
}
</script>
