<template>
    <div>
        <Login v-if="!$store.state.is_logged_in" />
        <el-container v-else>
            <Profile :show="showProfile" @close="showProfile = false" />
            <el-aside width="auto">
                <div class="brand-box">
                    <img :src="baseUrl + $store.state.company.logo" :style="collapse ? 'height:40px;margin:25px 0' : 'height:60px;margin:25px 0'" alt="">

                    <div v-show="!collapse">
                        <el-avatar :size="50" icon="el-icon-user"></el-avatar>
                        <br>
                        <strong>{{$store.state.user.name}}</strong><br>
                        <small>{{$store.state.user.email}}</small>
                        <br><br>
                    </div>
                </div>

                <el-menu
                router
                :collapse="collapse"
                default-active="1"
                background-color="#060446"
                text-color="#fff"
                class="sidebar"
                active-text-color="#cc0000">
                    <el-menu-item v-for="(m, i) in menus" :index="m.path" :key="i" :disabled="m.disabled">
                        <i :class="m.icon"></i><span slot="title">{{m.label}}</span>
                    </el-menu-item>
                </el-menu>
            </el-aside>
            <el-container>
                <el-header>
                    <el-row>
                        <el-col :span="12">
                            <el-button type="text" class="btn-big text-white" @click.prevent="collapse = !collapse" :icon="collapse ? 'el-icon-s-unfold' : 'el-icon-s-fold'"></el-button>
                            <span class="brand"> {{appName}} </span>
                        </el-col>
                        <el-col :span="12" class="text-right">
                            <el-dropdown @command="handleCommand">
                                <span class="el-dropdown-link" style="cursor:pointer">Selamat Datang, {{$store.state.user.name}}!</span>
                                <el-dropdown-menu slot="dropdown">
                                    <el-dropdown-item command="profile"><i class="el-icon-user"></i> Profil Saya</el-dropdown-item>
                                    <el-dropdown-item command="logout"><i class="el-icon-d-arrow-right"></i> Logout</el-dropdown-item>
                                </el-dropdown-menu>
                            </el-dropdown>
                        </el-col>
                    </el-row>
                </el-header>
                <el-main style="height:calc(100vh - 60px);overflow:auto;">
                    <el-collapse-transition>
                        <router-view @back="goBack"></router-view>
                    </el-collapse-transition>
                </el-main>
            </el-container>
        </el-container>
    </div>
</template>

<script>
import Login from './pages/Login'
import Profile from './pages/Profile'

export default {
    components: { Login, Profile },
    computed: {
        menus() {
            return this.$store.state.navigation
        },
        appName() {
            return APP_NAME + ' - ' + this.$store.state.company.name
        }
    },
    data() {
        return {
            collapse: false,
            baseUrl: BASE_URL,
            showProfile: false,
            showCompanyProfile: false,
            loginForm: !this.$store.state.is_logged_in
        }
    },
    methods: {
        goBack() {
            window.history.back();
        },
        handleCommand(command) {
            if (command === 'logout') {
                axios.post('/logout').then(r => {

                }).finally(() => {
                    this.$router.push({path: '/'})
                    window.localStorage.removeItem('user')
                    // window.localStorage.removeItem('token')
                    this.$store.state.user = {}
                    this.$store.state.token = ''
                    this.$store.state.is_logged_in = false
                })
            }

            if(command === 'profile') {
                this.showProfile = true
            }
        },
    },
    mounted() {
        this.$store.commit('getNavigation')
        this.$store.commit('getCompanyByUser')
    }
}
</script>

<style lang="css" scoped>
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
