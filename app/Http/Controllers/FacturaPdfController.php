<?php

namespace App\Http\Controllers;

use App\Filament\Resources\Facturas\FacturaResource;
use App\Models\Factura;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class FacturaPdfController extends Controller
{
    /**
     * Descarga el comprobante de una factura en PDF.
     *
     * Reutiliza FacturaResource::canViewAny() en vez de duplicar la regla de
     * permisos aquí: quien no puede ver Facturas en el panel (médico) tampoco
     * puede descargar su PDF por esta ruta.
     */
    public function download(Factura $factura): Response
    {
        abort_unless(FacturaResource::canViewAny(), 403);

        $factura->load(['paciente', 'cita.medico', 'cita.area', 'lineas']);

        return Pdf::loadView('pdf.factura', ['factura' => $factura])
            ->download("factura-{$factura->id}.pdf");
    }
}
