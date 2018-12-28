<template>
    <div class="pmt-container">
        <div class="pmt">
            <div class="pmt-row" :class="{'open': row.open}" v-for="(row, rowIndex) in rows">
                <div class="pmt-col" @click="toggleRow(row, column)" :class="classesForCol(column)" v-for="column in columns">
                    <span v-if="column.html" v-html="get(row, column.field)"></span>
                    <slot :name="column.field" :row="row" v-if="!column.html">
                        {{ get(row, column.field) }}
                    </slot>
                </div>
            </div>
        </div>
        <div class="pmt-pagination">
            <div class="paginator-content">
                <a class="paginator-left">
                    <chevron-left-icon></chevron-left-icon>
                </a>
                <div class="paginator-input">
                    <input type="text" v-model="pagination.page" class="form-control" @keyup.enter="$emit('pageChanged', pagination.page)">
                    <span>of {{ totalPages }}</span>
                </div>
                <a class="paginator-right">
                    <chevron-left-icon></chevron-left-icon>
                </a>
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
            pagination: {
                type: Object,
                required: false,
                default: function () {
                    return {
                        page: 1,
                        per_page: 15,
                        total: 15
                    };
                }
            },
            columns: {
                type: Array,
                required: true,
            }
        },
        data() {
            return {};
        },
        computed: {
            totalPages: function () {
                return Math.ceil(this.pagination.total / this.pagination.per_page);
            }
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
