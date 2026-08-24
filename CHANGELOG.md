# 📜 CHANGELOG — Clínica Benites

Registro cronológico de cambios del proyecto. Formato: más nuevo arriba, nunca se borran entradas viejas.

Ver `MEMORIA.md` para el estado actual y contexto técnico completo — este archivo es solo la bitácora de "qué cambió cuándo".

---

## [2026-08-23] Simplificar el sidebar: se descarta el fondo oscuro, tinte celeste claro sin forzar el texto

- El intento de sidebar oscuro (entrada de abajo) siguió sin verse bien en la prueba real incluso después del primer fix (texto invisible en el ítem activo, ver entrada de abajo): el usuario probó de nuevo en `/admin/areas` y seguía mal. Se investigó el código fuente real de Filament v5.7.6 (`item.blade.php`, descargado directo desde GitHub para la versión exacta instalada según `composer.lock`) y se confirmó que el `<li>` sí lleva las clases `fi-sidebar-item fi-active` como se esperaba — así que el CSS en teoría debía funcionar. Sin acceso a inspeccionar el DOM en vivo (herramientas de desarrollador del navegador del usuario) no se pudo determinar con certeza si el problema real era cache del navegador/Livewire (`wire:navigate` no siempre recarga el `<head>`) o algo de especificidad CSS.
- Ante la complicación, el usuario pidió simplificar: descartar el sidebar oscuro por completo y volver a un tinte celeste claro de fondo, sin forzar el color del texto en ningún estado (normal/hover/activo) — se deja el gris que Filament trae por defecto, que sobre un fondo claro ya contrasta bien.
- En `AdminPanelProvider.php`: se eliminaron todas las reglas de `.fi-sidebar-item-label`, `.fi-icon`, `.fi-sidebar-group-label`, `:hover` y `.fi-active` (siete reglas en total). Queda solo `.fi-sidebar { background-color: color-mix(in srgb, var(--primary-500) 8%, white); }` — mismo mecanismo de tinte suave que ya se usaba antes de probar el sidebar oscuro. El resto del `<style>` (línea de acento en topbar/tabla, color de botones de acción) no se tocó, esas clases (`.fi-topbar`, `.fi-ta-table`, `.fi-ta-actions`) son de nivel más alto y ya estaban confirmadas.
- Menor superficie de riesgo: de siete reglas dependientes de clases internas de Filament, queda solo una que apunta a `.fi-sidebar` (clase raíz del componente, ya confirmada desde la primera vuelta de branding).
- Sintaxis PHP no se pudo validar con `php -l` en este entorno (sin PHP instalado), pero el cambio es solo texto dentro del heredoc CSS — no se tocó estructura PHP.
- Entregado como patch (`git am`). **Pendiente confirmar en el entorno real.**

## [2026-08-23] Fix: texto invisible en el ítem activo del menú lateral

- El usuario probó el rediseño del sidebar oscuro (ver entrada de abajo) en el entorno real y mandó una captura de `/admin/facturas`: el ítem "Facturas" (activo, seleccionado) se veía con fondo blanco/claro y texto blanco encima — prácticamente ilegible.
- **Causa**: el fondo y el color de texto del ítem activo se aplicaban con selectores CSS distintos — el del fondo (`.fi-sidebar-item.fi-active .fi-sidebar-item-button`) dependía de una clase interna del botón que no calzó contra la versión real de Filament instalada; el del texto (`.fi-sidebar-item.fi-active .fi-sidebar-item-label`) sí calzó y forzó el texto a blanco. Resultado: texto blanco forzado sobre un fondo que nunca cambió de blanco.
- **Fix**: en `AdminPanelProvider.php`, el fondo y el color activo ahora se aplican juntos al `<li class="fi-sidebar-item fi-active">` (clase confirmada) y a todos sus descendientes (selector universal `*`), en vez de nombrar clases internas del botón/label que pueden variar entre versiones — así nunca vuelve a pasar que uno de los dos cambie sin el otro. Mismo criterio aplicado al estado `:hover`.
- Sintaxis PHP validada con `php -l`. Entregado como patch (`git am`). **Pendiente confirmar en el entorno real que el ítem activo ya se ve bien.**

## [2026-08-23] Rediseño del color extra con criterio (60/30/10, sidebar oscuro)

- El usuario pidió una segunda vuelta más pensada sobre el ajuste de color anterior ("¿cómo lo arreglarías vos? buscá en internet la mejor combinación"). Se investigaron guías de diseño (UAB Medicine, CMS.gov, regla 60/30/10) antes de tocar el código de nuevo.
- Se identificó un problema en la versión anterior: pintar todo el encabezado de la tabla de turquesa sólido competía visualmente con los badges de estado (`pendiente`/`confirmada`/`cancelada`), que ya usan color con significado propio (gray/info/danger). Se revirtió esa parte: el encabezado de tabla vuelve a blanco/gris neutro, con una línea de acento fina debajo.
- Se rediseñó el sidebar con un fondo turquesa oscuro sólido (`var(--primary-900)`) en vez de un tinte casi imperceptible — patrón común en paneles SaaS (Linear, Vercel). Texto/íconos del menú en turquesa clarito, blanco en hover y en el ítem activo. La cabecera con el logo se mantiene sin cambios (blanca).
- Se mantiene el resto sin cambios: línea de acento en la barra superior, botones de acción de tabla en color primario.
- **Nota de riesgo**: las clases nuevas de esta vuelta (`.fi-sidebar-item-label`, `.fi-icon`, `.fi-sidebar-group-label`) todavía no están confirmadas contra Filament v5.7 real — si el texto del menú queda invisible sobre el fondo oscuro, hay que inspeccionar el HTML y ajustar el selector.
- Sintaxis PHP validada con `php -l`. Falta probar en el entorno real.
- Entregado como patch (`git am`).

## [2026-08-23] Más color todavía: encabezados de tabla y botones de acción

