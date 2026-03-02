<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 5px; text-align: left; }
        h2,h3 { margin: 0 0 6px 0; }
    </style>
</head>
<body>
    <h2>Consolidado MADRE</h2>
    <p><strong>Indicador:</strong> {{ $indicator->code }} - {{ $indicator->name }}</p>
    <p><strong>Periodo:</strong> {{ $year }}-{{ str_pad((string) $month, 2, '0', STR_PAD_LEFT) }}</p>

    <table>
        <thead>
            <tr>
                <th>Zona</th>
                <th>Numerador</th>
                <th>Denominador</th>
                <th>%</th>
                <th>Semaforo</th>
                <th>Analisis</th>
                <th>Mejora</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($monthly['rows'] as $row)
                <tr>
                    <td>{{ $row['zone']->code }}</td>
                    <td>{{ $row['capture']?->numerator }}</td>
                    <td>{{ $row['capture']?->denominator }}</td>
                    <td>{{ $row['result_percentage'] !== null ? number_format((float) $row['result_percentage'], 2).'%' : '-' }}</td>
                    <td>{{ $row['semaforo'] }}</td>
                    <td>{{ $row['analysis_text'] }}</td>
                    <td>{{ $row['has_improvement'] ? 'SI' : 'NO' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Consolidado</h3>
    @if ($indicator->code === 'FT-OP-03')
        <p>A Frecuencia: {{ $monthly['consolidated']['a']['result_percentage'] !== null ? number_format($monthly['consolidated']['a']['result_percentage'],2).'%' : '-' }} ({{ $monthly['consolidated']['a']['semaforo'] }})</p>
        <p>B Impacto: {{ $monthly['consolidated']['b']['result_percentage'] !== null ? number_format($monthly['consolidated']['b']['result_percentage'],2).'%' : '-' }} ({{ $monthly['consolidated']['b']['semaforo'] }})</p>
        <p>Estado final: {{ $monthly['consolidated']['final'] }}</p>
    @else
        <p>Numerador total: {{ $monthly['consolidated']['numerator'] }}</p>
        <p>Denominador total: {{ $monthly['consolidated']['denominator'] }}</p>
        <p>% consolidado: {{ $monthly['consolidated']['result_percentage'] !== null ? number_format($monthly['consolidated']['result_percentage'],2).'%' : '-' }}</p>
        <p>Semaforo: {{ $monthly['consolidated']['semaforo'] }}</p>
    @endif
</body>
</html>
