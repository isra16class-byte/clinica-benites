<?php

namespace App\Filament\Widgets;

use App\Models\Cama;
use App\Models\Cita;
use App\Models\Factura;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;

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
 * Tarjetas presionables — dirección A "expande hacia abajo" (26 ago 2026,
 * a pedido del usuario: "que las tarjetas sean presionables... y muestre
 * más detalles"). Se descartaron 2 alternativas antes de elegir esta (ver
 * MEMORIA.md): B (la tarjeta crece en ANCHO, empujando a las otras) tenía
 * más riesgo de CSS nuevo sin poder probarlo en vivo; C (popover flotante)
 * no se sentía como "la tarjeta se transforma", que era el pedido.
 *
 * Mecanismo (confirmado contra el código fuente real de Filament 5.7.6 y
 * Livewire 4.4.1, no contra documentación de comunidad):
 * - `Stat::value()` acepta `string|Htmlable|Closure` (no solo string) —
 *   confirmado en el constructor de `Stat.php` — así que en vez de pasar
 *   el número como texto plano, se pasa un `HtmlString` que envuelve el
 *   número + un bloque de detalle oculto, todo dentro del mismo
 *   `<div class="fi-wi-stats-overview-stat-value">` que ya trae el Blade
 *   (nunca forkeado). El bloque de detalle usa `x-show="expanded"` +
 *   `x-collapse` para animar el alto en vez de aparecer de golpe.
 * - `x-collapse` NO lo registra Filament — lo registra Livewire 4.4.1
 *   globalmente (`js/lifecycle.js`, `Alpine.plugin(collapse)`), confirmado
 *   clonando el repo de Livewire en el tag instalado (`composer.lock`).
 *   Ya está disponible sin instalar nada nuevo; mismo mecanismo que ya usa
 *   el propio sidebar colapsable de Filament.
 * - El clic/teclado (`x-data`, `@click`, `@keydown`) vive en el `<div>`
 *   exterior de la tarjeta vía `extraAttributes()` — confirmado en
 *   `HasExtraAttributes.php` que ese método admite cualquier atributo, no
 *   solo `class`, y no lo escapa.
 * - **Problema no anticipado en la propuesta original, resuelto en
 *   `theme.css`**: `.fi-wi-stats-overview-stat` trae `h-full` de fábrica
 *   dentro de un grid con `align-items: stretch` (confirmado en
 *   `stats-overview-widget.css` de Filament) — si una tarjeta crece, la
 *   fila entera se estira y las otras 3 quedarían con aire vacío abajo
 *   (mismo tipo de problema que hizo descartar la dirección "C" del
 *   pulido visual, aunque por otro mecanismo). Sacar el `stretch` siempre
 *   rompería a "Por cobrar" (`cb-stat-destacado`), que hoy depende de ese
 *   mismo stretch para verse pareja con las otras 3 pese a su padding
 *   extra — ya confirmado por el usuario en el entorno real. Se usa
 *   `:has()` en `theme.css` para desactivar el stretch SOLO mientras hay
 *   una tarjeta expandida, dejando el comportamiento normal intacto en
 *   reposo.
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
     * Atributos comunes que hacen presionable una tarjeta (ver comentario
     * de clase). `$clases` es el mismo string de clases CSS que cada stat
     * ya arma hoy (acento de color + `cb-stat-destacado` si aplica) — se
     * agrega tal cual, sin tocar esa lógica existente.
     *
     * @return array<string, string>
     */
    private function atributosExpandible(string $clases): array
    {
        return [
            'class' => $clases,
            'x-data' => '{ expanded: false }',
            '@click' => 'expanded = !expanded',
            '@keydown.enter' => 'expanded = !expanded',
            '@keydown.space.prevent' => 'expanded = !expanded',
            ':class' => "{ 'cb-stat-expanded': expanded }",
            ':aria-expanded' => 'expanded',
            'role' => 'button',
            'tabindex' => '0',
        ];
    }

    /**
     * Arma el contenido del `<div class="fi-wi-stats-overview-stat-value">`
     * (ver `stat.blade.php` de Filament): el número de siempre + una
     * flecha que indica que es presionable + el bloque de detalle, oculto
     * hasta que `expanded` (definido en `atributosExpandible()`, mismo
     * `x-data` de la tarjeta que envuelve este value) sea `true`.
     * `$detalleHtml` ya viene armado (con sus propios valores escapados
     * con `e()`) por cada `desglose*()` de abajo.
     */
    private function valorConDetalle(string $valor, string $detalleHtml): HtmlString
    {
        return new HtmlString(
            '<span>'.e($valor).'</span>'
            .'<svg class="cb-stat-chevron" :class="{ \'cb-stat-chevron-abierto\': expanded }" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06z" clip-rule="evenodd" /></svg>'
            .'<div x-show="expanded" x-collapse.duration.200ms x-cloak class="cb-stat-detalle">'
            .$detalleHtml
            .'</div>'
        );
    }

    /**
     * Detalle de "Ingresos del mes": monto exacto del mes anterior y la
     * diferencia en dólares (no solo el % que ya se ve siempre) — el dato
     * concreto que el % no muestra por sí solo.
     */
    private function desgloseIngresosMes(float $actual, float $anterior): string
    {
        if ($anterior <= 0) {
            return '<p class="cb-stat-detalle-vacio">Sin datos del mes anterior para comparar.</p>';
        }

        $diferencia = $actual - $anterior;
        $signo = $diferencia >= 0 ? '+' : '-';

        return '<p class="cb-stat-detalle-texto">Mes anterior: $'.e(number_format($anterior, 2)).'</p>'
            .'<p class="cb-stat-detalle-texto">Diferencia: '.e($signo).'$'.e(number_format(abs($diferencia), 2)).'</p>';
    }

    /**
     * Detalle de "Por cobrar": cantidad de facturas pendientes y
     * antigüedad de la más vieja — el mismo dato de negocio que se había
     * propuesto (y descartado junto con el grid asimétrico, ver
     * MEMORIA.md) para la dirección C, ahora como detalle bajo demanda en
     * vez de descripción siempre visible. Mismo detalle técnico de esa
     * vez: `fecha` en `Factura` es una columna `date` sin cast declarado
     * (`Factura.php` no tiene `$casts`), así que se envuelve con
     * `Carbon::parse()` antes de `diffInDays()`.
     */
    private function desglosePorCobrar(float $porCobrar): string
    {
        if ($porCobrar <= 0) {
            return '<p class="cb-stat-detalle-vacio">Sin facturas pendientes.</p>';
        }

        $pendientes = Factura::query()->where('estado_pago', 'pendiente');

        $cantidad = (clone $pendientes)->count();
        $masAntigua = (clone $pendientes)->orderBy('fecha')->first();

        $texto = $cantidad === 1 ? '1 factura pendiente' : "{$cantidad} facturas pendientes";

        // Bug encontrado por el usuario en el entorno real (captura de
        // pantalla, "hace 316.95005496735 dias."): en Carbon 2, diffInDays()
        // devolvía int; en Carbon 3.13.2 (versión real instalada, confirmado
        // en composer.lock) el tipo de retorno cambió a float (mide precisión
        // de microsegundos, no solo días de calendario) — confirmado clonando
        // el código fuente real de nesbot/carbon tag 3.13.2
        // (Traits/Difference.php). Eso rompía 2 cosas a la vez: el match(true)
        // de abajo con === estricto nunca matcheaba un float contra los int
        // 0/1, y el string interpolado imprimía todos los decimales sin
        // redondear. alertas-operativas-widget.blade.php ya tenía este mismo
        // caso resuelto con (int) round(...) — se aplica acá el mismo criterio
        // para que quede consistente en todo el proyecto.
        $dias = (int) round(Carbon::parse($masAntigua->fecha)->diffInDays());
        $antiguedad = match (true) {
            $dias === 0 => 'hoy',
            $dias === 1 => 'hace 1 día',
            default => "hace {$dias} días",
        };

        return '<p class="cb-stat-detalle-texto">'.e($texto).'</p>'
            .'<p class="cb-stat-detalle-texto">La más antigua, '.e($antiguedad).'.</p>';
    }

    /**
     * Detalle de "Citas atendidas hoy": desglose por área, para saber de
     * un vistazo dónde se concentró la atención del día sin tener que
     * abrir la lista completa de Citas y filtrar a mano.
     */
    private function desgloseAreaCitasHoy(): string
    {
        $citas = Cita::query()
            ->whereDate('fecha', today())
            ->where('estado', 'atendida')
            ->with('area')
            ->get();

        if ($citas->isEmpty()) {
            return '<p class="cb-stat-detalle-vacio">Sin citas atendidas hoy todavía.</p>';
        }

        $porArea = $citas
            ->groupBy(fn (Cita $cita) => $cita->area?->nombre ?? 'Sin área')
            ->map->count()
            ->sortDesc();

        $filas = $porArea->map(
            fn (int $cantidad, string $area) => '<li><span>'.e($area).'</span><span>'.e((string) $cantidad).'</span></li>'
        )->implode('');

        return '<ul class="cb-stat-detalle-lista">'.$filas.'</ul>';
    }

    /**
     * Detalle de "Ocupación de camas": desglose por tipo (hospitalización
     * / UCI / UCIN, ver migración de `camas`) — el total agregado no dice
     * si la presión está en UCI (más crítico) o en hospitalización
     * general. Usa `withCount()` con la relación ya existente
     * (`Cama::internamientos()`) en vez de SQL crudo, mismo criterio de
     * `Cama::ocupada()` (estado derivado en vivo, nunca guardado aparte).
     */
    private function desgloseTipoCamas(): string
    {
        $etiquetas = [
            'hospitalizacion' => 'Hospitalización',
            'uci' => 'UCI',
            'ucin' => 'UCIN',
        ];

        $camas = Cama::query()
            ->withCount([
                'internamientos as ocupadas_count' => fn ($query) => $query->whereNull('fecha_alta'),
            ])
            ->get()
            ->groupBy('tipo');

        if ($camas->isEmpty()) {
            return '<p class="cb-stat-detalle-vacio">Sin camas registradas.</p>';
        }

        $filas = $camas->map(function ($grupo, string $tipo) use ($etiquetas) {
            $etiqueta = $etiquetas[$tipo] ?? ucfirst($tipo);
            $ocupadas = $grupo->sum('ocupadas_count');
            $total = $grupo->count();

            return '<li><span>'.e($etiqueta).'</span><span>'.e("{$ocupadas} / {$total}").'</span></li>';
        })->implode('');

        return '<ul class="cb-stat-detalle-lista">'.$filas.'</ul>';
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

        $valor = $this->valorConDetalle(
            '$'.number_format($ingresosMesActual, 2),
            $this->desgloseIngresosMes($ingresosMesActual, $ingresosMesAnterior),
        );

        return Stat::make('Ingresos del mes', $valor)
            ->description($descripcion)
            ->descriptionIcon($icono)
            ->color($color)
            ->extraAttributes($this->atributosExpandible("cb-stat-accent-{$color}"));
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
     */
    private function statPorCobrar(): Stat
    {
        $porCobrar = (float) Factura::query()
            ->where('estado_pago', 'pendiente')
            ->sum('monto');

        $color = $porCobrar > 0 ? 'warning' : 'success';
        $clases = $porCobrar > 0 ? "cb-stat-accent-{$color} cb-stat-destacado" : "cb-stat-accent-{$color}";

        $valor = $this->valorConDetalle(
            '$'.number_format($porCobrar, 2),
            $this->desglosePorCobrar($porCobrar),
        );

        return Stat::make('Por cobrar', $valor)
            ->description('Facturado y aún sin pagar')
            ->color($color)
            ->extraAttributes($this->atributosExpandible($clases));
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

        $valor = $this->valorConDetalle((string) $hoy, $this->desgloseAreaCitasHoy());

        return Stat::make('Citas atendidas hoy', $valor)
            ->description("{$semana} atendidas esta semana")
            ->color('info')
            ->extraAttributes($this->atributosExpandible('cb-stat-accent-info'));
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

        $valor = $this->valorConDetalle("{$ocupadas} / {$total}", $this->desgloseTipoCamas());

        return Stat::make('Ocupación de camas', $valor)
            ->description("{$porcentaje}% ocupado")
            ->color($color)
            ->extraAttributes($this->atributosExpandible("cb-stat-accent-{$color}"));
    }
}
