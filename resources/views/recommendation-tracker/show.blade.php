@extends('layouts.app')

@section('title', 'Recommendation Details - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-clipboard-list me-2"></i>Recommendation Details
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('recommendation-tracker.index') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i>Back to Recommendation Tracker
            </a>
            <a href="{{ route('recommendation-tracker.edit', $recommendation_tracker) }}" class="btn btn-primary">
                <i class="fas fa-edit me-1"></i>Edit
            </a>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><strong>CAR EMPIRE RECOMMENDATIONS</strong></div>
        <div class="card-body">
            <div class="row g-3 mb-3">
                <div class="col-md-2"><strong>DATE</strong><br>{{ $recommendation_tracker->date->format('M j, Y') }}</div>
                <div class="col-md-2"><strong>YEAR</strong><br>{{ $recommendation_tracker->year ?? '—' }}</div>
                <div class="col-md-2"><strong>CUSTOMER</strong><br>{{ $recommendation_tracker->customer ?? '—' }}</div>
                <div class="col-md-2"><strong>MAKE</strong><br>{{ $recommendation_tracker->make ?? '—' }}</div>
                <div class="col-md-2"><strong>MODEL</strong><br>{{ $recommendation_tracker->model ?? '—' }}</div>
                <div class="col-md-2"><strong>PAINT</strong><br>{{ $recommendation_tracker->paint ?? '—' }}</div>
                @if($recommendation_tracker->vehicle)
                <div class="col-12"><strong>Vehicle</strong> <a href="{{ route('vehicles.show', $recommendation_tracker->vehicle) }}">{{ $recommendation_tracker->vehicle->full_name }}</a></div>
                @endif
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><strong>Exterior Parts Checklist</strong></div>
        <div class="card-body">
            @php
                $exterior = [
                    'hood' => 'Hood', 'front_bumper' => 'Front Bumper', 'grille' => 'Grille',
                    'fender_right' => 'Fender Right', 'fender_left' => 'Fender Left',
                    'driver_passenger_door' => 'Driver Passenger Door', 'driver_side_door' => 'Driver Side Door',
                    'step_board_left' => 'Step Board Left', 'step_board_right' => 'Step Board Right',
                    'trunk_lid' => 'Trunk Lid', 'quarter_panels_left' => 'Quarter Panels Left', 'rear_bumper' => 'Rear Bumper',
                    'quarter_panel_right' => 'Quarter Panel Right', 'passenger_door_right_rear' => 'Passenger Door Right Rear',
                    'passenger_door_right_front' => 'Passenger Door Right Front', 'roof' => 'Roof', 'spoiler' => 'Spoiler',
                    'tire_1' => 'Tire 1', 'tire_2' => 'Tire 2', 'tire_3' => 'Tire 3', 'tire_4' => 'Tire 4',
                    'rims_1' => 'Rims 1', 'rims_2' => 'Rims 2', 'rims_3' => 'Rims 3', 'rims_4' => 'Rims 4',
                    'front_headlight_1' => 'Front Head Light 1', 'front_headlight_2' => 'Front Head Light 2',
                    'inner_rear_taillight_1' => 'Inner Rear Taillight 1', 'inner_rear_taillight_2' => 'Inner Rear Taillight 2',
                    'taillight_1' => 'Taillight 1', 'taillight_2' => 'Taillight 2',
                    'side_mirror_left' => 'Side Mirror Left', 'side_mirror_right' => 'Side Mirror Right',
                    'mud_guard' => 'Mud Guard', 'windshield_front' => 'Windshield Front', 'windshield_rear' => 'Windshield Rear',
                ];
            @endphp
            <div class="row g-2">
                @foreach($exterior as $key => $label)
                    @if($recommendation_tracker->$key)
                        <div class="col-6 col-md-4"><i class="fas fa-check text-success me-1"></i>{{ $label }}</div>
                    @endif
                @endforeach
            </div>
            @if(collect(array_keys($exterior))->filter(fn($k) => $recommendation_tracker->$k)->isEmpty())
                <p class="text-muted mb-0">No exterior items checked.</p>
            @endif
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><strong>Additional Items</strong></div>
        <div class="card-body">
            @php
                $items = [
                    'with_spare_key' => 'With spare key', 'with_spare_tire' => 'With spare tire',
                    'with_tools' => 'With tools', 'with_matting_complete' => 'With matting complete',
                    'row_2nd' => '2nd row', 'row_3rd' => '3rd row', 'row_1st' => '1st row', 'dash_cam' => 'Dash cam',
                ];
            @endphp
            <div class="row g-2 mb-2">
                @foreach($items as $key => $label)
                    @if($recommendation_tracker->$key)
                        <div class="col-6 col-md-4"><i class="fas fa-check text-success me-1"></i>{{ $label }}</div>
                    @endif
                @endforeach
            </div>
            <div class="row">
                <div class="col-md-6"><strong>ODOMETERS</strong><br>{{ $recommendation_tracker->odometers ?? '—' }}</div>
                <div class="col-md-6"><strong>AUTHORIZED DRIVER'S</strong><br>{{ $recommendation_tracker->authorized_drivers ?? '—' }}</div>
            </div>
        </div>
    </div>

    @if($recommendation_tracker->images->isNotEmpty())
    <div class="card mb-3">
        <div class="card-header"><strong>Uploaded Images</strong></div>
        <div class="card-body">
            <div class="row g-2">
                @foreach($recommendation_tracker->images as $img)
                <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                    <a href="{{ Storage::url($img->file_path) }}" target="_blank" class="d-block border rounded overflow-hidden bg-light">
                        <img src="{{ Storage::url($img->file_path) }}" alt="{{ $img->original_name }}" class="img-fluid w-100" style="object-fit: cover; height: 100px;">
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
