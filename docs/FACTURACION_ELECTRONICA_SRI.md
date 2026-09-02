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

## Lo que YA está construido (Parte 2 — integración real, escrita el 01 sep 2026, SIN PROBAR)

Escrito en la misma sesión que retomó este documento. Sigue sin poder
probarse (sin PHP/Composer en el sandbox) — **antes de usar esto en el
entorno real, correr `composer install` y probar contra el ambiente de
pruebas del SRI, no producción, la primera vez.**

- **`config/clinica.php`** (nuevo) — datos tributarios de la empresa (RUC,
  razón social, nombre comercial, dirección matriz, establecimiento/punto
  de emisión por defecto, certificado .p12), todos vía `env()`. Ningún
  valor real cargado — placeholders hasta que el cliente confirme.
- **`.env.example`** — bloque nuevo con `CLINICA_RUC`,
  `CLINICA_RAZON_SOCIAL`, `CLINICA_NOMBRE_COMERCIAL`,
  `CLINICA_DIRECCION_MATRIZ`, `CLINICA_CONTRIBUYENTE_ESPECIAL`,
  `CLINICA_OBLIGADO_CONTABILIDAD`, `CLINICA_AGENTE_RETENCION`,
  `CLINICA_ESTABLECIMIENTO_CODE/NAME`, `CLINICA_PUNTO_EMISION_CODE/NAME`,
  `SRI_AMBIENTE` (default `pruebas`), `SRI_CERTIFICATE_PATH`,
  `SRI_CERTIFICATE_PASSWORD`.
- **`composer.json`** — agregado `"dazza-dev/laravel-sri-ec": "^1.0"` a
  `require`. Sigue siendo código muerto hasta correr `composer install`;
  `composer.lock` no se regeneró (no hay Composer en el sandbox) — correr
  `composer update dazza-dev/laravel-sri-ec` la primera vez en el entorno
  real.
- **`app/Services/Sri/FacturaSriMapper.php`** (nuevo) — arma el array
  `$documentData` exacto (estructura verificada arriba) a partir de una
  `Factura` con `lineas`/`paciente` cargados. Lanza `InvalidArgumentException`
  explícita si falta algo (paciente, líneas, forma de pago, RUC/dirección
  de la empresa) en vez de mandar un XML incompleto. Agrupa las líneas por
  `codigo_iva` para armar `totals.taxes` (el SRI exige un renglón de
  impuesto por cada tarifa distinta usada, no un total único).
- **`app/Services/Sri/FacturaSriService.php`** (nuevo) —
  `motivosBloqueoEmision(Factura)` chequea todo lo verificable sin red
  (paquete instalado, RUC/certificado configurados, factura con
  líneas/forma de pago, no ya autorizada) y devuelve la lista de motivos
  en español; `puedeEmitir()` es el booleano corto. `emitir(Factura)`
  asigna el `secuencial` recién en este momento (no al crear la factura),
  llama a `LaravelSriEc::getClient()->setCertificate()->setDocumentType('invoice')
  ->setDocumentData()->sendDocument()`, y guarda `estado_sri`,
  `clave_acceso`, `numero_autorizacion`, `fecha_autorizacion`,
  `mensaje_sri` según el resultado. **Nota de concurrencia**: el cálculo
  del siguiente secuencial (`siguienteSecuencial()`) no usa
  `lockForUpdate()` — si en algún momento hay más de una caja emitiendo al
  mismo tiempo, hace falta agregarlo para no repetir secuencial (no se
  agregó para no complicar código que no se pudo probar).
- **Action "Emitir al SRI"** en `FacturasTable` — visible solo para admin,
  llama a `motivosBloqueoEmision()` primero y muestra una notificación de
  advertencia con la lista en español si no se puede emitir todavía (sin
  intentar la llamada real), o el resultado de éxito/error de `emitir()`.

Investigación adicional hecha para escribir esto (no repetir): se clonó
también `github.com/dazza-dev/sri-sender` para confirmar la forma exacta
de la respuesta de `Client::sendDocument()` — devuelve
`$send['authorization']` con `status`, `authorized_document` (`access_key`
= número de autorización del SRI — el nombre del campo es confuso, no es
la clave de acceso de 49 dígitos —, `xml`, `date`), `messages`,
`attempts`. Confirmado en `AuthorizationClient::getAuthorizationNumber()`
que navega `RespuestaAutorizacionComprobante->autorizaciones->autorizacion->numeroAutorizacion`.
Si el SRI rechaza, `Client::sendDocument()` lanza
`DazzaDev\SriEc\Exceptions\DocumentException` con el mensaje de error —
el servicio lo captura como `Throwable` genérico y guarda
`estado_sri = 'error'` + el mensaje en `mensaje_sri`.

## Lo que FALTA (para poder usar esto de verdad)

Todo esto depende de datos/trámites que **no son código** — nada más para
escribir hasta que el cliente los tenga:

1. Tramitar el certificado digital .p12 (entidad certificadora autorizada
   en Ecuador — Security Data, BCE, Uanataca, etc.).
2. Tramitar/confirmar RUC, código de establecimiento y punto de emisión
   ante el SRI.
3. Cargar esos datos reales en `.env` (nunca commitear el `.env` con
   datos reales ni el archivo `.p12` al repo).
4. Correr `composer require dazza-dev/laravel-sri-ec` (o `composer
   update` si ya está en `composer.json`, como ahora) en el entorno real.
5. Publicar las migraciones propias del paquete
   (`php artisan vendor:publish --tag="laravel-sri-ec-migrations"` —
   crean `sri_certificates`, `sri_listings`, `sri_documents`) y correr
   `php artisan migrate`.
6. Probar `FacturaSriService::emitir()` con `SRI_AMBIENTE=pruebas` contra
   el ambiente de certificación del SRI, con una factura real de prueba,
   **antes** de cambiar a `produccion` — un comprobante autorizado en
   producción es fiscalmente real y no se puede simplemente borrar si
   algo salió mal en el mapeo.
7. Confirmar con un contador si el default de `codigo_iva = '0'` (0%) en
   `LineaFactura` es correcto para todos los servicios que factura la
   clínica, o si alguno debería ir a 15% (ver nota en la migración de
   `lineas_factura` — verificado por búsqueda web que los servicios de
   salud están en tarifa 0% por LRTI art. 55-56, pero no confirmado caso
   por caso con un profesional).
8. Una vez validado el flujo completo en producción, actualizar
   MEMORIA.md sección 6 (marcar la pregunta como resuelta) y sección 11
   (roadmap), y considerar borrar o resumir este archivo si ya no hace
   falta como documento de continuación aparte.

## Cómo seguir si hace falta retomar el código (no los trámites)

1. Leer este archivo completo, en particular la sección de arriba con lo
   ya construido — no reescribir el mapper/servicio desde cero.
2. Si hace falta reconsultar el código fuente de los paquetes, volver a
   clonarlos (la red del sandbox permite `github.com`):
   `git clone --depth 1 https://github.com/dazza-dev/<paquete>.git`
   (`sri-xml-generator`, `sri-ec`, `sri-sender`, `laravel-sri-ec`).
3. Revisar con cuidado, apenas se pueda correr Composer en un entorno
   real, que el mapeo de `FacturaSriMapper` compile y que
   `LaravelSriEc::getClient()` exista con esa firma exacta — esto nunca
   se ejecutó, solo se verificó leyendo el código fuente.
