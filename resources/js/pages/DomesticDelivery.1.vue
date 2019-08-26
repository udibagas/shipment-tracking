<template>
    <div>
        <el-page-header @back="$emit('back')" content="DOMESTIC DELIVERIES"> </el-page-header>
        <el-divider></el-divider>

        <el-tabs type="card">
            <el-tab-pane v-for="status in statuses" :key="status.id">
                <span slot="label">
                    {{status.name}}
                    <el-badge v-if="total[status.id] > 0" type="warning" :value="total[status.id]" />
                </span>
                <Table :timestamp="ts" :status="status.id" @loaded="(data) => { total[status.id] = data.total; ts = new Date(); }" />
            </el-tab-pane>
        </el-tabs>
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
    data() {
        return {
            ts: new Date(),
            total: { 0: 0, 1: 0, 2: 0, 3: 0, 4: 0, 5: 0, 6: 0, "-1": 0 }
        }
    },
    mounted() {
        this.$store.commit('getCityList');
        this.$store.commit('getDeliveryTypeList');
        this.$store.commit('getServiceTypeList');
        this.$store.commit('getCustomerList');
        this.$store.commit('getAgentList');
    }
}
</script>