- A pedido del usuario, sobre el ajuste anterior de color (sidebar/topbar/ítem activo): se sumó al mismo `<style>` en `AdminPanelProvider` un tinte turquesa en el encabezado de las tablas (`.fi-ta-header-cell`, con línea de acento debajo) y color turquesa forzado en los botones de acción de fila (ej. "Editar", vía `.fi-ta-actions`), que antes quedaban en gris y se perdían un poco.
- Mismo mecanismo que el ajuste anterior (CSS inline vía `renderHook`, sin build nuevo) y mismo riesgo advertido: clases sin confirmar contra Filament v5.7 corriendo de verdad.
- Sintaxis PHP validada con `php -l`. Falta probar en el entorno real.
- Entregado como patch (`git am`).

## [2026-08-23] Más color en el panel: sidebar, topbar e ítem de menú activo

- A pedido del usuario tras confirmar el branding base ("falta más color", ver captura de `/admin/areas`): se agregó un `<style>` inline vía `->renderHook(PanelsRenderHook::HEAD_END, ...)` en `AdminPanelProvider`. Tiñe levemente el fondo del sidebar con el color primario, agrega una línea de acento turquesa bajo la barra superior, y le da más presencia de color al ítem de menú activo (antes quedaba en gris clarito).
- Se cambió también el color neutro del panel de `Color::Gray` (default) a `Color::Slate`, con un tinte azulado que combina mejor con el turquesa primario.
- Se usó CSS inline con `->renderHook()` en vez de compilar un tema custom de Filament (que requiere `composer create filament:theme` + build con Vite/Tailwind) — mucho más liviano para un ajuste puntual de color, sin agregar un paso de build nuevo al proyecto.
- **Nota de riesgo dejada explícita en `MEMORIA.md`**: las clases CSS usadas (`.fi-topbar`, `.fi-sidebar`, `.fi-sidebar-item.fi-active .fi-sidebar-item-button`) no se pudieron confirmar contra Filament v5.7 corriendo de verdad (no hay forma de levantar el panel real desde este entorno) — si no se nota el cambio, hay que inspeccionar el HTML del panel en el navegador y ajustar los selectores.
- Sintaxis PHP validada con `php -l`. Falta probar en el entorno real.
- Entregado como patch (`git am`).

## [2026-08-23] Confirmación en entorno real: branding del panel

- Confirmado por el usuario con una captura de `/admin/areas`: el logo, el nombre "Clínica Benites" y el color primario turquesa se ven correctamente aplicados en el panel.
- Feedback del usuario: "falta más color" — el resto del panel queda muy neutro (blanco/gris) por el comportamiento por defecto de Filament, que solo usa el color primario en botones y acentos puntuales. Ver entrada siguiente para el ajuste hecho a partir de este feedback.

## [2026-08-23] Branding del panel: nombre, colores, logo y favicon

- `AdminPanelProvider`: `->brandName('Clínica Benites')` (antes mostraba "Laravel" por defecto), `->brandLogo(asset('images/logo.svg'))`, `->brandLogoHeight('2.5rem')`, `->favicon(asset('images/icon.svg'))` y color primario cambiado de `Color::Amber` (default de Filament) a `Color::Cyan` — turquesa, a pedido del usuario y acorde a los colores reales de la fachada/cartel de la clínica (fotos de Google Street View compartidas por el usuario).
- Nuevos assets en `public/images/`: `icon.svg` (monograma "CB" en cuadrado redondeado turquesa, usado como favicon) y `logo.svg` (versión horizontal ícono + "Clínica Benites", usada como logo del header del panel). Son un diseño original simple (no una reproducción exacta del cartel físico, del que no se tiene el archivo vectorial) pensado para verse bien a tamaño chico en el panel.
- `public/favicon.ico` regenerado con Pillow (antes era un archivo vacío de 0 bytes, sin favicon real) a partir del mismo diseño del monograma "CB", en los tamaños estándar 16/32/48/64px — este es el favicon que se sirve para el sitio en general (fuera del panel de Filament, que usa `icon.svg` vía `->favicon()`).
- `.env.example`: `APP_NAME=Laravel` → `APP_NAME="Clínica Benites"`. **Importante**: el `.env` real (no versionado) de cada entorno hay que actualizarlo a mano con el mismo valor — este archivo solo cambia la plantilla, no el `.env` que ya existe localmente. Afecta el título de la pestaña del navegador y el remitente de los correos (`MAIL_FROM_NAME`), entre otros usos de `config('app.name')` en Laravel.
- **No se tocó** la página pública (`resources/views/welcome.blade.php`) más allá del efecto indirecto del cambio de `APP_NAME` en el `<title>` — sigue siendo la página de bienvenida por defecto de Laravel, la construcción del sitio público sigue pendiente (ver `MEMORIA.md`, sección 8).
- Sintaxis PHP validada con `php -l`. Falta probar visualmente en el entorno real (colores, tamaño del logo en el header/login, favicon en la pestaña del navegador).
- Entregado como patch (`git am`).

## [2026-08-23] Confirmación en entorno real: filtro de rol junto a la barra de búsqueda

- Confirmado por el usuario: el botón de filtro de rol aparece correctamente junto a la barra de búsqueda en `/admin/users`.
- `MEMORIA.md` actualizado quitando la nota de "falta probar".

## [2026-08-23] Ajuste de UX: filtro de rol junto a la barra de búsqueda

- A pedido del usuario tras confirmar el filtro anterior: se quitó `layout: FiltersLayout::AboveContentCollapsible` del `SelectFilter` de `UsersTable`. Con el layout `Dropdown` por defecto de Filament, el botón de filtro ahora aparece junto a la barra de búsqueda (como en la mayoría de tablas de Filament), en vez de en su propia fila arriba de la tabla.
- Se eligió no usar `AboveContentCollapsible` aquí (a diferencia de Citas) porque con un solo filtro no se justifica el espacio extra que ocupa esa fila.
- Sintaxis validada con `php -l`.
- Entregado como patch (`git am`).

## [2026-08-23] Confirmación en entorno real: filtro rápido por rol en /admin/users

