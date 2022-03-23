<template>
    <v-card>
        <v-data-table
            :headers="headers"
            :items="resources.recommendedVideos"
            sort-by="area"
            class="elevation-1"
            :loading="this.$store.state.Status.isLoading"
            loading-text="Loading... Please wait"
        >
            <template v-slot:top>
                <v-toolbar flat color="white">
                    <v-toolbar-title>{{ $t('global_recommended_video') }}</v-toolbar-title>
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
                                    <v-row v-if="editedIndex === -1">
                                        <v-col cols="6" sm="6" md="4">
                                            <v-select v-model="editedItem.channelId" :items="resources.channels" :label="$t('recommended_video_channel')"></v-select>
                                        </v-col>
                                        <v-col cols="6" sm="6" md="4">
                                            <v-select v-model="editedItem.contentId" :items="resources.contents" :label="$t('recommended_video_name')"></v-select>
                                        </v-col>
                                    </v-row>
                                    <v-row>
                                        <v-col cols="12" sm="6" md="4">
                                            <v-text-field v-model="editedItem.order" :label="$t('recommended_video_order')" :error-messages="error.order" type="number" min="1" :max="(editedIndex === -1 ? maxOrder + 1 : maxOrder)"></v-text-field>
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
                    :component-data="getComponentData()"
                    tag="tbody"
                >
                    <tr
                        v-for="(item, index) in props.items"
                        :key="index"
                    >
                        <!--                    <td>-->
                        <!--                        <v-icon-->
                        <!--                            small-->
                        <!--                            class="page__grab-icon"-->
                        <!--                        >-->
                        <!--                            mdi-arrow-all-->
                        <!--                        </v-icon>-->
                        <!--                    </td>-->
                        <td>{{ item.order }}</td>
                        <td>{{ item.channel_name }}</td>
                        <td>{{ item.content_name }}</td>
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
                                @click="deleteItem(item)"
                            >
                                mdi-delete
                            </v-icon>
                        </td>
                    </tr>
                </draggable>
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
                    v-if="item.total <= 0"
                    @click="deleteItem(item)"
                >
                    mdi-delete
                </v-icon>
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
    data      : () => ({
        dialog     : false,
        headers    : [],
        error      : [],
        resources  : {
            recommendedVideos: [],
            channels         : [],
            contents         : [],
        },
        maxOrder   : 1,
        editedIndex: -1,
        editedItem : {
            id       : null,
            channelId: null,
            contentId: null,
            order    : 1,
        },
        defaultItem: {
            id       : null,
            channelId: null,
            contentId: null,
            order    : 1,
        },
    }),

    computed: {
        formTitle() {
            return this.editedIndex === -1 ? this.$t('recommended_video_create') : this.$t('recommended_video_editor')
        },
    },

    watch: {
        dialog(val) {
            val || this.close()
        },
        'editedItem.channelId'(val) {
            if (val !== null) {
                this.getChannelContents();
            }
        }
    },

    created() {
        this.initialize()
        this.defaultColumns();
    },

    methods: {
        initialize() {
            let _this = this;
            let url = `/api/global/recommendedVideo/`;
            let params = {
                params: {
                    // limit: 12,
                }
            };
            _this.$store.dispatch("updateLoading", true);

            axios.get(url, params).then((result) => {
                // result = result.data.filter((v) => {
                //     return v.public === 1 && v.public === 1;
                // });
                result = result.data;

                _this.resources.recommendedVideos = result;
                _this.maxOrder = _.maxBy(result, 'order').order;
                _this.$store.dispatch("updateLoading", false);
            });
            this.getChannels();
            this.close()
        },

        getChannels() {
            let _this = this;
            let url = `/api/global/recommendedVideo/channel`;
            axios.get(url).then((result) => {
                result = result.data;
                _.forEach(result, function (item) {
                    _this.resources.channels.push({'text': item.name, 'value': item.id});
                });
            });
        },

        getChannelContents() {
            let _this = this;
            let url = `/api/global/recommendedVideo/channelContent/${this.editedItem.channelId}`;
            axios.get(url).then((result) => {
                result = result.data;
                _this.resources.contents = [];
                _.forEach(result, function (item) {
                    _this.resources.contents.push({'text': item.name, 'value': item.id});
                });
            });
        },

        defaultColumns() {
            let _this = this;
            const header = [
                // {text: '#', value: 'type', sortable: false},
                {text: this.$t('recommended_video_order'), value: 'order'},
                {text: this.$t('recommended_video_channel'), value: 'channel_name'},
                {text: this.$t('recommended_video_name'), align: 'start', sortable: false, value: 'content_name'},
                {text: this.$t('operation'), value: 'actions', sortable: false},
            ];

            _this.headers = header;
        },

        editItem(item) {
            this.error = [];
            this.editedIndex = this.resources.recommendedVideos.indexOf(item);
            this.editedItem = Object.assign({}, item);
            this.dialog = true
        },

        deleteItem(item) {
            let _this = this;
            let url = `/api/global/recommendedVideo/${item.id}`;
            if (confirm('Are you sure you want to delete this item?')) {
                axios.delete(url)
                    .then(response => {
                        if (response.status === 200) {
                            console.log(response);
                            this.initialize();
                        }
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            alert(error.response.data)
                        }
                    });
            }
        },

        close() {
            this.dialog = false;
            this.$nextTick(() => {
                this.editedItem = Object.assign({}, this.defaultItem);
                this.editedIndex = -1;
            });
            this.error = [];
            this.resources.contents = [];
        },

        save() {
            let _this = this;
            _this.$store.dispatch("updateLoading", true);
            let url = '';
            let params = {
                order: parseInt(_this.editedItem.order),
            };
            if (this.editedIndex > -1) {
                url = `/api/global/recommendedVideo/${this.editedItem.id}`;
                axios.put(url, params)
                    .then(response => {
                        if (response.status === 200) {
                            this.initialize();
                        }
                    })
                    .catch(error => {
                        _this.error = error.response.data.errors;
                        if (error.response.data.status_code === 422) {
                            _this.dialog = true;
                        }
                    });
                _this.$store.dispatch("updateLoading", false);

            } else {
                url = `/api/global/recommendedVideo`;
                params.channelId = _this.editedItem.channelId;
                params.contentId = _this.editedItem.contentId;
                axios.post(url, params)
                    .then(response => {
                        if (response.status === 200) {
                            this.initialize();
                        }
                    })
                    .catch(error => {
                        _this.error = error.response.data.errors;
                        if (error.response.data.status_code === 422) {
                            _this.dialog = true;
                        }
                    });
                _this.$store.dispatch("updateLoading", false);
            }
        },

        sort(e) {
            let _this = this;
            let url = `/api/global/recommendedVideo/sort/${e.moved.element.id}`;
            let params = {
                order: e.moved.newIndex + 1,
            };
            axios.put(url, params)
                .then(response => {
                    if (response.status === 200) {
                        this.initialize();
                    }
                })
                .catch(error => {
                    _this.error = error.response.data.errors;
                    if (error.response.data.status_code === 422) {
                        _this.dialog = true;
                    }
                });
        },

        getComponentData() {
            return {
                on   : {
                    change: (e) => {/*console.log(e)*/
                    },
                    end   : (e) => {/*console.log(e)*/
                    },
                },
                attrs: {
                    wrap: true
                },
                props: {
                    value: this.activeNames
                }
            };
        }

    },
}
</script>


<style scoped>

</style>
