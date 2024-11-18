<template>
    <div>
        <el-page-header @back="$emit('back')" content="PENGIRIMAN DOMESTIK">
        </el-page-header>
        <br />
        <DomesticDeliveryListCustomer v-if="user.role == 41" />
        <DomesticDeliveryList v-else />
    </div>
</template>

<script>
import { mapGetters } from "vuex";
import DomesticDeliveryList from "./domestic-delivery/DomesticDeliveryList";
import DomesticDeliveryListCustomer from "./domestic-delivery/DomesticDeliveryListCustomer";

export default {
    components: { DomesticDeliveryList, DomesticDeliveryListCustomer },
    computed: {
        ...mapGetters({
            user: "auth/user",
        }),
    },
};
</script>