- Confirmado por el usuario: el `SelectFilter` "Rol" en `/admin/users` funciona correctamente.
- `MEMORIA.md` actualizado quitando la nota de "falta probar".

## [2026-08-23] Filtro rápido por rol en /admin/users

- `UsersTable`: nuevo `SelectFilter` "Rol" (admin/recepción/médico), colocado arriba de la tabla vía `FiltersLayout::AboveContentCollapsible` (mismo patrón visual que los filtros rápidos de Citas).
- Se usó `SelectFilter` (una sola opción a la vez) en vez de 3 `Filter->toggle()` como en Citas, porque el rol de un usuario es mutuamente excluyente — no tiene sentido combinar "admin" y "médico" a la vez, a diferencia de "Hoy"/"Pendientes" en Citas.
- Sintaxis validada con `php -l`. Falta probar en el entorno real.
- Entregado como patch (`git am`).

## [2026-08-23] Confirmación en entorno real: fix de `medico_id` + filtro "mis pacientes"

- Confirmado por el usuario: el fix de `medico_id` (se limpia correctamente al cambiar el rol de un usuario, tanto en el `Select` en vivo como al guardar) funciona como se esperaba.
- Con esto, el filtro "mis pacientes" para el rol médico (Citas, Historias Clínicas, Dashboard, autoselect al crear) queda confirmado de punta a punta en el entorno real.
- `MEMORIA.md` actualizado quitando las notas de "pendiente probar".

## [2026-08-23] Fix: `medico_id` no se limpiaba al cambiar el rol de un usuario

- **Bug encontrado por el usuario al probar el filtro "mis pacientes"**: al editar un usuario `medico` (con médico vinculado) y cambiarle el rol a `recepcion`/`admin`, el campo "Médico vinculado" se ocultaba (`->visible()`) pero su valor seguía guardándose — Filament no descarta el valor de un campo solo por ocultarlo. El usuario quedaba con `rol` correcto pero `medico_id` apuntando a un médico "fantasma".
- Fix en dos capas:
  - `UserForm`: `->afterStateUpdated()` en el `Select` de `rol`, resetea `medico_id` a `null` en el estado del formulario en cuanto el rol deja de ser `medico`.
  - `CreateUser::mutateFormDataBeforeCreate()` y `EditUser::mutateFormDataBeforeSave()`: cinturón de seguridad adicional, fuerza `medico_id = null` justo antes de guardar si el rol no es `medico`.
- Sintaxis validada con `php -l`.
- **Aclaración sobre un segundo reporte del usuario (no era un bug)**: un usuario con rol médico no veía el botón "Crear" en `/admin/citas` pero sí en `/admin/historia-clinicas`. Es la matriz de permisos ya documentada desde antes (sección 10 de `MEMORIA.md`): médico nunca tuvo permiso de crear Citas (solo ver/editar), pero sí tiene permiso completo en Historia Clínica. No se tocó código por esto.
- Entregado como patch (`git am`).

## [2026-08-23] Filtrar "mis pacientes" para el rol médico

- Nueva migración `2026_08_23_220000_add_medico_id_to_users_table.php`: agrega `medico_id` (nullable, FK a `medicos`, `nullOnDelete()`) a la tabla `users`, conectando por fin `users` con `medicos` (hasta ahora eran tablas independientes).
- `User::medico()` (relación `belongsTo`) y `medico_id` agregado al atributo `#[Fillable]` del modelo.
- `UserForm` (`/admin/users`): nuevo campo `Select` "Médico vinculado", visible solo cuando el rol seleccionado es `medico` (el `Select` de `rol` ahora usa `->live()` para poder reaccionar a ese cambio sin recargar la página).
- `UsersTable`: nueva columna "Médico vinculado" (toggleable), muestra "—" cuando no aplica.
- `CitaResource::getEloquentQuery()` y `HistoriaClinicaResource::getEloquentQuery()`: si el usuario logueado tiene rol `medico` y `medico_id` asignado, la consulta base se filtra por `medico_id` — afecta tabla, edición y búsqueda global (que reutiliza `getEloquentQuery()` en Filament).
- `CitasDeHoyWidget`: mismo filtro aplicado a la query del widget de dashboard.
- `CitaForm` y `HistoriaClinicaForm`: el campo `medico_id` trae `->default()` que preselecciona al médico logueado si está vinculado (sigue siendo editable).
- Diseño defensivo: un usuario con rol `medico` sin `medico_id` asignado sigue viendo todo (sin filtro), igual que el comportamiento anterior — evita bloquear a alguien por un dato sin migrar.
- **Pendiente**: correr la migración en el entorno real (`sail artisan migrate`) y asignar `medico_id` a los usuarios médico existentes desde `/admin/users` para que el filtro empiece a aplicar. Falta probar de punta a punta en el entorno real.
- Sintaxis validada con `php -l` (PHP 8.3 CLI en un entorno aislado) en los 9 archivos nuevos/modificados.
- Entregado como patch (`git am`).

## [2026-08-23] Confirmación en entorno real: gestión de usuarios + exportar Facturas a PDF

- Se corrió `./vendor/bin/sail composer require barryvdh/laravel-dompdf` (usando el composer de Sail, no el composer nativo de Windows/WSL, que no tiene PHP en el PATH y fallaba con `php: not found`).
- Probado en `/admin/users`: listar, crear y editar usuarios con los 3 roles, contraseña opcional al editar, y bloqueo confirmado al intentar que un admin se elimine a sí mismo.
- Probado el botón "Exportar PDF" en la tabla de Facturas y en `EditFactura`: descarga correcta del comprobante con los datos de paciente/cita/médico/área.
- Probado con un rol sin permiso (médico): la ruta `/facturas/{id}/pdf` responde 403, igual que el resto del Resource de Facturas.
- Todo confirmado funcionando sin ajustes adicionales. `MEMORIA.md` actualizado (secciones 7, 9 y 10) quitando las notas de "falta probar".

## [2026-08-23] Gestión de usuarios (Resource) + Exportar Facturas a PDF

