<template>
    <div>
        <el-form :inline="true" style="text-align:right" @submit.native.prevent="() => { return }">
            <el-form-item>
                <el-button icon="el-icon-plus" @click="openForm({ items: [] })" type="primary">New Domestic Delivery</el-button>
            </el-form-item>
            <el-form-item>
                <el-button :disabled="selection.length == 0" icon="el-icon-money" @click="showInvoiceForm = true" type="primary">Create Invoice</el-button>
            </el-form-item>
            <el-form-item>
                <el-button icon="el-icon-download" @click="exportToExcel" type="primary">Export</el-button>
            </el-form-item>
            <el-form-item style="margin-right:0;">
                <el-input v-model="keyword" placeholder="Search" prefix-icon="el-icon-search" :clearable="true" @change="(v) => { keyword = v; requestData(); }">
                    <el-button @click="() => { page = 1; keyword = ''; requestData(); }" slot="append" icon="el-icon-refresh"></el-button>
                </el-input>
            </el-form-item>
        </el-form>

        <el-table :data="tableData.data" stripe
        ref="deliveryList"
        @row-dblclick="(row, column, event) => { selectedData = row; showDetailDialog = true; }"
        :default-sort = "{prop: sort, order: order}"
        height="calc(100vh - 290px)"
        v-loading="loading"
        @selection-change="(val) => { selection = val }"
        @filter-change="(f) => { let c = Object.keys(f)[0]; filters[c] = Object.values(f[c]); page = 1; requestData(); }"
        @sort-change="sortChange">
            <el-table-column fixed="left" type="selection" width="55"> </el-table-column>

            <el-table-column
            prop="customer"
            label="Customer"
            sortable="custom"
            min-width="150px"
            :filters="$store.state.customerList.map(c => { return { value: c.id, text: c.name } })"
            column-key="customer_id"
            show-overflow-tooltip>
            </el-table-column>

            <el-table-column prop="charge_to" label="Charge To" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="origin" label="Origin" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="destination" label="Destination" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="delivery_address" label="Delivery Address" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="pick_up_date" label="P/U Date" sortable="custom" min-width="140px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="etd" label="ETD" sortable="custom" min-width="140px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="eta" label="ETA" sortable="custom" min-width="140px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="delivery_date" label="Delivery Date" sortable="custom" min-width="140px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="delivered_date" label="Delivered Date" sortable="custom" min-width="140px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="received_date" label="Received Date" sortable="custom" min-width="140px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="invoice_date" label="Invoice Date" sortable="custom" min-width="140px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="payment_date" label="Payment Date" sortable="custom" min-width="140px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="resi_number" label="Receipt Number" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="spb_number" label="SPB Number" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="tracking_number" label="Tracking Number" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="delivery_type" label="Delivery Type" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="service_type" label="Delivery Service" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="volume" label="Volume (Kg)" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="quantity" label="Quantity" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="dimension" label="Dimension" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>

            <el-table-column
            prop="agent"
            label="Agent"
            sortable="custom"
            min-width="150px"
            :filters="$store.state.agentList.map(c => { return { value: c.id, text: c.name } })"
            column-key="agent_id"
            show-overflow-tooltip>

            </el-table-column>

            <el-table-column prop="ship_name" label="Ship Name" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="vehicle_number" label="Vehicle Number" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="drive_name" label="Driver Name" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="drive_phone" label="Driver Phone" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="user" label="Created By" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="updated_at" label="Last Update" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="status_note" label="Note" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column fixed="right" column-key="status" label="Status" sortable="custom" min-width="150px" :filters="$store.state.deliveryStatusList.map(s => { return { value: s.id, text: s.name } })">
                <template slot-scope="scope">
                    <el-tag type="warning" size="small">{{scope.row.statusName}}</el-tag>
                </template>
            </el-table-column>
            <el-table-column fixed="right" width="40px">
                <template slot-scope="scope">
                    <el-dropdown>
                        <span class="el-dropdown-link">
                            <i class="el-icon-more"></i>
                        </span>
                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item @click.native.prevent="() => { selectedData = scope.row; showDetailDialog = true; }"><i class="el-icon-zoom-in"></i> Show Detail</el-dropdown-item>
                            <el-dropdown-item v-if="scope.row.delivery_status_id == 0" @click.native.prevent="printSpb(scope.row)"><i class="el-icon-printer"></i> Print SPB</el-dropdown-item>
                            <el-dropdown-item v-if="scope.row.delivery_status_id == 1" @click.native.prevent="printResi(scope.row)"><i class="el-icon-printer"></i> Print Receipt</el-dropdown-item>
                            <el-dropdown-item v-if="scope.row.delivery_status_id == 1" @click.native.prevent="printAwb(scope.row)"><i class="el-icon-printer"></i> Print Airway Bill</el-dropdown-item>
                            <el-dropdown-item v-if="scope.row.delivery_status_id == 4" @click.native.prevent="createInvoice(scope.row)"><i class="el-icon-money"></i> Create Invoice</el-dropdown-item>
                            <el-dropdown-item v-if="scope.row.delivery_status_id < 6" @click.native.prevent="openStatusForm(scope.row)"><i class="el-icon-warning-outline"></i> Update Status</el-dropdown-item>
                            <el-dropdown-item v-if="scope.row.delivery_status_id == 0" @click.native.prevent="openForm(scope.row)"><i class="el-icon-edit-outline"></i> Edit</el-dropdown-item>
                            <el-dropdown-item v-if="scope.row.delivery_status_id == 0" @click.native.prevent="deleteData(scope.row.id)"><i class="el-icon-delete"></i> Delete</el-dropdown-item>
                        </el-dropdown-menu>
                    </el-dropdown>
                </template>
            </el-table-column>
        </el-table>

        <br>

        <el-pagination background
        @current-change="(p) => { page = p; requestData(); }"
        @size-change="(s) => { pageSize = s; requestData(); }"
        layout="prev, pager, next, sizes, total"
        :page-size="pageSize"
        :page-sizes="[10, 25, 50, 100]"
        :total="tableData.total">
        </el-pagination>

        <el-dialog title="DETAIL" :visible.sync="showDetailDialog" fullscreen>
            <Detail v-if="!!selectedData" :data="selectedData" />
        </el-dialog>

        <el-dialog
        fullscreen
        :visible.sync="showForm"
        :title="!!formModel.id ? 'EDIT DOMESTIC DELIVERY' : 'NEW DOMESTIC DELIVERY'"
        v-loading="loading"
        :close-on-click-modal="false">

            <el-alert type="error" title="ERROR"
                :description="error.message"
                v-show="error.message"
                style="margin-bottom:15px;">
            </el-alert>

            <el-form label-width="150px" label-position="left">

                <el-row :gutter="25">
                    <el-col :span="8">
                        <el-form-item label="Customer" :class="formErrors.customer_id ? 'is-error' : ''">
                            <el-select v-model="formModel.customer_id" placeholder="Customer" filterable default-first-option style="width:100%">
                                <el-option v-for="(t, i) in $store.state.customerList"
                                :value="t.id"
                                :label="t.code + ' - ' + t.name"
                                :key="i">
                                </el-option>
                            </el-select>
                            <div class="el-form-item__error" v-if="formErrors.customer_id">{{formErrors.customer_id[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Charge To" :class="formErrors.charge_to ? 'is-error' : ''">
                            <el-input placeholder="Charge To" v-model="formModel.charge_to"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.charge_to">{{formErrors.charge_to[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Origin" :class="formErrors.origin ? 'is-error' : ''">
                            <el-select v-model="formModel.origin" placeholder="Origin" filterable default-first-option style="width:100%">
                                <el-option v-for="(t, i) in $store.state.cityList"
                                :value="t.name"
                                :label="t.name"
                                :key="i">
                                </el-option>
                            </el-select>
                            <div class="el-form-item__error" v-if="formErrors.origin">{{formErrors.origin[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Destination" :class="formErrors.destination ? 'is-error' : ''">
                            <el-select v-model="formModel.destination" placeholder="Destination" filterable default-first-option style="width:100%">
                                <el-option v-for="(t, i) in $store.state.cityList"
                                :value="t.name"
                                :label="t.name"
                                :key="i">
                                </el-option>
                            </el-select>
                            <div class="el-form-item__error" v-if="formErrors.destination">{{formErrors.destination[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Delivery Address" :class="formErrors.delivery_address ? 'is-error' : ''">
                            <el-input type="textarea" rows="4" placeholder="Delivery Address" v-model="formModel.delivery_address"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.delivery_address">{{formErrors.delivery_address[0]}}</div>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="P/U Date" :class="formErrors.pick_up_date ? 'is-error' : ''">
                            <el-date-picker
                            style="width:100%"
                            type="date"
                            format="dd-MMM-yyyy"
                            value-format="yyyy-MM-dd"
                            placeholder="P/U Date"
                            v-model="formModel.pick_up_date">
                            </el-date-picker>
                            <div class="el-form-item__error" v-if="formErrors.pick_up_date">{{formErrors.pick_up_date[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Receipt Number" :class="formErrors.resi_number ? 'is-error' : ''">
                            <el-input placeholder="Receipt Number" v-model="formModel.resi_number"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.resi_number">{{formErrors.resi_number[0]}}</div>
                        </el-form-item>

                        <el-form-item label="SPB Number" :class="formErrors.spb_number ? 'is-error' : ''">
                            <el-input placeholder="SPB Number" v-model="formModel.spb_number"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.spb_number">{{formErrors.spb_number[0]}}</div>
                        </el-form-item>
                        <el-form-item label="Delivery Type" :class="formErrors.delivery_type_id ? 'is-error' : ''">
                            <el-select v-model="formModel.delivery_type_id" placeholder="Delivery Type" filterable default-first-option style="width:100%">
                                <el-option v-for="(t, i) in $store.state.deliveryTypeList"
                                :value="t.id"
                                :label="t.code + ' - ' + t.name"
                                :key="i">
                                </el-option>
                            </el-select>
                            <div class="el-form-item__error" v-if="formErrors.delivery_type_id">{{formErrors.delivery_type_id[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Delivery Service" :class="formErrors.service_type_id ? 'is-error' : ''">
                            <el-select v-model="formModel.service_type_id" placeholder="Delivery Service" filterable default-first-option style="width:100%">
                                <el-option v-for="(t, i) in $store.state.serviceTypeList"
                                :value="t.id"
                                :label="t.code + ' - ' + t.name"
                                :key="i">
                                </el-option>
                            </el-select>
                            <div class="el-form-item__error" v-if="formErrors.service_type_id">{{formErrors.service_type_id[0]}}</div>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="Weight (kg)" :class="formErrors.weight ? 'is-error' : ''">
                            <el-input type="number" placeholder="Weight (kg)" v-model="formModel.weight"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.weight">{{formErrors.weight[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Volume (m3)" :class="formErrors.volume ? 'is-error' : ''">
                            <el-input type="number" placeholder="Volume (m3)" v-model="formModel.volume"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.volume">{{formErrors.volume[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Quantity" :class="formErrors.quantity ? 'is-error' : ''">
                            <el-input type="number" placeholder="Quantity" v-model="formModel.quantity"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.quantity">{{formErrors.quantity[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Dimension" :class="formErrors.dimension ? 'is-error' : ''">
                            <el-input placeholder="Dimension" v-model="formModel.dimension"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.dimension">{{formErrors.dimension[0]}}</div>
                        </el-form-item>
                    </el-col>
                </el-row>
            </el-form>

            <el-table striped :data="formModel.items" style="margin-top:10px">
                <el-table-column type="index"></el-table-column>
                <el-table-column label="Description">
                    <template slot-scope="scope">
                        <el-input v-model="scope.row.description" size="small" placeholder="Description"></el-input>
                    </template>
                </el-table-column>
                <el-table-column label="Coli">
                    <template slot-scope="scope">
                        <el-input type="number" v-model="scope.row.coli" size="small" placeholder="Coli"></el-input>
                    </template>
                </el-table-column>
                <el-table-column label="Weight (Kg)">
                    <template slot-scope="scope">
                        <el-input type="number" v-model="scope.row.weight" size="small" placeholder="Weight"></el-input>
                    </template>
                </el-table-column>
                <el-table-column label="Item">
                    <template slot-scope="scope">
                        <el-input type="number" v-model="scope.row.item" size="small" placeholder="Item"></el-input>
                    </template>
                </el-table-column>
                <el-table-column label="Reference">
                    <template slot-scope="scope">
                        <el-input v-model="scope.row.reference" size="small" placeholder="Reference"></el-input>
                    </template>
                </el-table-column>
                <el-table-column label="Remark">
                    <template slot-scope="scope">
                        <el-input v-model="scope.row.remark" size="small" placeholder="Remark"></el-input>
                    </template>
                </el-table-column>
                <el-table-column width="70px" align="right" header-align="right">
                    <template slot="header">
                        <el-button @click="addItem" icon="el-icon-plus" type="primary" plain size="small"></el-button>
                    </template>
                    <template slot-scope="scope">
                        <el-button @click="deleteItem(scope.$index, scope.row.id)" icon="el-icon-delete" type="danger" plain size="small"></el-button>
                    </template>
                </el-table-column>
            </el-table>

            <span slot="footer">
                <el-button type="primary" @click="() => !!formModel.id ? update() : store()"><i class="el-icon-success"></i> SAVE</el-button>
                <el-button type="info" @click="showForm = false"><i class="el-icon-error"></i> CANCEL</el-button>
            </span>
        </el-dialog>

        <UpdateForm @submitted="requestData" @close="showStatusForm = false" :data="selectedData" :visible.sync="showStatusForm" />
        <InvoiceForm @submitted="requestData" @close="showInvoiceForm = false" :items="selection" :visible.sync="showInvoiceForm" />

    </div>
</template>

<script>
import UpdateForm from './UpdateForm'
import InvoiceForm from './InvoiceForm'
import Detail from './Detail'

export default {
    components: { UpdateForm, Detail, InvoiceForm },
    watch: {
        timestamp(v, o) {
            this.requestData()
        }
    },
    data() {
        return {
            loading: false,
            tableData: {},
            page: 1,
            pageSize: 10,
            keyword: '',
            sort: 'updated_at',
            order: 'descending',
            formModel: { items: [] },
            formErrors: {},
            error: {},
            showForm: false,
            showStatusForm: false,
            selectedData: {},
            showDetailDialog: false,
            filters: {},
            selection: [],
            showInvoiceForm: false
        }
    },
    methods: {
        printResi(data) {
            window.open(BASE_URL + '/domesticDelivery/printResi/' + data.id + '?token=' + this.$store.state.token, '_blank')
        },
        printSpb(data) {
            window.open(BASE_URL + '/domesticDelivery/printSpb/' + data.id + '?token=' + this.$store.state.token, '_blank')
        },
        printAwb(data) {
            window.open(BASE_URL + '/domesticDelivery/printAwb/' + data.id + '?token=' + this.$store.state.token, '_blank')
        },
        exportToExcel() {

        },
        openStatusForm(data) {
            this.selectedData = JSON.parse(JSON.stringify(data));
            this.showStatusForm = true
        },
        sortChange(c) {
            if (c.prop != this.sort || c.order != this.order) {
                this.sort = c.prop; this.order = c.order; this.requestData()
            }
        },
        requestData() {
            let params = {
                page: this.page,
                keyword: this.keyword,
                pageSize: this.pageSize,
                sort: this.sort,
                order: this.order,
            }

            this.loading = true;
            axios.get('/domesticDelivery', { params: Object.assign(params, this.filters) }).then(r => {
                    this.tableData = r.data
                    this.$emit('loaded', r.data)
            }).catch(e => {
                if (e.response.status == 500) {
                    this.$message({
                        message: e.response.data.message + '\n' + e.response.data.file + ':' + e.response.data.line,
                        type: 'error',
                        showClose: true
                    });
                }
            }).finally(() => {
                this.loading = false
            })
        },
        openForm(data) {
            this.error = {}
            this.formErrors = {}
            this.formModel = JSON.parse(JSON.stringify(data));
            this.showForm = true
        },
        addItem() {
            this.formModel.items.push({
                description: '',
                coli: '',
                weight: '',
                item: '',
                reference: '',
                remark: ''
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
        store() {
            this.loading = true;
            axios.post('/domesticDelivery', this.formModel).then(r => {
                this.showForm = false;
                this.$message({
                    message: 'Data berhasil disimpan.',
                    type: 'success',
                    showClose: true
                });
                this.requestData();
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
                this.showForm = false
                this.$message({
                    message: 'Data berhasil disimpan.',
                    type: 'success',
                    showClose: true
                });
                this.requestData()
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
        deleteData(id) {
            this.$confirm('Anda yakin akan menghapus data ini?', 'Warning', { type: 'warning' }).then(() => {
                axios.delete('/domesticDelivery/' + id).then(r => {
                    this.requestData();
                    this.$message({
                        message: r.data.message,
                        type: 'success',
                        showClose: true
                    });
                }).catch(e => {
                    this.$message({
                        message: e.response.data.message,
                        type: 'error',
                        showClose: true
                    });
                })
            }).catch(() => console.log(e));
        }
    },
    mounted() {
        this.requestData();
        this.$store.commit('getCustomerList')
        this.$store.commit('getAgentList')
    }
}
</script>

<style lang="scss" scoped>

</style>
