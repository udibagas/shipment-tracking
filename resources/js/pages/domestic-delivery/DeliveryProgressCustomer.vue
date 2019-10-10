<template>
    <el-timeline style="margin-top:20px">
        <el-timeline-item
        size="large"
        v-for="p in progress"
        :key="p.id"
        :timestamp="p.time"
        :type="p.type"
        placement="top">
            <!-- <el-card> -->
                <strong>{{p.status_name}}</strong>
                <p>{{p.note}}</p>
            <!-- </el-card> -->
        </el-timeline-item>
    </el-timeline>
</template>

<script>
export default {
    props: ['id', 'data'],
    watch: {
        id(v) { this.requestData() }
    },
    data() {
        return {
            progress: []
        }
    },
    methods: {
        requestData() {
            let params = { delivery_id: this.id }
            axios.get('/deliveryProgress', { params: params }).then(r => {
                this.progress = r.data.map(d => {
                    d.status_name = this.$store.state.deliveryStatusList[d.status].name
                    d.type = this.$store.state.deliveryStatusList[d.status].type
                    d.time = moment(d.created_at).format('DD-MMM-YYYY HH:mm')
                    return d;
                })
            }).catch(e => console.log(e))
        }
    },
    mounted() {
        this.requestData()
    }
}
</script>