- **Gestión de usuarios**: nuevo `app/Filament/Resources/Users/` (`UserResource.php`, `Schemas/UserForm.php`, `Tables/UsersTable.php`, `Pages/{ListUsers,CreateUser,EditUser}.php`), mismo patrón de carpetas que los otros 6 Resources. Solo `admin` puede ver/crear/editar/eliminar (`canViewAny()` bloquea el acceso completo para recepción/médico, incluyendo la entrada en el menú). Formulario con `name`, `email` (único, `ignoreRecord: true`), `Select` de `rol` (admin/recepcion/medico) y `password` (obligatorio solo al crear, opcional al editar — dejarlo en blanco no cambia la contraseña actual, usando el patrón estándar de Filament `dehydrateStateUsing`/`dehydrated` + `Hash::make`). Protección agregada: `canDelete()` excluye la propia cuenta del usuario logueado (no puede eliminarse a sí mismo), y el `DeleteBulkAction` de la tabla repite esa misma validación con un `->before()` porque un borrado masivo no pasa por `canDelete()` registro por registro. No se usó ningún paquete de permisos granulares (`spatie/laravel-permission`, Filament Shield) — no hace falta con solo 3 roles fijos.
- **Exportar Facturas a PDF**: nueva dependencia `barryvdh/laravel-dompdf` (⚠️ correr `composer require barryvdh/laravel-dompdf` — no se editó `composer.json`/`composer.lock` a mano para no dejarlos desincronizados entre sí). Se agregó `resources/views/pdf/factura.blade.php` (plantilla con CSS simple e inline; dompdf solo soporta un subconjunto de CSS, por eso no se usó ningún framework de estilos), `app/Http/Controllers/FacturaPdfController.php` (reutiliza `FacturaResource::canViewAny()` en vez de duplicar la regla de permisos) y la ruta `GET /facturas/{factura}/pdf` en `routes/web.php`, fuera de `/admin` porque descarga un archivo binario en vez de mostrar una pantalla de Filament, protegida con el middleware `auth` (mismo guard de sesión que usa Filament). Botón "Exportar PDF" agregado en la tabla de Facturas (acción por fila) y en la cabecera de `EditFactura`. Se dejó pendiente para una próxima pasada, si se pide: el mismo patrón aplicado a Historia Clínica, y la exportación nativa de tabla completa a Excel/CSV (`ExportAction` de Filament, no requiere código nuevo).
- **Nota de entorno**: igual que el resto del código entregado en este proyecto, se escribió sin acceso a Sail/MySQL del entorno real. Se instaló PHP 8.3 CLI en un entorno aislado para validar sintaxis (`php -l`) de los 11 archivos nuevos/modificados, y se verificaron contra la documentación oficial de Filament los nombres exactos de los iconos usados (`Heroicon::OutlinedUsers`, `Heroicon::OutlinedDocumentArrowDown`) y la firma de la Facade de `barryvdh/laravel-dompdf` (`Barryvdh\DomPDF\Facade\Pdf`). **Falta probar en el entorno real**, y falta correr `composer require barryvdh/laravel-dompdf` antes de que el botón de exportar funcione.
- Entregado como patch (`git am`).

## [2026-08-23] Investigación de gestión de usuarios y exportación + pregunta pendiente sobre "cuantificos"

- Se investigó gestión de usuarios desde el panel (hoy solo se puede por consola, ver sección 10) y exportación de registros (ej. Facturas). Conclusiones agregadas a la sección 9 de `MEMORIA.md`:
  - Gestión de usuarios: no hace falta un paquete de permisos granulares (`spatie/laravel-permission`, Filament Shield) dados los 3 roles fijos actuales — alcanza con un `UserResource` normal, solo visible para `admin`.
  - Exportación: Filament ya trae exportación nativa de tablas a Excel/CSV (`ExportAction`); para un comprobante individual con formato (ej. una factura) el patrón de la comunidad es `barryvdh/laravel-dompdf` con una plantilla Blade propia.
- Se agregó una pregunta pendiente en la sección 6: el contacto interno mencionó que la administración se maneja mediante algo que llamó "cuantificos" — término sin aclarar, queda pendiente de la entrevista formal.
- No hay cambios de código en esta entrada, solo documentación e investigación. Entregado como patch (`git am`).

## [2026-08-23] Investigación de funciones futuras — propuesta documentada, sin priorizar

- Sesión de investigación (buenas prácticas de software de gestión clínica, requisitos de la LOPDP de Ecuador para datos de salud, e ideas de otras industrias) para tener un banco de ideas listo cuando se quiera ampliar el sistema.
- Se agregó la sección 9 en `MEMORIA.md` ("Propuesta de funciones futuras") con: requisitos de cumplimiento pendientes (consentimiento del paciente, registro de auditoría vía `spatie/laravel-activitylog`), funciones típicas de EHR/practice management que aún faltan (exportar a PDF, reportes/KPIs, filtro "mis pacientes" por médico), y funciones "cruzadas" de otras industrias: lista de espera automática para cancelaciones (patrón de restaurantes/hoteles), recall/control preventivo (patrón de CRM de retail y clínicas dentales), encuesta de satisfacción post-visita, marcado de paciente frecuente, turno virtual para walk-ins, y panel ejecutivo de KPIs del negocio.
- **Decisión del usuario**: por ahora no se prioriza ni se construye nada de esto — queda solo como propuesta documentada para una futura sesión.
- No hay cambios de código en esta entrada, solo documentación. Entregado como patch (`git am`), igual que el resto de cambios a `MEMORIA.md`/`CHANGELOG.md`.

## [2026-08-23] Validación de cédula única — confirmado funcionando en el entorno real

- Se probó en `/admin/pacientes`: crear un paciente con una cédula repetida muestra el mensaje de validación en vez del error crudo de MySQL; editar un paciente sin cambiar su propia cédula guarda sin problema (confirma que `ignoreRecord: true` funciona); editar un paciente cambiando su cédula por la de otro paciente existente también dispara la validación correctamente.
- No hay cambios de código en esta entrada, solo la confirmación.

