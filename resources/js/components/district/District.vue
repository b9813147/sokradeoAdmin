<template>
    <v-card>
        <v-data-table
            :headers="headers"
            :items="resource"
            sort-by="calories"
            class="elevation-1"
            :loading="this.$store.state.Status.isLoading"
            loading-text="Loading... Please wait"
        >
            <template v-slot:top>
                <v-toolbar flat color="white">
                    <v-toolbar-title>{{ $t('district_manager') }}</v-toolbar-title>
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
                                            <v-text-field v-model="editedItem.name" :label="$t('district_name')" disabled/>
                                        </v-col>
                                        <v-col cols="12" sm="6" md="4">
                                            <v-text-field v-model="editedItem.abbr" :label="$t('district_code')" disabled/>
                                        </v-col>
                                        <!--                                    <v-col cols="12" sm="6" md="4">-->
                                        <!--                                        <v-select :items="status" item-text="text" item-value="value" :label="$t('status')" v-model="editedItem.status" return-object required/>-->
                                        <!--                                    </v-col>-->
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
            <template v-slot:item.status="{ item }">
                {{ item.status.text }}
            </template>
        </v-data-table>
    </v-card>

</template>

<script>
export default {
    name: "District",
    data: () => ({
        dialog     : false,
        headers    : [],
        status     : [],
        access     : [],
        resource   : [],
        editedIndex: -1,
        editedItem : {
            id         : null,
            school_code: null,
            abbr       : null,
            name       : null,
            status     : null,
            // public: 0,
            thumbnail: null,
        },
        defaultItem: {
            id         : null,
            school_code: null,
            abbr       : null,
            name       : null,
            status     : null,
            // public: 0,
            thumbnail: null,
        },
    }),

    computed: {
        formTitle() {
            return this.editedIndex === -1 ? this.$t('create_district') : this.$t('editor_district')
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
            let districtId = this.$store.state.Auth.user.district_id;

            let _this = this;
            let url = `/api/district/${districtId}`;
            _this.$store.dispatch("updateLoading", true);

            axios.get(url).then((response) => {
                // console.log(response)
                let data = response.data;
                //thum 顯示圖片用 沒有任何作用
                const CustomData = {
                    id         : data.id,
                    abbr       : data.abbr,
                    name       : data.name,
                    school_code: data.school_code,
                    thum       : _.isEmpty(data.thumbnail) ? null : `${this.$store.state.Path.district}${data.id}/${data.thumbnail}?${_.random(0, 9)}`,
                    // status: (data.status === 1) ? {text: this.$t('enable'), value: 1} : {text: this.$t('disable'), value: 0},
                    // public: (data.public === 1) ? {text: this.$t('open'), value: 1} : {text: this.$t('private'), value: 0},
                };
                _this.resource = [CustomData];
                _this.$store.dispatch("updateLoading", false);
            });

            _this.defaultColumns();

        },
        // 預設欄位
        defaultColumns() {
            const header = [
                {text: `${this.$t('district_name')}`, value: 'name', sortable: false},
                {text: `${this.$t('district_code')}`, value: 'abbr', align: 'left', sortable: false},
                // {text: `${this.$t('status')}`, value: 'status'},
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
            const review_status = [
                {text: this.$t('enable'), value: 1},
                {text: this.$t('disable'), value: 0}
            ];

            this.headers = header;
            this.status = status;
            this.access = access;
            this.review_status = review_status;
        },


        editItem(item) {
            this.editedIndex = this.resource.indexOf(item);
            this.editedItem = Object.assign({}, item);
            this.dialog = true
        },
        // deleteItem(item) {
        //   const index = this.resource.indexOf(item);
        //   confirm('Are you sure you want to delete this item?') && this.resource.splice(index, 1)
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
                let url = `/api/district/${_this.editedItem.id}`;
                // 轉換格式
                const obj = {
                    // public: _this.editedItem.public.value,
                    // status: _this.editedItem.status.value,
                };
                let config = {
                    headers: {
                        'content-Type': 'multipart/form-data'
                    }
                };
                delete this.editedItem.thum
                // 合併格式
                let data = Object.assign({}, this.editedItem, obj);


                if (_this.editedItem.thumbnail) {
                    // console.log('有圖片');
                    formData.append('thumbnail', _this.editedItem.thumbnail[0]);
                    axios.post(url, formData, {params: data}, {config}).then((response) => {
                        _this.initialize();
                        _this.$store.dispatch("updateLoading", false);
                    });
                    Object.assign(this.resource[this.editedIndex], this.editedItem);
                    return this.close();
                }
                // 純更新內容
                axios.post(url, formData, {params: data}, {config}).then((response) => {
                    _this.initialize()
                });
                Object.assign(this.resource[this.editedIndex], this.editedItem);
                return this.close()
            } else {
                // todo 尚未實作
                let _this = this;
                let formData = new FormData();
                let url = `/api/district`;
                // 轉換格式
                const obj = {
                    // public: _this.editedItem.public.value,
                    // status: _this.editedItem.status.value,
                    // review_status: _this.editedItem.review_status.value
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
                        this.resource.push(this.editedItem);
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
                    this.resource.push(this.editedItem);
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
