@php
    $chart = $chart ?? [];
    $title = $chart['title'] ?? 'Chart';
    $labels = is_array($chart['labels'] ?? null) ? $chart['labels'] : [];
    $data = is_array($chart['data'] ?? null) ? array_map('floatval', $chart['data']) : [];
    $type = strtolower((string) ($chart['type'] ?? 'bar'));
    $datasetLabel = $chart['dataset_label'] ?? 'Value';
    while (count($data) < count($labels)) {
        $data[] = 0;
    }
    while (count($labels) < count($data)) {
        $labels[] = '';
    }
    $maxVal = count($data) ? max(0.0001, max($data)) : 0.0001;
    $tl = strtolower($title);
    $dl = strtolower((string) $datasetLabel);
    // Pie legend only: peso for money charts; plain numbers for counts / units / age / splits.
    $isCurrency = ! str_contains($dl, 'unit')
        && ! str_contains($dl, 'day')
        && ! str_contains($tl, 'split')
        && ! str_contains($tl, 'by status')
        && ! str_contains($tl, 'age bucket');
@endphp
@if(empty($data))
    <div style="margin: 8px 0; font-size: 8px; color: #666;">{{ $title }} — no data</div>
@else
<div style="margin: 12px 0 8px 0; page-break-inside: avoid;">
    <div style="font-weight: bold; font-size: 10px; margin-bottom: 4px;">{{ $title }}</div>
    @if($type === 'pie' || $type === 'doughnut')
        @php
            $total = array_sum($data);
            $cx = 120;
            $cy = 110;
            $r = 85;
            $colors = ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1', '#0dcaf0', '#6c757d'];
        @endphp
        <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="220" viewBox="0 0 520 220" style="max-width: 520px;">
            @if($total <= 0)
                <text x="120" y="110" text-anchor="middle" font-size="10" fill="#666">No data</text>
            @else
                @php
                    $start = -90 * M_PI / 180;
                @endphp
                @foreach($data as $i => $val)
                    @php
                        $angle = ($val / $total) * 2 * M_PI;
                        $x1 = $cx + $r * cos($start);
                        $y1 = $cy + $r * sin($start);
                        $end = $start + $angle;
                        $x2 = $cx + $r * cos($end);
                        $y2 = $cy + $r * sin($end);
                        $large = $angle > M_PI ? 1 : 0;
                        $fill = $colors[$i % count($colors)];
                        $start = $end;
                    @endphp
                    <path d="M {{ $cx }} {{ $cy }} L {{ $x1 }} {{ $y1 }} A {{ $r }} {{ $r }} 0 {{ $large }} 1 {{ $x2 }} {{ $y2 }} Z"
                          fill="{{ $fill }}" stroke="#fff" stroke-width="1"/>
                @endforeach
            @endif
            @foreach($labels as $i => $lab)
                @php
                    $c = $colors[$i % count($colors)];
                @endphp
                <rect x="260" y="{{ 18 + $i * 18 }}" width="10" height="10" fill="{{ $c }}"/>
                <text x="276" y="{{ 27 + $i * 18 }}" font-size="8">{{ \Illuminate\Support\Str::limit($lab, 28) }} ({{ $isCurrency ? '₱'.number_format($data[$i] ?? 0, 2) : number_format($data[$i] ?? 0, 0) }})</text>
            @endforeach
        </svg>
    @elseif($type === 'line')
        @php
            $W = 640;
            $H = 140;
            $padL = 50;
            $padB = 45;
            $padT = 15;
            $gw = $W - $padL - 10;
            $gh = $H - $padB - $padT;
            $cnt = count($data);
            $step = $cnt > 1 ? $gw / ($cnt - 1) : 0;
            $pts = [];
            foreach ($data as $i => $val) {
                $x = $cnt > 1 ? $padL + ($step * $i) : $padL + $gw / 2;
                $y = $padT + $gh - (($val / $maxVal) * $gh);
                $pts[] = round($x, 2) . ',' . round($y, 2);
            }
            $poly = implode(' ', $pts);
        @endphp
        <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="200" viewBox="0 0 {{ $W + 20 }} {{ $H + 30 }}" style="max-width: 100%;">
            <rect x="{{ $padL }}" y="{{ $padT }}" width="{{ $gw }}" height="{{ $gh }}" fill="none" stroke="#ddd" stroke-width="1"/>
            <polyline points="{{ $poly }}" fill="none" stroke="#0d6efd" stroke-width="2"/>
            @foreach($data as $i => $val)
                @php
                    $x = $cnt > 1 ? $padL + ($step * $i) : $padL + $gw / 2;
                    $y = $padT + $gh - (($val / $maxVal) * $gh);
                @endphp
                <circle cx="{{ $x }}" cy="{{ $y }}" r="3" fill="#0d6efd"/>
                <text x="{{ $x }}" y="{{ $H + 8 }}" font-size="6" text-anchor="middle" transform="rotate(-35 {{ $x }} {{ $H + 8 }})">{{ \Illuminate\Support\Str::limit($labels[$i] ?? '', 8) }}</text>
            @endforeach
        </svg>
    @else
        @php
            $W = 640;
            $H = 150;
            $padL = 45;
            $padB = 50;
            $padT = 12;
            $gw = $W - $padL - 10;
            $gh = $H - $padB - $padT;
            $barCount = max(count($data), 1);
            $slot = $gw / $barCount;
            $barW = max(4, $slot * 0.55);
        @endphp
        <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="210" viewBox="0 0 {{ $W + 20 }} {{ $H + 25 }}" style="max-width: 100%;">
            <line x1="{{ $padL }}" y1="{{ $padT + $gh }}" x2="{{ $padL + $gw }}" y2="{{ $padT + $gh }}" stroke="#333" stroke-width="1"/>
            @foreach($data as $i => $val)
                @php
                    $h = ($val / $maxVal) * $gh;
                    $x = $padL + $i * $slot + ($slot - $barW) / 2;
                    $y = $padT + $gh - $h;
                @endphp
                <rect x="{{ $x }}" y="{{ $y }}" width="{{ $barW }}" height="{{ max($h, 1) }}" fill="rgba(13,110,253,0.75)" stroke="#0d6efd" stroke-width="0.5"/>
                <text x="{{ $x + $barW / 2 }}" y="{{ $padT + $gh + 10 }}" font-size="6" text-anchor="middle" transform="rotate(-40 {{ $x + $barW / 2 }} {{ $padT + $gh + 10 }})">{{ \Illuminate\Support\Str::limit($labels[$i] ?? '', 10) }}</text>
            @endforeach
        </svg>
    @endif
</div>
@endif