## [2026-08-23] Deuda técnica: validación de cédula única en el formulario de Pacientes

- `app/Filament/Resources/Pacientes/Schemas/PacienteForm.php`: se agregó `->unique(table: 'pacientes', column: 'cedula', ignoreRecord: true)` al campo `cedula`. Antes solo existía la restricción `unique` a nivel de base de datos (definida en la migración), así que crear o editar un paciente con una cédula repetida desde `/admin/pacientes` mostraba el error crudo de MySQL (`Integrity constraint violation`) en vez de un mensaje de validación claro.
- Se usó `ignoreRecord: true` porque este formulario se comparte entre la página de Crear y la de Editar — sin esa opción, guardar un paciente existente sin tocar su cédula fallaría la validación al "encontrarse a sí mismo" como duplicado.
- Este mismo problema ya se había corregido puntualmente en el modal de creación rápida de paciente dentro de `CitaForm.php` (ver entrada del punto 3 del plan de UX más abajo), pero no en el formulario original — quedaba documentado como deuda técnica en `MEMORIA.md` sección 7 y ahora se cierra.
- **Nota de entorno**: se validó sintaxis con `php -l` y la firma de `->unique(ignoreRecord: true)` contra la documentación oficial de Filament. Falta probarlo en el entorno real.
- Entregado como patch (`git am`).

## [2026-08-23] Buscador global — confirmado funcionando en el entorno real

- Se probó en `/admin` del entorno real: buscar "ju" en el buscador global devolvió correctamente las categorías "Citas" y "Pacientes", ambas mostrando a Julio Jaramillo con el título compuesto ("Cita — Julio Jaramillo Montenegro Aguirre") y los detalles de contexto (Médico, Área, Fecha, Estado / Cédula, Teléfono) tal como se diseñó en el punto 5 del plan de UX.
- Se detectó de paso un dato de prueba mal cargado (no un bug del código): un médico tiene el nombre completo duplicado entre los campos `nombres` y `apellidos`, lo que hace que se vea repetido en cualquier pantalla que muestre "nombres + apellidos" (incluyendo el detalle del buscador). Se corrige editando ese registro en `/admin/medicos`, no requiere cambio de código.
- Con esto quedan confirmados en el entorno real 4 de los 5 puntos del plan de UX (1, 2, 3 y 5); el punto 4 (filtros rápidos) sigue resuelto en código pero pendiente de esa confirmación.

## [2026-08-23] Buscador global (punto 5 del plan de UX)

- **Áreas** y **Médicos/Pacientes** ya tenían el buscador global habilitado por defecto (vía `$recordTitleAttribute`), pero solo buscaban por un único campo (`nombre`/`nombres`). Se amplió en `PacienteResource.php` y `MedicoResource.php` con `getGloballySearchableAttributes()` para buscar también por apellido, cédula, email y teléfono, y `getGlobalSearchResultTitle()` para mostrar "Nombres Apellidos" en vez de solo el nombre de pila.
- **Citas**, **Historias Clínicas** y **Facturas** no tenían buscador global activo (no tienen un único campo de texto representativo, así que nunca se les puso `$recordTitleAttribute`). Se agregó a los tres:
  - `$recordTitleAttribute` (un campo real cualquiera, solo para cumplir el requisito de habilitación — el título mostrado se sobreescribe).
  - `getGloballySearchableAttributes()` con "dot notation" para buscar dentro de las relaciones (ej. `paciente.nombres`, `medico.apellidos`) además de campos propios (`notas`, `motivo_consulta`, `diagnostico`, `estado_pago`, `metodo_pago`).
  - `getGlobalSearchResultTitle()` con un título compuesto ("Cita — Juan Pérez", "Historia clínica — Juan Pérez", "Factura — Juan Pérez").
  - `getGlobalSearchResultDetails()` con datos de contexto bajo el título (médico, área, fecha/hora y estado para Citas; médico, motivo y diagnóstico para Historias; monto, estado de pago y fecha para Facturas).
  - `getGlobalSearchEloquentQuery()` con `->with([...])` para precargar las relaciones usadas en título/detalles y evitar N+1 en cada resultado de búsqueda.
- Los permisos existentes se respetan sin cambios adicionales: Filament excluye automáticamente de la búsqueda global cualquier recurso cuyo `canViewAny()` devuelva `false` para el usuario actual (ej. recepción no ve Historias Clínicas ni en el menú ni en el buscador; médico no ve Facturas).
- **Nota de entorno**: se escribió sin acceso a PHP/Composer/Sail del proyecto real, pero se validó la sintaxis de los 5 archivos modificados instalando PHP CLI en un entorno aislado (`php -l`), y las firmas de los métodos se verificaron contra la documentación oficial de Filament 5.x (`getGloballySearchableAttributes()`, `getGlobalSearchResultTitle()`, `getGlobalSearchResultDetails()`, `getGlobalSearchEloquentQuery()`). Falta probarlo corriendo el proyecto real (igual que los puntos 2, 3 y 4 del plan de UX en su momento).
- Entregado como patch (`git am`).

## [2026-08-23] Filtros rápidos en la tabla de Citas (punto 4 del plan de UX)

- `app/Filament/Resources/Citas/Tables/CitasTable.php`: se agregaron 3 filtros tipo toggle (switch) en `->filters()`: "Hoy" (`whereDate('fecha', today())`), "Pendientes" y "Confirmadas" (`where('estado', ...)`).
- Se usó `layout: FiltersLayout::AboveContentCollapsible` para que los filtros aparezcan como controles visibles arriba de la tabla (colapsables) en vez de escondidos detrás del ícono de filtro por defecto — así son más rápidos de encontrar y usar en el día a día.
- No se agregaron filtros para "Atendida"/"Cancelada" — el plan solo pedía los 3 más usados; agregar más sigue el mismo patrón si hace falta después.
- **Nota de entorno**: igual que los puntos anteriores, se escribió sin acceso a PHP/Composer/Sail. Se verificó la firma exacta de `Filter::toggle()`, `->query()` y `Table::filters(array, layout:)` contra el código fuente y la documentación oficial de Filament 5, pero falta probarlo corriendo el proyecto real.
- Entregado como patch (`git am`).

