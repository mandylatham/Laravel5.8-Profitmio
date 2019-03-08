<template>
    <div class="container">
        <div class="row align-items-end no-gutters">
            <div class="col-12 col-sm-5 col-lg-3">
                <div class="form-group filter--form-group">
                    <label>Filter By Status</label>
                    <v-select :options="statuses" class="filter--v-select"></v-select>
                </div>
            </div>
            <div class="col-none col-sm-2 col-lg-6"></div>
            <div class="col-12 col-sm-5 col-lg-3">
                <input type="text" class="form-control filter--search-box" aria-describedby="search"
                       placeholder="Search" v-model="searchTerm">
            </div>
            <div class="col-12">
                <pm-responsive-table :rows="rows" :columns="columns">
                    <template slot="logo" slot-scope="{row}">
                        <img href="https://lorempixel.com/100x50" height="50px" width="100px">
                    </template>
                    <template slot="name" slot-scope="{row}">
                        {{ row.name }} <span class="font-weight-bold ml-1">(id: {{ row.id }})</span>
                    </template>
                    <template slot="type" slot-scope="{row}">
                        <mail-icon></mail-icon>
                        <span class="ml-10">{{ row.type }}</span>
                    </template>
                    <template slot="address" slot-scope="{row}">
                        <phone-icon></phone-icon>
                        <span class="ml-10">{{ row.address }}</span>
                    </template>
                    <template slot="status" slot-scope="{row}">
                        <status :active="row.active"></status>
                    </template>
                    <template slot="active_campaigns" slot-scope="{row}">
                        <span class="ml-10" v-for="campaign in row.active_campaigns">{{ campaign.name }} ({{ campaign.id}})</span>
                    </template>
                    <template slot="options" slot-scope="{row}">
                        <a class="pm-btn btn btn-transparent">
                            <home-icon class="custom-class"></home-icon>
                        </a>
                        <a class="pm-btn btn btn-transparent">
                            <edit-icon class="custom-class"></edit-icon>
                        </a>
                    </template>
                </pm-responsive-table>
            </div>
        </div>
    </div>
</template>
<script>
    require("./company-index.scss").default;
    export default {
        data() {
            return {
                rows: [
                    {
                        id: 1,
                        logo: 'http://placehold.it/50/100',
                        name: 'Carlos',
                        type: 'Arauz',
                        address: 'carauzs@gmail.com',
                        active: true,
                        active_campaigns: [{
                            'id': 1,
                            'name': 'Campaign A',
                            'responses': 217
                        },{
                            'id': 2,
                            'name': 'Campaign B',
                            'responses': 63
                        }]
                    },{
                        id: 2,
                        logo: 'http://placehold.it/50/100',
                        name: 'Carlos',
                        type: 'Arauz',
                        address: 'carauzs@gmail.com',
                        active: true,
                        active_campaigns: [{
                            'id': 1,
                            'name': 'Campaign A',
                            'responses': 217
                        },{
                            'id': 2,
                            'name': 'Campaign B',
                            'responses': 63
                        }]
                    },{
                        id: 3,
                        logo: 'http://placehold.it/50/100',
                        name: 'Carlos',
                        type: 'Arauz',
                        address: 'carauzs@gmail.com',
                        active: true,
                        active_campaigns: [{
                            'id': 1,
                            'name': 'Campaign A',
                            'responses': 217
                        },{
                            'id': 2,
                            'name': 'Campaign B',
                            'responses': 63
                        }]
                    }
                ],
                statuses: ['active', 'expired'],
                columns: [
                    {
                        field: 'logo',
                        classes: ['logo-col']
                    }, {
                        field: 'name',
                        is_manager: true,
                        classes: ['first-name-col']
                    }, {
                        field: 'type',
                        classes: ['email-col']
                    }, {
                        field: 'address',
                        classes: ['phone-number-col']
                    }, {
                        field: 'status',
                        classes: ['status-col']
                    }, {
                        field: 'active_campaigns',
                        classes: ['companies-col']
                    }, {
                        field: 'options',
                        is_manager_footer: true,
                        classes: ['options-col']
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
