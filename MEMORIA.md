# 🧠 MEMORIA DEL PROYECTO — Clínica Benites

Este archivo es un resumen de contexto para retomar el desarrollo en cualquier momento (por ti mismo o pegándoselo a una IA). Explica qué es el proyecto, cómo está armado, qué decisiones se tomaron y por qué, y qué falta.

Última actualización: 25 de agosto de 2026 — segunda entrada del día (a pedido del usuario, se organizó el menú lateral del panel en grupos de navegación (`$navigationGroup`), ya que solo el módulo de Infraestructura tenía grupo propio y los otros 10 Resources aparecían todos sueltos y mezclados. Ver el detalle completo en la sección 8.4 (nueva) más abajo — no se tocaron íconos ni ningún otro comportamiento, solo la agrupación del menú.)

Última actualización anterior: 25 de agosto de 2026 (se sincronizó el repo con `git pull` (traía el patch de la sección 6.2 aplicado en la sesión anterior) y se reemplazó el logo provisorio dibujado a mano por el **logo real de la clínica**, que el usuario compartió (imagen suelta + embebido en un PDF de servicios). Ver la nueva entrada "Logo real recibido y aplicado (25 ago 2026)" dentro de la sección 8.1 para el detalle completo. De paso, y en una conversación aparte sobre cómo mostrar el sistema en la entrevista sin llevar la laptop pesada, se agregó `URL::forceScheme('https')` condicional en `AppServiceProvider.php` — soluciona un problema de "mixed content" (CSS/JS bloqueados) al exponer el sistema local por un túnel HTTPS (Cloudflare Tunnel); no afecta nada en local porque solo se activa si `APP_URL` empieza con `https://`.)

Última actualización anterior: 24 de agosto de 2026 — decimocuarta entrada del día (a pedido del usuario, se cambió otra vez el criterio del nombre de archivo del adjunto de Orden de Estudio — de "ULID + nombre original subido" (decimotercera entrada) a **"ULID + nombre del paciente + tipo de estudio"** (ej. `01jz3k9x...-paul-guerrero-laboratorio.pdf`), para que el nombre en el disco identifique a quién pertenece el resultado en vez de depender del nombre que traía el archivo del navegador. Usa `Get $get` dentro de `getUploadedFileNameForStorageUsing()` para leer `paciente_id`/`tipo` del propio formulario en el momento del upload (mismo patrón que ya usaba `UserForm.php` para mostrar/ocultar el campo `medico_id` según el rol) — si el usuario sube el archivo antes de seleccionar paciente/tipo, cae a un texto genérico en vez de fallar. **Confirmado funcionando por el usuario en el entorno real.**)

Última actualización anterior: 24 de agosto de 2026 — decimotercera entrada del día (dos bugs más encontrados y corregidos al probar **Órdenes de Estudio** en el entorno real: (1) el adjunto (`resultado_archivo`) no tenía botones de ver/descargar — se agregó `->openable()`/`->downloadable()` — y no tenía restricción de tipo de archivo — se limitó a PDF/JPG/PNG/WEBP; (2) al usarlos, el botón "ver" daba `ERR_CONNECTION_REFUSED` en `localhost:8000` — causa real: el `.env` del usuario tenía `APP_URL=http://localhost:8000` pero Sail expone la app en el puerto 80 (`http://localhost`, sin puerto) según `compose.yaml` — se corrigió cambiando `APP_URL` en su `.env` local, no fue necesario tocar código. **Ambos confirmados funcionando por el usuario en el entorno real.** Además, a pedido del usuario, se cambió el nombre generado para el archivo guardado: en vez del nombre aleatorio de 26 caracteres por defecto de Filament (ilegible), ahora usa un ULID corto + el nombre original slugificado + extensión (`->getUploadedFileNameForStorageUsing()`), para que sea identificable sin arriesgar colisión entre archivos con el mismo nombre.)

Última actualización anterior: 24 de agosto de 2026 — duodécima entrada del día (fix de bug reportado por el usuario al probar 6.2 en el entorno real por primera vez: al crear una Cirugía con al menos un "médico adicional", Filament tiraba `SQLSTATE[HY000]: Field 'nombres' doesn't have a default value` al insertar en `medicos`. Causa: el `Repeater` de médicos adicionales usaba `->relationship()` sobre `Cirugia::medicosAdicionales()`, que es un `belongsToMany` — Filament interpreta eso como "crear un registro NUEVO en la tabla relacionada por cada fila", no como "asociar un médico ya existente + guardar su rol en el pivote `cirugia_medico`". Se quitó `->relationship()` del Repeater (`CirugiaForm.php`) y se reemplazó por sync manual del pivote: `CreateCirugia::afterCreate()` y `EditCirugia::mutateFormDataBeforeFill()` + `afterSave()`. **Confirmado funcionando por el usuario en el entorno real.** De paso, el usuario detectó que los clics repetidos al botón "Crear cirugía" (mientras la pantalla no daba feedback por el error de arriba) sí habían alcanzado a guardar la cirugía antes de fallar en el repeater, dejando 4 registros duplicados — se le indicó borrarlos a mano desde el listado, no fue necesario tocar código para eso. Este bug solo afectaba a Cirugía (único Resource con un Repeater sobre relación `belongsToMany` con pivote); el resto del módulo 6.2 no usa este patrón.)

Última actualización anterior: 24 de agosto de 2026 — undécima entrada del día (la sección **6.2** — infraestructura física: camas/internamiento, quirófanos/cirugías, procedimientos/estudios, emergencias, ambulancia — pasó de propuesta documentada a **módulo construido**, mismo criterio que se usó con 6.3: el usuario pidió avanzar con **supuestos razonables** sobre las 5 decisiones que seguían sin confirmar con la clínica, documentándolos para poder ajustarlos después. Se crearon 7 tablas nuevas (`camas`, `quirofanos`, `internamientos`, `cirugias`, `cirugia_medico`, `ordenes_estudio`, `servicios_ambulancia`) + 2 columnas nuevas en `citas` (`origen`/`prioridad`), 6 modelos nuevos, 6 Resources completos de Filament, y se tocó `CitaForm`/`CitasTable` para agregar los campos de emergencia. Ver la sección 6.2 actualizada abajo para el detalle completo. **Aún sin confirmar por el usuario en el entorno real** — escrito sin acceso a PHP/Sail (se validó sintaxis con `php -l`, pero no se corrió la migración ni se probó en `/admin`).)

Última actualización anterior: 24 de agosto de 2026 — décima entrada del día (se le explicó al usuario, en un documento Word entregado aparte (no versionado en el repo), qué es cada módulo del sistema, sus opciones y cómo se relacionan entre sí. De esa conversación salió una pregunta pendiente nueva, agregada a la sección 6: si los 3 roles actuales alcanzan o falta un rol de farmacia, dado que hoy el médico no tiene acceso al módulo de inventario aunque es quien aplica los insumos. Solo documentación — no se tocó código.)

Última actualización anterior: 24 de agosto de 2026 — novena entrada del día (el módulo de **Medicamentos e Insumos** (sección 6.3), construido en la entrada anterior, quedó **confirmado funcionando por el usuario en el entorno real**: catálogo, lotes con vencimiento, movimientos de entrada/salida con stock recalculado en vivo, protección contra borrado y permisos por rol, todo probado en vivo sin problemas.)

Última actualización anterior: 24 de agosto de 2026 — octava entrada del día (primer módulo **construido** con código, no solo documentado: **Medicamentos e Insumos**, sección 6.3. El usuario decidió empezar por este módulo en vez de la infraestructura de la 6.2, y pidió avanzar con **supuestos razonables** sobre las decisiones aún sin confirmar con la clínica, documentándolos para poder ajustarlos después en vez de esperar esa confirmación. Se crearon 3 tablas (`items_inventario`, `lotes_inventario`, `movimientos_inventario`), 3 modelos y 3 Resources completos de Filament — ver la sección 6.3 actualizada abajo para el detalle completo de qué se construyó y qué supuesto se aplicó en cada decisión pendiente. **Aún sin confirmar por el usuario en el entorno real** — no se ha corrido la migración todavía.)

Última actualización anterior: 24 de agosto de 2026 — séptima entrada del día (a pedido del usuario, se investigó en internet cómo resuelven este mismo problema otros sistemas hospitalarios/clínicos y estándares del sector, para validar las propuestas de planificación de las secciones 6.2 y 6.3 y detectar huecos antes de construir nada. Resultado: ambas propuestas están bien encaminadas y no se rediseñan, pero se les agregan **3 ajustes concretos** encontrados en la investigación — ver el detalle marcado "(validado con investigación externa, 24 ago 2026)" dentro de 6.2 y 6.3 — y se agrega una sección nueva, **6.4**, con el marco legal ecuatoriano aplicable (protección de datos de salud y normativa del MSP sobre historia clínica electrónica), que no estaba documentado. Explícitamente **solo documentación e investigación, sin tocar código**.)

Última actualización anterior: 24 de agosto de 2026 — sexta entrada del día (a pedido del usuario, se agregó la sección **6.3**: una propuesta de planificación — sin tocar código, mismo estilo que la 6.2 — de cómo modelar a futuro el módulo de **medicamentos e insumos** mencionado por el contacto interno en la sección 6.1, que debe vivir en farmacia, quirófano, admisión y facturación. A diferencia de la infraestructura física de la 6.2, este módulo no tenía ninguna propuesta previa — es dominio completamente nuevo, sin nada construido hoy. Explícitamente **solo documentación**, para planificar fase 2/3.)

Última actualización anterior: 24 de agosto de 2026 — quinta entrada del día (a pedido del usuario, se agregó la sección **6.2**: una propuesta de planificación — sin tocar código — de cómo modelar a futuro la infraestructura del PDF de la sección 6.1 (UCI, quirófanos, hospitalización, laboratorio, etc.), agrupada en 5 conceptos posibles (camas/internamiento, quirófanos/cirugías, procedimientos/estudios, emergencias, ambulancia), más las decisiones que faltan confirmar con la clínica antes de construir cualquiera de ellos. Explícitamente **solo documentación**, el usuario aclaró que es para planificar la fase 2/3, no para construir ahora.)

Última actualización anterior: 24 de agosto de 2026 — cuarta entrada del día (se creó `database/seeders/AreaSeeder.php` con las 27 especialidades reales de la clínica, la respuesta del contacto interno documentada en la entrada anterior — usa `firstOrCreate` para no duplicar si ya había áreas de prueba cargadas. Registrado en `DatabaseSeeder.php` para correr con `db:seed`, o solo con `--class=AreaSeeder`. No se cargaron los "servicios/infraestructura" del PDF (UCI, quirófanos, etc.) como áreas — no son especialidades médicas en el sentido del modelo actual, quedan solo como contexto documentado. **Confirmado funcionando por el usuario en el entorno real.** Ver sección 6.1 y 9 para el detalle.)

Última actualización anterior: 24 de agosto de 2026 — tercera entrada del día (el contacto interno de la clínica respondió sobre las áreas/especialidades, con material de marketing real (`Servicios_CB_2026.pdf`): son 27 especialidades, y la clínica funciona con lógica de hospital — quirófanos, UCI, hospitalización — no solo consultorios. También aclaró el alcance por fases: el registro de pacientes/citas de hoy corresponde solo a **admisión**; un futuro módulo de medicamentos/insumos iría en farmacia, quirófano, admisión y facturación (no construido aún); y para 2027 planean innovar consultorios + agregar registro de prescripciones médicas. Propósito general declarado: digitalizar la mayor parte del historial clínico del paciente. **Solo documentación por ahora — no se tocó código**, ver sección 6.1 para el detalle completo. No confundir con la entrada anterior, sobre el botón "Cancelar"/"Atrás", que sigue siendo válida y no se modificó.)