## [2026-08-23] Crear paciente sin salir del formulario de Cita (punto 3 del plan de UX)

- `app/Filament/Resources/Citas/Schemas/CitaForm.php`: se agregó `->createOptionForm([...])` al selector `paciente_id`, con los mismos campos que el formulario de Pacientes (nombres, apellidos, cédula, fecha de nacimiento, teléfono, email, dirección, sexo). Ahora hay un botón "+" junto al selector que abre un modal para dar de alta un paciente nuevo sin perder los datos ya cargados en la cita.
- Se agregó validación `->unique(table: 'pacientes', column: 'cedula')` al campo cédula de ese modal — el formulario original de Pacientes no la tenía (solo la restricción de la base de datos), así que sin esto el modal habría mostrado el error crudo de MySQL en vez de un mensaje de validación claro si se repetía una cédula. **Nota**: esta validación se agregó solo en el modal nuevo, no en `PacienteForm.php` original — queda documentado como deuda técnica pendiente en `MEMORIA.md` sección 7.
- Se mejoró el selector de paciente en el mismo formulario: antes solo mostraba `nombres`, ahora muestra "Nombres Apellidos" (`getOptionLabelFromRecordUsing`) y permite buscar por nombre, apellido o cédula — para poder distinguir pacientes con el mismo nombre de pila.
- **Nota de entorno**: igual que los puntos 1 y 2, se escribió sin acceso a PHP/Composer/Sail. Se verificó la API exacta de `createOptionForm()`, `getOptionLabelFromRecordUsing()` y la firma de `unique()` contra el código fuente y la documentación oficial de Filament 5, pero falta probarlo corriendo el proyecto real.
- Entregado como patch (`git am`).

## [2026-08-23] Cambiar estado de una cita con un clic (punto 2 del plan de UX)

- `app/Filament/Resources/Citas/Tables/CitasTable.php`: se agregó un `ActionGroup` "Cambiar estado" en la tabla de Citas, antes del botón Editar. Contiene un botón por cada estado válido (Pendiente/Confirmada/Atendida/Cancelada); cada uno se oculta si la cita ya está en ese estado, y al hacer clic actualiza `estado` directo (`$record->update(...)`) sin abrir el formulario ni navegar de página, mostrando una notificación de éxito.
- Se extrajo el `match` de colores de estado (antes duplicado inline en la columna) a un método `colorEstado()` compartido, usado tanto por la columna con badge como por los nuevos botones de cambio de estado — así ambos quedan visualmente consistentes.
- El grupo completo respeta permisos: solo visible si `CitaResource::canEdit($record)`, igual que el botón Editar existente.
- **No implementado en esta pasada**: el mismo flujo rápido en el widget "Citas de hoy" del Dashboard (que por ahora solo tiene Editar). Queda como mejora natural para una próxima sesión si se quiere.
- **Nota de entorno**: igual que el punto 1, este cambio se escribió sin acceso a PHP/Composer/Sail. Se verificó la API exacta (`Filament\Actions\Action`, `ActionGroup`, `Notification::make()->send()`) contra el código fuente y la documentación oficial de Filament 5, pero falta probarlo corriendo el proyecto real.
- Entregado como patch (`git am`).

## [2026-08-23] Dashboard: widget de "citas de hoy" (punto 1 del plan de UX)

- Nuevo `app/Filament/Widgets/CitasDeHoyWidget.php`: widget de tabla (`Filament\Widgets\TableWidget`) que muestra las citas con `fecha` = hoy, ordenadas por `hora_inicio`, con columnas Hora/Paciente/Médico/Área/Estado (mismos colores de badge que la tabla de Citas) y acción de Editar respetando `CitaResource::canEdit()`. Se autodescubre solo porque `AdminPanelProvider` ya apuntaba `discoverWidgets()` a esa carpeta.
- `AdminPanelProvider`: se quitaron `AccountWidget` y `FilamentInfoWidget` (las tarjetas genéricas "Welcome"/"filament") para que el widget de citas de hoy sea lo primero que se ve al entrar a `/admin`.
- **No implementado en esta pasada**: filtrar las citas por médico logueado si el rol es `medico`. Sigue aplicando la limitación conocida de que `users` y `medicos` no están conectados (ver `MEMORIA.md` sección 9) — por ahora todos los roles ven todas las citas del día.
- **Nota de entorno**: este cambio se escribió sin acceso a PHP/Composer/Sail (entorno de generación del patch no tiene esas herramientas), así que no se pudo correr `php artisan` ni probar visualmente. Se siguió al pie de la letra la convención de los Resources existentes (namespace, sintaxis Filament 5 con `recordActions`, colores de badge). **Falta probar en el entorno real** levantando Sail y entrando a `/admin`.
- Entregado como patch (`git am`).

## [2026-08-23] Investigación de buenas prácticas de agendamiento clínico + plan de UX

Con el sistema interno ya funcional de punta a punta (CRUD + roles confirmados), se investigaron buenas prácticas de software de agendamiento clínico para identificar las mejoras de experiencia de uso con mayor impacto para recepción/médicos en el día a día.

Se dejó un plan priorizado de 5 mejoras (documentado en `MEMORIA.md` sección 8):
1. Dashboard con "citas de hoy" al entrar al panel (⭐ mayor impacto).
2. Cambiar el estado de una cita con un clic, sin abrir el formulario completo.
3. Crear un paciente nuevo sin salir del formulario de Cita (modal).
4. Filtros rápidos en la lista de Citas ("Hoy", "Pendientes", "Confirmadas").
5. Buscador global mejorado.

Explícitamente descartado por ahora (fase futura): recordatorios automáticos por WhatsApp/SMS, portal de autoagendamiento para pacientes.

