<?php

namespace App\Filament\Widgets;

use App\Models\Factura;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

/**
 * Sesión 2 del Dashboard gerencial (MEMORIA.md sección 6.6/6.6.2):
 * primero de los 2 gráficos planificados — tendencia de ingresos por
 * mes, con selector de rango de fechas (a pedido del usuario, ver
 * sección 6.6.2). Muestra 2 series por mes: lo facturado en total y lo
 * efectivamente cobrado (estado_pago = pagado), para distinguir "lo que
 * se vendió" de "lo que entró a caja" — la misma distinción que ya usa
 * IndicadoresGerencialesWidget entre "ingresos del mes" y "por cobrar".
 *
 * Solo lectura — no crea tablas ni modifica ningún modelo, es 100%
 * queries sobre Factura, igual que el resto del Dashboard gerencial.
 */
class IngresosPorMesChartWidget extends ChartWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Ingresos por mes';

    public ?string $filter = 'seis_meses';

    public static function canView(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    protected function getFilters(): ?array
    {
        return [
            'seis_meses' => 'Últimos 6 meses',
            'doce_meses' => 'Últimos 12 meses',
            'anio_actual' => 'Año actual',
            'anio_anterior' => 'Año anterior',
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    /**
     * Las 2 series son siempre montos en dólares, así que a diferencia
     * de FacturacionPorAreaChartWidget (donde el formato solo aplica en
     * una de las 2 métricas) acá se aplica siempre, sin condicional.
     */
    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<'JS'
            {
                scales: {
                    y: {
                        ticks: {
                            callback: (value) => '$' + value.toLocaleString('es-EC'),
                        },
                    },
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: (context) => context.dataset.label + ': $' + context.parsed.y.toLocaleString('es-EC'),
                        },
                    },
                },
            }
        JS);
    }

    protected function getData(): array
    {
        [$inicio, $fin] = $this->rangoSeleccionado();

        $labels = [];
        $facturado = [];
        $cobrado = [];

        $mes = $inicio->copy()->startOfMonth();

        while ($mes <= $fin) {
            $inicioMes = $mes->copy()->startOfMonth();
            $finMes = $mes->copy()->endOfMonth();

            $labels[] = ucfirst($mes->translatedFormat('M Y'));

            $facturado[] = (float) Factura::query()
                ->whereBetween('fecha', [$inicioMes, $finMes])
                ->sum('total');

            $cobrado[] = (float) Factura::query()
                ->where('estado_pago', 'pagado')
                ->whereBetween('fecha', [$inicioMes, $finMes])
                ->sum('total');

            $mes->addMonthNoOverflow();
        }

        return [
            'datasets' => [
                [
                    // Jerarquía por tamaño (26 ago 2026, dirección A de
                    // MEMORIA.md/DISEÑO.md): #85B7EB era un celeste
                    // genérico que no correspondía a ninguno de los 2
                    // colores de marca. #6D8FB0 es el navy de marca
                    // (#0C447C) aclarado ~40% con blanco — mismo criterio
                    // de "derivar del navy" ya documentado en DISEÑO.md
                    // en vez de introducir un color nuevo sin relación.
                    'label' => 'Facturado',
                    'data' => $facturado,
                    'backgroundColor' => '#6D8FB0',
                ],
                [
                    'label' => 'Cobrado',
                    'data' => $cobrado,
                    'backgroundColor' => '#0F6E56',
                ],
            ],
            'labels' => $labels,
        ];
    }

    /**
     * Traduce el filtro seleccionado a un rango [inicio, fin] de meses.
     * El fin nunca pasa del mes en curso (salvo "año anterior", que es
     * un rango cerrado en el pasado) — evita mostrar meses futuros
     * vacíos en "año actual". Se valida con match() + default en vez de
     * usar $this->filter directo en la query, tal como recomienda la
     * documentación de Filament (el valor es controlable por el usuario).
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    private function rangoSeleccionado(): array
    {
        $finMesActual = Carbon::now()->endOfMonth();

        return match ($this->filter) {
            'doce_meses' => [Carbon::now()->subMonthsNoOverflow(11)->startOfMonth(), $finMesActual],
            'anio_actual' => [Carbon::now()->startOfYear(), $finMesActual],
            'anio_anterior' => [
                Carbon::now()->subYearNoOverflow()->startOfYear(),
                Carbon::now()->subYearNoOverflow()->endOfYear(),
            ],
            default => [Carbon::now()->subMonthsNoOverflow(5)->startOfMonth(), $finMesActual],
        };
    }
}
