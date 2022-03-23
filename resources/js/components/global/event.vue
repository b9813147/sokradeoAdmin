<template>
    <v-data-table
        :headers="headers"
        :items="resources"
        sort-by="calories"
        class="elevation-1"
        :loading="this.$store.state.Status.isLoading"
        loading-text="Loading... Please wait"
    >
        <template v-slot:top>
            <v-toolbar flat color="white">
                <v-toolbar-title>{{ $t('event.eventManage') }}</v-toolbar-title>
                <v-divider
                    class="mx-4"
                    inset
                    vertical
                />
                <v-spacer/>
                <v-dialog v-model="dialog" max-width="900px">
                    <v-card>
                        <v-card-title>
                            <span class="headline">{{ formTitle }}</span>
                        </v-card-title>

                        <v-card-text>
                            <v-container>
                                <v-row>
                                    <v-col cols="12" sm="6" md="4">
                                        <v-text-field v-model="editedItem.event_data.eventType" :label="$t('event.eventType')"/>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="4">
                                        <v-text-field v-model="editedItem.event_data.maxParticipant" :label="$t('event.maxParticipant')"/>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="4">
                                        <v-text-field v-model="editedItem.event_data.stageCount" :label="$t('event.stageCount')"/>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="4">
                                        <v-text-field v-model="editedItem.event_data.enableTrial" :label="$t('event.enableTrial')"/>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="4">
                                        <validation-provider
                                            v-slot="{ errors }"
                                            name="cqty"
                                            rules="numeric"
                                        >
                                            <v-text-field
                                                v-model="editedItem.event_data.cqty"
                                                :error-messages="errors"
                                                :label="$t('event.cqty')"

                                            ></v-text-field>
                                        </validation-provider>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="4">
                                        <v-select
                                            v-model="editedItem.event_data.productCode"
                                            :items="productCodes"
                                            item-text="name"
                                            item-value="value"
                                            :label="$t('event.productCode')"
                                            persistent-hint
                                            return-object
                                            single-line
                                        ></v-select>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="4">
                                        <validation-provider
                                            v-slot="{ errors }"
                                            name="school_code"
                                        >
                                            <v-text-field
                                                v-model="editedItem.event_data.school_code"
                                                :error-messages="errors"
                                                :label="$t('school_code')"

                                            ></v-text-field>
                                        </validation-provider>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="4">
                                        <validation-provider
                                            v-slot="{ errors }"
                                            name="trialDeadline"
                                            rules="numeric"
                                        >
                                            <v-text-field
                                                v-model="editedItem.event_data.trialDeadline"
                                                :error-messages="errors"
                                                :label="$t('event.days')"

                                            ></v-text-field>
                                        </validation-provider>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="4">
                                        <validation-provider
                                            v-slot="{ errors }"
                                            name="cliGroup"
                                            rules="numeric"
                                        >
                                            <v-text-field
                                                v-model="editedItem.event_data.cliGroup"
                                                :error-messages="errors"
                                                :label="$t('event.cliGroup')"

                                            ></v-text-field>
                                        </validation-provider>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="4">
                                        <validation-provider
                                            v-slot="{ errors }"
                                            name="sokNumber"
                                            rules="numeric"
                                        >
                                            <v-text-field
                                                v-model="editedItem.event_data.sokNumber"
                                                :error-messages="errors"
                                                :label="$t('event.sokNumber')"

                                            ></v-text-field>
                                        </validation-provider>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="4" v-for="(v,k) in editedItem.event_data.ap" :key="k">
                                        <v-checkbox
                                            v-if="v.key !== 'cligroup' && v.key !=='soknumber' "
                                            v-model="v.val"
                                            :label="$t('event.' + v.key)"
                                        ></v-checkbox>
                                    </v-col>

                                    <v-col cols="12" sm="12" md="12">
                                        <v-divider></v-divider>
                                        <span class=pt-3>{{ $t('event.registration') }}</span>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="4">
                                        <v-text-field v-model="editedItem.event_data.eventStage.registration.stageOrder" :label="$t('event.stageOrder')"/>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="4">
                                        <v-menu
                                            v-model="registrationStartDateMenu"
                                            :close-on-content-click="false"
                                            :nudge-right="40"
                                            transition="scale-transition"
                                            offset-y
                                            min-width="auto"
                                        >
                                            <template v-slot:activator="{ on, attrs }">
                                                <v-text-field
                                                    v-model="editedItem.event_data.eventStage.registration.startDate"
                                                    prepend-icon="mdi-calendar"
                                                    :label="$t('event.startDate')"
                                                    readonly
                                                    v-bind="attrs"
                                                    v-on="on"
                                                ></v-text-field>
                                            </template>
                                            <v-date-picker
                                                v-model="editedItem.event_data.eventStage.registration.startDate"
                                                @input="registrationStartDateMenu = false"
                                            ></v-date-picker>
                                        </v-menu>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="4">
                                        <v-menu
                                            v-model="registrationEndDateMenu"
                                            :close-on-content-click="false"
                                            :nudge-right="40"
                                            transition="scale-transition"
                                            offset-y
                                            min-width="auto"
                                        >
                                            <template v-slot:activator="{ on, attrs }">
                                                <v-text-field
                                                    v-model="editedItem.event_data.eventStage.registration.endDate"
                                                    :label="$t('event.endDate')"
                                                    prepend-icon="mdi-calendar"
                                                    readonly
                                                    v-bind="attrs"
                                                    v-on="on"
                                                ></v-text-field>
                                            </template>
                                            <v-date-picker
                                                v-model="editedItem.event_data.eventStage.registration.endDate"
                                                @input="registrationEndDateMenu = false"
                                            ></v-date-picker>
                                        </v-menu>
                                    </v-col>
                                    <v-col cols="12" sm="12" md="12">
                                        <v-divider></v-divider>
                                        <span class=pt-3>{{ $t('event.reviewing') }}</span>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="4">
                                        <v-text-field v-model="editedItem.event_data.eventStage.reviewing.stageOrder" :label="$t('event.stageOrder')"/>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="4">
                                        <v-menu
                                            v-model="reviewingStartDateMenu"
                                            :close-on-content-click="false"
                                            :nudge-right="40"
                                            transition="scale-transition"
                                            offset-y
                                            min-width="auto"
                                        >
                                            <template v-slot:activator="{ on, attrs }">
                                                <v-text-field
                                                    v-model="editedItem.event_data.eventStage.reviewing.startDate"
                                                    prepend-icon="mdi-calendar"
                                                    :label="$t('event.startDate')"
                                                    readonly
                                                    v-bind="attrs"
                                                    v-on="on"
                                                ></v-text-field>
                                            </template>
                                            <v-date-picker
                                                v-model="editedItem.event_data.eventStage.reviewing.startDate"
                                                @input="reviewingStartDateMenu = false"
                                            ></v-date-picker>
                                        </v-menu>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="4">
                                        <v-menu
                                            v-model="reviewingEndDateMenu"
                                            :close-on-content-click="false"
                                            :nudge-right="40"
                                            transition="scale-transition"
                                            offset-y
                                            min-width="auto"
                                        >
                                            <template v-slot:activator="{ on, attrs }">
                                                <v-text-field
                                                    v-model="editedItem.event_data.eventStage.reviewing.endDate"
                                                    :label="$t('event.endDate')"
                                                    prepend-icon="mdi-calendar"
                                                    readonly
                                                    v-bind="attrs"
                                                    v-on="on"
                                                ></v-text-field>
                                            </template>
                                            <v-date-picker
                                                v-model="editedItem.event_data.eventStage.reviewing.endDate"
                                                @input="reviewingEndDateMenu = false"
                                            ></v-date-picker>
                                        </v-menu>
                                    </v-col>
                                    <v-col cols="12" sm="12" md="12">
                                        <v-divider></v-divider>
                                        <span class=pt-3>{{ $t('event.submission') }}</span>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="4">
                                        <v-text-field v-model="editedItem.event_data.eventStage.submission.stageOrder" :label="$t('event.stageOrder')"/>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="4">
                                        <v-menu
                                            v-model="submissionStartDateMenu"
                                            :close-on-content-click="false"
                                            :nudge-right="40"
                                            transition="scale-transition"
                                            offset-y
                                            min-width="auto"
                                        >
                                            <template v-slot:activator="{ on, attrs }">
                                                <v-text-field
                                                    v-model="editedItem.event_data.eventStage.submission.startDate"
                                                    prepend-icon="mdi-calendar"
                                                    :label="$t('event.startDate')"
                                                    readonly
                                                    v-bind="attrs"
                                                    v-on="on"
                                                ></v-text-field>
                                            </template>
                                            <v-date-picker
                                                v-model="editedItem.event_data.eventStage.submission.startDate"
                                                @input="submissionStartDateMenu = false"
                                            ></v-date-picker>
                                        </v-menu>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="4">
                                        <v-menu
                                            v-model="submissionEndDateMenu"
                                            :close-on-content-click="false"
                                            :nudge-right="40"
                                            transition="scale-transition"
                                            offset-y
                                            min-width="auto"
                                        >
                                            <template v-slot:activator="{ on, attrs }">
                                                <v-text-field
                                                    v-model="editedItem.event_data.eventStage.submission.endDate"
                                                    :label="$t('event.endDate')"
                                                    prepend-icon="mdi-calendar"
                                                    readonly
                                                    v-bind="attrs"
                                                    v-on="on"
                                                ></v-text-field>
                                            </template>
                                            <v-date-picker
                                                v-model="editedItem.event_data.eventStage.submission.endDate"
                                                @input="submissionEndDateMenu = false"
                                            ></v-date-picker>
                                        </v-menu>
                                    </v-col>
                                    <v-col cols="12" sm="2" md="2">
                                        <v-checkbox
                                            v-model="editedItem.event_data.eventStage.submission.isMultiTask"
                                            :label="$t('event.isMultiTask')"
                                            color="primary"
                                            :value="editedItem.event_data.eventStage.submission.isMultiTask"
                                            hide-details
                                        ></v-checkbox>
                                    </v-col>
                                    <v-col cols="12" sm="2" md="2">
                                        <v-checkbox
                                            v-model="editedItem.event_data.eventStage.submission.isGroupUser"
                                            :label="$t('event.isGroupUser')"
                                            color="primary"
                                            :value="editedItem.event_data.eventStage.submission.isGroupUser"
                                            hide-details
                                        ></v-checkbox>
                                    </v-col>
                                    <v-col cols="12" sm="2" md="2">
                                        <v-checkbox
                                            v-model="editedItem.event_data.eventStage.submission.requirement.isDoubleGreen"
                                            :label="$t('event.isDoubleGreen')"
                                            color="primary"
                                            hide-details
                                        ></v-checkbox>
                                    </v-col>
                                    <v-col cols="12" sm="2" md="2">
                                        <v-checkbox
                                            v-model="editedItem.event_data.eventStage.submission.requirement.hasMaterial"
                                            :label="$t('event.hasMaterial')"
                                            color="primary"
                                            hide-details
                                        ></v-checkbox>
                                    </v-col>
                                    <v-col cols="12" sm="2" md="2">
                                        <v-checkbox
                                            v-model="editedItem.event_data.eventStage.submission.requirement.hasTPC"
                                            :label="$t('event.hasTPC')"
                                            color="primary"
                                            hide-details
                                        ></v-checkbox>
                                    </v-col>
                                    <v-col cols="12" sm="12" md="12">
                                        <v-divider></v-divider>
                                        <span class=pt-3>{{ $t('event.certification') }}</span>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="4">
                                        <v-text-field v-model="editedItem.event_data.eventStage.certification.stageOrder" :label="$t('event.stageOrder')"/>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="4">
                                        <v-menu
                                            v-model="certificationStartDateMenu"
                                            :close-on-content-click="false"
                                            :nudge-right="40"
                                            transition="scale-transition"
                                            offset-y
                                            min-width="auto"
                                        >
                                            <template v-slot:activator="{ on, attrs }">
                                                <v-text-field
                                                    v-model="editedItem.event_data.eventStage.certification.startDate"
                                                    prepend-icon="mdi-calendar"
                                                    :label="$t('event.startDate')"
                                                    readonly
                                                    v-bind="attrs"
                                                    v-on="on"
                                                ></v-text-field>
                                            </template>
                                            <v-date-picker
                                                v-model="editedItem.event_data.eventStage.certification.startDate"
                                                @input="certificationStartDateMenu = false"
                                            ></v-date-picker>
                                        </v-menu>
                                    </v-col>
                                    <v-col cols="12" sm="6" md="4">
                                        <v-menu
                                            v-model="certificationEndDateMenu"
                                            :close-on-content-click="false"
                                            :nudge-right="40"
                                            transition="scale-transition"
                                            offset-y
                                            min-width="auto"
                                        >
                                            <template v-slot:activator="{ on, attrs }">
                                                <v-text-field
                                                    v-model="editedItem.event_data.eventStage.certification.endDate"
                                                    :label="$t('event.endDate')"
                                                    prepend-icon="mdi-calendar"
                                                    readonly
                                                    v-bind="attrs"
                                                    v-on="on"
                                                ></v-text-field>
                                            </template>
                                            <v-date-picker
                                                v-model="editedItem.event_data.eventStage.certification.endDate"
                                                @input="certificationEndDateMenu = false"
                                            ></v-date-picker>
                                        </v-menu>
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
                v-if="item.event_data"
            >
                edit
            </v-icon>
            <v-icon
                small
                class="mr-2"
                @click="createItem(item)"
                v-else
            >
                mdi-plus
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

