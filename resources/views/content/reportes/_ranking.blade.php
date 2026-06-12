@if($por_unidad->isEmpty())
  <p class="text-muted text-center py-3 small">Sin datos de unidades</p>
@else
  @foreach($por_unidad->take(8) as $ru)
  @php $rc = $ru['porcentaje'] >= 75 ? '#28c76f' : ($ru['porcentaje'] >= 50 ? '#ff9f43' : '#ea5455'); @endphp
  <div class="rank-row">
    <span class="rank-label" title="{{ $ru['nombre'] }}">{{ $ru['nombre'] }}</span>
    <div class="rank-bar-bg">
      <div class="rank-bar-fg" style="width:{{ $ru['porcentaje'] }}%;background:{{ $rc }}"></div>
    </div>
    <div class="rank-pct" style="color:{{ $rc }}">
      {{ $ru['porcentaje'] }}%
      <div class="rank-sub">{{ $ru['completadas'] }}/{{ $ru['total'] }}</div>
    </div>
  </div>
  @endforeach
@endif
