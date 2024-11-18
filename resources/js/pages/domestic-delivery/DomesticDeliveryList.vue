<template>
	<div>
		<el-form
			inline
			class="text-right"
			@submit.native.prevent="
				() => {
					return;
				}
			"
		>
			<el-form-item
				class="margin-bottom-10"
				v-if="$store.state.auth.user.role == 21 || $store.state.auth.user.role == 31"
			>
				<el-button
					size="small"
					type="primary"
					icon="el-icon-plus"
					@click="
						openForm({
							items: [],
							forwarder_cost: 0,
							additional_cost: 0,
							forwarder_cost_ppn: 0,
							additional_cost_ppn: 0,
							delivery_cost_ppn: 0,
							delivery_status_id: null,
						})
					"
					>PENGIRIMAN DOMESTIK BARU</el-button
				>
			</el-form-item>
			<el-form-item class="margin-bottom-10">
				<el-date-picker
					size="small"
					@change="requestData"
					type="daterange"
					format="dd-MMM-yyyy"
					value-format="yyyy-MM-dd"
					start-placeholder="Dari"
					end-placeholder="Sampai"
					v-model="dateRange"
				></el-date-picker>
			</el-form-item>
			<el-form-item class="margin-bottom-10">
				<el-input
					size="small"
					v-model="keyword"
					placeholder="Cari"
					prefix-icon="el-icon-search"
					clearable
					@change="
						(v) => {
							keyword = v;
							requestData();
						}
					"
				></el-input>
			</el-form-item>
			<el-form-item
				class="margin-bottom-10"
				v-if="$store.state.auth.user.role == 21 || $store.state.auth.user.role == 31"
			>
				<el-button-group>
					<el-button
						size="small"
						type="primary"
						@click="selectField = true"
						icon="el-icon-download"
						title="EXPORT KE EXCEL"
					></el-button>
					<el-button
						size="small"
						type="primary"
						@click="showReportForm = true"
						icon="el-icon-message"
						title="KIRIM REPORT"
					></el-button>
				</el-button-group>
			</el-form-item>
		</el-form>

		<el-table
			:data="tableData.data"
			stripe
			ref="deliveryList"
			@row-dblclick="
				(row, column, event) => {
					selectedData = row;
					showDetailDialog = true;
				}
			"
			:default-sort="{ prop: sort, order: order }"
			height="calc(100vh - 245px)"
			v-loading="loading"
			@filter-change="
				(f) => {
					let c = Object.keys(f)[0];
					filters[c] = Object.values(f[c]);
					page = 1;
					requestData();
				}
			"
			@sort-change="sortChange"
		>
			<el-table-column
				align="center"
				header-align="center"
				column-key="status"
				label="Status"
				sortable="custom"
				min-width="150px"
				:filters="
					$store.state.deliveryStatusList.map((s) => {
						return { value: s.id, text: s.name };
					})
				"
			>
				<template slot-scope="scope">
					<el-tag
						effect="dark"
						size="small"
						class="full-width text-center"
						:type="
							$store.state.deliveryStatusList.find(
								(s) => s.id === scope.row.delivery_status_id
							).type
						"
						>{{ scope.row.statusName.toUpperCase() }}</el-tag
					>
				</template>
			</el-table-column>

			<el-table-column
				prop="resi_number"
				label="Nomor Resi"
				sortable="custom"
				min-width="150px"
			></el-table-column>
			<el-table-column
				prop="spb_number"
				label="Nomor SPB"
				sortable="custom"
				min-width="150px"
			></el-table-column>

			<el-table-column
				prop="customer"
				label="Customer"
				sortable="custom"
				min-width="150px"
				:filters="
					$store.state.customerList.map((c) => {
						return { value: c.id, text: c.name };
					})
				"
				column-key="customer_id"
			></el-table-column>

			<el-table-column
				prop="origin"
				label="Asal"
				sortable="custom"
				min-width="150px"
			></el-table-column>
			<el-table-column
				prop="destination"
				label="Tujuan"
				sortable="custom"
				min-width="150px"
			></el-table-column>
			<el-table-column
				prop="delivery_address"
				label="Alamat Pengiriman"
				sortable="custom"
				min-width="170px"
			></el-table-column>
			<el-table-column
				prop="pick_up_date"
				label="Tanggal Pick Up"
				sortable="custom"
				min-width="150px"
				align="center"
				header-align="center"
			>
				<template slot-scope="scope">{{
					scope.row.pick_up_date | readableDate
				}}</template>
			</el-table-column>
			<el-table-column
				prop="etd"
				label="ETD"
				sortable="custom"
				min-width="140px"
				header-align="center"
				align="center"
			>
				<template slot-scope="scope">{{
					scope.row.etd | readableDate
				}}</template>
			</el-table-column>
			<el-table-column
				prop="eta"
				label="ETA"
				sortable="custom"
				min-width="140px"
				header-align="center"
				align="center"
			>
				<template slot-scope="scope">{{
					scope.row.eta | readableDate
				}}</template>
			</el-table-column>
			<el-table-column
				prop="delivery_date"
				label="Tgl Dikirim"
				sortable="custom"
				min-width="140px"
				header-align="center"
				align="center"
			>
				<template slot-scope="scope">{{
					scope.row.delivery_date | readableDate
				}}</template>
			</el-table-column>
			<el-table-column
				prop="delivered_date"
				label="Tgl Terima"
				sortable="custom"
				min-width="140px"
				header-align="center"
				align="center"
			>
				<template slot-scope="scope">{{
					scope.row.delivered_date | readableDate
				}}</template>
			</el-table-column>
			<el-table-column
				prop="stt_received_date"
				label="Tgl Terima STT"
				sortable="custom"
				min-width="140px"
				header-align="center"
				align="center"
			>
				<template slot-scope="scope">{{
					scope.row.stt_received_date | readableDate
				}}</template>
			</el-table-column>
			<el-table-column
				prop="receiver"
				label="Penerima"
				sortable="custom"
				min-width="100px"
			></el-table-column>
			<!-- <el-table-column prop="received_date" label="Tgl Terima" sortable="custom" min-width="140px" header-align="center" align="center"></el-table-column> -->
			<!-- <el-table-column prop="invoice_date" label="Invoice Date" sortable="custom" min-width="140px"></el-table-column> -->
			<!-- <el-table-column prop="payment_date" label="Payment Date" sortable="custom" min-width="140px"></el-table-column> -->
			<!-- <el-table-column prop="tracking_number" label="Nomor Tracking" sortable="custom" min-width="150px"></el-table-column> -->
			<el-table-column
				prop="delivery_type"
				label="Jenis Pengiriman"
				sortable="custom"
				min-width="150px"
			></el-table-column>
			<el-table-column
				prop="service_type"
				label="Layanan Pengiriman"
				sortable="custom"
				min-width="170px"
			></el-table-column>
			<el-table-column
				prop="vehicle_type.name"
				label="Jenis Armada"
				sortable="custom"
				min-width="130px"
			></el-table-column>
			<el-table-column
				prop="quantity"
				label="Jml Koli"
				sortable="custom"
				min-width="100px"
				header-align="center"
				align="center"
			></el-table-column>
			<el-table-column
				prop="volume"
				label="Volume"
				sortable="custom"
				min-width="100px"
				header-align="right"
				align="right"
			>
				<template slot-scope="scope">
					{{ scope.row.volume | formatNumber }} M
					<sup>3</sup>
				</template>
			</el-table-column>
			<el-table-column
				prop="packing_volume"
				label="Volume Packing"
				sortable="custom"
				min-width="140px"
				header-align="right"
				align="right"
			>
				<template slot-scope="scope">
					{{ scope.row.packing_volume | formatNumber }} M
					<sup>3</sup>
				</template>
			</el-table-column>
			<el-table-column
				prop="weight"
				label="Berat"
				sortable="custom"
				min-width="100px"
				header-align="right"
				align="right"
			>
				<template slot-scope="scope"
					>{{ scope.row.weight | formatNumber }} KG</template
				>
			</el-table-column>
			<el-table-column
				prop="volume_weight"
				label="Berat Volume"
				sortable="custom"
				min-width="130px"
				header-align="right"
				align="right"
			>
				<template slot-scope="scope"
					>{{ scope.row.volume_weight | formatNumber }} KG</template
				>
			</el-table-column>
			<el-table-column
				prop="invoice_weight"
				label="Berat Invoice"
				sortable="custom"
				min-width="120px"
				header-align="right"
				align="right"
			>
				<template slot-scope="scope"
					>{{ scope.row.invoice_weight | formatNumber }} KG</template
				>
			</el-table-column>
			<el-table-column
				prop="delivery_cost"
				label="Biaya Pengiriman"
				sortable="custom"
				min-width="150px"
				header-align="right"
				align="right"
			>
				<template slot-scope="scope"
					>Rp. {{ scope.row.delivery_cost | formatNumber }}</template
				>
			</el-table-column>
			<el-table-column
				prop="delivery_cost_ppn"
				label="PPN Biaya Pengiriman"
				sortable="custom"
				min-width="180px"
				header-align="right"
				align="right"
			>
				<template slot-scope="scope"
					>Rp.
					{{
						(
							scope.row.delivery_cost_ppn *
							0.1 *
							scope.row.delivery_cost
						).toFixed(0) | formatNumber
					}}</template
				>
			</el-table-column>
			<el-table-column
				prop="packing_cost"
				label="Biaya Packing"
				sortable="custom"
				min-width="150px"
				header-align="right"
				align="right"
			>
				<template slot-scope="scope"
					>Rp. {{ scope.row.packing_cost | formatNumber }}</template
				>
			</el-table-column>
			<el-table-column
				prop="packing_cost_ppn"
				label="PPN Biaya Packing"
				sortable="custom"
				min-width="170px"
				header-align="right"
				align="right"
			>
				<template slot-scope="scope"
					>Rp.
					{{
						(scope.row.packing_cost_ppn * 0.1 * scope.row.packing_cost).toFixed(
							0
						) | formatNumber
					}}</template
				>
			</el-table-column>
			<el-table-column
				prop="forwarder_cost"
				label="Biaya Penerus"
				sortable="custom"
				min-width="150px"
				header-align="right"
				align="right"
			>
				<template slot-scope="scope"
					>Rp. {{ scope.row.forwarder_cost | formatNumber }}</template
				>
			</el-table-column>
			<el-table-column
				prop="forwarder_cost_ppn"
				label="PPN Biaya Penerus"
				sortable="custom"
				min-width="170px"
				header-align="right"
				align="right"
			>
				<template slot-scope="scope"
					>Rp.
					{{
						(
							scope.row.forwarder_cost_ppn *
							0.1 *
							scope.row.forwarder_cost
						).toFixed(0) | formatNumber
					}}</template
				>
			</el-table-column>
			<el-table-column
				prop="additional_cost"
				label="Biaya Lain - Lain"
				sortable="custom"
				min-width="170px"
				header-align="right"
				align="right"
			>
				<template slot-scope="scope"
					>Rp. {{ scope.row.additional_cost | formatNumber }}</template
				>
			</el-table-column>
			<el-table-column
				prop="additional_cost_ppn"
				label="PPN Biaya Lain - Lain"
				sortable="custom"
				min-width="170px"
				header-align="right"
				align="right"
			>
				<template slot-scope="scope"
					>Rp.
					{{
						(
							scope.row.additional_cost_ppn *
							0.1 *
							scope.row.additional_cost
						).toFixed(0) | formatNumber
					}}</template
				>
			</el-table-column>
			<el-table-column
				prop="total_cost"
				label="Total Biaya"
				sortable="custom"
				min-width="150px"
				header-align="right"
				align="right"
			>
				<template slot-scope="scope">
					<el-tag effect="dark" size="small" type="primary"
						>Rp. {{ scope.row.total_cost | formatNumber }}</el-tag
					>
				</template>
			</el-table-column>

			<el-table-column
				prop="agent"
				label="Agent"
				sortable="custom"
				min-width="150px"
				:filters="
					$store.state.agentList.map((c) => {
						return { value: c.id, text: c.name };
					})
				"
				column-key="agent_id"
			></el-table-column>

			<el-table-column
				prop="ship_name"
				label="Nama Kapal"
				sortable="custom"
				min-width="150px"
			></el-table-column>
			<el-table-column
				prop="vehicle_number"
				label="No. Plat Armada"
				sortable="custom"
				min-width="150px"
			></el-table-column>
			<el-table-column
				prop="driver_name"
				label="Nama Driver"
				sortable="custom"
				min-width="150px"
			></el-table-column>
			<el-table-column
				prop="driver_phone"
				label="No. HP Driver"
				sortable="custom"
				min-width="150px"
			></el-table-column>
			<el-table-column
				prop="user"
				label="Diupdate Oleh"
				sortable="custom"
				min-width="150px"
			></el-table-column>
			<el-table-column
				prop="updated_at"
				label="Update Terkahir"
				sortable="custom"
				min-width="150px"
			></el-table-column>
			<el-table-column
				prop="status_note"
				label="Note"
				sortable="custom"
				min-width="150px"
			></el-table-column>
			<el-table-column fixed="right" width="40px">
				<template slot="header">
					<el-button
						class="text-white"
						@click="
							() => {
								page = 1;
								keyword = '';
								requestData();
							}
						"
						type="text"
						icon="el-icon-refresh"
					></el-button>
				</template>
				<template slot-scope="scope">
					<el-dropdown>
						<span class="el-dropdown-link">
							<i class="el-icon-more"></i>
						</span>
						<el-dropdown-menu slot="dropdown">
							<el-dropdown-item
								icon="el-icon-zoom-in"
								@click.native.prevent="
									() => {
										selectedData = scope.row;
										showDetailDialog = true;
									}
								"
								>Lihat Detail</el-dropdown-item
							>
							<!-- <el-dropdown-item v-if="scope.row.delivery_status_id == 1" @click.native.prevent="printResi(scope.row)"><i class="el-icon-printer"></i> Print Receipt</el-dropdown-item> -->
							<!-- <el-dropdown-item v-if="scope.row.delivery_status_id == 1" @click.native.prevent="printAwb(scope.row)"><i class="el-icon-printer"></i> Print Airway Bill</el-dropdown-item> -->
							<el-dropdown-item
								icon="el-icon-warning-outline"
								v-if="
									scope.row.delivery_status_id < 4 &&
									scope.row.delivery_status_id !== null
								"
								@click.native.prevent="openStatusForm(scope.row)"
								>Update Status</el-dropdown-item
							>
							<el-dropdown-item
								icon="el-icon-edit-outline"
								divided
								@click.native.prevent="openForm(scope.row)"
								>Edit</el-dropdown-item
							>
							<el-dropdown-item
								icon="el-icon-delete"
								v-if="
									scope.row.delivery_status_id == 0 ||
									scope.row.delivery_status_id
								"
								@click.native.prevent="deleteData(scope.row.id)"
								>Hapus</el-dropdown-item
							>
						</el-dropdown-menu>
					</el-dropdown>
				</template>
			</el-table-column>
		</el-table>

		<br />

		<el-pagination
			background
			@current-change="
				(p) => {
					page = p;
					requestData();
				}
			"
			@size-change="
				(s) => {
					pageSize = s;
					requestData();
				}
			"
			layout="total, sizes, prev, pager, next"
			:page-size="pageSize"
			:page-sizes="[10, 25, 50, 100]"
			:total="tableData.total"
		></el-pagination>

		<el-dialog
			title="DETAIL PENGIRIMAN DOMESTIK"
			:visible.sync="showDetailDialog"
			fullscreen
		>
			<Detail v-if="!!selectedData" :data="selectedData" />
		</el-dialog>

		<DomesticDeliveryForm
			:delivery="formModel"
			:show="showForm"
			@close="showForm = false"
			@refresh="requestData"
		/>

		<UpdateForm
			@submitted="requestData"
			@close="showStatusForm = false"
			:model="updateFormModel"
			:data="selectedData"
			:visible.sync="showStatusForm"
		/>

		<ReportForm
			@submitted="requestData"
			@close="showReportForm = false"
			:visible.sync="showReportForm"
		/>

		<el-dialog
			center
			title="PILIH KOLOM"
			:visible.sync="selectField"
			width="300px"
		>
			<div style="height: 500px; overflow: auto">
				<el-checkbox-group v-model="selectedFields">
					<el-checkbox
						style="display: block; margin-bottom: 5px"
						v-for="(f, i) in fields"
						:label="f"
						:key="i"
					></el-checkbox>
				</el-checkbox-group>
			</div>
			<div slot="footer">
				<el-button
					type="primary"
					@click="
						() => {
							exportToExcel();
							selectField = false;
						}
					"
					>DOWNLOAD</el-button
				>
			</div>
		</el-dialog>
	</div>
