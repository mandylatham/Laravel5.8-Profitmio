<template>
    <div class="container-fluid">
        <div class="row align-items-end no-gutters">
            <div class="col-12 col-sm-5 col-lg-3">
                <div class="form-group filter--form-group">
                    <label>Filter By Company</label>
                    <v-select :options="companies" class="filter--v-select"></v-select>
                </div>
            </div>
            <div class="col-none col-sm-2 col-lg-6"></div>
            <div class="col-12 col-sm-5 col-lg-3">
                <input type="text" class="form-control filter--search-box" aria-describedby="search"
                       placeholder="Search" v-model="searchTerm">
            </div>
            <div class="col-12">
                <pm-responsive-table :rows="rows" :columns="columns">
                    <template slot="recipients_count" slot-scope="{row}">
                        <user-icon></user-icon>
                        <span class="ml-10">{{ row.recipients_count }}</span>
                    </template>
                    <template slot="phone_responses_count" slot-scope="{row}">
                        <phone-icon></phone-icon>
                        <span class="ml-10">{{ row.phone_responses_count }}</span>
                    </template>
                    <template slot="email_responses_count" slot-scope="{row}">
                        <mail-icon></mail-icon>
                        <span class="ml-10">{{ row.email_responses_count }}</span>
                    </template>
                    <template slot="text_responses_count" slot-scope="{row}">
                        <message-circle-icon></message-circle-icon>
                        <span class="ml-10">{{ row.text_responses_count }}</span>
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
                        classes: ['font-weight-bold', 'id-col']
                    }, {
                        field: 'name',
                        classes: ['name-col']
                    }, {
                        field: 'dealership.name',
                        classes: ['dealership-col']
                    }, {
                        field: 'agency.name',
                        classes: ['agency-col']
                    }, {
                        field: 'recipients_count',
                        classes: ['recipients-col']
                    }, {
                        field: 'phone_responses_count',
                        classes: ['phone-responses-col']
                    }, {
                        field: 'email_responses_count',
                        classes: ['email-responses-col']
                    }, {
                        field: 'text_responses_count',
                        classes: ['text-responses-col']
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
