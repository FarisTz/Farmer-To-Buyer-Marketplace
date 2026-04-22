@extends('layouts.marketplace')

@section('title', 'Platform Activities')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-history me-2 text-success"></i>Platform Activities
            </h1>
            <p class="text-muted mb-0">Recent system activities and events</p>
        </div>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>
        </div>
    </div>

    <!-- Activity Statistics -->
    <div class="row g-4 mb-4">
        <div class="col-md-2">
            <div class="stats-card text-center">
                <div class="text-primary mb-2">
                    <i class="fas fa-chart-line fa-2x"></i>
                </div>
                <h4 class="fw-bold">{{ $activityStats['total_activities'] }}</h4>
                <p class="text-muted mb-0">Total Activities</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stats-card text-center">
                <div class="text-success mb-2">
                    <i class="fas fa-calendar-day fa-2x"></i>
                </div>
                <h4 class="fw-bold">{{ $activityStats['today_activities'] }}</h4>
                <p class="text-muted mb-0">Today's Activities</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stats-card text-center">
                <div class="text-info mb-2">
                    <i class="fas fa-sign-in-alt fa-2x"></i>
                </div>
                <h4 class="fw-bold">{{ $activityStats['login_activities'] }}</h4>
                <p class="text-muted mb-0">Logins</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stats-card text-center">
                <div class="text-warning mb-2">
                    <i class="fas fa-carrot fa-2x"></i>
                </div>
                <h4 class="fw-bold">{{ $activityStats['crop_activities'] }}</h4>
                <p class="text-muted mb-0">Crop Activities</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stats-card text-center">
                <div class="text-danger mb-2">
                    <i class="fas fa-shopping-bag fa-2x"></i>
                </div>
                <h4 class="fw-bold">{{ $activityStats['order_activities'] }}</h4>
                <p class="text-muted mb-0">Order Activities</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stats-card text-center">
                <div class="text-secondary mb-2">
                    <i class="fas fa-users fa-2x"></i>
                </div>
                <h4 class="fw-bold">{{ $activityStats['user_activities'] }}</h4>
                <p class="text-muted mb-0">User Activities</p>
            </div>
        </div>
    </div>

    <!-- Activity Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.activities') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="type" class="form-label">Activity Type</label>
                        <select class="form-select" id="type" name="type">
                            <option value="">All Activities</option>
                            <option value="user" {{ request('type') == 'user' ? 'selected' : '' }}>User Activities</option>
                            <option value="crop" {{ request('type') == 'crop' ? 'selected' : '' }}>Crop Activities</option>
                            <option value="order" {{ request('type') == 'order' ? 'selected' : '' }}>Order Activities</option>
                            <option value="system" {{ request('type') == 'system' ? 'selected' : '' }}>System Activities</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" 
                               value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" 
                               value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="{{ route('admin.activities') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Activities List -->
    @if($activities->count() > 0)
        <div class="card">
            <div class="card-body">
                <div class="timeline">
                    @foreach($activities as $activity)
                        <div class="timeline-item">
                            <div class="timeline-marker">
                                <div class="timeline-icon bg-{{ $activity->typeColor() }}">
                                    <i class="fas fa-{{ $activity->typeIcon() }}"></i>
                                </div>
                            </div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $activity->description }}</h6>
                                        <p class="text-muted mb-2">
                                            {{ $activity->created_at->format('M d, Y H:i') }} 
                                            @if($activity->causer)
                                                by {{ $activity->causer->name }}
                                            @endif
                                        </p>
                                        @if($activity->subject)
                                            <div class="activity-details">
                                                @switch($activity->subject_type)
                                                    @case('App\\Models\\User')
                                                        <span class="badge bg-primary">User: {{ $activity->subject->name }}</span>
                                                    @case('App\\Models\\Crop')
                                                        <span class="badge bg-success">Crop: {{ $activity->subject->name }}</span>
                                                    @case('App\\Models\\Order')
                                                        <span class="badge bg-warning">Order #{{ $activity->subject->order_number }}</span>
                                                @endswitch
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="card-footer">
                {{ $activities->links() }}
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-history fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No activities found</h5>
                <p class="text-muted">System activities will appear here once users start performing actions.</p>
            </div>
        </div>
    @endif
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    top: 0;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.timeline-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 14px;
}

.timeline-content {
    background: white;
    padding: 15px;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.activity-details .badge {
    margin-right: 5px;
    margin-bottom: 5px;
}
</style>
@endsection
