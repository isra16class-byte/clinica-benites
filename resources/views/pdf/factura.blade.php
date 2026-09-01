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

        .aviso-no-electronica {
            background-color: #fef9c3;
            color: #854d0e;
            border: 1px solid #fde047;
            padding: 8px 12px;
            margin-bottom: 16px;
            font-size: 10px;
            text-align: center;
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

        table.lineas {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        table.lineas th {
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            color: #6b7280;
            border-bottom: 1px solid #d1d5db;
            padding: 4px 6px;
        }

        table.lineas td {
            padding: 4px 6px;
            border-bottom: 1px solid #f3f4f6;
        }

        .numero {
            text-align: right;
        }

        .totales {
            margin-top: 12px;
            border-top: 1px solid #d1d5db;
            padding-top: 10px;
            text-align: right;
        }

        .totales table {
            width: 100%;
        }

        .totales td {
            padding: 2px 0;
        }

        .totales .fila-total .cifra {
            font-size: 18px;
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
        <h1>Clínica Benites</h1>
        <p>Comprobante de factura</p>
    </div>

    {{--
        Facturación electrónica SRI (MEMORIA.md sección 6): mientras el
        cliente no tenga RUC/establecimiento/punto de emisión/certificado
        .p12 tramitados, ninguna factura puede ser un comprobante válido
        ante el SRI (sin crédito tributario para el paciente) — este aviso
        evita que un PDF "no_emitida" se confunda con una factura
        electrónica real. Desaparece solo cuando estado_sri = 'autorizada'.
    --}}
    @if ($factura->estado_sri !== 'autorizada')
        <div class="aviso-no-electronica">
            Comprobante interno — no es una factura electrónica autorizada por el SRI todavía.
            @if ($factura->estado_sri === 'rechazada')
                (Rechazada por el SRI: {{ $factura->mensaje_sri }})
            @endif
        </div>
    @endif

    <div class="seccion">
        <div class="seccion-titulo">Paciente</div>
        <table class="datos">
            <tr>
                <td class="etiqueta">Nombre</td>
                <td>{{ trim(($factura->paciente->nombres ?? '').' '.($factura->paciente->apellidos ?? '')) ?: 'N/D' }}</td>
            </tr>
            <tr>
                <td class="etiqueta">{{ $factura->paciente?->tipo_identificacion === 'ruc' ? 'RUC' : 'Cédula' }}</td>
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
                <td>{{ $factura->numeroComprobante() ?? '#'.$factura->id.' (sin emitir)' }}</td>
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
            @if ($factura->forma_pago)
                <tr>
                    <td class="etiqueta">Forma de pago</td>
                    <td>{{ \App\Models\Factura::FORMAS_PAGO[$factura->forma_pago] ?? $factura->forma_pago }}</td>
                </tr>
            @endif
            @if ($factura->clave_acceso)
                <tr>
                    <td class="etiqueta">Clave de acceso</td>
                    <td style="font-size: 9px;">{{ $factura->clave_acceso }}</td>
                </tr>
            @endif
            @if ($factura->numero_autorizacion)
                <tr>
                    <td class="etiqueta">N.° autorización</td>
                    <td>{{ $factura->numero_autorizacion }}</td>
                </tr>
            @endif
        </table>
    </div>

    <div class="seccion">
        <div class="seccion-titulo">Detalle</div>
        <table class="lineas">
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th class="numero">Cant.</th>
                    <th class="numero">P. unitario</th>
                    <th class="numero">Desc.</th>
                    <th class="numero">IVA</th>
                    <th class="numero">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($factura->lineas as $linea)
                    <tr>
                        <td>{{ $linea->descripcion }}</td>
                        <td class="numero">{{ $linea->cantidad }}</td>
                        <td class="numero">${{ number_format((float) $linea->precio_unitario, 2) }}</td>
                        <td class="numero">${{ number_format((float) $linea->descuento, 2) }}</td>
                        <td class="numero">{{ \App\Models\LineaFactura::TARIFAS_IVA[$linea->codigo_iva]['label'] ?? $linea->codigo_iva }}</td>
                        <td class="numero">${{ number_format((float) $linea->subtotal, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="color: #9ca3af;">Sin líneas registradas todavía.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="totales">
        <table>
            <tr>
                <td>Subtotal</td>
                <td class="numero">${{ number_format((float) $factura->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td>IVA</td>
                <td class="numero">${{ number_format((float) $factura->iva, 2) }}</td>
            </tr>
            <tr class="fila-total">
                <td><span class="cifra">Total</span></td>
                <td class="numero"><span class="cifra">${{ number_format((float) $factura->total, 2) }}</span></td>
            </tr>
        </table>
    </div>

    <div class="pie">
        Generado el {{ \Illuminate\Support\Carbon::now()->format('d/m/Y H:i') }} desde el sistema interno de Clínica Benites.
    </div>
</body>
</html>
