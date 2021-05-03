<template>
	<div>
		<el-page-header @back="$emit('back')" content="USERS"> </el-page-header>
		<br />
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
					@click="openForm({ password: '' })"
					type="primary"
					>TAMBAH USER</el-button
				>
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
					style="margin-top: 5px"
					@current-change="
						(p) => {
							pagination.current_page = p;
							getData();
						}
					"
					@size-change="
						(s) => {
							pagination_per_page = s;
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
			@row-dblclick="
				(row, column, event) => {
					selectedData = row;
					showDetail = true;
				}
			"
			:default-sort="{ prop: sort, order: order }"
			height="calc(100vh - 205px)"
			v-loading="loading"
			@sort-change="sortChange"
		>
			<el-table-column
				label="Status"
				sortable="custom"
				min-width="100px"
				align="center"
				header-align="center"
			>
				<template slot-scope="scope">
					<el-tag
						class="full-width text-center"
						size="small"
						effect="dark"
						:type="scope.row.active ? 'success' : 'info'"
						>{{ scope.row.active ? "AKTIF" : "NONAKTIF" }}</el-tag
					>
				</template>
			</el-table-column>
			<el-table-column
				prop="name"
				label="Name"
				sortable="custom"
				min-width="150px"
			></el-table-column>
			<el-table-column
				prop="email"
				label="Email"
				sortable="custom"
				min-width="180px"
			></el-table-column>
			<el-table-column
				prop="phone"
				label="Phone"
				sortable="custom"
				min-width="150px"
			></el-table-column>
			<el-table-column
				prop="role"
				label="Role"
				sortable="custom"
				min-width="100px"
			>
				<template slot-scope="scope">
					{{ scope.row.role_name }}
				</template>
			</el-table-column>

			<el-table-column
				v-if="user.role == 11"
				prop="company"
				label="Company"
				sortable="custom"
				min-width="150px"
			>
				<template slot-scope="scope">
					{{ scope.row.company ? scope.row.company.name : "" }}
				</template>
			</el-table-column>

			<el-table-column
				prop="customer"
				label="Customer"
				sortable="custom"
				min-width="150px"
			>
				<template slot-scope="scope">
					{{ scope.row.customer ? scope.row.customer.name : "" }}
				</template>
			</el-table-column>

			<el-table-column
				prop="agent"
				label="Agent"
				sortable="custom"
				min-width="150px"
			>
				<template slot-scope="scope">
					{{ scope.row.agent ? scope.row.agent.name : "" }}
				</template>
			</el-table-column>

			<el-table-column
				fixed="right"
				width="40px"
				header-align="center"
				align="center"
			>
				<template slot="header">
					<el-button
						type="text"
						class="text-white"
						@click="
							() => {
								pagination.current_page = 1;
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
							<el-dropdown-item
								icon="el-icon-zoom-in"
								@click.native.prevent="
									() => {
										selectedData = scope.row;
										showDetail = true;
									}
								"
								>Lihat Detail</el-dropdown-item
							>
							<el-dropdown-item
								icon="el-icon-edit-outline"
								@click.native.prevent="openForm(scope.row)"
								>Edit</el-dropdown-item
							>
							<el-dropdown-item
								icon="el-icon-delete"
								@click.native.prevent="deleteData(scope.row.id)"
								>Hapus</el-dropdown-item
							>
						</el-dropdown-menu>
					</el-dropdown>
				</template>
			</el-table-column>
		</el-table>

		<el-dialog title="DETAIL USER" center :visible.sync="showDetail">
			<table class="table table-striped table-sm" v-if="selectedData">
				<tbody>
					<tr>
						<td class="text-bold" style="width: 150px">Name</td>
						<td>: {{ selectedData.name }}</td>
					</tr>
					<tr>
						<td class="text-bold">Email</td>
						<td>: {{ selectedData.email }}</td>
					</tr>
					<tr>
						<td class="text-bold">Phone</td>
						<td>: {{ selectedData.phone }}</td>
					</tr>
					<tr>
						<td class="text-bold">Role</td>
						<td>: {{ selectedData.role_name }}</td>
					</tr>
					<tr>
						<td class="text-bold">Company</td>
						<td>
							: {{ selectedData.company ? selectedData.company.name : "" }}
						</td>
					</tr>
					<tr>
						<td class="text-bold">Customer</td>
						<td>
							: {{ selectedData.customer ? selectedData.customer.name : "" }}
						</td>
					</tr>
					<tr>
						<td class="text-bold">Agent</td>
						<td>: {{ selectedData.agent ? selectedData.agent.name : "" }}</td>
					</tr>
					<tr>
						<td class="text-bold">Status</td>
						<td>: {{ selectedData.active ? "Aktif" : "Nonaktif" }}</td>
					</tr>
				</tbody>
			</table>
		</el-dialog>

		<el-dialog
			:visible.sync="showForm"
			:title="!!formModel.id ? 'EDIT USER' : 'TAMBAH USER'"
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

			<el-form label-width="170px" label-position="left">
				<el-form-item label="Name" :class="formErrors.name ? 'is-error' : ''">
					<el-input placeholder="Name" v-model="formModel.name"></el-input>
					<div class="el-form-item__error" v-if="formErrors.name">
						{{ formErrors.name[0] }}
					</div>
				</el-form-item>

				<el-form-item label="Email" :class="formErrors.email ? 'is-error' : ''">
					<el-input placeholder="Email" v-model="formModel.email"></el-input>
					<div class="el-form-item__error" v-if="formErrors.email">
						{{ formErrors.email[0] }}
					</div>
				</el-form-item>

				<el-form-item label="Phone" :class="formErrors.phone ? 'is-error' : ''">
					<el-input placeholder="Phone" v-model="formModel.phone"></el-input>
					<div class="el-form-item__error" v-if="formErrors.phone">
						{{ formErrors.phone[0] }}
					</div>
				</el-form-item>

				<el-form-item label="Role" :class="formErrors.role ? 'is-error' : ''">
					<el-select
						v-model="formModel.role"
						placeholder="Role"
						style="width: 100%"
					>
						<el-option
							v-for="r in roleList"
							:value="r.id"
							:label="r.role"
							:key="r.id"
						>
						</el-option>
					</el-select>
					<div class="el-form-item__error" v-if="formErrors.role">
						{{ formErrors.role[0] }}
					</div>
				</el-form-item>

				<el-form-item
					v-show="formModel.role == 21 || formModel.role == 31"
					label="Company"
					:class="formErrors.company_id ? 'is-error' : ''"
				>
					<el-select
						v-model="formModel.company_id"
						placeholder="Company"
						filterable
						default-first-option
						style="width: 100%"
					>
						<el-option
							v-for="(t, i) in companyList"
							:value="t.id"
							:label="t.code + ' - ' + t.name"
							:key="i"
						>
						</el-option>
					</el-select>
					<div class="el-form-item__error" v-if="formErrors.company_id">
						{{ formErrors.company_id[0] }}
					</div>
				</el-form-item>

				<el-form-item
					v-show="formModel.role == 41"
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
					v-show="formModel.role == 51"
					label="Agent"
					:class="formErrors.agent_id ? 'is-error' : ''"
				>
					<el-select
						v-model="formModel.agent_id"
						placeholder="Agent"
						filterable
						default-first-option
						style="width: 100%"
					>
						<el-option
							v-for="(t, i) in agentList"
							:value="t.id"
							:label="t.code + ' - ' + t.name"
							:key="i"
						>
						</el-option>
					</el-select>
					<div class="el-form-item__error" v-if="formErrors.agent_id">
						{{ formErrors.agent_id[0] }}
					</div>
				</el-form-item>

				<el-form-item
					label="Password"
					:class="formErrors.password ? 'is-error' : ''"
				>
					<el-input
						type="password"
						placeholder="Password"
						v-model="formModel.password"
					></el-input>
					<div class="el-form-item__error" v-if="formErrors.password">
						{{ formErrors.password[0] }}
					</div>
				</el-form-item>

				<el-form-item
					label="Password Confirmation"
					:class="formErrors.password ? 'is-error' : ''"
				>
					<el-input
						type="password"
						placeholder="Password Confirmation"
						v-model="formModel.password_confirmation"
					></el-input>
				</el-form-item>

				<el-form-item
					label="Status"
					:class="formErrors.active ? 'is-error' : ''"
				>
					<el-switch
						:active-value="1"
						:inactive-value="0"
						v-model="formModel.active"
						active-color="#13ce66"
					>
					</el-switch>
					<el-tag
						:type="formModel.active ? 'success' : 'info'"
						size="small"
						style="margin-left: 10px"
						>{{ !!formModel.active ? "Active" : "Inactive" }}</el-tag
					>

					<div class="el-form-item__error" v-if="formErrors.active">
						{{ formErrors.active[0] }}
					</div>
				</el-form-item>
			</el-form>
			<span slot="footer" class="dialog-footer">
				<el-button icon="el-icon-success" type="primary" @click="saveData"
					>SIMPAN</el-button
				>
				<el-button icon="el-icon-error" type="info" @click="showForm = false"
					>BATAL</el-button
				>
			</span>
		</el-dialog>
	</div>
</template>

<script>
import crud from "../crud";
import { mapGetters, mapState } from "vuex";

export default {
	mixins: [crud],
	data() {
		return {
			showDetail: false,
			url: "/api/user"
		};
	},

	computed: {
		...mapState(["roleList", "customerList", "agentList", "companyList"]),
		...mapGetters({ user: "auth/user" })
	},

	mounted() {
		this.$store.commit("getAgentList");
		this.$store.commit("getCompanyList");
		this.$store.commit("getCustomerList");
		this.$store.commit("getRoleList");
	}
};
</script>
