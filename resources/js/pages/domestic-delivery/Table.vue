<template>
    <div>
        <el-form :inline="true" style="text-align:right" @submit.native.prevent="() => { return }">
            <el-form-item v-if="$store.state.user.role == 21 || $store.state.user.role == 31">
                <el-button type="primary" icon="el-icon-plus" @click="openForm({ items: [] })">PENGIRIMAN DOMESTIK BARU</el-button>
            </el-form-item>
            <el-form-item>
                <el-date-picker
                @change="requestData"
                type="daterange"
                format="dd-MMM-yyyy"
                value-format="yyyy-MM-dd"
                start-placeholder="Dari"
                end-placeholder="Sampai"
                v-model="dateRange"></el-date-picker>
            </el-form-item>
            <el-form-item>
                <el-input v-model="keyword" placeholder="Search" prefix-icon="el-icon-search" :clearable="true" @change="(v) => { keyword = v; requestData(); }">
                    <el-button @click="() => { page = 1; keyword = ''; requestData(); }" slot="append" icon="el-icon-refresh"></el-button>
                </el-input>
            </el-form-item>
            <el-form-item v-if="$store.state.user.role == 21 || $store.state.user.role == 31">
                <el-button-group>
                    <el-button type="primary" @click="exportToExcel" icon="el-icon-download" title="EXPORT KE EXCEL"></el-button>
                    <el-button type="primary" @click="showReportForm = true" icon="el-icon-message" title="KIRIM REPORT"></el-button>
                </el-button-group>
            </el-form-item>
        </el-form>

        <el-table :data="tableData.data" stripe
        ref="deliveryList"
        @row-dblclick="(row, column, event) => { selectedData = row; showDetailDialog = true; }"
        :default-sort = "{prop: sort, order: order}"
        height="calc(100vh - 290px)"
        v-loading="loading"
        @filter-change="(f) => { let c = Object.keys(f)[0]; filters[c] = Object.values(f[c]); page = 1; requestData(); }"
        @sort-change="sortChange">

            <el-table-column
            align="center"
            header-align="center"
            column-key="status"
            label="Status"
            sortable="custom"
            min-width="150px"
            :filters="$store.state.deliveryStatusList.map(s => { return { value: s.id, text: s.name } })">
                <template slot-scope="scope">
                    <el-tag effect="dark" size="small" class="rounded full-width text-center"
                    :type="$store.state.deliveryStatusList[scope.row.delivery_status_id].type">
                        {{scope.row.statusName.toUpperCase()}}
                    </el-tag>
                </template>
            </el-table-column>

            <el-table-column prop="resi_number" label="Nomor Resi" sortable="custom" min-width="150px"></el-table-column>
            <el-table-column prop="spb_number" label="Nomor SPB" sortable="custom" min-width="150px"></el-table-column>

            <el-table-column
            prop="customer"
            label="Customer"
            sortable="custom"
            min-width="150px"
            :filters="$store.state.customerList.map(c => { return { value: c.id, text: c.name } })"
            column-key="customer_id">
            </el-table-column>

            <el-table-column prop="origin" label="Asal" sortable="custom" min-width="150px"></el-table-column>
            <el-table-column prop="destination" label="Tujuan" sortable="custom" min-width="150px"></el-table-column>
            <el-table-column prop="delivery_address" label="Alamat Pengiriman" sortable="custom" min-width="170px"></el-table-column>
            <el-table-column prop="pick_up_date" label="Tanggal Pick Up" sortable="custom" min-width="150px" align="center" header-align="center">
                <template slot-scope="scope">
                    {{ scope.row.pick_up_date | readableDate }}
                </template>
            </el-table-column>
            <el-table-column prop="etd" label="ETD" sortable="custom" min-width="140px" header-align="center" align="center">
                <template slot-scope="scope">
                    {{ scope.row.etd | readableDate }}
                </template>
            </el-table-column>
            <el-table-column prop="eta" label="ETA" sortable="custom" min-width="140px" header-align="center" align="center">
                <template slot-scope="scope">
                    {{ scope.row.eta | readableDate }}
                </template>
            </el-table-column>
            <el-table-column prop="delivery_date" label="Tgl Dikirim" sortable="custom" min-width="140px" header-align="center" align="center">
                <template slot-scope="scope">
                    {{ scope.row.delivery_date | readableDate }}
                </template>
            </el-table-column>
            <el-table-column prop="delivered_date" label="Tgl Terima" sortable="custom" min-width="140px" header-align="center" align="center">
                <template slot-scope="scope">
                    {{ scope.row.delivered_date | readableDate }}
                </template>
            </el-table-column>
            <el-table-column prop="stt_received_date" label="Tgl Terima STT" sortable="custom" min-width="140px" header-align="center" align="center">
                <template slot-scope="scope">
                    {{ scope.row.stt_received_date | readableDate }}
                </template>
            </el-table-column>
            <el-table-column prop="receiver" label="Penerima" sortable="custom" min-width="100px"></el-table-column>
            <!-- <el-table-column prop="received_date" label="Tgl Terima" sortable="custom" min-width="140px" header-align="center" align="center"></el-table-column> -->
            <!-- <el-table-column prop="invoice_date" label="Invoice Date" sortable="custom" min-width="140px"></el-table-column> -->
            <!-- <el-table-column prop="payment_date" label="Payment Date" sortable="custom" min-width="140px"></el-table-column> -->
            <!-- <el-table-column prop="tracking_number" label="Nomor Tracking" sortable="custom" min-width="150px"></el-table-column> -->
            <el-table-column prop="delivery_type" label="Jenis Pengiriman" sortable="custom" min-width="150px"></el-table-column>
            <el-table-column prop="service_type" label="Layanan Pengiriman" sortable="custom" min-width="170px"></el-table-column>
            <el-table-column prop="vehicle_type.name" label="Jenis Armada" sortable="custom" min-width="170px"></el-table-column>
            <el-table-column prop="quantity" label="Jml Koli" sortable="custom" min-width="150px" header-align="center" align="center"></el-table-column>
            <el-table-column prop="volume" label="Volume" sortable="custom" min-width="100px" header-align="right" align="right">
                <template slot-scope="scope">
                    {{ scope.row.volume | formatNumber }} M<sup>3</sup>
                </template>
            </el-table-column>
            <el-table-column prop="packing_volume" label="Volume Packing" sortable="custom" min-width="140px" header-align="right" align="right">
                <template slot-scope="scope">
                    {{ scope.row.packing_volume | formatNumber }} M<sup>3</sup>
                </template>
            </el-table-column>
            <el-table-column prop="weight" label="Berat" sortable="custom" min-width="100px" header-align="right" align="right">
                <template slot-scope="scope">
                    {{ scope.row.weight | formatNumber }} KG
                </template>
            </el-table-column>
            <el-table-column prop="volume_weight" label="Berat Volume" sortable="custom" min-width="130px" header-align="right" align="right">
                <template slot-scope="scope">
                    {{ scope.row.volume_weight | formatNumber }} KG
                </template>
            </el-table-column>
            <el-table-column prop="invoice_weight" label="Berat Invoice" sortable="custom" min-width="120px" header-align="right" align="right">
                <template slot-scope="scope">
                    {{ scope.row.invoice_weight | formatNumber }} KG
                </template>
            </el-table-column>
            <el-table-column prop="delivery_cost" label="Biaya Pengiriman" sortable="custom" min-width="150px" header-align="right" align="right">
                <template slot-scope="scope">
                    Rp. {{ scope.row.delivery_cost | formatNumber }}
                </template>
            </el-table-column>
            <el-table-column prop="delivery_cost_ppn" label="PPN Biaya Pengiriman" sortable="custom" min-width="180px" header-align="right" align="right">
                <template slot-scope="scope">
                    Rp. {{ (scope.row.delivery_cost_ppn * 0.1 * scope.row.delivery_cost).toFixed(0) | formatNumber }}
                </template>
            </el-table-column>
            <el-table-column prop="packing_cost" label="Biaya Packing" sortable="custom" min-width="150px" header-align="right" align="right">
                <template slot-scope="scope">
                    Rp. {{ scope.row.packing_cost | formatNumber }}
                </template>
            </el-table-column>
            <el-table-column prop="packing_cost_ppn" label="PPN Biaya Packing" sortable="custom" min-width="170px" header-align="right" align="right">
                <template slot-scope="scope">
                    Rp. {{ (scope.row.packing_cost_ppn * 0.1 * scope.row.packing_cost).toFixed(0) | formatNumber }}
                </template>
            </el-table-column>
            <el-table-column prop="forwarder_cost" label="Biaya Penerus" sortable="custom" min-width="150px" header-align="right" align="right">
                <template slot-scope="scope">
                    Rp. {{ scope.row.forwarder_cost | formatNumber }}
                </template>
            </el-table-column>
            <el-table-column prop="forwarder_cost_ppn" label="PPN Biaya Penerus" sortable="custom" min-width="170px" header-align="right" align="right">
                <template slot-scope="scope">
                    Rp. {{ (scope.row.forwarder_cost_ppn * 0.1 * scope.row.forwarder_cost).toFixed(0) | formatNumber }}
                </template>
            </el-table-column>
            <el-table-column prop="additional_cost" label="Biaya Lain - Lain" sortable="custom" min-width="170px" header-align="right" align="right">
                <template slot-scope="scope">
                    Rp. {{ scope.row.additional_cost | formatNumber }}
                </template>
            </el-table-column>
            <el-table-column prop="additional_cost_ppn" label="PPN Biaya Lain - Lain" sortable="custom" min-width="170px" header-align="right" align="right">
                <template slot-scope="scope">
                    Rp. {{ (scope.row.additional_cost_ppn * 0.1 * scope.row.additional_cost).toFixed(0) | formatNumber }}
                </template>
            </el-table-column>
            <el-table-column prop="total_cost" label="Total Biaya" sortable="custom" min-width="150px" header-align="right" align="right">
                <template slot-scope="scope">
                    Rp. {{ scope.row.total_cost | formatNumber }}
                </template>
            </el-table-column>

            <el-table-column
            prop="agent"
            label="Agent"
            sortable="custom"
            min-width="150px"
            :filters="$store.state.agentList.map(c => { return { value: c.id, text: c.name } })"
            column-key="agent_id">
            </el-table-column>

            <el-table-column prop="ship_name" label="Nama Kapal" sortable="custom" min-width="150px"></el-table-column>
            <el-table-column prop="vehicle_number" label="No. Plat Armada" sortable="custom" min-width="150px"></el-table-column>
            <el-table-column prop="driver_name" label="Nama Driver" sortable="custom" min-width="150px"></el-table-column>
            <el-table-column prop="driver_phone" label="No. HP Driver" sortable="custom" min-width="150px"></el-table-column>
            <el-table-column prop="user" label="Diupdate Oleh" sortable="custom" min-width="150px"></el-table-column>
            <el-table-column prop="updated_at" label="Update Terkahir" sortable="custom" min-width="150px"></el-table-column>
            <el-table-column prop="status_note" label="Note" sortable="custom" min-width="150px"></el-table-column>
            <el-table-column fixed="right" width="40px">
                <template slot-scope="scope">
                    <el-dropdown>
                        <span class="el-dropdown-link">
                            <i class="el-icon-more"></i>
                        </span>
                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item icon="el-icon-zoom-in" @click.native.prevent="() => { selectedData = scope.row; showDetailDialog = true; }">Lihat Detail</el-dropdown-item>
                            <!-- <el-dropdown-item v-if="scope.row.delivery_status_id == 1" @click.native.prevent="printResi(scope.row)"><i class="el-icon-printer"></i> Print Receipt</el-dropdown-item> -->
                            <!-- <el-dropdown-item v-if="scope.row.delivery_status_id == 1" @click.native.prevent="printAwb(scope.row)"><i class="el-icon-printer"></i> Print Airway Bill</el-dropdown-item> -->
                            <el-dropdown-item icon="el-icon-warning-outline" v-if="scope.row.delivery_status_id < 6" @click.native.prevent="openStatusForm(scope.row)">Update Status</el-dropdown-item>
                            <el-dropdown-item icon="el-icon-edit-outline" divided @click.native.prevent="openForm(scope.row)">Edit</el-dropdown-item>
                            <el-dropdown-item icon="el-icon-delete" v-if="scope.row.delivery_status_id == 0" @click.native.prevent="deleteData(scope.row.id)">Hapus</el-dropdown-item>
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

        <el-dialog title="DETAIL PENGIRIMAN DOMESTIK" :visible.sync="showDetailDialog" fullscreen>
            <Detail v-if="!!selectedData" :data="selectedData" />
        </el-dialog>

        <el-dialog
        fullscreen
        :visible.sync="showForm"
        :title="!!formModel.id ? 'EDIT PENGIRIMAN DOMESTIK' : 'PENGIRIMAN DOMESTIK BARU'"
        v-loading="loading"
        :close-on-click-modal="false">

            <el-alert type="error" title="ERROR"
                :description="error.message"
                v-show="error.message"
                style="margin-bottom:15px;">
            </el-alert>

            <el-form label-width="150px" label-position="left">

                <el-row :gutter="30">
                    <el-col :span="8">

                        <el-form-item label="Customer" :class="formErrors.customer_id ? 'is-error' : ''">
                            <el-input v-model="formModel.customer_name" placeholder="Customer" disabled>
                                <el-popover placement="bottom" trigger="manual" v-model="showCustomerList" width="700" slot="append" ref="popover">
                                    <CustomerList :selection="true" @select="selectCustomer" />
                                    <el-button slot="reference" @click="showCustomerList = !showCustomerList" icon="el-icon-search"></el-button>
                                </el-popover>
                            </el-input>
                            <!-- <el-select
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
                            </el-select> -->
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
                            <el-input type="number" placeholder="Berat Minimum (KG)" v-model="formModel.minimum_weight"></el-input>
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
                            <el-input size="small" type="number" v-model="formModel.delivery_rate"></el-input>
                        </td>
                        <td class="td-value text-right">Rp. {{delivery_cost | formatNumber}}</td>
                        <td class="td-value text-right">
                            <el-checkbox v-model="formModel.delivery_cost_ppn"></el-checkbox>
                        </td>
                        <td class="td-value text-right">Rp. {{parseInt(delivery_cost) + (formModel.delivery_cost_ppn * 0.1 * parseInt(delivery_cost)) | formatNumber}}</td>
                    </tr>
                    <tr>
                        <td class="td-label">PACKING PETI</td>
                        <td class="td-value text-right">{{totalVolumePacking | formatNumber}} M<sup>3</sup></td>
                        <td class="td-value text-right">
                            <el-input size="small" type="number" v-model="formModel.packing_rate"></el-input>
                        </td>
                        <td class="td-value text-right">Rp. {{packing_cost | formatNumber}}</td>
                        <td class="td-value text-right">
                            <el-checkbox v-model="formModel.packing_cost_ppn"></el-checkbox>
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
                <el-button type="info" @click="showForm = false; showCustomerList = false" icon="el-icon-error">BATAL</el-button>
            </span>
        </el-dialog>

        <UpdateForm @submitted="requestData" @close="showStatusForm = false" :data="selectedData" :visible.sync="showStatusForm" />
        <ReportForm @submitted="requestData" @close="showReportForm = false" :visible.sync="showReportForm" />

    </div>
