<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Dashboard Ejecutivo {{ $year }}-{{ $month }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111827; }
        h1, h2 { margin: 0 0 8px 0; }
        .mb-16 { margin-bottom: 16px; }
        .grid { width: 100%; }
        .card { border: 1px solid #e5e7eb; padding: 8px; margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #e5e7eb; padding: 6px; text-align: left; }
        th { background: #f9fafb; font-size: 11px; text-transform: uppercase; }
        .ok { color: #047857; font-weight: 700; }
        .bad { color: #b91c1c; font-weight: 700; }
    </style>
</head>
<body>
    <h1>Dashboard General de Operaciones</h1>
    <p class="mb-16">Periodo: {{ $year }}-{{ str_pad((string) $month, 2, '0', STR_PAD_LEFT) }}</p>

    <div class="card">
        <h2>Cumplimiento Global</h2>
        <p>Score global: <strong>{{ number_format($dashboard['global_score'], 2) }}%</strong></p>
        <p>Estado: <strong>{{ $dashboard['global_state'] }}</strong></p>
    </div>

    <div class="card">
        <h2>Tarjetas KPI</h2>
        <table>
            <thead>
                <tr>
                    <th>Indicador</th>
                    <th>Resultado</th>
                    <th>Meta</th>
                    <th>Estado</th>
                    <th>Mejoras</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($dashboard['kpis'] as $kpi)
                    <tr>
                        <td>{{ $kpi['indicator']->code }} - {{ $kpi['indicator']->name }}</td>
                        <td>{{ $kpi['result'] !== null ? number_format((float) $kpi['result'], 2).'%' : '-' }}</td>
                        <td>{{ $kpi['meta'] }}</td>
                        <td class="{{ $kpi['semaforo'] === 'VERDE' ? 'ok' : 'bad' }}">{{ $kpi['semaforo'] }}</td>
                        <td>{{ $kpi['has_improvements'] ? 'SI' : 'NO' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card">
        <h2>Ranking de Zonas</h2>
        <table>
            <thead>
                <tr>
                    <th>Posicion</th>
                    <th>Zona</th>
                    <th>Score</th>
                    <th>Rojos</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($dashboard['zone_ranking'] as $index => $row)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $row['zone']->name }}</td>
                        <td>{{ number_format($row['score'], 2) }}%</td>
                        <td>{{ $row['red_count'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card">
        <h2>Ranking Indicadores Criticos</h2>
        <table>
            <thead>
                <tr>
                    <th>Indicador</th>
                    <th>Resultado</th>
                    <th>Meta</th>
                    <th>Zonas rojo</th>
                    <th>Criticidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($dashboard['critical_ranking'] as $row)
                    <tr>
                        <td>{{ $row['indicator']->code }}</td>
                        <td>{{ $row['result'] !== null ? number_format((float) $row['result'], 2).'%' : '-' }}</td>
                        <td>{{ $row['meta'] }}</td>
                        <td>{{ $row['zones_red'] }}</td>
                        <td>{{ number_format((float) $row['criticality'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card">
        <h2>Resumen Ejecutivo</h2>
        <p style="white-space: pre-wrap;">{{ $summary?->summary_text ?? 'Sin resumen guardado.' }}</p>
    </div>
</body>
</html>
