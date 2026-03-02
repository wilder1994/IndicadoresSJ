<table>
    <tr><td colspan="2"><strong>Reporte por Zona</strong></td></tr>
    <tr><td>Indicador</td><td>{{ $indicator->code }} - {{ $indicator->name }}</td></tr>
    <tr><td>Zona</td><td>{{ $zone->code }} - {{ $zone->name }}</td></tr>
    <tr><td>Periodo</td><td>{{ $year }}-{{ str_pad((string) $month, 2, '0', STR_PAD_LEFT) }}</td></tr>
    <tr><td colspan="2"></td></tr>
    <tr><td><strong>Campo</strong></td><td><strong>Valor</strong></td></tr>
    @foreach ($display as $key => $value)
        <tr><td>{{ $key }}</td><td>{{ $value }}</td></tr>
    @endforeach
    <tr><td>Resultado %</td><td>{{ $capture?->result_percentage }}</td></tr>
    <tr><td>Semaforo</td><td>{{ $capture ? ($capture->complies ? 'VERDE' : 'ROJO') : '-' }}</td></tr>
    <tr><td>Analisis</td><td>{{ $capture?->analysis_text }}</td></tr>
    <tr><td>Mejora - Analisis</td><td>{{ $capture?->improvement?->analysis }}</td></tr>
    <tr><td>Mejora - Accion tomada</td><td>{{ $capture?->improvement?->action_taken }}</td></tr>
    <tr><td>Mejora - Accion definida</td><td>{{ $capture?->improvement?->action_defined }}</td></tr>
</table>
