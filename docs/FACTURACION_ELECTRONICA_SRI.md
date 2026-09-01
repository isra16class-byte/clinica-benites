# Facturación electrónica SRI — estado y continuación

Este documento es para que la **próxima sesión de Claude** retome exactamente
donde quedó esta. Leer esto antes de seguir con el tema. MEMORIA.md sección 8
tiene un resumen corto que apunta acá; este archivo es el detalle completo.

## Contexto de negocio (confirmado con el cliente, 01 sep 2026)

- El cliente confirmó que la clínica **sí necesita** facturación electrónica
  al SRI (Ecuador).
- **No tiene certificado digital de firma electrónica (.p12) todavía** — hay
  que tramitarlo con una entidad certificadora autorizada (Security Data,
  BCE, Uanataca, etc.), tiene costo y trámite propio.
- **No tiene RUC/código de establecimiento/punto de emisión asignados** —
  los asigna el SRI al registrar el punto de venta.
- Con esos dos bloqueos reales, se decidió: construir **Parte 1** (cambio
  estructural interno, sin depender de nada externo) completa y ya
  funcional, y dejar **Parte 2** (integración real con el paquete SRI)
  armada con datos de ejemplo, sin poder probarla — mismo criterio que se
  usó con Alergias/Antecedentes/Signos vitales (sin `vendor/` en el
  sandbox, código revisado a mano contra el patrón del proyecto).

## Investigación ya hecha (no repetir)

Se clonaron y revisaron a fondo, en el sandbox, los 3 paquetes reales de
`dazza-dev` (con `git clone --depth 1`, la red del sandbox permite
`github.com`):

- `github.com/dazza-dev/sri-xml-generator` — genera el XML. Tiene modelos
  PHP tipados (`Document`, `Company`, `Customer`, `Establishment`,
  `EmissionPoint`, `Document\LineItem`, `Tax\Tax`, `Payment\Payment`,
  `Totals\Totals`) y catálogos oficiales del SRI como JSON en `src/Data/`
  (`identification-types.json`, `payment-methods.json`, `tax-types.json`,
  `taxes/2.json` = tarifas de IVA). **Estos catálogos ya están reflejados
  en el código nuevo** (`Factura::FORMAS_PAGO`, `Paciente::TIPOS_IDENTIFICACION`,
  `LineaFactura::TARIFAS_IVA`) — los códigos coinciden exactamente.
- `github.com/dazza-dev/sri-ec` — firma (`Signer`) y envía (`Sender`) el
  XML al SRI. Tiene la clase `Client` que orquesta todo: `setCertificate()`,
  `setDocumentType()`, `setDocumentData()`, `sendDocument()`. La clave de
  acceso (49 dígitos, dígito verificador módulo 11) **se genera sola**
  dentro de `Document::generateAccessKey()` — no hay que calcularla a mano.
- `github.com/dazza-dev/laravel-sri-ec` — wrapper Laravel: facade
  `LaravelSriEc`, config publicable (`SRI_TEST`, `SRI_CERTIFICATE_PATH`,
  `SRI_CERTIFICATE_PASSWORD`, `SRI_PATH`), migraciones propias
  (`sri_certificates`, `sri_listings`, `sri_documents` — esta última con
  `documentable` polimórfico, pensada para colgarse de cualquier modelo,
  en este caso `Factura`).

Búsqueda web adicional (no asumido, verificado): los **servicios de salud y
medicinas están gravados con tarifa 0% de IVA** en Ecuador (LRTI art. 55-56),
no con la tarifa general del 15%. Por eso el default de `codigo_iva` en
`LineaFactura` es `'0'` (0%), no `'4'` (15%) — **no confirmado con un
contador**, queda marcado como pendiente de validar caso por caso (una
línea que no sea un servicio de salud, ej. un cobro administrativo, sí
podría llevar 15%).

### Estructura exacta que espera `$client->setDocumentData($documentData)`

Verificada leyendo `Document.php`/modelos reales (no inventada):

