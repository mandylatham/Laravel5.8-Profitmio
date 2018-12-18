<template>
    <div class="container-fluid list-company-container">
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
                    <template slot="company-resume" slot-scope="{row}">
                        <div class="company-resume--container">
                            <div class="company-resume--image">
                                <img src="/img/img.png" alt="Image">
                            </div>
                            <div class="company-resume--title">
                                <strong>{{ row.name }}</strong>
                                <span>{{ row.address }}</span>
                            </div>
                        </div>
                    </template>
                    <template slot="company-role" slot-scope="{row}">
                        <div class="company-role--container">
                        <v-select :options="roles" class="filter--v-select"></v-select>
                        </div>
                    </template>
                    <template slot="company-phone" slot-scope="{row}">
                        <div class="company-phone--container">
                            <phone-icon></phone-icon>
                            <span>{{ row.phone_number }}</span>
                        </div>
                    </template>
                    <template slot="company-stats" slot-scope="{row}">
                        <div class="company-stats--container">
                            <volume-icon></volume-icon>
                            <span class="badge badge-pill">{{ row.active_campaigns }}</span>
                            <span class="campaign-stats--label">Active Campaigns</span>
                        </div>
                    </template>
                </pm-responsive-table>
            </div>
        </div>
    </div>
</template>
<script>
    require("./list-company.scss");
    export default {
        data() {
            return {
                rows: [
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        role: 'admin',
                        address: 'Sa. Soaf sjhdifops',
                        phone_number: '12831283',
                        active_campaigns: 3
                    },
                ],
                companies: [],
                columns: [
                    {
                        field: 'company-resume',
                        is_manager: true,
                        classes: ['company-resume']
                    }, {
                        field: 'company-role',
                        classes: ['company-role']
                    }, {
                        field: 'company-phone',
                        classes: ['company-phone']
                    }, {
                        field: 'company-stats',
                        classes: ['company-stats']
                    }
                ],
                roles: [],
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
