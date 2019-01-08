import Vue from 'vue';
import './../../common';
import moment from 'moment';
import Form from './../../common/form';
import 'vue-toastr-2/dist/vue-toastr-2.min.css'
import VueToastr2 from 'vue-toastr-2';
window.toastr = require('toastr');
Vue.use(VueToastr2);
// Chart Library
import VueChartkick from 'vue-chartkick'
import Chart from 'chart.js'
import {filter} from 'lodash';
import axios from "axios";

Vue.use(VueChartkick, {adapter: Chart});

window['app'] = new Vue({
    el: '#dashboard',
    components: {
        'campaign': require('./../../components/campaign/campaign'),
        'pm-pagination': require('./../../components/pm-pagination/pm-pagination'),
        'spinner-icon': require('./../../components/spinner-icon/spinner-icon'),
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
        selectedDate: moment().format('YYYY-MM-DD'),
        searchFormUrl: null,
        searchForm: new Form({
            company: null,
            q: null,
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
            this.searchForm.get(this.searchFormUrl)
                .then(response => {
                    this.campaigns = response.data;
                    this.searchForm.page = response.current_page;
                    this.searchForm.per_page = response.per_page;
                    this.total= response.total;
                    this.isLoading = false;
                })
                .catch(error => {
                    this.$toastr.error("Unable to get campaigns");
                });
        },
        onPageChanged(event) {
            this.searchForm.page = event.page;
            return this.fetchData();
        }
    }
});

window['sidebar'] = new Vue({
    el: '#sidebar',
    components: {
        'date-pick': require('./../../components/date-pick/date-pick')
    },
    computed: {
        eventsForDay: function () {
            const events = [];
            if (this.selectedDate) {
                this.calendarEvents.forEach(e => {
                    if (e.date === this.selectedDate) {
                        events.push(e);
                    }
                });
            }
            return events;
        }
    },
    data: {
        appointmentSelected: true,
        calendarEvents: [],
        dropsSelected: true,
        // eventsForDay: [],
        filter: 'appointment',
        selectedDate: moment().format('YYYY-MM-DD'),
    },
    methods: {
        parseDate: function (date, format) {
            return moment(date, format).toDate();
        },
        // onDaySelected: function (dateSelected) {
        //     const eventsForDay = [];
        //     this.calendarEvents.forEach(e => {
        //         if (e.date === dateSelected) {
        //             eventsForDay.push(e);
        //         }
        //     });
        //     this.eventsForDay = eventsForDay;
        // },
        fetchCalendarData: function () {
            const promises = [];
            if (this.filter === 'appointment') {
                promises.push(
                    axios
                        .get(window.appointmentsUrl, {
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            params: {
                                per_page: 100,
                                start_date: moment(this.selectedDate, 'YYYY-MM-DD').startOf('month').format('YYYY-MM-DD'),
                                end_date: moment(this.selectedDate, 'YYYY-MM-DD').endOf('month').format('YYYY-MM-DD')
                            },
                            data: null
                        })
                        .then(response => {
                            this.calendarEvents = response.data;
                        })
                );
            }
            if (this.filter === 'drop') {
                promises.push(
                    axios
                        .get(window.dropsUrl, {
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            params: {
                                per_page: 100,
                                start_date: moment(this.selectedDate, 'YYYY-MM-DD').startOf('month').format('YYYY-MM-DD'),
                                end_date: moment(this.selectedDate, 'YYYY-MM-DD').endOf('month').format('YYYY-MM-DD')
                            },
                            data: null
                        })
                        .then(response => {
                            this.calendarEvents = response.data;
                        })
                );
            }
            return Promise.all(promises);
        }
    },
    mounted() {
        this.fetchCalendarData();
    },
    watch: {
        selectedDate: function (newDate, oldDate) {
            newDate = moment(newDate, 'YYYY-MM-DD');
            oldDate = moment(oldDate, 'YYYY-MM-DD');
            // Month Changed, fetch new data for calendar
            if (newDate.format('MMYYYY') !== oldDate.format('MMYYYY')) {
                this.fetchCalendarData();
            }
        }
    }
});