```php
[
    'sequential' => '000000001',       // 9 dígitos
    'date' => '2026-09-01',
    'establishment' => ['code' => '001', 'name' => '...', 'address' => '...'],
    'emission_point' => ['code' => '001', 'name' => '...'],
    'company' => [
        'ruc' => '...',
        'legal_name' => '...',
        'trade_name' => '...',
        'head_office_address' => '...',
        // opcionales: rimpe_regime_taxpayer, special_taxpayer_number,
        // withholding_agent, requires_accounting
    ],
    'customer' => [
        'identification_type' => '05',   // código SRI, ver Paciente::TIPOS_IDENTIFICACION
        'identification_number' => '...',
        'name' => '...',
        'address' => '...',              // opcional
    ],
    'line_items' => [
        [
            'code' => '...',                       // código interno del ítem
            'description' => '...',
            'unit' => 'UNI',                        // unidad de medida
            'quantity' => 1.0,
            'unit_price' => 45.0,
            'discount' => 0.0,
            'total_price_without_tax' => 45.0,      // = subtotal de la línea
            'taxes' => [
                ['code' => 2, 'percentage_code' => '0', 'rate' => 0, 'taxable_base' => 45.0, 'value' => 0.0],
                // code=2 es siempre IVA (tax-types.json); percentage_code
                // es el código de LineaFactura::TARIFAS_IVA ('0','4','6','7')
            ],
        ],
    ],
    'payments' => [
        ['payment_method' => '01', 'amount' => 45.0], // código de Factura::FORMAS_PAGO
    ],
    'totals' => [
        'subtotal' => 45.0,
        'total_discount' => 0.0,
        'taxes' => [ /* mismo formato que en line_items, agregado */ ],
        'total' => 45.0,
    ],
]
```

## Lo que YA está construido (Parte 1 — funcional, sin dependencias externas)

Todo esto está escrito y es consistente entre sí (verificado con `grep` que
no queden referencias a las columnas viejas `monto`/`metodo_pago` en ningún
archivo de la app). **No se pudo correr `composer install` ni
`sail artisan migrate` en este sandbox** (sin PHP/Composer disponibles) —
falta probarlo en el entorno real, igual que los módulos anteriores.

### Migraciones nuevas
- `2026_09_01_130000_add_tipo_identificacion_to_pacientes_table.php`
- `2026_09_01_130001_add_facturacion_electronica_a_facturas_table.php` —
  renombra `monto` → `total`, agrega `subtotal`, `iva`, reemplaza
  `metodo_pago` (texto libre) por `forma_pago` (código SRI), agrega
  `estado_sri`, `ambiente_sri`, `establecimiento`, `punto_emision`,
  `secuencial`, `clave_acceso`, `numero_autorizacion`, `mensaje_sri`,
  `fecha_autorizacion`.
- `2026_09_01_130002_create_lineas_factura_table.php` — detalle de la
  factura (descripción, cantidad, precio unitario, descuento, código de
  IVA, subtotal calculado).

### Modelos
- `app/Models/LineaFactura.php` (nuevo) — calcula su propio `subtotal` al
  guardar, y dispara `Factura::recalcularTotales()` al guardar/borrar
  (`booted()`). Tiene `TARIFAS_IVA` (catálogo SRI).
- `app/Models/Factura.php` — `lineas()` (HasMany), `FORMAS_PAGO`,
  `ESTADOS_SRI`, `numeroComprobante()`, `recalcularTotales()`.
- `app/Models/Paciente.php` — `tipo_identificacion` en fillable,
  `TIPOS_IDENTIFICACION` (catálogo SRI, con mapeo a los códigos del
  paquete).

### Filament
- `FacturaForm` — sin campo de monto (se calcula solo); `forma_pago` como
  `Select` con el catálogo SRI en vez de `TextInput` libre.
- `FacturasTable` — columna `total` (antes `monto`), `forma_pago` con
  label del catálogo, `numeroComprobante()`, columna/filtro `estado_sri`.
- `FacturaResource` — registra `LineasFacturaRelationManager` en
  `getRelations()`; `getGlobalSearchResultDetails()` usa `total`.
- `LineasFacturaRelationManager.php` (nuevo) — tab "Líneas" dentro de
  Editar Factura, mismo patrón que `AlergiasRelationManager`.
- `PacienteForm`/`PacientesTable` — campo/columna `tipo_identificacion`.

