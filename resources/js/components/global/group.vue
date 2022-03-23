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
                    <v-toolbar-title>{{ $t('channel_manager') }}</v-toolbar-title>
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
                                            <v-text-field v-model="editedItem.school_code" :error-messages="errors.school_code" :label="$t('school_code')" v-if="editedIndex!==-1" disabled/>
                                            <v-text-field v-model="editedItem.school_code" :error-messages="errors.school_code" :label="$t('school_code')" v-else/>
                                        </v-col>
                                        <v-col cols="12" sm="6" md="4">
                                            <v-text-field v-model="editedItem.name" :error-messages="errors.name" :label="$t('channel_name')"/>
                                        </v-col>
                                        <v-col cols="12" sm="6" md="4">
                                            <v-text-field v-model="editedItem.abbr" :error-messages="errors.abbr" :label="$t('abbr')"/>
                                        </v-col>
                                        <v-col cols="12" sm="12" md="12">
                                            <v-textarea v-model="editedItem.description" :label="$t('description')"/>
                                        </v-col>
                                        <!--                                    <v-col cols="12" sm="6" md="4">-->
                                        <!--                                        <v-select :items="status" item-text="text" item-value="value" :label="$t('status')" v-model="editedItem.status" return-object required/>-->
                                        <!--                                    </v-col>-->
                                        <!--                                    <v-col cols="12" sm="6" md="4">-->
                                        <!--                                        <v-select :items="access" item-text="text" item-value="value" :label="$t('access')" v-model="editedItem.public" return-object required/>-->
                                        <!--                                    </v-col>-->
                                        <v-col cols="12" sm="6" md="4">
                                            <v-select :items="country" item-text="text" item-value="value" :label="$t('country_code')" v-model="editedItem.country_code" return-object required/>
                                        </v-col>
                                        <v-col cols="12" sm="6" md="4">
                                            <v-select :items="eventType" item-text="text" item-value="value" :label="$t('event.eventType')" v-model="editedItem.public" return-object required/>
                                        </v-col>
                                        <v-col cols="12" sm="6" md="4">
                                            <v-select :items="status" item-text="text" item-value="value" :label="$t('school_upload')" v-model="editedItem.school_upload_status" return-object required/>
                                        </v-col>
                                        <v-col cols="12" sm="6" md="4">
                                            <v-select :items="status" item-text="text" item-value="value" :label="$t('channel_review')" v-model="editedItem.review_status" return-object required/>
                                        </v-col>
                                        <v-col cols="12" sm="6" md="4">
                                            <v-select :items="status" item-text="text" item-value="value" :label="$t('general_member_review')" v-model="editedItem.public_note_status" return-object required/>
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
                <v-img src="/images/app/tw/teammodel/original-black-small.png" style="width: 50px; height: 50px" v-else/>
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
            <template v-slot:item.review_status="{ item }">
                {{ item.review_status.text }}
            </template>
            <template v-slot:item.public_note_status="{ item }">
                {{ item.public_note_status.text }}
            </template>
        </v-data-table>
    </v-card>
</template>

