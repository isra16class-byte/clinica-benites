# 🧠 MEMORIA DEL PROYECTO — Clínica Benites

Este archivo es un resumen de contexto para retomar el desarrollo en cualquier momento (por ti mismo o pegándoselo a una IA). Explica qué es el proyecto, cómo está armado, qué decisiones se tomaron y por qué, y qué falta.

Última actualización: 23 de agosto de 2026 (filtro "mis pacientes" para el rol médico probado en el entorno real; se encontró y corrigió un bug real (medico_id no se limpiaba al cambiar el rol) y se aclaró un comportamiento reportado que en realidad es la matriz de permisos ya documentada — ver sección 10).

---

## 1. Qué es

Sistema de gestión para una clínica (**Clínica Benites**, Guayaquil — encontrada en Google Maps, 3.1★/16 opiniones, sin presencia digital previa) con dos partes:

1. **Página web pública** — información de la clínica, servicios, contacto. El paciente **NO agenda cita desde ahí** (decisión confirmada por el contacto interno del cliente).
2. **Sistema interno privado** (con login) — citas, historias clínicas, facturación. Lo usa el personal (recepción, médicos, administración). Las citas las sigue creando el personal manualmente (teléfono/WhatsApp), pero quedan registradas en el sistema.

Ambas partes conviven en el **mismo proyecto Laravel**, separadas por rutas (pública vs. protegida con login), no por dominios distintos.

Aún **no se ha hecho la entrevista formal** con el dueño de la clínica — el proyecto surgió de un contacto interno (un amigo que trabaja ahí).

Repo: `https://github.com/isra16class-byte/clinica-benites` (público)

## 2. Stack y por qué

- **Backend**: Laravel (PHP) — framework más usado en la región para este tipo de sistema (auth, roles/permisos y ORM ya resueltos).
- **Panel de administración**: Filament — genera pantallas de gestión (formularios + tablas) con poco código, ideal para el sistema privado de recepción/médicos.
- **Base de datos**: MySQL.
- **Entorno de desarrollo**: Docker vía Laravel Sail, corriendo sobre WSL2 (la PC de desarrollo es Windows). Elegido para que el entorno local sea idéntico al de producción (evita el clásico "en mi máquina sí funciona").
- **Hosting (a futuro, al publicar)**: VPS (Hetzner o DigitalOcean) en vez de hosting compartido — se necesita control total para Docker, y por tratarse de datos de salud, mejor tener control real sobre backups/seguridad.
- **Dominio**: .com, aún sin comprar.
- **Control de versiones**: Git + GitHub, repo público (decisión consciente: el código no contiene datos reales de pacientes — esos viven en la base de datos, no en el repo).

## 3. Estructura de archivos (hasta ahora)

```
clinica-benites/
  app/
    Models/
      Area.php              # Con $fillable y relación hasMany(Medico)
      Paciente.php           # Con $fillable y relaciones hasMany(Cita, HistoriaClinica, Factura)
      Medico.php              # Con $fillable y relaciones belongsTo(Area), hasMany(Cita, HistoriaClinica)
      Cita.php                 # Con $fillable y relaciones belongsTo(Paciente, Medico, Area)
      HistoriaClinica.php      # Con $fillable y relaciones belongsTo(Paciente, Medico, Cita)
      Factura.php              # Con $fillable y relaciones belongsTo(Paciente, Cita)
      User.php                # Con $fillable, campo `rol` y métodos isAdmin()/isRecepcion()/isMedico()
    Http/
      Controllers/
        FacturaPdfController.php  # Genera el PDF de una factura (dompdf), reutiliza permisos de FacturaResource
    Filament/
      Resources/
        Areas/                   # Resource completo (Form, Table, Pages)
        Pacientes/               # Resource completo
        Medicos/                 # Resource completo, selector de Área por relación
        Citas/                   # Resource completo, selectores por relación + estado con colores
        HistoriaClinicas/        # Resource completo + vista de solo lectura (Infolist)
        Facturas/                # Resource completo, selectores por relación + estado con colores, exportar a PDF
        Users/                   # Resource completo, solo accesible por admin (gestión de usuarios/roles)
  database/
    migrations/
      ..._create_areas_table.php            # Completa (nombre)
      ..._create_pacientes_table.php        # Completa (datos personales + cedula unique)
      ..._create_medicos_table.php          # Completa (FK area_id)
      ..._create_citas_table.php            # Completa (FKs paciente/medico/area, horario, estado)
      ..._create_historia_clinicas_table.php # Completa (FKs paciente/medico/cita nullable)
      ..._create_facturas_table.php         # Completa (FKs paciente/cita nullable, monto, pago)
  resources/
    views/
      pdf/
        factura.blade.php       # Plantilla del comprobante de factura (CSS simple, para dompdf)
  routes/
    web.php                    # Ruta GET /facturas/{factura}/pdf, protegida con middleware auth
  docker-compose.yml         # Generado por Sail
  .env                       # NO se sube a git (credenciales locales de MySQL, etc.)
  MEMORIA.md                 # Este archivo
  CHANGELOG.md                # Bitácora cronológica
```

