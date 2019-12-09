<template>
    <el-table striped :data="items" style="margin-top:10px" stripe show-summary :summary-method="getSummaryItem">
        <el-table-column type="index"></el-table-column>
        <el-table-column label="Deskripsi" prop="description" show-overflow-tooltip></el-table-column>
        <el-table-column label="P" align="center" header-align="center" width="100">
            <template slot-scope="scope">
                {{scope.row.dimension_p | formatNumber}} CM
            </template>
        </el-table-column>
        <el-table-column label="L" align="center" header-align="center" width="100">
            <template slot-scope="scope">
                {{scope.row.dimension_l | formatNumber}} CM
            </template>
        </el-table-column>
        <el-table-column label="T" align="center" header-align="center" width="100">
            <template slot-scope="scope">
                {{scope.row.dimension_t | formatNumber}} CM
            </template>
        </el-table-column>
        <el-table-column label="Berat" align="center" header-align="center" width="100">
            <template slot-scope="scope">
                {{scope.row.weight | formatNumber}} KG
            </template>
        </el-table-column>
        <el-table-column label="Volume" align="center" header-align="center" width="100">
            <template slot-scope="scope">
                    {{scope.row.volume | formatNumber}} M<sup>3</sup>
                </template>
        </el-table-column>
        <el-table-column label="Berat Volume" align="center" header-align="center" width="100">
            <template slot-scope="scope">
                {{scope.row.volume_weight | formatNumber}} KG
            </template>
        </el-table-column>
        <el-table-column label="Berat Invoice" align="center" header-align="center" width="100">
            <template slot-scope="scope">
                {{scope.row.invoice_weight | formatNumber}} KG
            </template>
        </el-table-column>
        <el-table-column label="Packing" header-align="center" align="center" width="100">
            <template slot-scope="scope">
                <el-checkbox disabled v-model="scope.row.packing"></el-checkbox>
            </template>
        </el-table-column>
        <el-table-column label="Keterangan" prop="remark" show-overflow-tooltip> </el-table-column>
    </el-table>
</template>

<script>
export default {
    props: ['data', 'items'],
    methods: {
        getSummaryItem(param) {
            const { columns, data } = param;
            const sums = []
            columns.forEach((column, index) => {
                // berat
                if (index == 5) {
                    sums[index] = this.data.weight + ' KG'
                }
                // Volume
                if (index == 6) {
                    sums[index] = this.data.volume + ' M3'
                }
                // berat volume
                if (index == 7) {
                    sums[index] = this.data.volume_weight + ' KG'
                }
                // berat invoie
                if (index == 8) {
                    sums[index] = this.data.invoice_weight + ' KG'
                }
                // volume packing
                if (index == 9) {
                    sums[index] = this.data.packing_volume + ' M3'
                }
            })

            return sums
        },
    }
}
</script>

<style lang="scss" scoped>

</style>
