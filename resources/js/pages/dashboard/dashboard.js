import Vue from 'vue';
import './../../common';
import moment from 'moment';
import Form from './../../common/form';
import './../../filters/m-utc-parse.filter';
import './../../filters/m-format-localized.filter';
// Chart Library
import VueChartkick from 'vue-chartkick'
import Chart from 'chart.js'
import {filter} from 'lodash';
import axios from "axios";

Vue.use(VueChartkick, {adapter: Chart});

window['app'] = new Vue({
    el: '#dashboard',
    components: {
        'campaign': require('./../../components/campaign/campaign').default,
        'pm-pagination': require('./../../components/pm-pagination/pm-pagination').default,
        'spinner-icon': require('./../../components/spinner-icon/spinner-icon').default,
    },
    computed: {
        countActiveCampaigns: function () {
            return filter(this.campaigns, {
                status: 'Active'
            }).length;
        },
        countInactiveCampaigns: function () {
            return filter(this.campaigns, item => {
                return item.status !== 'Active';
            }).length;
        },
        pagination: function () {
            return {
                page: this.searchForm.page,
                per_page: this.searchForm.per_page,
                total: this.total
            };
        }
    },
    data: {
        companies: [],
        companySelected: null,
        selectedDate: moment().format('YYYY-MM-DD'),
        searchFormUrl: null,
        searchForm: new Form({
            company: null,
            q: localStorage.getItem('dashboardIndexQ'),
            page: 1,
            per_page: 15,
        }),
        isLoading: true,
        total: null,
        campaigns: [],
        searchTerm: '',
        tableOptions: {
            mobile: 'lg'
        },
        formUrl: ''
    },
    mounted() {
        this.searchFormUrl = window.searchFormUrl;
        this.searchForm.q = window.q;

        axios
            .get(window.getCompanyUrl, {
                headers: {
                    'Content-Type': 'application/json'
                },
                params: {
                    per_page: 100
                },
                data: null
            })
            .then(response => {
                this.companies = response.data.data;
            });

        this.fetchData();
    },
    methods: {
        onCompanySelected() {
            this.searchForm.page = 1;
            return this.fetchData();
        },
        parseDate: function (date, format) {
            return moment(date, format).toDate();
        },
        fetchData() {
            this.isLoading = true;
            if (this.searchForm.q) {
                localStorage.setItem('dashboardIndexQ', this.searchForm.q);
            } else {
                localStorage.removeItem('dashboardIndexQ');
            }
            this.searchForm.get(this.searchFormUrl)
                .then(response => {
                    this.campaigns = response.data;
                    this.searchForm.page = response.current_page;
                    this.searchForm.per_page = response.per_page;
                    this.total= response.total;
                    this.isLoading = false;
                })
                .catch(error => {
                    window.PmEvent.fire('errors.api', "Unable to get campaigns");
                });
        },
        onPageChanged(event) {
            this.searchForm.page = event.page;
            return this.fetchData();
        }
    }
});

window['sidebar'] = new Vue({
    el: '#sidebar--container',
    components: {
        'date-pick': require('./../../components/date-pick/date-pick').default,
        'spinner-icon': require('./../../components/spinner-icon/spinner-icon').default
    },
    data: {
        loading: true,
        appointmentSelected: true,
        calendarEvents: [],
        monthEvents: [],
        dropsSelected: true,
        filter: 'appointment',
        selectedDate: moment().format('YYYY-MM-DD'),
    },
    filters: {
        prettyDate: function (value) {
            return moment(value, 'YYYY-MM-DD HH:mm').format('MM-DD-YYYY HH:mm');
        }
    },
    methods: {
        parseDate: function (date, format) {
            return moment(date, format).toDate();
        },
        fetchMonthEvents: function () {
            let url = '';
            let dateProp = '';
            if (this.filter === 'appointment') {
                url = window.appointmentsUrl;
                dateProp = 'appointment_at';
            } else if (this.filter === 'drop') {
                url = window.dropsUrl;
                dateProp = 'send_at';
            }
            return axios
                .get(url, {
                    params: {
                        per_page: 1000,
                        start_date: moment(this.selectedDate, 'YYYY-MM-DD').startOf('month').startOf('week').format('YYYY-MM-DD'),
                        end_date: moment(this.selectedDate, 'YYYY-MM-DD').endOf('month').endOf('week').format('YYYY-MM-DD')
                    },
                    data: null
                })
                .then(response => {
                    response.data.data.forEach(d => {
                        d.date = moment(d[dateProp], 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD');
                    });
                    this.monthEvents = response.data.data;
                });
        },
        fetchDayEvents: function () {
            this.loading = true;
            let url = '';
            let dateProp = '';
            if (this.filter === 'appointment') {
                url = window.appointmentsUrl;
                dateProp = 'appointment_at';
            } else if (this.filter === 'drop') {
                url = window.dropsUrl;
                dateProp = 'send_at';
            }
            return axios
                .get(url, {
                    params: {
                        per_page: 1000,
                        start_date: moment(this.selectedDate, 'YYYY-MM-DD').startOf('week').format('YYYY-MM-DD'),
                        end_date: moment(this.selectedDate, 'YYYY-MM-DD').endOf('week').format('YYYY-MM-DD')
                    },
                    data: null
                })
                .then(response => {
                    this.loading = false;
                    response.data.data.forEach(d => {
                        d.date = moment(d[dateProp], 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD');
                    });
                    this.calendarEvents = response.data.data;
                }, () => {
                    this.loading = false;
                });
        },
        selectWeek: function () {
            const date = moment(this.selectedDate, 'YYYY-MM-DD');
            const row = document.querySelector('.vdpRow.selected');
            if (row) {
                row.classList.remove('selected');
            }
            document.querySelector('[data-id="' + date.format('YYYY-M-D') + '"]').parentNode.classList.add('selected');
        }
    },
    mounted() {
        this.fetchDayEvents();
        this.fetchMonthEvents();
        this.selectWeek();
    },
    watch: {
        selectedDate: function (newDate, oldDate) {
            newDate = moment(newDate, 'YYYY-MM-DD');
            oldDate = moment(oldDate, 'YYYY-MM-DD');
            if (newDate.format('DDMMYYYY') !== oldDate.format('DDMMYYYY')) {
                if (newDate.format('MMYYYY') !== oldDate.format('MMYYYY')) {
                    this.fetchMonthEvents();
                }
                if (newDate.week() !== oldDate.week()) {
                    // Don't remove setTimeout, it waits the calendar to render the next month
                    setTimeout(() => {
                        this.fetchDayEvents();
                        this.selectWeek();
                    }, 0);
                }
            }
        }
    }
});