## 4. Modelo de datos (ya escrito en las migraciones)

```
areas
  - nombre

pacientes
  - nombres, apellidos, cedula (unique), fecha_nacimiento, telefono, email, direccion, sexo

medicos
  - nombres, apellidos, area_id (FK -> areas), telefono, email

users (tabla default de Laravel, pendiente agregar)
  - name, email, password, rol (admin / recepcion / medico)

citas
  - paciente_id (FK), medico_id (FK), area_id (FK), fecha, hora_inicio, hora_fin,
    estado (pendiente/confirmada/cancelada/atendida), notas

historia_clinicas
  - paciente_id (FK), medico_id (FK), cita_id (FK, nullable), motivo_consulta,
    diagnostico, tratamiento, notas

facturas
  - paciente_id (FK), cita_id (FK, nullable), monto, estado_pago, metodo_pago, fecha
```

**Relaciones clave:**
- Un médico pertenece a un área.
- Una cita conecta paciente + médico en un horario.
- Una historia clínica normalmente nace de una cita (pero puede existir sin cita).
- Una factura normalmente nace de una cita atendida.

**Por qué `areas` es su propia tabla y no un campo fijo**: aún no se sabe cuántas especialidades tiene la clínica (pregunta pendiente, ver sección 6) — con una tabla aparte, agregar/quitar áreas después no requiere tocar código, solo agregar filas.

## 5. Estado actual del entorno de desarrollo

- ✅ WSL2 con Ubuntu instalado y funcionando (Windows).
- ✅ Docker Desktop conectado a WSL2.
- ✅ Git + VS Code + extensión WSL instalados.
- ✅ Proyecto Laravel (`clinica-benites`) corriendo con Sail (`./vendor/bin/sail up`).
- ✅ MySQL conectado, migraciones base de Laravel corridas.
- ✅ Filament instalado, panel accesible en `http://localhost/admin` con usuario admin creado.
- ✅ Modelos y archivos de migración creados para las 6 tablas principales, con columnas y relaciones ya definidas.
- ✅ Tablas creadas en MySQL (`sail artisan migrate` corrido correctamente, las 6 con `DONE`).
- ✅ Filament Resources generados para las 6 tablas (formulario + tabla + páginas). Los selectores de llaves foráneas (`area_id`, `paciente_id`, `medico_id`, `cita_id`) ya usan `Select` con relación Eloquent en vez de `TextInput` numérico, tanto en formularios como en las columnas de las tablas (muestran nombres, no IDs). Los campos `estado` (citas) y `estado_pago` (facturas) son `Select` con opciones fijas y colores (badge) en vez de texto libre.
- ✅ Git: repo en GitHub confirmado funcionando — `git log --oneline` muestra `HEAD -> main, origin/main` en el mismo commit, o sea que local y remoto están sincronizados.
- ✅ `composer require barryvdh/laravel-dompdf` corrido con `./vendor/bin/sail composer require ...` (dependencia agregada por el código de exportar Facturas a PDF, ver sección 9). `composer.json`/`composer.lock` ya están commiteados y pusheados con la entrada de `barryvdh/laravel-dompdf`.

## 6. Preguntas pendientes (por confirmar con el contacto interno / en la entrevista formal)

- [ ] ¿Cuántas áreas/especialidades tiene la clínica?
- [x] ¿Se agenda cita desde la web? → **No.**
- [ ] ¿El paciente ve precios/servicios publicados en la web, o se maneja solo internamente?
- [ ] ¿La cita se confirma automática o la aprueba recepción manualmente?
- [ ] ¿Historias clínicas digitales desde el inicio, o fase 2?
- [ ] ¿Facturación electrónica con el SRI? ¿Manejan seguros médicos/reembolsos, con cuáles aseguradoras?
- [ ] ¿Cuántos médicos/usuarios van a usar el sistema, y qué roles necesitan?
- [ ] ¿Acceso remoto o solo desde la clínica?
- [ ] ¿Presupuesto definido o hay que proponerlo?
- [ ] ¿Plazo de entrega esperado?
- [ ] ¿Planean crecer (más sucursales) pronto?
- [ ] ¿Qué es exactamente lo que el amigo/contacto interno llamó "cuantificos" al describir cómo se maneja hoy la administración de la clínica? Término sin aclarar (posibles hipótesis sin confirmar: recibos/comprobantes en papel, un cuaderno de registro manual, cálculos de caja a mano) — no se puede saber a qué proceso actual corresponde ni si el sistema ya lo resuelve o falta cubrirlo, hasta preguntarle directamente a él o al dueño.

