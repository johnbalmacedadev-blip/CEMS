@php
    $r = $recommendation_tracker ?? null;
    $val = function($key, $default = '') use ($r) {
        return old($key, $r ? $r->$key : $default);
    };
    $checked = function($key) use ($r) {
        return old($key, $r ? $r->$key : false) ? 'checked' : '';
    };
@endphp
<div class="card mb-3">
    <div class="card-header"><strong>CAR EMPIRE RECOMMENDATIONS</strong></div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-2">
                <label for="date" class="form-label">DATE <span class="text-danger">*</span></label>
                <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', $r ? $r->date->format('Y-m-d') : date('Y-m-d')) }}" required>
                @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-2">
                <label for="year" class="form-label">YEAR</label>
                <input type="text" class="form-control" id="year" name="year" value="{{ $val('year') }}" placeholder="Year">
            </div>
            <div class="col-md-2">
                <label for="customer" class="form-label">CUSTOMER</label>
                <input type="text" class="form-control" id="customer" name="customer" value="{{ $val('customer') }}" placeholder="Customer">
            </div>
            <div class="col-md-2">
                <label for="make" class="form-label">MAKE</label>
                <input type="text" class="form-control" id="make" name="make" value="{{ $val('make') }}" placeholder="Make">
            </div>
            <div class="col-md-2">
                <label for="model" class="form-label">MODEL</label>
                <input type="text" class="form-control" id="model" name="model" value="{{ $val('model') }}" placeholder="Model">
            </div>
            <div class="col-md-2">
                <label for="paint" class="form-label">PAINT (short)</label>
                <input type="text" class="form-control" id="paint" name="paint" value="{{ $val('paint') }}" placeholder="Paint summary">
            </div>
            <div class="col-md-3">
                <label for="plate_number" class="form-label">PLATE NUMBER</label>
                <input type="text" class="form-control" id="plate_number" name="plate_number" value="{{ $val('plate_number') }}" placeholder="Links to vehicle when matched">
            </div>
            <div class="col-md-3">
                <label for="variant" class="form-label">VARIANT</label>
                <input type="text" class="form-control" id="variant" name="variant" value="{{ $val('variant') }}">
            </div>
            <div class="col-md-2">
                <label for="transmission" class="form-label">TRANSMISSION</label>
                <input type="text" class="form-control" id="transmission" name="transmission" value="{{ $val('transmission') }}">
            </div>
            <div class="col-md-2">
                <label for="fuel_type" class="form-label">FUEL TYPE</label>
                <input type="text" class="form-control" id="fuel_type" name="fuel_type" value="{{ $val('fuel_type') }}">
            </div>
            <div class="col-md-2">
                <label for="color" class="form-label">COLOR</label>
                <input type="text" class="form-control" id="color" name="color" value="{{ $val('color') }}">
            </div>
            <div class="col-md-2">
                <label for="final_status" class="form-label">FINAL STATUS</label>
                <input type="text" class="form-control" id="final_status" name="final_status" value="{{ $val('final_status') }}" list="final_status_options">
                <datalist id="final_status_options">
                    <option value="COMPLETE"></option>
                    <option value="PENDING"></option>
                    <option value="IN PROGRESS"></option>
                </datalist>
            </div>
            <div class="col-md-3">
                <label for="purchase_price" class="form-label">PURCHASE PRICE</label>
                <input type="number" step="0.01" class="form-control" id="purchase_price" name="purchase_price" value="{{ $val('purchase_price') }}">
            </div>
            <div class="col-md-3">
                <label for="purchased_from" class="form-label">PURCHASED FROM</label>
                <input type="text" class="form-control" id="purchased_from" name="purchased_from" value="{{ $val('purchased_from') }}">
            </div>
            <div class="col-md-2">
                <label for="purchase_date" class="form-label">PURCHASE DATE</label>
                <input type="date" class="form-control" id="purchase_date" name="purchase_date" value="{{ old('purchase_date', $r && $r->purchase_date ? $r->purchase_date->format('Y-m-d') : '') }}">
            </div>
            <div class="col-12">
                <label for="vehicle_id" class="form-label">Link to vehicle (optional)</label>
                <select class="form-select" id="vehicle_id" name="vehicle_id">
                    <option value="">— None —</option>
                    @foreach($vehicles as $v)
                        @php
                            $vMake = is_object($v->make) ? ($v->make->name ?? '') : ($v->make ?? '');
                            $vModel = is_object($v->vehicleModel) ? ($v->vehicleModel->name ?? '') : ($v->model ?? '');
                            $vCustomer = $v->statusDetail ? trim(($v->statusDetail->customer_first_name ?? '') . ' ' . ($v->statusDetail->customer_last_name ?? '')) : '';
                        @endphp
                        <option value="{{ $v->id }}"
                            data-year="{{ $v->year ?? '' }}"
                            data-make="{{ e($vMake) }}"
                            data-model="{{ e($vModel) }}"
                            data-customer="{{ e($vCustomer) }}"
                            {{ (old('vehicle_id', $r ? $r->vehicle_id : request('vehicle_id')) == $v->id) ? 'selected' : '' }}>{{ $v->full_name }} @if($v->plate_number)({{ $v->plate_number }})@endif</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header"><strong>Recommendation & Completion Notes</strong></div>
    <div class="card-body">
        <div class="row g-3">
            @foreach(\App\Models\RecommendationTracker::recommendationCategories() as $key => $label)
                <div class="col-md-6">
                    <label for="{{ $key }}_recommendation" class="form-label">{{ $label }} Recommendation</label>
                    <textarea class="form-control" id="{{ $key }}_recommendation" name="{{ $key }}_recommendation" rows="3">{{ $val($key.'_recommendation') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label for="{{ $key }}_completion" class="form-label">{{ $label }} Completion</label>
                    <textarea class="form-control" id="{{ $key }}_completion" name="{{ $key }}_completion" rows="3">{{ $val($key.'_completion') }}</textarea>
                </div>
            @endforeach
            <div class="col-12">
                <label for="notes" class="form-label">Notes</label>
                <textarea class="form-control" id="notes" name="notes" rows="2">{{ $val('notes') }}</textarea>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header"><strong>Exterior Parts Checklist</strong></div>
    <div class="card-body">
        <div class="row g-2">
            @foreach([
                'hood' => 'Hood',
                'front_bumper' => 'Front Bumper',
                'grille' => 'Grille',
                'fender_right' => 'Fender Right',
                'fender_left' => 'Fender Left',
                'driver_passenger_door' => 'Driver Passenger Door',
                'driver_side_door' => 'Driver Side Door',
                'step_board_left' => 'Step Board Left Side',
                'step_board_right' => 'Step Board Right Side',
                'trunk_lid' => 'Trunk Lid',
                'quarter_panels_left' => 'Quarter Panels Left Side',
                'rear_bumper' => 'Rear Bumper',
                'quarter_panel_right' => 'Quarter Panel Right Side',
                'passenger_door_right_rear' => 'Passenger Door Right Rear Side',
                'passenger_door_right_front' => 'Passenger Door Right Front Side',
                'roof' => 'Roof',
                'spoiler' => 'Spoiler',
            ] as $key => $label)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="{{ $key }}" id="{{ $key }}" value="1" {{ $checked($key) }}>
                    <label class="form-check-label" for="{{ $key }}">{{ $label }}</label>
                </div>
            </div>
            @endforeach
        </div>
        <hr class="my-3">
        <div class="row g-2">
            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="tire_1" id="tire_1" value="1" {{ $checked('tire_1') }}><label class="form-check-label" for="tire_1">1. Tire</label></div></div>
            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="tire_2" id="tire_2" value="1" {{ $checked('tire_2') }}><label class="form-check-label" for="tire_2">2. Tire</label></div></div>
            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="tire_3" id="tire_3" value="1" {{ $checked('tire_3') }}><label class="form-check-label" for="tire_3">3. Tire</label></div></div>
            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="tire_4" id="tire_4" value="1" {{ $checked('tire_4') }}><label class="form-check-label" for="tire_4">4. Tire</label></div></div>
            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="rims_1" id="rims_1" value="1" {{ $checked('rims_1') }}><label class="form-check-label" for="rims_1">Rims 1</label></div></div>
            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="rims_2" id="rims_2" value="1" {{ $checked('rims_2') }}><label class="form-check-label" for="rims_2">Rims 2</label></div></div>
            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="rims_3" id="rims_3" value="1" {{ $checked('rims_3') }}><label class="form-check-label" for="rims_3">Rims 3</label></div></div>
            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="rims_4" id="rims_4" value="1" {{ $checked('rims_4') }}><label class="form-check-label" for="rims_4">Rims 4</label></div></div>
            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="front_headlight_1" id="front_headlight_1" value="1" {{ $checked('front_headlight_1') }}><label class="form-check-label" for="front_headlight_1">Front Head Light 1</label></div></div>
            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="front_headlight_2" id="front_headlight_2" value="1" {{ $checked('front_headlight_2') }}><label class="form-check-label" for="front_headlight_2">Front Head Light 2</label></div></div>
            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="inner_rear_taillight_1" id="inner_rear_taillight_1" value="1" {{ $checked('inner_rear_taillight_1') }}><label class="form-check-label" for="inner_rear_taillight_1">Inner Rear Taillight 1</label></div></div>
            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="inner_rear_taillight_2" id="inner_rear_taillight_2" value="1" {{ $checked('inner_rear_taillight_2') }}><label class="form-check-label" for="inner_rear_taillight_2">Inner Rear Taillight 2</label></div></div>
            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="taillight_1" id="taillight_1" value="1" {{ $checked('taillight_1') }}><label class="form-check-label" for="taillight_1">Taillight 1</label></div></div>
            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="taillight_2" id="taillight_2" value="1" {{ $checked('taillight_2') }}><label class="form-check-label" for="taillight_2">Taillight 2</label></div></div>
            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="side_mirror_left" id="side_mirror_left" value="1" {{ $checked('side_mirror_left') }}><label class="form-check-label" for="side_mirror_left">Side Mirror Left</label></div></div>
            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="side_mirror_right" id="side_mirror_right" value="1" {{ $checked('side_mirror_right') }}><label class="form-check-label" for="side_mirror_right">Side Mirror Right</label></div></div>
            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="mud_guard" id="mud_guard" value="1" {{ $checked('mud_guard') }}><label class="form-check-label" for="mud_guard">Mud Guard</label></div></div>
            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="windshield_front" id="windshield_front" value="1" {{ $checked('windshield_front') }}><label class="form-check-label" for="windshield_front">Windshield Front</label></div></div>
            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="windshield_rear" id="windshield_rear" value="1" {{ $checked('windshield_rear') }}><label class="form-check-label" for="windshield_rear">Windshield Rear</label></div></div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header"><strong>Additional Items</strong></div>
    <div class="card-body">
        <div class="row g-2">
            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="with_spare_key" id="with_spare_key" value="1" {{ $checked('with_spare_key') }}><label class="form-check-label" for="with_spare_key">With spare key</label></div></div>
            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="with_spare_tire" id="with_spare_tire" value="1" {{ $checked('with_spare_tire') }}><label class="form-check-label" for="with_spare_tire">With spare tire</label></div></div>
            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="with_tools" id="with_tools" value="1" {{ $checked('with_tools') }}><label class="form-check-label" for="with_tools">With tools (jack, hook, tire wrench)</label></div></div>
            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="with_matting_complete" id="with_matting_complete" value="1" {{ $checked('with_matting_complete') }}><label class="form-check-label" for="with_matting_complete">With matting complete</label></div></div>
            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="row_2nd" id="row_2nd" value="1" {{ $checked('row_2nd') }}><label class="form-check-label" for="row_2nd">2nd row</label></div></div>
            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="row_3rd" id="row_3rd" value="1" {{ $checked('row_3rd') }}><label class="form-check-label" for="row_3rd">3rd row</label></div></div>
            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="row_1st" id="row_1st" value="1" {{ $checked('row_1st') }}><label class="form-check-label" for="row_1st">1st row</label></div></div>
            <div class="col-6 col-md-4"><div class="form-check"><input class="form-check-input" type="checkbox" name="dash_cam" id="dash_cam" value="1" {{ $checked('dash_cam') }}><label class="form-check-label" for="dash_cam">Dash cam</label></div></div>
        </div>
        <div class="row g-3 mt-2">
            <div class="col-md-6">
                <label for="odometers" class="form-label">ODOMETERS</label>
                <input type="text" class="form-control" id="odometers" name="odometers" value="{{ $val('odometers') }}" placeholder="Odometer reading">
            </div>
            <div class="col-md-6">
                <label for="authorized_drivers" class="form-label">AUTHORIZED DRIVER'S</label>
                <input type="text" class="form-control" id="authorized_drivers" name="authorized_drivers" value="{{ $val('authorized_drivers') }}" placeholder="Authorized driver name/signature">
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header"><strong>Upload Images</strong></div>
    <div class="card-body">
        <label for="images" class="form-label">Add images (JPEG, PNG, GIF, WebP – max 5MB each)</label>
        <input type="file" class="form-control" id="images" name="images[]" accept="image/jpeg,image/png,image/gif,image/webp" multiple>
        <div id="imagePreview" class="row g-2 mt-2"></div>
        @if($r && $r->relationLoaded('images') && $r->images->isNotEmpty())
        <hr class="my-3">
        <p class="mb-2"><strong>Uploaded images</strong></p>
        <div class="row g-2" id="existingImages">
            @foreach($r->images as $img)
            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                <div class="position-relative border rounded overflow-hidden bg-light">
                    <a href="{{ Storage::url($img->file_path) }}" target="_blank" class="d-block">
                        <img src="{{ Storage::url($img->file_path) }}" alt="" class="img-fluid w-100" style="object-fit: cover; height: 100px;">
                    </a>
                    @if($r)
                    <form action="{{ route('recommendation-tracker.images.destroy', [$r, $img]) }}" method="POST" class="position-absolute top-0 end-0 m-1" onsubmit="return confirm('Remove this image?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger py-0 px-1" title="Remove image"><i class="fas fa-times"></i></button>
                    </form>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
