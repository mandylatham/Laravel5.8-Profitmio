@if ($campaigns->count() > 0)
    @foreach ($campaigns as $campaign)
        <div class="panel panel-bordered animation-scale-up" style="animation-fill-mode: backwards; animation-duration: 250ms; animation-delay: 850ms;">
            <div class="panel-heading
                        @if ($campaign->status == 'Complete')
                    bg-light-green-100
@endif
                    ">
                <div class="float-right p-15">
                    <small>{{ $campaign->status }}</small>
                </div>
                <h3 class="panel-title">
                    <small>Campaign {{ $campaign->id}}</small><br>
                    {{ $campaign->name}}<br>
                </h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-6">
                        <button class="panel-action btn btn-info form-control data-link bg-blue-500" data-url="{{ secure_url('campaign/' . $campaign->id . '/response-console') }}">
                            <i class=" icon fa-terminal" style="" aria-hidden="true"></i>
                            <span style="">Go To Console</span>
                        </button>
                        <a href="{{ secure_url('campaign/' . $campaign->id . '/response-console/unread') }}"
                           class="card card-block card-bordered p-300 mt-10
                                    @if ($campaign->unread >= 0 and $campaign->unread < 50)
                                   bg-light-green-600
@elseif ($campaign->unread >= 50 && $campaign->unread < 100)
                                   bg-orange-500
@elseif ($campaign->unread >= 100 && $campaign->unread < 150)
                                   bg-deep-orange-500
@elseif ($campaign->unread >= 150)
                                   bg-red-500
@else
                                   bg-red-900
@endif
                                   ">
                            <div class="card-watermark darker font-size-60 m-15">
                                <i class="icon md-comment-text-alt" aria-hidden="true"></i>
                            </div>
                            <div class="counter counter-inverse counter-md text-left">
                                <div class="counter-number-group">
                                    <span class="counter-number">{{ number_format($campaign->unread) }}</span>
                                </div>
                                <div class="counter-label text-uppercase">Unread</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-6">
                        <table class="table table-condensed font-size-10 table-hover">
                            <tbody>
                            <tr class="data-link" data-url="{{ secure_url('campaign/' . $campaign->id . '/response-console/calls') }}">
                                <th>Calls</th>
                                <td>{{ number_format($campaign->phones) }}</td>
                            </tr>
                            <tr class="data-link" data-url="{{ secure_url('campaign/' . $campaign->id . '/response-console/email') }}">
                                <th>Emails</th>
                                <td>{{ number_format($campaign->emails) }}</td>
                            </tr>
                            <tr class="data-link" data-url="{{ secure_url('campaign/' . $campaign->id . '/response-console/sms') }}">
                                <th>SMS</th>
                                <td>{{ number_format($campaign->texts) }}</td>
                            </tr>
                            <tr>
                                <th>Appointments</th>
                                <td>{{ $appointmentCounts->has($campaign->id) ? number_format($appointmentCounts->get($campaign->id)->appointments) : 0 }}</td>
                            </tr>
                            <tr>
                                <th>Callbacks</th>
                                <td>{{ $appointmentCounts->has($campaign->id) ? number_format($appointmentCounts->get($campaign->id)->callbacks) : 0 }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="exampleMorrisDonut"></div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@else
    <div class="panel panel-info">
        <div class="panel-heading">
            <h5 class="panel-title">No Campaigns Found</h5>
        </div>
    </div>
@endif