## 7. Roadmap / pendientes técnicos

- [x] ~~Llenar las 6 migraciones con sus columnas~~ — resuelto.
- [x] ~~Confirmar que el push a GitHub se completó correctamente~~ — resuelto.
- [x] ~~Correr `sail artisan migrate`~~ — resuelto, las 6 tablas creadas.
- [x] ~~Crear los Resources de Filament (pantallas) para cada tabla~~ — resuelto, con selectores por relación en vez de IDs.
- [x] ~~Fix MassAssignmentException (faltaba \$fillable en los modelos)~~ — resuelto y confirmado: flujo completo Área → Médico → Paciente → Cita probado con éxito desde `/admin`.
- [x] ~~Agregar campo `rol` a la tabla `users` y definir permisos/roles en Filament~~ — resuelto y confirmado funcionando de punta a punta, incluyendo el fix de fondo de botones no conectados a permisos (ver sección 10).
- [x] ~~Dashboard con widget de "citas de hoy"~~ — resuelto (ver sección 8, punto 1). Confirmado funcionando por el usuario en el entorno real, incluyendo con una cita de prueba cargada.
- [x] ~~Cambiar estado de una cita con un clic desde la tabla~~ — resuelto (ver sección 8, punto 2). **Pendiente probar en el entorno real** (este cambio se escribió sin acceso a PHP/Sail, igual que el punto 1).
- [x] ~~Crear paciente nuevo sin salir del formulario de Cita~~ — resuelto (ver sección 8, punto 3). **Pendiente probar en el entorno real.**
- [x] ~~Filtros rápidos en la lista de Citas (Hoy/Pendientes/Confirmadas)~~ — resuelto (ver sección 8, punto 4). **Pendiente probar en el entorno real.**
- [x] ~~Buscador global mejorado en los 6 Resources~~ — resuelto (ver sección 8, punto 5). Confirmado funcionando por el usuario en el entorno real (búsqueda de "ju" mostró correctamente Citas y Pacientes con Julio Jaramillo, con título compuesto y detalles). De paso se detectó un dato de prueba mal cargado en un médico (nombre completo duplicado entre `nombres` y `apellidos`), sin relación con el buscador — corregirlo directamente en `/admin/medicos`.
- [x] ~~**Deuda técnica**: `PacienteForm.php` original sin validar `cedula` única a nivel de formulario~~ — **resuelto y confirmado funcionando por el usuario en el entorno real**. Se agregó `->unique(table: 'pacientes', column: 'cedula', ignoreRecord: true)` al campo `cedula` en `app/Filament/Resources/Pacientes/Schemas/PacienteForm.php` (el formulario de `/admin/pacientes`, usado tanto en Crear como en Editar). Ahora, si se repite una cédula al crear o editar un paciente desde ahí, sale un mensaje de validación claro en vez del error crudo de MySQL — mismo comportamiento que ya tenía el modal de creación rápida en `CitaForm.php`. Se usó `ignoreRecord: true` (a diferencia del modal, que no lo necesita por ser solo de creación) para que editar un paciente sin cambiar su propia cédula no dispare el error por "duplicarse a sí mismo".
- [x] ~~**Gestión de usuarios desde el panel** (`/admin/users`)~~ — resuelto y confirmado funcionando por el usuario en el entorno real (ver sección 9 y 10 para el detalle). Incluye la protección contra que un admin se elimine a sí mismo, también confirmada.
- [x] ~~**Exportar Facturas a PDF** (botón "Exportar PDF")~~ — resuelto y confirmado funcionando por el usuario en el entorno real, incluyendo que un rol sin permiso (médico) recibe 403 al intentar la ruta directa (ver sección 9 para el detalle).
- [x] ~~**Filtrar "mis pacientes" para el rol médico**~~ — resuelto y probado en el entorno real (ver sección 10 para el detalle). Durante la prueba se encontró y corrigió un bug (`medico_id` no se limpiaba al cambiar el rol de un usuario) y se aclaró que un segundo comportamiento reportado no era un bug, sino la matriz de permisos ya documentada (médico no crea Citas, sí Historias Clínicas).

## 8. Plan para la próxima sesión — pulir UX del sistema interno

El sistema ya es funcional de punta a punta (CRUD + roles). Lo que sigue es hacerlo **más rápido de usar en el día a día** para recepción/médicos. Investigado contra buenas prácticas de software de agendamiento clínico — priorizado de más a menos impacto:

