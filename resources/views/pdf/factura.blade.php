<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Factura #{{ $factura->id }}</title>
    <style>
        /* dompdf soporta un subconjunto de CSS; se mantiene simple y sin
           frameworks externos (Tailwind, etc.) a propósito. */
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #1f2937;
        }

        .encabezado {
            text-align: center;
            border-bottom: 2px solid #1f2937;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .encabezado img.logo {
            height: 50px;
            margin-bottom: 6px;
        }

        .encabezado h1 {
            font-size: 18px;
            margin: 0 0 4px 0;
        }

        .encabezado p {
            margin: 0;
            font-size: 11px;
            color: #4b5563;
        }

        .seccion {
            margin-bottom: 16px;
        }

        .seccion-titulo {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
            margin-bottom: 4px;
        }

        table.datos {
            width: 100%;
            border-collapse: collapse;
        }

        table.datos td {
            padding: 4px 0;
            vertical-align: top;
        }

        table.datos td.etiqueta {
            width: 140px;
            color: #4b5563;
        }

        .monto {
            margin-top: 20px;
            border-top: 1px solid #d1d5db;
            padding-top: 12px;
            text-align: right;
        }

        .monto .cifra {
            font-size: 20px;
            font-weight: bold;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }

        .badge-pagado {
            background-color: #dcfce7;
            color: #166534;
        }

        .badge-pendiente {
            background-color: #fef9c3;
            color: #854d0e;
        }

        .badge-anulado {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .pie {
            margin-top: 40px;
            font-size: 10px;
            color: #9ca3af;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="encabezado">
        <img class="logo" src="{{ public_path('images/logo.png') }}" alt="Clínica Benites">
        <h1>{{ config('app.name', 'Clínica Benites') }}</h1>
        <p>Comprobante de factura</p>
    </div>

    <div class="seccion">
        <div class="seccion-titulo">Paciente</div>
        <table class="datos">
            <tr>
                <td class="etiqueta">Nombre</td>
                <td>{{ trim(($factura->paciente->nombres ?? '').' '.($factura->paciente->apellidos ?? '')) ?: 'N/D' }}</td>
            </tr>
            <tr>
                <td class="etiqueta">Cédula</td>
                <td>{{ $factura->paciente->cedula ?? 'N/D' }}</td>
            </tr>
        </table>
    </div>

    @if ($factura->cita)
        <div class="seccion">
            <div class="seccion-titulo">Cita relacionada</div>
            <table class="datos">
                <tr>
                    <td class="etiqueta">Fecha</td>
                    <td>{{ \Illuminate\Support\Carbon::parse($factura->cita->fecha)->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td class="etiqueta">Médico</td>
                    <td>{{ trim(($factura->cita->medico->nombres ?? '').' '.($factura->cita->medico->apellidos ?? '')) ?: 'N/D' }}</td>
                </tr>
                <tr>
                    <td class="etiqueta">Área</td>
                    <td>{{ $factura->cita->area->nombre ?? 'N/D' }}</td>
                </tr>
            </table>
        </div>
    @endif

    <div class="seccion">
        <div class="seccion-titulo">Factura</div>
        <table class="datos">
            <tr>
                <td class="etiqueta">Número</td>
                <td>#{{ $factura->id }}</td>
            </tr>
            <tr>
                <td class="etiqueta">Fecha de emisión</td>
                <td>{{ \Illuminate\Support\Carbon::parse($factura->fecha)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td class="etiqueta">Estado de pago</td>
                <td>
                    <span class="badge badge-{{ $factura->estado_pago }}">
                        {{ ucfirst($factura->estado_pago) }}
                    </span>
                </td>
            </tr>
            @if ($factura->metodo_pago)
                <tr>
                    <td class="etiqueta">Método de pago</td>
                    <td>{{ $factura->metodo_pago }}</td>
                </tr>
            @endif
        </table>
    </div>

    <div class="monto">
        <span class="seccion-titulo">Monto total</span><br>
        <span class="cifra">${{ number_format((float) $factura->monto, 2) }}</span>
    </div>

    <div class="pie">
        Generado el {{ \Illuminate\Support\Carbon::now()->format('d/m/Y H:i') }} desde el sistema interno de {{ config('app.name', 'Clínica Benites') }}.
    </div>
</body>
</html>
