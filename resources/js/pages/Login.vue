<template>
	<el-container id="login-form">
		<el-main>
			<div style="text-align: center; margin: 60px 0">
				<img src="images/logo.jpeg" alt="" style="width: 70px" />
				<h2>{{ appName }}</h2>
			</div>
			<el-form style="width: 300px; margin: 0px auto; text-align: center">
				<el-divider><h3>LOGIN</h3></el-divider>

				<el-form-item>
					<el-input v-model="email" placeholder="Email"></el-input>
				</el-form-item>
				<el-form-item>
					<el-input
						type="password"
						v-model="password"
						placeholder="Password"
					></el-input>
				</el-form-item>
				<el-form-item>
					<el-button type="primary" @click="login" style="width: 100%"
						>LOGIN</el-button
					>
				</el-form-item>
				<el-form-item>
					<el-button
						type="primary"
						plain
						@click="cekResiDialog = true"
						style="width: 100%"
						>CEK RESI</el-button
					>
				</el-form-item>

				<el-divider>&copy; {{ year }}</el-divider>
			</el-form>

			<el-dialog
				v-loading="loading"
				:visible.sync="cekResiDialog"
				fullscreen
				title="CEK RESI"
			>
				<div style="width: 350px; margin: 0 auto 20px">
					<el-form>
						<el-form-item
							label="Nomor SPB/Nomor Resi"
							:class="formErrors.tracking_number ? 'is-error' : ''"
						>
							<el-input
								class="text-center"
								placeholder="NOMOR SPB/NOMOR RESI PENGIRIMAN"
								v-model="tracking_number"
							></el-input>
							<div
								class="el-form-item__error"
								v-if="formErrors.tracking_number"
							>
								{{ formErrors.tracking_number[0] }}
							</div>
						</el-form-item>
						<el-form-item
							label="Nomor HP/Email"
							:class="formErrors.phone_or_email ? 'is-error' : ''"
						>
							<el-input
								class="text-center"
								placeholder="NOMOR HP/EMAIL"
								v-model="phone_or_email"
							></el-input>
							<div class="el-form-item__error" v-if="formErrors.phone_or_email">
								{{ formErrors.phone_or_email[0] }}
							</div>
						</el-form-item>
						<br />
						<el-form-item>
							<el-button @click="cekResi" type="primary" style="width: 100%"
								>TAMPILKAN DATA</el-button
							>
						</el-form-item>
					</el-form>
				</div>

				<DetailDelivery v-if="delivery_data" :data="delivery_data" />
			</el-dialog>
		</el-main>
	</el-container>
</template>

<script>
import DetailDelivery from "./domestic-delivery/DetailCustomer";
import { mapActions } from "vuex";

export default {
	components: { DetailDelivery },
	data() {
		return {
			appName: APP_NAME,
			email: "",
			password: "",
			year: moment().format("YYYY"),
			cekResiDialog: false,
			tracking_number: "",
			delivery_data: null,
			phone_or_email: "",
			loading: false,
			formErrors: {}
		};
	},

	methods: {
		...mapActions({
			signIn: "auth/signIn"
		}),

		cekResi() {
			const data = {
				tracking_number: this.tracking_number,
				phone_or_email: this.phone_or_email
			};

			this.loading = true;
			axios
				.post("/api/cekResi", data)
				.then(r => {
					this.formErrors = {};
					this.delivery_data = r.data;
				})
				.catch(e => {
					const status = e.response.status;

					if (status == 422) {
						this.formErrors = e.response.data.errors;
					}

					if (status == 404 || status == 500) {
						this.$message({
							message: e.response.data.message,
							type: "error",
							showClose: true
						});
					}
				})
				.finally(() => {
					this.loading = false;
				});
		},

		async login() {
			if (!this.email || !this.password) {
				return;
			}

			let data = {
				email: this.email,
				password: this.password
			};

			await this.signIn(data);

			this.$router.push({ path: "/" });
		}
	},
	mounted() {
		document.getElementById("login-form").onkeypress = e => {
			if (e.key == "Enter") {
				this.login();
			}
		};
	}
};
</script>