Última actualización anterior: 24 de agosto de 2026 — segunda entrada del día (botón "Cancelar" reemplazado por "Atrás" en todas las pantallas de Editar del panel — antes, al terminar de editar cualquier registro, el formulario mostraba "Guardar cambios" y "Cancelar" lado a lado, pero "Cancelar" ahí es redundante: los cambios ya guardados no se "cancelan" descartándolos, solo se vuelve al listado sin guardar. Se reemplazó por un botón "Atrás" (ícono de flecha a la izquierda) que lleva directo al listado del recurso — ej. `/admin/pacientes` al editar un paciente. **No se tocó** el botón "Cancelar" de las pantallas de **Crear**: ahí sí es útil, porque descarta un formulario que todavía no se guardó. Aplica a los 7 Resources con pantalla de Editar (Áreas, Médicos, Pacientes, Citas, Historia Clínicas, Facturas, Usuarios). Ver sección 8.3 para el detalle completo. **Confirmado funcionando por el usuario en el entorno real.**)

Última actualización anterior: 24 de agosto de 2026 (se creó el primer theme propio de Filament para el panel — antes no existía ninguno, se usaban los estilos por defecto — con un único ajuste de CSS: en todas las tablas del panel, incluido el widget "Citas de hoy" del Dashboard, el título y la barra de búsqueda/filtros ahora quedan en la misma fila en vez de dos filas apiladas, que es el comportamiento de fábrica de Filament. Al confirmarlo en el entorno real apareció un efecto secundario en la tabla de Citas — sus filtros rápidos dejaban un hueco vacío por usar un layout distinto al resto (`AboveContentCollapsible`) — corregido cambiándolos a `Dropdown` (mismo patrón que Usuarios), sin tocar la lógica de los filtros. **Ambos cambios confirmados funcionando por el usuario en el entorno real.** Ver sección 8.2 para el detalle completo, incluyendo por qué se descartó tanto dejarlo como estaba como forkear la plantilla Blade completa de la tabla).

Última actualización anterior: 23 de agosto de 2026 (nuevo logo — el usuario generó con IA una aproximación del cartel físico real de la clínica (triángulo con hoja/llama bicolor azul-turquesa y texto curvo) y se pidió rehacer esa referencia como vector limpio para el panel. Se reemplazó el monograma "CB" provisional por un ícono vectorial propio inspirado en esa imagen — triángulo con contorno navy y una hoja partida en azul/turquesa — y se aplicó el color primario Teal de Filament, ya coherente con los colores del logo nuevo. Antes de esto, con datos reales cargados, el usuario probó el branding con color turquesa/sidebar celeste y decidió que no convencía — se había revertido a los colores por defecto de Filament (ámbar/gris); ver sección 8.1 para el detalle completo de ambas vueltas. Se encontró y corrigió también un bug de fondo: la app nunca se configuró con idioma español ni con la zona horaria de Guayaquil — se quedó en los defaults de Laravel (inglés/UTC) desde el inicio del proyecto. Esto causaba fechas en inglés en las tablas y, más grave, que el widget "Citas de hoy" del Dashboard dejara de mostrar las citas del día a partir de las 19:00 hora local — ver sección 5 y 8.1 para el detalle).

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
      ItemInventario.php       # Con $fillable y métodos stockActual()/bajoStockMinimo() (sección 6.3)
      LoteInventario.php       # Con $fillable, relación item, y stockActual()/vencido()/porVencer() (sección 6.3)
      MovimientoInventario.php # Con $fillable y relaciones lote/usuario/paciente/cita (sección 6.3)
      Cama.php                 # Con $fillable, relación internamientos, y ocupada() derivado (sección 6.2)
      Quirofano.php            # Con $fillable y relación cirugias (sección 6.2)
      Internamiento.php        # Con $fillable y relaciones paciente/cama/medico/cita (sección 6.2)
      Cirugia.php              # Con $fillable, relaciones + medicosAdicionales (BelongsToMany con pivot) (sección 6.2)
      OrdenEstudio.php         # Con $fillable y relaciones paciente/medicoSolicitante/cita (sección 6.2)
      ServicioAmbulancia.php   # Con $fillable y relación paciente (sección 6.2)
    Http/
      Controllers/
        FacturaPdfController.php  # Genera el PDF de una factura (dompdf), reutiliza permisos de FacturaResource
    Filament/
      Resources/
        Areas/                   # Resource completo (Form, Table, Pages)
        Pacientes/               # Resource completo
        Medicos/                 # Resource completo, selector de Área por relación
        Citas/                   # Resource completo, selectores por relación + estado con colores + origen/prioridad (sección 6.2)
        HistoriaClinicas/        # Resource completo + vista de solo lectura (Infolist)
        Facturas/                # Resource completo, selectores por relación + estado con colores, exportar a PDF
        Users/                   # Resource completo, solo accesible por admin (gestión de usuarios/roles)
        ItemsInventario/         # Resource completo — catálogo de medicamentos/insumos (sección 6.3)
        LotesInventario/         # Resource completo — lotes con vencimiento, FEFO (sección 6.3)
        MovimientosInventario/   # Resource completo — entradas/salidas/traslados/ajustes (sección 6.3)
        Camas/                   # Resource completo — catálogo de camas, estado derivado (sección 6.2)
        Quirofanos/              # Resource completo — catálogo de quirófanos, estado editable (sección 6.2)
        Internamientos/          # Resource completo — ingreso/alta, filtrado "mis pacientes" para médico (sección 6.2)
        Cirugias/                # Resource completo — agenda de quirófano, médicos adicionales con Repeater (sección 6.2)
        OrdenesEstudio/          # Resource completo — laboratorio/imagenología, adjunto opcional (sección 6.2)
        ServiciosAmbulancia/     # Resource completo — traslados, el más simple del módulo (sección 6.2)
  database/
    migrations/
      ..._create_areas_table.php            # Completa (nombre)
      ..._create_pacientes_table.php        # Completa (datos personales + cedula unique)
      ..._create_medicos_table.php          # Completa (FK area_id)
      ..._create_citas_table.php            # Completa (FKs paciente/medico/area, horario, estado)
      ..._create_historia_clinicas_table.php # Completa (FKs paciente/medico/cita nullable)
      ..._create_facturas_table.php         # Completa (FKs paciente/cita nullable, monto, pago)
      ..._create_items_inventario_table.php       # Completa — catálogo medicamentos/insumos (sección 6.3)
      ..._create_lotes_inventario_table.php       # Completa — lotes con vencimiento (sección 6.3)
      ..._create_movimientos_inventario_table.php # Completa — ledger de movimientos (sección 6.3)
      ..._create_camas_table.php                  # Completa — camas hospitalización/UCI/UCIN (sección 6.2)
      ..._create_quirofanos_table.php              # Completa — quirófanos con estado editable (sección 6.2)
      ..._create_internamientos_table.php          # Completa — FKs paciente/cama/medico/cita, origen/prioridad (sección 6.2)
      ..._create_cirugias_table.php                # Completa — FKs paciente/quirofano/medico_principal/cita (sección 6.2)
      ..._create_cirugia_medico_table.php          # Completa — pivote médicos adicionales de una cirugía (sección 6.2)
      ..._create_ordenes_estudio_table.php         # Completa — laboratorio/imagenología/etc, adjunto opcional (sección 6.2)
      ..._create_servicios_ambulancia_table.php    # Completa — traslados (sección 6.2)
      ..._add_origen_prioridad_to_citas_table.php  # Alter — cubre "Emergencias" sin tabla propia (sección 6.2)
  resources/
    views/
      pdf/
        factura.blade.php       # Plantilla del comprobante de factura (CSS simple, para dompdf)
  public/
    images/
      logo.png                   # Logo real (vertical, ícono + texto), navy — de la marca oficial, extraído de PDF del cliente
      logo-white.png              # Misma versión, en blanco (para fondos oscuros)
      logo-horizontal.png         # Recomposición horizontal (ícono izq. + texto der.) navy — usada en el header del panel (2.5rem de alto)
      logo-horizontal-white.png   # Misma versión horizontal, en blanco
      _legacy/                    # Logo placeholder (SVG dibujado a mano) anterior a tener el logo real; se conserva por si acaso
    favicon.ico                 # Favicon regenerado desde el monograma real del logo
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

**Bug de fondo encontrado y corregido — idioma y zona horaria nunca se configuraron para Ecuador**: desde el inicio del proyecto, `config('app.locale')` y `config('app.timezone')` se quedaron en los defaults de Laravel (`en` / `UTC`), nunca se ajustaron a la clínica (español, Guayaquil = UTC-5). Dos síntomas causados por esto:
1. **Fechas en inglés**: las columnas con `->date()` (Citas, Facturas, Historia Clínica, Pacientes) mostraban el mes abreviado en inglés (ej. "Apr 12, 2024" en vez de "abr. 12, 2024"), confuso para alguien leyendo en español — reportado por el usuario como que "no aparece la fecha completa".
2. **Más grave — el widget "Citas de hoy" del Dashboard dejaba de mostrar las citas del día**: el widget filtra con el helper `today()` de Laravel, que calcula la fecha según la zona horaria de la app. Con la app fija en UTC y la clínica en Guayaquil (UTC-5), a partir de las 19:00 hora local en Guayaquil ya es el día siguiente en UTC — así que de esa hora en adelante, `today()` devolvía la fecha de mañana y las citas de hoy (con `fecha` = hoy en el sentido real/local) dejaban de matchear el filtro, quedando invisibles en el Dashboard justo en las horas de la tarde/noche, que es cuando más se usa el sistema.
- **Solución aplicada**: en `config/app.php`, `locale`/`fallback_locale`/`faker_locale` ahora usan `es`/`es`/`es_ES` por defecto (antes `en`/`en`/`en_US`), y `timezone` ahora es `env('APP_TIMEZONE', 'America/Guayaquil')` (antes hardcodeado en `'UTC'`, sin poder overridearse por `.env`). `.env.example` actualizado con `APP_LOCALE=es`, `APP_FALLBACK_LOCALE=es`, `APP_FAKER_LOCALE=es_ES` y la variable nueva `APP_TIMEZONE=America/Guayaquil`.
- **El `.env` real de cada entorno no se actualiza solo** (mismo caso que `APP_NAME`, ver sección 8.1) — si el `.env` real ya tiene `APP_LOCALE=en` (probable, Laravel lo escribe así al crear el proyecto) o no tiene `APP_TIMEZONE` en absoluto, hay que agregar/corregir esas líneas a mano ahí también, y después correr `./vendor/bin/sail artisan config:clear` para que tome el cambio (Laravel puede cachear la config).
- **Nota para el futuro**: si la clínica llegara a tener sucursales fuera de Guayaquil con otro huso horario, este valor fijo dejaría de ser válido para todas — no es el caso hoy (una sola ubicación, confirmado en sección 1).

## 6. Preguntas pendientes (por confirmar con el contacto interno / en la entrevista formal)

