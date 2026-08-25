<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Camas\CamaResource;
use App\Filament\Resources\Facturas\FacturaResource;
use App\Filament\Resources\LotesInventario\LoteInventarioResource;
use App\Models\Cama;
use App\Models\Factura;
use App\Models\LoteInventario;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Sesión 3 (última) del Dashboard gerencial (MEMORIA.md sección 6.6/6.6.3):
 * alertas operativas que hoy son invisibles a menos que alguien entre a
 * buscarlas a mano — lotes de inventario vencidos/por vencer, facturas
 * vencidas sin cobrar, y camas ocupadas hace demasiado tiempo. Solo
 * lectura, no crea tablas ni modifica ningún modelo; cada alerta enlaza
 * directo al listado ya filtrado del recurso correspondiente.
 *
 * Visible solo para el rol admin (mismo patrón que el resto del
 * Dashboard gerencial), ubicado debajo de los 2 ChartWidget de la
 * Sesión 2 ($sort = 3) — CitasDeHoyWidget se corre de 3 a 4 para
 * dejarlo siempre al final.
 *
 * Umbrales usados (sin confirmar con la clínica todavía — supuestos
 * razonables, editables después vía las constantes de abajo, mismo
 * criterio que se aplicó con el resto de decisiones pendientes del
 * Dashboard gerencial):
 * - Lotes por vencer: 90 días — mismo default que ya usaba
 *   LoteInventario::porVencer(), confirmado por el usuario que se
 *   mantiene igual acá.
 * - Factura vencida: pendiente hace más de 30 días desde su `fecha`
 *   (no hay columna de fecha de vencimiento en `facturas`, solo fecha
 *   de emisión — se usa antigüedad desde la emisión como proxy).
 * - Cama ocupada "hace demasiado tiempo": 14 días de internamiento
 *   activo sin alta.
 */
class AlertasOperativasWidget extends Widget
{
    protected static ?int $sort = 3;

    protected string $view = 'filament.widgets.alertas-operativas-widget';

    protected int|string|array $columnSpan = 'full';

    /** Umbral (días) para considerar una factura pendiente como vencida. */
    private const DIAS_FACTURA_VENCIDA = 30;

    /** Umbral (días) para considerar una cama ocupada "hace demasiado tiempo". */
    private const DIAS_CAMA_OCUPADA_LARGA = 14;

    /** Umbral (días) para lotes "por vencer" — mismo default de LoteInventario::porVencer(). */
    private const DIAS_LOTE_POR_VENCER = 90;

    public static function canView(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    /**
     * Lotes vencidos o por vencer, con stock actual > 0 (un lote ya
     * agotado no representa ningún riesgo real, aunque su fecha ya haya
     * pasado). Ordenados por fecha de vencimiento ascendente (lo más
     * urgente primero), limitado a 5 para no desbordar el widget — el
     * conteo real (sin límite) se muestra aparte, y el enlace lleva al
     * listado completo.
     */
    protected function getLotesVencidosOPorVencer(): array
    {
        $lotes = LoteInventario::query()
            ->with('item')
            ->where('fecha_vencimiento', '<=', now()->addDays(self::DIAS_LOTE_POR_VENCER))
            ->orderBy('fecha_vencimiento')
            ->get()
            ->filter(fn (LoteInventario $lote): bool => $lote->stockActual() > 0)
            ->values();

        return [
            'total' => $lotes->count(),
            'items' => $lotes->take(5),
            'url' => LoteInventarioResource::getUrl('index'),
        ];
    }

    /**
     * Facturas en estado `pendiente` con más de 30 días desde su
     * `fecha` de emisión. Ordenadas por fecha ascendente (las más
     * antiguas primero, son las más urgentes de cobrar).
     */
    protected function getFacturasVencidas(): array
    {
        $limite = now()->subDays(self::DIAS_FACTURA_VENCIDA);

        $facturas = Factura::query()
            ->with('paciente')
            ->where('estado_pago', 'pendiente')
            ->where('fecha', '<=', $limite)
            ->orderBy('fecha')
            ->get();

        return [
            'total' => $facturas->count(),
            'monto_total' => (float) $facturas->sum('monto'),
            'items' => $facturas->take(5),
            'url' => FacturaResource::getUrl('index'),
        ];
    }

    /**
     * Camas con un internamiento activo (fecha_alta nula) cuya
     * fecha_ingreso ya supera el umbral de días definido. Ordenadas por
     * fecha_ingreso ascendente (la estadía más larga primero).
     */
    protected function getCamasOcupadasLargoTiempo(): array
    {
        $limite = now()->subDays(self::DIAS_CAMA_OCUPADA_LARGA);

        $camas = Cama::query()
            ->with(['internamientos' => function ($query) {
                $query->whereNull('fecha_alta')->with('paciente')->latest('fecha_ingreso')->limit(1);
            }])
            ->whereHas('internamientos', function ($query) use ($limite) {
                $query->whereNull('fecha_alta')->where('fecha_ingreso', '<=', $limite);
            })
            ->get()
            ->sortBy(fn (Cama $cama) => optional($cama->internamientoActivo())->fecha_ingreso)
            ->values();

        return [
            'total' => $camas->count(),
            'items' => $camas->take(5),
            'url' => CamaResource::getUrl('index'),
        ];
    }

    protected function getViewData(): array
    {
        return [
            'lotes' => $this->getLotesVencidosOPorVencer(),
            'facturas' => $this->getFacturasVencidas(),
            'camas' => $this->getCamasOcupadasLargoTiempo(),
            'diasFacturaVencida' => self::DIAS_FACTURA_VENCIDA,
            'diasCamaOcupadaLarga' => self::DIAS_CAMA_OCUPADA_LARGA,
            'diasLotePorVencer' => self::DIAS_LOTE_POR_VENCER,
        ];
    }
}
