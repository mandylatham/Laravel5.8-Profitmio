import Vue from 'vue';
import './../../common';

Vue.component('pm-responsive-table', require('./../../components/pm-responsive-table/pm-responsive-table'));

window.app = new Vue({
    el: '#campaign-index',
    data: {
        form: new Form({
            company: '',
            keywords: '',
        }),
        pageVariables: {
            searchTerm: '',
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
                    id: 2,
                    name: 'asdfasdfasdfasd',
                    dealership: {
                        name: 'Dealersfasdfasdfasdfhip'
                    },
                    agency: {
                        name: 'asdfasdf'
                    },
                    recipients_count: 123,
                    phone_responses_count: 12,
                    email_responses_count: 123,
                    text_responses_count: 12321
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
            columns: [
                {
                    field: 'name',
                    is_manager: true,
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
            formUrl: ''
        }
    },
    methods: {
        addPageVariables(name, value) {
            this.pageVariables = Object.assign({}, this.pageVariables, {[name]: value})
        },
        getPageVariable(name) {
            return this.pageVariables[name];
        },
        onSubmit() {
            this.form.post(this.pageVariables.formUrl);
        }
    }
});
