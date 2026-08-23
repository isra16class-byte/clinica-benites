# 📜 CHANGELOG — Clínica Benites

Registro cronológico de cambios del proyecto. Formato: más nuevo arriba, nunca se borran entradas viejas.

Ver `MEMORIA.md` para el estado actual y contexto técnico completo — este archivo es solo la bitácora de "qué cambió cuándo".

---

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
