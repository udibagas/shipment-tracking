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
			<el-form-item class="margin-bottom-10">
				<el-button
					size="small"
					icon="el-icon-plus"
					@click="openForm({})"
					type="primary"
				>
					TAMBAH TARIF CHARTER
				</el-button>
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
							getData();
						}
					"
				>
				</el-input>
			</el-form-item>
			<el-form-item class="margin-bottom-10">
				<el-pagination
					background
					@current-change="
						(p) => {
							pagination.current_page = p;
							getData();
						}
					"
					@size-change="
						(s) => {
							pagination.per_page = s;
							getData();
						}
					"
					layout="total, sizes, prev, next"
					:page-size="pagination.per_page"
					:page-sizes="[10, 25, 50, 100]"
					:total="pagination.total"
				>
				</el-pagination>
			</el-form-item>
		</el-form>

		<el-table
			:data="tableData"
			stripe
			:default-sort="{ prop: sort, order: order }"
			height="calc(100vh - 260px)"
			v-loading="loading"
			@filter-change="
				(f) => {
					let c = Object.keys(f)[0];
					filters[c] = Object.values(f[c]);
					page = 1;
					getData();
				}
			"
			@sort-change="sortChange"
		>
			<el-table-column
				v-if="user.role == 11"
				prop="company"
				label="Company"
				sortable="custom"
				show-overflow-tooltip
			></el-table-column>

			<el-table-column
				:filters="
					customerList.map((c) => {
						return { value: c.id, text: c.name };
					})
				"
				show-overflow-tooltip
				column-key="customer_id"
				prop="customer"
				label="Customer"
				sortable="custom"
			>
			</el-table-column>

			<el-table-column
				prop="destination"
				label="Tujuan"
				sortable="custom"
				show-overflow-tooltip
			></el-table-column>

			<el-table-column
				:filters="
					vehicleTypeList.map((c) => {
						return { value: c.id, text: c.name };
					})
				"
				column-key="vehicle_type_id"
				prop="vehicle"
				label="Jenis Armada"
				sortable="custom"
			></el-table-column>

			<el-table-column
				prop="fare"
				label="Tarif"
				sortable="custom"
				header-align="right"
				align="right"
			>
				<template slot-scope="scope">
					Rp. {{ scope.row.fare | formatNumber }}
				</template>
			</el-table-column>
			<el-table-column
				prop="lead_time"
				label="Lead Time"
				sortable="custom"
				align="center"
				header-align="center"
			>
				<template slot-scope="scope"> {{ scope.row.lead_time }} HARI </template>
			</el-table-column>
			<el-table-column
				prop="ppn"
				label="PPN"
				sortable="custom"
				align="center"
				header-align="center"
			>
				<template slot-scope="scope">
					<i
						:class="
							scope.row.ppn
								? 'el-icon-check text-success'
								: 'el-icon-close text-danger'
						"
					></i>
				</template>
			</el-table-column>
			<el-table-column
				prop="updated_at"
				label="Update"
				sortable="custom"
				align="center"
				header-align="center"
			>
				<template slot-scope="scope">
					{{ scope.row.updated_at | readableDate }}
				</template>
			</el-table-column>
			<el-table-column width="40px" align="center" header-align="center">
				<template slot="header">
					<el-button
						type="text"
						class="text-white"
						@click="
							() => {
								page = 1;
								keyword = '';
								getData();
							}
						"
						icon="el-icon-refresh"
					>
					</el-button>
				</template>
				<template slot-scope="scope">
					<el-dropdown>
						<span class="el-dropdown-link">
							<i class="el-icon-more"></i>
						</span>
						<el-dropdown-menu slot="dropdown">
							<el-dropdown-item @click.native.prevent="openForm(scope.row)"
								><i class="el-icon-edit-outline"></i> Edit</el-dropdown-item
							>
							<el-dropdown-item @click.native.prevent="deleteData(scope.row.id)"
								><i class="el-icon-delete"></i> Hapus</el-dropdown-item
							>
						</el-dropdown-menu>
					</el-dropdown>
				</template>
			</el-table-column>
		</el-table>

		<el-dialog
			:visible.sync="showForm"
			:title="!!formModel.id ? 'EDIT TARIF CHARTER' : 'TAMBAH TARIF CHARTER'"
			width="500px"
			v-loading="loading"
			:close-on-click-modal="false"
		>
			<el-alert
				type="error"
				title="ERROR"
				:description="error.message + '\n' + error.file + ':' + error.line"
				v-show="error.message"
				style="margin-bottom: 15px"
			>
			</el-alert>

			<el-form label-width="150px" label-position="left">
				<el-form-item
					label="Customer"
					:class="formErrors.customer_id ? 'is-error' : ''"
				>
					<el-select
						v-model="formModel.customer_id"
						placeholder="Customer"
						filterable
						default-first-option
						style="width: 100%"
					>
						<el-option
							v-for="(t, i) in customerList"
							:value="t.id"
							:label="t.code + ' - ' + t.name"
							:key="i"
						>
						</el-option>
					</el-select>
					<div class="el-form-item__error" v-if="formErrors.customer_id">
						{{ formErrors.customer_id[0] }}
					</div>
				</el-form-item>

				<el-form-item
					label="Tujuan"
					:class="formErrors.destination ? 'is-error' : ''"
				>
					<el-select
						v-model="formModel.destination"
						placeholder="Tujuan"
						filterable
						default-first-option
						style="width: 100%"
					>
						<el-option
							v-for="(t, i) in cityList"
							:value="t.name"
							:label="t.name"
							:key="i"
						>
						</el-option>
					</el-select>
					<div class="el-form-item__error" v-if="formErrors.destination">
						{{ formErrors.destination[0] }}
					</div>
				</el-form-item>

				<el-form-item
					label="Jenis Armada"
					:class="formErrors.vehicle_type_id ? 'is-error' : ''"
				>
					<el-select
						v-model="formModel.vehicle_type_id"
						placeholder="Jenis Armada"
						filterable
						default-first-option
						style="width: 100%"
					>
						<el-option
							v-for="(t, i) in vehicleTypeList"
							:value="t.id"
							:label="t.name"
							:key="i"
						>
						</el-option>
					</el-select>
					<div class="el-form-item__error" v-if="formErrors.vehicle_type_id">
						{{ formErrors.vehicle_type_id[0] }}
					</div>
				</el-form-item>

				<el-form-item
					label="Tarif (Rp)"
					:class="formErrors.fare ? 'is-error' : ''"
				>
					<el-input
						type="number"
						placeholder="Tarif (Rp)"
						v-model="formModel.fare"
					></el-input>
					<div class="el-form-item__error" v-if="formErrors.fare">
						{{ formErrors.fare[0] }}
					</div>
				</el-form-item>

				<el-form-item
					label="Lead Time"
					:class="formErrors.lead_time ? 'is-error' : ''"
				>
					<el-input
						type="number"
						placeholder="Lead Time"
						v-model="formModel.lead_time"
					></el-input>
					<div class="el-form-item__error" v-if="formErrors.lead_time">
						{{ formErrors.lead_time[0] }}
					</div>
				</el-form-item>

				<el-form-item label="PPN" :class="formErrors.ppn ? 'is-error' : ''">
					<el-select
						placeholder="PPN"
						v-model="formModel.ppn"
						style="width: 100%"
					>
						<el-option
							v-for="(label, index) in ['Tidak', 'Ya']"
							:key="index"
							:value="index"
							:label="label"
						></el-option>
					</el-select>
					<div class="el-form-item__error" v-if="formErrors.ppn">
						{{ formErrors.ppn[0] }}
					</div>
				</el-form-item>
			</el-form>
			<span slot="footer" class="dialog-footer">
				<el-button type="primary" @click="saveData" icon="el-icon-success">
					SIMPAN
				</el-button>
				<el-button type="info" @click="showForm = false" icon="el-icon-error">
					BATAL
				</el-button>
			</span>
		</el-dialog>
	</div>
</template>

<script>
import { mapGetters, mapState } from "vuex";
import crud from "../crud";

export default {
	mixins: [crud],
	data() {
		return {
			url: "/api/masterFareCharter"
		};
	},
	computed: {
		...mapState(["vehicleTypeList", "customerList", "cityList"]),
		...mapGetters({ user: "auth/user" })
	}
};
</script>
