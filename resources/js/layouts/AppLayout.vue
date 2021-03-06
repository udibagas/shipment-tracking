<template>
	<el-container>
		<el-aside width="auto">
			<div class="brand-box">
				<img
					v-if="user.company"
					:src="baseUrl + user.company.logo"
					:style="
						collapse ? 'height:40px;margin:25px 0' : 'height:60px;margin:25px 0'
					"
					alt=""
				/>

				<div v-show="!collapse" style="margin-top: 25px">
					<el-avatar :size="50" icon="el-icon-user"></el-avatar>
					<br />
					<strong>{{ user.name }}</strong
					><br />
					<small>{{ user.email }}</small>
					<br /><br />
				</div>
			</div>

			<el-menu
				router
				:collapse="collapse"
				default-active="1"
				background-color="#060446"
				text-color="#fff"
				class="sidebar"
				active-text-color="#cc0000"
			>
				<el-menu-item
					v-for="(m, i) in menus"
					:index="m.path"
					:key="i"
					:disabled="m.disabled"
				>
					<i :class="m.icon"></i><span slot="title">{{ m.label }}</span>
				</el-menu-item>
			</el-menu>
		</el-aside>
		<el-container>
			<el-header>
				<el-row>
					<el-col :span="12">
						<el-button
							type="text"
							class="btn-big text-white"
							@click.prevent="collapse = !collapse"
							:icon="collapse ? 'el-icon-s-unfold' : 'el-icon-s-fold'"
						></el-button>
						<span class="brand"> {{ appName }} </span>
					</el-col>
					<el-col :span="12" class="text-right">
						<el-dropdown @command="handleCommand">
							<span class="el-dropdown-link" style="cursor: pointer"
								>Selamat Datang, {{ user.name }}!</span
							>
							<el-dropdown-menu slot="dropdown">
								<el-dropdown-item icon="el-icon-user" command="profile"
									>Profil Saya</el-dropdown-item
								>
								<el-dropdown-item icon="el-icon-d-arrow-right" command="logout"
									>Logout</el-dropdown-item
								>
							</el-dropdown-menu>
						</el-dropdown>
					</el-col>
				</el-row>
			</el-header>
			<el-main>
				<el-collapse-transition>
					<slot />
				</el-collapse-transition>
			</el-main>
		</el-container>

		<Profile
			v-if="authenticated"
			:show="showProfile"
			@close="showProfile = false"
		/>
	</el-container>
</template>

<script>
import Profile from "../pages/Profile";
import { mapGetters, mapActions } from "vuex";

export default {
	components: { Profile },
	computed: {
		menus() {
			return this.$store.state.navigation;
		},
		appName() {
			return APP_NAME;
		},
		...mapGetters({
			authenticated: "auth/authenticated",
			user: "auth/user"
		})
	},
	data() {
		return {
			collapse: false,
			baseUrl: BASE_URL,
			showProfile: false
		};
	},
	methods: {
		...mapActions({
			signOutAction: "auth/signOut"
		}),

		goBack() {
			window.history.back();
		},

		async signOut() {
			await this.signOutAction();
			this.$router.push("/login");
		},

		handleCommand(command) {
			if (command === "logout") {
				this.signOut();
			}

			if (command === "profile") {
				this.showProfile = true;
			}
		}
	},

	mounted() {
		this.$store.commit("getNavigation");
	}
};
</script>

<style lang="css" scoped>
.el-main {
	height: calc(100vh - 60px);
	overflow: auto;
}

.brand {
	font-size: 22px;
	margin-left: 20px;
}

.brand-box {
	max-height: 220px;
	background-color: #060446;
	text-align: center;
	color: #fff;
}

.btn-big {
	font-size: 22px;
}

.el-header {
	background-color: #4d00d5;
	color: #fff;
	line-height: 60px;
}

.sidebar {
	background-color: #060446;
	border-color: #060446;
	height: calc(100vh - 220px);
}

.sidebar:not(.el-menu--collapse) {
	width: 220px;
}

.el-aside {
	height: 100vh;
	background-color: #060446;
	border-color: #060446;
}

.el-dropdown-link {
	color: #fff;
}
</style>
