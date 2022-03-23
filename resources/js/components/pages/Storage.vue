<template>
    <v-container class="grey lighten-5 rounded-lg" fluid>
        <v-card>
            <div class="progress" :title="0">
                <div class="progress-bar lime darken-3" role="progressbar" :style="{width: autoWidth.blob }" v-if="storage.blob"
                     aria-valuenow="0"
                     aria-valuemin="0"
                     aria-valuemax="100" :title="formatSizeUnits(storage.blob) ">
                    {{ formatSizeUnits(storage.blob) }}
                </div>
                <div class="progress-bar  green darken-3" role="progressbar" :style="{width: autoWidth.old}" v-if="storage.old"
                     aria-valuenow="0"
                     aria-valuemin="0"
                     aria-valuemax="100" :title="formatSizeUnits(storage.old)">
                    {{ formatSizeUnits(storage.old) }}
                </div>
                <div class="progress-bar  cyan darken-3" role="progressbar" :style="{width: autoWidth.hiTeachNote}" v-if="storage.hiTeachNote"
                     aria-valuenow="0"
                     aria-valuemin="0"
                     aria-valuemax="100" :title="formatSizeUnits(storage.hiTeachNote)">
                    {{ formatSizeUnits(storage.hiTeachNote) }}
                </div>
                <div class="progress-bar  blue darken-3" role="progressbar" :style="{width: autoWidth.lessonPlan}" v-if="storage.lessonPlan"
                     aria-valuenow="0"
                     aria-valuemin="0"
                     aria-valuemax="100" :title="formatSizeUnits(storage.lessonPlan)">
                    {{ formatSizeUnits(storage.lessonPlan) }}
                </div>
                <div class="progress-bar  purple darken-3" role="progressbar" :style="{width: autoWidth.material}" v-if="storage.material"
                     aria-valuenow="0"
                     aria-valuemin="0"
                     aria-valuemax="100" :title="formatSizeUnits(storage.material)">
                    {{ formatSizeUnits(storage.material) }}
                </div>

            </div>
        </v-card>


        <v-row class="pt-5">
            <v-col md="12">
                <div class="pa-2 lime darken-3 rounded d-inline-block"></div>
                {{ $t('storage.video') }} {{ formatSizeUnits(storage.blob) }}
            </v-col>
            <v-col md="12">
                <div class="pa-2 green darken-3 rounded d-inline-block"></div>
                {{ $t('storage.traditionVideo') }} {{ formatSizeUnits(storage.old) }}
            </v-col>
            <v-col md="12">
                <div class="pa-2 cyan darken-3 rounded d-inline-block"></div>
                {{ $t('storage.hiTeachNote') }} {{ formatSizeUnits(storage.hiTeachNote) }}
            </v-col>
            <v-col md="12">
                <div class="pa-2 blue darken-3 rounded d-inline-block"></div>
                {{ $t('storage.lessonPlan') }} {{ formatSizeUnits(storage.lessonPlan) }}
            </v-col>
            <v-col md="12">
                <div class="pa-2 purple darken-3 rounded d-inline-block"></div>
                {{ $t('storage.material') }} {{ formatSizeUnits(storage.material) }}
            </v-col>
            <v-col md="12">
                <div class="pa-2 white rounded d-inline-block"></div>
                {{ formatSizeUnits(storage.blob + storage.old + storage.hiTeachNote + storage.lessonPlan + storage.material) }} {{ $t('storage.use') }}( {{ $t('storage.total') }} {{ formatSizeUnits(storage.total) }})
                <v-icon>mdi-database</v-icon>
            </v-col>
        </v-row>
    </v-container>

</template>

<script>
export default {
    props: ['storage'],
    name : "storage",

    computed: {
        autoWidth() {
            let blob = this.storage.blob / (this.storage.blob + this.storage.old) * 100;
            let old = this.storage.old / (this.storage.blob + this.storage.old) * 100;
            return {
                blob: parseFloat(blob.toFixed(1)) + '%',
                old : parseFloat(old.toFixed(1)) + '%'
            }
        },
    },
    methods : {
        /**
         * 單位換算
         */
        formatSizeUnits($bytes) {
            if ($bytes >= 1073741824) {
                $bytes = this.number_format($bytes / 1073741824, 2) + ' GB';
            } else if ($bytes >= 1048576) {
                $bytes = this.number_format($bytes / 1048576, 2) + ' MB';
            } else if ($bytes >= 1024) {
                $bytes = this.number_format($bytes / 1024, 2) + ' KB';
            } else if ($bytes > 1) {
                $bytes = $bytes + ' bytes';
            } else if ($bytes == 1) {
                $bytes = $bytes + ' byte';
            } else {
                $bytes = '0 bytes';
            }

            return $bytes;
        },
        /**
         *  格式轉換
         */
        number_format(number, decimals, dec_point, thousands_sep) {
            // Strip all characters but numerical ones.
            number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
            let n          = !isFinite(+number) ? 0 : +number,
                prec       = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                sep        = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                dec        = (typeof dec_point === 'undefined') ? '.' : dec_point,
                s          = '',
                toFixedFix = function (n, prec) {
                    let k = Math.pow(10, prec);
                    return '' + Math.round(n * k) / k;
                };
            // Fix for IE parseFloat(0.55).toFixed(0) = 0;
            s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
            }
            if ((s[1] || '').length < prec) {
                s[1] = s[1] || '';
                s[1] += new Array(prec - s[1].length + 1).join('0');
            }
            return s.join(dec);
        },
    }
}
</script>

<style lang="scss" scoped>
.progress {
    display: flex;
    height: 1.5rem;
    overflow: hidden; /*force rounded corners by cropping it*/
    font-size: 1rem *.75;
    background-color: white;
    border-radius: .25rem;
    box-shadow: inset 0 1px 2px rgba(black, .075);
}

.progress-bar {
    display: flex;
    flex-direction: column;
    justify-content: center;
    overflow: hidden;
    //color: darkgray;
    text-align: center;
    white-space: nowrap;
    //background-color: darkgray;
    transition: width .6s ease;
}
</style>
