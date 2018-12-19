<template>
    <div class="container-fluid campaign-recipients--container">
        <div class="row align-items-end no-gutters">
            <div class="col-12 col-sm-5 col-lg-3">
                <a class="pm-btn btn btn-pm">
                    <plus-icon></plus-icon>
                    <span>UPLOAD A NEW LIST</span>
                </a>
            </div>
            <div class="col-12">
                <pm-responsive-table :rows="rows" :columns="columns">
                    <template slot="name" slot-scope="{row}">
                        <div class="recipient-name--container">
                            <div class="recipient-name--icon">
                                <list-icon></list-icon>
                            </div>
                            <div class="recipient-name--data">
                                <strong>{{ row.name }}</strong>
                                <small>Database List</small>
                                <div class="recipient-name--id">List File ID: {{ row.id }}</div>
                            </div>
                        </div>
                    </template>
                    <template slot="stats1" slot-scope="{row}">
                        <div class="recipient-stats--container">
                            <div class="recipient-stats--stat">
                                <users-icon></users-icon>
                                <span>Total</span>
                                <strong>{{ row.total }}</strong>
                            </div>
                            <div class="recipient-stats--stat">
                                <mail-icon></mail-icon>
                                <span>Email</span>
                                <strong>{{ row.email_count }}</strong>
                            </div>
                            <div class="recipient-stats--stat">
                                <phone-icon></phone-icon>
                                <span>Phone</span>
                                <strong>{{ row.phone_count }}</strong>
                            </div>
                        </div>
                    </template>
                    <template slot="stats2" slot-scope="{row}">
                        <div class="recipient-stats--container">
                            <div class="recipient-stats--stat">
                                <map-icon></map-icon>
                                <span>Conquest</span>
                                <strong>{{ row.conquest_count }}</strong>
                            </div>
                            <div class="recipient-stats--stat">
                                <database-icon></database-icon>
                                <span>Dealer DB</span>
                                <strong>{{ row.dealer_count }}</strong>
                            </div>
                        </div>
                    </template>
                    <template slot="options" slot-scope="{row}">
                        <a class="btn btn-action">
                            MODIFY MEMBER
                        </a>
                        <div class="options-group">
                            <a class="pm-btn btn btn-transparent">
                                <download-icon></download-icon>
                            </a>
                            <a class="pm-btn btn btn-transparent">
                                <trash-icon></trash-icon>
                            </a>
                        </div>
                    </template>
                </pm-responsive-table>
            </div>
        </div>
    </div>
</template>
<script>
    require("./campaign-recipients.scss");
    export default {
        data() {
            return {
                rows: [
                    {
                        id: 1,
                        name: 'Dealership Name',
                        date: '2018-01-01 12:00:01',
                        total: 123,
                        email_count: 1,
                        phone_count: 1123,
                        conquest_count: 123,
                        dealer_count: 12
                    }
                ],
                companies: [],
                columns: [
                    {
                        field: 'name',
                        is_manager: true,
                        classes: ['recipient-name']
                    }, {
                        field: 'stats1',
                        classes: ['recipient-stats1']
                    }, {
                        field: 'stats2',
                        classes: ['recipient-stats2']
                    }, {
                        field: 'options',
                        is_manager_footer: true,
                        classes: ['recipient-options']
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