- [x] ¿Cuántas áreas/especialidades tiene la clínica? → **Respondido por el contacto interno (24 ago 2026), con material de marketing de la clínica (`Servicios_CB_2026.pdf`, compartido por el usuario).** La clínica es bastante más grande de lo asumido hasta ahora — funciona con lógica de clínica/hospital con quirófanos, UCI y hospitalización, no solo consultorio de especialidades. Detalle completo en la nueva sección **6.1**.
- [x] ¿Se agenda cita desde la web? → **No.**
- [ ] ¿El paciente ve precios/servicios publicados en la web, o se maneja solo internamente?
- [ ] ¿La cita se confirma automática o la aprueba recepción manualmente?
- [x] ¿Historias clínicas digitales desde el inicio, o fase 2? → **Aclarado parcialmente (24 ago 2026):** el propósito declarado del contacto interno es justamente "digitalizar la mayor parte del proceso historial clínico del paciente" — o sea, sí es objetivo del proyecto, no descartado. Pero el alcance real es gradual/por fases, no todo de una — ver sección 6.1 para el detalle de qué se digitaliza primero y qué queda para después.
- [ ] ¿Facturación electrónica con el SRI? ¿Manejan seguros médicos/reembolsos, con cuáles aseguradoras?
- [ ] ¿Cuántos médicos/usuarios van a usar el sistema, y qué roles necesitan?
- [ ] ¿Acceso remoto o solo desde la clínica?
- [ ] ¿Presupuesto definido o hay que proponerlo?
- [ ] ¿Plazo de entrega esperado?
- [ ] ¿Planean crecer (más sucursales) pronto?
- [ ] ¿Qué es exactamente lo que el amigo/contacto interno llamó "cuantificos" al describir cómo se maneja hoy la administración de la clínica? Término sin aclarar (posibles hipótesis sin confirmar: recibos/comprobantes en papel, un cuaderno de registro manual, cálculos de caja a mano) — no se puede saber a qué proceso actual corresponde ni si el sistema ya lo resuelve o falta cubrirlo, hasta preguntarle directamente a él o al dueño.
- [ ] **¿Los 3 roles actuales (admin/recepción/médico) alcanzan, o falta un 4º rol de farmacia?** Surgió al explicarle el sistema al usuario (24 ago 2026): hoy el rol **médico no tiene acceso a Ítems/Lotes/Movimientos de Inventario** (ni siquiera para ver), aunque es quien físicamente aplica el medicamento/insumo en la consulta — el registro de ese consumo depende de que recepción lo cargue por su cuenta. Es una decisión de negocio, no un bug: hay que decidir (a) si el médico debería poder registrar sus propios movimientos de inventario (ej. solo lo usado en su propia cita), y (b) si más adelante, cuando farmacia empiece a operar como un puesto dedicado (ver sección 6.3, punto sobre si "farmacia" es una entidad propia), conviene un rol **farmacéutico/bodega** separado con acceso a inventario pero sin ver historias clínicas ni facturación. Por ahora, con el volumen actual, 3 roles sigue pareciendo suficiente — no se ha tomado ninguna decisión ni se tocó código.
  **Parcialmente respondido (24 ago 2026, por WhatsApp con el contacto interno, fuera del entorno de código):** la clínica **sí tiene personal dedicado en farmacia** — esto respalda que probablemente conviene el rol `farmacéutico`/`bodega` separado. **Sigue sin confirmarse el mecanismo real**: no se sabe si el médico registra el consumo del insumo en el momento, si avisa después a farmacia, o si farmacia ya prepara el insumo antes de la consulta — sin esa respuesta no se puede diseñar bien el flujo prescripción → despacho → descuento de stock (`movimientos_inventario`, sección 6.3). Pregunta pendiente para la entrevista formal del 25 ago con Ysrael Calle (contabilidad, conoce el flujo operativo). Ver sección 6.5 para el detalle completo y el resto de preguntas preparadas para esa entrevista.

### 6.1 Áreas/especialidades reales y alcance por fases — respuesta del contacto interno (24 ago 2026)

**Especialidades (27, según el material de marketing `Servicios_CB_2026.pdf` — el texto de la imagen dice "más de 26" pero la lista enumerada trae 27 ítems)**: Auditoría Médica, Anestesiología y Terapia del Dolor, Cardiología, Cateterismo Cardiaco, Cirugía General y Digestiva, Cirugía Vascular, Cirugía Plástica, Cirugía Oncológica, Cirugía Pediátrica, Cirugía Holep de Próstata, Cirugía Torácica, Coloproctología, Cuidados Críticos, Endocrinología, Gastroenterología, Ginecología, Médico Ocupacional, Nutrición Clínica, Nutricionista, Neurología, Laparoscopía, Otorrinolaringología, Oncocirugía Traumatológica, Pediatría y Neonatología, Traumatología y Ortopedia, Terapia Intensiva, Urología.

**Servicios/infraestructura adicional** (mismo PDF, no son "áreas" en el sentido de especialidad médica, pero dan la magnitud real de la clínica): Hospitalización, UCI, Unidad de Endoscopía (alta y baja), Central de Quirófanos, Ambulancia, Laboratorio, Rayos X, Ecografía, UCIN, Cafetería, Emergencias, Centro de Imagen, Centro de Gastroenterología, Consulta Externa, Procedimientos Ambulatorios.

**Implicación clave**: el modelo de datos actual (`areas` como tabla simple con solo `nombre`, ver sección 4) ya soporta cargar estas 27 especialidades como filas sin tocar código — la decisión de diseño original ("no se sabe cuántas áreas tiene la clínica, mejor tabla aparte que campo fijo", sección 4) resultó acertada. **Resuelto**: se creó `database/seeders/AreaSeeder.php` con las 27 especialidades, registrado en `DatabaseSeeder.php` (corre con `db:seed` o solo con `--class=AreaSeeder`). Usa `firstOrCreate` para no duplicar si el entorno ya tenía áreas de prueba con el mismo nombre. **Confirmado funcionando por el usuario en el entorno real.**

**Alcance aclarado por fases (textual, resumido del contacto interno)**:
1. **Fase actual — registro de pacientes**: solo en **admisión**. O sea, el flujo de `Paciente`/`Cita` que ya existe en el sistema corresponde al proceso de admisión de la clínica — no se está pidiendo (todavía) que cada consultorio/área registre pacientes por su cuenta.
2. **Registro de medicamentos e insumos**: **no construido, y no es parte de lo ya hecho** — debe vivir en **farmacia, quirófano, admisión y facturación**. Esto es un módulo nuevo (inventario/insumos médicos, posiblemente con movimientos entre esas 4 áreas) que el sistema actual no cubre en absoluto — ni `Factura` ni ningún modelo actual modela insumos o medicamentos, es dominio nuevo.
3. **Para el próximo año (2027)**: planean "innovar los consultorios" (sin detalle aún de qué significa exactamente) y agregar un **sistema de registro de prescripciones** — las recetas que los médicos hacen a los pacientes. Tampoco existe hoy: `HistoriaClinica` tiene `diagnostico`/`tratamiento`/`notas` como texto libre, pero no hay un modelo estructurado de receta/prescripción (medicamento, dosis, frecuencia, duración).
4. **Propósito declarado, general**: "digitalizar la mayor parte del proceso de historial clínico del paciente" — confirma que el rumbo de historias clínicas digitales (ya construido en el sistema actual, aunque simple) va en la dirección correcta, pero el alcance completo es más amplio y gradual de lo que el sistema cubre hoy.

**Qué NO cambia por ahora**: nada de esto son pedidos de construir algo ya — es información de contexto/alcance para planificar. El sistema actual (Áreas/Médicos/Pacientes/Citas/HistoriaClinicas/Facturas, todo en admisión) sigue siendo válido como base y como "fase 1". Los módulos nuevos (farmacia/insumos, prescripciones estructuradas, lo que sea "innovar consultorios") son trabajo futuro, no reemplazo de lo ya hecho.

**Aún sin responder**: aunque ahora se sabe la lista de especialidades, sigue sin confirmarse si la clínica planea usar el sistema para *todas* esas 27 especialidades desde ya, o si el registro de citas/pacientes por especialidad se ampliará gradualmente. Tampoco se aclaró qué son exactamente "consultorios" en el contexto de "innovar los consultorios" (¿construcción física, digitalización de citas por consultorio, otra cosa?).

### 6.2 Infraestructura física — MÓDULO CONSTRUIDO (24 ago 2026)

**Estado**: **construido y en pruebas en el entorno real.** El usuario decidió avanzar con **supuestos razonables** sobre las 5 decisiones que seguían sin confirmar con la clínica (mismo criterio que se usó en 6.3), documentándolos abajo para poder ajustarlos después. Al probar **Cirugías** por primera vez apareció un bug (Repeater de médicos adicionales insertaba médicos vacíos en vez de asociar existentes — ver duodécima entrada del día arriba) — **ya corregido y confirmado funcionando**. Al probar **Órdenes de Estudio** aparecieron 2 bugs más en el adjunto (sin botones de ver/descargar, y `APP_URL` mal configurado en el entorno del usuario) — **ambos corregidos y confirmados funcionando**, ver decimotercera entrada del día arriba. El resto del módulo (Camas, Quirófanos, Internamientos, Servicios de Ambulancia) todavía no se ha probado uno por uno en el entorno real.

**Por qué no entra en el modelo actual de `areas`**: un `Area` hoy representa una especialidad médica (algo a lo que un `Medico` pertenece). Hospitalización, UCI, quirófanos, etc. no son especialidades — son **capacidad física/operativa** (camas, salas, equipos) que se ocupa y se libera con el tiempo. Necesitan su propio concepto de "ocupado/libre" y de "quién está usando qué, desde cuándo hasta cuándo", que `Area` no tiene ni debería tener.

**Estructura construida** (7 tablas nuevas + 2 columnas en `citas`, migraciones `2026_08_24_130000` a `2026_08_24_130007`):

1. **Camas — `camas`** (`app/Models/Cama.php`): `numero` (único), `tipo` (hospitalizacion/uci/ucin), `piso` (nullable). **No tiene columna `estado`** — `ocupada()` la deriva en vivo de si existe un `Internamiento` sin `fecha_alta`, mismo criterio que el stock del módulo de inventario (sección 6.3), para que nunca se desincronice de la realidad.

2. **Internamientos — `internamientos`** (`app/Models/Internamiento.php`): `paciente_id`, `cama_id`, `medico_id` responsable, `cita_id` (nullable), `fecha_ingreso`, `fecha_alta` (nullable — nulo mientras sigue internado), `motivo`, `origen` (programado/emergencia), `prioridad` (nullable, escala ESI), `notas`.

3. **Quirófanos — `quirofanos`** (`app/Models/Quirofano.php`): `numero` (único), `nombre` (nullable), `estado` — a diferencia de `camas`, este sí es una columna guardada y editable, con 4 pasos (preparación → en cirugía → limpieza → libre) en vez de solo libre/ocupado, tal como se validó con la investigación externa de la entrada anterior.

4. **Cirugías — `cirugias`** (`app/Models/Cirugia.php`): `paciente_id`, `quirofano_id`, `medico_principal_id` (cirujano responsable), `cita_id` (nullable), `fecha`, `hora_inicio`, `hora_fin` (nullable), `tipo_cirugia`, `estado` (programada/en_curso/completada/cancelada), `notas`. Médicos adicionales (anestesiólogo, ayudantes) se modelan en una tabla pivote aparte — ver siguiente punto.

5. **Médicos adicionales de una cirugía — `cirugia_medico`** (pivote, gestionado con un `Repeater` en el formulario de Cirugía, sin Resource propio): `cirugia_id`, `medico_id`, `rol` (texto libre, ej. "Anestesiólogo"). Resuelve el punto abierto de la propuesta original: una cirugía suele involucrar más de un médico, a diferencia de `Cita` que asume uno solo.

6. **Órdenes de estudio — `ordenes_estudio`** (`app/Models/OrdenEstudio.php`): modelo unificado para Laboratorio, Rayos X, Ecografía, Centro de Imagen, Endoscopía (alta/baja), Gastroenterología y Procedimientos Ambulatorios, en vez de una tabla por tipo. Campos: `paciente_id`, `medico_solicitante_id`, `cita_id` (nullable), `tipo`, `fecha_solicitud`, `fecha_realizacion` (nullable), `estado` (solicitado/en_proceso/completado), `resultado_texto`, `resultado_archivo` (path, nullable), `notas`.