1. [x] ~~**Dashboard con "citas de hoy" al entrar**~~ (⭐ mayor impacto) — **resuelto**. Se creó `app/Filament/Widgets/CitasDeHoyWidget.php` (extiende `Filament\Widgets\TableWidget`), que filtra `Cita::whereDate('fecha', today())` ordenado por `hora_inicio`, con columnas Hora/Paciente/Médico/Área/Estado (mismo badge de colores que la tabla de Citas) y una `EditAction` que respeta `CitaResource::canEdit()`. Se registra solo (`discoverWidgets` ya apuntaba a esa carpeta). Se quitaron del `AdminPanelProvider` los widgets genéricos `AccountWidget` y `FilamentInfoWidget` ("Welcome"/"filament"), así el widget de citas queda como lo primero que se ve al entrar a `/admin`. **No se filtró por médico logueado**: se mantiene la limitación conocida (`users` y `medicos` no están conectados, ver sección 10) — todos los roles ven todas las citas de hoy por ahora.

2. [x] ~~**Cambiar estado de una cita con un clic, sin abrir el formulario completo**~~ — **resuelto**. En `app/Filament/Resources/Citas/Tables/CitasTable.php` se agregó un `ActionGroup` "Cambiar estado" (icono de refresh) antes del botón Editar, con un botón por cada estado válido (pendiente/confirmada/atendida/cancelada). Cada botón se oculta si la cita ya está en ese estado, actualiza directo con `$record->update(['estado' => $estado])` (sin navegar a otra página) y muestra una notificación de éxito. Los colores de cada botón coinciden con los del badge de la columna Estado (se extrajo a un helper `colorEstado()` compartido para no duplicar el `match`). Todo el grupo respeta `CitaResource::canEdit($record)`, igual que el botón Editar. **No se aplicó todavía al widget de "Citas de hoy" del Dashboard** (punto 1) — ese widget por ahora solo tiene el botón Editar; sería una mejora natural para una próxima pasada si se quiere el mismo flujo rápido desde el Dashboard.

3. [x] ~~**Crear paciente nuevo sin salir del formulario de Cita**~~ — **resuelto**. En `app/Filament/Resources/Citas/Schemas/CitaForm.php` se agregó `->createOptionForm([...])` al selector de `paciente_id`, con los mismos campos que `PacienteForm` (nombres, apellidos, cédula, fecha de nacimiento, teléfono, email, dirección, sexo). Ahora aparece un botón "+" junto al selector que abre un modal para crear el paciente sin perder los datos ya cargados en el formulario de la cita. Se agregó validación `->unique(table: 'pacientes', column: 'cedula')` al campo cédula del modal (el `PacienteForm` original no la tenía — solo la restricción de la base de datos — así que sin esto el modal habría mostrado el error crudo de MySQL en vez de un mensaje claro si se repetía una cédula). De paso se mejoró cómo se ve el selector de paciente: antes solo mostraba `nombres`, ahora muestra "Nombres Apellidos" (vía `getOptionLabelFromRecordUsing`) y se puede buscar también por apellido o cédula — útil para poder diferenciar pacientes con el mismo nombre de pila, cosa que se vuelve más común ahora que se crean pacientes rápido desde acá.

4. [x] ~~**Filtros rápidos en la lista de Citas**~~ ("Hoy", "Pendientes", "Confirmadas") — **resuelto**. En `CitasTable.php` se agregaron 3 `Filter` con `->toggle()` (checkbox tipo switch en vez de checkbox normal): "Hoy" (`whereDate('fecha', today())`), "Pendientes" y "Confirmadas" (`where('estado', ...)`). Se combinó `layout: FiltersLayout::AboveContentCollapsible` para que aparezcan arriba de la tabla en vez de escondidos en un dropdown, y sean realmente "rápidos" de encontrar y tocar (se pueden colapsar si molestan). No se agregó filtro para "Atendida"/"Cancelada" porque el plan solo pedía esos 3 (los más usados en el día a día); si hace falta, es trivial copiar el patrón.

5. [x] ~~**Buscador global mejorado**~~ — **resuelto y confirmado funcionando por el usuario en el entorno real**. `Área` ya tenía buscador global habilitado por defecto (Filament lo activa solo con `$recordTitleAttribute`), pero `Médico`/`Paciente` solo buscaban por un campo (`nombres`), y `Cita`/`HistoriaClinica`/`Factura` no tenían buscador global en absoluto (no tienen un campo de texto único, así que nunca se les puso `$recordTitleAttribute`). Se agregó `getGloballySearchableAttributes()` a los 5 Resources restantes, con "dot notation" para buscar dentro de relaciones donde hace falta (ej. `Cita` ahora se encuentra buscando por nombre/apellido/cédula del paciente o del médico, o por texto en las notas). Se agregó `getGlobalSearchResultTitle()` para mostrar un título compuesto y legible (ej. "Cita — Juan Pérez" en vez del valor crudo de `fecha`) y `getGlobalSearchResultDetails()` para mostrar contexto extra bajo el título (médico, área, fecha/hora, estado, monto, etc., según el recurso). Se agregó `getGlobalSearchEloquentQuery()` con eager loading (`->with([...])`) en los recursos que muestran datos de relaciones en el título/detalles, para no generar N+1 en cada resultado. Los permisos no requirieron cambios: Filament ya excluye del buscador global cualquier recurso donde `canViewAny()` sea `false` para el usuario actual. **Probado en el entorno real**: búsqueda por "ju" devolvió correctamente las categorías "Citas" y "Pacientes" con Julio Jaramillo, con título compuesto y detalles como se diseñó.

