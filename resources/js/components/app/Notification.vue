<template>
    <v-card>
        <v-data-table
            :headers="headers"
            :items="resources"
            sort-by="calories"
            class="elevation-1"
            hide-default-footer
            :loading="this.$store.state.Status.isLoading"
            loading-text="Loading... Please wait"
        >
            <template v-slot:top>
                <v-toolbar
                    flat
                >
                    <v-toolbar-title>{{ $t('notification.admin') }}</v-toolbar-title>
                    <v-divider
                        class="mx-4"
                        inset
                        vertical
                    ></v-divider>
                    <v-spacer></v-spacer>
                    <v-dialog
                        v-model="dialog"
                        max-width="500px"
                    >
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                color="primary"
                                dark
                                class="mb-2"
                                v-bind="attrs"
                                v-on="on"
                            >
                                {{ $t('notification.create') }}
                            </v-btn>
                        </template>
                        <v-card>
                            <v-card-title>
                                <span class="text-h5">{{ formTitle }}</span>
                            </v-card-title>

                            <v-card-text>
                                <v-container>
                                    <v-row>
                                        <v-col
                                            cols="12"
                                            sm="12"
                                            md="12"
                                        >
                                            <v-select
                                                :items="types"
                                                item-text="text"
                                                item-value="value"
                                                v-model="editedItem.type"
                                                :label="$t('notification.type')"
                                            ></v-select>
                                        </v-col>
                                        <v-col
                                            cols="12"
                                            sm="12"
                                            md="12"
                                        >
                                            <v-text-field
                                                v-model="editedItem.content.title"
                                                :label="$t('notification.title')"
                                            ></v-text-field>
                                        </v-col>
                                        <v-col
                                            cols="12"
                                            sm="12"
                                            md="12"
                                        >
                                            <v-textarea
                                                clear-icon="mdi-close-circle"
                                                :label="$t('notification.content')"
                                                :value="defaultItem.content"
                                                v-model="editedItem.content.content"
                                                required
                                            ></v-textarea>
                                        </v-col>
                                        <v-col
                                            cols="12"
                                            sm="12"
                                            md="12"
                                        >
                                            <v-text-field
                                                v-model="editedItem.content.url"
                                                label="url">
                                            </v-text-field>
                                        </v-col>
                                        <v-col
                                            cols="12"
                                            sm="12"
                                            md="12"
                                        >
                                            <v-menu
                                                ref="menu"
                                                v-model="menu"
                                                :close-on-content-click="false"
                                                transition="scale-transition"
                                                offset-y
                                                max-width="290px"
                                                min-width="auto"
                                            >
                                                <template v-slot:activator="{ on, attrs }">
                                                    <v-text-field
                                                        v-model="editedItem.validity"
                                                        :label="$t('notification.validity')"
                                                        hint="MM/DD/YYYY"
                                                        persistent-hint
                                                        prepend-icon="mdi-calendar"
                                                        v-bind="attrs"
                                                        @blur="date = parseDate(dateFormatted)"
                                                        v-on="on"
                                                    ></v-text-field>
                                                </template>
                                                <v-date-picker
                                                    v-model="editedItem.validity"
                                                    no-title
                                                    @input="menu = false"
                                                ></v-date-picker>
                                            </v-menu>
                                        </v-col>
                                    </v-row>
                                </v-container>
                            </v-card-text>

                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn
                                    color="blue darken-1"
                                    text
                                    @click="close"
                                >
                                    {{ $t('model.cancel') }}
                                </v-btn>
                                <v-btn
                                    color="blue darken-1"
                                    text
                                    @click="save"
                                >
                                    {{ $t('model.submit') }}
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                    <v-dialog v-model="dialogDelete" max-width="500px">
                        <v-card>
                            <v-card-title class="text-h5">{{ $t('delete.confirm') }}</v-card-title>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn color="blue darken-1" text @click="closeDelete">{{ $t('model.cancel') }}</v-btn>
                                <v-btn color="blue darken-1" text @click="deleteItemConfirm">{{ $t('model.submit') }}</v-btn>
                                <v-spacer></v-spacer>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                </v-toolbar>
            </template>
            <template v-slot:item.type="{ item }">
                {{ getType(item.type) }}
            </template>
            <template v-slot:item.actions="{ item }">
                <v-icon
                    small
                    class="mr-2"
                    @click="editItem(item)"
                >
                    mdi-pencil
                </v-icon>
                <v-icon
                    small
                    @click="deleteItem(item)"
                >
                    mdi-delete
                </v-icon>
            </template>
            <template v-slot:no-data>
                <v-btn
                    color="primary"
                    @click="initialize"
                >
                    Reset
                </v-btn>
            </template>
        </v-data-table>
    </v-card>
</template>

