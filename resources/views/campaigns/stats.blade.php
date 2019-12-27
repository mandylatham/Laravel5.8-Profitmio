@extends('campaigns.base')

@section('head-styles')
    <link href="{{ asset('css/campaigns-stats.css') }}" rel="stylesheet">
@endsection

@section('body-script')
    <script>
        window.getStatsDataUrl = @json(route('campaigns.stats.data', ['campaign' => $campaign->id]));
    </script>
    <script src="{{ asset('js/campaigns-stats.js') }}"></script>
@endsection

@section('main-content')
    <div id="campaign-stats" v-cloak>
        <div class="row">
            <div class="col-12">
                <div class="stat-cell">
                    <div class="stat-cell--title">User Activity Ranking</div>
                    <table class="table table-striped">
                        <thead class="thead-pm">
                        <tr>
                            <th width="80px">Ranking</th>
                            <th width="40%">User</th>
                            <th width="15%">Lead Engagement</th>
                            <th width="15%">Leads Open</th>
                            <th width="15%">Closed: Pos</th>
                            <th width="15%">Closed: Neg</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(row, index) in ranking">
                            <td>@{{ index+1 }}</td>
                            <td>@{{ row.user.first_name }} @{{ row.user.last_name }}</td>
                            <td>@{{ row.percentage }} %</td>
                            <td>@{{ row.openLeads }}</td>
                            <td>@{{ row.closedLeads.positive.total }}</td>
                            <td>@{{ row.closedLeads.negative.total }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-12 col-xl-6">
                <div class="stat-cell stat-cell_by-media">
                    <div class="stat-cell--title">Time to open</div>
                    <column-chart
                        :library="timeToOpenChartOptions"
                        :data="leadsOpenByTime"
                        xtitle="Time" ytitle="Recipients"
                    ></column-chart>
                </div>
            </div>
            <div class="col-12 col-xl-6">
                <div class="stat-cell stat-cell_by-media">
                    <div class="stat-cell--title">Time to close</div>
                    <column-chart
                        :library="timeToCloseChartOptions"
                        :data="leadsClosedByTime"
                        xtitle="Time" ytitle="Recipients"
                    ></column-chart>
                </div>
            </div>
            <div class="col-12 col-xl-6">
                <div class="stat-cell">
                    <div class="stat-cell--title text-center">Average time to open</div>
                    <div class="stat-cell--value">@{{ averageTimeToOpen | humanizeWithNumber }}</div>
                </div>
            </div>
            <div class="col-12 col-xl-6">
                <div class="stat-cell">
                    <div class="stat-cell--title text-center">Average time to close</div>
                    <div class="stat-cell--value">@{{ averageTimeToClose | humanizeWithNumber }}</div>
                </div>
            </div>
            <div class="col-12 mb-3 mt-5">
                <div class="row">
                    <div class="col-6">
                        <h2>Lead Activity</h2>
                    </div>
                    <div class="col-6 d-flex align-items-center justify-content-end">
                        <date-picker :format="'MM/DD/YYYY'" v-model="range" type="date" range placeholder="Filter by range"></date-picker>
                        <button class="btn btn-primary ml-2" type="button" @click="loadStatsData()">Update</button>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="stat-cell">
                    <div class="stat-cell--title">Leads over time</div>
                    <column-chart :library="leadsChartOptions" :data="leadsOvertime"></column-chart>
                </div>
            </div>
            <div class="col-12 col-xl-6">
                <div class="stat-cell stat-cell_by-media">
                    <div class="stat-cell--title">Leads by media</div>
                    <column-chart :library="{'legend': false}" :dataset="leadsByMediaDatasetOptions" :data="leadsByMedia"></column-chart>
                </div>
            </div>
            <div class="col-12 col-xl-3 pb-4">
                <div class="stat-cell stat-cell_outcome">
                    <div class="stat-cell--title text-center">Positive Lead Outcomes</div>
                    <div class="stat-cell--value">
                        <i class="fa fa-thumbs-up mr-3"></i>
                        <span>@{{ outcomes.positive.total || 0 }}</span>
                    </div>
                    <i class="fas fa-chart-pie" v-if="Object.keys(outcomes.positive.tags).length === 0"></i>
                    <div class="stat-cell--subtitle text-center" v-if="Object.keys(outcomes.positive.tags).length > 0">Tags</div>
                    <pie-chart v-if="Object.keys(outcomes.positive.tags).length > 0" class="mt-2" :library="{legend: {position: 'bottom'}}" height="200px" :data="outcomes.positive.tags || []"></pie-chart>
                </div>
            </div>
            <div class="col-xl-3 pb-4">
                <div class="stat-cell stat-cell_outcome">
                    <div class="stat-cell--title text-center">Negative Lead Outcomes</div>
                    <div class="stat-cell--value">
                        <i class="fa fa-thumbs-down mr-3"></i>
                        <span>@{{ outcomes.negative.total || 0 }}</span>
                    </div>
                    <i class="fas fa-chart-pie" v-if="Object.keys(outcomes.negative.tags).length === 0"></i>
                    <div class="stat-cell--subtitle text-center" v-if="Object.keys(outcomes.negative.tags).length > 0">Tags</div>
                    <pie-chart v-if="Object.keys(outcomes.negative.tags).length > 0" class="mt-2" :library="{legend: {position: 'bottom'}}" height="200px" :data="outcomes.negative.tags"></pie-chart>
                </div>
            </div>
        </div>
    </div>
@endsection
