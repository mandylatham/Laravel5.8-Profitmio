@extends('layouts.remark')

@section('header')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="{{ secure_url('vendor/bootstrap-datepicker/bootstrap-datepicker.css') }}">
    <link rel="stylesheet" href="{{ secure_url('vendor/layout-grid/layout-grid.min.css?v2.2.0') }}">
    <link rel="stylesheet" href="{{ secure_url('vendor/icheck/icheck.css') }}">
    <link rel="stylesheet" href="{{ secure_url('vendor/morris/morris.css') }}">
    <style type="text/css" media="all">
        .fc-event, .fc-event-dot {
            background-color: #eafde1;
        }
        .data-link {
            cursor: pointer;
        }
        a.card:hover {
            text-decoration:none;
            color: lightgreen;
            border: 1px solid darkgreen;
            text-shadow: 1px 1px 4px green;
        }
        .lt-body {
            background-color: #fff;
            overflow-y: auto;
        }
.loader2 {
  color: blue; //#ffffff;
  font-size: 90px;
  text-indent: -9999em;
  overflow: hidden;
  width: 1em;
  height: 1em;
  border-radius: 50%;
  margin: 72px auto;
  position: relative;
  -webkit-transform: translateZ(0);
  -ms-transform: translateZ(0);
  transform: translateZ(0);
  -webkit-animation: load6 1.7s infinite ease, round 1.7s infinite ease;
  animation: load6 1.7s infinite ease, round 1.7s infinite ease;
}
@-webkit-keyframes load6 {
  0% {
    box-shadow: 0 -0.83em 0 -0.4em, 0 -0.83em 0 -0.42em, 0 -0.83em 0 -0.44em, 0 -0.83em 0 -0.46em, 0 -0.83em 0 -0.477em;
  }
  5%,
  95% {
    box-shadow: 0 -0.83em 0 -0.4em, 0 -0.83em 0 -0.42em, 0 -0.83em 0 -0.44em, 0 -0.83em 0 -0.46em, 0 -0.83em 0 -0.477em;
  }
  10%,
  59% {
    box-shadow: 0 -0.83em 0 -0.4em, -0.087em -0.825em 0 -0.42em, -0.173em -0.812em 0 -0.44em, -0.256em -0.789em 0 -0.46em, -0.297em -0.775em 0 -0.477em;
  }
  20% {
    box-shadow: 0 -0.83em 0 -0.4em, -0.338em -0.758em 0 -0.42em, -0.555em -0.617em 0 -0.44em, -0.671em -0.488em 0 -0.46em, -0.749em -0.34em 0 -0.477em;
  }
  38% {
    box-shadow: 0 -0.83em 0 -0.4em, -0.377em -0.74em 0 -0.42em, -0.645em -0.522em 0 -0.44em, -0.775em -0.297em 0 -0.46em, -0.82em -0.09em 0 -0.477em;
  }
  100% {
    box-shadow: 0 -0.83em 0 -0.4em, 0 -0.83em 0 -0.42em, 0 -0.83em 0 -0.44em, 0 -0.83em 0 -0.46em, 0 -0.83em 0 -0.477em;
  }
}
@keyframes load6 {
  0% {
    box-shadow: 0 -0.83em 0 -0.4em, 0 -0.83em 0 -0.42em, 0 -0.83em 0 -0.44em, 0 -0.83em 0 -0.46em, 0 -0.83em 0 -0.477em;
  }
  5%,
  95% {
    box-shadow: 0 -0.83em 0 -0.4em, 0 -0.83em 0 -0.42em, 0 -0.83em 0 -0.44em, 0 -0.83em 0 -0.46em, 0 -0.83em 0 -0.477em;
  }
  10%,
  59% {
    box-shadow: 0 -0.83em 0 -0.4em, -0.087em -0.825em 0 -0.42em, -0.173em -0.812em 0 -0.44em, -0.256em -0.789em 0 -0.46em, -0.297em -0.775em 0 -0.477em;
  }
  20% {
    box-shadow: 0 -0.83em 0 -0.4em, -0.338em -0.758em 0 -0.42em, -0.555em -0.617em 0 -0.44em, -0.671em -0.488em 0 -0.46em, -0.749em -0.34em 0 -0.477em;
  }
  38% {
    box-shadow: 0 -0.83em 0 -0.4em, -0.377em -0.74em 0 -0.42em, -0.645em -0.522em 0 -0.44em, -0.775em -0.297em 0 -0.46em, -0.82em -0.09em 0 -0.477em;
  }
  100% {
    box-shadow: 0 -0.83em 0 -0.4em, 0 -0.83em 0 -0.42em, 0 -0.83em 0 -0.44em, 0 -0.83em 0 -0.46em, 0 -0.83em 0 -0.477em;
  }
}
@-webkit-keyframes round {
  0% {
    -webkit-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@keyframes round {
  0% {
    -webkit-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
    </style>
@endsection

@section('content')
    <div class="page">
        <div class="page-header container-fluid">
            <div class="row-fluid">
                <div class="col-md-12">
                    <h3 class="page-title text-default"></h3>
                </div>
            </div>
        </div>
        <div class="page-content container-fluid">
            <div class="row">
                <div class="col-md-2">
                    <div class="card card-block p-300 bg-indigo-600">
                        <div class="card-watermark darker font-size-60 m-15"><i class="icon md-account-circle" aria-hidden="true"></i></div>
                        <div class="counter counter-inverse counter-md text-left">
                            <span class="counter-number">{{ number_format($stats->responses) }}</span>
                            <div class="counter-label text-uppercase">responses</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card card-block p-300 bg-light-blue-600">
                        <div class="card-watermark darker font-size-60 m-15"><i class="icon md-phone-in-talk" aria-hidden="true"></i></div>
                        <div class="counter counter-inverse counter-md text-left">
                            <div class="counter-number-group">
                                <span class="counter-number">{{ number_format($stats->calls) }}</span>
                            </div>
                            <div class="counter-label text-uppercase">calls</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card card-block p-300 bg-light-blue-600">
                        <div class="card-watermark darker font-size-60 m-15">
                            <i class="icon md-email" aria-hidden="true"></i>
                        </div>
                        <div class="counter counter-inverse counter-md text-left">
                            <span class="counter-number">{{ number_format($stats->emails) }}</span>
                            <div class="counter-label text-uppercase">emails</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card card-block p-300 bg-light-blue-600">
                        <div class="card-watermark darker font-size-60 m-15">
                            <i class="icon md-comment-text-alt" aria-hidden="true"></i>
                        </div>
                        <div class="counter counter-inverse counter-md text-left">
                            <div class="counter-number-group">
                                <span class="counter-number">{{ number_format($stats->sms) }}</span>
                            </div>
                            <div class="counter-label text-uppercase">sms</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card card-block p-300 bg-green-700">
                        <div class="card-watermark darker font-size-60 m-15">
                            <i class="icon md-calendar" aria-hidden="true"></i>
                        </div>
                        <div class="counter counter-inverse counter-md  text-left">
                            <div class="counter-number-group">
                                <span class="counter-number">{{ number_format($stats->appointments) }}</span>
                                <span class="counter-number-related text-capitalize"></span>
                            </div>
                            <div class="counter-label text-capitalize">appointments</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card card-block p-300 bg-green-700">
                        <div class="card-watermark darker font-size-60 m-15">
                            <i class="icon md-phone-forwarded" aria-hidden="true"></i>
                        </div>
                        <div class="counter counter-inverse counter-md text-left">
                            <div class="counter-number-group">
                                <span class="counter-number">{{ number_format($stats->callbacks) }}</span>
                                <span class="counter-number-related text-capitalize"></span>
                            </div>
                            <div class="counter-label text-capitalize">callbacks</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="lt-container
                    lt-xs-h-18
                    lt-sm-h-12
                    lt-md-h-8
                    lt-lg-h-6">

                    <div class="lt
                      lt-xs-x-0
                      lt-xs-y-0
                      lt-xs-w-1
                      lt-xs-h-1
                      lt-sm-x-0
                      lt-sm-y-0
                      lt-sm-w-1
                      lt-sm-h-1
                      lt-md-x-0
                      lt-md-y-0
                      lt-md-w-1
                      lt-md-h-1
                      lt-lg-x-0
                      lt-lg-y-0
                      lt-lg-w-1
                      lt-lg-h-1">
                      <div class="lt-body bg-blue-grey-20 text-center padding-20">
                          <h5>Drops</h5>
                          <div class="datepicker datepicker-inline"></div>
                      </div>
                    </div>
                    <div class="lt
                      lt-xs-x-0
                      lt-xs-y-1
                      lt-xs-w-1
                      lt-xs-h-2
                      lt-sm-x-1
                      lt-sm-y-0
                      lt-sm-w-1
                      lt-sm-h-2
                      lt-md-x-2
                      lt-md-y-0
                      lt-md-w-1
                      lt-md-h-2
                      lt-lg-x-1
                      lt-lg-y-0
                      lt-lg-w-1
                      lt-lg-h-2">
                      <div class="lt-body bg-blue-grey-20 text-center padding-20">
                          <h3>Callbacks</h3>
                          <div class="table-responsive">
                              <table class="table table-bordered table-striped table-hover datatable">
                                  <thead>
                                  <tr>
                                      <td>&check;</td>
                                      <td>Name</td>
                                      <td>Called At</td>
                                  </tr>
                                  </thead>
                                  <tbody>
                                  @foreach ($callbacks as $callback)
                                      <tr>
                                          <td><input type="checkbox" class="callback-button" data-callback_id="{{ $callback->id }}"></td>
                                          <td>
                                              <div>{{ $callback->name }}</div>
                                              <small>{{ $callback->email }}</small>
                                              <div style="text-transform: uppercase; font-size: small; color: #555;">{{ $callback->vehicle }}</div>
                                              <div>
                                                  <button class="btn btn-primary btn-pure button-link" data-url="tel:{{ $callback->phone_number}}">
                                                      <i class="icon md-phone" aria-hidden="true"></i>
                                                      {{ $callback->phone_number }}
                                                  </button>
                                              </div>
                                          </td>
                                          <td>{{ show_date($callback->created_at) }}</td>
                                      </tr>
                                  @endforeach
                                  </tbody>
                              </table>
                          </div>
                      </div>
                    </div>
                    <div class="lt
                      lt-xs-x-0
                      lt-xs-y-3
                      lt-xs-w-1
                      lt-xs-h-1
                      lt-sm-x-0
                      lt-sm-y-1
                      lt-sm-w-1
                      lt-sm-h-1
                      lt-md-x-1
                      lt-md-y-0
                      lt-md-w-1
                      lt-md-h-1
                      lt-lg-x-0
                      lt-lg-y-1
                      lt-lg-w-1
                      lt-lg-h-1">
                      <div class="lt-body bg-blue-grey-20 text-center padding-20">
                          <h3>Appointments</h3>
                          <div class="appointments"></div>
                      </div>
                    </div>
                    <div class="lt
                      lt-xs-x-0
                      lt-xs-y-4
                      lt-xs-w-1
                      lt-xs-h-2
                      lt-sm-x-0
                      lt-sm-y-2
                      lt-sm-w-2
                      lt-sm-h-2
                      lt-md-x-0
                      lt-md-y-1
                      lt-md-w-2
                      lt-md-h-2
                      lt-lg-x-2
                      lt-lg-y-0
                      lt-lg-w-2
                      lt-lg-h-2">
                      <div class="lt-body bg-blue-grey-20 text-center padding-20">
                          <h3>Campaigns</h3>

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
                      </div>
                    </div>
                    <div class="lt
                      lt-xs-x-0
                      lt-xs-y-6
                      lt-xs-w-1
                      lt-xs-h-1
                      lt-sm-x-0
                      lt-sm-y-4
                      lt-sm-w-1
                      lt-sm-h-1
                      lt-md-x-2
                      lt-md-y-2
                      lt-md-w-1
                      lt-md-h-1
                      lt-lg-x-0
                      lt-lg-y-2
                      lt-lg-w-1
                      lt-lg-h-1">
                      <div class="lt-body bg-blue-grey-20 text-center padding-20">
                        <h3>5</h3>
                      </div>
                    </div>
                    <div class="lt
                      lt-xs-x-0
                      lt-xs-y-7
                      lt-xs-w-1
                      lt-xs-h-1
                      lt-sm-x-0
                      lt-sm-y-5
                      lt-sm-w-2
                      lt-sm-h-1
                      lt-md-x-1
                      lt-md-y-3
                      lt-md-w-2
                      lt-md-h-1
                      lt-lg-x-1
                      lt-lg-y-2
                      lt-lg-w-2
                      lt-lg-h-1">
                      <div class="lt-body bg-blue-grey-20 text-center padding-20">
                        <h3>Recent Campaigns</h3>
                      </div>
                    </div>
                    <div class="lt
                      lt-xs-x-0
                      lt-xs-y-8
                      lt-xs-w-1
                      lt-xs-h-1
                      lt-sm-x-0
                      lt-sm-y-6
                      lt-sm-w-2
                      lt-sm-h-1
                      lt-md-x-0
                      lt-md-y-4
                      lt-md-w-2
                      lt-md-h-1
                      lt-lg-x-0
                      lt-lg-y-3
                      lt-lg-w-2
                      lt-lg-h-1">
                      <div class="lt-body bg-blue-grey-20 text-center padding-20">
                        <h3>7</h3>
                      </div>
                    </div>
                    <div class="lt
                      lt-xs-x-0
                      lt-xs-y-9
                      lt-xs-w-1
                      lt-xs-h-2
                      lt-sm-x-0
                      lt-sm-y-7
                      lt-sm-w-1
                      lt-sm-h-2
                      lt-md-x-2
                      lt-md-y-4
                      lt-md-w-1
                      lt-md-h-2
                      lt-lg-x-3
                      lt-lg-y-3
                      lt-lg-w-1
                      lt-lg-h-2">
                      <div class="lt-body bg-blue-grey-20 text-center padding-20">
                        <h3>8</h3>
                      </div>
                    </div>
                    <div class="lt
                      lt-xs-x-0
                      lt-xs-y-11
                      lt-xs-w-1
                      lt-xs-h-1
                      lt-sm-x-1
                      lt-sm-y-7
                      lt-sm-w-1
                      lt-sm-h-1
                      lt-md-x-0
                      lt-md-y-5
                      lt-md-w-1
                      lt-md-h-1
                      lt-lg-x-2
                      lt-lg-y-3
                      lt-lg-w-1
                      lt-lg-h-1">
                      <div class="lt-body bg-blue-grey-20 text-center padding-20">
                        <h3>9</h3>
                      </div>
                    </div>
                    <div class="lt
                      lt-xs-x-0
                      lt-xs-y-12
                      lt-xs-w-1
                      lt-xs-h-1
                      lt-sm-x-1
                      lt-sm-y-8
                      lt-sm-w-1
                      lt-sm-h-1
                      lt-md-x-1
                      lt-md-y-5
                      lt-md-w-1
                      lt-md-h-1
                      lt-lg-x-0
                      lt-lg-y-4
                      lt-lg-w-1
                      lt-lg-h-1">
                      <div class="lt-body bg-blue-grey-20 text-center padding-20">
                        <h3>10</h3>
                      </div>
                    </div>
                    <div class="lt
                      lt-xs-x-0
                      lt-xs-y-13
                      lt-xs-w-1
                      lt-xs-h-2
                      lt-sm-x-0
                      lt-sm-y-9
                      lt-sm-w-2
                      lt-sm-h-2
                      lt-md-x-1
                      lt-md-y-6
                      lt-md-w-2
                      lt-md-h-2
                      lt-lg-x-1
                      lt-lg-y-4
                      lt-lg-w-2
                      lt-lg-h-2">
                      <div class="lt-body bg-blue-grey-20 text-center padding-20">
                        <h3>11</h3>
                      </div>
                    </div>
                    <div class="lt
                      lt-xs-x-0
                      lt-xs-y-15
                      lt-xs-w-1
                      lt-xs-h-1
                      lt-sm-x-0
                      lt-sm-y-11
                      lt-sm-w-1
                      lt-sm-h-1
                      lt-md-x-0
                      lt-md-y-6
                      lt-md-w-1
                      lt-md-h-1
                      lt-lg-x-0
                      lt-lg-y-5
                      lt-lg-w-1
                      lt-lg-h-1">
                      <div class="lt-body bg-blue-grey-20 text-center padding-20">
                        <h3>12</h3>
                      </div>
                    </div>
                    <div class="lt
                      lt-xs-x-0
                      lt-xs-y-16
                      lt-xs-w-1
                      lt-xs-h-1
                      lt-sm-x-1
                      lt-sm-y-4
                      lt-sm-w-1
                      lt-sm-h-1
                      lt-md-x-0
                      lt-md-y-3
                      lt-md-w-1
                      lt-md-h-1
                      lt-lg-x-3
                      lt-lg-y-2
                      lt-lg-w-1
                      lt-lg-h-1">
                      <div class="lt-body bg-blue-grey-20 text-center padding-20">
                        <h3>13</h3>
                      </div>
                    </div>
                    <div class="lt
                      lt-xs-x-0
                      lt-xs-y-17
                      lt-xs-w-1
                      lt-xs-h-1
                      lt-sm-x-1
                      lt-sm-y-11
                      lt-sm-w-1
                      lt-sm-h-1
                      lt-md-x-0
                      lt-md-y-7
                      lt-md-w-1
                      lt-md-h-1
                      lt-lg-x-3
                      lt-lg-y-5
                      lt-lg-w-1
                      lt-lg-h-1">
                      <div class="lt-body bg-blue-grey-20 text-center padding-20">
                        <h3>14</h3>
                      </div>
                    </div>
                </div>
              </div>
              <!-- End Example Layout grid -->
          <!-- End Panel Static example -->
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-default panel-bordered anumation-scale-up"
                         style="animation-fill-mode: backwards; animation-duration: 250ms; animation-delay: 850ms;">
                        <div class="panel-heading">
                            <h5 class="panel-title">Calendars</h5>
                        </div>
                        <div class="panel-body">

                        </div>
                    </div>
                    <div class="panel panel-default panel-bordered anumation-scale-up"
                         style="animation-fill-mode: backwards; animation-duration: 250ms; animation-delay: 850ms;">
                        <div class="panel-heading">
                            <h5 class="panel-title">Callback List</h5>
                        </div>
                        <div id="callbacks" class="panel-body">
                        </div>
                    </div>
                </div>
                <div id="campaigns" class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading"><h3 class="panel-title">Test</h3></div>
                        <div class="panel-body">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scriptTags')
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="{{ secure_url('vendor/icheck/icheck.min.js') }}"></script>
    <script type="text/javascript" src="{{ secure_url('vendor/moment/moment.min.js') }}"></script>

    <script type="text/javascript" src="{{ secure_url('vendor/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
    <script type="text/javascript" src="{{ secure_url('vendor/slidepanel/jquery-slidePanel.min.js') }}"></script>
    <script type="text/javascript" src="{{ secure_url('vendor/raphael/raphael.min.js') }}"></script>
    <script type="text/javascript" src="{{ secure_url('vendor/morris/morris.min.js') }}"></script>
    <script>

        $(document).ready(function() {
            $(".datatable").dataTable({"order": [[0, "desc"]]});

            $(".data-link").click(function() {
                window.location.href = $(this).data('url');
            });

            $('.icheckbox > input[type=checkbox]').iCheck({
                labelHover: false,
                cursor: true
            });

            $(".callback-button").change(function() {
                $.post(
                    "{{ secure_url('appointment/') }}" + "/" + $(this).data('callback_id') + "/update-called-status",
                    { "called_back": $(this).prop('checked') },
                    'json'
                );

                $(this).parent().closest('tr').hide();
            });

            $(".datepicker").datepicker();
        })();

    </script>
@endsection