7. **Servicios de ambulancia — `servicios_ambulancia`** (`app/Models/ServicioAmbulancia.php`): la tabla más simple del módulo — `paciente_id` (nullable), `origen`, `destino`, `fecha_hora`, `motivo`, `notas`.

8. **`citas.origen` / `citas.prioridad`** (columnas nuevas en la tabla ya existente, no una tabla nueva): cubren "Emergencias" — una emergencia que no requiere internamiento queda registrada como una Cita normal con `origen = emergencia` y su `prioridad` ESI, en vez de necesitar un modelo propio. Se agregaron también a `CitaForm`/`CitasTable` (selector condicional de prioridad solo visible si origen es emergencia, badge rojo en la tabla, filtro rápido "Emergencias").

**6 Resources completos de Filament** (mismo patrón de carpetas que los módulos existentes), todos agrupados bajo el grupo de navegación "Infraestructura": `Camas`, `Quirofanos`, `Internamientos`, `Cirugias`, `OrdenesEstudio`, `ServiciosAmbulancia`.

**Permisos aplicados**:
- Camas, Quirófanos (catálogo de recurso físico, mismo criterio que Áreas/Médicos): cualquier usuario logueado ve; admin y recepción editan (recepción necesita asignar camas/quirófanos en el día a día); solo admin crea/borra.
- Internamientos, Cirugías (operación clínica, mismo criterio que Citas): admin y recepción crean/administran; cualquier usuario logueado edita, pero un médico vinculado (`users.medico_id`) solo ve/edita los suyos (filtrado por `medico_id`/`medico_principal_id`, igual patrón que `CitaResource::getEloquentQuery()`); solo admin borra.
- Órdenes de estudio: igual que arriba, pero el médico **también puede crear** (es quien solicita el estudio), filtrado por `medico_solicitante_id`.
- Servicios de ambulancia: admin y recepción crean/editan/borran (admin); médico solo ve (no participa directamente del transporte).

**Las 5 decisiones que estaban pendientes — resueltas con supuestos razonables, editables después**:

| Decisión pendiente | Supuesto aplicado | Qué implica si la clínica responde distinto |
|---|---|---|
| ¿Estado de camas/quirófanos en tiempo real o histórico alcanza? | **Tiempo real** — camas derivado en vivo (`ocupada()`), quirófanos con columna editable de estado granular. | Ya cubierto en ambos escenarios, igual criterio que el stock de 6.3. |
| ¿Una cirugía siempre nace de una `Cita` ya agendada? | **No** — `cita_id` nullable, una cirugía puede agendarse directo, igual que `Factura`/`HistoriaClinica` ya tratan `cita_id` como opcional. | Si la clínica confirma que siempre pasa por Citas, se podría hacer `cita_id` obligatorio más adelante sin romper lo ya guardado (solo agregaría una validación). |
| ¿Resultados de estudios con archivo adjunto desde el día uno? | **Sí** — `FileUpload` opcional en `OrdenEstudioForm`, disco local de Sail (`disk('public')`), además del texto libre. No se evaluó storage externo tipo S3. | Migrar a S3 más adelante es un cambio de configuración del disco, no de la columna (`resultado_archivo` solo guarda la ruta). |
| ¿"Emergencias" necesita triage/prioridad, no solo `origen`? | **Sí** — se implementó `prioridad` con escala ESI (5 niveles) en `citas` e `internamientos`, además de `origen`, tal como se validó con la investigación externa de la entrada anterior. | N/A, ya resuelta con la investigación previa. |
| ¿Más de un quirófano/UCI si la clínica crece a otra sede? | **No se modela sede/sucursal todavía** — el sistema sigue asumiendo una sola ubicación (igual que el resto del proyecto, ver sección 5 sobre timezone). | Si la clínica confirma expansión, hace falta agregar una tabla `sedes` y `sede_id` en `camas`/`quirofanos` (y probablemente en `areas`) — cambio más grande, no solo de este módulo. |

**Qué NO se hizo todavía**:
- No se corrió la migración ni se probó en el entorno real (a diferencia de 6.3, que sí se confirmó en vivo) — falta que el usuario corra `sail artisan migrate` y pruebe el flujo completo.
- No se cargó ningún dato real (camas/quirófanos existentes de la clínica) — no hay seeder, la clínica no ha dado esa información.
- No se conectó con el módulo de inventario (sección 6.3) — la `MovimientoInventario` sigue sin `cirugia_id`, tal como quedó documentado como pendiente en 6.3 punto 6 de la tabla de decisiones. Se puede agregar sin romper nada de lo actual.
- No se validó a nivel de formulario que una cama/quirófano esté libre antes de asignarla a un internamiento/cirugía nuevo — el formulario solo muestra un mensaje de ayuda pidiendo verificar en el listado correspondiente. Si la clínica confirma que hace falta bloquear la selección de recursos ocupados, es un ajuste puntual en `InternamientoForm`/`CirugiaForm`.
- No se filtra por médicos adicionales de una cirugía (`medicosAdicionales`) al mostrar la lista a un médico logueado — solo ve las cirugías donde es el responsable principal, no las que le asignaron como anestesiólogo/ayudante. Simplificación documentada en `CirugiaResource::getEloquentQuery()`.

**Validación externa (24 ago 2026)**: se investigó cómo modelan esto sistemas hospitalarios reales y estándares del sector (HL7 FHIR, sistemas open source como OpenMRS/OpenHospital/Bahmni, literatura de gestión hospitalaria). Conclusión general: la estructura propuesta arriba (separar "ubicación/recurso físico" de "ocupación en el tiempo", que es la base de los puntos 1 y 2) coincide con el patrón estándar de la industria, conocido como **ADT** (Admission-Discharge-Transfer) — el módulo que virtualmente todo sistema hospitalario tiene para esto. No hay que rediseñar nada, solo se incorporaron los 2 ajustes marcados arriba (estado de quirófano más granular, prioridad/triage en emergencias).

### 6.3 Medicamentos e insumos — MÓDULO CONSTRUIDO Y CONFIRMADO (24 ago 2026)

**Estado**: **construido y confirmado funcionando por el usuario en el entorno real** (probado en vivo: creación de ítem, lote, movimientos de entrada y salida con stock recalculado correctamente, protección contra borrado y permisos por rol). El usuario decidió empezar por este módulo (en vez de la infraestructura física de la 6.2) y pidió avanzar con **supuestos razonables** sobre las decisiones que seguían sin confirmar con la clínica, documentándolos para poder ajustarlos después en vez de bloquear el desarrollo esperando esa confirmación.

**Encargo explícito del usuario (24 ago 2026, misma sesión)**: pidió avanzar con una propuesta de modelado para el módulo de medicamentos/insumos que el contacto interno mencionó en la sección 6.1 ("debe vivir en farmacia, quirófano, admisión y facturación"). Igual que la sección 6.2, es **solo para planificar** — no se tocó ningún modelo, migración, Resource ni seeder.

**Punto de partida — a diferencia de la infraestructura de la sección 6.2, acá no hay nada construido ni cargado todavía**: ni una tabla, ni un campo, ni un seeder. `Factura` solo modela `monto`/`estado_pago`, no ítems ni insumos. Es dominio nuevo desde cero.

**Por qué no entra en ningún modelo actual**: lo que describe el contacto interno mezcla dos cosas distintas que conviene separar desde el diseño:
1. Un **catálogo** de qué medicamentos/insumos existen y cuánto stock hay (inventario).
2. Un **movimiento** de esos ítems entre las 4 áreas que mencionó (farmacia, quirófano, admisión, facturación) — entradas, salidas, traslados y consumo real en la atención de un paciente.

**Estructura construida** (3 tablas nuevas, migraciones `2026_08_24_120000` a `2026_08_24_120002`):

1. **Catálogo — `items_inventario`** (`app/Models/ItemInventario.php`): `nombre`, `tipo` (medicamento/insumo), `unidad_medida`, `stock_minimo` (nullable, para alertas), `precio_unitario` (nullable, si se factura). Un solo catálogo para ambos tipos en vez de dos tablas separadas. **No tiene columna `stock_actual`** — se calcula en vivo con `ItemInventario::stockActual()`, sumando el `stockActual()` de todos sus lotes, para que nunca se desincronice de la realidad (mismo criterio que el estado de una cama en 6.2). `bajoStockMinimo()` compara ese valor contra `stock_minimo`.

2. **Lotes — `lotes_inventario`** (`app/Models/LoteInventario.php`): `item_id` FK, `numero_lote`, `fecha_vencimiento`. Trazabilidad FEFO (First-Expired-First-Out) implementada tal como se validó con la investigación externa — cada lote es una unidad separada con su propio vencimiento. Tampoco tiene columna de cantidad: `LoteInventario::stockActual()` la calcula sumando sus movimientos (entrada + ajuste, menos salida). `vencido()` y `porVencer($dias = 90)` para la lógica de color en la tabla del panel.

3. **Movimientos — `movimientos_inventario`** (`app/Models/MovimientoInventario.php`, el ledger/fuente de verdad): `lote_id` FK, `tipo_movimiento` (entrada/salida/traslado/ajuste), `cantidad`, `area_origen`/`area_destino` (nullable, texto de una lista fija), `fecha_hora`, `user_id` (quién lo registró — se asigna solo, no se pide en el formulario), `paciente_id`/`cita_id` (nullable, para dejar registrado consumo real en la atención de alguien), `notas`. Toda entrada de stock —incluida la carga inicial de un lote— se registra como un movimiento de tipo "entrada", nunca como una columna editable a mano.

**3 Resources completos de Filament** (mismo patrón de carpetas que los 7 existentes): `ItemsInventario`, `LotesInventario`, `MovimientosInventario`. Formulario de movimientos con campos "Área de origen"/"Área de destino" condicionales según `tipo_movimiento` (`->live()` + `->visible()`). Protección contra borrado: un ítem no se borra si tiene lotes, un lote no se borra si tiene movimientos (mismo patrón que Área/Médico/Paciente/Cita).

**Permisos aplicados** (no existe rol "farmacia" separado — el sistema solo tiene admin/recepcion/medico):
- Catálogo (Ítems, Lotes): mismo criterio que Áreas/Médicos — admin todo, recepción solo ver, médico sin acceso.
- Movimientos: mismo criterio que Facturas — admin y recepción todo, médico sin acceso.
- **Queda abierto** si la clínica confirma que necesita un rol propio de farmacia; hoy cualquier persona con rol `recepcion` puede registrar movimientos.

**Las 6 decisiones que estaban pendientes — resueltas con supuestos razonables, editables después** (a diferencia de la 6.2, aquí sí se avanzó a construir sin esperar la confirmación de la clínica, a pedido explícito del usuario):

