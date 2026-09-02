# Resumen de sesión — 02 sep 2026: fix de Facturación SRI + decisión de hosting

Este archivo es un resumen de continuidad para la próxima sesión de Claude. No reemplaza `MEMORIA.md` (que ya tiene las entradas relevantes incorporadas) — es un repaso rápido de todo lo que se discutió y decidió hoy, sobre todo la parte de hosting que **todavía no está en `MEMORIA.md`**.

## 1. Bug de Facturación SRI — resuelto y confirmado

- **Bug**: `facturas.total` (renombrada de `monto`) se quedó `NOT NULL` sin `default(0)` al agregarse la facturación SRI — a diferencia de `subtotal`/`iva`, que sí lo tienen. Como el diseño crea la Factura antes de tener líneas, el insert inicial fallaba (`SQLSTATE 1364`), tanto en el seeder como en el formulario real de Filament.
- **Fix**: migración `2026_09_02_000000_add_default_to_total_column_facturas_table.php`, ya pusheada a `main`.
- **Confirmado funcionando en el entorno real** (02 sep 2026): seeder completo sin errores, listado `/admin/facturas` con totales correctos, botón "Emitir al SRI" bloqueando correctamente por falta de RUC/certificado — comportamiento esperado, no un bug.
- Ya documentado en `MEMORIA.md` (sección 8) y `CHANGELOG.md`.

## 2. Gotcha de sincronización entre patches y `composer.lock`

Durante esta sesión hubo fricción porque `composer.lock` se actualizó **localmente** en la computadora del usuario (vía `composer update dazza-dev/laravel-sri-ec --with-all-dependencies`) pero nunca se commiteó/pusheó — solo `composer.json` llegaba vía patches de Claude. Esto causó que un patch de Claude fallara al aplicar (`git am`) por desincronización de `CHANGELOG.md`, y que la segunda computadora no fuera a poder instalar sin repetir el `composer update` manual.

**Lección para la próxima sesión**: siempre verificar que `composer.lock` esté commiteado y pusheado después de cualquier cambio de dependencias — no asumir que viene incluido en los patches de Claude si el `composer update` se corrió del lado del usuario.

## 3. Segunda computadora (proyecto detenido en el módulo de Antecedentes)

Pasos para ponerla al día una vez que `composer.lock` ya esté sincronizado en GitHub (ya lo está, confirmado 02 sep 2026):

```bash
git pull
./vendor/bin/sail up -d
./vendor/bin/sail composer install
./vendor/bin/sail artisan migrate
```

Opcional, si se quieren datos de demo ahí también: `AreaSeeder` → crear admin con `make:filament-user` + asignar `rol=admin` vía `tinker --execute` → `DemoHistoricoSeeder`.

**Truco útil descubierto**: usar `artisan tinker --execute="..."` en vez de la consola interactiva de `tinker` evita problemas de timing al pegar comandos multilínea en la terminal (el REPL interactivo puede perder líneas si el paste es muy rápido).

## 4. GitHub Student Developer Pack — qué sirve para este proyecto

- **DigitalOcean**: $200 de crédito, 1 año. Cubre hosting real gratis por 11-33 meses según el plan.
- **Namecheap**: dominio `.me` gratis con SSL — útil para staging/demo, pero para la clínica real probablemente conviene comprar el `.com` igual (más profesional).
- **JetBrains** (PhpStorm): gratis, opcional si se quiere mejor IDE.
- **Figma Professional**: gratis, dura ~1 año (renovable mientras siga el status de estudiante). *Ojo*: es para uso académico, no para trabajo comercial pagado — revisar si el proyecto pasa a ser un contrato formal con el dueño.

## 5. Comparación de hosting — decisión pendiente, no cerrada

### DigitalOcean vs. Hetzner (verificado en pantalla por el usuario)
- La línea barata de Hetzner ("Cost-Optimized", ~€4,59-5,99) **no está disponible** actualmente para la cuenta del usuario.
- La línea que sí está disponible ("Regular Performance" / CPX) es **más cara que DigitalOcean en specs equivalentes**:
  - 2GB/1vCPU: Hetzner CPX12 $17,05/mes vs. DigitalOcean $6/mes
  - 2GB/2vCPU: Hetzner CPX11 $25,52/mes vs. DigitalOcean $18/mes
  - 4GB/2vCPU: Hetzner CPX22 $28,54/mes vs. DigitalOcean $24/mes
- **Conclusión de esta sesión**: con la disponibilidad real, Hetzner no conviene frente a DigitalOcean (que además tiene el crédito gratis de estudiante).

### Oracle Cloud "Always Free" — en proceso de prueba
- Gratis **para siempre** (no es un crédito que se agota): 2 OCPU ARM (Ampere A1) + 12 GB RAM, repartibles en hasta 4 VMs. Esto es mucho más de lo que el proyecto necesita (se estimó 500-700MB de uso real con PHP-FPM + MySQL sin Redis/Meilisearch, que no se usan en producción).
- **Riesgos conocidos**: errores de "sin capacidad" al crear la instancia Ampere (común, puede requerir reintentos); arquitectura ARM64 (no debería haber problema, el proyecto no tiene extensiones PHP raras); firewall doble específico de Oracle (Security List de red + firewall del SO, hay que abrir puertos en ambos).
- **Estado actual**: cuenta ya creada y aceptada (tenancy `isra16`, región **Colombia Central (Bogotá)** — elegida por ser la más cercana a Ecuador entre las opciones de Latinoamérica). La cuenta tiene un "Free Trial" de 30 días con crédito extra temporal, que es **adicional** a los recursos Always Free (no es un período de espera — los recursos Always Free ya están disponibles desde ahora).
- **Próximo paso pendiente**: crear la VM instance eligiendo la forma **Ampere / `VM.Standard.A1.Flex`** (no la que viene por default, que suele ser paga) — la próxima sesión debe guiar ese paso con cuidado, incluyendo el firewall doble.

### Físico (descartado por ahora)
Se evaluó comprar hardware físico para tener en la clínica — descartado por: dependencia del internet/electricidad local, riesgo de seguridad física con datos sensibles de pacientes (relevante para cumplimiento LOPDP, ver sección 8 de `MEMORIA.md`), sin backups automáticos. El costo inicial es menor, pero no hay ahorro real mientras el crédito/Always Free de la nube siga vigente.

## 6. Pendiente de arquitectura para el deploy (aplica a cualquier proveedor elegido)

El proyecto **todavía no tiene un `Dockerfile`/compose de producción** — solo existe `compose.yaml` de Sail, pensado para desarrollo local (incluye Mailpit, Selenium, Meilisearch, que no se usan en producción real; sesiones/cola/caché ya están configuradas con driver `database`, no requieren Redis). Armar ese Dockerfile de producción es un paso pendiente independiente de cuál proveedor de hosting se termine eligiendo.

## 7. Roadmap general sin cambios (ver `MEMORIA.md` sección 11)

Sigue pendiente: confirmar visualmente el sitio público, reemplazar 6 placeholders de teléfono/WhatsApp, entrevista formal con el dueño, confirmar matriz de permisos del expediente clínico, y del lado del cliente — certificado .p12 + RUC/establecimiento/punto de emisión para poder probar la Parte 2 de facturación SRI.
