import Vue from 'vue';
import '../../common';
import VueChartkick from 'vue-chartkick';
import Chart from 'chart.js';
import axios from 'axios';
import moment from 'moment';
import vueNumeralFilterInstaller from 'vue-numeral-filter';

const showZeroPlugin = {
    beforeRender: function (chartInstance) {
        if(chartInstance.config.type === 'horizontalBar'){
            var datasets = chartInstance.config.data.datasets;

            for (var i = 0; i < datasets.length; i++) {
                var meta = datasets[i]._meta;
                var metaData = meta[Object.keys(meta)[0]];
                var bars = metaData.data;

                for (var j = 0; j < bars.length; j++) {
                    var model = bars[j]._model;

                    if (metaData.type === "horizontalBar" && model.base === model.x) {
                        if(chartInstance.config.options.scales.xAxes[0].ticks.reverse) {
                            model.x = model.base - 2;
                        } else {
                            model.x = model.base + 2;
                        }
                    } else if (model.base === model.y) {
                        model.y = model.base - 2;
                    }
                }
            }
        }
    }
};
Chart.pluginService.register(showZeroPlugin);

Vue.use(vueNumeralFilterInstaller, { locale: 'en-gb' });
Vue.use(VueChartkick, {adapter: Chart});

window['app'] = new Vue({
    el: '#campaign-facebook-campaign',
    components: {
        'spinner-icon': require('./../../components/spinner-icon/spinner-icon').default,
    },
    data() {
        var self = this;
        return {
            isLoading: true,
            defaultActiontype: "link_click",
            optDataset1: 'results',
            optDataset2: 'reach',
            highestValue: 0,
            selectedData: {
                women: {
                    age: '',
                    dataset1: 0,
                    dataset1_ratio: 0,
                    dataset2: 0,
                    dataset2_ratio: 0,
                },
                men: {
                    age: '',
                    dataset1: 0,
                    dataset1_ratio: 0,
                    dataset2: 0,
                    dataset2_ratio: 0,
                }
            },
            totalDataWomen: {
                dataset1: 0,
                dataset1_ratio: 0,
                dataset2: 0,
                dataset2_ratio: 0,
            },
            totalDataMen: {
                dataset1: 0,
                dataset1_ratio: 0,
                dataset2: 0,
                dataset2_ratio: 0,
            },
            demographicsWomen: {
                data: [
                    {
                        name: 'dataset1',
                        data: [
                            ['13-17', 0],
                            ['18-24', 0],
                            ['25-34', 0],
                            ['35-44', 0],
                            ['45-54', 0],
                            ['55-64', 0],
                            ['65+', 0],
                        ],
                        dataset: {
                            xAxisID: 'axis-dataset1',
                            backgroundColor: '#5c3bbf',
                            borderColor: '#5c3bbf',
                            hoverBackgroundColor: '#5c3bbf',
                            hoverBorderColor: '#5c3bbf',
                        },
                    },
                    {
                        name: 'dataset2',
                        data: [
                            ['13-17', 0],
                            ['18-24', 0],
                            ['25-34', 0],
                            ['35-44', 0],
                            ['45-54', 0],
                            ['55-64', 0],
                            ['65+', 0],
                        ],
                        dataset: {
                            xAxisID: 'axis-dataset2',
                            backgroundColor: '#32cdcd',
                            borderColor: '#32cdcd',
                            hoverBackgroundColor: '#32cdcd',
                            hoverBorderColor: '#32cdcd',
                        }
                    }
                ],
                library: {
                    responsive: true,
                    maintainAspectRatio: false,
                    hover: {
                        intersect: false,
                        onHover: function(event, item) {
                            self.onHoverData(item);
                        }
                    },
                    tooltips: {
                        enabled: false
                    },
                    scales:{
                        xAxes: [
                            {
                                id: 'axis-dataset1',
                                type: 'linear',
                                gridLines: {
                                    drawOnChartArea: true,
                                    drawTicks: false
                                },
                                ticks: {
                                    reverse: true,
                                    display: false,
                                    stepSize: 1,
                                    min: 0,
                                    max: 1,
                                }
                            },
                            {
                                id: 'axis-dataset2',
                                type: 'linear',
                                gridLines: {
                                    drawOnChartArea: true,
                                    drawTicks: false
                                },
                                ticks: {
                                    reverse: true,
                                    display: false,
                                    stepSize: 1,
                                    min: 0,
                                    max: 1,
                                }
                            }],
                        yAxes: [{
                            gridLines: {
                                display: false
                            },
                            ticks: {
                                display: false
                            },
                            barThickness: 8,
                            position: 'right'
                        }]
                    }
                }
            },
            demographicsMen: {
                data: [
                    {
                        name: 'dataset1',
                        data: [
                            ['13-17', 0],
                            ['18-24', 0],
                            ['25-34', 0],
                            ['35-44', 0],
                            ['45-54', 0],
                            ['55-64', 0],
                            ['65+', 0],
                        ],
                        dataset: {
                            xAxisID: 'axis-dataset1',
                            backgroundColor: '#5c3bbf',
                            borderColor: '#5c3bbf',
                            hoverBackgroundColor: '#5c3bbf',
                            hoverBorderColor: '#5c3bbf',
                        },
                    },
                    {
                        name: 'dataset2',
                        data: [
                            ['13-17', 0],
                            ['18-24', 0],
                            ['25-34', 0],
                            ['35-44', 0],
                            ['45-54', 0],
                            ['55-64', 0],
                            ['65+', 0],
                        ],
                        dataset: {
                            xAxisID: 'axis-dataset2',
                            backgroundColor: '#32cdcd',
                            borderColor: '#32cdcd',
                            hoverBackgroundColor: '#32cdcd',
                            hoverBorderColor: '#32cdcd',
                        },
                    }
                ],
                library: {
                    responsive: true,
                    maintainAspectRatio: false,
                    hover: {
                        intersect: false,
                        onHover: function(event, item) {
                            self.onHoverData(item);
                        }
                    },
                    tooltips: {
                        enabled: false
                    },
                    scales:{
                        xAxes: [
                            {
                                id: 'axis-dataset1',
                                type: 'linear',
                                gridLines: {
                                    drawOnChartArea: true,
                                    drawTicks: false
                                },
                                ticks: {
                                    display: false,
                                    stepSize: 1,
                                    min: 0,
                                    max: 1,
                                }
                            },
                            {
                                id: 'axis-dataset2',
                                type: 'linear',
                                gridLines: {
                                    drawOnChartArea: true,
                                    drawTicks: false
                                },
                                ticks: {
                                    display: false,
                                    stepSize: 1,
                                    min: 0,
                                    max: 1,
                                }
                            }],
                        yAxes: [{
                            gridLines: {
                                display: false
                            },
                            ticks: {
                                display: false,
                            },
                            barThickness: 8,
                            position: 'right'
                        }]
                    }
                }
            },
            resultsChart: {
                data: {},
                library: {
                    elements: {
                        point:{
                            radius: 0
                        }
                    },
                    tooltips: {
                        intersect: false,
                    },
                    scales:{
                        xAxes: [{
                            gridLines: {
                                drawOnChartArea: true
                            },
                            ticks: {
                                padding: 3
                            }
                        }],
                        yAxes: [
                            {
                                position: 'right',
                                type: 'linear',
                                ticks: {
                                    beginAtZero: true,
                                    min: 0
                                }
                            }]
                    }
                }
            },
            reachChart: {
                data: {},
                library: {
                    elements: {
                        point:{
                            radius: 0
                        }
                    },
                    tooltips: {
                        intersect: false,
                    },
                    scales:{
                        xAxes: [{
                            gridLines: {
                                drawOnChartArea: true
                            },
                            ticks: {
                                padding: 3
                            }
                        }],
                        yAxes: [{
                            position: 'right',
                            type: 'linear',
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            },
            frequencyCumulativeChart: {
                data: {},
                library: {
                    elements: {
                        point:{
                            radius: 0
                        }
                    },
                    layout: {
                        padding: {
                            right: 2,
                        }
                    },
                    tooltips: {
                        intersect: false,
                    },
                    scales:{
                        xAxes: [{
                            gridLines: {
                                drawOnChartArea: true,
                                drawTicks: false
                            },
                            ticks: {
                                fontColor: '#fff'
                            }
                        }],
                        yAxes: [
                            {
                                position: 'right',
                                type: 'linear'
                            }]
                    }
                }
            },
            metrics: {
                demographics: [],
                demographics_summary: [],
                summary: {
                    actions: [],
                    action_result: "0",
                    frequency: "0.0",
                    impressions: "0",
                    reach: "0",
                }
            }
        }
    },
    mounted() {
        this.getFacebookCampaignDataUrl = window.getFacebookCampaignDataUrl;

        this.loadFacebookCampaignData();
    },
    methods: {
        loadFacebookCampaignData() {
            this.isLoading = true;
            axios
                .get(this.getFacebookCampaignDataUrl, {
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    this.metrics = {
                        ...response.data,
                        summary: {
                            ...this.metrics.summary,
                            ...response.data.summary
                        }
                    };

                    const dateStart = moment(this.metrics.summary.date_start);
                    const dateStop = moment(this.metrics.summary.date_stop);

                    if(this.metrics.summary.actions.length > 0){

                        const actionResult =  this.getDefaultAction(this.metrics.summary.actions);

                        if(actionResult){
                            this.metrics.summary.action_result = actionResult.value;
                        }

                    }

                    if(this.metrics.demographics && this.metrics.demographics.length > 0){
                        this.setDemographicDatasetData();
                    }

                    if(this.metrics.performance && this.metrics.performance.length > 0){
                        const resultsData = this.metrics.performance.map((item) => {
                            const action = item.actions ? this.getDefaultAction(item.actions) : null;
                            return {
                                date: item.date_start,
                                total: action ? action.value : 0
                            };
                        });
                        this.resultsChart.data = parseListToObject(resultsData, dateStart, dateStop);

                        const reachData = this.metrics.performance.map((item) => {
                            return {
                                date: item.date_start,
                                total: item.reach || 0
                            };
                        });
                        this.reachChart.data = parseListToObject(reachData, dateStart, dateStop);

                        let cumulative = 0;
                        const frequencyCumulativeData = this.metrics.performance.map((item) => {
                            const fre = +item.frequency || 0;
                            cumulative = +fre + cumulative;
                            return {
                                date: item.date_start,
                                total: cumulative.toFixed(2)
                            };
                        });
                        this.frequencyCumulativeChart.data = parseListToObject(frequencyCumulativeData, dateStart, dateStop);
                    }

                    this.isLoading = false;
                })
                .catch(error => {
                    console.log('error', error);
                    window.PmEvent.fire('errors.api', "Unable to get campaigns");
                });
        },
        onHoverData(item) {
            const selectedDataIndex = item.length > 0 ? item[0]._index : null;

            if(selectedDataIndex !== null){
                const totalWomenDataset1 = this.getTotalLabel(this.demographicsWomen.data[0].data[selectedDataIndex][1], this.getSummaryValue(this.optDataset1));
                const totalWomenDataset2 = this.getTotalLabel(this.demographicsWomen.data[1].data[selectedDataIndex][1], this.getSummaryValue(this.optDataset2));
                const totalMenDataset1 = this.getTotalLabel(this.demographicsMen.data[0].data[selectedDataIndex][1], this.getSummaryValue(this.optDataset1));
                const totalMenDataset2 = this.getTotalLabel(this.demographicsMen.data[1].data[selectedDataIndex][1], this.getSummaryValue(this.optDataset2));
                this.selectedData = {
                    women: {
                        age: this.demographicsWomen.data[1].data[selectedDataIndex][0],
                        dataset1: totalWomenDataset1,
                        dataset1_ratio: this.getRatioLabel(totalWomenDataset1, this.getSummaryValue(this.optDataset1)),
                        dataset2: totalWomenDataset2,
                        dataset2_ratio: this.getRatioLabel(totalWomenDataset2, this.getSummaryValue(this.optDataset2)),
                    },
                    men: {
                        age: this.demographicsMen.data[1].data[selectedDataIndex][0],
                        dataset1: totalMenDataset1,
                        dataset1_ratio: this.getRatioLabel(totalMenDataset1, this.getSummaryValue(this.optDataset1)),
                        dataset2:  totalMenDataset2,
                        dataset2_ratio: this.getRatioLabel(totalMenDataset2, this.getSummaryValue(this.optDataset2)),
                    }
                };
            } else {
                this.selectedData = {
                    women: {
                        age: '',
                        ...this.totalDataWomen
                    },
                    men: {
                        age: '',
                        ...this.totalDataMen
                    }
                };
            }
        },
        getDemographicSummary(item){
            let data = {
                dataset1: 0,
                dataset1_ratio: 0,
                dataset2: 0,
                dataset2_ratio: 0,
            };
            data.dataset1 = this.getDemographicValue(this.optDataset1, item);
            data.dataset1_ratio = this.getRatioLabel(data.dataset1, this.getSummaryValue(this.optDataset1));
            data.dataset2 = this.getDemographicValue(this.optDataset2, item);
            data.dataset2_ratio = this.getRatioLabel(data.dataset2, this.getSummaryValue(this.optDataset2));
            return data;
        },
        getRatioLabel(n1, n2){
            const { ratio, ratioRounded } = this.getRatio(n1, n2);
            return `${ratio < 1 && ratio !== 0  ? '<' : ''} ${ratioRounded}`;
        },
        getRatio(n1, n2){
            const ratio = (n1 * 100) / n2;
            return { ratio, ratioRounded: Math.round(ratio)};
        },
        getTotalLabel(n1, n2){
            const total = (n1 * n2) / 100;
            return total;
        },
        getDefaultAction(list) {
            if(!Array.isArray(list)) {
                return undefined;
            }
            return list.find(action => action.action_type === this.defaultActiontype);
        },
        getDatasetText(option){
            switch (option) {
                case 'results':
                    return 'Results: Link Clicks';
                case 'reach':
                    return 'Reach';
                case 'impressions':
                    return 'Impressions';
            }
        },
        getDemographicValue(option, item){
            switch (option) {
                case 'results':
                    const action = this.getDefaultAction(item.actions);
                    return action && action.value ? +action.value : 0;
                case 'reach':
                    return item.reach ? +item.reach : 0;
                case 'impressions':
                    return item.impressions ? +item.impressions : 0;
                default:
                    return 0;
            }
        },
        getSummaryValue(option){
            if(!this.metrics || !this.metrics.summary){
                return 0;
            }
            switch (option) {
                case 'results':
                    return this.metrics.summary.action_result || 0;
                case 'reach':
                    return this.metrics.summary.reach || 0;
                case 'impressions':
                    return this.metrics.summary.impressions || 0;
                default:
                    return 0;
            }
        },
        setHighestValue(value){
            if( value > this.highestValue){
                this.highestValue = value;
            }
        },
        setDemographicDatasetData(){
            this.highestValue = 0;
            let tmpChartDataWomenDataset1 = this.demographicsWomen.data[0].data;
            let tmpChartDataWomenDataset2 = this.demographicsWomen.data[1].data;
            let tmpChartDataMenDataset1 = this.demographicsMen.data[0].data;
            let tmpChartDataMenDataset2 = this.demographicsMen.data[1].data;
            this.metrics.demographics.forEach(item => {
                if(item.gender === "female"){
                    tmpChartDataWomenDataset2 = tmpChartDataWomenDataset2.map((data, index) => {
                        // Set dataset2 value
                        if(data[0] === item.age){
                            const value = this.getDemographicValue(this.optDataset2, item);
                            const { ratio } = this.getRatio(value, this.getSummaryValue(this.optDataset2));
                            data[1] = ratio;
                            this.setHighestValue(ratio);
                        }
                        // Set dataset1 value
                        if(data[0] === item.age && item.actions){
                            const value = this.getDemographicValue(this.optDataset1, item);
                            const { ratio } = this.getRatio(value, this.getSummaryValue(this.optDataset1));
                            tmpChartDataWomenDataset1[index][1] = ratio;
                            this.setHighestValue(ratio);
                        }
                        return data;
                    });
                }
                if(item.gender === "male"){
                    tmpChartDataMenDataset2 = tmpChartDataMenDataset2.map((data, index) => {
                        // Set dataset2 value
                        if(data[0] === item.age){
                            const value = this.getDemographicValue(this.optDataset2, item);
                            const { ratio } = this.getRatio(value, this.getSummaryValue(this.optDataset2));
                            data[1] = ratio;
                            this.setHighestValue(ratio);
                        }
                        // Set dataset1 value
                        if(data[0] === item.age && item.actions){
                            const value = this.getDemographicValue(this.optDataset1, item);
                            const { ratio } = this.getRatio(value, this.getSummaryValue(this.optDataset1));
                            tmpChartDataMenDataset1[index][1] = ratio;
                            this.setHighestValue(ratio);
                        }
                        return data;
                    });
                }
            });

            if(this.metrics.demographics_summary && this.metrics.demographics_summary.length > 0){
                this.metrics.demographics_summary.forEach(item => {
                    if(item.gender === "female"){
                        this.totalDataWomen = this.getDemographicSummary(item);
                    }
                    if(item.gender === "male"){
                        this.totalDataMen = this.getDemographicSummary(item);
                    }
                });
            }

            this.selectedData = {
                women: {
                    age: '',
                    ...this.totalDataWomen
                },
                men: {
                    age: '',
                    ...this.totalDataMen
                }
            };

            const tickStepSize = this.highestValue / 5;
            const tickMax = this.highestValue + tickStepSize;

            this.demographicsWomen.library.scales.xAxes[0].ticks.stepSize = tickStepSize;
            this.demographicsWomen.library.scales.xAxes[0].ticks.max = tickMax;
            this.demographicsWomen.library.scales.xAxes[1].ticks.stepSize = tickStepSize;
            this.demographicsWomen.library.scales.xAxes[1].ticks.max = tickMax;
            Vue.set(this.demographicsWomen.data, 0, { ...this.demographicsWomen.data[0], data: tmpChartDataWomenDataset1 });
            Vue.set(this.demographicsWomen.data, 1, { ...this.demographicsWomen.data[1], data: tmpChartDataWomenDataset2 });

            this.demographicsMen.library.scales.xAxes[0].ticks.stepSize = tickStepSize;
            this.demographicsMen.library.scales.xAxes[0].ticks.max = tickMax;
            this.demographicsMen.library.scales.xAxes[1].ticks.stepSize = tickStepSize;
            this.demographicsMen.library.scales.xAxes[1].ticks.max = tickMax;
            Vue.set(this.demographicsMen.data, 0, { ...this.demographicsMen.data[0], data: tmpChartDataMenDataset1 });
            Vue.set(this.demographicsMen.data, 1, { ...this.demographicsMen.data[1], data: tmpChartDataMenDataset2 });
        },
        changeDataChartTo(dataset, option) {
            if(dataset === 'dataset1'){
                this.optDataset1 = option;
            }
            if(dataset === 'dataset2'){
                this.optDataset2 = option;
            }
            this.setDemographicDatasetData();
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
        result[l.date] = +l.total;
    });
    return result;
}
