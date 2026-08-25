<?php

namespace App\Filament\Widgets;

use App\Models\Area;
use App\Models\Factura;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

/**
 * Sesión 2 del Dashboard gerencial (MEMORIA.md sección 6.6/6.6.2):
 * segundo de los 2 gráficos planificados — qué área/especialidad genera
 * más citas o más facturación, con un selector para alternar entre
 * ambas métricas (a pedido del usuario, ver sección 6.6.2).
 *
 * Alcance fijo al año en curso — supuesto razonable documentado en
 * MEMORIA.md 6.6.2 (la pregunta pendiente de "rango seleccionable" se
 * resolvió con un selector en el gráfico de ingresos; agregar un
 * segundo selector de rango acá, además del de métrica, hubiera hecho
 * falta usar el mecanismo de filtros por schema de Filament, más nuevo
 * y sin validar en este proyecto — se prefirió mantener el patrón
 * simple de un solo filtro por widget, ya probado). Editable después
 * si la clínica prefiere otro rango.
 *
 * Solo lectura — no crea tablas ni modifica ningún modelo.
 */
class FacturacionPorAreaChartWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Por área — año en curso';

    public ?string $filter = 'citas';

    public static function canView(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    protected function getFilters(): ?array
    {
        return [
            'citas' => 'Cantidad de citas',
            'facturacion' => 'Monto facturado',
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    /**
     * Formatea el eje Y y el tooltip. Con "Monto facturado" agrega
     * signo $ y separador de miles; con "Cantidad de citas" deja el
     * número simple (con separador de miles igual, por prolijidad).
     *
     * FIX (25 ago 2026): antes esto devolvía un array vacío `[]` para
     * "Cantidad de citas" y un `RawJs` para "Monto facturado" — ese
     * cambio de *tipo* de valor entre una métrica y otra rompía el
     * gráfico (quedaba en blanco) al cambiar el selector con Livewire,
     * confirmado por el usuario en el entorno real. Ahora siempre
     * devuelve `RawJs` con el mismo tipo/estructura, y la decisión de
     * anteponer "$" o no queda resuelta *adentro* del JS (interpolando
     * un booleano de PHP), en vez de cambiar la forma de lo que se
     * devuelve.
     */
    protected function getOptions(): RawJs
    {
        $esFacturacion = $this->filter === 'facturacion' ? 'true' : 'false';

        return RawJs::make(<<<JS
            {
                scales: {
                    y: {
                        ticks: {
                            callback: (value) => ({$esFacturacion} ? '$' : '') + value.toLocaleString('es-EC'),
                        },
                    },
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: (context) => context.dataset.label + ': ' + ({$esFacturacion} ? '$' : '') + context.parsed.y.toLocaleString('es-EC'),
                        },
                    },
                },
            }
        JS);
    }

    protected function getData(): array
    {
        $inicio = Carbon::now()->startOfYear();
        $fin = Carbon::now()->endOfYear();

        $areas = Area::query()->orderBy('nombre')->get();

        $esFacturacion = $this->filter === 'facturacion';

        $valores = $esFacturacion
            ? $this->montoFacturadoPorArea($areas, $inicio, $fin)
            : $this->citasPorArea($areas, $inicio, $fin);

        return [
            'datasets' => [
                [
                    'label' => $esFacturacion ? 'Monto facturado ($)' : 'Citas',
                    'data' => $valores,
                    'backgroundColor' => '#0C447C',
                ],
            ],
            'labels' => $areas->pluck('nombre')->all(),
        ];
    }

    /**
     * Cantidad de citas por área en el año en curso, sin filtrar por
     * estado — una cita cancelada igual indica demanda del área, y
     * filtrar solo "atendida" subestimaría áreas con mucha cancelación.
     */
    private function citasPorArea(Collection $areas, Carbon $inicio, Carbon $fin): array
    {
        return $areas
            ->map(fn (Area $area) => $area->citas()
                ->whereBetween('fecha', [$inicio, $fin])
                ->count())
            ->all();
    }

    /**
     * Monto facturado por área. Pasa por Factura -> Cita -> Area (Area
     * no tiene relación directa con Factura, ver sección 4 de
     * MEMORIA.md), así que una factura sin cita_id asociado (permitido,
     * cita_id es nullable) no se puede atribuir a ningún área con el
     * modelo actual y queda fuera de este gráfico — limitación conocida,
     * no un bug.
     */
    private function montoFacturadoPorArea(Collection $areas, Carbon $inicio, Carbon $fin): array
    {
        return $areas
            ->map(fn (Area $area) => (float) Factura::query()
                ->whereHas('cita', fn ($query) => $query->where('area_id', $area->id))
                ->whereBetween('fecha', [$inicio, $fin])
                ->sum('monto'))
            ->all();
    }
}
