<template>
    <el-dialog
    center
    fullscreen
    title="INVOICE FORM"
    :visible.sync="visible"
    :show-close="false"
    :destroy-on-close="true"
    :close-on-click-modal="false"
    :close-on-press-escape="false">

        <el-form label-width="150px" label-position="left">
            <el-row :gutter="30">
                <el-col :span="12">
                    <el-form-item label="Customer">
                        <el-select v-model="formModel.customer_id" placeholder="Customer"  style="width:100%">
                            <el-option v-for="c in $store.state.customerList" :key="c.id" :value="c.id" :label="c.name"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="Date">
                        <el-date-picker v-model="formModel.date" placeholder="Date" style="width:100%"></el-date-picker>
                    </el-form-item>
                    <el-form-item label="Number">
                        <el-input placeholder="Number" v-model="formModel.number"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="Total (Rp)">
                        <el-input type="number" v-model="total" placeholder="Total"></el-input>
                    </el-form-item>
                    <el-form-item label="PPN (Rp)">
                        <el-input type="number" v-model="ppn" placeholder="PPN"></el-input>
                    </el-form-item>
                    <el-form-item label="Grand Total (Rp)">
                        <el-input type="number" v-model="grand_total" placeholder="Grand Total"></el-input>
                    </el-form-item>
                </el-col>
            </el-row>
        </el-form>

        <el-table :data="formModel.items">
            <el-table-column label="No" type="index" width="55px"></el-table-column>
            <el-table-column label="Tgl Kirim">
                <template slot-scope="scope">
                    {{scope.row.delivery_date | formattedDate}}
                </template>
            </el-table-column>
            <el-table-column label="Surat Pengantar" prop="spb_number"></el-table-column>
            <el-table-column label="Tujuan" prop="destination"></el-table-column>
            <el-table-column label="Koli">
                <template slot-scope="scope">
                    <el-input type="number" v-model="scope.row.coli" placeholder="Koli" size="small"></el-input>
                </template>
            </el-table-column>
            <el-table-column label="KG/M3">
                <template slot-scope="scope">
                    <el-input type="number" v-model="scope.row.kg_per_m3" placeholder="KG/M3" size="small"></el-input>
                </template>
            </el-table-column>
            <el-table-column label="(RP) KG/M3">
                <template slot-scope="scope">
                    <el-input type="number" v-model="scope.row.price_per_unit" placeholder="(RP) KG/M3" size="small"></el-input>
                </template>
            </el-table-column>
            <el-table-column label="Biaya Kirim" header-align="right" align="right">
                <template slot-scope="scope">
                    Rp {{(scope.row.kg_per_m3 * scope.row.price_per_unit) | formatNumber}}
                </template>
            </el-table-column>
            <el-table-column label="PPN" width="55px">
                <template slot-scope="scope">
                    <el-checkbox v-model="scope.row.ppn"></el-checkbox>
                </template>
            </el-table-column>
            <el-table-column label="PPN (RP)">
                <template slot-scope="scope">
                    Rp {{(scope.row.kg_per_m3 * scope.row.price_per_unit) * (10 * scope.row.ppn / 100) | formatNumber}}
                </template>
            </el-table-column>
            <el-table-column label="Biaya Kirim + PPN" header-align="right" align="right">
                <template slot-scope="scope">
                    Rp {{(scope.row.kg_per_m3 * scope.row.price_per_unit) + (scope.row.kg_per_m3 * scope.row.price_per_unit) * (10 * scope.row.ppn / 100) | formatNumber}}
                </template>
            </el-table-column>
        </el-table>

        <div slot="footer">
            <el-button type="primary" @click="submit" icon="el-icon-success">SAVE</el-button>
            <el-button type="info" @click="$emit('close')" icon="el-icon-error">CANCEL</el-button>
        </div>

    </el-dialog>
</template>

<script>
export default {
    props: ['visible', 'items'],
    computed: {
        formModel() {
            return {items: this.items}
        },
        total() {
            return this.formModel.items.reduce((t, c) => t + c.kg_per_m3 * c.price_per_unit, 0)
        },
        ppn() {
            return this.formModel.items.reduce((t, c) => t + (c.kg_per_m3 * c.price_per_unit * c.ppn * 10 / 100), 0)
        },
        grand_total() {
            return this.total + this.ppn
        },
    },
    filters: {
        formattedDate(d) {
            return moment(d, 'YYYY-MM-DD').format('DD/MM/YYYY')
        }
    },
    methods: {
        submit() {
            axios.post('/invoice', this.formModel).then(r => {
                this.$message({
                    message: r.data.message,
                    type: 'success',
                    showClose: true
                })
                this.$emit('close');
            }).catch(e => {
                this.$message({
                    message: e.response.data.message,
                    type: 'error',
                    showClose: true
                })
            });
        }
    }
}
</script>

<style lang="scss" scoped>

</style>
