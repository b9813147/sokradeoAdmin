<template>
    <v-app id="inspire">
        <v-navigation-drawer
            v-model="drawer"
            app
            clipped
        >
            <v-list dense>
                <district-sidebar v-if="district"></district-sidebar>
                <global-sidebar v-else-if="global_role_type"></global-sidebar>
                <sidebar v-else></sidebar>
            </v-list>
        </v-navigation-drawer>

        <v-app-bar
            app
            clipped-left
        >
            <v-app-bar-nav-icon @click.stop="drawer = !drawer"/>
            <div class="px-3">
                <v-avatar>
                    <img :src="this.$store.state.Path.district+this.$store.state.Auth.user.district_id+'/'+this.$store.state.Auth.user.thumbnail+'?'+Math.random(0,9)" alt="Logo" v-if="district">
                    <img :src="this.$store.state.Path.groupChannel+this.$store.state.Auth.user.channel_id+'/'+this.$store.state.Auth.user.thumbnail+'?'+Math.random(0,9)" alt="Logo" v-else-if="this.$store.state.Auth.user.thumbnail">
                    <img style="width: 100%; object-fit: contain;" src="/images/app/tw/teammodel/original-black-small.png" v-else>
                </v-avatar>
            </div>
            <v-toolbar-title>{{ global_role_type ? $t('global_management') : (district ? this.$store.state.Auth.user.name : this.$store.state.Auth.user.name) }}</v-toolbar-title>
            <v-toolbar-items class="ml-auto">
                <v-menu transition="scale-transition">
                    <template v-slot:activator="{ on }">
                        <v-btn
                            dark
                            color="blue-grey darken-2"
                            v-on="on"
                        >
                            {{ $t('language') }}
                        </v-btn>
                    </template>
                    <v-list>
                        <v-list-item v-for="v in langs" :key="v.text" @click="">
                            <v-list-item-title v-text="v.text" @click="setLang(v.value)"/>
                        </v-list-item>
                    </v-list>
                </v-menu>
            </v-toolbar-items>
        </v-app-bar>
        <v-main>
            <v-container
                fluid
            >
                <v-row
                    align="center"
                    justify="center"
                >
                    <v-col cols="col-12">
                        <router-view/>
                        <Snackbar></Snackbar>
                    </v-col>
                </v-row>
            </v-container>
        </v-main>

        <v-footer app>
            <span>&copy; {{ new Date().getFullYear() }}</span>
        </v-footer>
    </v-app>
</template>

<script>
import i18n            from "../lang";
import sidebar         from "../layouts/sidebar";
import districtSidebar from "../layouts/districtSidebar";
import globalSidebar   from "../layouts/globalSidebar";
import Snackbar        from "../components/notification/Snackbar.vue";

export default {
    components: {
        sidebar,
        districtSidebar,
        globalSidebar,
        Snackbar,
    },
    comments  : {
        sidebar        : sidebar,
        Snackbar       : Snackbar,
        districtSidebar: districtSidebar,
        globalSidebar  : globalSidebar
    },
    props     : {
        source: String
    },
    data      : () => ({
        drawer          : null,
        langs           : [{text: '繁體', value: 'zh-TW'}, {text: 'English', value: 'en-US'}, {text: '简体', value: 'zh-CN'}],
        district        : false,
        global_role_type: null,
    }),
    async created() {
        this.$vuetify.theme.dark = false
        await this.getLang()
        if (this.$store.state.Auth.user.global_role_type != null) {
            this.global_role_type = this.$store.state.Auth.user.global_role_type;
            this.$router.push('/global/recommendedVideo')
        } else if (this.$store.state.Auth.user.district_id == null) {
            this.$router.push(location.pathname)
        } else {
            this.district = true;
            this.$router.push(location.pathname);
        }
    },
    methods: {
        setLang(lang) {
            // 設定後端語系
            axios.get('/api/lang/setLocal', {
                params: {
                    lang: lang
                }
            }).then((resource) => {
                if (resource.status === 200) {
                    localStorage.setItem('local', lang);
                }
            });
            return window.location = location.pathname;
        },
        async getLang() {
            await axios.get('/api/lang/getLocal')
                .then((resource) => {
                    if (resource.status === 200) {
                        localStorage.setItem('local', resource.data.lang)
                        return this.$i18n.locale = resource.data.lang
                    }
                });
        }
    },
}
</script>
