<template>
    <v-card>
        <v-data-table
            :headers="headers"
            :items="resources.group_subject"
            class="elevation-1"
            :loading="this.$store.state.Status.isLoading"
            loading-text="Loading... Please wait"
        >
            <template v-slot:top>
                <v-toolbar flat color="white">
                    <v-toolbar-title>{{ $t('classification_editor') }}</v-toolbar-title>
                    <v-divider
                        class="mx-4"
                        inset
                        vertical
                    ></v-divider>
                    <v-spacer></v-spacer>
                    <v-dialog v-model="dialog" max-width="500px">
                        <v-card>
                            <v-card-title>
                                <span class="headline">{{ formTitle }}</span>
                            </v-card-title>

                            <v-card-text>
                                <v-container>
                                    <v-row>
                                        <v-col cols="12" sm="6" md="4">
                                            <v-text-field v-model="editedItem.alias" :label="$t('alias')" :error-messages="error.alias" disabled></v-text-field>
                                        </v-col>
                                        <v-col cols="12" sm="6" md="4">
                                            <v-select :items="resources.district_subject" item-text="text" item-value="editedItem" :error-messages="error.district_subject" :label="$t('district_subject')" v-model="editedItem.subject" return-object required/>
                                        </v-col>

                                    </v-row>
                                </v-container>
                            </v-card-text>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn color="blue darken-1" text @click="close">{{ $t('cancel') }}</v-btn>
                                <v-btn color="blue darken-1" text @click="save">{{ $t('submit') }}</v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                </v-toolbar>
            </template>
            <template v-slot:no-data>
                <v-btn color="primary" @click="initialize">Reset</v-btn>
            </template>
            <template v-slot:body="props">
                <tbody>
                <tr
                    v-for="(item, index) in props.items"
                    :key="index"
                >
                    <td>{{ index + 1 }}</td>
                    <td>{{ item.name }}</td>
                    <td>{{ item.alias }}</td>
                    <td>
                        <div v-if="item.subject !== 0">
                            {{ item.subject.text }}
                        </div>
                        <div v-else>
                            <v-chip
                                color="red accent-2"
                                dark
                                small
                            >
                                {{ $t('unspecified') }}
                            </v-chip>
                            <!--                        <v-icon>mdi-close-circle-outline</v-icon>-->
                        </div>
                    </td>
                    <td>
                        <v-icon
                            small
                            class="mr-2"
                            @click="editItem(item)"
                        >
                            mdi-pencil
                        </v-icon>
                        <!--                    <v-icon-->
                        <!--                        small-->
                        <!--                        v-if="item.alias === null"-->
                        <!--                        @click="deleteItem(item)"-->
                        <!--                    >-->
                        <!--                        mdi-delete-->
                        <!--                    </v-icon>-->
                    </td>
                </tr>
                </tbody>
            </template>
        </v-data-table>
    </v-card>
</template>

<script>
import draggable from 'vuedraggable'

export default {
    components: {
        draggable,
    },
    subject   : "classification",
    data      : () => ({
        dialog      : false,
        headers     : [],
        error       : [],
        resources   : {
            group_subject   : [],
            district_subject: []
        },
        subject_list: [],
        editedIndex : -1,
        editedItem  : {
            id     : null,
            order  : null,
            subject: {
                type   : Object,
                default: {},
            },
            alias  : {
                type   : Array,
                default: [],
            },
        },
        defaultItem : {
            id     : null,
            order  : null,
            subject: {
                type   : Object,
                default: {},
            },
            alias  : {
                type   : Array,
                default: [],
            }
        },
    }),

    computed: {
        formTitle() {
            return this.editedIndex === -1 ? this.$t('subject_create') : this.$t('subject_editor')
        },
    },

    watch: {
        dialog(val) {
            val || this.close()
        },
    },

    created() {
        this.initialize()
        this.defaultColumns();
    },

    methods: {
        initialize() {
            let _this = this;
            let url = `/api/district/classification/${this.$store.state.Auth.user.district_id}`;
            _this.$store.dispatch("updateLoading", true);

            axios.get(url).then((resource) => {
                if (resource.status !== 200) return;
                let result = resource.data;
                _this.resources.group_subject = result.group_subject;
                _this.resources.district_subject = result.district_subject;

                _this.$store.dispatch("updateLoading", false);
            });

        },

        defaultColumns() {
            let _this = this;
            const header = [
                // {text: '#', value: 'type', sortable: false},
                {text: this.$t('rating_order'), value: 'id', sortable: false},
                {text: this.$t('channel_name'), align: 'start', sortable: true, value: 'name',},
                {text: this.$t('alias'), align: 'start', sortable: true, value: 'alias',},
                {text: this.$t('district_subject'), align: 'start', sortable: true, value: 'subject',},
                {text: this.$t('operation'), value: 'actions', sortable: false},
            ];

            _this.headers = header;
        },

        editItem(item) {
            this.error = [];
            this.editedIndex = this.resources.group_subject.indexOf(item)
            this.editedItem = Object.assign({}, item)
            this.dialog = true
        },

        deleteItem(item) {
            let _this = this;
            let url = `/api/district/classification/${item.id}`
            if (confirm('Are you sure you want to delete this item?')) {
                axios.delete(url).then((response => {
                    if (response.status !== 204) return;
                    _this.initialize();
                })).catch(error => {
                    _this.error = error.response.data.errors;
                    if (error.response.data.status_code === 422) _this.dialog = true;
                });
            }
        },

        close() {
            this.dialog = false
            this.$nextTick(() => {
                this.editedItem = Object.assign({}, this.defaultItem)
                this.editedIndex = -1
            })
        },

        save() {
            let _this = this;
            let url = `/api/district/classification/${this.editedItem.id}`

            _this.$store.dispatch("updateLoading", true);

            // 這裡傳得值是 subject_fields_id 轉換過得參數
            let params = {
                subject: _this.editedItem.subject.value
            }
            if (this.editedIndex > -1) {
                axios.put(url, params).then((response => {
                    if (response.status !== 204) return;
                    _this.initialize();

                })).catch(error => {
                    _this.error = error.response.data.errors;
                    if (error.response.data.status_code === 422) _this.dialog = true;

                });
                _this.$store.dispatch("updateLoading", false);
            }

            // let url = '/api/district/classification/'
            // let districtId = this.$store.state.Auth.user.district_id;
            // let params = {
            //     districts_id: districtId,
            //     subject: _this.editedItem.subject
            // }
            // _this.dialog = true;
            // axios.post(url, params)
            //     .then(response => {
            //         if (response.status === 201) {
            //             this.initialize();
            //         }
            //     })
            //     .catch(error => {
            //         _this.error = error.response.data.errors;
            //         if (error.response.data.status_code === 422) {
            //             _this.dialog = true;
            //         }
            //     })
            // _this.$store.dispatch("updateLoading", false);
            this.close()
        },

        // sort(e) {
        //     let _this = this;
        //     let districtId = this.$store.state.Auth.user.district_id;
        //     let url = `/api/district/classification/sort/${districtId}`
        //     let params = {
        //         newIndex: _this.resources[e.moved.newIndex],
        //         oldIndex: _this.resources[e.moved.oldIndex],
        //     }
        //     axios.put(url, params)
        //         .then(response => {
        //             if (response.status === 200) {
        //                 _this.initialize();
        //             }
        //         })
        //         .catch(error => {
        //             _this.error = error.response.data.errors;
        //             if (error.response.data.status_code === 422) {
        //                 _this.dialog = true;
        //             }
        //         });
        // },
    },
}
</script>


<style scoped>

</style>