**Explícitamente descartado por ahora** (para no abrumar al personal antes de que domine lo básico): recordatorios automáticos por WhatsApp/SMS, portal de autoagendamiento para pacientes. Quedan para una fase futura, después de validar que recepción/médicos ya están cómodos con el sistema base.

**Estado**: los 5 puntos del plan original de mejoras de UX están resueltos (Dashboard, cambiar estado con un clic, crear paciente desde el formulario de Cita, filtros rápidos, buscador global). Puntos 1, 2, 3 y 5 confirmados funcionando por el usuario en el entorno real; el punto 4 (filtros rápidos) sigue pendiente de esa confirmación (ver sección 7).

**Otros pendientes de fondo, sin definir aún**:
- Construir la página web pública (diseño, contenido) — sigue sin arrancar.
- Personalizar la marca del panel (logo, colores, nombre en vez de "Laravel").
- Sigue pendiente la respuesta del contacto interno de la clínica sobre cuántas áreas/especialidades tiene — no bloquea el desarrollo (el sistema ya soporta cualquier número de áreas dinámicamente), pero sería bueno tenerla para cargar datos reales en vez de datos de prueba.
- No se ha hecho la entrevista formal con el dueño de la clínica.

## 9. Propuesta de funciones futuras (investigadas, no priorizadas aún)

Sesión de investigación (23 de agosto de 2026) sobre buenas prácticas de software de gestión clínica y de otras industrias, para tener ideas listas cuando se quiera ampliar el sistema más allá del plan de UX ya resuelto (sección 8). **Nada de esto se ha construido ni se ha priorizado** — el usuario decidió dejarlo documentado como propuesta para más adelante, no tocar código por ahora.

**De cumplimiento legal (Ecuador — LOPDP, Ley Orgánica de Protección de Datos Personales, arts. 30-31):**
- Los datos de salud requieren tratarse con confidencialidad/secreto profesional y **consentimiento previo del paciente** — hoy el sistema no registra ese consentimiento.
- Se debe poder acreditar trazabilidad del tratamiento de datos de salud — el sistema hoy no tiene un registro de auditoría (quién vio/editó qué y cuándo) sobre `Paciente`/`HistoriaClinica`.
- Posible solución técnica identificada: paquete `spatie/laravel-activitylog` (+ un Resource de Filament para verlo) es el patrón estándar de la comunidad Laravel para esto — no haría falta construirlo desde cero.

**De software de gestión clínica típico (lo que ya traen Cliniko, Medesk, Doctoralia, etc. y que Benites aún no tiene):**
- Recordatorios de cita por WhatsApp/SMS (reducen inasistencias 35-50% según varias fuentes de la industria) — ya estaba descartado explícitamente para esta fase (ver arriba), se mantiene la decisión.
- Exportar historia clínica o factura a PDF.
- Reportes/KPIs básicos (citas por estado, ingresos por método de pago) vía widgets de estadísticas de Filament.
- ~~Filtrar "mis pacientes" para el rol médico~~ — resuelto, ver sección 10.

**Ideas cruzadas de otras industrias (más "renovadoras", no vistas típicamente en clínicas pequeñas de la región):**
1. **Lista de espera automática** (patrón de restaurantes/hoteles — OpenTable, Waitwhile): cuando se cancela una cita, el sistema ofrece automáticamente ese cupo al primer paciente en una lista de espera (por WhatsApp/link), en vez de que el hueco quede vacío hasta que recepción se acuerde de llamar a alguien. Usaría datos que el sistema ya tiene (`citas`, `estado`), sin necesitar APIs de pago de entrada. Identificada como la propuesta de mayor impacto/novedad.
2. **Recall / control preventivo**: el sistema marca automáticamente pacientes que "ya les toca su control" según su `historia_clinica` (ej. cada 6 meses) y sugiere a recepción a quién contactar — patrón de CRM de retail (reactivación de clientes) y de clínicas dentales/oftalmológicas. Convierte la clínica de reactiva a proactiva.
3. Encuesta rápida de satisfacción post-visita (1 pregunta tipo NPS, por WhatsApp el mismo día) — patrón de e-commerce/hotelería.
4. Marcado de "paciente frecuente" con notas de preferencia visibles en su ficha (ej. horario preferido, alergias) — patrón de hostelería (perfil de huésped recurrente).
5. Turno/ticket virtual para pacientes que llegan sin cita agendada — patrón de fila virtual de retail/bancos.
6. Panel ejecutivo de "salud del negocio" (ocupación de agenda, tasa de inasistencia, ingresos por médico/mes) para el dueño, más allá del widget operativo de "citas de hoy" que ya existe.