</template>

<script>
import UpdateForm from "./UpdateForm";
import ReportForm from "./ReportForm";
import Detail from "./Detail";
import DomesticDeliveryForm from "./DomesticDeliveryForm";
import exportFromJSON from "export-from-json";

export default {
	components: { UpdateForm, Detail, ReportForm, DomesticDeliveryForm },
	data() {
		return {
			loading: false,
			tableData: {},
			page: 1,
			pageSize: 10,
			keyword: "",
			sort: "updated_at",
			order: "descending",
			formModel: { items: [] },
			showForm: false,
			showStatusForm: false,
			updateFormModel: {},
			showReportForm: false,
			selectedData: {},
			showDetailDialog: false,
			filters: {},
			dateRange: "",
			selectField: false,
			selectedFields: [],
			fields: [
				"Nomor SPB",
				"Nomor Resi",
				"Customer",
				"Asal",
				"Tujuan",
				"Alamat Pengiriman",
				"Jenis Pengiriman",
				"Layanan",
				"Jenis Armada",
				"Agent",
				"Nama Kapal",
				"No. Plat Armada",
				"Driver Armada",
				"No. HP Driver",
				"Tgl Pick Up",
				"ETD",
				"Tgl Kirim",
				"ETA",
				"Tgl Terima",
				"Penerima",
				"Tgl Terima STT",
				"Jml Koli",
				"Berat",
				"Berat Volume",
				"Berat Invoice",
				"Volume",
				"Volume Packing",
				"Biaya Kirim",
				"PPN Biaya Kirim",
				"Biaya Packing",
				"PPN Biaya Packing",
				"Biaya Penerus",
				"PPN Biaya Penerus",
				"Biaya Lain - Lain",
				"PPN Biaya Lain - Lain",
				"Total Biaya",
				"Status",
				"Waktu Update",
			],
		};
	},
	methods: {
		deleteData(id) {
			this.$confirm("Anda yakin akan menghapus data ini?", "Warning", {
				type: "warning",
			})
				.then(() => {
					axios
						.delete("/domesticDelivery/" + id)
						.then((r) => {
							this.requestData();
							this.$message({
								message: r.data.message,
								type: "success",
								showClose: true,
							});
						})
						.catch((e) => {
							this.$message({
								message: e.response.data.message,
								type: "error",
								showClose: true,
							});
						});
				})
				.catch(() => console.log(e));
		},
		printResi(data) {
			window.open(
				BASE_URL + "/domesticDelivery/printResi/" + data.id,
				"_blank"
			);
		},
		printAwb(data) {
			window.open(BASE_URL + "/domesticDelivery/printAwb/" + data.id, "_blank");
		},
		exportToExcel() {
			let params = {
				keyword: this.keyword,
				pageSize: 1000000, // hacky solution, but it works
				sort: this.sort,
				order: this.order,
				dateRange: this.dateRange,
			};

			this.loading = true;
			axios
				.get("/domesticDelivery", {
					params: Object.assign(params, this.filters),
				})
				.then((r) => {
					let data = r.data.data.map((d) => {
						return {
							"Nomor SPB": d.spb_number,
							"Nomor Resi": d.resi_number,
							Customer: d.customer,
							Asal: d.origin,
							Tujuan: d.destination,
							"Alamat Pengiriman": d.delivery_address,
							"Jenis Pengiriman": d.delivery_type,
							Layanan: d.service_type,
							"Jenis Armada": d.vehicle_type ? d.vehicle_type.name : "",
							Agent: d.agent,
							"Nama Kapal": d.ship_name,
							"No. Plat Armada": d.vehicle_number,
							"Driver Armada": d.driver_name,
							"No. HP Driver": d.driver_phone,
							"Tgl Pick Up": d.pick_up_date,
							ETD: d.etd ? moment(d.etd).format("DD/MM/YYYY") : "",
							"Tgl Kirim": d.delivery_date
								? moment(d.delivery_date).format("DD/MM/YYYY")
								: "",
							ETA: d.eta ? moment(d.eta).format("DD/MM/YYYY") : "",
							"Tgl Terima": d.delivered_date
								? moment(d.delivered_date).format("DD/MM/YYYY")
								: "",
							Penerima: d.receiver,
							"Tgl Terima STT": d.stt_received_date
								? moment(d.stt_received_date).format("DD/MM/YYYY")
								: "",
							"Jml Koli": d.quantity,
							Berat: d.weight,
							"Berat Volume": d.volume_weight,
							"Berat Invoice": d.invoice_weight,
							Volume: d.volume,
							"Volume Packing": d.packing_volume,
							"Biaya Kirim": d.delivery_cost,
							"PPN Biaya Kirim": d.delivery_cost_ppn * 0.1 * d.delivery_cost,
							"Biaya Packing": d.packing_cost,
							"PPN Biaya Packing": d.packing_cost_ppn * 0.1 * d.packing_cost,
							"Biaya Penerus": d.packing_cost,
							"PPN Biaya Penerus": d.packing_cost_ppn * 0.1 * d.packing_cost,
							"Biaya Lain - Lain": d.packing_cost,
							"PPN Biaya Lain - Lain":
								d.packing_cost_ppn * 0.1 * d.packing_cost,
							"Total Biaya": d.total_cost,
							Status: d.statusName.toUpperCase(),
							"Waktu Update": d.updated_at,
						};
					});

					const redux = (array) =>
						array.map((o) =>
							this.selectedFields.reduce((acc, curr) => {
								acc[curr] = o[curr];
								return acc;
							}, {})
						);

					exportFromJSON({
						data: redux(data),
						fileName: "domestic-delivery",
						exportType: "xls",
					});
				})
				.catch((e) => {
					console.log(e);
					if (e.response.status == 500) {
						this.$message({
							message: e.response.data.message,
							type: "error",
							showClose: true,
						});
					}
				})
				.finally(() => {
					this.loading = false;
				});
		},
		openStatusForm(data) {
			this.updateFormModel = { delivery_id: data.id };
			this.selectedData = JSON.parse(JSON.stringify(data));
			this.showStatusForm = true;
		},
		sortChange(c) {
			if (c.prop != this.sort || c.order != this.order) {
				this.sort = c.prop;
				this.order = c.order;
				this.requestData();
			}
		},
		requestData() {
			let params = {
				page: this.page,
				keyword: this.keyword,
				pageSize: this.pageSize,
				sort: this.sort,
				order: this.order,
				dateRange: this.dateRange,
			};

			this.loading = true;
			axios
				.get("/domesticDelivery", {
					params: Object.assign(params, this.filters),
				})
				.then((r) => {
					this.tableData = r.data;
					this.$emit("loaded", r.data);
				})
				.catch((e) => {
					if (e.response.status == 500) {
						this.$message({
							message:
								e.response.data.message +
								"\n" +
								e.response.data.file +
								":" +
								e.response.data.line,
							type: "error",
							showClose: true,
						});
					}
				})
				.finally(() => {
					this.loading = false;
				});
		},
		openForm(data) {
			this.formModel = JSON.parse(JSON.stringify(data));
			this.showForm = true;
		},
	},
	mounted() {
		this.requestData();
		this.$store.commit("getDeliveryTypeList");
		this.$store.commit("getServiceTypeList");
		this.$store.commit("getCityList");
		this.$store.commit("getAgentList");
		this.$store.commit("getCustomerList");
		this.$store.commit("getVehicleTypeList");
	},
};
</script>

<style lang="scss" scoped>
.big {
	font-size: 20px;
}
</style>
