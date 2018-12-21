<template>
    <div class="pmt">
        <div class="pmt-row" @click="$emit('onClickRow', row)" :class="{'open': row.open}" v-for="(row, rowIndex) in rows">
            <div class="pmt-col" @click="toggleRow(row, column)" :class="classesForCol(column)" v-for="column in columns">
                <span v-if="column.html" v-html="get(row, column.field)"></span>
                <slot :name="column.field" :row="row" v-if="!column.html">
                    {{ get(row, column.field) }}
                </slot>
            </div>
        </div>
    </div>
</template>
<script>
    require("./pm-responsive-table.scss");

    import Vue from 'vue';
    import {get} from 'lodash';

    export default {
        props: {
            rows: {
                type: Array,
                required: true,
            },
            columns: {
                type: Array,
                required: true,
            }
        },
        data() {
            return {};
        },
        methods: {
            get,
            classesForCol: function (col) {
                const classes = [];
                if (col.is_manager) {
                    classes.push('manager');
                }
                if (col.is_manager_footer) {
                    classes.push('manager-footer');
                }
                if (col.classes) {
                    classes.push(...col.classes);
                }
                return classes;
            },
            toggleRow: function (row, column) {
                if (column.is_manager) {
                    Vue.set(row, 'open', !row.open);
                }
            }
        }
    }
</script>