</template>

<script>
import {required, digits, email, max, regex, numeric}                       from 'vee-validate/dist/rules'
import {extend, ValidationObserver, ValidationProvider, setInteractionMode} from 'vee-validate'

setInteractionMode('eager')

extend('digits', {
    ...digits,
    message: '{_field_} needs to be {length} digits. ({_value_})',
})
extend('numeric', {
    ...numeric,
    message: '{_field_} must be  numeric. ',
})

extend('required', {
    ...required,
    message: '{_field_} can not be empty',
})

extend('max', {
    ...max,
    message: '{_field_} may not be greater than {length} characters',
})

extend('regex', {
    ...regex,
    message: '{_field_} {_value_} does not match {regex}',
})

extend('email', {
    ...email,
    message: 'Email must be valid',
})
export default {
    components: {
        ValidationProvider,
        ValidationObserver,
    },
    data(vm) {
        return {
            // trialDeadlineDateMenu     : false,
            registrationEndDateMenu   : false,
            registrationStartDateMenu : false,
            reviewingEndDateMenu      : false,
            reviewingStartDateMenu    : false,
            submissionStartDateMenu   : false,
            submissionEndDateMenu     : false,
            certificationStartDateMenu: false,
            certificationEndDateMenu  : false,
            dialog                    : false,
            headers                   : [],
            status                    : [],
            access                    : [],
            review_status             : [],
            public_note_status        : [],
            resources                 : [],
            editedIndex               : -1,
            productCodes              : [
                {name: 'HiTeach STD', value: 'J223IZ6M'},
                {name: 'HiTeach TBL', value: '3222C6D2'},
                {name: 'HiTeach PRO', value: 'J223IZAM'},
                {name: 'HiTeach Lite', value: 'J2236ZCX'},
                {name: 'HiTeach Mobile', value: '3222DNG2'},
                {name: 'HiTeach Premium', value: '3222IAVN'},
                {name: 'HiTeach 5', value: 'BYJ6LZ6Z'},
            ],
            editedItem                : {
                school_code: null,
                name       : null,
                description: null,
                // status: 0,
                // public: 0,
                thumbnail         : null,
                groupId           : null,
                review_status     : null,
                event_data        : {
                    cliGroup      : 0,
                    sokNumber     : 0,
                    school_code   : null,
                    eventType     : null,
                    startDate     : null,
                    endDate       : null,
                    maxParticipant: 0,
                    stageCount    : 0,
                    enableTrial   : 0,
                    trialDeadline : 0,
                    productCode   : null,
                    ap            : [],
                    cqty          : 0,
                    eventStage    : {
                        registration : {
                            stageOrder: 0,
                            startDate : null,
                            endDate   : null
                        },
                        reviewing    : {
                            stageOrder: 0,
                            startDate : null,
                            endDate   : null
                        },
                        submission   : {
                            stageOrder : 0,
                            startDate  : null,
                            endDate    : null,
                            isMultiTask: 0,
                            requirement: {
                                isDoubleGreen: 0,
                                hasMaterial  : 0,
                                hasTPC       : 0
                            }
                        },
                        certification: {
                            stageOrder: 0,
                            startDate : null,
                            endDate   : null
                        }
                    }
                },
                public_note_status: null,
            },
            defaultItem               : {
                school_code: null,
                name       : null,
                description: null,
                // status: 0,
                // public: 0,
                thumbnail         : null,
                groupId           : null,
                review_status     : null,
                event_data        : {
                    cliGroup      : 0,
                    sokNumber     : 0,
                    school_code   : null,
                    eventType     : null,
                    startDate     : null,
                    endDate       : null,
                    maxParticipant: 0,
                    stageCount    : 0,
                    enableTrial   : 0,
                    trialDeadline : 0,
                    cqty          : 0,
                    productCode   : null,
                    ap            : [
                        {key: 'sokapp', val: false},
                        {key: 'sokvdo', val: false},
                        {key: 'sokdesk', val: false},
                        {key: 'sokrpt', val: false},
                        {key: 'ezs', val: false},
                    ],
                    eventStage    : {
                        registration : {
                            stageOrder: 0,
                            startDate : null,
                            endDate   : null
                        },
                        reviewing    : {
                            stageOrder: 0,
                            startDate : null,
                            endDate   : null
                        },
                        submission   : {
                            stageOrder : 0,
                            startDate  : null,
                            endDate    : null,
                            isMultiTask: 0,
                            isGroupUser: 0,
                            requirement: {
                                isDoubleGreen: 0,
                                hasMaterial  : 0,
                                hasTPC       : 0
                            }
                        },
                        certification: {
                            stageOrder: 0,
                            startDate : null,
                            endDate   : null
                        }
                    }
                },
                public_note_status: null,
            },
            nameRules                 : [
                v => !!v || 'Name is required',
                v => (v && v.length <= 30) || 'Name must be less than 30 characters'
            ],
            school_codeRules          : [
                v => !!v || 'school_code is required',
                v => (v && v.length <= 30) || 'Name must be less than 30 characters'
            ]
        }
    },

    computed: {
        formTitle() {
            return this.editedIndex === -1 ? this.$t('event.create_event') : this.$t('event.editor_event')
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
            let _this = this;
            let url = `/api/global/event/channel`;
            _this.$store.dispatch("updateLoading", true);

            axios.get(url).then((response) => {
                let data = response.data;
                _this.resources = data.map((v) => {
                    return {
                        'school_code'       : v.school_code,
                        'name'              : v.name,
                        'description'       : v.description,
                        'thum'              : _.isEmpty(v.thumbnail) ? null : `${this.$store.state.Path.group}${v.id}/${v.thumbnail}?${_.random(0, 9)}`,
                        'event_data'        : JSON.parse(v.event_data),
                        'groupId'           : v.id,
                        'review_status'     : (v.review_status === '1') ? {text: this.$t('enable'), value: 1} : {text: this.$t('disable'), value: 0},
                        'public_note_status': (v.public_note_status === 1) ? {text: this.$t('enable'), value: 1} : {text: this.$t('disable'), value: 0}
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
            const public_note_status = [
                {text: this.$t('enable'), value: 1},
                {text: this.$t('disable'), value: 0}
            ];

            this.headers = header;
            this.status = status;
            this.access = access;
            this.review_status = review_status;
            this.public_note_status = public_note_status;
        },


        editItem(item) {
            this.editedIndex = this.resources.indexOf(item);
            this.editedItem = Object.assign({}, item);
            if (_.isEmpty(item.event_data)) {
                this.editedItem.event_data = this.defaultItem.event_data;
            }
            this.dialog = true
        },
        createItem(item) {
            this.editedIndex = -1;
            delete item.event_data;
            this.editedItem = Object.assign({}, this.defaultItem, item);
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
                this.editedIndex = -1
            }, 300)
        },

        save() {
            let _this = this;
            let url = `/api/group/${_this.editedItem.groupId}`;

            // App 擴充
            let ap = this.editedItem.event_data.ap;
            ap.push({key: 'cligroup', val: _this.editedItem.event_data.cliGroup});
            ap.push({key: 'soknumber', val: _this.editedItem.event_data.sokNumber});
            // 格式轉換
            const obj = {
                ap         : ap,
                productCode: _.isObject(_this.editedItem.event_data.productCode) ? _this.editedItem.event_data.productCode.value : _this.editedItem.event_data.productCode,
            };

            // 合併格式
            let data = Object.assign({}, this.editedItem.event_data, obj);
            // 純更新內容
            axios.put(url, {event_data: data})
                .then((response) => {
                    if (response.data.status !== 200) {
                        return
                    }
                    _this.initialize();
                    this.resources.push(this.editedItem);
                    return this.close()
                }).catch((error) => {
                console.log(error)
            })
        },
        parseDate(date) {
            if (!date) return null

            const [month, day, year] = date.split('/')
            return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`
        },
    },
}

</script>

<style scoped>

</style>
