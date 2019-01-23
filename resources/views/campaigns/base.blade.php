@extends('layouts.base', [
    'hasSidebar' => true
])

@section('sidebar-content')
    <div class="company-data">
        <div class="company-data--image" :style="{backgroundImage: 'url(\'' + user.image_url + '\')'}"></div>
        <h3>{{ $campaign->name }}</h3>
    </div>
    <nav class="company-navigation">
        <ul>
            <li>
                <a class="{{ \Route::current()->getName() === 'campaigns.stats' ? 'active' : '' }}" href="{{ route('campaigns.stats', ['campaign' => $campaign->id]) }}">
                    <i class="pm-font-stats-icon"></i>
                    <span>STATS</span>
                </a>
            </li>
            <li>
                <a class="{{ \Route::current()->getName() === 'campaigns.drops.index' ? 'active' : '' }}" href="{{ route('campaigns.drops.index', ['campaign' => $campaign->id]) }}">
                    <i class="pm-font-drops-icon"></i>
                    <span>DROPS</span>
                </a>
            </li>
            <li>
                <a class="{{ \Route::current()->getName() === 'campaigns.recipients.index' ? 'active' : '' }}" href="{{ route('campaigns.recipients.index', ['campaign' => $campaign->id]) }}">
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
        </ul>
    </nav>
@endsection
