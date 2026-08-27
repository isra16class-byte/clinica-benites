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
 *
 * Acento de color (26 ago 2026, a pedido del usuario tras ver el borde
 * izquierdo de "Por cobrar" en el entorno real: "me gustó, se lo puedes
 * poner a los demás con su respectivo color?"): las 4 tarjetas pasan una
 * clase `cb-stat-accent-{color}` (ver theme.css) calculada a partir del
 * MISMO color que ya se le pasa a `->color()` en cada stat — así el borde
 * de acento siempre coincide con el color real del stat
 * (success/danger/warning/info/gray), sin mantener esa lógica duplicada en
 * dos lugares. `->color()` de Filament NO pinta la tarjeta ni su borde por
 * sí solo (confirmado en `packages/widgets/src/StatsOverviewWidget/Stat.php`
 * de la v5.7.6 — solo alimenta el color del ícono/texto de la descripción
 * y, si hubiera, el del gráfico de fondo), de ahí la necesidad de esta
 * clase aparte.
 *
 * Grid asimétrico (26 ago 2026, dirección C de MEMORIA.md/DISEÑO.md,
 * tercera de las 3 direcciones propuestas para "Refinar tarjetas y
 * gráficos" — A y el pedido de generalizar el acento ya están resueltos y
 * confirmados): "Por cobrar" pasa a ocupar 2 unidades de un grid de 5 (en
 * vez de 1 de 4), los otros 3 KPIs quedan en 1 unidad cada uno — filas
 * exactas: 2+1+1+1 = 5, sin sobrantes ni huecos. Confirmado contra
 * `Stat.php`/`CanSpanColumns.php` de Filament 5.7.6 que `Stat` SÍ soporta
 * `->columnSpan()` (hereda de `Component`, que usa el trait
 * `CanSpanColumns` — no es un método propio de formularios nada más). A
 * diferencia de la dirección A (int fijo `4`, que Filament resuelve como
 * `'lg' => 4` con el mobile ya en 1 columna por defecto, confirmado en
 * `Filament\Schemas\Concerns\HasColumns::getAllColumns()`), acá se pasa el
 * array explícito para no perder ese comportamiento responsivo ya
 * correcto: en mobile/tablet (por debajo de `lg`) sigue en 1 columna
 * apilada, y recién en `lg` (1024px+) es donde entra el grid de 5 con
 * "Por cobrar" ocupando 2. Mismo cuidado en el `columnSpan` de la propia
 * tarjeta (ver `statPorCobrar()`): span 1 por debajo de `lg`, span 2 solo
 * desde `lg` — así el span de 2 nunca compite con un grid que todavía no
 * llegó a 5 columnas.
 */
class IndicadoresGerencialesWidget extends BaseWidget
{
    protected static ?int $sort = 0;

    public static function canView(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    protected function getColumns(): array
    {
        return [
            'default' => 1,
            'lg' => 5,
        ];
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
            ->color($color)
            ->extraAttributes(['class' => "cb-stat-accent-{$color}"]);
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
     *
     * FIX (26 ago 2026, mismo reporte): esta tarjeta es la única que ya
     * llevaba `cb-stat-destacado`, que hoy es la que pinta el borde
     * izquierdo de acento (ver theme.css) — se le suma también
     * `cb-stat-accent-{color}` (mismo mecanismo que las otras 3, ver
     * `statIngresosDelMes()`) para que el color de acento sea siempre
     * consistente con el color real del stat, incluso cuando no hay
     * deuda (estado "success", sin `cb-stat-destacado`).
     *
     * Grid asimétrico (26 ago 2026, dirección C): además de las clases de
     * arriba, esta tarjeta gana `->columnSpan(['default' => 1, 'lg' => 2])`
     * (2 unidades del grid de 5 de `getColumns()`, solo desde `lg`; 1 unidad
     * por debajo, donde el grid entero sigue apilado en 1 columna) y, SOLO
     * cuando hay deuda > 0, la clase `cb-stat-asimetrico` (ver theme.css)
     * con un tinte de fondo sutil en el mismo warning que ya tiene el
     * acento del borde — mismo condicional de "destacar solo si aplica"
     * que ya se usa en `cb-stat-destacado`, para no teñir de warning una
     * tarjeta en $0 (estado "success", sin deuda).
     */
    private function statPorCobrar(): Stat
    {
        $porCobrar = (float) Factura::query()
            ->where('estado_pago', 'pendiente')
            ->sum('monto');

        $color = $porCobrar > 0 ? 'warning' : 'success';
        $clases = $porCobrar > 0
            ? "cb-stat-accent-{$color} cb-stat-destacado cb-stat-asimetrico"
            : "cb-stat-accent-{$color}";

        return Stat::make('Por cobrar', '$'.number_format($porCobrar, 2))
            ->description('Facturado y aún sin pagar')
            ->color($color)
            ->columnSpan(['default' => 1, 'lg' => 2])
            ->extraAttributes(['class' => $clases]);
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
            ->color('info')
            ->extraAttributes(['class' => 'cb-stat-accent-info']);
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
                ->color('gray')
                ->extraAttributes(['class' => 'cb-stat-accent-gray']);
        }

        $porcentaje = round(($ocupadas / $total) * 100);
        $color = $porcentaje >= 90 ? 'danger' : ($porcentaje >= 70 ? 'warning' : 'success');

        return Stat::make('Ocupación de camas', "{$ocupadas} / {$total}")
            ->description("{$porcentaje}% ocupado")
            ->color($color)
            ->extraAttributes(['class' => "cb-stat-accent-{$color}"]);
    }
}
