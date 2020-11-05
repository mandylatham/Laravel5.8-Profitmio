@extends('layouts.base', [
    'hasSidebar' => true
])

@section('sidebar-content')
    <div class="company-data mt-5">
        <h3>{{ $campaign->name }}</h3>
    </div>
    <nav class="company-navigation">
        <ul>
            <li>
                <a class="{{ \Route::current()->getName() === 'campaigns.stats' ? 'active' : '' }}" href="{{ route('campaigns.stats', ['campaign' => $campaign->id]) }}">
                    <i class="far fa-chart-bar"></i>
                    <span>STATS</span>
                </a>
            </li>
            @if($campaign->enable_facebook_campaign)
            <li>
                <a class="{{ \Route::current()->getName() === 'campaigns.facebook-campaign' ? 'active' : '' }}" href="{{ route('campaigns.facebook-campaign', ['campaign' => $campaign->id]) }}">
                    <i class="far fa-chart-bar"></i>
                    <span>FACEBOOK CAMPAIGN</span>
                </a>
            </li>
            @endif

            @if(auth()->user()->isAdmin())
            <li>
                <a class="{{ \Route::current()->getName() === 'campaigns.drops.index' ? 'active' : '' }}" href="{{ route('campaigns.drops.index', ['campaign' => $campaign->id]) }}">
                    <i class="pm-font-drops-icon"></i>
                    <span>DROPS</span>
                </a>
            </li>
            <li>
                <a class="{{ \Route::current()->getName() === 'campaigns.recipient-lists.index' ? 'active' : '' }}" href="{{ route('campaigns.recipient-lists.index', ['campaign' => $campaign->id]) }}">
                    <i class="pm-font-recipients-icon"></i>
                    <span>RECIPIENTS</span>
                </a>
            </li>
            <li>
                <a class="{{ \Route::current()->getName() === 'campaigns.responses.index' ? 'active' : '' }}" href="{{ route('campaigns.responses.index', ['campaign' => $campaign->id]) }}">
                    <i class="pm-font-responses-icon"></i>
                    <span>RESPONSES</span>
                </a>
            </li>
            <li>
                <a class="{{ \Route::current()->getName() === 'campaigns.edit' ? 'active' : '' }}" href="{{ route('campaigns.edit', ['campaign' => $campaign->id]) }}">
                    <i class="pm-font-edit-icon"></i>
                    <span>EDIT</span>
                </a>
            </li>
            @endif
            <li>
                <hr>
            </li>
            <li>
                <a class="{{ \Route::current()->getName() === 'campaign.response-console.index' ? 'active' : '' }}" href="{{ route('campaign.response-console.index', ['campaign' => $campaign->id]) }}">
                    <i class="fa fa-terminal"></i>
                    <span>CONSOLE</span>
                </a>
            </li>
        </ul>
    </nav>
@endsection
