# 🧠 MEMORIA DEL PROYECTO — Clínica Benites

Este archivo es un resumen de contexto para retomar el desarrollo en cualquier momento (por ti mismo o pegándoselo a una IA). Explica qué es el proyecto, cómo está armado, qué decisiones se tomaron y por qué, y qué falta.

Última actualización: 23 de agosto de 2026 (se crearon los modelos y archivos de migración vacíos de las 6 tablas principales — `Area`, `Paciente`, `Medico`, `Cita`, `HistoriaClinica`, `Factura` — todavía sin las columnas definidas dentro. Repo inicializado y conectado a GitHub, primer commit en proceso).

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
      Area.php              # Creado, vacío (solo boilerplate de Eloquent)
      Paciente.php           # Creado, vacío
      Medico.php              # Creado, vacío
      Cita.php                 # Creado, vacío
      HistoriaClinica.php      # Creado, vacío
      Factura.php              # Creado, vacío
      User.php                # Por defecto de Laravel — pendiente agregarle campo `rol`
  database/
    migrations/
      ..._create_areas_table.php            # Creada, columnas AÚN NO agregadas
      ..._create_pacientes_table.php        # Creada, columnas AÚN NO agregadas
      ..._create_medicos_table.php          # Creada, columnas AÚN NO agregadas
      ..._create_citas_table.php            # Creada, columnas AÚN NO agregadas
      ..._create_historia_clinicas_table.php # Creada, columnas AÚN NO agregadas
      ..._create_facturas_table.php         # Creada, columnas AÚN NO agregadas
  docker-compose.yml         # Generado por Sail
  .env                       # NO se sube a git (credenciales locales de MySQL, etc.)
  MEMORIA.md                 # Este archivo
  CHANGELOG.md                # Bitácora cronológica
```

## 4. Modelo de datos (diseño acordado, columnas pendientes de escribir en las migraciones)

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
- ✅ Modelos y archivos de migración creados para las 6 tablas principales (vacíos por dentro).
- ⬜ **Pendiente inmediato**: llenar cada migración con sus columnas (ver sección 4) y correr `sail artisan migrate`.
- 🔄 Git: repo creado en GitHub (público), `git init` + `git add .` + commit en curso — push aún sin confirmar como exitoso.

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

## 7. Roadmap / pendientes técnicos

- [ ] Llenar las 6 migraciones con sus columnas (ver sección 4).
- [ ] Correr `sail artisan migrate`.
- [ ] Confirmar que el push a GitHub se completó correctamente.
- [ ] Agregar campo `rol` a la tabla `users` (admin/recepción/médico).
- [ ] Crear los Resources de Filament (pantallas) para cada tabla.
- [ ] Definir roles y permisos dentro de Filament (qué ve/hace cada rol).
- [ ] Construir la página web pública (diseño, contenido).
- [ ] Definir alcance real de facturación con el cliente (¿SRI, seguros?) antes de modelar esa parte a fondo.
- [ ] Backups automáticos antes de publicar.
- [ ] Comprar dominio y contratar VPS cuando el sistema esté listo para lanzarse.

## 8. Historial de cambios

Ver `CHANGELOG.md` — ahí se registra cronológicamente cada paso importante. Este archivo (`MEMORIA.md`) describe el estado **actual**, se sobreescribe cada vez que algo cambia. El changelog se va acumulando, nunca se borra lo viejo.

---

**Para continuar el desarrollo**: lee este archivo primero, después revisa `CHANGELOG.md` para el detalle de qué se hizo en qué orden.

**Cada vez que se haga un cambio importante:**
1. Actualizar `MEMORIA.md` para reflejar el estado nuevo.
2. Agregar una entrada nueva arriba en `CHANGELOG.md`.
3. Hacer commit de ambos junto con el código.

**Nota sobre el flujo de trabajo**: el usuario prefiere subir el código él mismo — Claude no tiene ni debe asumir acceso de escritura directo al repo remoto. Cuando Claude proponga cambios de código, los entrega como texto/patch para que el usuario los aplique y haga su propio `git add` / `commit` / `push`.