**Explícitamente descartado por ahora** (ver también sección 8): recordatorios automáticos por WhatsApp/SMS, portal de autoagendamiento para pacientes — quedan para una fase futura, después de validar que recepción/médicos ya están cómodos con el sistema base. Las ideas de esta sección quedan en la misma categoría: útiles a futuro, no urgentes.

**Resuelto en esta sesión** (a diferencia de la lista de arriba, estas eran huecos operativos reales de hoy, no "ideas para más adelante"):

- **Gestión de usuarios desde el panel** — **resuelto y confirmado funcionando por el usuario en el entorno real**. Nuevo `app/Filament/Resources/Users/` (mismo patrón de carpetas que los demás Resources: `UserResource.php`, `Schemas/UserForm.php`, `Tables/UsersTable.php`, `Pages/{ListUsers,CreateUser,EditUser}.php`). No se usó ningún paquete de permisos granulares (`spatie/laravel-permission`, Filament Shield) — con los 3 roles fijos (admin/recepcion/medico) alcanza con este Resource normal + el campo `Select` de `rol` que ya existía en la tabla `users`. Solo `admin` puede ver/crear/editar/eliminar este Resource (`canViewAny()` ya bloquea el acceso completo, incluida la entrada en el menú, para recepción y médico). Protección extra agregada: un admin **no puede eliminar su propia cuenta** (`canDelete()` lo excluye explícitamente, y el `DeleteBulkAction` de la tabla valida lo mismo con un `->before()` antes de un borrado masivo) — así la clínica no puede quedarse sin ningún admin activo por accidente; probado directamente y confirmado que bloquea el autoborrado. El campo contraseña es opcional al editar (dejarlo en blanco no cambia la contraseña actual) y obligatorio al crear, con el patrón estándar de Filament (`dehydrateStateUsing`/`dehydrated` + `Hash::make`).
- **Exportar Facturas a PDF** — **resuelto y confirmado funcionando por el usuario en el entorno real** para el caso de un comprobante individual (el caso identificado con más sentido de negocio). Se agregó `barryvdh/laravel-dompdf` como dependencia nueva (se instaló corriendo `./vendor/bin/sail composer require barryvdh/laravel-dompdf` — importante usar el composer **de Sail**, no el composer nativo de Windows/WSL, porque este último no tiene PHP en el PATH). Se agregó `resources/views/pdf/factura.blade.php` (plantilla con CSS simple e inline, sin frameworks externos — dompdf solo soporta un subconjunto de CSS), `app/Http/Controllers/FacturaPdfController.php` (reutiliza `FacturaResource::canViewAny()` para no duplicar la regla de permisos — médico no puede descargarlo tampoco por esta ruta, confirmado con 403 al probarlo) y una ruta nueva `GET /facturas/{factura}/pdf` en `routes/web.php` (fuera de `/admin` porque genera un archivo binario para descargar, no una pantalla de Filament; protegida con el middleware `auth`, mismo guard de sesión que usa Filament). Botón "Exportar PDF" agregado tanto en la tabla de Facturas (recordActions) como en la cabecera de `EditFactura`, ambos probados y funcionando. **No se hizo** exportación de tabla completa (Excel/CSV) — Filament ya trae eso nativo (`ExportAction`) sin necesitar código nuevo, se puede agregar en cualquier momento si se pide. Tampoco se aplicó a Historia Clínica en esta pasada (quedaría con el mismo patrón, ver sección 7 para dejarlo como pendiente si se quiere).

**Pendiente sin resolver (ver sección 6)**: el contacto interno mencionó que la administración de la clínica hoy se maneja mediante algo que llamó "cuantificos" — término sin aclarar todavía, no se sabe a qué proceso corresponde. No se puede evaluar si el sistema ya lo cubre o si falta construir algo para eso hasta preguntarle directamente.

## 10. Roles y permisos

Campo `rol` en la tabla `users` (string, default `recepcion`). Valores válidos: `admin`, `recepcion`, `medico`. El modelo `User` tiene los métodos `isAdmin()`, `isRecepcion()`, `isMedico()` para chequear el rol.

**Matriz de permisos implementada** (en cada `XResource.php`, vía `canViewAny()`/`canCreate()`/`canEdit()`/`canDelete()`):

