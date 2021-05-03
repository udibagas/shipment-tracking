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
					>ADD NEW DELIVERY TYPE</el-button
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
			:default-sort="{ prop: sort, order: order }"
			height="calc(100vh - 260px)"
			v-loading="loading"
			@sort-change="sortChange"
		>
			<el-table-column
				prop="code"
				label="Code"
				sortable="custom"
				show-overflow-tooltip
			></el-table-column>
			<el-table-column
				prop="name"
				label="Name"
				sortable="custom"
				show-overflow-tooltip
			></el-table-column>
			<el-table-column
				prop="description"
				label="Description"
				sortable="custom"
				show-overflow-tooltip
			></el-table-column>
			<el-table-column width="40px" header-align="center" align="center">
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
							<el-dropdown-item @click.native.prevent="openForm(scope.row)"
								><i class="el-icon-edit-outline"></i> Edit</el-dropdown-item
							>
							<el-dropdown-item @click.native.prevent="deleteData(scope.row.id)"
								><i class="el-icon-delete"></i> Delete</el-dropdown-item
							>
						</el-dropdown-menu>
					</el-dropdown>
				</template>
			</el-table-column>
		</el-table>

		<el-dialog
			:visible.sync="showForm"
			:title="!!formModel.id ? 'EDIT DELIVERY TYPE' : 'ADD NEW DELIVERY TYPE'"
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

			<el-form label-width="120px" label-position="left">
				<el-form-item label="Code" :class="formErrors.code ? 'is-error' : ''">
					<el-input placeholder="Code" v-model="formModel.code"></el-input>
					<div class="el-form-item__error" v-if="formErrors.code">
						{{ formErrors.code[0] }}
					</div>
				</el-form-item>

				<el-form-item label="Name" :class="formErrors.name ? 'is-error' : ''">
					<el-input placeholder="Name" v-model="formModel.name"></el-input>
					<div class="el-form-item__error" v-if="formErrors.name">
						{{ formErrors.name[0] }}
					</div>
				</el-form-item>

				<el-form-item
					label="Descrption"
					:class="formErrors.description ? 'is-error' : ''"
				>
					<el-input
						placeholder="Descrption"
						v-model="formModel.description"
					></el-input>
					<div class="el-form-item__error" v-if="formErrors.description">
						{{ formErrors.description[0] }}
					</div>
				</el-form-item>
			</el-form>
			<span slot="footer" class="dialog-footer">
				<el-button type="primary" @click="saveData"
					><i class="el-icon-success"></i> SAVE</el-button
				>
				<el-button type="info" @click="showForm = false"
					><i class="el-icon-error"></i> CANCEL</el-button
				>
			</span>
		</el-dialog>
	</div>
</template>

<script>
import crud from "../crud";

export default {
	mixins: [crud],
	data() {
		return {
			url: "/api/deliveryType"
		};
	}
};
</script>
