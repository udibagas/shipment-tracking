<template>
    <el-dialog fullscreen
    :before-close="(done) => { closeForm() }"
    :visible.sync="show"
    :title="!!formModel.id ? 'EDIT PENGIRIMAN DOMESTIK' : 'PENGIRIMAN DOMESTIK BARU'"
    v-loading="loading">

        <el-alert type="error" title="ERROR"
            :description="error.message"
            v-show="error.message"
            style="margin-bottom:15px;">
        </el-alert>

        <el-form label-width="150px" label-position="left">

            <el-row :gutter="30">
                <el-col :span="8">

                    <el-form-item label="Customer" :class="formErrors.customer_id ? 'is-error' : ''">
                        <el-select
                        @change="() => { getFare(); getFarePacking(); }"
                        v-model="formModel.customer_id"
                        placeholder="Customer"
                        filterable
                        default-first-option
                        style="width:100%">
                            <el-option v-for="(t, i) in $store.state.customerList"
                            :value="t.id"
                            :label="t.code + ' - ' + t.name"
                            :key="i">
                            </el-option>
                        </el-select>
                        <div class="el-form-item__error" v-if="formErrors.customer_id">{{formErrors.customer_id[0]}}</div>
                    </el-form-item>

                    <el-form-item label="Asal" :class="formErrors.origin ? 'is-error' : ''">
                        <el-select v-model="formModel.origin" placeholder="Asal" filterable default-first-option style="width:100%">
                            <el-option v-for="(t, i) in $store.state.cityList"
                            :value="t.name"
                            :label="t.name"
                            :key="i">
                            </el-option>
                        </el-select>
                        <div class="el-form-item__error" v-if="formErrors.origin">{{formErrors.origin[0]}}</div>
                    </el-form-item>

                    <el-form-item label="Tujuan" :class="formErrors.destination ? 'is-error' : ''">
                        <el-select @change="getFare" v-model="formModel.destination" placeholder="Tujuan" filterable default-first-option style="width:100%">
                            <el-option v-for="(t, i) in $store.state.cityList"
                            :value="t.name"
                            :label="t.name"
                            :key="i">
                            </el-option>
                        </el-select>
                        <div class="el-form-item__error" v-if="formErrors.destination">{{formErrors.destination[0]}}</div>
                    </el-form-item>

                    <el-form-item label="Alamat Pengiriman" :class="formErrors.delivery_address ? 'is-error' : ''">
                        <el-input type="textarea" rows="5" placeholder="Alamat Pengiriman" v-model="formModel.delivery_address"></el-input>
                        <div class="el-form-item__error" v-if="formErrors.delivery_address">{{formErrors.delivery_address[0]}}</div>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="Tanggal Pick Up" :class="formErrors.pick_up_date ? 'is-error' : ''">
                        <el-date-picker
                        style="width:100%"
                        type="date"
                        format="dd-MMM-yyyy"
                        value-format="yyyy-MM-dd"
                        placeholder="Tanggal Pick Up"
                        v-model="formModel.pick_up_date">
                        </el-date-picker>
                        <div class="el-form-item__error" v-if="formErrors.pick_up_date">{{formErrors.pick_up_date[0]}}</div>
                    </el-form-item>

                    <el-form-item label="Nomor Resi" :class="formErrors.resi_number ? 'is-error' : ''">
                        <el-input placeholder="Nomor Resi" v-model="formModel.resi_number"></el-input>
                        <div class="el-form-item__error" v-if="formErrors.resi_number">{{formErrors.resi_number[0]}}</div>
                    </el-form-item>

                    <el-form-item label="Nomor SPB" :class="formErrors.spb_number ? 'is-error' : ''">
                        <el-input placeholder="Nomor SPB" v-model="formModel.spb_number"></el-input>
                        <div class="el-form-item__error" v-if="formErrors.spb_number">{{formErrors.spb_number[0]}}</div>
                    </el-form-item>
                </el-col>
                <el-col :span="8">
                    <el-form-item label="Jenis Pengiriman" :class="formErrors.delivery_type_id ? 'is-error' : ''">
                        <el-select v-model="formModel.delivery_type_id" placeholder="Jenis Pengiriman" filterable default-first-option style="width:100%">
                            <el-option v-for="(t, i) in $store.state.deliveryTypeList"
                            :value="t.id"
                            :label="t.name"
                            :key="i">
                            </el-option>
                        </el-select>
                        <div class="el-form-item__error" v-if="formErrors.delivery_type_id">{{formErrors.delivery_type_id[0]}}</div>
                    </el-form-item>

                    <el-form-item label="Layanan Pengiriman" :class="formErrors.service_type ? 'is-error' : ''">
                        <el-select @change="getFare" v-model="formModel.service_type" placeholder="Layanan Pengiriman" style="width:100%">
                            <el-option v-for="(l, i) in ['REGULER', 'CHARTER']" :value="l" :label="l" :key="i">
                            </el-option>
                        </el-select>
                        <div class="el-form-item__error" v-if="formErrors.service_type">{{formErrors.service_type[0]}}</div>
                    </el-form-item>

                    <el-form-item label="Jenis Armada" :class="formErrors.vehicle_type_id ? 'is-error' : ''">
                        <el-select @change="getFare" v-model="formModel.vehicle_type_id" placeholder="Jenis Armada" style="width:100%">
                            <el-option v-for="v in $store.state.vehicleTypeList" :key="v.id" :label="v.name" :value="v.id"></el-option>
                        </el-select>
                        <div class="el-form-item__error" v-if="formErrors.vehicle_type_id">{{formErrors.vehicle_type_id[0]}}</div>
                    </el-form-item>

                    <el-form-item label="Berat Minimum (KG)" :class="formErrors.minimum_weight ? 'is-error' : ''">
                        <el-input disabled placeholder="Berat Minimum (KG)" v-model="formModel.minimum_weight"></el-input>
                        <div class="el-form-item__error" v-if="formErrors.minimum_weight">{{formErrors.minimum_weight[0]}}</div>
                    </el-form-item>
                </el-col>
            </el-row>
        </el-form>

        <el-table striped :data="formModel.items" style="margin-top:10px" show-summary :summary-method="getSummaryItem">
            <el-table-column type="index" label="Koli"></el-table-column>
            <el-table-column label="Deskripsi">
                <template slot-scope="scope">
                    <el-input v-model="scope.row.description" size="small" placeholder="Deskripsi"></el-input>
                </template>
            </el-table-column>
            <el-table-column label="P (cm)" width="100" header-align="center">
                <template slot-scope="scope">
                    <el-input type="number" v-model="scope.row.dimension_p" size="small" placeholder="P"></el-input>
                </template>
            </el-table-column>
            <el-table-column label="L (cm)" width="100" header-align="center">
                <template slot-scope="scope">
                    <el-input type="number" v-model="scope.row.dimension_l" size="small" placeholder="L"></el-input>
                </template>
            </el-table-column>
            <el-table-column label="T (cm)" width="100" header-align="center">
                <template slot-scope="scope">
                    <el-input type="number" v-model="scope.row.dimension_t" size="small" placeholder="T"></el-input>
                </template>
            </el-table-column>
            <el-table-column label="Berat (KG)" width="100" header-align="right" align="right">
                <template slot-scope="scope">
                    <el-input type="number" v-model="scope.row.weight" size="small" placeholder="Berat (KG)"></el-input>
                </template>
            </el-table-column>
            <el-table-column label="Volume" width="120" header-align="right" align="right">
                <template slot-scope="scope">
                    {{(scope.row.dimension_p * scope.row.dimension_l * scope.row.dimension_t / 1000000).toFixed(3)}} M<sup>3</sup>
                </template>
            </el-table-column>
            <el-table-column label="Berat Volume" width="120" header-align="right" align="right">
                <template slot-scope="scope">
                    {{(scope.row.dimension_p * scope.row.dimension_l * scope.row.dimension_t / 4000).toFixed(0)}} KG
                </template>
            </el-table-column>
            <el-table-column label="Berat Invoice" width="120" header-align="right" align="right">
                <template slot-scope="scope">
                    {{scope.row.weight >= (scope.row.dimension_p * scope.row.dimension_l * scope.row.dimension_t / 4000) ? scope.row.weight : (scope.row.dimension_p * scope.row.dimension_l * scope.row.dimension_t / 4000).toFixed(0)}} KG
                </template>
            </el-table-column>
            <el-table-column label="Packing" header-align="center" align="center" min-width="70">
                <template slot-scope="scope">
                    <el-checkbox v-model="scope.row.packing"></el-checkbox>
                </template>
            </el-table-column>
            <el-table-column label="Keterangan">
                <template slot-scope="scope">
                    <el-input v-model="scope.row.remark" size="small" placeholder="Keterangan"></el-input>
                </template>
            </el-table-column>
            <el-table-column width="70px" align="right" header-align="right">
                <template slot="header">
                    <el-button @click="addItem" icon="el-icon-plus" type="primary" size="small"></el-button>
                </template>
                <template slot-scope="scope">
                    <el-button @click="deleteItem(scope.$index, scope.row.id)" icon="el-icon-delete" type="danger" size="small"></el-button>
                </template>
            </el-table-column>
        </el-table>

        <table style="width:100%;margin-top:20px" class="table" v-if="!!formModel.service_type">
            <thead>
                <tr>
                    <th>Jenis Biaya</th>
                    <th class="text-right" style="width:200px">Berat/Volume</th>
                    <th class="text-right" style="width:100px">Tarif</th>
                    <th class="text-right" style="width:100px">Biaya</th>
                    <th class="text-right" style="width:100px">PPN</th>
                    <th class="text-right" style="width:100px">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="td-label"> {{formModel.service_type}} - {{formModel.destination}} </td>
                    <td class="td-value text-right">{{totalInvoiceWeight | formatNumber}} KG</td>
                    <td class="td-value text-right">
                        Rp. {{ formModel.delivery_rate | formatNumber }}
                    </td>
                    <td class="td-value text-right">Rp. {{delivery_cost | formatNumber}}</td>
                    <td class="td-value text-right">
                        <el-checkbox disabled v-model="formModel.delivery_cost_ppn"></el-checkbox>
                    </td>
                    <td class="td-value text-right">Rp. {{parseInt(delivery_cost) + (formModel.delivery_cost_ppn * 0.1 * parseInt(delivery_cost)) | formatNumber}}</td>
                </tr>
                <tr>
                    <td class="td-label">PACKING PETI</td>
                    <td class="td-value text-right">{{totalVolumePacking | formatNumber}} M<sup>3</sup></td>
                    <td class="td-value text-right">
                        Rp. {{ formModel.packing_rate | formatNumber }}
                    </td>
                    <td class="td-value text-right">Rp. {{packing_cost | formatNumber}}</td>
                    <td class="td-value text-right">
                        <el-checkbox disabled v-model="formModel.packing_cost_ppn"></el-checkbox>
                    </td>
                    <td class="td-value text-right">Rp. {{parseInt(packing_cost) + (formModel.packing_cost_ppn * 0.1 * parseInt(packing_cost)) | formatNumber}}</td>
                </tr>
                <tr>
                    <td class="td-label">BIAYA PENERUS</td>
                    <td></td>
                    <td class="td-value text-right">
                        <el-input size="small" type="number"
                        v-model="formModel.forwarder_cost"
                        placeholder="Biaya Penerus"></el-input>
                    </td>
                    <td class="td-value text-right">Rp. {{formModel.forwarder_cost | formatNumber}}</td>
                    <td class="td-value text-right">
                        <el-checkbox v-model="formModel.forwarder_cost_ppn"></el-checkbox>
                    </td>
                    <td class="td-value text-right">
                        Rp. {{parseInt(formModel.forwarder_cost) + (formModel.forwarder_cost_ppn * 0.1 * parseInt(formModel.forwarder_cost)) | formatNumber}}
                    </td>
                </tr>
                <tr>
                    <td class="td-label">BIAYA LAIN-LAIN</td>
                    <td class="td-value text-right">
                        <el-input size="small" v-model="formModel.additional_cost_description"
                        placeholder="Keterangan"></el-input>
                    </td>
                    <td class="td-value text-right">
                        <el-input size="small" type="number"
                        v-model="formModel.additional_cost"
                        placeholder="Biaya Penerus"></el-input>
                    </td>
                    <td class="td-value text-right">Rp. {{formModel.additional_cost | formatNumber}}</td>
                    <td class="td-value text-right">
                        <el-checkbox v-model="formModel.additional_cost_ppn"></el-checkbox>
                    </td>
                    <td class="td-value text-right">
                        Rp. {{parseInt(formModel.additional_cost) + (formModel.additional_cost_ppn * 0.1 * parseInt(formModel.additional_cost)) | formatNumber}}
                    </td>
                </tr>
                <tr>
                    <td class="td-label">TOTAL</td>
                    <td class="td-value text-right big" colspan="5">Rp. {{total_cost | formatNumber}}</td>
                </tr>
            </tbody>
        </table>

        <span slot="footer">
            <el-button type="primary" @click="save" icon="el-icon-success">SIMPAN</el-button>
            <el-button type="info" @click="closeForm()" icon="el-icon-error">BATAL</el-button>
        </span>
    </el-dialog>
