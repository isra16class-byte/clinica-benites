# 📜 CHANGELOG — Clínica Benites

Registro cronológico de cambios del proyecto. Formato: más nuevo arriba, nunca se borran entradas viejas.

Ver `MEMORIA.md` para el estado actual y contexto técnico completo — este archivo es solo la bitácora de "qué cambió cuándo".

---

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
