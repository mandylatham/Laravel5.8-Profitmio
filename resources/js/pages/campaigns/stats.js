import Vue from 'vue';
import './../../common';
import VueChartkick from 'vue-chartkick';
import Chart from 'chart.js';
import moment from 'moment';
import Form from "../../common/form";
import DatePicker from 'vue2-datepicker';
import './../../filters/humanize-with-number.filter';

Vue.use(VueChartkick, {adapter: Chart});

window['app'] = new Vue({
    el: '#campaign-stats',
    components: {
        DatePicker
    },
    data: {
        averageTimeToOpen: 0,
        averageTimeToClose: 0,
        timeToCloseChartOptions: {
            legend: false,
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        const value = data['datasets'][0]['data'][tooltipItem['index']];
                        return `${value} recipient${value !== 1 ? 's' : ''}`;
                    }
                }
            },
            scales: {
                xAxes: [{
                    ticks: {
                        callback: function(value) {
                            return value + ' days';
                        }
                    }
                }]
            },
        },
        timeToOpenChartOptions: {
            legend: false,
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        const value = data['datasets'][0]['data'][tooltipItem['index']];
                        return `${value} recipient${value !== 1 ? 's' : ''}`;
                    }
                }
            },
            scales: {
                xAxes: [{
                    ticks: {
                        callback: function(value) {
                            return value + ' hours';
                        }
                    }
                }]
            },
        },
        negativeOutcomes: 0,
        positiveOutcomes: 0,
        leadsByMedia: [
            {
                name: '',
                data: {
                    sms: 15,
                    call: 24,
                    email: 25
                }
            }
        ],
        leadsByMediaDatasetOptions: {
            backgroundColor: ['#572E8D', '#e7f386', '#67a7cc'],
            borderColor: ['#492777', '#dced52', '#4493c0'],
        },
        leadsChartOptions: {
            tooltips: {
                mode: 'index',
                callbacks: {
                    title: function (tooltipItem, data) {
                        const label = data['labels'][tooltipItem[0]['index']];
                        return moment(label, 'YYYY-MM-DD').format('ddd DD, MMM');
                    }
                }
            },
            scales: {
                xAxes: [{
                    ticks: {
                        callback: function(value, index, values) {
                            return moment(value, 'YYYY-MM-DD').format('ddd DD, MMM');
                        }
                    }
                }]
            },
            position: {
                position: 'bottom'
            }
        },
        leadsOvertime: [],
        leadsClosedByTime: [],
        leadsOpenByTime: [],
        outcomes: {
            positive: {
                tags: {}
            },
            negative: {
                tags: {}
            },
        },
        range: [moment().subtract(1, 'month').toDate(), moment().toDate()],
        ranking: [],
        searchForm: new Form({
            start_date: '',
            end_date: ''
        })
    },
    mounted() {
        this.getStatsDataUrl = window.getStatsDataUrl;

        this.loadStatsData();
    },
    methods: {
        loadStatsData() {
            const startDate = moment(this.range[0]);
            const endDate = moment(this.range[1]);

            this.searchForm.start_date = startDate.format('YYYY-MM-DD');
            this.searchForm.end_date = endDate.format('YYYY-MM-DD');

            this.searchForm
                .get(this.getStatsDataUrl)
                .then(response => {
                    this.leadsOvertime = [
                        {
                            name: 'New leads',
                            color: '#990099',
                            data: parseListToObject(response.newLeadsOverTime, startDate, endDate)
                        },
                        {
                            name: 'Leads open',
                            color: '#3366CC',
                            data: parseListToObject(response.leadsClosedOverTime, startDate, endDate)
                        },
                        {
                            name: 'Leads closed',
                            color: '#DC3912',
                            data: parseListToObject(response.leadsOpenOverTime, startDate, endDate)
                        },
                        {
                            name: 'Appointments',
                            color: '#E68A00',
                            data: parseListToObject(response.appointmentsOverTime, startDate, endDate)
                        },
                        {
                            name: 'Callbacks',
                            color: '#109618',
                            data: parseListToObject(response.callbacksOverTime, startDate, endDate)
                        }
                    ];
                    this.averageTimeToOpen = response.averageTimeToOpen;
                    this.averageTimeToClose = response.averageTimeToClose;
                    console.log('response', response);
                    this.leadsByMedia = {
                        email: response.leadsByEmail,
                        phone: response.leadsByPhone,
                        sms: response.leadsBySms
                    };
                    this.outcomes = response.outcomes;
                    this.ranking = response.ranking;
                    this.leadsOpenByTime = response.leadsOpenByTime.map((total, idx) => {
                        let label = idx + 1;
                        if (idx === 0) {
                            label = '0-1';
                        }
                        if (idx === 11) {
                            label = '12+';
                        }
                        return [label, total];
                    });
                    this.leadsClosedByTime = response.leadsClosedByTime.map((total, idx) => {
                        let label = idx + 1;
                        if (idx === 0) {
                            label = '0-1';
                        }
                        if (idx === 6) {
                            label = '7+';
                        }
                        return [label, total];
                    });
                })
                .catch(error => {
                    console.log('error', error);
                    window.PmEvent.fire('errors.api', "Unable to get campaigns");
                });
        }
    }
});

function parseListToObject(list, startDate, endDate) {
    const result = {};
    const start = startDate.clone();
    const end = endDate.clone();
    while(start.isBefore(end) || start.isSame(end)) {
        result[start.format('YYYY-MM-DD')] = 0;
        start.add(1, 'days');
    }
    list.forEach(l => {
        result[l.date] = l.total;
    });
    return result;
}