### PDF y datos
- `resources/views/pdf/factura.blade.php` — reescrito: tabla de detalle
  (líneas), subtotal/IVA/total, número de comprobante en formato SRI
  (`001-001-000000001`) si ya tiene secuencial, y un **aviso visible**
  ("Comprobante interno — no es una factura electrónica autorizada por el
  SRI todavía") mientras `estado_sri !== 'autorizada'` — importante no
  quitar este aviso hasta que la Parte 2 esté realmente probada y
  funcionando, para no emitir algo que parezca válido sin serlo.
- `FacturaPdfController.php` — carga la relación `lineas`.
- `DemoHistoricoSeeder.php` — `crearFacturas()` ahora crea 1-3 líneas por
  factura (conceptos genéricos: consulta general, especializada, examen,
  procedimiento) con `forma_pago` de catálogo; todas quedan en
  `estado_sri = 'no_emitida'` (ninguna factura demo se emite de verdad).
  `crearPacientes()` asigna `tipo_identificacion` (1 de cada 10 con RUC).
- 4 widgets del dashboard (`AlertasOperativasWidget`,
  `IndicadoresGerencialesWidget`, `IngresosPorMesChartWidget`,
  `FacturacionPorAreaChartWidget`) actualizados de `sum('monto')` a
  `sum('total')` — se habrían roto en el entorno real si no se corregían
  junto con el rename de columna.

## Lo que FALTA (Parte 2 — integración real, no empezada)

No se escribió ni una línea de esto todavía. Es lo que sigue:

1. **`config/clinica.php`** (nuevo) — datos tributarios de la empresa (RUC,
   razón social, nombre comercial, dirección matriz, código de
   establecimiento/punto de emisión por defecto), todos vía `env()`, con
   placeholders explícitos hasta que el cliente confirme los datos reales
   (sección 6 de MEMORIA.md, pregunta pendiente).
2. **`.env.example`** — agregar bloque con las variables de arriba +
   `SRI_TEST`, `SRI_CERTIFICATE_PATH`, `SRI_CERTIFICATE_PASSWORD`,
   `SRI_PATH` (las que pide `laravel-sri-ec/config/laravel-sri-ec.php`).
3. **`composer.json`** — agregar `"dazza-dev/laravel-sri-ec": "^1.0"` a
   `require`. Sigue siendo código muerto hasta que el usuario corra
   `composer install` en su entorno real.
4. **`app/Services/Sri/FacturaSriMapper.php`** (nuevo) — clase que arma el
   array `$documentData` (estructura verificada arriba) a partir de un
   `Factura` con sus `lineas` cargadas + los datos de `config/clinica.php`
   + el `Paciente` relacionado. Punto de mayor riesgo de bugs de mapeo:
   revisar con cuidado que cada `codigo_iva` de `LineaFactura` se traduzca
   bien a `taxes[].percentage_code`, y que `unit` tenga un valor por
   defecto razonable (el paquete lo exige, el proyecto no tiene ese
   concepto todavía — usar `'UNI'` fijo es razonable para servicios).
5. **`app/Services/Sri/FacturaSriService.php`** (nuevo) — llama a
   `LaravelSriEc::getClient()->setDocumentType('invoice')->
   setDocumentData($mapper->toArray($factura))->sendDocument()`, envuelto
   en `try/catch` sobre `DazzaDev\SriEc\Exceptions\DocumentException`, y
   guarda el resultado en la factura (`estado_sri`, `clave_acceso`,
   `numero_autorizacion`, `mensaje_sri`, `fecha_autorizacion`,
   `secuencial` asignado en este momento, no antes — ver comentario en la
   migración de `facturas`). Debe chequear
   `class_exists(\DazzaDev\LaravelSriEc\Facades\LaravelSriEc::class)`
   primero y devolver un error claro si el paquete todavía no está
   instalado (composer install pendiente).
6. **Action "Emitir al SRI"** en `FacturasTable`/`EditFactura` — visible
   solo para admin, llama al servicio de arriba, muestra notificación de
   éxito/error. Debe avisar explícitamente si falta certificado/RUC en
   config (no intentar silenciosamente).
7. **Publicar migraciones del paquete** (`sri_certificates`,
   `sri_listings`, `sri_documents`) — documentar en este archivo el
   comando (`php artisan vendor:publish --tag="laravel-sri-ec-migrations"`)
   para que el usuario lo corra en su entorno.
8. Actualizar MEMORIA.md sección 10 (permisos) y sección 11 (roadmap) una
   vez que la Parte 2 esté escrita: agregar los pasos pendientes del lado
   del cliente (tramitar certificado .p12, tramitar RUC/establecimiento/
   punto de emisión, cargar esos datos en `.env`, correr
   `composer require dazza-dev/laravel-sri-ec` + publicar migraciones +
   migrar, y recién ahí probar "Emitir al SRI" en ambiente de pruebas del
   SRI antes de producción).

## Cómo seguir

1. Leer este archivo completo.
2. Empezar por el punto 1 de "Lo que FALTA" (`config/clinica.php`) y seguir
   el orden — cada punto depende un poco del anterior.
3. Mismo flujo de trabajo de siempre: sin PHP/Composer en el sandbox,
   escribir el código a mano verificando contra el código fuente real de
   los paquetes (ya clonados una vez en esta sesión — si hace falta
   reconsultarlos, volver a clonar con `git clone --depth 1
   https://github.com/dazza-dev/<paquete>.git`, la red del sandbox lo
   permite).
4. Al terminar, actualizar este mismo archivo (o borrarlo y dejar el
   resumen final directo en MEMORIA.md/CHANGELOG.md, si para ese momento
   ya no hace falta un documento de continuación aparte).
