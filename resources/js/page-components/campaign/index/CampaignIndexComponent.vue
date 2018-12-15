<template>
    <div class="container-fluid">
        <div class="row align-items-end no-gutters">
            <div class="col-12 col-sm-5">
                <div class="form-group filter--form-group">
                    <label>Filter By Company</label>
                    <v-select :options="companies" class="filter--v-select"></v-select>
                </div>
            </div>
            <div class="col-none col-sm-2"></div>
            <div class="col-12 col-sm-5">
                <input type="text" class="form-control filter--search-box" aria-describedby="search"
                       placeholder="Search" v-model="searchTerm">
            </div>
            <div class="col-12">
                <pm-responsive-table :rows="rows" :columns="columns">
                    <template slot="options" slot-scope="{row}">
                        <button class="btn">A</button>
                    </template>
                </pm-responsive-table>
            </div>
        </div>
    </div>
</template>
<script>
    require("./CampaignIndex.scss");
    export default {
        data() {
            return {
                rows: [
                    {
                        id: 1,
                        name: 'name',
                        dealership: {
                            name: 'Dealership'
                        },
                        agency: {
                            name: 'Agency'
                        },
                        recipients_count: 1,
                        phone_responses_count: 1,
                        email_responses_count: 1,
                        text_responses_count: 1
                    },
                    {
                        id: 1,
                        name: 'name',
                        dealership: {
                            name: 'Dealership'
                        },
                        agency: {
                            name: 'Agency'
                        },
                        recipients_count: 1,
                        phone_responses_count: 1,
                        email_responses_count: 1,
                        text_responses_count: 1
                    },
                    {
                        id: 1,
                        name: 'name',
                        dealership: {
                            name: 'Dealership'
                        },
                        agency: {
                            name: 'Agency'
                        },
                        recipients_count: 1,
                        phone_responses_count: 1,
                        email_responses_count: 1,
                        text_responses_count: 1
                    }
                ],
                companies: [],
                columns: [
                    {
                        field: 'id',
                        is_manager: true,
                        classes: ['font-weight-bold']
                    }, {
                        field: 'name'
                    }, {
                        field: 'dealership.name',
                        html: true
                    }, {
                        field: 'agency.name'
                    }, {
                        field: 'recipients_count'
                    }, {
                        field: 'phone_responses_count'
                    }, {
                        field: 'email_responses_count'
                    }, {
                        field: 'text_responses_count'
                    }, {
                        field: 'options',
                        is_manager_footer: true
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
