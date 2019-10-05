<template>
    <div>
        <!-- <el-row :gutter="20">
            <el-col :span="6">
                <el-card class="summary-container bg-cyan">

                </el-card>
            </el-col>
            <el-col :span="6">
                <el-card class="summary-container bg-pink">
                    <h1 class="summary-info"></h1>
                </el-card>
            </el-col>
            <el-col :span="6">
                <el-card class="summary-container bg-teal">

                </el-card>
            </el-col>
            <el-col :span="6">
                <el-card class="summary-container bg-orange">

                </el-card>
            </el-col>
        </el-row> -->

        <el-card>
            <div slot="header">
                <el-button style="float: right; padding: 3px 0" type="text" icon="el-icon-refresh" @click="requestData">Refresh</el-button>
                SUMMARY PENGIRIMAN DOMESTIK
            </div>

            <el-form>
                <el-form-item label="Pilih Tanggal:">
                    <el-date-picker
                    @change="requestData"
                    clearable
                    start-placeholder="Dari"
                    end-placeholder="Sampai"
                    type="daterange"
                    format="dd-MMM-yyyy"
                    value-format="yyyy-MM-dd"
                    v-model="dateRange"></el-date-picker>
                </el-form-item>
            </el-form>

            <el-table :data="summary" show-summary :summary-method="getSummaryItem" stripe border>
                <el-table-column type="index"></el-table-column>
                <el-table-column label="Customer" prop="customer" show-overflow-tooltip></el-table-column>
                <el-table-column label="Registered" prop="registered" header-align="center" align="center"></el-table-column>
                <el-table-column label="Ready for Delivery" prop="ready_for_delivery" header-align="center" align="center"></el-table-column>
                <el-table-column label="On Delivery" prop="on_delivery" header-align="center" align="center"></el-table-column>
                <el-table-column label="Delivered" prop="delivered" header-align="center" align="center"></el-table-column>
                <el-table-column label="Total" prop="total" header-align="center" align="center"></el-table-column>
            </el-table>
        </el-card>
    </div>
</template>

<script>
export default {
    data() {
        return {
            summary: [],
            dateRange: null
        }
    },
    methods: {
        requestData() {
            axios('/report/summary').then(r => {
                this.summary = r.data
            }).catch(e => console.log(e))
        },
        getSummaryItem(param) {
            const { columns, data } = param;
            const sums = []

            columns.forEach((column, index) => {
                if (index == 1) {
                    sums[index] = 'TOTAL'
                }

                if (index == 2) {
                    sums[index] = this.summary.reduce((prev, curr) => prev + Number(curr.registered), 0)
                }

                if (index == 3) {
                    sums[index] = this.summary.reduce((prev, curr) => prev + Number(curr.ready_for_delivery), 0)
                }

                if (index == 4) {
                    sums[index] = this.summary.reduce((prev, curr) => prev + Number(curr.on_delivery), 0)
                }

                if (index == 5) {
                    sums[index] = this.summary.reduce((prev, curr) => prev + Number(curr.delivered), 0)
                }

                if (index == 6) {
                    sums[index] = this.summary.reduce((prev, curr) => prev + Number(curr.total), 0)
                }
            })

            return sums
        },
    },
    mounted() {
        this.requestData()
    }
}
</script>

<style lang="scss" scoped>
.summary-container {
    height: 150px;
    text-align: center;
}

.summary-info {
    font-size: 30px;
}
</style>