<script>
export default {
    data() {
        return {
            dialog          : false,
            search          : '',
            headers         : [],
            status          : [],
            access          : [],
            eventType       : [],
            country         : [],
            resources       : [],
            langs           : [],
            editedIndex     : -1,
            editedItem      : {
                school_code         : null,
                name                : null,
                description         : null,
                abbr                : null,
                country_code        : null,
                school_upload_status: null,
                public              : null,
                thumbnail           : null,
                groupId             : null,
                review_status       : null,
                public_note_status  : null,
            },
            defaultItem     : {
                school_code         : null,
                name                : null,
                description         : null,
                abbr                : null,
                country_code        : 886,
                school_upload_status: {text: this.$t('disable'), value: 0},
                public              : {text: this.$t('general'), value: 0},
                thumbnail           : null,
                lang                : {text: this.$t('tw'), value: 'tw'},
                groupId             : null,
                review_status       : {text: this.$t('disable'), value: 0},
                public_note_status  : {text: this.$t('disable'), value: 0},
            },
            errors          : {},
            nameRules       : [
                v => !!v || 'Name is required',
                v => (v && v.length <= 30) || 'Name must be less than 30 characters'
            ],
            school_codeRules: [
                v => !!v || 'school_code is required',
                v => (v && v.length <= 30) || 'Name must be less than 30 characters'
            ]
        }
    },

    computed: {
        formTitle() {
            return this.editedIndex === -1 ? this.$t('create_group') : this.$t('editor_group')
        }
    },

    watch: {
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
            // let groupId = this.$store.state.Auth.user.group_id;

            let _this = this;
            let url = `/api/group`;
            _this.$store.dispatch("updateLoading", true);

            axios.get(url).then((response) => {
                let data = response.data;
                //thum 顯示圖片用 沒有任何作用
                _this.resources = data.map((v) => {
                    return {
                        'school_code'         : v.school_code,
                        'name'                : v.name,
                        'description'         : v.description,
                        'abbr'                : v.abbr,
                        'country_code'        : v.country_code,
                        'thum'                : _.isEmpty(v.thumbnail) ? null : `${this.$store.state.Path.group}${v.id}/${v.thumbnail}?${_.random(0, 9)}`,
                        'groupId'             : v.id,
                        'review_status'       : (v.review_status === '1') ? {text: this.$t('enable'), value: 1} : {text: this.$t('disable'), value: 0},
                        'public_note_status'  : (v.public_note_status === 1) ? {text: this.$t('enable'), value: 1} : {text: this.$t('disable'), value: 0},
                        'school_upload_status': (v.school_upload_status === 1) ? {text: this.$t('enable'), value: 1} : {text: this.$t('disable'), value: 0},
                        'public'              : (v.public === 2) ? {text: this.$t('public_welfare'), value: 2} : (v.public === 1) ? {text: this.$t('activity'), value: 1} : {text: this.$t('general'), value: 0},
                    }
                });
                _this.$store.dispatch("updateLoading", false);
            });

            _this.defaultColumns();

        },
        // 預設欄位
        defaultColumns() {
            const header = [
                {text: `${this.$t('school_code')}`, value: 'school_code', align: 'left', sortable: false},
                {text: `${this.$t('channel_name')}`, value: 'name', sortable: false},
                {text: `${this.$t('description')}`, value: 'description', sortable: false},
                // {text: `${this.$t('status')}`, value: 'status'},
                {text: `${this.$t('event.eventType')}`, value: 'public.text'},
                {text: `${this.$t('channel_review')}`, value: 'review_status'},
                {text: `${this.$t('general_member_review')}`, value: 'public_note_status'},
                {text: `${this.$t('school_upload')}`, value: 'school_upload_status.text'},
                {text: `${this.$t('thumbnail')}`, value: 'thumbnail', sortable: false},
                {text: `${this.$t('operation')}`, value: 'action', sortable: false}
            ];

            const eventType = [
                {text: this.$t('general'), value: 0},
                {text: this.$t('activity'), value: 1},
                {text: this.$t('public_welfare'), value: 2},
            ];

            const country = [
                {text: 886, value: 886},
                {text: 86, value: 86},
                {text: 1, value: 1},
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
            const access = [
                {text: this.$t('open'), value: 1},
                {text: this.$t('private'), value: 0}
            ];


            this.headers = header;
            this.status = status;
            this.langs = langs;
            this.access = access;
            this.eventType = eventType;
            this.country = country;
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
            if (this.editedIndex > -1) {
                let _this = this;
                let formData = new FormData();
                formData.append('_method', 'PUT')
                let url = `/api/group/${_this.editedItem.groupId}`;
                // 轉換格式
                const obj = {
                    public: _this.editedItem.public.value,
                    // status: _this.editedItem.status.value,
                    school_upload_status: _this.editedItem.school_upload_status.value,
                    review_status       : _this.editedItem.review_status.value,
                    public_note_status  : _this.editedItem.public_note_status.value,
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
                    axios.post(url, formData, {params: data}, {config}).then((response) => {
                        // console.log(response)
                        _this.initialize();
                        _this.$store.dispatch("updateLoading", false);
                    });
                    Object.assign(this.resources[this.editedIndex], this.editedItem);
                    return this.close();
                }
                // console.log('沒圖片');
                // 純更新內容
                axios.post(url, formData, {params: data}, {config}).then((response) => {
                    // console.log(response)
                    _this.initialize()
                });
                Object.assign(this.resources[this.editedIndex], this.editedItem);
                return this.close()
            } else {
                let _this = this;
                let formData = new FormData();
                let url = `/api/group`;
                // 轉換格式
                const obj = {
                    public: _this.editedItem.public.value,
                    // status: _this.editedItem.status.value,
                    school_upload_status: _this.editedItem.school_upload_status.value,
                    review_status       : _this.editedItem.review_status.value,
                    public_note_status  : _this.editedItem.public_note_status.value,
                    lang                : _this.editedItem.lang.value
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
                    axios.post(url, formData, {params: data}, {config}).then((response) => {
                        if (response.data.status !== 200) {
                            return
                        }
                        _this.initialize();
                        this.resources.push(this.editedItem);
                        return this.close()
                    }).catch((error) => {
                        console.log(error)
                    })
                }
                // 純更新內容
                axios.post(url, formData, {params: data}, {config}).then((response) => {
                    if (response.data.status !== 200) {
                        return
                    }
                    _this.initialize();
                    this.resources.push(this.editedItem);
                    return this.close()
                }).catch((error) => {
                    _this.errors = error.response.data.errors;
                    console.log(error.response.data.errors)
                })
            }
        }
    },
}

</script>

<style scoped>

</style>