| Decisión pendiente | Supuesto aplicado | Qué implica si la clínica responde distinto |
|---|---|---|
| ¿Farmacia es área física propia o solo un paso lógico? | Solo un paso lógico — texto libre (`area_origen`/`area_destino`, lista fija: farmacia/quirófano/admisión/facturación/bodega) dentro del movimiento, sin Resource propio. | Si la clínica confirma que necesita responsable/horario propio por área, habría que crear una tabla `areas_inventario` (o reusar el concepto) y convertir esos campos en relaciones. |
| ¿Stock en tiempo real o histórico alcanza? | **Tiempo real** — se implementó calculado en vivo desde los movimientos (`stockActual()`), nunca una columna editable a mano. | Ya cubierto en ambos escenarios: si solo hiciera falta histórico, este diseño lo sigue dando gratis (es más, no menos, de lo mínimo necesario). |
| ¿Se cobra todo insumo consumido al paciente? | **No se conectó con `Factura` todavía** — `paciente_id`/`cita_id` en el movimiento solo deja constancia de qué se usó, no genera cobro automático. | Si la clínica confirma que sí se cobra, falta construir la tabla intermedia `factura_items` que ya proponía la 6.3 original (ver más abajo, en "Qué falta"). |
| ¿Hace falta registrar proveedores/compras externas? | **No** — un movimiento "entrada" solo registra que llegó stock, sin detalle de quién lo vendió. | Se puede agregar una tabla `proveedores` + FK opcional en el movimiento más adelante, sin romper lo ya construido. |
| ¿Trazabilidad por lote/vencimiento? | **Sí** (ya no era pregunta abierta desde la investigación externa) — implementado tal cual, ver `lotes_inventario` arriba. | N/A, ya resuelta. |
| ¿Depende de la infraestructura de 6.2 (quirófanos/cirugías)? | **No, se construyó independiente.** El movimiento solo referencia `cita_id` (ya existe), no `cirugia_id` (esa tabla no existe todavía). | Cuando se construya la 6.2, se puede agregar `cirugia_id` nullable al movimiento sin romper nada de lo actual. |

**Relación con Facturación — todavía no construida**: sigue siendo la propuesta original (tabla intermedia `factura_items`: factura_id, item_id, cantidad, precio_unitario), para no mezclar "qué se usó clínicamente" con "qué se cobró". No se construyó en esta pasada — es lo próximo si la clínica confirma que sí se factura el consumo de insumos.

**Qué NO se hizo todavía**:
- No se cargó ningún ítem/catálogo real (la clínica no ha dado esa lista) — no hay seeder, el módulo está construido y probado, pero vacío de datos reales.
- No se conectó con `Factura` (ver arriba).
- No se agregó ningún rol nuevo (`farmacia`) — se usan los 3 roles existentes con el criterio explicado arriba.

**Validación externa (24 ago 2026)**: la separación catálogo/movimientos con stock derivado (nunca editado a mano) coincide con cómo lo resuelven sistemas de farmacia hospitalaria reales — es prácticamente unánime en las fuentes consultadas. Se incorporaron los 2 ajustes marcados arriba (lotes/vencimiento con FEFO, y separar prescrito/dispensado/administrado — este último **no se implementó todavía**, sigue siendo relevante para cuando se conecte con el futuro módulo de prescripciones de 2027, ver 6.1). Ver también la sección 6.4 sobre el marco legal de datos de salud, relevante para este módulo si en algún momento maneja datos de pacientes.

### 6.4 Marco legal ecuatoriano aplicable (contexto nuevo, investigación externa, 24 ago 2026)

**Por qué se agrega esta sección**: al investigar cómo modelan otros sistemas los módulos de 6.2/6.3, apareció contexto legal específico de Ecuador que no estaba documentado en ningún lado del proyecto hasta ahora, y que aplica no solo a los módulos futuros sino, en menor medida, a lo ya construido (Pacientes, HistoriaClinicas). Es información de contexto, no implica ningún cambio de código inmediato.

**Ley Orgánica de Protección de Datos Personales (LOPDP)**: vigente en Ecuador desde el 26 de mayo de 2021, con reglamento general desde noviembre de 2023. Clasifica explícitamente los **datos de salud como "dato sensible"** (junto con datos biométricos, genéticos, etc.), con reglas más estrictas que datos personales comunes — en particular, el tratamiento de datos sensibles requiere en general **consentimiento explícito del titular**, salvo excepciones (la ley sí prevé que instituciones y profesionales de salud puedan tratar datos de salud de sus propios pacientes bajo tratamiento, sin necesitar ese consentimiento caso por caso). También establece el principio de "protección de datos desde el diseño" — pensar en seguridad/minimización desde que se diseña un sistema, no agregarlo después.

**Normativa del Ministerio de Salud Pública (MSP) sobre historia clínica electrónica**: existen Acuerdos Ministeriales (1190-2012, 0009-2017 y un reglamento específico de 2017) que disponen el uso del estándar internacional **HL7** para historia clínica electrónica, obligatorios para instituciones de salud **tanto públicas como privadas** en Ecuador, y definen un conjunto de datos mínimos estandarizados para la "Historia Clínica Única".

**Qué implica esto para el proyecto (sin ser una lista de tareas urgentes, solo contexto a tener presente)**:
- No cambia nada de lo ya construido hoy — el sistema actual (login, roles, permisos por recurso, protección contra borrado, etc.) ya va en la línea correcta de cuidar el acceso a datos sensibles, aunque no fue diseñado citando esta ley explícitamente.
- Es relevante sobre todo **de cara al futuro**: cuando se aborde el módulo de prescripciones (2027, sección 6.1) o cualquier ampliación de `HistoriaClinica`, conviene tener en mente el principio de "protección desde el diseño" (quién puede ver qué, por qué, y durante cuánto tiempo se conserva la información).
- El estándar HL7 mencionado por el MSP es la misma familia de estándares (HL7/FHIR) que se usó como referencia para validar las propuestas de 6.2 y 6.3 — no es una coincidencia forzada, es el estándar de facto del sector tanto a nivel internacional como en la normativa local.
- **No hay ninguna acción pendiente inmediata por esto** — se documenta como contexto para que, si en algún momento la clínica pregunta por cumplimiento normativo o el dueño lo plantea en la entrevista formal (sección 1, aún pendiente), ya haya un punto de partida investigado en vez de partir de cero.

### 6.5 Preparación entrevista de seguimiento (24-25 ago 2026) — planificación, sin tocar código

**Contexto**: sesión aparte, en paralelo a la que construyó 6.2/6.3 y el resto de cambios de UX (sección 8). Sirvió solo para preparar la entrevista formal del **25 ago 2026, después de las 9am, con Ysrael Calle** (contabilidad, pero conoce el flujo operativo) — no generó ningún cambio de código hasta que el usuario lo pidió explícitamente. El detalle completo de la conversación queda en `MEMORIA_SESION_ENTREVISTA_2026-08-24.md` y `PLAN_MODULOS.md` (archivos de trabajo, no versionados en el repo salvo lo que se resume acá).

**Expediente clínico completo — alcance confirmado por el cliente**: al preguntarle directamente si "digitalizar la mayor parte del historial clínico" significa un expediente completo (antecedentes, alergias, signos vitales, resultados de exámenes, todo conectado), **confirmó que sí**. Esto amplía lo que hoy hace `HistoriaClinica` (solo `diagnostico`/`tratamiento`/`notas` en texto libre, ver sección 4).

**Reconciliado contra lo ya construido en 6.2/6.3 (importante — evita duplicar trabajo)**:
- **Resultados de exámenes con archivo adjunto**: **ya cubierto**, es exactamente lo que hace `OrdenEstudio` (sección 6.2) — no hace falta ningún módulo nuevo para esto.
- **Antecedentes, alergias, signos vitales**: **no existen todavía**, ni como tabla ni como campo — son los 3 módulos nuevos reales que hacen falta para completar el expediente.

**Los 3 módulos pendientes, con diseño ya pensado (no implementado)**:
1. **Alergias**: por paciente (no por consulta), tipo (medicamento/alimento/otro) + severidad. Debe verse destacado en la ficha del paciente y en Historia Clínica, no como texto libre escondido — es la razón de separarlo en su propia tabla.
2. **Antecedentes**: por paciente, categorizado (personal/quirúrgico/familiar/hábito), más grupo sanguíneo.
3. **Signos vitales**: por consulta (vinculado a `HistoriaClinica`), presión arterial/temperatura/frecuencia cardíaca/frecuencia respiratoria/peso/talla/saturación de oxígeno.

Orden sugerido para construirlos (por seguridad del paciente primero, y porque alergias/antecedentes son más simples que signos vitales al ser "por paciente" en vez de "por consulta"): alergias → antecedentes → signos vitales.

**Requerimiento del cliente sobre inventario — matiz nuevo, no resuelto en 6.3**: el cliente pidió que el registro de insumos cubra **farmacia, quirófano, admisión y facturación** — 4 puntos, no solo farmacia. El módulo de 6.3 ya soporta esto a nivel de dato (`area_origen`/`area_destino` como texto libre con esa lista fija, ver 6.3 punto 1 de la tabla de decisiones), pero no está confirmado si debe ser un inventario compartido entre las 4 áreas o si cada una maneja el suyo, ni qué hace específicamente cada área con ese registro — depende de la misma pregunta pendiente sobre el mecanismo real de farmacia (ver ítem sin resolver en la sección 6, arriba).

**Prescripciones (2027)**: sigue sin construirse (ya documentado en 6.1). Nuevo detalle: falta confirmar si el médico prescribe solo lo que existe en `items_inventario`, o también medicamentos que el paciente compra afuera de la clínica — define si la prescripción se vincula o no al inventario de 6.3.

**Checklist completo preparado para la entrevista del 25/08** (documento Word entregado al usuario, no versionado en el repo):
- Mecanismo real de farmacia (pregunta nueva, prioridad alta — ver arriba).
- Prioridad dentro del expediente clínico: ¿qué construir primero de los 3 módulos pendientes?
- Qué significa "innovar los consultorios" (2027).
- Mecánica de prescripciones: ¿solo inventario interno, o también medicamentos externos?
- El resto de preguntas de negocio ya documentadas en la sección 6 principal (precios web, confirmación de citas, facturación SRI/seguros, cantidad de usuarios, "cuantificos", acceso remoto, presupuesto, plazos, sucursales futuras) — sin cambios, siguen abiertas.

**Puntos detectados en esta planificación que ni siquiera tienen pregunta preparada todavía** (quedan para una vuelta futura, después de que se resuelvan las preguntas de arriba):
- Si algún examen (`OrdenEstudio`) consume insumos del inventario (agujas, reactivos) y si eso debería descontar stock automáticamente.
- Permisos/roles para los 3 módulos nuevos (alergias, antecedentes, signos vitales) — falta extender la matriz de la sección 10 cuando se construyan.
- Si un antecedente/alergia corregido debe conservar historial de cambios o simplemente editarse.

**Qué NO se hizo en esta sesión de planificación**: no se tocó ningún modelo, migración, Resource ni seeder — es 100% preparación para la entrevista, a pedido explícito del usuario de no interferir con el patch grande que se estaba aplicando en otra sesión en paralelo.

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
- [x] ~~**Gestión de usuarios desde el panel** (`/admin/users`)~~ — resuelto y confirmado funcionando por el usuario en el entorno real (ver sección 9 y 10 para el detalle). Incluye la protección contra que un admin se elimine a sí mismo y el filtro rápido por rol en la tabla, ambos confirmados.
- [x] ~~**Exportar Facturas a PDF** (botón "Exportar PDF")~~ — resuelto y confirmado funcionando por el usuario en el entorno real, incluyendo que un rol sin permiso (médico) recibe 403 al intentar la ruta directa (ver sección 9 para el detalle).
- [x] ~~**Branding del panel** (nombre, colores, logo, favicon)~~ — resuelto y **confirmado funcionando por el usuario en el entorno real** (logo, nombre "Clínica Benites" y color turquesa OK). Se intentó un ajuste extra con sidebar oscuro que se complicó en la prueba real (texto invisible, no se pudo depurar sin acceso a inspeccionar el DOM en vivo) y se descartó a pedido del usuario — ver sección 8.1 para el detalle completo. Quedó simplificado a un tinte celeste claro de fondo en el sidebar, sin tocar el color del texto (se usa el gris default de Filament). **Pendiente confirmar en el entorno real que esta versión simplificada se ve bien.**
- [x] ~~**Filtrar "mis pacientes" para el rol médico**~~ — **resuelto y confirmado funcionando por el usuario en el entorno real** (ver sección 10 para el detalle), incluyendo el fix del bug de `medico_id` al cambiar el rol de un usuario.
- [x] ~~**Locale español + timezone Guayaquil**~~ (fechas en inglés y Dashboard sin citas de hoy después de las 19:00) — resuelto, ver sección 5 para el detalle. **Pendiente confirmar en el entorno real** — recordar actualizar el `.env` real (no se actualiza solo) y correr `config:clear`.
- [x] ~~**Botón "Cancelar" → "Atrás" en las pantallas de Editar**~~ — resuelto y **confirmado funcionando por el usuario en el entorno real**, ver sección 8.3 para el detalle.

