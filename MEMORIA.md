# 🧠 MEMORIA DEL PROYECTO — Clínica Benites

Este archivo es un resumen de contexto para retomar el desarrollo en cualquier momento (por ti mismo o pegándoselo a una IA). Explica qué es el proyecto, cómo está armado, qué decisiones se tomaron y por qué, y qué falta.

Última actualización: 24 de agosto de 2026 — cuarta entrada del día (se creó `database/seeders/AreaSeeder.php` con las 27 especialidades reales de la clínica, la respuesta del contacto interno documentada en la entrada anterior — usa `firstOrCreate` para no duplicar si ya había áreas de prueba cargadas. Registrado en `DatabaseSeeder.php` para correr con `db:seed`, o solo con `--class=AreaSeeder`. No se cargaron los "servicios/infraestructura" del PDF (UCI, quirófanos, etc.) como áreas — no son especialidades médicas en el sentido del modelo actual, quedan solo como contexto documentado. **Confirmado funcionando por el usuario en el entorno real.** Ver sección 6.1 y 9 para el detalle.)

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
  public/
    images/
      icon.svg                 # Monograma "CB" (favicon del panel /admin)
      logo.svg                  # Ícono + "Clínica Benites" (logo del header del panel)
    favicon.ico                 # Favicon del sitio en general (regenerado, antes vacío)
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

**Otros pendientes de fondo, sin definir aún**:
- Construir la página web pública (diseño, contenido) — sigue sin arrancar.
- Sigue pendiente la respuesta del contacto interno de la clínica sobre cuántas áreas/especialidades tiene — no bloquea el desarrollo (el sistema ya soporta cualquier número de áreas dinámicamente), pero sería bueno tenerla para cargar datos reales en vez de datos de prueba.
- No se ha hecho la entrevista formal con el dueño de la clínica.
- Si se consigue el logo real de la clínica (archivo original), reemplazar el diseño provisorio de branding (ver sección 8.1).

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
