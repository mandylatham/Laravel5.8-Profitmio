<template>
    <div class="container-fluid campaign-drops--container">
        <div class="row align-items-end no-gutters">
            <div class="col-12 col-sm-5 col-lg-3">
                <a class="pm-btn btn btn-pm">
                    <plus-icon></plus-icon>
                    <span>NEW</span>
                </a>
            </div>
            <div class="col-none col-sm-2 col-lg-6"></div>
            <div class="col-12 col-sm-5 col-lg-3">
                <input type="text" class="form-control filter--search-box" aria-describedby="search"
                       placeholder="Search" v-model="searchTerm">
            </div>
            <div class="col-12">
                <pm-responsive-table :rows="rows" :columns="columns">
                    <template slot="type" slot-scope="{row}">
                        <div class="drop-type--container">
                            <div class="drop-type--icon">
                                <drop-type-icon :type="row.type"></drop-type-icon>
                            </div>
                            <div class="drop-type--date">
                                <calendar-icon></calendar-icon>
                                <span>{{ row.date | amDateTimeFormat('MM/DD/YYYY | HH:mm A') }}</span>
                            </div>
                        </div>
                    </template>
                    <template slot="status" slot-scope="{row}">
                        <drop-status :status="row.status"></drop-status>
                    </template>
                    <template slot="recipients" slot-scope="{row}">
                        <user-icon></user-icon>
                        <span>{{ row.recipients_count }} RECIPIENTS</span>
                    </template>
                    <template slot="options" slot-scope="{row}">
                        <a class="pm-btn btn btn-transparent">
                            <edit-icon></edit-icon>
                        </a>
                        <a class="pm-btn btn btn-transparent">
                            <trash-icon></trash-icon>
                        </a>
                    </template>
                </pm-responsive-table>
            </div>
        </div>
    </div>
</template>
<script>
    require("./campaign-drops.scss");
    export default {
        data() {
            return {
                rows: [
                    {
                        id: 1,
                        type: 'email',
                        date: '2018-01-01 12:00:01',
                        status: 'completed',
                        recipients_count: 123
                    }
                ],
                companies: [],
                columns: [
                    {
                        field: 'type',
                        is_manager: true,
                        classes: ['drop-type']
                    }, {
                        field: 'status',
                        classes: ['drop-status']
                    }, {
                        field: 'recipients',
                        classes: ['drop-recipients-count']
                    }, {
                        field: 'options',
                        is_manager_footer: true,
                        classes: ['drop-options']
                    }
                ],
                searchTerm: '',
                serverParams: {
                    columnFilters: {},
                    sort: {
                        field: '',
                        type: '',
                    },
                    page: 1,
                    perPage: 10
                },
                totalRecords: 0
            }
        }
    }
</script>