## 8. Plan para la próxima sesión — pulir UX del sistema interno

El sistema ya es funcional de punta a punta (CRUD + roles). Lo que sigue es hacerlo **más rápido de usar en el día a día** para recepción/médicos. Investigado contra buenas prácticas de software de agendamiento clínico — priorizado de más a menos impacto:

1. [x] ~~**Dashboard con "citas de hoy" al entrar**~~ (⭐ mayor impacto) — **resuelto**. Se creó `app/Filament/Widgets/CitasDeHoyWidget.php` (extiende `Filament\Widgets\TableWidget`), que filtra `Cita::whereDate('fecha', today())` ordenado por `hora_inicio`, con columnas Hora/Paciente/Médico/Área/Estado (mismo badge de colores que la tabla de Citas) y una `EditAction` que respeta `CitaResource::canEdit()`. Se registra solo (`discoverWidgets` ya apuntaba a esa carpeta). Se quitaron del `AdminPanelProvider` los widgets genéricos `AccountWidget` y `FilamentInfoWidget` ("Welcome"/"filament"), así el widget de citas queda como lo primero que se ve al entrar a `/admin`. **No se filtró por médico logueado**: se mantiene la limitación conocida (`users` y `medicos` no están conectados, ver sección 10) — todos los roles ven todas las citas de hoy por ahora.

2. [x] ~~**Cambiar estado de una cita con un clic, sin abrir el formulario completo**~~ — **resuelto**. En `app/Filament/Resources/Citas/Tables/CitasTable.php` se agregó un `ActionGroup` "Cambiar estado" (icono de refresh) antes del botón Editar, con un botón por cada estado válido (pendiente/confirmada/atendida/cancelada). Cada botón se oculta si la cita ya está en ese estado, actualiza directo con `$record->update(['estado' => $estado])` (sin navegar a otra página) y muestra una notificación de éxito. Los colores de cada botón coinciden con los del badge de la columna Estado (se extrajo a un helper `colorEstado()` compartido para no duplicar el `match`). Todo el grupo respeta `CitaResource::canEdit($record)`, igual que el botón Editar. **No se aplicó todavía al widget de "Citas de hoy" del Dashboard** (punto 1) — ese widget por ahora solo tiene el botón Editar; sería una mejora natural para una próxima pasada si se quiere el mismo flujo rápido desde el Dashboard.

3. [x] ~~**Crear paciente nuevo sin salir del formulario de Cita**~~ — **resuelto**. En `app/Filament/Resources/Citas/Schemas/CitaForm.php` se agregó `->createOptionForm([...])` al selector de `paciente_id`, con los mismos campos que `PacienteForm` (nombres, apellidos, cédula, fecha de nacimiento, teléfono, email, dirección, sexo). Ahora aparece un botón "+" junto al selector que abre un modal para crear el paciente sin perder los datos ya cargados en el formulario de la cita. Se agregó validación `->unique(table: 'pacientes', column: 'cedula')` al campo cédula del modal (el `PacienteForm` original no la tenía — solo la restricción de la base de datos — así que sin esto el modal habría mostrado el error crudo de MySQL en vez de un mensaje claro si se repetía una cédula). De paso se mejoró cómo se ve el selector de paciente: antes solo mostraba `nombres`, ahora muestra "Nombres Apellidos" (vía `getOptionLabelFromRecordUsing`) y se puede buscar también por apellido o cédula — útil para poder diferenciar pacientes con el mismo nombre de pila, cosa que se vuelve más común ahora que se crean pacientes rápido desde acá.

4. [x] ~~**Filtros rápidos en la lista de Citas**~~ ("Hoy", "Pendientes", "Confirmadas") — **resuelto**. En `CitasTable.php` se agregaron 3 `Filter` con `->toggle()` (checkbox tipo switch en vez de checkbox normal): "Hoy" (`whereDate('fecha', today())`), "Pendientes" y "Confirmadas" (`where('estado', ...)`). No se agregó filtro para "Atendida"/"Cancelada" porque el plan solo pedía esos 3 (los más usados en el día a día); si hace falta, es trivial copiar el patrón. **Layout cambiado de `AboveContentCollapsible` a `Dropdown` (default) el 24 de agosto de 2026** — ver sección 8.2 para el detalle: con el layout de encabezado unificado (título+buscador en una fila, ver arriba), el bloque de filtros colapsado dejaba un hueco vacío feo entre el buscador y el ícono de filtro; se optó por el mismo patrón que ya se había adoptado en Usuarios (dropdown junto al buscador) para que también quepa todo en la misma fila.

5. [x] ~~**Buscador global mejorado**~~ — **resuelto y confirmado funcionando por el usuario en el entorno real**. `Área` ya tenía buscador global habilitado por defecto (Filament lo activa solo con `$recordTitleAttribute`), pero `Médico`/`Paciente` solo buscaban por un campo (`nombres`), y `Cita`/`HistoriaClinica`/`Factura` no tenían buscador global en absoluto (no tienen un campo de texto único, así que nunca se les puso `$recordTitleAttribute`). Se agregó `getGloballySearchableAttributes()` a los 5 Resources restantes, con "dot notation" para buscar dentro de relaciones donde hace falta (ej. `Cita` ahora se encuentra buscando por nombre/apellido/cédula del paciente o del médico, o por texto en las notas). Se agregó `getGlobalSearchResultTitle()` para mostrar un título compuesto y legible (ej. "Cita — Juan Pérez" en vez del valor crudo de `fecha`) y `getGlobalSearchResultDetails()` para mostrar contexto extra bajo el título (médico, área, fecha/hora, estado, monto, etc., según el recurso). Se agregó `getGlobalSearchEloquentQuery()` con eager loading (`->with([...])`) en los recursos que muestran datos de relaciones en el título/detalles, para no generar N+1 en cada resultado. Los permisos no requirieron cambios: Filament ya excluye del buscador global cualquier recurso donde `canViewAny()` sea `false` para el usuario actual. **Probado en el entorno real**: búsqueda por "ju" devolvió correctamente las categorías "Citas" y "Pacientes" con Julio Jaramillo, con título compuesto y detalles como se diseñó.

**Explícitamente descartado por ahora** (para no abrumar al personal antes de que domine lo básico): recordatorios automáticos por WhatsApp/SMS, portal de autoagendamiento para pacientes. Quedan para una fase futura, después de validar que recepción/médicos ya están cómodos con el sistema base.

**Estado**: los 5 puntos del plan original de mejoras de UX están resueltos (Dashboard, cambiar estado con un clic, crear paciente desde el formulario de Cita, filtros rápidos, buscador global). Puntos 1, 2, 3 y 5 confirmados funcionando por el usuario en el entorno real; el punto 4 (filtros rápidos) sigue pendiente de esa confirmación (ver sección 7).

### 8.1 Branding del panel — resuelto (falta confirmar en entorno real)

A pedido del usuario, se personalizó el panel de Filament (antes con los defaults genéricos de Filament: nombre "Laravel", logo de texto, color ámbar):

