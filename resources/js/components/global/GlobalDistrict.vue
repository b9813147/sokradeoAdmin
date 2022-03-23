<template>
    <v-card>
        <v-card-title>
            <v-text-field
                v-model="search"
                append-icon="search"
                label="Search"
                single-line
                hide-details
            />
        </v-card-title>
        <v-data-table
            :headers="headers"
            :items="resources"
            :search="search"
            sort-by="calories"
            class="elevation-1"
            :loading="this.$store.state.Status.isLoading"
            loading-text="Loading... Please wait"
        >
            <template v-slot:top>
                <v-toolbar flat color="white">
                    <v-toolbar-title>{{ $t('event.districtManage') }}</v-toolbar-title>
                    <v-divider
                        class="mx-4"
                        inset
                        vertical
                    />
                    <v-spacer/>
                    <v-dialog v-model="dialog" max-width="900px">
                        <template v-slot:activator="{ on }">
                            <v-btn color="primary" dark class="mb-2" v-on="on">+</v-btn>
                        </template>
                        <v-card>
                            <v-card-title>
                                <span class="headline">{{ formTitle }}</span>
                            </v-card-title>

                            <v-card-text>
                                <v-container>
                                    <v-row>
                                        <v-col cols="12" sm="6" md="4">
                                            <v-text-field v-model="editedItem.school_code" :error-messages="errors.school_code" :label="$t('school_code')"/>
                                        </v-col>
                                        <v-col cols="12" sm="6" md="4">
                                            <v-text-field v-model="editedItem.abbr" :error-messages="errors.abbr" :label="$t('district_code')"/>
                                        </v-col>
                                        <v-col cols="12" sm="6" md="4">
                                            <v-text-field v-model="editedItem.name" :error-messages="errors.name" :label="$t('district_name')"/>
                                        </v-col>
                                        <v-col cols="12" sm="6" md="4">
                                            <v-text-field v-model="editedItem.description" :label="$t('description')"/>
                                        </v-col>
                                        <v-col cols="12" sm="6" md="4">
                                            <v-text-field v-model="editedItem.group_ids" :label="$t('selectChannel')"/>
                                        </v-col>
                                        <v-col cols="12" sm="6" md="4">
                                            <v-select :items="status" item-text="text" item-value="value" :label="$t('status')" v-model="editedItem.status" return-object required/>
                                        </v-col>
                                        <v-col cols="12" sm="6" md="4" v-if="editedIndex===-1">
                                            <v-select :items="langs" item-text="text" item-value="value" :label="$t('language')" v-model="editedItem.lang" return-object required/>
                                        </v-col>
                                        <v-col cols="12" sm="6" md="4">
                                            <v-file-input v-model="editedItem.thumbnail" multiple :label="$t('thumbnail')"/>
                                        </v-col>
                                    </v-row>
                                </v-container>
                            </v-card-text>

                            <v-card-actions>
                                <v-spacer/>
                                <v-btn color="blue darken-1" text @click="close">{{ $t('cancel') }}</v-btn>
                                <v-btn color="blue darken-1" text @click="save">{{ $t('submit') }}</v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                </v-toolbar>
            </template>
            <template v-slot:item.thumbnail="{ item }">
                <v-img :src="item.thum" style="width: 50px; height: 50px" v-if="item.thum"/>
                <v-img src="/storage/error.png" style="width: 50px; height: 50px" v-else/>
            </template>
            <template v-slot:item.action="{ item }">
                <v-icon
                    small
                    class="mr-2"
                    @click="editItem(item)"
                >
                    edit
                </v-icon>
                <!--      <v-icon-->
                <!--        small-->
                <!--        @click="deleteItem(item)"-->
                <!--      >-->
                <!--        delete-->
                <!--      </v-icon>-->
            </template>
            <template v-slot:no-data>
                <v-btn color="primary" @click="initialize">Reset</v-btn>
            </template>
            <!--            <template v-slot:item.review_status="{ item }">-->
            <!--                {{ item.review_status.text }}-->
            <!--            </template>-->
            <!--            <template v-slot:item.public_note_status="{ item }">-->
            <!--                {{ item.public_note_status.text }}-->
            <!--            </template>-->
        </v-data-table>
    </v-card>
</template>