| Recurso | Admin | Recepción | Médico |
|---|---|---|---|
| Áreas | Todo | Solo ver | Solo ver |
| Médicos | Todo | Solo ver | Solo ver |
| Pacientes | Todo | Todo | Ver y editar (sin eliminar) |
| Citas | Todo | Todo | Ver y editar (sin eliminar) |
| Historias Clínicas | Todo | Sin acceso | Todo (eliminar solo admin, por sensibilidad legal) |
| Facturas | Todo | Todo | Sin acceso |
| Usuarios | Todo (excepto eliminar su propia cuenta) | Sin acceso | Sin acceso |

**Filtro "mis pacientes" para el rol médico — resuelto** (ya no es una limitación conocida). Se conectó la tabla `users` con `medicos`:

- Migración `2026_08_23_220000_add_medico_id_to_users_table.php`: agrega `medico_id` (nullable, FK a `medicos`, `nullOnDelete()`) a `users`.
- `User::medico()` (`belongsTo`) y `medico_id` agregado al `#[Fillable]` del modelo.
- `UserForm`: nuevo `Select` "Médico vinculado" (`medico_id`), visible solo cuando el `Select` de `rol` (ahora `->live()`) está en `medico`. Se asigna desde `/admin/users`, junto con el resto de datos del usuario.
- `CitaResource::getEloquentQuery()` y `HistoriaClinicaResource::getEloquentQuery()`: si el usuario logueado `isMedico()` y tiene `medico_id` asignado, se agrega `->where('medico_id', $user->medico_id)` a la consulta base — afecta la tabla, el buscador global (usa `getGlobalSearchEloquentQuery()`, que ya extiende de `getEloquentQuery()` en Filament) y cualquier vista que dependa de esos Resources.
- `CitasDeHoyWidget`: mismo filtro aplicado a la query del widget del Dashboard, para que un médico solo vea sus propias citas de hoy, no las de todos.
- `CitaForm` y `HistoriaClinicaForm`: el campo `medico_id` ahora trae `->default(fn (): ?int => Auth::user()?->medico_id)` — si quien crea el registro es un médico vinculado, aparece preseleccionado a sí mismo (sigue siendo editable), para evitar el error de agendar/registrar algo a nombre de un colega por descuido.
- **Diseño defensivo**: si un usuario con rol `medico` no tiene `medico_id` asignado todavía (dato sin migrar, o admin que olvidó vincularlo), el filtro no se aplica y ese usuario sigue viendo todo — igual que el comportamiento actual — en vez de no ver nada. Es preferible "ver de más" temporalmente a que un médico real quede bloqueado por un dato sin cargar.
- **Pendiente**: asignar `medico_id` a los usuarios con rol médico que ya existen en el sistema (créalos/edítalos desde `/admin/users` y selecciona su médico vinculado). Sin ese paso, el filtro simplemente no aplica para ellos (ver diseño defensivo arriba) — no rompe nada, pero tampoco se benefician del filtro hasta hacerlo. **Probado parcialmente en el entorno real**: el filtro de Citas/Historias Clínicas/Dashboard y el autoselect al crear funcionan según lo esperado. Se encontró y corrigió un bug real durante la prueba (ver debajo); un segundo comportamiento reportado no era un bug, sino la matriz de permisos ya documentada (ver debajo).

**Bug encontrado y corregido durante la prueba — `medico_id` no se limpiaba al cambiar el rol**: al editar un usuario con rol `medico` (con un médico vinculado) y cambiarle el rol a `recepcion`/`admin`, el campo "Médico vinculado" desaparecía del formulario (por el `->visible()`), pero **su valor seleccionado seguía guardándose** al hacer submit — Filament no descarta el valor de un campo solo por ocultarlo con `->visible()`. Resultado: el usuario quedaba con `rol = recepcion` pero `medico_id` seguía apuntando al médico anterior, un estado inconsistente. **Solución aplicada, en dos capas**:
1. El `Select` de `rol` en `UserForm` ahora tiene `->afterStateUpdated()`: en cuanto cambia a cualquier valor distinto de `medico`, resetea `medico_id` a `null` directamente en el estado del formulario (antes de guardar).
2. Cinturón de seguridad adicional en `CreateUser::mutateFormDataBeforeCreate()` y `EditUser::mutateFormDataBeforeSave()`: si el `rol` guardado no es `medico`, fuerza `medico_id = null` justo antes de persistir, por si algún caso raro del navegador no dispara el evento del punto 1.

**No era un bug — comportamiento esperado**: el usuario de prueba con rol médico no veía el botón "Crear" en `/admin/citas`, pero sí en `/admin/historia-clinicas`. Esto es correcto según la matriz de permisos ya documentada (ver tabla arriba): en **Citas**, médico solo tiene "Ver y editar (sin eliminar)" — nunca tuvo permiso de crear, por diseño (las citas las agenda recepción por teléfono/WhatsApp). En **Historia Clínica**, médico sí tiene "Todo", por eso ahí el botón aparece. Si se quiere cambiar esta regla (permitir que médico también cree citas), es una decisión de negocio a confirmar, no una corrección de código.

