<?php

use App\Http\Controllers\EspecialidadController;
use App\Http\Controllers\FacturaPdfController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/especialidades/{slug}', [EspecialidadController::class, 'show'])
    ->name('especialidades.show');

// Descarga del comprobante de una factura en PDF. Vive fuera de /admin
// (que es el panel de Filament) porque genera un archivo binario para
// descargar, no una pantalla de Filament. Protegida con el guard 'web'
// (la misma sesión con la que se inicia sesión en /admin) y, dentro del
// controlador, con la misma regla de permisos que ya usa FacturaResource.
Route::middleware('auth')
    ->get('/facturas/{factura}/pdf', [FacturaPdfController::class, 'download'])
    ->name('facturas.pdf');

