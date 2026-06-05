@php
    $currentSaleOrigin = $currentValue ?? '';
    $saleOriginOpts = \App\Models\VehicleStatusDetail::saleOriginOptions();
    $saleOriginLegacy = $currentSaleOrigin !== '' && !in_array($currentSaleOrigin, $saleOriginOpts, true);
    $selectId = $selectId ?? 'sale_origin';
    $name = $name ?? 'sale_origin';
@endphp
<select class="form-select" id="{{ $selectId }}" name="{{ $name }}">
    <option value="">Select</option>
    @if($saleOriginLegacy)
        <option value="{{ $currentSaleOrigin }}" selected>{{ $currentSaleOrigin }} (legacy)</option>
    @endif
    @foreach($saleOriginOpts as $originOpt)
        <option value="{{ $originOpt }}" {{ !$saleOriginLegacy && $currentSaleOrigin === $originOpt ? 'selected' : '' }}>{{ $originOpt }}</option>
    @endforeach
</select>
