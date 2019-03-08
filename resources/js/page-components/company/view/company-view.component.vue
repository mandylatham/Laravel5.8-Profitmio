<template>
    <div id="wrapper" class="wrapper--user-view" :class="{'open': asideOpen}">
        <div class="wrapper-aside">
            <a href="javascript:;" class="wrapper-aside-close-control" @click="asideOpen = false;">
                <x-icon></x-icon>
            </a>
            <a href="javascript:;" class="wrapper-aside-control" @click="asideOpen = !asideOpen">
                <menu-icon></menu-icon>
            </a>
            <div class="user-avatar">
                <button class="btn btn-default">
                    <edit-icon></edit-icon>
                </button>
            </div>
            <form action="">
                <div class="row no-gutters">
                    <div class="col-12 mb-4">
                        <label for="first_name" class="pmt-label">First Name</label>
                        <div class="input-group pmt-input-group-prepend">
                            <input type="text" class="form-control" id="first_name" required>
                            <div class="input-group-prepend">
                                <edit-icon></edit-icon>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mb-4">
                        <label for="last_name" class="pmt-label">Last Name</label>
                        <div class="input-group pmt-input-group-prepend">
                            <input type="text" class="form-control" id="last_name" required>
                            <div class="input-group-prepend">
                                <edit-icon></edit-icon>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mb-4">
                        <label for="username" class="pmt-label">Username</label>
                        <div class="input-group pmt-input-group-prepend">
                            <input type="text" class="form-control" id="username" required>
                            <div class="input-group-prepend">
                                <edit-icon></edit-icon>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mb-4">
                        <label for="phone_number" class="pmt-label">Phone Number</label>
                        <div class="input-group pmt-input-group-prepend">
                            <input type="text" class="form-control" id="phone_number" required>
                            <div class="input-group-prepend">
                                <edit-icon></edit-icon>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="actions">
                <button class="btn btn-remove">
                    <trash-icon></trash-icon>
                    <span class="ml-1">Delete User</span>
                </button>
            </div>
        </div>
        <div class="wrapper-content">
            <b-tabs pills card>
                <b-tab title="CAMPAIGN" active>
                    <list-campaign></list-campaign>
                </b-tab>
                <b-tab title="COMPANY">
                    <list-company></list-company >
                </b-tab>
            </b-tabs>
        </div>
    </div>
</template>
<script>
    require("./company-view.scss").default;
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
                asideOpen: false,
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
