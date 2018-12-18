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
                    <template slot="first_name" slot-scope="{row}">
                        {{ row.first_name }} {{ row.last_name }} <span class="font-weight-bold ml-1">(id: {{ row.id }})</span>
                    </template>
                    <template slot="email" slot-scope="{row}">
                        <mail-icon></mail-icon>
                        <span class="ml-10">{{ row.email }}</span>
                    </template>
                    <template slot="phone_number" slot-scope="{row}">
                        <phone-icon></phone-icon>
                        <span class="ml-10">{{ row.phone_number }}</span>
                    </template>
                    <template slot="status" slot-scope="{row}">
                        <status :active="row.active"></status>
                    </template>
                    <template slot="companies" slot-scope="{row}">
                        <span>-</span>
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
    require("./UserIndex.scss");
    export default {
        data() {
            return {
                rows: [
                    {
                        id: 1,
                        first_name: 'Carlos',
                        last_name: 'Arauz',
                        email: 'carauzs@gmail.com',
                        phone_number: '213213123',
                        active: true,
                        companies: [{
                            'id': 1,
                            'name': 'Company A',
                            'role': 'admin'
                        }]
                    }
                ],
                companies: [],
                columns: [
                    {
                        field: 'first_name',
                        is_manager: true,
                        classes: ['first-name-col']
                    }, {
                        field: 'email',
                        classes: ['email-col']
                    }, {
                        field: 'phone_number',
                        classes: ['phone-number-col']
                    }, {
                        field: 'status',
                        classes: ['status-col']
                    }, {
                        field: 'companies',
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