No se tocó código en esta sesión — es planificación para la siguiente. Pendiente confirmar con el usuario cuál de los 5 puntos priorizar primero; sugerencia por defecto: puntos 1 y 2.

## [2026-08-23] Fix de fondo: botones no conectados a los permisos por rol

Diagnóstico del problema reportado (botón "Crear" visible para rol sin permiso, y borrado de Paciente ejecutándose sin 403 en vez de bloquearse):

**Causa raíz**: `canCreate()`/`canEdit()`/`canDelete()` en el Resource solo se aplican automáticamente al navegar por URL completa a las páginas de Crear/Editar (ahí sí bloquean con 403 correctamente). La *visibilidad* de los botones en pantalla y la ejecución del botón "Eliminar" (una acción de Livewire sin navegación de página) **no** estaban conectadas a esos métodos — Filament no lo hace automático, hay que conectarlo a mano.

**Impacto real**: no era solo cosmético — el botón "Eliminar" se podía ejecutar sin chequeo de rol; en la prueba anterior solo lo salvó la restricción de integridad de MySQL (el paciente tenía citas asociadas). Un registro sin relaciones se habría borrado sin ser admin.

**Fix**: se agregó `->visible()` en 18 puntos: los 6 `CreateAction` (páginas de lista), los 6 `EditAction` (tablas), los 6 `DeleteAction` (páginas de edición), y los 6 `DeleteBulkAction` (tablas) — cada uno conectado al método de autorización correspondiente del Resource.

Entregado como patch (`git am`). **Confirmado funcionando**: probado con el usuario de rol `recepcion` — Áreas/Médicos sin botones de Crear/Editar/Eliminar, Pacientes/Citas/Facturas con Crear/Editar pero sin Eliminar, Historia Clínicas sigue sin aparecer en el menú. Sistema de roles y permisos completo y verificado de punta a punta.

## [2026-08-23] Protección contra borrado con datos relacionados + diagnóstico de botón visible sin permiso

- **Reportado**: al probar el usuario de prueba con rol `recepcion`, el botón "Crear" aparecía visible en Áreas y Médicos (no debería), aunque al hacer clic sí devolvía 403 correctamente. Diagnóstico en curso — el código de `canCreate()` en el servidor está correcto, así que se sospecha de caché de Laravel o de una carga de página anterior al cambio de permisos. Se documentaron los pasos de troubleshooting en `MEMORIA.md` sección 9 (`optimize:clear` + refresh fuerte + verificar rol real en BD vía tinker). Pendiente confirmar si se resolvió.
- **Reportado y resuelto**: al intentar eliminar un Paciente con citas asociadas, salía el error crudo de MySQL `SQLSTATE[23000]: Integrity constraint violation... Cannot delete or update a parent row`. Se agregó una validación `->before()` en el `DeleteAction` de las páginas de edición de **Área**, **Médico**, **Paciente** y **Cita**: si el registro tiene datos dependientes, se cancela el borrado y se muestra una notificación clara en español en vez del error técnico. Se agregaron las relaciones `Area::citas()`, `Cita::historiaClinicas()` y `Cita::facturas()` a los modelos (faltaban, necesarias para poder chequear).
- Entregado como patch (`git am`).

## [2026-08-23] Roles y permisos por tipo de usuario

- Migración nueva: agrega columna `rol` (string, default `recepcion`) a la tabla `users`.
- Modelo `User` actualizado: `rol` agregado al atributo `#[Fillable]` (esta versión de Laravel usa atributos PHP en vez de la propiedad `$fillable` clásica), más los métodos `isAdmin()`, `isRecepcion()`, `isMedico()`.
- Se implementó autorización por rol en los 6 Filament Resources (`canViewAny`, `canCreate`, `canEdit`, `canDelete`), con la matriz de permisos documentada en `MEMORIA.md` sección 9: admin con acceso total; recepción sin acceso a Historias Clínicas; médico sin acceso a Facturas; solo admin puede eliminar en todos los recursos (y también editar/crear Áreas y Médicos).
- Limitación conocida y documentada: no hay todavía relación entre `users` y `medicos`, así que un médico ve todas las citas/historias, no solo las propias — queda pendiente para una fase futura.
- Entregado como patch (`git am`). Pasos pendientes tras aplicar: correr `sail artisan migrate`, y asignarle `rol = admin` al usuario admin existente vía `artisan tinker` (comando en `MEMORIA.md` sección 9) — si no, quedaría con el rol default `recepcion` y perdería acceso a partes del sistema.

## [2026-08-23] Fix: MassAssignmentException al crear registros desde Filament

Al intentar crear un Área desde `/admin`, Laravel bloqueó el guardado con `MassAssignmentException: Add [nombre] to fillable property`. Causa: por seguridad, Laravel no permite guardar campos en un modelo a menos que estén declarados explícitamente en `$fillable` (evita que se puedan sobreescribir campos no previstos, como manipular un `id` desde un formulario adulterado). Se agregó `$fillable` a los 6 modelos con exactamente las columnas de sus respectivas migraciones (sin incluir `id`, `created_at`, `updated_at`, que Laravel maneja aparte).

Entregado como patch (`git am`). **Confirmado funcionando**: se probó crear un Área, un Médico (con selector de Área encontrando "Odontología" por búsqueda), un Paciente, y una Cita conectando paciente + médico + área con selectores buscables — todo el flujo de punta a punta guarda correctamente.

## [2026-08-23] Relaciones Eloquent en modelos + Filament Resources ajustados

