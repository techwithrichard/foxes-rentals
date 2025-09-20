@extends('layouts.main')

@section('content')
<div class="nk-content">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <!-- Header -->
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">{{ __('Dashboard') }}</h3>
                            <div class="nk-block-des text-soft">
                                <p>{{ __('Welcome back, :name', ['name' => $user->name]) }}</p>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <div class="toggle-wrap nk-block-tools-toggle">
                                <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu">
                                    <em class="icon ni ni-more-v"></em>
                                </a>
                                <div class="toggle-expand-content" data-content="pageMenu">
                                    <ul class="nk-block-tools g-3">
                                        @foreach($quick_actions as $action)
                                            <li>
                                                <a href="{{ $action['url'] }}" class="btn btn-{{ $action['color'] }}">
                                                    <em class="icon {{ $action['icon'] }}"></em>
                                                    <span>{{ $action['title'] }}</span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Role-based Dashboard Sections -->
                @foreach($dashboard_sections as $sectionKey => $section)
                    @if($section['visible'])
                        <div class="nk-block">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <em class="icon {{ $section['icon'] }}"></em>
                                        {{ $section['title'] }}
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-gs">
                                        @foreach($section['widgets'] as $widget)
                                            <div class="col-md-{{ $widget['size'] ?? 4 }}">
                                                @include($widget['component'], $widget['data'] ?? [])
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach

                <!-- Statistics Overview -->
                @if(!empty($statistics))
                    <div class="nk-block">
                        <div class="row g-gs">
                            @foreach($statistics as $statKey => $statValue)
                                <div class="col-md-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <em class="icon ni ni-chart text-primary"></em>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h6 class="mb-1">{{ ucwords(str_replace('_', ' ', $statKey)) }}</h6>
                                                    <span class="text-muted">{{ number_format($statValue) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Recent Activities -->
                @if(!empty($recent_activities))
                    <div class="nk-block">
                        <div class="row g-gs">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Recent Activities</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="timeline">
                                            @foreach($recent_activities as $activity)
                                                <div class="timeline-item">
                                                    <div class="timeline-time">{{ $activity->created_at->diffForHumans() }}</div>
                                                    <div class="timeline-content">
                                                        <h6>{{ $activity->description }}</h6>
                                                        <p class="text-muted">{{ $activity->subject_type }}</p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- System Alerts -->
                            @if(!empty($system_alerts))
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title">System Alerts</h5>
                                        </div>
                                        <div class="card-body">
                                            @foreach($system_alerts as $alert)
                                                <div class="alert alert-{{ $alert->type }} alert-dismissible">
                                                    <h6>{{ $alert->title }}</h6>
                                                    <p>{{ $alert->message }}</p>
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