</template>

<script>
export default {
    props: ['delivery', 'show'],
    computed: {
        formModel() {
            return this.delivery
        },
        totalWeight() {
            return this.formModel.items.reduce((prev, curr) => {
                return prev + Number(curr.weight)
            }, 0);
        },
        totalVolume() {
            return this.formModel.items.reduce((prev, curr) => {
                return prev + (curr.dimension_p * curr.dimension_l * curr.dimension_t / 1000000)
            }, 0);
        },
        totalVolumePacking() {
            return this.formModel.items.reduce((prev, curr) => {
                let volume = !!curr.packing ? (curr.dimension_p * curr.dimension_l * curr.dimension_t / 1000000) : 0
                return prev + volume
            }, 0);
        },
        totalVolumeWeight() {
            return this.formModel.items.reduce((prev, curr) => {
                return prev + (curr.dimension_p * curr.dimension_l * curr.dimension_t / 4000)
            }, 0);
        },
        totalInvoiceWeight() {
            let total =  this.formModel.items.reduce((prev, curr) => {
                let invoiceWeight = curr.dimension_p * curr.dimension_l * curr.dimension_t / 4000
                return curr.weight > invoiceWeight ? prev + Number(curr.weight) : prev + Number(invoiceWeight)
            }, 0);

            // kalau dia reguler
            if (!!this.formModel.minimum_weight) {
                return total >= this.formModel.minimum_weight ? total : this.formModel.minimum_weight;
            }

            return total
        },
        totalQuantity() {
            return this.formModel.items.length
        },
        delivery_cost() {
            return this.formModel.service_type == 'CHARTER'
                ? this.formModel.delivery_rate
                : this.formModel.delivery_rate * this.totalInvoiceWeight
        },
        packing_cost() {
            return this.formModel.packing_rate * this.totalVolumePacking
        },
        total_cost() {
            return parseInt(this.formModel.delivery_cost)
                + parseInt(this.formModel.packing_cost)
                + parseInt(this.formModel.forwarder_cost)
                + parseInt(this.formModel.additional_cost)
                + (this.formModel.packing_cost_ppn * 0.1 * parseInt(this.formModel.packing_cost))
                + (this.formModel.delivery_cost_ppn * 0.1 * parseInt(this.formModel.delivery_cost))
                + (this.formModel.forwarder_cost_ppn * 0.1 * parseInt(this.formModel.forwarder_cost))
                + (this.formModel.additional_cost_ppn * 0.1 * parseInt(this.formModel.additional_cost))
        }
    },
    watch: {
        'formModel.customer_id'(v, o) {
            const customer = this.$store.state.customerList.find(c => c.id == v)
            if (customer) {
                this.formModel.customer_name = customer.name
                if (!this.formModel.id) {
                    this.getFare();
                    this.getFarePacking();
                }
            }
        }
    },
    data() {
        return {
            formErrors: {},
            error: {},
            loading: false
        }
    },
    methods: {
        closeForm() {
            this.error = {}
            this.formErrors = {}
            this.$emit('close')
        },
        addItem() {
            this.formModel.items.push({
                description: '',
                dimension_p: null,
                dimension_l: null,
                dimension_t: null,
                weight: null,
                volume: 0,
                volume_weight: 0,
                invoice_weight: 0,
                packing: false,
                remark: '',
            });
        },
        deleteItem(index, id) {
            if (!!id) {
                axios.delete('/domesticDeliveryItem/' + id).then(r => {
                    this.formModel.items.splice(index, 1);
                }).catch(e => {
                    this.$message({
                        message: 'Data gagal dihapus.',
                        type: 'error',
                        showClose: true
                    });
                })
            } else {
                this.formModel.items.splice(index, 1);
            }
        },
        getSummaryItem(param) {
            const { columns, data } = param;
            const sums = []
            columns.forEach((column, index) => {
                // berat
                if (index == 5) {
                    sums[index] = this.totalWeight + ' KG'
                }
                // Volume
                if (index == 6) {
                    sums[index] = this.totalVolume.toFixed(3) + ' M3'
                }
                // berat volume
                if (index == 7) {
                    sums[index] = this.totalVolumeWeight.toFixed(0) + ' KG'
                }
                // berat invoie
                if (index == 8) {
                    sums[index] = this.totalInvoiceWeight + ' KG'
                }
                // volume packing
                if (index == 9) {
                    sums[index] = this.totalVolumePacking.toFixed(3) + ' M3'
                }
            })

            return sums
        },
        save() {
            this.formModel.weight = this.totalWeight
            this.formModel.volume = this.totalVolume
            this.formModel.volume_weight = this.totalVolumeWeight
            this.formModel.invoice_weight = this.totalInvoiceWeight
            this.formModel.packing_volume = this.totalVolumePacking
            this.formModel.quantity = this.totalQuantity
            this.formModel.delivery_cost = this.delivery_cost
            this.formModel.packing_cost = this.packing_cost
            this.formModel.total_cost = this.total_cost
            this.formModel.packing = this.totalVolumePacking > 0 ? 1 : 0

            // cari data item yg ga lengkap
            let invalidItems = this.formModel.items.filter(i => {
                return !i.description || !i.dimension_p || !i.dimension_l || !i.dimension_t || !i.weight
            });

            if ((this.formModel.service_type == 'REGULER' && this.formModel.items.length == 0) || invalidItems.length > 0) {
                this.$message({
                    message: 'Mohon lengkapi data item.',
                    type: 'error',
                    showClose: true
                });
                return
            }

            // kalau tarifnya ga ada
            if (!this.formModel.delivery_rate) {
                this.$message({
                    message: 'Tarif untuk data terkait tidak ada. Silakan lengkapi data master tarif terlebih dahulu.',
                    type: 'error',
                    showClose: true
                });
                return
            }

            // kalau volume packing ada tapi tafir packing ga ada
            if (this.totalVolumePacking > 0 && !this.formModel.packing_rate) {
                this.$message({
                    message: 'Tarif packing peti untuk data terkait tidak ada. Silakan lengkapi data master tarif terlebih dahulu.',
                    type: 'error',
                    showClose: true
                });
                return
            }

            if (!!this.formModel.id) {
                this.update()
            } else {
                this.store()
            }
        },
        store() {
            this.loading = true;
            axios.post('/domesticDelivery', this.formModel).then(r => {
                this.$emit('close');
                this.$message({
                    message: 'Data berhasil disimpan.',
                    type: 'success',
                    showClose: true
                });
                this.$emit('refresh');
            }).catch(e => {
                if (e.response.status == 422) {
                    this.error = {}
                    this.formErrors = e.response.data.errors;
                }

                if (e.response.status == 500) {
                    this.formErrors = {}
                    this.error = e.response.data;
                }
            }).finally(() => {
                this.loading = false
            })
        },
        update() {
            this.loading = true;
            axios.put('/domesticDelivery/' + this.formModel.id, this.formModel).then(r => {
                this.$emit('close')
                this.$message({
                    message: 'Data berhasil disimpan.',
                    type: 'success',
                    showClose: true
                });
                this.$emit('refresh')
            }).catch(e => {
                if (e.response.status == 422) {
                    this.error = {}
                    this.formErrors = e.response.data.errors;
                }

                if (e.response.status == 500) {
                    this.formErrors = {}
                    this.error = e.response.data;
                }
            }).finally(() => {
                this.loading = false
            })
        },
        getFare() {
            if (!this.formModel.customer_id
                || !this.formModel.destination
                || !this.formModel.vehicle_type_id
                || !this.formModel.service_type) {
                return
            }

            let url = this.formModel.service_type == 'REGULER' ? '/masterFare/search' : '/masterFareCharter/search'

            let params = {
                customer_id: this.formModel.customer_id,
                destination: this.formModel.destination,
                company_id: this.$store.state.user.company_id,
                vehicle_type_id: this.formModel.vehicle_type_id
            }

            axios.get(url, { params: params }).then(r => {
                this.formModel.delivery_rate = r.data.fare
                this.formModel.delivery_cost_ppn = r.data.ppn
                if (this.formModel.service_type == 'REGULER') {
                    this.formModel.minimum_weight = r.data.minimum
                }
            }).catch(e => {
                this.formModel.delivery_rate = 0
                this.formModel.delivery_cost_ppn = false

                if (this.formModel.service_type == 'REGULER') {
                    this.formModel.minimum_weight = 0
                }

                this.$message({
                    message: e.response.data.message,
                    type: 'error',
                    showClose: true
                });
            })
        },
        getFarePacking() {
            if (!this.formModel.customer_id) {
                return
            }

            let params = {
                customer_id: this.formModel.customer_id,
                company_id: this.$store.state.user.company_id,
            }

            axios.get('/masterFarePacking/search', { params: params }).then(r => {
                this.formModel.packing_rate = r.data.fare
                this.formModel.packing_cost_ppn = r.data.ppn
            }).catch(e => {
                this.formModel.packing_rate = 0
                this.formModel.packing_cost_ppn = false
                this.$message({
                    message: e.response.data.message,
                    type: 'error',
                    showClose: true
                });
            })
        },
    }
}
</script>
