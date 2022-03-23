<template>
    <v-data-table
        :headers="headers"
        :items="result"
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
                        <!--todo 有管理者權限才可以新增-->
                        <!--            <v-btn color="primary" dark class="mb-2" v-on="on">+</v-btn>-->
                    </template>
                    <v-card>
                        <v-card-title>
                            <span class="headline">{{ formTitle }}</span>
                        </v-card-title>

                        <v-card-text>
                            <v-container>
                                <v-row>
                                    <v-col cols="12" sm="6" md="4">
                                        <v-text-field v-model="editedItem.school_code" :rules="school_codeRules" :label="$t('school_code')" disabled/>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="4">
                                        <v-text-field v-model="editedItem.name" :rules="nameRules" :label="$t('channel_name')"/>
                                    </v-col>
                                    <v-col cols="12" sm="12" md="12">
                                        <v-textarea v-model="editedItem.description" :label="$t('description')"/>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="4">
                                        <v-select :items="status" item-text="text" item-value="value" :label="$t('group.notify_status')" v-model="editedItem.notify_status" return-object required/>
                                    </v-col>
                                    <!--                                    <v-col cols="12" sm="6" md="4">-->
                                    <!--                                        <v-select :items="access" item-text="text" item-value="value" :label="$t('access')" v-model="editedItem.public" return-object required/>-->
                                    <!--                                    </v-col>-->
                                    <v-col cols="12" sm="6" md="4">
                                        <v-select :items="status" item-text="text" item-value="value" :label="$t('channel_review')" v-model="editedItem.review_status" return-object required/>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="4">
                                        <v-select :items="status" item-text="text" item-value="value" :label="$t('general_member_review')" v-model="editedItem.public_note_status" return-object required/>
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
            <v-img src="
            /images/app/tw/teammodel/original-black-small.png" style="width: 50px; height: 50px" v-else/>
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
        <template v-slot:item.notify_status="{ item }">
            {{ item.notify_status.text }}
        </template>
        <template v-slot:item.review_status="{ item }">
            {{ item.review_status.text }}
        </template>
        <template v-slot:item.public_note_status="{ item }">
            {{ item.public_note_status.text }}
        </template>
    </v-data-table>
</template>

<script>
import _ from "lodash";

export default {
    data: () => ({
        dialog          : false,
        headers         : [],
        status          : [],
        access          : [],
        result          : [],
        editedIndex     : -1,
        editedItem      : {
            school_code  : null,
            name         : null,
            description  : null,
            notify_status: null,
            // public: 0,
            thumbnail         : null,
            groupId           : null,
            review_status     : null,
            public_note_status: null,
        },
        defaultItem     : {
            school_code  : null,
            name         : null,
            description  : null,
            notify_status: null,
            // public: 0,
            thumbnail         : null,
            groupId           : null,
            review_status     : null,
            public_note_status: null,
        },
        nameRules       : [
            v => !!v || 'Name is required',
            v => (v && v.length <= 30) || 'Name must be less than 30 characters'
        ],
        school_codeRules: [
            v => !!v || 'school_code is required',
            v => (v && v.length <= 30) || 'Name must be less than 30 characters'
        ]
    }),

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
            let groupId = this.$store.state.Auth.user.group_id;

            let _this = this;
            let url = `/api/group/${groupId}`;
            _this.$store.dispatch("updateLoading", true);

            axios.get(url).then((response) => {
                let data = response.data;
                //thum 顯示圖片用 沒有任何作用
                const CustomData = {
                    school_code       : data.school_code,
                    name              : data.name,
                    description       : data.description,
                    thum              : _.isEmpty(data.thumbnail) ? null : `${this.$store.state.Path.group}${data.id}/${data.thumbnail}?${_.random(0, 9)}`,
                    groupId           : data.id,
                    notify_status     : _.find(_this.status, v => v.value === data.notify_status),
                    review_status     : _.find(_this.status, v => v.value === data.review_status),
                    public_note_status: _.find(_this.status, v => v.value === data.public_note_status)


                };
                _this.result = [CustomData];
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
                {text: `${this.$t('group.notify_status')}`, value: 'notify_status'},
                // {text: `${this.$t('access')}`, value: 'public'},
                {text: `${this.$t('channel_review')}`, value: 'review_status'},
                {text: `${this.$t('general_member_review')}`, value: 'public_note_status'},
                {text: `${this.$t('thumbnail')}`, value: 'thumbnail', sortable: false},
                {text: `${this.$t('operation')}`, value: 'action', sortable: false}
            ];

            const status = [
                {text: this.$t('enable'), value: 1},
                {text: this.$t('disable'), value: 0}
            ];
            const access = [
                {text: this.$t('open'), value: 1},
                {text: this.$t('private'), value: 0}
            ];
            this.headers = header;
            this.status = status;
            this.access = access;
        },


        editItem(item) {
            this.editedIndex = this.result.indexOf(item);
            this.editedItem = Object.assign({}, item);
            this.dialog = true
        },
        // deleteItem(item) {
        //   const index = this.result.indexOf(item);
        //   confirm('Are you sure you want to delete this item?') && this.result.splice(index, 1)
        // },
        close() {
            this.dialog = false;
            setTimeout(() => {
                this.editedItem = Object.assign({}, this.defaultItem);
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
                    // public: _this.editedItem.public.value,
                    notify_status     : _this.editedItem.notify_status.value,
                    review_status     : _this.editedItem.review_status.value,
                    public_note_status: _this.editedItem.public_note_status.value
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
                    Object.assign(this.result[this.editedIndex], this.editedItem);
                    return this.close();
                }
                // console.log('沒圖片');
                // 純更新內容
                axios.post(url, formData, {params: data}, {config}).then((response) => {
                    // console.log(response)
                    _this.initialize()
                });
                // Object.assign(this.result[this.editedIndex], this.editedItem);
                return this.close()
            } else {
                let _this = this;
                let formData = new FormData();
                let url = `/api/group`;
                // 轉換格式
                const obj = {
                    // public: _this.editedItem.public.value,
                    // status: _this.editedItem.status.value,
                    notify_status     : _this.editedItem.notify_status.value,
                    review_status     : _this.editedItem.review_status.value,
                    public_note_status: _this.editedItem.public_note_status.value
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
                        this.result.push(this.editedItem);
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
                    this.result.push(this.editedItem);
                    return this.close()
                }).catch((error) => {
                    console.log(error)
                })
            }
        }
    },
}

</script>

<style scoped>

</style>