- **Nombre**: `->brandName('Clínica Benites')` en `AdminPanelProvider`. Además, `APP_NAME` en `.env.example` se cambió de `Laravel` a `"Clínica Benites"` (afecta el título de la pestaña del navegador y el remitente de los correos vía `config('app.name')`). **El `.env` real de cada entorno no se actualiza solo** — hay que cambiarlo a mano ahí también, ya que no está versionado.
- **Colores**: primario cambiado de `Color::Amber` (default) a `Color::Cyan` (turquesa) — elegido por el usuario para que combine con los colores reales del cartel/fachada de la clínica (confirmado con fotos de Google Street View que el usuario compartió: el logo físico de la clínica usa un triángulo turquesa con un ícono adentro, y también existe una variante con monograma cuadrado azul, usada en distintos carteles del local). El color neutro (`gray`) también se cambió de `Color::Gray` (default) a `Color::Slate`, que tiene un tinte levemente azulado y combina mejor con el turquesa que el gris neutro por defecto.
- **Sidebar oscuro — descartado, simplificado a tinte celeste claro**: se intentó un sidebar con fondo turquesa oscuro sólido (patrón Linear/Vercel) con texto forzado a blanco. En la prueba real (`/admin/facturas`, luego `/admin/areas`) el ítem de menú activo se veía con texto blanco sobre un fondo que no cambiaba de blanco — invisible. Se investigó la causa contra el código fuente real de Filament v5.7.6 (`item.blade.php`): el `<li>` sí lleva las clases `fi-sidebar-item fi-active` como se esperaba, así que un segundo intento apuntando el CSS directamente a ese `<li>` en teoría debía funcionar — pero el usuario reportó que seguía sin verse el cambio, y sin acceso a inspeccionar el DOM real en vivo (herramientas de desarrollador del navegador) no se pudo confirmar si era un problema de cache del navegador/Livewire (`wire:navigate` no siempre recarga el `<head>` en navegación interna) o de especificidad CSS. Ante la complicación, el usuario pidió simplificar: se descartó el sidebar oscuro y todo el forzado de color de texto (blanco en hover/activo, etc.).
- **Solución actual, mucho más simple y robusta**: el sidebar usa un tinte celeste suave de fondo (`color-mix(in srgb, var(--primary-500) 8%, white)`, mismo mecanismo que ya se usaba en la versión anterior a la del sidebar oscuro) y **no se toca el color del texto en absoluto** — se deja el gris que Filament trae por defecto, que al ser sobre un fondo claro contrasta bien sin necesidad de adivinar ni encadenar clases internas del paquete (`fi-sidebar-item-label`, `fi-sidebar-item-btn`, etc.) que no se pudieron confirmar contra el entorno real. Menor superficie de riesgo: menos reglas, ninguna depende de saber el nombre exacto de un elemento interno de Filament.
- **Nota de riesgo**: las clases usadas ahora (`.fi-sidebar`, `.fi-topbar`, `.fi-ta-table`, `.fi-ta-actions`) son las de nivel más alto/genérico de cada componente — las mismas que ya se habían confirmado funcionando en la primera vuelta de branding (logo, nombre, color primario). El riesgo de que no calcen es mucho menor que con las clases internas del sidebar que causaron el problema.
- **Troubleshooting para casos futuros similares**: si un `renderHook` con CSS a mano no muestra ningún cambio pese a que el commit ya está aplicado (confirmado con `git log`) y se corrieron `sail artisan view:clear`/`optimize:clear`, sospechar de la navegación tipo SPA de Livewire (`wire:navigate`): si el cambio se prueba haciendo clic en links del menú desde una pestaña que ya estaba abierta antes del cambio, es posible que el `<head>` (donde vive el `<style>` del renderHook) no se vuelva a cargar. Cerrar la pestaña/ventana por completo y escribir la URL de nuevo a mano fuerza una carga completa. Ver el código fuente (Ctrl+U) buscando un fragmento único del CSS nuevo confirma si el servidor ya está mandando la versión actualizada, independientemente de cómo se vea en pantalla.
- **Logo e ícono**: no se tiene el archivo vectorial original del logo físico de la clínica (solo fotos), así que se diseñó un logo simple original (no una reproducción exacta del cartel) inspirado en esos mismos colores: un monograma "CB" en un cuadrado redondeado turquesa (`#0e7490`). Dos archivos en `public/images/`: `icon.svg` (el cuadrado solo, usado como favicon del panel vía `->favicon()`) y `logo.svg` (ícono + texto "CLÍNICA BENITES" en dos líneas, usado como `->brandLogo()` en el header).
- **Favicon general del sitio**: `public/favicon.ico` estaba vacío (0 bytes, nunca se había cargado uno real) — se generó uno de verdad con Pillow (Python) a partir del mismo diseño del monograma, en los tamaños estándar 16/32/48/64px, ya que no había herramienta de conversión SVG→ICO disponible en el entorno (ImageMagick sin el delegate `rsvg-convert`).
- **Si más adelante se consigue el logo real** (archivo vectorial/de alta resolución de la clínica, o el dueño define una identidad de marca distinta): reemplazar directamente `public/images/logo.svg` e `icon.svg` (y regenerar `favicon.ico` con el mismo enfoque) — el resto de la configuración (`brandLogo()`, `favicon()`) no necesita cambiar, solo los archivos.
- **Logo — reemplazado por una versión vectorial propia, inspirada en el cartel real (23 ago 2026)**: el usuario generó con IA (Gemini) una imagen aproximada del cartel físico de la clínica — un emblema triangular con una hoja/llama partida en dos tonos (azul y turquesa/verde) y el texto "CLÍNICA BENITES" curvado alrededor. Esa imagen es una foto/render (fondo gris de mármol, reflejos y sombras 3D), no un vector — usarla tal cual como `brandLogo()`/favicon se habría visto con un recuadro gris de fondo en el header blanco de Filament, y a tamaño de favicon (16-32px) el detalle se pierde. Se optó, a pedido explícito del usuario, por **rehacer una versión vectorial propia inspirada en esa referencia**, no una reproducción exacta: mismo concepto (triángulo + hoja bicolor) pero geometría simple, fondo transparente, legible a cualquier tamaño.
- **Archivos actualizados** (reemplazan al monograma "CB" anterior): `public/images/icon.svg` (triángulo con contorno navy `#12395c` y una hoja partida en dos mitades — azul `#1f6fa8` a la izquierda, turquesa/verde `#1fae8e` a la derecha — usado como favicon del panel vía `->favicon()`) y `public/images/logo.svg` (mismo ícono a la izquierda + "CLÍNICA BENITES" en dos líneas en navy, mismo patrón que la versión anterior, usado como `->brandLogo()`). `public/favicon.ico` regenerado desde el `icon.svg` nuevo con `cairosvg` (instalado en este entorno vía `pip install cairosvg --break-system-packages`) — más preciso que el enfoque anterior con Pillow dibujando a mano, ya que ahora si se vuelve a tocar el SVG, el favicon se puede regenerar fielmente desde él en vez de mantener dos dibujos por separado. Verificado visualmente con capturas a 32px (tamaño de pestaña de navegador) y 200px — el triángulo y el corte de color se distinguen bien incluso achicado.
- **Color primario cambiado a Teal**: los colores del logo nuevo (`#1f6fa8` azul, `#1fae8e` turquesa/verde) calzan casi exacto con el `Color::Teal` predefinido de Filament (Tailwind `teal-500` ≈ `#14b8a6`), así que se aplicó como primario del panel — reemplaza el ámbar por defecto al que se había vuelto en la vuelta anterior (ver debajo). **A diferencia del intento anterior con Cyan**, esta vez **no se agregó ningún CSS a medida** (sin `renderHook`, sin tocar sidebar/topbar/tablas) — solo el override de `->colors(['primary' => Color::Teal])`, una sola línea. Menor superficie de riesgo, y evita repetir el problema de clases internas de Filament que no calzaban contra la versión real instalada (ver el intento de sidebar oscuro, más abajo en esta sección).
- **Intento anterior con Cyan y sidebar personalizado — revertido a defaults, y ahora superado por Teal**: se había probado un primario turquesa (`Color::Cyan`) con CSS a medida para el sidebar/topbar/tablas; en la prueba real con datos cargados (captura de `/admin/citas`) el usuario decidió que el conjunto no convencía y se pidió volver a los colores por defecto de Filament (ámbar/gris), sin CSS extra — eso quedó commiteado antes de esta vuelta. El cambio de esta sesión (Teal + logo nuevo) es independiente de aquel intento: mismo mecanismo simple (`->colors()`, sin renderHook), pero motivado por el logo nuevo en vez de una elección de color aislada.
- **No se tocó** `brandName('Clínica Benites')` — se mantiene igual.
- **Pendiente probar en el entorno real**: cómo se ve el logo nuevo en el header y en la pantalla de login, si el Teal contrasta bien contra el resto de la interfaz, y si el favicon se ve nítido en la pestaña del navegador.
- **Si más adelante se consigue el logo real** (archivo vectorial/de alta resolución oficial de la clínica, o el dueño define una identidad de marca distinta): reemplazar directamente `public/images/logo.svg` e `icon.svg` (y regenerar `favicon.ico` con `cairosvg` siguiendo el mismo comando documentado arriba) — el resto de la configuración (`brandLogo()`, `favicon()`, color primario) no necesita cambiar, solo los archivos y, si el color de marca real es distinto, el valor de `Color::Teal`.
- **No se tocó** la página web pública (`resources/views/welcome.blade.php`) — sigue siendo la de bienvenida por defecto de Laravel (solo cambia el `<title>` por el efecto indirecto de `APP_NAME`). La construcción real del sitio público sigue como pendiente de fondo (ver abajo).

### Logo real recibido y aplicado (25 ago 2026, previo a la entrevista formal)

El usuario compartió el logo oficial de la clínica (imagen suelta + embebido en dos páginas de un PDF de servicios, `Servicios_CB_2026.pdf`). Reemplaza al logo provisorio diseñado a mano (triángulo/hoja) descrito arriba.

- **Origen del archivo**: no hay vectorial disponible, solo raster de baja resolución (184×185px, extraído del PDF recuperando su máscara de transparencia real — la primera extracción directa vino sin alpha, con fondo blanco sólido, así que se reconstruyó combinando la imagen de color con su `SMask` del PDF). Es un lockup **vertical**: monograma (ícono estilizado, parece una "F"/"B" entrelazadas o similar abstracción — no un texto literal) arriba, "CLÍNICA BENITES" + "EXCELENCIA QUIRÚRGICA" abajo, en dos líneas.
- **Archivos nuevos en `public/images/`** (reemplazan a `logo.svg`/`icon.svg`, que se archivaron sin borrar en `public/images/_legacy/` por las dudas):
  - `logo.png` — el lockup vertical completo, recoloreado a navy (`#12395c`, mismo tono que se venía usando) preservando la transparencia real. Usado en el encabezado de la factura PDF (ahí sí hay espacio vertical de sobra).
  - `logo-white.png` — misma pieza, recoloreada a blanco, para fondos oscuros (no está en uso todavía, queda lista por si se necesita).
  - `logo-horizontal.png` / `logo-horizontal-white.png` — **recomposición horizontal** (ícono a la izquierda + las dos líneas de texto a la derecha), armada recortando y reacomodando las mismas piezas del logo real (sin redibujar ni usar una tipografía nueva). Fue necesaria porque el lockup vertical, escalado a la altura chica del header del panel (`brandLogoHeight('2.5rem')` ≈ 40px), dejaba el texto ilegible — a esa altura el ancho resultante es demasiado angosto. La versión horizontal sí es legible a ese tamaño (probado con un render simulado a 40px de alto antes de aplicar).
  - `public/favicon.ico` regenerado desde el monograma (recortado del logo real, con margen y fondo blanco para que se vea bien en pestañas claras u oscuras del navegador).
- **Código actualizado**: `AdminPanelProvider.php` → `->brandLogo(asset('images/logo-horizontal.png'))` y `->favicon(asset('favicon.ico'))`. `resources/views/pdf/factura.blade.php` → se agregó el logo (`logo.png`, 50px de alto) en el encabezado, que antes solo tenía el nombre en texto.
- **Color primario Teal sin cambios** — el navy del logo real es distinto al azul/turquesa del logo provisorio anterior, pero no se tocó `Color::Teal` en esta vuelta porque el usuario no lo pidió; queda como posible ajuste futuro si al verlo en el entorno real no combina bien.
- **Limitación conocida**: la resolución fuente es baja (184×185px) porque no hay un archivo vectorial ni de alta resolución disponible — se ve bien a los tamaños actuales (favicon, header 2.5rem, factura 50px), pero si en el futuro se necesita el logo grande (ej. para imprimir, o un hero en la página web pública), conviene pedirle al dueño el archivo original en alta resolución o vectorial en la entrevista.
- **Pendiente probar en el entorno real**: cómo se ve el logo horizontal en el header del panel y en el login, y el logo vertical en una factura generada de verdad (hasta ahora solo se verificó con renders simulados fuera de la app).

**Otros pendientes de fondo, sin definir aún**:
- Construir la página web pública (diseño, contenido) — sigue sin arrancar.
- Sigue pendiente la respuesta del contacto interno de la clínica sobre cuántas áreas/especialidades tiene — no bloquea el desarrollo (el sistema ya soporta cualquier número de áreas dinámicamente), pero sería bueno tenerla para cargar datos reales en vez de datos de prueba.
- No se ha hecho la entrevista formal con el dueño de la clínica.
- ~~Si se consigue el logo real de la clínica (archivo original), reemplazar el diseño provisorio de branding~~ — hecho, ver entrada de arriba (25 ago 2026).


## 8.2 Theme propio del panel — encabezado de tablas (título + buscador en una sola fila)

**Pedido original**: en el widget "Citas de hoy" del Dashboard, el título ("Citas de hoy") aparecía en una fila y la barra de búsqueda en la fila de abajo — se pidió juntarlos en una sola fila.

**Primer intento (no funcionó)**: mover el título de `protected static ?string $heading` (nivel widget) a `->heading()` dentro de `table()`. Se investigó el código fuente de `filament/widgets` y se confirmó que ambos caminos terminan siendo exactamente lo mismo internamente (`TableWidget::makeTable()` ya envuelve el `$heading` estático en `->heading()` de la tabla) — por eso no cambió nada visualmente. Este cambio quedó igual en el código (es inofensivo, más explícito), pero la causa real era otra.

**Causa real**: no es un bug de este widget — es el comportamiento de fábrica de **todas** las tablas de Filament (confirmado leyendo el código fuente real del paquete `filament/tables` en la versión exacta instalada, `v5.7.3`, clonado temporalmente para inspeccionarlo). El encabezado de una tabla (`.fi-ta-header-ctn`) contiene dos bloques hijos separados: `.fi-ta-header` (título/descripción/acciones) y `.fi-ta-header-toolbar` (buscador/filtros/selector de columnas) — cada uno con su propio `border-bottom`, apilados uno debajo del otro por diseño. No hay una opción de configuración que los junte.

**Opciones evaluadas con el usuario**:
1. Dejarlo como está (consistente con el resto del panel, cero riesgo).
2. Forkear/copiar la plantilla Blade completa de la tabla (`vendor/filament/tables/resources/views/index.blade.php`, **2604 líneas** — orden, selección masiva, agrupamiento, columnas, todo el motor de la tabla) a `resources/views/vendor/filament-tables/`, y editar ahí los dos `<div>`. **Descartado**: congela esa plantilla completa en la versión actual del paquete — cualquier actualización futura de Filament (incluidos parches de seguridad) dejaría de aplicarse silenciosamente a esa copia, en todas las tablas del sistema.
3. **Elegida**: lograr el mismo resultado visual con **CSS scoped**, dirigido específicamente a esas dos clases (`.fi-ta-header`, `.fi-ta-header-toolbar`, dentro de `.fi-ta-header-ctn`), sin tocar ningún archivo de Filament. Mismo resultado, sin el riesgo de la opción 2.

