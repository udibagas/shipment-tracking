<template>
    <div>
        <el-page-header @back="$emit('back')" content="CUSTOMER"> </el-page-header>
        <el-divider></el-divider>
        <CustomerList :selection="false" />
    </div>
</template>

<script>
import CustomerList from './CustomerList'

export default {
    components: { CustomerList }
}
</script>