<script>
export default {
    name: "GlobalDistrict",
    data() {
        return {
            resources       : [],
            dialog          : false,
            search          : '',
            headers         : [],
            langs           : [],
            editedIndex     : -1,
            editedItem      : {
                id         : null,
                abbr       : null,
                school_code: null,
                thumbnail  : null,
                status     : null,
                name       : null,
                description: null,
                group_ids  : [],
            },
            defaultItem     : {
                id         : null,
                abbr       : null,
                school_code: null,
                thumbnail  : null,
                status     : null,
                name       : null,
                description: null,
                group_ids  : [],
            },
            status          : {text: this.$t('enable'), value: 1},
            errors          : {},
            nameRules       : [
                v => !!v || 'Name is required',
                v => (v && v.length <= 30) || 'Name must be less than 30 characters'
            ],
            school_codeRules: [
                v => !!v || 'school_code is required',
                v => (v && v.length <= 30) || 'Name must be less than 30 characters'
            ]

        };
    },
    computed: {
        formTitle() {
            return this.editedIndex === -1 ? this.$t('create_district') : this.$t('editor_district')
        }
    },
    watch   : {
        dialog(val) {
            val || this.close()
        },
        '$route': 'initialize'
    },
    created() {
        this.initialize()
    },
    methods: {
        initialize() {
            let _this = this;
            let url = `/api/district`;
            _this.$store.dispatch("updateLoading", true);

            axios.get(url).then((response) => {
                let data = response.data;
                //thum 顯示圖片用 沒有任何作用
                _this.resources = data.map((v) => {
                    return {
                        'id'         : v.id,
                        'abbr'       : v.abbr,
                        'school_code': v.school_code,
                        'thum'       : _.isEmpty(v.thumbnail) ? null : `${this.$store.state.Path.district}${v.id}/${v.thumbnail}?${_.random(0, 9)}`,
                        'status'     : (v.status === 1) ? {text: this.$t('enable'), value: 1} : {text: this.$t('disable'), value: 0},
                        'name'       : v.name,
                        'description': v.description,
                        'group_ids'  : v.group_ids
                    }
                });
                _this.$store.dispatch("updateLoading", false);
            });

            _this.defaultColumns();

        },
        // 預設欄位
        defaultColumns() {
            const header = [
                {text: `${this.$t('district_code')}`, value: 'abbr', align: 'left', sortable: false},
                {text: `${this.$t('school_code')}`, value: 'school_code', sortable: false},
                {text: `${this.$t('name')}`, value: 'name'},
                {text: `${this.$t('description')}`, value: 'description'},
                {text: `${this.$t('status')}`, value: 'status.text'},
                {text: `${this.$t('thumbnail')}`, value: 'thumbnail', sortable: false},
                {text: `${this.$t('operation')}`, value: 'action', sortable: false}
            ];


            const status = [
                {text: this.$t('enable'), value: 1},
                {text: this.$t('disable'), value: 0}
            ];
            const langs = [
                {text: this.$t('tw'), value: 'tw'},
                {text: this.$t('cn'), value: 'cn'},
                {text: this.$t('en'), value: 'en'}
            ];

            this.langs = langs;
            this.headers = header;
            this.status = status;
        },


        editItem(item) {
            this.editedIndex = this.resources.indexOf(item);
            this.editedItem = Object.assign({}, item);
            this.dialog = true
        },
        // deleteItem(item) {
        //   const index = this.resources.indexOf(item);
        //   confirm('Are you sure you want to delete this item?') && this.resources.splice(index, 1)
        // },
        close() {
            this.dialog = false;
            setTimeout(() => {
                this.editedItem = Object.assign({}, this.defaultItem);
                this.errors = {};
                this.editedIndex = -1
            }, 300)
        },

        save() {
            let _this = this;
            let formData = new FormData();
            _this.$store.dispatch("updateLoading", true);
            if (this.editedIndex > -1) {
                let url = `/api/district/${_this.editedItem.id}`;
                // 轉換格式
                const obj = {
                    status : _this.editedItem.status.value,
                    _method: 'put'
                };
                let config = {
                    headers: {
                        'content-Type': 'multipart/form-data'
                    }
                };
                // 合併格式
                let data = Object.assign({}, this.editedItem, obj);
                if (_this.editedItem.thumbnail) {
                    // console.log('有圖片');
                    formData.append('thumbnail', _this.editedItem.thumbnail[0]);
                }
                // 更新

                console.log('editor')
                axios.post(url, formData, {params: data}, {config}).then((response) => {
                    _this.initialize()
                    _this.$store.dispatch("updateLoading", false);
                });
                Object.assign(this.resources[this.editedIndex], this.editedItem);
                return this.close()
            } else {
                let url = `/api/district`;
                // 轉換格式
                const obj = {
                    status: _this.editedItem.status.value,
                    lang  : _this.editedItem.lang.value
                };
                let config = {
                    headers: {
                        'content-Type': 'multipart/form-data'
                    }
                };
                // 合併格式
                let data = Object.assign({}, this.editedItem, obj);
                if (_this.editedItem.thumbnail) {
                    formData.append('thumbnail', _this.editedItem.thumbnail[0]);
                }
                console.log('create')
                axios.post(url, formData, {params: data}, {config}).then((response) => {
                    if (response.status !== 200) {
                        return
                    }
                    _this.initialize();
                    this.resources.push(this.editedItem);
                    _this.$store.dispatch("updateLoading", false);
                    return this.close()
                }).catch((error) => {
                    _this.errors = error.response.data.errors;
                    console.log(error.response.data.errors)
                })
            }
        }
    },
};
</script>

<style scoped>

</style>
