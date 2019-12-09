<template>
    <div>
        <el-page-header @back="$emit('back')" content="INVOICE"> </el-page-header>
        <el-divider></el-divider>
        <el-form :inline="true" style="text-align:right" @submit.native.prevent="() => { return }">
            <el-form-item v-if="$store.state.user.role == 21 || $store.state.user.role == 31">
                <el-button icon="el-icon-plus" @click="openForm({items: []})" type="primary">INVOICE BARU</el-button>
            </el-form-item>
            <el-form-item>
                <el-date-picker
                @change="requestData"
                start-placeholder="Dari"
                end-placeholder="Sampai"
                type="daterange"
                format="dd-MMM-yyyy"
                value-format="yyyy-MM-dd"
                v-model="dateRange"></el-date-picker>
            </el-form-item>
            <el-form-item style="margin-right:0;">
                <el-input v-model="keyword" placeholder="Cari" prefix-icon="el-icon-search" :clearable="true" @change="(v) => { keyword = v; requestData(); }">
                    <el-button @click="() => { page = 1; keyword = ''; requestData(); }" slot="append" icon="el-icon-refresh"></el-button>
                </el-input>
            </el-form-item>
        </el-form>

        <el-table :data="tableData.data" stripe
        :default-sort = "{prop: sort, order: order}"
        height="calc(100vh - 290px)"
        v-loading="loading"
        @sort-change="sortChange">
            <el-table-column label="Status" sortable="custom" width="100px" align="center" header-align="center">
                <template slot-scope="scope">
                    <el-tag class="rounded full-width text-center" size="small" effect="dark" :type="scope.row.status ? 'success' : 'info'">{{scope.row.status ? 'FINAL' : 'DRAFT'}}</el-tag>
                </template>
            </el-table-column>
            <el-table-column prop="date" label="Tanggal" sortable="custom" width="120" header-align="center" align="center">
                <template slot-scope="scope">
                    {{scope.row.date | readableDate}}
                </template>
            </el-table-column>
            <el-table-column prop="number" label="Nomor Invoice" sortable="custom" min-width="150"></el-table-column>
            <el-table-column prop="faktur" label="Nomor Faktur" sortable="custom" min-width="150"></el-table-column>
            <el-table-column prop="customer" label="Customer" sortable="custom" min-width="120"></el-table-column>
            <el-table-column prop="service_type" label="Layanan" sortable="custom" min-width="100"></el-table-column>
            <el-table-column prop="total" label="Total" sortable="custom" align="right" header-align="right" min-width="120">
                <template slot-scope="scope">
                    <el-tag size="small" effect="dark" type="info">Rp {{scope.row.total | formatNumber}}</el-tag>
                </template>
            </el-table-column>
            <el-table-column label="Update Terakhir" prop="updated_at" min-width="150" sortable="custom" align="center" header-align="center">
                <template slot-scope="scope">
                    {{scope.row.updated_at | readableDateTime}}
                </template>
            </el-table-column>
            <el-table-column label="User" prop="user" sortable="custom" min-width="120"></el-table-column>
            <el-table-column fixed="right" width="40px">
                <template slot-scope="scope">
                    <el-dropdown>
                        <span class="el-dropdown-link">
                            <i class="el-icon-more"></i>
                        </span>
                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item @click.native.prevent="() => { showDetail = true; selectedData = scope.row; }"><i class="el-icon-zoom-in"></i> Lihat Detail</el-dropdown-item>
                            <el-dropdown-item v-if="!!scope.row.status" @click.native.prevent="print(scope.row.id)"><i class="el-icon-printer"></i> Print Invoice</el-dropdown-item>
                            <el-dropdown-item v-if="!scope.row.status" divided @click.native.prevent="openForm(scope.row)"><i class="el-icon-edit-outline"></i> Edit</el-dropdown-item>
                            <el-dropdown-item v-if="!scope.row.status" @click.native.prevent="deleteData(scope.row.id)"><i class="el-icon-delete"></i> Hapus</el-dropdown-item>
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

        <el-dialog :visible.sync="showForm" :title="!!formModel.id ? 'EDIT INVOICE' : 'INVOICE BARU'" fullscreen v-loading="loading" :close-on-click-modal="false">
            <el-alert type="error" title="ERROR"
                :description="error.message"
                v-show="error.message"
                style="margin-bottom:15px;">
            </el-alert>

            <el-form label-width="150px" label-position="left">
                <el-row :gutter="30">
                    <el-col :span="8">
                        <el-form-item label="Customer" :class="formErrors.customer_id ? 'is-error' : ''">
                            <el-select @change="getItems" v-model="formModel.customer_id" placeholder="Customer" style="width:100%">
                                <el-option v-for="c in $store.state.customerList" :key="c.id" :value="c.id" :label="c.name"></el-option>
                            </el-select>
                            <div class="el-form-item__error" v-if="formErrors.customer_id">{{formErrors.customer_id[0]}}</div>
                        </el-form-item>
                        <el-form-item label="Tanggal" :class="formErrors.date ? 'is-error' : ''">
                            <el-date-picker format="dd-MMM-yyyy" value-format="yyyy-MM-dd" v-model="formModel.date" placeholder="Tanggal" style="width:100%"></el-date-picker>
                            <div class="el-form-item__error" v-if="formErrors.date">{{formErrors.date[0]}}</div>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="Nomor Invoice" :class="formErrors.number ? 'is-error' : ''">
                            <el-input placeholder="Nomor Invoice" v-model="formModel.number"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.number">{{formErrors.number[0]}}</div>
                        </el-form-item>
                        <el-form-item label="Nomor Faktur" :class="formErrors.faktur ? 'is-error' : ''">
                            <el-input placeholder="Nomor Faktur" v-model="formModel.faktur"></el-input>
                            <div class="el-form-item__error" v-if="formErrors.faktur">{{formErrors.faktur[0]}}</div>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="Layanan Pengiriman" :class="formErrors.service_type ? 'is-error' : ''">
                            <el-select @change="getItems" v-model="formModel.service_type" placeholder="Layanan Pengiriman" style="width:100%">
                                <el-option v-for="(l, i) in ['REGULER', 'CHARTER']" :value="l" :label="l" :key="i">
                                </el-option>
                            </el-select>
                            <div class="el-form-item__error" v-if="formErrors.service_type">{{formErrors.service_type[0]}}</div>
                        </el-form-item>
                        <el-form-item label="Total" :class="formErrors.total ? 'is-error' : ''">
                            <div style="font-size:20px;height:40px;line-height:40px;border:1px solid #ddd;border-radius:4px;background-color:yellow;padding-left:20px;">
                                Rp {{total | formatNumber}}
                            </div>
                            <div class="el-form-item__error" v-if="formErrors.total">{{formErrors.total[0]}}</div>
                        </el-form-item>
                    </el-col>
                </el-row>

                <el-form-item label="Terbilang" :class="formErrors.total_said ? 'is-error' : ''">
                    <div style="height:40px;line-height:40px;border:1px solid #ddd;border-radius:4px;background-color:yellow;padding-left:20px;">
                        {{terbilang(total)}}
                    </div>
                    <div class="el-form-item__error" v-if="formErrors.total_said">{{formErrors.total_said[0]}}</div>
                </el-form-item>
            </el-form>

            <br>

            <el-table :data="formModel.items">
                <el-table-column label="#" type="index" width="30px"></el-table-column>
                <el-table-column label="Tgl Kirim" min-width="100" header-align="center" align="center">
                    <template slot-scope="scope">
                        {{scope.row.delivery_date | readableDate}}
                    </template>
                </el-table-column>
                <el-table-column label="Tgl Terima" min-width="100" header-align="center" align="center">
                    <template slot-scope="scope">
                        {{scope.row.delivered_date | readableDate}}
                    </template>
                </el-table-column>
                <el-table-column label="Surat Pengantar" prop="spb_number" min-width="110"></el-table-column>
                <el-table-column label="Asal" prop="origin" min-width="100"></el-table-column>
                <el-table-column label="Tujuan" prop="destination" min-width="100"></el-table-column>
                <el-table-column label="Layanan" prop="service_type" min-width="100">
                    <template slot-scope="scope">
                        {{scope.row.service_type == 'CHARTER' ? 'CHARTER ' + scope.row.vehicle_type : scope.row.service_type}}
                    </template>
                </el-table-column>
                <el-table-column v-if="formModel.service_type == 'REGULER'" label="Qty" prop="quantity" min-width="70" align="right" header-align="right">
                    <template slot-scope="scope">
                        {{scope.row.quantity | formatNumber}}
                    </template>
                </el-table-column>
                <el-table-column v-if="formModel.service_type == 'REGULER'" label="Unit" prop="unit" header-align="center" align="center" min-width="60"></el-table-column>
                <el-table-column label="Tarif" prop="fare" align="right" header-align="right" min-width="100">
                    <template slot-scope="scope">
                        Rp. {{scope.row.fare | formatNumber}}
                    </template>
                </el-table-column>
                <el-table-column label="Harga" prop="price" align="right" header-align="right" min-width="100">
                    <template slot-scope="scope">
                        Rp. {{scope.row.price | formatNumber}}
                    </template>
                </el-table-column>
                <el-table-column label="PPN" prop="tax" align="right" header-align="right" min-width="100">
                    <template slot-scope="scope">
                        Rp. {{scope.row.tax | formatNumber}}
                    </template>
                </el-table-column>
                <el-table-column label="Total" prop="total" align="right" header-align="right" min-width="100">
                    <template slot-scope="scope">
                        Rp. {{scope.row.total | formatNumber}}
                    </template>
                </el-table-column>
            </el-table>

            <span slot="footer" class="dialog-footer">
                <el-button type="primary" plain @click="save(0)" icon="el-icon-edit">DRAFT</el-button>
                <el-button type="primary" @click="save(1)" icon="el-icon-success">SIMPAN</el-button>
                <el-button type="info" @click="showForm = false" icon="el-icon-error">BATAL</el-button>
            </span>
        </el-dialog>

        <el-dialog :visible.sync="showDetail" :title="selectedData.number" fullscreen center>
            <iframe v-if="!!selectedData.id" :src="baseUrl + '/invoice/print/' + selectedData.id + '?token= ' + $store.state.token" frameborder="0" style="height:calc(100vh - 150px);width:100%;"></iframe>
        </el-dialog>
    </div>