**Qué se hizo**:
- El proyecto **no tenía un theme propio de Filament configurado todavía** (usaba los estilos por defecto del paquete) — se creó por primera vez.
- Archivo nuevo `resources/css/filament/admin/theme.css`: importa el theme base de Filament (`@import '/vendor/filament/filament/resources/css/theme.css';`) y agrega el ajuste de CSS descrito arriba, dentro de un `@media (min-width: 640px)` (mismo breakpoint `sm` que usa Filament en sus propios patrones responsivos) — en pantallas angostas se mantiene el apilado normal, para no apretar el título contra el buscador en mobile.
- `vite.config.js`: se agregó `resources/css/filament/admin/theme.css` al arreglo `input` de la configuración de Laravel/Vite.
- `AdminPanelProvider.php`: se agregó `->viteTheme('resources/css/filament/admin/theme.css')`.
- **Efecto**: aplica a **todas** las tablas del panel (Áreas, Citas, Facturas, Historia Clínicas, Médicos, Pacientes, Usuarios, y el widget "Citas de hoy"), no solo al widget del Dashboard — es la misma decisión que se había tomado al elegir esta opción, para que el panel se vea consistente.
- **Confirmado funcionando por el usuario en el entorno real**, en todas las tablas del panel, no solo en el widget del Dashboard. Fue necesario correr `npm install` (nunca se había instalado el frontend dentro del contenedor de Sail hasta este cambio, por eso `vite` no se encontraba al primer intento de `npm run build`) y luego sí `npm run build` para que Vite compilara el `theme.css` nuevo.

**Efecto secundario encontrado y corregido — hueco vacío en la tabla de Citas**: al probar en el entorno real, la tabla `/admin/citas` quedó viéndose distinta a las demás: un hueco vacío grande entre el buscador y el ícono de filtro, a la derecha del todo. No era un problema del CSS nuevo — es que Citas, a diferencia del resto de tablas, usaba (a propósito, ver sección 9) `layout: FiltersLayout::AboveContentCollapsible` para sus 3 filtros rápidos ("Hoy"/"Pendientes"/"Confirmadas"), que por defecto aparecen **colapsados** (solo el ícono de embudo, sin nada en el medio de esa fila) — al expandirlos (clic en el embudo) se abre un bloque grande debajo con los 3 switches + botón "Aplicar filtros" + "Resetear los filtros", empujando el resto de la tabla hacia abajo. Se cambió el layout a `Dropdown` (el default de Filament, quitando el `layout:` explícito) — mismo patrón ya adoptado en Usuarios (ver sección 9): el botón de filtro ahora queda pegado al buscador, y el panel con los 3 switches aparece flotando encima del contenido al hacer clic, sin dejar huecos ni empujar la tabla. Se quitó también el `use Filament\Tables\Enums\FiltersLayout;` de `CitasTable.php`, que quedó sin uso. **Los 3 filtros ("Hoy"/"Pendientes"/"Confirmadas") siguen funcionando exactamente igual** (mismos toggles, se pueden combinar) — solo cambió dónde y cómo se muestran, no su lógica. **Confirmado funcionando por el usuario en el entorno real.**

## 8.3 Botón "Cancelar" → "Atrás" en las pantallas de Editar

**Pedido original**: al editar un paciente (o cualquier otro registro), tras guardar y volver a esa misma pantalla de Editar, aparecían dos botones: "Guardar cambios" y "Cancelar". El usuario notó que "Cancelar" ahí no tiene mucho sentido — el registro ya está guardado, no hay nada que "cancelar" — y pidió reemplazarlo por un botón que simplemente regrese al listado. Aclarado explícitamente: el botón "Cancelar" de las pantallas de **Crear** sí es útil (ahí si descarta un formulario sin guardar) y no debía tocarse.

**Decisión con el usuario**: aplicar el cambio a **todas** las pantallas de Editar del panel (no solo Pacientes), y que el botón lleve al listado del recurso (ej. `/admin/pacientes`), no a la página anterior en el historial del navegador.

**Qué se hizo**:
- Filament arma los botones del formulario de Editar (`Guardar` + `Cancelar`) en el método `getFormActions()` de cada página `EditRecord`. Se creó un trait nuevo, `App\Filament\Concerns\HasBackFormAction`, que sobreescribe ese método devolviendo `[Guardar, Atrás]` en vez de `[Guardar, Cancelar]`.
- El botón "Atrás" (`Action::make('back')`) usa el ícono `Heroicon::OutlinedArrowLeft`, color gris (mismo que tenía "Cancelar") y navega con `$this->getResourceUrl()` sin argumentos — en Filament esto resuelve siempre a la página de listado (`index`) del Resource actual, sin necesidad de escribir la URL a mano en cada Resource.
- El trait se aplica (`use HasBackFormAction;`) en las 7 páginas de Editar existentes: `EditArea`, `EditMedico`, `EditPaciente`, `EditCita`, `EditHistoriaClinica`, `EditFactura`, `EditUser`. Ninguna tenía ya un `getFormActions()` propio, así que no hubo conflicto en ningún Resource.
- **No se tocó** ninguna página de Crear (`CreateArea`, `CreateMedico`, etc.) — ninguna usa el trait nuevo, así que conservan el comportamiento por defecto de Filament (`Crear` + `Cancelar`).
- Archivo nuevo: `app/Filament/Concerns/HasBackFormAction.php` (carpeta `Concerns/` nueva dentro de `app/Filament/`, para código compartido entre Resources que no es un Resource en sí mismo).

**Confirmado funcionando por el usuario en el entorno real**, en las 7 pantallas de Editar.

## 8.4 Agrupar el menú lateral en categorías (navigationGroup)

**Motivo**: solo el módulo de Infraestructura (Camas, Quirófanos, Cirugías, Internamientos, Órdenes de Estudio, Servicios Ambulancia) tenía `$navigationGroup` asignado. Los otros 10 Resources (Pacientes, Citas, Facturas, Historia Clínicas, Áreas, Médicos, Usuarios, Item/Lote/Movimiento Inventarios) aparecían todos sueltos en el sidebar, sin ningún criterio de orden — el usuario lo notó al ver la captura del listado de Facturas y pidió aplicar el mismo criterio de agrupación que ya existía para Infraestructura.

**Grupos definidos** (criterio: frecuencia de uso del día a día primero, catálogos/administración al final):
- **Atención al paciente**: Pacientes, Citas, Historia Clínicas.
- **Facturación**: Facturas.
- **Infraestructura** (ya existía, sin cambios): Camas, Quirófanos, Cirugías, Internamientos, Órdenes de Estudio, Servicios Ambulancia.
- **Inventario**: Item Inventarios, Lote Inventarios, Movimiento Inventarios.
- **Administración**: Áreas, Médicos, Usuarios.
- **Escritorio** (Dashboard) queda sin grupo, como está ahora — es una Page, no un Resource, y va arriba de todo.

**Qué se hizo**: en cada uno de los 10 Resources, se agregó `use UnitEnum;` (import) y la propiedad `protected static string|UnitEnum|null $navigationGroup = '...';`, mismo patrón ya usado en Infraestructura. Cambio puramente de organización del menú — no se tocaron permisos, íconos, ni ningún otro comportamiento.

**Nota sobre permisos y grupos**: Filament oculta automáticamente un grupo entero del sidebar si el usuario logueado no tiene acceso a ningún Resource de ese grupo (según los `canViewAny()` ya existentes en cada Resource) — no hizo falta ningún ajuste extra para que, por ejemplo, un médico no vea el grupo "Administración".

**Orden de los grupos forzado explícitamente**: el usuario confirmó que quería el orden propuesto (no el alfabético por defecto de Filament), así que se agregó `->navigationGroups([...])` en `AdminPanelProvider.php` con el orden exacto: Atención al paciente → Facturación → Infraestructura → Inventario → Administración.

**Pendiente, no incluido en este cambio**: diferenciar los íconos del sidebar — hoy varios Resources comparten el mismo ícono genérico (`Heroicon::OutlinedRectangleStack`): Áreas, Citas, Facturas, Historia Clínicas, Médicos y Pacientes lo usan los seis. Ayudaría a distinguirlos de un vistazo dentro de cada grupo, pero no se aplicó en esta vuelta porque este entorno de trabajo no tiene `vendor/` instalado (no se pudo verificar contra el enum real `Filament\Support\Icons\Heroicon` qué nombres de ícono son válidos en la versión de Filament instalada, y poner un nombre inválido rompería el panel). Si se quiere hacer, conviene definir los íconos junto al usuario con el entorno real corriendo para poder probar cada uno antes de commitear.

**Confirmado funcionando por el usuario en el entorno real** (los 5 grupos aparecen colapsados en el sidebar, con los ítems correctos en cada uno — ver captura compartida). El orden explícito (`navigationGroups()`) se agregó después, aún sin confirmar visualmente por el usuario.

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

- **Gestión de usuarios desde el panel** — **resuelto y confirmado funcionando por el usuario en el entorno real**. Nuevo `app/Filament/Resources/Users/` (mismo patrón de carpetas que los demás Resources: `UserResource.php`, `Schemas/UserForm.php`, `Tables/UsersTable.php`, `Pages/{ListUsers,CreateUser,EditUser}.php`). No se usó ningún paquete de permisos granulares (`spatie/laravel-permission`, Filament Shield) — con los 3 roles fijos (admin/recepcion/medico) alcanza con este Resource normal + el campo `Select` de `rol` que ya existía en la tabla `users`. Solo `admin` puede ver/crear/editar/eliminar este Resource (`canViewAny()` ya bloquea el acceso completo, incluida la entrada en el menú, para recepción y médico). Protección extra agregada: un admin **no puede eliminar su propia cuenta** (`canDelete()` lo excluye explícitamente, y el `DeleteBulkAction` de la tabla valida lo mismo con un `->before()` antes de un borrado masivo) — así la clínica no puede quedarse sin ningún admin activo por accidente; probado directamente y confirmado que bloquea el autoborrado. El campo contraseña es opcional al editar (dejarlo en blanco no cambia la contraseña actual) y obligatorio al crear, con el patrón estándar de Filament (`dehydrateStateUsing`/`dehydrated` + `Hash::make`). **Filtro rápido por rol agregado** en `UsersTable` (`SelectFilter` con las 3 opciones, layout `Dropdown` por defecto de Filament — el botón de filtro queda junto a la barra de búsqueda, no en su propia fila arriba de la tabla como en Citas, porque aquí es un solo filtro y no hace falta el espacio extra) — se usó `SelectFilter` en vez de 3 `Filter->toggle()` como en Citas porque los roles son mutuamente excluyentes (un usuario solo tiene uno), a diferencia de "Hoy"/"Pendientes" en Citas que sí se pueden combinar. **Confirmado funcionando por el usuario en el entorno real**, y ajustado a pedido a este layout tras esa confirmación (el `AboveContentCollapsible` original quedó descartado para este Resource).
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
- **Pendiente**: asignar `medico_id` a los usuarios con rol médico que ya existen en el sistema (créalos/edítalos desde `/admin/users` y selecciona su médico vinculado). Sin ese paso, el filtro simplemente no aplica para ellos (ver diseño defensivo arriba) — no rompe nada, pero tampoco se benefician del filtro hasta hacerlo. **Confirmado funcionando por el usuario en el entorno real**, incluyendo el fix del bug de `medico_id` (ver debajo): filtro de Citas/Historias Clínicas/Dashboard, autoselect al crear, y ahora también el correcto limpiado de `medico_id` al cambiar el rol de un usuario.

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
