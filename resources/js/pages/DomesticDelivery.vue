<template>
    <div>
        <el-page-header @back="$emit('back')" content="PENGIRIMAN DOMESTIK"> </el-page-header>
        <el-divider></el-divider>
        <DomesticDeliveryListCustomer v-if="$store.state.user.role == 41" />
        <DomesticDeliveryList v-else />
    </div>
</template>

<script>

import DomesticDeliveryList from './domestic-delivery/DomesticDeliveryList'
import DomesticDeliveryListCustomer from './domestic-delivery/DomesticDeliveryListCustomer'

export default {
    components: { DomesticDeliveryList, DomesticDeliveryListCustomer },
}
</script>
