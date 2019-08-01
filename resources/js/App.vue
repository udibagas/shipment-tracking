<template>
    <el-container>
        <Login :visible.sync="!this.$store.state.is_logged_in" />
        <Profile :show="showProfile" @close="showProfile = false" />
        <el-header>
            <el-row>
                <el-col :span="12">
                    <el-button type="text" class="btn-big text-white" @click.prevent="collapse = !collapse" :icon="collapse ? 'el-icon-s-unfold' : 'el-icon-s-fold'"></el-button>
                    <span class="brand"> {{appName}} </span>
                </el-col>
                <el-col :span="12" class="text-right">
                    <el-dropdown @command="handleCommand">
                        <span class="el-dropdown-link" style="cursor:pointer">Welcome, {{$store.state.user.name}}!</span>
                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item command="profile">My Profile</el-dropdown-item>
                            <el-dropdown-item command="logout">Logout</el-dropdown-item>
                        </el-dropdown-menu>
                    </el-dropdown>
                </el-col>
            </el-row>
        </el-header>

        <el-container>
            <el-aside width="auto">
                <el-menu
                :router="true"
                :collapse="collapse"
                default-active="1"
                background-color="#060446"
                text-color="#fff"
                class="sidebar"
                active-text-color="#cc0000">
                    <el-menu-item v-for="(m, i) in menus" :index="m.path" :key="i">
                        <i :class="m.icon"></i><span slot="title">{{m.label}}</span>
                    </el-menu-item>
                </el-menu>
            </el-aside>
            <el-main>
                <el-collapse-transition>
                    <router-view @back="goBack"></router-view>
                </el-collapse-transition>
            </el-main>
        </el-container>
    </el-container>
</template>

<script>
import Login from './pages/Login'
import Profile from './pages/Profile'

export default {
    components: { Login, Profile },
    computed: {
        menus() {
            return [
                {label: 'Dashboard', icon: 'el-icon-menu', path: '/' },
                {label: 'Domestic Deliveries', icon: 'el-icon-truck', path: 'domestic-delivery' },
                {label: 'International Deliveries', icon: 'el-icon-ship', path: 'international-delivery' },
                {label: 'Reports', icon: 'el-icon-data-analysis', path: 'report' },
                {label: 'Agents', icon: 'el-icon-s-custom', path: 'agent' },
                {label: 'Companies', icon: 'el-icon-office-building', path: 'company' },
                {label: 'Customers', icon: 'el-icon-connection', path: 'customer' },
                {label: 'Delivery Statuses', icon: 'el-icon-s-flag', path: 'delivery-status' },
                {label: 'Delivery Services', icon: 'el-icon-document-copy', path: 'service-type' },
                {label: 'Delivery Types', icon: 'el-icon-collection-tag', path: 'delivery-type' },
                {label: 'Users', icon: 'el-icon-user', path: 'user' },
            ]
        }
    },
    data() {
        return {
            collapse: false,
            appName: APP_NAME,
            showProfile: false,
            loginForm: !this.$store.state.is_logged_in
        }
    },
    methods: {
        goBack() {
            window.history.back();
        },
        handleCommand(command) {
            if (command === 'logout') {
                axios.get('/logout').then(r => {
                    window.localStorage.removeItem('user')
                    window.localStorage.removeItem('token')
                    this.$store.state.user = {}
                    this.$store.state.token = ''
                    this.$store.state.is_logged_in = false
                })
            }

            if(command === 'profile') {
                this.showProfile = true
            }
        },
    }
}
</script>

<style lang="css" scoped>
.brand {
    font-size: 22px;
    margin-left: 20px;
}

.btn-big {
    font-size: 22px;
}

.el-header {
    background-color: #2d5309;
    color: #fff;
    line-height: 60px;
}

.sidebar {
    background-color: #463404;
    border-color: #060446;
    height: calc(100vh - 60px);
}

.sidebar:not(.el-menu--collapse) {
    width: 220px;
}

.el-aside {
    height: calc(100vh - 60px);
}

.el-dropdown-link {
    color: #fff;
}
</style>
