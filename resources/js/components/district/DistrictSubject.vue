<template>
    <v-card>
        <v-data-table
            :headers="headers"
            :items="resources"
            class="elevation-1"
            :loading="this.$store.state.Status.isLoading"
            loading-text="Loading... Please wait"
        >
            <template v-slot:top>
                <v-toolbar flat color="white">
                    <v-toolbar-title>{{ $t('subject_editor') }}</v-toolbar-title>
                    <v-divider
                        class="mx-4"
                        inset
                        vertical
                    ></v-divider>
                    <v-spacer></v-spacer>
                    <v-dialog v-model="dialog" max-width="500px">
                        <template v-slot:activator="{ on }">
                            <v-btn color="primary" dark class="mb-2" v-on="on"> +</v-btn>
                        </template>
                        <v-card>
                            <v-card-title>
                                <span class="headline">{{ formTitle }}</span>
                            </v-card-title>

                            <v-card-text>
                                <v-container>
                                    <v-row>
                                        <v-col cols="12" sm="12" md="12">
                                            <v-text-field v-model="editedItem.subject" :label="$t('district_subject')" :error-messages="error.subject"></v-text-field>
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
            <template v-slot:body="props">
                <draggable
                    :list="props.items"
                    @change="sort"
                    tag="tbody"
                >
                    <tr
                        v-for="(item, index) in props.items"
                        :key="index"
                    >
                        <td>
                            <v-icon
                                small
                                class="page__grab-icon"
                            >
                                mdi-arrow-all
                            </v-icon>
                        </td>
                        <td>{{ item.order }}</td>
                        <td>{{ item.subject }}</td>
                        <td>
                            <div v-for="(v,k) in item.alias">
                                {{ v.text }} <span style="color:#BDBDBD"> {{ v.name }}</span>
                            </div>
                        </td>
                        <td>{{ item.total }}</td>
                        <td>
                            <v-icon
                                small
                                class="mr-2"
                                @click="editItem(item)"
                            >
                                mdi-pencil
                            </v-icon>
                            <v-icon
                                small
                                v-if="item.alias.length <= 0"
                                @click="deleteItem(item)"
                            >
                                mdi-delete
                            </v-icon>
                        </td>
                    </tr>
                </draggable>
            </template>
            <template v-slot:no-data>
                <v-btn color="primary" @click="initialize">Reset</v-btn>
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
    subject   : "districtSubject",
    data      : () => ({
        dialog      : false,
        headers     : [],
        error       : [],
        maxOrder    : 1,
        resources   : [{
            id     : null,
            order  : null,
            subject: null,
            total  : null,
            alias  : {
                type   : Array,
                default: [],
            }
        }],
        subject_list: [],
        editedIndex : -1,
        editedItem  : {
            id     : null,
            order  : null,
            subject: null,
            alias  : {
                type   : Array,
                default: [],
            }
        },
        defaultItem : {
            id     : null,
            order  : null,
            subject: null,
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
            let url = `/api/district/subjects/${this.$store.state.Auth.user.district_id}`;
            _this.$store.dispatch("updateLoading", true);

            axios.get(url).then((resource) => {
                if (resource.status !== 200) return;
                let result = resource.data;
                _this.resources = result;
                _this.maxOrder = _.maxBy(result, 'order').order;

                _this.$store.dispatch("updateLoading", false);
            });

        },

        defaultColumns() {
            let _this = this;
            const header = [
                {text: '#', value: 'type', sortable: false},
                {text: this.$t('rating_order'), value: 'type', sortable: false},
                {text: this.$t('district_subject'), align: 'start', sortable: true, value: 'subject',},
                {text: this.$t('alias'), align: 'start', sortable: false, value: 'alias.value',},
                {text: this.$t('using'), align: 'start', sortable: true, value: 'total',},
                {text: this.$t('operation'), value: 'actions', sortable: false},
            ];

            _this.headers = header;
        },

        editItem(item) {
            this.error = [];
            this.editedIndex = this.resources.indexOf(item)
            this.editedItem = Object.assign({}, item)
            this.dialog = true
        },

        deleteItem(item) {
            let url = `/api/district/subjects/${item.id}`
            let _this = this;
            if (confirm('Are you sure you want to delete this item?')) {
                axios.delete(url).then((response => {
                    if (response.status === 204) {
                        this.initialize();
                    }
                })).catch(error => {
                    _this.error = error.response.data.errors;
                    if (error.response.data.status_code === 422) {
                        _this.dialog = true;
                    }
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
            let url = `/api/district/subjects/${this.editedItem.id}`
            let districtId = this.$store.state.Auth.user.district_id;
            _this.$store.dispatch("updateLoading", true);

            // 這裡傳得值是 subject_fields_id 轉換過得參數
            let params = {
                subject     : _this.editedItem.subject.replace(/\ +/g, ""),
                order       : _this.editedIndex === -1 ? _this.maxOrder + 1 : parseInt(_this.editedItem.order),
                districts_id: districtId,
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
            } else {
                url = '/api/district/subjects/'
                _this.dialog = true;
                axios.post(url, params)
                    .then(response => {
                        if (response.status !== 204) return;
                        _this.initialize();
                    })
                    .catch(error => {
                        _this.error = error.response.data.errors;
                        if (error.response.data.status_code === 422) _this.dialog = true;
                    })
                _this.$store.dispatch("updateLoading", false);
            }
            this.close()
        },

        sort(e) {
            let _this = this;
            let url = `/api/district/subjects/sort/${e.moved.element.id}`
            let params = {
                order: e.moved.newIndex + 1,
            }
            axios.put(url, params)
                .then(response => {
                    if (response.status !== 204) return;
                    _this.initialize();
                })
                .catch(error => {
                    _this.error = error.response.data.errors;
                    if (error.response.data.status_code === 422) _this.dialog = true;
                });
        },
    },
}
</script>


<style scoped>

</style>
