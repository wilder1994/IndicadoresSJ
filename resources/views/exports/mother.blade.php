<table>
    <tr><td colspan="5"><strong>Consolidado MADRE</strong></td></tr>
    <tr><td>Indicador</td><td colspan="4">{{ $indicator->code }} - {{ $indicator->name }}</td></tr>
    <tr><td>Periodo</td><td colspan="4">{{ $year }}-{{ str_pad((string) $month, 2, '0', STR_PAD_LEFT) }}</td></tr>
    <tr><td colspan="5"></td></tr>
    <tr>
        <td><strong>Zona</strong></td>
        <td><strong>Numerador</strong></td>
        <td><strong>Denominador</strong></td>
        <td><strong>%</strong></td>
        <td><strong>Semaforo</strong></td>
    </tr>
    @foreach ($monthly['rows'] as $row)
        <tr>
            <td>{{ $row['zone']->code }}</td>
            <td>{{ $row['capture']?->numerator }}</td>
            <td>{{ $row['capture']?->denominator }}</td>
            <td>{{ $row['result_percentage'] }}</td>
            <td>{{ $row['semaforo'] }}</td>
        </tr>
    @endforeach
</table>
