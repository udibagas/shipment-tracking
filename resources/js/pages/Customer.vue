<template>
    <div>
        <el-page-header @back="$emit('back')" content="CUSTOMER"> </el-page-header>
        <br>
        <CustomerList :selection="false" />
    </div>
</template>

<script>
import CustomerList from './CustomerList'

export default {
    components: { CustomerList }
}
</script>

