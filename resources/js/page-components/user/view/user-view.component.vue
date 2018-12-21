<template>
    <wrapper ref="wrapperComponent" class="wrapper--user-view" :has-sidebar="true">
        <template slot="wrapper-sidebar">
            <div class="user-avatar">
                <button class="btn btn-default">
                    <span class="pm-font-edit-icon"></span>
                </button>
            </div>
            <form action="">
                <div class="row no-gutters">
                    <div class="col-12 mb-4">
                        <div class="form-group">
                            <label for="first_name" class="pmt-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" required>
                        </div>
                    </div>
                    <div class="col-12 mb-4">
                        <div class="form-group">
                            <label for="first_name" class="pmt-label">First Name</label>
                            <input type="text" class="form-control" id="last_name" required>
                        </div>
                    </div>
                    <div class="col-12 mb-4">
                        <div class="form-group">
                            <label for="first_name" class="pmt-label">First Name</label>
                            <input type="text" class="form-control" id="username" required>
                        </div>
                    </div>
                    <div class="col-12 mb-4">
                        <div class="form-group">
                            <label for="phone_number" class="pmt-label">Phone Number</label>
                            <input type="text" class="form-control" id="phone_number" required>
                        </div>
                    </div>
                </div>
            </form>
            <div class="actions">
                <button class="btn pm-btn pm-btn-purple btn-remove">
                    <trash-icon></trash-icon>
                    <span class="ml-1">Delete User</span>
                </button>
            </div>
        </template>
        <template slot="wrapper-content">
            <b-tabs pills card>
                <b-tab title="CAMPAIGN" active>
                    <list-campaign></list-campaign>
                </b-tab>
                <b-tab title="COMPANY">
                    <list-company></list-company >
                </b-tab>
            </b-tabs>
        </template>
    </wrapper>
</template>
<script>
    require("./user-view.scss");
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
