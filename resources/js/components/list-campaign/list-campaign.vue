<template>
    <div class="container-fluid list-campaign-container">
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
                    <template slot="campaign-resume" slot-scope="{row}">
                        <div class="campaign-resume--container">
                            <div class="campaign-resume--status">
                                <status :active="true" no-label></status>
                            </div>
                            <div class="campaign-resume--title">
                                <strong>Campaign {{ row.id }}</strong>
                                <span>{{ row.name }}</span>
                            </div>
                        </div>
                    </template>
                    <template slot="campaign-asset" slot-scope="{row}">
                        <img src="/img/img.png" alt="Image">
                    </template>
                    <template slot="campaign-date" slot-scope="{row}">
                        <div class="campaign-date--label">End Date:</div>
                        <div class="campaign-date--value">{{ row.end_date | amDateFormat('DD.MM.YYYY') }}</div>
                    </template>
                    <template slot="campaign-chart" slot-scope="{row}">
                        <div class="campaign-chart--container">
                            <div class="campaign-chart--chart">
                                <img src="/img/pie.png" alt="Image">
                            </div>
                            <div class="campaign-chart--labels">
                                <span class="sms">sms</span>
                                <span class="call">call</span>
                                <span class="email">email</span>
                            </div>
                        </div>
                    </template>
                </pm-responsive-table>
            </div>
        </div>
    </div>
</template>
<script>
    require("./list-campaign.scss");
    export default {
        data() {
            return {
                rows: [
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                    {
                        id: 539,
                        name: 'asdfasdf asdf sadf sad',
                        end_date: '2018-01-01'
                    },
                ],
                companies: [],
                columns: [
                    {
                        field: 'campaign-resume',
                        is_manager: true,
                        classes: ['campaign-resume']
                    }, {
                        field: 'campaign-asset',
                        classes: ['campaign-asset']
                    }, {
                        field: 'campaign-date',
                        classes: ['campaign-date']
                    }, {
                        field: 'campaign-chart',
                        classes: ['campaign-chart']
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
