<template>
    <div class="pmt-container">
        <div class="pmt">
            <div class="pmt-spinner" v-if="isLoading">
                <spinner-icon></spinner-icon>
            </div>
            <div class="no-items-row" v-if="rows.length === 0">
                No Items
            </div>
            <div class="pmt-row" :class="{'open': row.open || disableToggle}" v-for="(row, rowIndex) in rows">
                <div class="pmt-col" @click="toggleRow(row, column)" :class="classesForCol(column)" v-for="column in columns">
                    {{ column.field ? get(row, column.field) : '' }}
                    <slot :name="column.slot" :row="row" v-if="column.slot"></slot>
                </div>
            </div>
        </div>
        <pm-pagination :pagination="pagination" @page-changed="onPageChanged"></pm-pagination>
    </div>
</template>
<script>
    import Vue from 'vue';
    import {get} from 'lodash';

    export default {
        components: {
            'spinner-icon':  require('./../spinner-icon/spinner-icon').default,
            'pm-pagination':  require('./../pm-pagination/pm-pagination').default
        },
        props: {
            isLoading: {
                type: Boolean,
                required: false,
                default: false
            },
            disableToggle: {
                type: Boolean,
                required: false,
                default: false
            },
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
            options: {
                type: Object,
                required: false,
                default: function () {
                    return {
                        breakMobile: 'lg'
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
            onPageChanged: function (event) {
                this.$emit('page-changed', {page: event.page});
            },
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