- Se agregaron las relaciones Eloquent (`belongsTo`/`hasMany`) a los 6 modelos (`Area`, `Paciente`, `Medico`, `Cita`, `HistoriaClinica`, `Factura`), que estaban vacíos desde su creación inicial.
- Se generaron los Filament Resources (`make:filament-resource`) para las 6 tablas, usando `nombre`/`nombres` como atributo de título y generando el formulario desde las columnas existentes de la base de datos. `HistoriaClinica` incluye además una vista de solo lectura (Infolist).
- Se ajustaron manualmente los formularios y tablas generados: los campos de llave foránea (`area_id`, `paciente_id`, `medico_id`, `cita_id`) pasaron de `TextInput` numérico a `Select` con relación Eloquent (`searchable()` + `preload()`), tanto en los formularios como en las columnas de listado (que ahora muestran nombres, ej. `area.nombre`, en vez de IDs crudos). Los campos `estado` (citas) y `estado_pago` (facturas) pasaron de texto libre a `Select` con opciones fijas, y se les agregó color (`badge`) según el valor. El campo `monto` de facturas se formatea ahora como moneda (`money('USD')`).
- Entregado como patch (`git am`) para aplicar sobre el commit de "Genera Filament Resources para las 6 tablas principales". Pendiente: aplicar el patch y probar el flujo completo desde `/admin` (crear un área → un médico → un paciente → una cita, y confirmar que los selectores encuentran los registros).

## [2026-08-23] Columnas y relaciones agregadas a las 6 migraciones

Se llenaron las 6 migraciones (`areas`, `pacientes`, `medicos`, `citas`, `historia_clinicas`, `facturas`) con las columnas y llaves foráneas acordadas (ver `MEMORIA.md`, sección 4). Se verificó que el orden de los archivos (por timestamp en el nombre) respeta las dependencias entre tablas: `areas` → `pacientes` → `medicos` → `citas` → `historia_clinicas` → `facturas` — necesario porque Laravel corre las migraciones en ese orden y una tabla con `foreignId()->constrained()` necesita que la tabla referenciada ya exista.

Entregado como patch (`git am`) para aplicar sobre el commit `fa1f37a`. Pendiente: correr `sail artisan migrate` después de aplicar el patch, para crear las tablas de verdad en MySQL.

## [2026-08-23] Modelos y migraciones creadas para las 6 tablas principales

Se generaron con `artisan make:model ... -m` los modelos y archivos de migración (aún vacíos, sin columnas) para: `Area`, `Paciente`, `Medico`, `Cita`, `HistoriaClinica`, `Factura`. La tabla `users` no se recreó — ya existe por defecto en Laravel, se le agregará el campo `rol` más adelante.

Se acordó el diseño completo de columnas y relaciones (ver `MEMORIA.md`, sección 4) — pendiente escribirlas dentro de cada archivo de migración y correr `sail artisan migrate`.

## [2026-08-23] Repositorio creado en GitHub y conectado al proyecto local

- Repo creado como **público** en `https://github.com/isra16class-byte/clinica-benites` — decisión consciente: el código no va a contener datos reales de pacientes (esos viven en la base de datos local, no en el repo).
- `git init`, `git add .`, `git remote add origin ...` corridos.
- Primer intento de `git push` falló con `src refspec main does not match any` — causa: se había hecho `git branch -M main` y el push antes de tener ningún commit real.
- Segundo intento de commit falló con `Author identity unknown` — faltaba configurar `user.name`/`user.email` de Git en esta máquina (nunca se había usado Git ahí antes).
- Configurado `git config --global user.email` y `user.name`. Pendiente confirmar que el commit + push posteriores se completaron sin errores.

## [2026-08-23] Instalación del entorno de desarrollo completa

Windows + WSL2 + Docker Desktop + Laravel Sail + Filament, todo funcionando:

- WSL2 instalado con Ubuntu (`wsl --install -d Ubuntu`) — el primer intento con `wsl --install` a secas no dejó ninguna distro instalada, hubo que especificar `-d Ubuntu` explícitamente.
- Docker Desktop instalado con integración WSL2 activada (Settings → Resources → WSL Integration → switch de Ubuntu).
- Git + VS Code + extensión WSL instalados. Confirmado que VS Code puede conectarse a la ventana WSL (indicador "WSL: Ubuntu" abajo a la izquierda).
- Proyecto Laravel creado con `curl -s "https://laravel.build/clinica-benites" | bash` (instala Laravel + Sail, corre todo en Docker sin PHP/MySQL directo en la máquina).
- `sail up` levantado — la primera carga tardó varios minutos descargando imágenes de Docker; los logs periódicos de "Meilisearch" con `status_code=200` generaron confusión (se aclaró que es un health-check normal, no que algo esté cargando).
- Error inicial al entrar a `localhost`: `SQLSTATE[42S02]: Base table or view not found... sessions` — resuelto corriendo `sail artisan migrate` (faltaban las migraciones base de Laravel).
- Filament instalado (`composer require filament/filament` + `artisan filament:install --panels`), usuario admin creado con `artisan make:filament-user`, panel accesible y funcional en `http://localhost/admin`.

## [2026-08-22] Definición de alcance y stack técnico

- Se identificó la clínica objetivo: **Clínica Benites**, Av. Francisco de Orellana, Guayaquil (vía Google Maps — 3.1★, 16 opiniones, sin web ni redes sociales encontradas).
- Confirmado por el contacto interno: **el paciente no va a agendar cita desde la web** — las citas las sigue creando el personal manualmente, pero quedan registradas en el sistema (por lo tanto, base de datos sí es necesaria de todas formas).
- Arquitectura decidida: web pública y sistema privado en el **mismo proyecto**, separados por rutas y autenticación, no por dominios distintos.
- Stack decidido: **Laravel + Filament + MySQL**, entorno de desarrollo con **Docker vía WSL2** (Windows), hosting futuro en **VPS** (Hetzner/DigitalOcean) en vez de hosting compartido.
- Confirmado que todo el desarrollo es gratuito hasta el momento de publicar (dominio ~$10-15/año + VPS ~$5-15/mes ≈ $70-100/año total).

## [2026-08-22] Guía de entrevista con el cliente

Se armó un documento con las preguntas clave a cubrir en la entrevista formal con el dueño/cliente de la clínica (aún pendiente de realizarse): contexto general, usuarios/roles, agenda, historias clínicas, facturación, página web, datos/privacidad (LOPDP Ecuador), infraestructura/presupuesto, soporte y crecimiento futuro.
