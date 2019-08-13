<template>
    <div>
        <el-page-header @back="$emit('back')" content="DOMESTIC DELIVERIES"> </el-page-header>
        <el-divider></el-divider>
        <el-form :inline="true" style="text-align:right" @submit.native.prevent="() => { return }">
            <el-form-item>
                <el-button icon="el-icon-plus" @click="openForm({role: 0, password: ''})" type="primary">ADD NEW DOMESTIC DELIVERY</el-button>
            </el-form-item>
            <el-form-item style="margin-right:0;">
                <el-input v-model="keyword" placeholder="Search" prefix-icon="el-icon-search" :clearable="true" @change="(v) => { keyword = v; requestData(); }">
                    <el-button @click="() => { page = 1; keyword = ''; requestData(); }" slot="append" icon="el-icon-refresh"></el-button>
                </el-input>
            </el-form-item>
        </el-form>

        <el-table :data="tableData.data" stripe
        :default-sort = "{prop: sort, order: order}"
        height="calc(100vh - 290px)"
        v-loading="loading"
        @sort-change="sortChange">
            <el-table-column fixed="left" prop="customer" label="Customer" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="origin" label="Origin" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="destination" label="Destination" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="delivery_address" label="Delivery Address" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="charge_to" label="Charge To" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="resi_number" label="Receipt Number" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="spb_number" label="SPB Number" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="tracking_number" label="Tracking Number" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="delivery_type" label="Delivery Type" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="service_type" label="Delivery Service" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="volume" label="Volume (Kg)" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="quantity" label="Quantity" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="dimension" label="Dimension" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="pick_up_date" label="P/U Date" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="etd" label="ETD" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="delivery_date" label="Delivery Date" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="eta" label="ETA" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="delivered_date" label="Arrival Date" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="receiver" label="Receiver" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="agent" label="Agent" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="ship_name" label="Ship Name" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="vehicle_number" label="Vehicle Number" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="driver_name" label="Driver Name" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="driver_phone" label="Driver Phone" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column prop="user" label="Created By" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column fixed="right" prop="updated_at" label="Last Update" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column fixed="right" prop="status" label="Status" sortable="custom" min-width="150px" show-overflow-tooltip></el-table-column>
            <el-table-column fixed="right" width="40px">
                <template slot-scope="scope">
                    <el-dropdown>
                        <span class="el-dropdown-link">
                            <i class="el-icon-more"></i>
                        </span>
                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item @click.native.prevent="openForm(scope.row)"><i class="el-icon-edit-outline"></i> Edit</el-dropdown-item>
                            <el-dropdown-item @click.native.prevent="deleteData(scope.row.id)"><i class="el-icon-delete"></i> Delete</el-dropdown-item>
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

        <el-dialog
        fullscreen
        :visible.sync="showForm"
        :title="!!formModel.id ? 'EDIT DOMESTIC DELIVERY' : 'ADD NEW DOMESTIC DELIVERY'"
        width="500px"
        v-loading="loading"
        :close-on-click-modal="false">

            <el-alert type="error" title="ERROR"
                :description="error.message + '\n' + error.file + ':' + error.line"
                v-show="error.message"
                style="margin-bottom:15px;">
            </el-alert>

            <el-form label-width="150px">

                <el-row :gutter="15">
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

                        <el-form-item label="Origin" :class="formErrors.origin ? 'is-error' : ''">
                            <el-select v-model="formModel.origin" placeholder="Origin" filterable default-first-option style="width:100%">
                                <el-option v-for="(t, i) in $store.state.cityList"
                                :value="t.id"
                                :label="t.name"
                                :key="i">
                                </el-option>
                            </el-select>
                            <div class="el-form-item__error" v-if="formErrors.origin">{{formErrors.origin[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Destination" :class="formErrors.destination ? 'is-error' : ''">
                            <el-select v-model="formModel.destination" placeholder="Destination" filterable default-first-option style="width:100%">
                                <el-option v-for="(t, i) in $store.state.cityList"
                                :value="t.id"
                                :label="t.name"
                                :key="i">
                                </el-option>
                            </el-select>
                            <div class="el-form-item__error" v-if="formErrors.destination">{{formErrors.destination[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Delivery Address" :class="formErrors.delivery_address ? 'is-error' : ''">
                            <el-input type="textarea" rows="5" placeholder="Delivery Address" v-model="formModel.delivery_address"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.delivery_address">{{formErrors.delivery_address[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Charge To" :class="formErrors.charge_to ? 'is-error' : ''">
                            <el-input placeholder="Charge To" v-model="formModel.charge_to"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.charge_to">{{formErrors.charge_to[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Receipt Number" :class="formErrors.resi_number ? 'is-error' : ''">
                            <el-input placeholder="Receipt Number" v-model="formModel.resi_number"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.resi_number">{{formErrors.resi_number[0]}}</div>
                        </el-form-item>

                        <el-form-item label="SPB Number" :class="formErrors.spb_number ? 'is-error' : ''">
                            <el-input placeholder="SPB Number" v-model="formModel.spb_number"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.spb_number">{{formErrors.spb_number[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Tracking Number" :class="formErrors.tracking_number ? 'is-error' : ''">
                            <el-input placeholder="Tracking Number" v-model="formModel.tracking_number"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.tracking_number">{{formErrors.tracking_number[0]}}</div>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
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

                        <el-form-item label="Delivery Service" :class="formErrors.dservice_type_id ? 'is-error' : ''">
                            <el-select v-model="formModel.dservice_type_id" placeholder="Delivery Service" filterable default-first-option style="width:100%">
                                <el-option v-for="(t, i) in $store.state.serviceTypeList"
                                :value="t.id"
                                :label="t.code + ' - ' + t.name"
                                :key="i">
                                </el-option>
                            </el-select>
                            <div class="el-form-item__error" v-if="formErrors.dservice_type_id">{{formErrors.dservice_type_id[0]}}</div>
                        </el-form-item>
                        <el-form-item label="Volume (Kg)" :class="formErrors.volume ? 'is-error' : ''">
                            <el-input type="number" placeholder="Volume (Kg)" v-model="formModel.volume"></el-input>
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

                        <el-form-item label="ETD" :class="formErrors.etd ? 'is-error' : ''">
                            <el-date-picker
                            style="width:100%"
                            type="date"
                            format="dd-MMM-yyyy"
                            value-format="yyyy-MM-dd"
                            placeholder="ETD"
                            v-model="formModel.etd">
                            </el-date-picker>
                            <div class="el-form-item__error" v-if="formErrors.etd">{{formErrors.etd[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Delivery Date" :class="formErrors.delivery_date ? 'is-error' : ''">
                            <el-date-picker
                            style="width:100%"
                            type="date"
                            format="dd-MMM-yyyy"
                            value-format="yyyy-MM-dd"
                            placeholder="Delivery Date"
                            v-model="formModel.delivery_date">
                            </el-date-picker>
                            <div class="el-form-item__error" v-if="formErrors.delivery_date">{{formErrors.delivery_date[0]}}</div>
                        </el-form-item>

                        <el-form-item label="ETA" :class="formErrors.eta ? 'is-error' : ''">
                            <el-date-picker
                            style="width:100%"
                            type="date"
                            format="dd-MMM-yyyy"
                            value-format="yyyy-MM-dd"
                            placeholder="ETA"
                            v-model="formModel.eta">
                            </el-date-picker>
                            <div class="el-form-item__error" v-if="formErrors.eta">{{formErrors.eta[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Arrival Date" :class="formErrors.delivered_date ? 'is-error' : ''">
                            <el-date-picker
                            style="width:100%"
                            type="date"
                            format="dd-MMM-yyyy"
                            value-format="yyyy-MM-dd"
                            placeholder="Arrival Date"
                            v-model="formModel.delivered_date">
                            </el-date-picker>
                            <div class="el-form-item__error" v-if="formErrors.delivered_date">{{formErrors.delivered_date[0]}}</div>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="Agent" :class="formErrors.agent_id ? 'is-error' : ''">
                            <el-select v-model="formModel.agent_id" placeholder="Agent" filterable default-first-option style="width:100%">
                                <el-option v-for="(t, i) in $store.state.agentList"
                                :value="t.id"
                                :label="t.code + ' - ' + t.name"
                                :key="i">
                                </el-option>
                            </el-select>
                            <div class="el-form-item__error" v-if="formErrors.agent_id">{{formErrors.agent_id[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Ship Name" :class="formErrors.ship_name ? 'is-error' : ''">
                            <el-input placeholder="Ship Name" v-model="formModel.ship_name"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.ship_name">{{formErrors.ship_name[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Vehicle Number" :class="formErrors.vehicle_number ? 'is-error' : ''">
                            <el-input placeholder="Vehicle Number" v-model="formModel.vehicle_number"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.vehicle_number">{{formErrors.vehicle_number[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Driver Name" :class="formErrors.driver_name ? 'is-error' : ''">
                            <el-input placeholder="Driver Name" v-model="formModel.driver_name"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.driver_name">{{formErrors.driver_name[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Driver Phone" :class="formErrors.driver_phone ? 'is-error' : ''">
                            <el-input placeholder="Driver Phone" v-model="formModel.driver_phone"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.driver_phone">{{formErrors.driver_phone[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Status" :class="formErrors.delivery_status_id ? 'is-error' : ''">
                            <el-select v-model="formModel.delivery_status_id" placeholder="Status" filterable default-first-option style="width:100%">
                                <el-option v-for="(t, i) in $store.state.deliveryStatusList"
                                :value="t.id"
                                :label="t.code + ' - ' + t.name"
                                :key="i">
                                </el-option>
                            </el-select>
                            <div class="el-form-item__error" v-if="formErrors.delivery_status_id">{{formErrors.delivery_status_id[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Receiver" :class="formErrors.receiver ? 'is-error' : ''">
                            <el-input placeholder="Receiver" v-model="formModel.receiver"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.receiver">{{formErrors.receiver[0]}}</div>
                        </el-form-item>

                        <el-form-item label="Note" :class="formErrors.status_note ? 'is-error' : ''">
                            <el-input type="textarea" rows="3" placeholder="Note" v-model="formModel.status_note"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.status_note">{{formErrors.status_note[0]}}</div>
                        </el-form-item>
                    </el-col>
                </el-row>
            </el-form>

            <el-divider></el-divider>
            <el-button icon="el-icon-plus" type="primary">ADD ITEM</el-button>

            <el-table striped :data="items" style="margin-top:10px">
                <el-table type="index"></el-table>
                <el-table-column label="Description"></el-table-column>
                <el-table-column label="Coli"></el-table-column>
                <el-table-column label="Weight"></el-table-column>
                <el-table-column label="Item"></el-table-column>
                <el-table-column label="Reference"></el-table-column>
                <el-table-column label="Remark"></el-table-column>
            </el-table>

            <span slot="footer">
                <el-button type="primary" @click="() => !!formModel.id ? update() : store()"><i class="el-icon-success"></i> SAVE</el-button>
                <el-button type="info" @click="showForm = false"><i class="el-icon-error"></i> CANCEL</el-button>
            </span>
        </el-dialog>
    </div>
</template>

<script>
export default {
    data() {
        return {
            showForm: false,
            formErrors: {},
            error: {},
            formModel: {},
            keyword: '',
            page: 1,
            pageSize: 10,
            tableData: {},
            sort: 'name',
            order: 'ascending',
            loading: false,
            items: []
        }
    },
    methods: {
        sortChange(c) {
            if (c.prop != this.sort || c.order != this.order) {
                this.sort = c.prop; this.order = c.order; this.requestData()
            }
        },
        openForm(data) {
            this.error = {}
            this.formErrors = {}
            this.formModel = JSON.parse(JSON.stringify(data));
            this.showForm = true
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
            axios.get('/domesticDelivery', {params: params}).then(r => {
                    this.tableData = r.data
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
        }
    },
    mounted() {
        this.requestData();
        this.$store.commit('getAgentList');
        this.$store.commit('getCityList');
        this.$store.commit('getDeliveryTypeList');
        this.$store.commit('getDeliveryStatusList');
        this.$store.commit('getServiceTypeList');
        this.$store.commit('getCustomerList');
    }
}
</script>
