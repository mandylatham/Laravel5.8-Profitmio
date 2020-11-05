@extends('campaigns.base')

@section('head-styles')
    <link href="{{ asset('css/campaigns-facebook-campaign.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script>
        window.getFacebookCampaignDataUrl = @json(route('campaigns.facebook-campaign.data', ['campaign' => $campaign->id]));
    </script>
    <script src="{{ asset('js/campaigns-facebook-campaign.js') }}"></script>
@endsection

@section('main-content')
    <div id="campaign-facebook-campaign" v-cloak>
        <div class="row">
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-9">
                <div class="stat-cell">
                    <div class="stat-cell--title">Facebook Campaign</div>

                    <b-tabs content-class="mt-3">
                        <b-tab active>
                            <template v-slot:title>
                                <i class="far fa-chart-bar"></i>
                                Performance
                            </template>
                            <b-tabs pills vertical nav-wrapper-class="col-2" content-class="col-10">
                                <b-tab active>
                                    <template v-slot:title>
                                        <strong>
                                            @{{metrics.summary.action_result | numeral('0,0')}}
                                        </strong>
                                        <div style="font-size: 12px">
                                            Results: Link Clicks
                                        </div>
                                    </template>

                                    <div class="row pl-3">
                                        <div class="col-md-auto p-0">
                                            <div class="ref-color-chart-one"></div>
                                        </div>
                                        <div class="col-md-auto pl-2">
                                            Results: Link Clicks
                                            <strong class="text-dark pl-2">
                                                @{{metrics.summary.action_result | numeral('0,0')}}
                                            </strong>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-md-11">
                                            <line-chart
                                                :colors="['#5c3bbf']"
                                                :data="resultsChart.data"
                                                :dataset="{lineTension: 0}"
                                                :library="resultsChart.library"
                                                height="180px"
                                                v-if="!isLoading"
                                            >
                                            </line-chart>
                                        </div>
                                    </div>

                                </b-tab>
                                <b-tab lazy>
                                    <template v-slot:title>
                                        <strong>
                                            @{{metrics.summary.reach | numeral('0,0')}}
                                        </strong>
                                        <div style="font-size: 12px">
                                            People Reached
                                        </div>
                                    </template>

                                    <div class="row pl-3">
                                        <div class="col-md-auto p-0">
                                            <div class="ref-color-chart-one"></div>
                                        </div>
                                        <div class="col-md-auto pl-2">
                                            Reach
                                            <strong class="text-dark pl-2">
                                                @{{metrics.summary.reach | numeral('0,0')}}
                                            </strong>
                                        </div>
                                        <div class="col-md-auto p-0">
                                            <div class="ref-color-chart-two"></div>
                                        </div>
                                        <div class="col-md-auto pl-2">
                                            Frequency (cumulative)
                                            <strong class="text-dark pl-2">
                                                @{{metrics.summary.frequency | numeral('0.[00]')}}
                                            </strong>
                                        </div>
                                        <div class="col-md-auto pl-0">
                                            Impressions
                                            <strong class="text-dark pl-2">
                                                @{{metrics.summary.impressions | numeral('0,0')}}
                                            </strong>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-md-11">
                                            <line-chart
                                                :colors="['#5c3bbf']"
                                                :data="reachChart.data"
                                                :dataset="{lineTension: 0}"
                                                :library="reachChart.library"
                                                height="180px"
                                                v-if="!isLoading"
                                            >
                                            </line-chart>
                                            <line-chart
                                                :colors="['#32cdcd']"
                                                :data="frequencyCumulativeChart.data"
                                                :dataset="{lineTension: 0}"
                                                :library="frequencyCumulativeChart.library"
                                                height="100px"
                                                v-if="!isLoading"
                                            >
                                            </line-chart>
                                        </div>
                                    </div>

                                </b-tab>
                                <b-tab disabled>
                                    <template v-slot:title>
                                        <strong>
                                            @{{metrics.summary.frequency | numeral('0.[00]')}}
                                         </strong>
                                         <div style="font-size: 12px">
                                            Frequency
                                         </div>
                                    </template>
                                </b-tab>
                            </b-tabs>
                        </b-tab>
                        <b-tab>
                            <template v-slot:title>
                                <i class="fas fa-user-friends"></i>
                                Demographics
                            </template>

                            <div class="row">
                                <div class="col-md-auto pr-0">
                                    <b-dropdown class="options-results mr-2" variant='light'>
                                        <template v-slot:button-content>
                                            <div class="ref-color-chart-one"></div>
                                            <span class="text-muted">
                                                @{{getDatasetText(optDataset1)}}
                                            </span>
                                            <strong class="text-dark pl-2">
                                                @{{getSummaryValue(optDataset1) | numeral('0,0')}}
                                            </strong>
                                        </template>
                                        <b-dropdown-item
                                            href="#"
                                            @click="changeDataChartTo('dataset1', 'results')"
                                            :active="optDataset1 === 'results'"
                                            :disabled="optDataset2 === 'results'"
                                        >
                                            <i class="fas fa-check"></i>
                                            Results: Link Clicks
                                        </b-dropdown-item>
                                        <b-dropdown-item
                                            href="#"
                                            @click="changeDataChartTo('dataset1', 'reach')"
                                            :active="optDataset1 === 'reach'"
                                            :disabled="optDataset2 === 'reach'"
                                        >
                                            <i class="fas fa-check"></i>
                                            Reach
                                        </b-dropdown-item>
                                        <b-dropdown-item
                                            href="#"
                                            @click="changeDataChartTo('dataset1', 'impressions')"
                                            :active="optDataset1 === 'impressions'"
                                            :disabled="optDataset2 === 'impressions'"
                                        >
                                            <i class="fas fa-check"></i>
                                            Impressions
                                        </b-dropdown-item>
                                    </b-dropdown>
                                </div>
                                <div class="col-md-auto pl-0">
                                    <b-dropdown class="options-results" variant='light'>
                                        <template v-slot:button-content>
                                            <div class="ref-color-chart-two"></div>
                                            <span class="text-muted">
                                                @{{getDatasetText(optDataset2)}}
                                            </span>
                                            <strong class="text-dark pl-2">
                                                @{{getSummaryValue(optDataset2) | numeral('0,0')}}
                                            </strong>
                                        </template>
                                        <b-dropdown-item
                                            href="#"
                                            @click="changeDataChartTo('dataset2', 'results')"
                                            :active="optDataset2 === 'results'"
                                            :disabled="optDataset1 === 'results'"
                                        >
                                            <i class="fas fa-check"></i>
                                            Results: Link Clicks
                                        </b-dropdown-item>
                                        <b-dropdown-item
                                            href="#"
                                            @click="changeDataChartTo('dataset2', 'reach')"
                                            :active="optDataset2 === 'reach'"
                                            :disabled="optDataset1 === 'reach'"
                                        >
                                            <i class="fas fa-check"></i>
                                            Reach
                                        </b-dropdown-item>
                                        <b-dropdown-item
                                            href="#"
                                            @click="changeDataChartTo('dataset2', 'impressions')"
                                            :active="optDataset2 === 'impressions'"
                                            :disabled="optDataset1 === 'impressions'"
                                        >
                                            <i class="fas fa-check"></i>
                                            Impressions
                                        </b-dropdown-item>
                                    </b-dropdown>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 demographics-content">
                                    <div class="text-center">
                                        <i class="fas fa-female fa-lg"></i>

                                        <p>
                                            All Women @{{ selectedData.women.age }}
                                        </p>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="ref-color-chart-one small mr-2"></div>
                                        <strong>
                                            @{{ selectedData.women.dataset1_ratio }}%
                                            (@{{ selectedData.women.dataset1 | numeral('0,0')}})
                                        </strong>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="ref-color-chart-two small mr-2"></div>
                                        <strong>
                                            @{{ selectedData.women.dataset2_ratio }}%
                                            (@{{ selectedData.women.dataset2 | numeral('0,0')}})
                                        </strong>
                                    </div>
                                    <hr>
                                </div>
                                <div class="col-md-auto pr-0 demographics-content pl-0" style="position: relative;width: 100%;max-width: 31%;">
                                    <bar-chart
                                        :legend="false"
                                        :data="demographicsWomen.data"
                                        :library="demographicsWomen.library"
                                        height="200px"
                                        v-if="!isLoading"
                                    >
                                    </bar-chart>
                                </div>
                                <div class="col-md-auto p-0" style="margin-left: -9px;" v-on:mouseleave="onHoverData([])">
                                    <div class="age-title">
                                       <strong>Age</strong>
                                    </div>
                                    <div class="age-label" v-on:mouseover="onHoverData([{_index: 0}])">
                                        13-17
                                    </div>
                                    <div class="age-label" v-on:mouseover="onHoverData([{_index: 1}])">
                                        18-24
                                    </div>
                                    <div class="age-label" v-on:mouseover="onHoverData([{_index: 2}])">
                                        25-34
                                    </div>
                                    <div class="age-label" v-on:mouseover="onHoverData([{_index: 3}])">
                                        35-44
                                    </div>
                                    <div class="age-label" v-on:mouseover="onHoverData([{_index: 4}])">
                                        45-54
                                    </div>
                                    <div class="age-label" v-on:mouseover="onHoverData([{_index: 5}])">
                                        55-64
                                    </div>
                                    <div class="age-label" v-on:mouseover="onHoverData([{_index: 6}])">
                                        65+
                                    </div>
                                </div>
                                <div class="col-md-auto demographics-content pr-0 pl-0" style="position: relative;width: 100%;max-width: 31%;">
                                    <bar-chart
                                        :legend="false"
                                        :data="demographicsMen.data"
                                        :library="demographicsMen.library"
                                        height="200px"
                                        v-if="!isLoading"
                                    >
                                    </bar-chart>
                                </div>
                                <div class="col-md-2 demographics-content">
                                    <div class="text-center">
                                        <i class="fas fa-male fa-lg"></i>

                                        <p>
                                            All Men @{{ selectedData.men.age }}
                                        </p>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="ref-color-chart-one small mr-2"></div>
                                        <strong>
                                            @{{ selectedData.men.dataset1_ratio }}%
                                            (@{{ selectedData.men.dataset1 | numeral('0,0')}})
                                        </strong>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="ref-color-chart-two small mr-2"></div>
                                        <strong>
                                            @{{ selectedData.men.dataset2_ratio }}%
                                            (@{{ selectedData.men.dataset2 | numeral('0,0')}})
                                        </strong>
                                    </div>
                                    <hr>
                                </div>
                            </div>
                        </b-tab>
                    </b-tabs>
                </div>
                <div class="loader-spinner" v-if="isLoading">
                    <spinner-icon></spinner-icon>
                </div>
            </div>
        </div>
    </div>
@endsection