<script>
export default {
    name: "Notification",
    data(vm) {
        return {
            dialog      : false,
            dialogDelete: false,

            headers      : [
                {text: this.$t('notification.title'), align: 'start', sortable: false, value: 'content.title',},
                {text: this.$t('notification.content'), value: 'content.content'},
                // {text: this.$t('notification.status'), value: 'status'},
                {text: this.$t('notification.type'), value: 'type'},
                {text: this.$t('notification.validity'), value: 'validity'},
                {text: this.$t('operation'), value: 'actions', sortable: false},
            ],
            types        : [
                {text: this.$t('notification.announce'), value: 1},
                // {text: this.$t('notification.license'), value: 4},
                {text: this.$t('notification.join'), value: 5},
                {text: this.$t('notification.reviewer'), value: 3},
            ],
            resources    : [],
            editedIndex  : -1,
            editedItem   : {
                id      : null,
                status  : null,
                validity: null,
                group_id: null,
                content : {
                    title        : null,
                    content      : null,
                    channel_id   : null,
                    channel_ids  : null,
                    district_ids : null,
                    top          : false,
                    url          : null,
                    link         : null,
                    isOperating  : false,
                    isReview     : false,
                    teamModel_ids: null,
                },
            },
            defaultItem  : {
                status  : 2,
                validity: new Date().toISOString().substr(0, 10),
                group_id: this.$store.state.Auth.user.group_id,
                id      : null,
                content : {
                    title        : null,
                    content      : null,
                    channel_id   : this.$store.state.Auth.user.channel_id,
                    channel_ids  : null,
                    district_ids : null,
                    top          : false,
                    url          : null,
                    link         : '/content-review/' + this.$store.state.Auth.user.channel_id,
                    isOperating  : false,
                    isReview     : false,
                    teamModel_ids: null,
                },
            },
            date         : new Date().toISOString().substr(0, 10),
            dateFormatted: vm.formatDate(new Date().toISOString().substr(0, 10)),
            menu         : false,
        }
    },

    computed: {
        formTitle() {
            return this.editedIndex === -1 ? this.$t('notification.create') : this.$t('notification.edit')
        },
    },

    watch: {
        dialog(val) {
            val || this.close()
        },
        dialogDelete(val) {
            val || this.closeDelete()
        },
    },

    created() {
        this.initialize()
    },


    methods: {
        initialize() {
            this.getMessageContent();
        },

        editItem(item) {
            this.editedIndex = this.resources.indexOf(item)
            this.editedItem = Object.assign({}, item)
            this.dialog = true
        },

        deleteItem(item) {
            this.editedIndex = this.resources.indexOf(item)
            this.editedItem = Object.assign({}, item)
            this.dialogDelete = true
        },

        deleteItemConfirm() {
            let _this = this
            let url = `/api/group/notification/`;
            this.$store.dispatch("updateLoading", true);
            // delete
            axios.delete(url + _this.editedItem.id).then((response) => {
                if (response.status === 201) {
                    this.initialize();
                    this.resources.splice(this.editedIndex, 1)
                    this.closeDelete()
                    this.$store.dispatch("updateLoading", false);
                }
            }).catch((e) => {
                console.log(e.message);
            })

        },

        close() {
            this.dialog = false
            this.$nextTick(() => {
                this.editedItem = Object.assign({}, this.defaultItem)
                this.editedIndex = -1
            })
        },

        closeDelete() {
            this.dialogDelete = false
            this.$nextTick(() => {
                this.editedItem = Object.assign({}, this.defaultItem)
                this.editedIndex = -1
            })
        },

        save() {
            let _this = this
            let url = `/api/group/notification/`;
            this.$store.dispatch("updateLoading", true);
            // 依照 type 給預設值
            if (_this.editedItem.type === 3) {
                _this.editedItem.content.link = '/content-review/' + this.$store.state.Auth.user.channel_id
                _this.editedItem.content.isOperating = true
                _this.editedItem.content.isReview = true
                _this.editedItem.content.top = false
            }
            // 預設資料
            _this.editedItem.content.channel_id = _this.defaultItem.content.channel_id
            _this.editedItem.status = _this.defaultItem.status
            _this.editedItem.group_id = _this.defaultItem.group_id
            // edit
            if (this.editedIndex > -1) {
                // 格式轉換
                const obj = {
                    content: JSON.stringify(_this.editedItem.content)
                };
                // // 合併修改
                const data = Object.assign({}, _this.editedItem, obj);

                axios.put(url + _this.editedItem.id, data).then((response) => {
                    if (response.status === 201) {
                        this.initialize();
                        this.$store.dispatch("updateLoading", false);
                        // Object.assign(this.resources[this.editedIndex], this.editedItem)
                    }
                }).catch((e) => {
                    console.log(e.message);
                })

            } else {
                // 格式轉換
                const obj = {
                    content   : JSON.stringify(_this.editedItem.content),
                    channel_id: this.$store.state.Auth.user.channel_id
                };
                // 合併修改
                const data = Object.assign({}, _this.editedItem, obj);
                // create
                axios.post(url, data).then((response) => {
                    if (response.status === 200) {
                        this.initialize();
                        this.$store.dispatch("updateLoading", false);
                        // this.resources.push(this.editedItem)
                    }
                }).catch((e) => {
                    console.log(e.message);
                })
            }

            this.close()
        },
        formatDate(date) {
            if (!date) return null

            const [year, month, day] = date.split('-')
            return `${month}/${day}/${year}`
        },
        parseDate(date) {
            if (!date) return null

            const [month, day, year] = date.split('/')
            return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`
        },
        // 轉換 type 名稱
        getType(type) {
            switch (type) {
                case 1:
                    return this.$t('notification.announce');
                case 5:
                    return this.$t('notification.join')
                case 3:
                    return this.$t('notification.reviewer')
                default:
                    return this.$t('notification.license')
            }
        },

        // 通知內容
        getMessageContent() {
            let _this = this;
            let url = `/api/group/${this.$store.state.Auth.user.group_id}`
            axios.get(url).then((response) => {
                    if (response.status === 200) {
                        let data = response.data.notifications
                        _this.resources = data.map(v => {
                            return {
                                content : JSON.parse(v.content),
                                status  : v.status,
                                type    : v.type,
                                validity: v.validity,
                                group_id: v.group_id,
                                id      : v.id
                            }
                        });
                    }
                }
            ).catch((e) => {
                // console.log(e.message);
            });
        }
    },
}
</script>

<style scoped>

</style>