</template>

<script>
import UpdateForm from './UpdateForm'
import ReportForm from './ReportForm'
import Detail from './Detail'
import exportFromJSON from 'export-from-json'
import CustomerList from '../CustomerList'

export default {
    components: { UpdateForm, Detail, ReportForm, CustomerList },
    computed: {
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
            showReportForm: false,
            selectedData: {},
            showDetailDialog: false,
            filters: {},
            dateRange: '',
            showCustomerList: false
        }
    },
    methods: {
        selectCustomer(customer) {
            this.formModel.customer_name = customer.name;
            this.formModel.customer_id = customer.id;
            this.$forceUpdate()
            this.showCustomerList = false
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
        printResi(data) {
            window.open(BASE_URL + '/domesticDelivery/printResi/' + data.id + '?token=' + this.$store.state.token, '_blank')
        },
        printAwb(data) {
            window.open(BASE_URL + '/domesticDelivery/printAwb/' + data.id + '?token=' + this.$store.state.token, '_blank')
        },
        exportToExcel() {
            let params = {
                keyword: this.keyword,
                pageSize: 1000000, // hacky solution, but it works
                sort: this.sort,
                order: this.order,
                dateRange: this.dateRange
            }

            this.loading = true;
            axios.get('/domesticDelivery', { params: Object.assign(params, this.filters) }).then(r => {
                let data = r.data.data.map(d => {
                    return {
                        "Nomor SPB": d.spb_number,
                        "Nomor Resi": d.resi_number,
                        "Customer": d.customer,
                        "Asal": d.origin,
                        "Tujuan": d.destination,
                        "Alamat Pengiriman": d.delivery_address,
                        "Jenis Pengiriman": d.delivery_type,
                        "Layanan": d.service_type,
                        "Jenis Armada": d.vehicle_type ? d.vehicle_type.name : '',
                        "Agent": d.agent,
                        "Nama Kapal": d.ship_name,
                        "No. Plat Armada": d.vehicle_number,
                        "Driver Armada": d.driver_name,
                        "No. HP Driver": d.driver_phone,
                        "Tgl Pick Up": d.pick_up_date,
                        "ETD": d.etd,
                        "Tgl Kirim": d.delivery_date,
                        "ETA": d.eta,
                        "Tgl Terima": d.delivered_date,
                        "Penerima": d.receiver,
                        "Jml Koli": d.quantity,
                        "Berat": d.weight,
                        "Berat Volume": d.volume_weight,
                        "Berat Invoice": d.invoice_weight,
                        "Volume": d.volume,
                        "Volume Packing": d.packing_volume,
                        "Biaya Kirim": d.delivery_cost,
                        "PPN Biaya Kirim": d.delivery_cost_ppn * 0.1 * d.delivery_cost,
                        "Biaya Packing": d.packing_cost,
                        "PPN Biaya Packing": d.packing_cost_ppn * 0.1 * d.packing_cost,
                        "Biaya Penerus": d.packing_cost,
                        "PPN Biaya Penerus": d.packing_cost_ppn * 0.1 * d.packing_cost,
                        "Biaya Lain - Lain": d.packing_cost,
                        "PPN Biaya Lain - Lain": d.packing_cost_ppn * 0.1 * d.packing_cost,
                        "Total Biaya": d.total_cost,
                        "Status": d.statusName.toUpperCase(),
                        "Waktu Update": d.updated_at,
                    }
                })

                exportFromJSON({ data: data, fileName: 'domestic-delivery', exportType: 'xls' })
            }).catch(e => {
                console.log(e)
                if (e.response.status == 500) {
                    this.$message({
                        message: e.response.data.message,
                        type: 'error',
                        showClose: true
                    });
                }
            }).finally(() => {
                this.loading = false
            })
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
                dateRange: this.dateRange
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
    }
}
</script>

<style lang="scss" scoped>
.big {
    font-size: 20px;
}
</style>