**Cómo asignar el rol al usuario admin existente** (el usuario creado con `make:filament-user` antes de esta migración quedó con el default `recepcion`):

```
./vendor/bin/sail artisan tinker
>>> \App\Models\User::first()->update(['rol' => 'admin']);
>>> exit
```

**Para crear usuarios nuevos con cualquier rol**, ya no hace falta la consola: desde `/admin/users` un usuario `admin` puede crear/editar usuarios y asignarles rol directamente (Resource agregado, ver sección 9). El método de `tinker` de arriba sigue siendo útil solo para el primer usuario admin (antes de que exista ninguno con ese rol para entrar al Resource).

**Causa raíz confirmada del botón visible con 403 (y del riesgo de borrado sin permiso)**: en Filament, `canCreate()`/`canEdit()`/`canDelete()` del Resource solo se revisan automáticamente cuando se **navega a una ruta completa** (`/areas/create`, `/areas/{id}/edit`) — ahí sí bloquean con 403. Pero ni la visibilidad de los botones en pantalla, ni el botón "Eliminar" (que actúa como una acción de Livewire dentro de la misma página, sin navegar a otra ruta) estaban conectados a esos métodos por defecto. Esto significaba dos problemas: (1) botones de Crear/Editar visibles para roles sin permiso aunque el clic diera 403, y (2) más grave — el botón **Eliminar no pasaba ninguna validación de permiso**, solo lo salvó la restricción de MySQL en el caso probado (paciente con citas relacionadas); un registro sin relaciones se habría podido borrar sin ser admin.

**Solución aplicada**: se agregó `->visible()` explícito a cada `CreateAction` (en los 6 `List*.php`), cada `EditAction` (en las 6 tablas), cada `DeleteAction` (en los 6 `Edit*.php`) y cada `DeleteBulkAction` (en las 6 tablas) — 18 puntos en total — todos referenciando los métodos `canCreate()`/`canEdit()`/`canDelete()` ya definidos en cada Resource (o `Auth::user()?->isAdmin()` directo para los borrados masivos, ya que en la matriz solo admin borra en todos los recursos).

**Troubleshooting general para casos futuros similares**: si un botón de acción (Crear/Editar/Eliminar) se ve visible mostrando 403 al usarlo, o si una acción tipo "Eliminar"/"Ver" no respeta el rol, revisar si esa acción tiene `->visible()` conectado explícitamente al método de autorización del Resource — no basta con definir `canCreate()`/`canEdit()`/`canDelete()` en el Resource, hay que conectarlos a mano en cada botón.

**Protección contra borrado con datos relacionados**: Área, Médico, Paciente y Cita ahora validan antes de borrar (en el `DeleteAction` de su página de edición) si tienen registros dependientes (ej. un paciente con citas, un médico con historias clínicas). Si los tiene, se cancela el borrado y se muestra una notificación clara en español, en vez del error crudo de MySQL (`Integrity constraint violation`). Historia Clínica y Factura no necesitan esta protección porque nada más depende de ellas.

## 11. Historial de cambios

Ver `CHANGELOG.md` — ahí se registra cronológicamente cada paso importante. Este archivo (`MEMORIA.md`) describe el estado **actual**, se sobreescribe cada vez que algo cambia. El changelog se va acumulando, nunca se borra lo viejo.

---

**Para continuar el desarrollo**: lee este archivo primero, después revisa `CHANGELOG.md` para el detalle de qué se hizo en qué orden.

**Cada vez que se haga un cambio importante:**
1. Actualizar `MEMORIA.md` para reflejar el estado nuevo.
2. Agregar una entrada nueva arriba en `CHANGELOG.md`.
3. Hacer commit de ambos junto con el código.

**Nota sobre el flujo de trabajo**: el usuario no le da a Claude push directo al repo. El flujo real es: Claude clona el repo (`https://github.com/isra16class-byte/clinica-benites`, es público) en un entorno propio, hace el cambio, commitea localmente **usando la identidad de Git del usuario** (`user.name = isra16class-byte`, `user.email = isra16class@gmail.com` — los mismos configurados en su máquina), y genera un patch con `git format-patch -1 HEAD` que entrega como archivo descargable. Así, el autor que queda registrado en cada commit es el usuario, nunca Claude. El usuario lo aplica de su lado con `git am nombre-del-patch.patch` (conserva ese autor y el mensaje de commit) y hace el `git push` él mismo. Esto aplica también a los commits que actualizan `MEMORIA.md`/`CHANGELOG.md`: van en un patch aparte o en el mismo patch que el código, pero siempre pasan por este mismo mecanismo — nunca se asuma que Claude tiene (o debe pedir) acceso de escritura directo al repo remoto.
