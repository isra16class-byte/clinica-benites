<?php

namespace App\Filament\Widgets;

use App\Models\Cama;
use App\Models\Cita;
use App\Models\Factura;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

/**
 * Sesión 1 del Dashboard gerencial (MEMORIA.md sección 6.6): los 4
 * indicadores clave para que el admin sepa "si está ganando o no" de un
 * vistazo, sin sumar facturas a mano. Solo lectura — no crea tablas ni
 * modifica ningún modelo, todo son queries sobre lo ya existente.
 *
 * Visible solo para el rol admin (mismo patrón ->visible()/canView() usado
 * en el resto del panel, ver sección 10 de MEMORIA.md), y ubicado arriba de
 * CitasDeHoyWidget (sort 1) gracias a $sort = 0.
 */
class IndicadoresGerencialesWidget extends BaseWidget
{
    protected static ?int $sort = 0;

    public static function canView(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
        return [
            $this->statIngresosDelMes(),
            $this->statPorCobrar(),
            $this->statCitasAtendidas(),
            $this->statOcupacionCamas(),
        ];
    }

    /**
     * Ingresos del mes: suma de facturas.monto pagadas dentro del mes
     * actual, con comparación (%) contra el total pagado el mes anterior.
     */
    private function statIngresosDelMes(): Stat
    {
        $inicioMesActual = Carbon::now()->startOfMonth();
        $finMesActual = Carbon::now()->endOfMonth();
        $inicioMesAnterior = Carbon::now()->subMonthNoOverflow()->startOfMonth();
        $finMesAnterior = Carbon::now()->subMonthNoOverflow()->endOfMonth();

        $ingresosMesActual = (float) Factura::query()
            ->where('estado_pago', 'pagado')
            ->whereBetween('fecha', [$inicioMesActual, $finMesActual])
            ->sum('monto');

        $ingresosMesAnterior = (float) Factura::query()
            ->where('estado_pago', 'pagado')
            ->whereBetween('fecha', [$inicioMesAnterior, $finMesAnterior])
            ->sum('monto');

        if ($ingresosMesAnterior > 0) {
            $variacion = (($ingresosMesActual - $ingresosMesAnterior) / $ingresosMesAnterior) * 100;
            $descripcion = sprintf('%+.1f%% vs. mes anterior', $variacion);
            $color = $variacion >= 0 ? 'success' : 'danger';
            $icono = $variacion >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';
        } else {
            $descripcion = $ingresosMesActual > 0
                ? 'Mes anterior sin ingresos registrados'
                : 'Sin ingresos este mes';
            $color = $ingresosMesActual > 0 ? 'success' : 'gray';
            $icono = null;
        }

        return Stat::make('Ingresos del mes', '$'.number_format($ingresosMesActual, 2))
            ->description($descripcion)
            ->descriptionIcon($icono)
            ->color($color);
    }

    /**
     * Por cobrar: dinero ya facturado (estado_pago = pendiente) que
     * todavía no entró a caja. No se limita al mes actual — es la
     * cartera pendiente completa, para que no se pierda de vista una
     * factura pendiente de meses anteriores.
     *
     * Jerarquía por tamaño (26 ago 2026, dirección A de MEMORIA.md /
     * DISEÑO.md): esta tarjeta gana la clase `cb-stat-destacado` (ver
     * theme.css) para ocupar más espacio visual que las otras 3 — pero
     * SOLO si hay deuda real (> 0). Si "Por cobrar" da $0 (estado
     * "success", sin deuda pendiente), destacarla pierde sentido, así
     * que la jerarquía queda condicional al monto, tal como se anticipó
     * como riesgo al proponer esta dirección.
     */
    private function statPorCobrar(): Stat
    {
        $porCobrar = (float) Factura::query()
            ->where('estado_pago', 'pendiente')
            ->sum('monto');

        return Stat::make('Por cobrar', '$'.number_format($porCobrar, 2))
            ->description('Facturado y aún sin pagar')
            ->color($porCobrar > 0 ? 'warning' : 'success')
            ->extraAttributes($porCobrar > 0 ? ['class' => 'cb-stat-destacado'] : []);
    }

    /**
     * Citas atendidas hoy / en la semana (estado = atendida). La semana
     * usa el inicio de semana por defecto de Carbon (lunes).
     */
    private function statCitasAtendidas(): Stat
    {
        $hoy = Cita::query()
            ->whereDate('fecha', today())
            ->where('estado', 'atendida')
            ->count();

        $semana = Cita::query()
            ->whereBetween('fecha', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
            ])
            ->where('estado', 'atendida')
            ->count();

        return Stat::make('Citas atendidas hoy', (string) $hoy)
            ->description("{$semana} atendidas esta semana")
            ->color('info');
    }

    /**
     * Ocupación de camas en tiempo real: camas con un internamiento
     * activo (fecha_alta nula) sobre el total de camas registradas.
     * Mismo criterio que Cama::ocupada(), pero como query agregada para
     * no recorrer cada registro uno por uno.
     */
    private function statOcupacionCamas(): Stat
    {
        $total = Cama::query()->count();

        $ocupadas = Cama::query()
            ->whereHas('internamientos', fn ($query) => $query->whereNull('fecha_alta'))
            ->count();

        if ($total === 0) {
            return Stat::make('Ocupación de camas', 'Sin camas registradas')
                ->color('gray');
        }

        $porcentaje = round(($ocupadas / $total) * 100);

        return Stat::make('Ocupación de camas', "{$ocupadas} / {$total}")
            ->description("{$porcentaje}% ocupado")
            ->color($porcentaje >= 90 ? 'danger' : ($porcentaje >= 70 ? 'warning' : 'success'));
    }
}