</template>

<script>
export default {
    computed: {
        total() {
            return this.formModel.items.reduce((t, c) => t + c.price + c.tax , 0)
        }
    },
    data() {
        return {
            baseUrl: BASE_URL,
            showForm: false,
            formErrors: {},
            error: {},
            formModel: { items: []},
            keyword: '',
            page: 1,
            pageSize: 10,
            tableData: {},
            sort: 'date',
            order: 'ascending',
            loading: false,
            showDetail: false,
            selectedData: {},
            dateRange: [],
        }
    },
    methods: {
        sortChange(c) {
            if (c.prop != this.sort || c.order != this.order) {
                this.sort = c.prop; this.order = c.order; this.requestData()
            }
        },
        print(id) {
            window.open(BASE_URL + '/invoice/print/' + id + '?token=' + this.$store.state.token, '_blank')
        },
        terbilang(v) {
            let bilangan = 'nol,satu,dua,tiga,empat,lima,enam,tujuh,delapan,sembilan'.split(',')
            let level_bilangan = ',ribu,juta,milyar,triliun'.split(',')
            let input = Math.round(v) + ''
            let tmp = [];

            // pisahin angka 3 digit
            let level = 0
            while (true) {
                tmp.push({level: level, angka: input.slice(-3)})
                input = input.slice(0, -3)
                level++
                if (input == '') {
                    break
                }

            }

            tmp.reverse()
            let said = ''

            tmp.forEach(i => {
                if (i.angka.length < 3) {
                    i.angka = i.angka.length == 2 ? '0' + i.angka : '00' + i.angka
                }

                let array_angka = i.angka.split('').map(i => Number(i))

                if (array_angka[0] == '1') {
                    said += 'seratus '
                } else {
                    said += array_angka[0] > 0 ? bilangan[array_angka[0]] + ' ratus ' : ''
                }

                if (array_angka[1] == '1') {
                    if (array_angka[2] == '0') {
                        said += 'sepuluh '
                    } else if (array_angka[2] == '1') {
                        said += 'sebelas '
                    } else {
                        said += bilangan[array_angka[2]] + ' belas '
                    }
                } else {
                    said += array_angka[1] > 0 ? bilangan[array_angka[1]] + ' puluh ' : ''
                    said += array_angka[2] > 0 ? bilangan[array_angka[2]] + ' ' : ''
                }

                said += level_bilangan[i.level] + ' '
            })

            return said.replace('satu ribu', 'seribu').toUpperCase() + 'RUPIAH'
        },
        openForm(data) {
            this.error = {}
            this.formErrors = {}
            this.formModel = JSON.parse(JSON.stringify(data));

            if (!!this.formModel.id) {
                this.formModel.items.map(i => {
                    i.delivery_date = i.description.delivery_date
                    i.delivered_date = i.description.delivered_date
                    i.origin = i.description.origin
                    i.destination = i.description.destination
                    i.service_type = i.description.service_type
                    i.spb_number = i.description.spb_number
                    i.vehicle_type = i.description.vehicle_type
                    return i
                })
            }

            this.showForm = true
        },
        save(status) {
            if (this.formModel.items.length == 0) {
                this.$message({
                    message: 'Tidak ada pengiriman yang bisa ditagih untuk customer terkait',
                    type: 'error',
                    showClose: true
                });
                return
            }

            this.formModel.status = status
            this.formModel.total = this.total
            this.formModel.total_said = this.terbilang(this.total)

            if (!!status) {
                this.$confirm('Anda yakin?' ,'Konfirmasi', { type: 'warning' }).then(() => {
                    if (!!this.formModel.id) {
                        this.update()
                    } else {
                        this.store()
                    }
                }).catch(e => {
                    console.log(e)
                })
            } else {
                if (!!this.formModel.id) {
                    this.update()
                } else {
                    this.store()
                }
            }
        },
        store() {
            this.loading = true;
            axios.post('/invoice', this.formModel).then(r => {
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
            axios.put('/invoice/' + this.formModel.id, this.formModel).then(r => {
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
            this.$confirm('Anda yakin akan menghapus data ini?', 'Konfirmasi', { type: 'warning' }).then(() => {
                axios.delete('/invoice/' + id).then(r => {
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
                dateRange: this.dateRange
            }

            this.loading = true;
            axios.get('/invoice', {params: params}).then(r => {
                    this.tableData = r.data
            }).catch(e => {
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
        getItems() {
            if (!this.formModel.customer_id || !this.formModel.service_type) {
                return
            }

            let params = {
                delivery_status_id: 4, // stt_received
                invoice_status: 0,
                customer_id: this.formModel.customer_id,
                company_id: this.$store.state.user.company_id,
                service_type: this.formModel.service_type
            }

            this.formModel.items = []

            axios.get('/domesticDelivery/search', { params: params }).then(r => {
                if (r.data.length == 0) {
                    this.$message({
                        message: 'Tidak ada pengiriman yang bisa ditagih untuk customer terkait',
                        type: 'error',
                        showClose: true
                    });
                    return
                }

                r.data.forEach(data => {
                    // Biaya kirim
                    let d = JSON.parse(JSON.stringify(data))
                    d.description = {
                        delivery_id: d.id,
                        delivery_date: d.delivery_date,
                        delivered_date: d.delivered_date,
                        service_type: d.service_type,
                        origin: d.origin,
                        destination: d.destination,
                        vehicle_type: d.vehicle_type ? d.vehicle_type.name : '',
                        spb_number: d.spb_number,
                        total_coli: d.quantity
                    }

                    d.quantity = d.invoice_weight
                    d.unit = 'KG'
                    d.fare = d.delivery_rate
                    d.price = d.delivery_cost
                    d.tax = d.delivery_cost_ppn * 0.1 * d.price
                    d.total = d.price + d.tax
                    d.vehicle_type = d.vehicle_type ? d.vehicle_type.name : ''
                    this.formModel.items.push(d)
                    console.log(this.formModel.items)

                    // biaya packing
                    if (d.packing_cost > 0) {
                        let d = JSON.parse(JSON.stringify(data))
                        d.description = {
                            delivery_id: d.id,
                            delivery_date: d.delivery_date,
                            delivered_date: d.delivered_date,
                            service_type: 'PACKING PETI',
                            origin: d.origin,
                            destination: d.destination,
                            vehicle_type: d.vehicle_type ? d.vehicle_type.name : '',
                            spb_number: d.spb_number,
                            total_coli: d.quantity
                        }

                        d.service_type = 'PACKING PETI'
                        d.quantity = d.packing_volume
                        d.unit = 'M3'
                        d.fare = d.packing_rate
                        d.price = d.packing_cost
                        d.tax = d.packing_cost_ppn * 0.1 * d.price
                        d.total = d.price + d.tax
                        this.formModel.items.push(d)
                        console.log(this.formModel.items)
                    }

                    // biaya penerus
                    if (d.forwarder_cost > 0) {
                        let d = JSON.parse(JSON.stringify(data))
                        d.description = {
                            delivery_id: d.id,
                            delivery_date: d.delivery_date,
                            delivered_date: d.delivered_date,
                            service_type: 'BIAYA PENERUS',
                            origin: d.origin,
                            destination: d.destination,
                            vehicle_type: d.vehicle_type ? d.vehicle_type.name : '',
                            spb_number: d.spb_number,
                            total_coli: d.quantity
                        }

                        d.service_type = 'BIAYA PENERUS'
                        d.quantity = 1
                        d.unit = '-'
                        d.fare = 0
                        d.price = d.forwarder_cost
                        d.tax = d.forwarder_cost_ppn * 0.1 * d.price
                        d.total = d.price + d.tax
                        this.formModel.items.push(d)
                    }

                    // biaya lain - lain
                    if (d.additional_cost > 0) {
                        let d = JSON.parse(JSON.stringify(data))
                        d.description = {
                            delivery_id: d.id,
                            delivery_date: d.delivery_date,
                            delivered_date: d.delivered_date,
                            service_type: 'BIAYA LAIN - LAIN : ' + d.additional_cost_description,
                            origin: d.origin,
                            destination: d.destination,
                            vehicle_type: d.vehicle_type ? d.vehicle_type.name : '',
                            spb_number: d.spb_number,
                            total_coli: d.quantity
                        }

                        d.service_type = 'BIAYA LAIN - LAIN : ' + d.additional_cost_description
                        d.quantity = 1
                        d.unit = '-'
                        d.fare = 0
                        d.price = d.additional_cost
                        d.tax = d.additional_cost_ppn * 0.1 * d.price
                        d.total = d.price + d.tax
                        this.formModel.items.push(d)
                    }
                })

            }).catch(e => {
                this.$message({
                    message: e.response.data.message,
                    type: 'error',
                    showClose: true
                });
            })
        }
    },
    mounted() {
        this.requestData();
        this.$store.commit('getCustomerList')
    }
}
</script>
