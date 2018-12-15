<template>
    <div class="container-fluid">
        <div class="row align-items-end no-gutters">
            <div class="col-3">
                <div class="form-group mb-0 filter--form-group">
                    <label>Filter By Company</label>
                    <v-select label="countryName" :options="countries" class="filter--v-select"></v-select>
                </div>
            </div>
            <div class="col-6"></div>
            <div class="col-3">
                <input type="text" class="form-control filter--search-box" aria-describedby="search" placeholder="Search" v-model="searchTerm">
            </div>
            <div class="col-12">
                <vue-good-table
                        class="pm-datatable"
                        mode="remote"
                        :columns="columns"
                        :rows="rows"
                        :totalRows="totalRecords"
                        :search-options="searchOptions"
                />
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                countries: [{
                    countryCode: "CA",
                    countryName: "Canada"
                }],
                columns: [
                    {
                        label: 'Id',
                        field: 'id',
                        sortable: true
                    },
                    {
                        label: 'Name',
                        field: 'name',
                        sortable: true
                    },
                    {
                        label: 'Agency',
                        field: 'agency.name',
                        sortable: true
                    },
                    {
                        label: 'Dealership',
                        field: 'dealership.name',
                        sortable: true
                    },
                    {
                        label: 'Targets',
                        field: 'recipients_count',
                        sortable: true
                    },
                    {
                        label: 'Phones',
                        field: 'phone_responses_count',
                        sortable: true
                    },
                    {
                        label: 'Emails',
                        field: 'email_responses_count',
                        sortable: true
                    },
                    {
                        label: 'SMS/MMS',
                        field: 'text_responses_count',
                        sortable: true
                    }, {
                        label: 'Options',
                        field: this.optionsField
                    }
                ],
                optionsFilter() {

                },
                rows: [{
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
                }],
                searchTerm: '',
                searchOptions: {
                    enabled: true,
                    externalQuery: 'searchTerm'
                },
                serverParams: {
                    columnFilters: {
                    },
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
