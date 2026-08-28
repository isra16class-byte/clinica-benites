# 🧠 MEMORIA DEL PROYECTO — Clínica Benites

Este archivo resume el estado **actual** del proyecto para retomar el desarrollo en cualquier momento. Se sobreescribe cada vez que algo cambia — no acumula narrativa de sesiones pasadas (eso vive en `docs/historico/` y en `CHANGELOG.md`).

Última reorganización: 28 de agosto de 2026 — se archivaron el `MEMORIA.md` y `CHANGELOG.md` originales (habían crecido a ~535KB combinados, sobre todo por el detalle vuelta-por-vuelta de ajustes de CSS) en `docs/historico/MEMORIA_2026-08-23_2026-08-28.md` y `docs/historico/CHANGELOG_2026-08-23_2026-08-28.md`. Este archivo es la versión condensada: mismo esqueleto, sin la reconstrucción completa del proceso de cada ajuste. Ver la sección 12 para el criterio a seguir de ahora en adelante.

---

## 1. Qué es

Sistema de gestión para una clínica (**Clínica Benites**, Guayaquil — sin presencia digital previa) con dos partes en el **mismo proyecto Laravel** (rutas separadas, no dominios distintos):

1. **Sitio web público** — información, servicios, contacto. El paciente **no agenda cita desde ahí** (confirmado).
2. **Sistema interno privado** (login) — citas, historias clínicas, facturación, inventario, infraestructura física. Lo usa recepción/médicos/administración. Las citas las sigue creando el personal manualmente (teléfono/WhatsApp), pero quedan registradas.

Aún **no se ha hecho la entrevista formal** con el dueño — el proyecto surgió de un contacto interno (amigo que trabaja ahí).

Repo: `https://github.com/isra16class-byte/clinica-benites` (público)

## 2. Stack

- **Backend**: Laravel (PHP).
- **Panel admin**: Filament (v5.7.x).
- **Base de datos**: MySQL.
- **Entorno de desarrollo**: Docker vía Laravel Sail, sobre WSL2 (PC de desarrollo Windows).
- **Frontend público**: Blade + Tailwind 4 (`resources/css/public.css`), sin JS/Alpine (menú móvil resuelto con checkbox+CSS).
- **Hosting futuro**: VPS (Hetzner o DigitalOcean). Dominio `.com` aún sin comprar.
- **Control de versiones**: Git + GitHub, repo público (el código no contiene datos reales de pacientes).

## 3. Estructura de archivos (resumen)

```
clinica-benites/
  app/
    Models/            # Area, Paciente, Medico, Cita, HistoriaClinica, Factura, User,
                        # ItemInventario, LoteInventario, MovimientoInventario,
                        # Cama, Quirofano, Internamiento, Cirugia, OrdenEstudio, ServicioAmbulancia
    Http/Controllers/
      FacturaPdfController.php   # Exporta factura a PDF (dompdf)
    Filament/
      Concerns/
        HasBackFormAction.php    # Trait: botón "Atrás" en vez de "Cancelar" en pantallas de Editar
      Resources/                 # Un Resource completo (Form+Table+Pages) por cada modelo de arriba
      Widgets/
        CitasDeHoyWidget.php
        IndicadoresGerencialesWidget.php   # solo admin
        IngresosPorMesChartWidget.php      # solo admin
        FacturacionPorAreaChartWidget.php  # solo admin
        AlertasOperativasWidget.php        # solo admin
  resources/
    css/
      public.css                          # paleta/tipografía/animaciones del sitio público
      filament/admin/theme.css            # theme propio del panel (encabezado de tablas, etc.)
    views/
      components/layouts/public.blade.php
      partials/nav.blade.php
      sections/hero.blade.php             # única sección del sitio público construida hasta ahora
      home.blade.php                      # compone layout+nav+secciones
      pdf/factura.blade.php
  public/images/                          # logo real de la clínica, hero-quirofano.jpg, hero video
  docs/
    historico/                            # MEMORIA.md y CHANGELOG.md completos, pre-28 ago 2026
```

## 4. Modelo de datos

```
areas             — nombre
pacientes         — nombres, apellidos, cedula (unique), fecha_nacimiento, telefono, email, direccion, sexo
medicos           — nombres, apellidos, area_id (FK), telefono, email
users             — name, email, password, rol (admin/recepcion/medico), medico_id (FK nullable, solo si rol=medico)
citas             — paciente_id, medico_id, area_id, fecha, hora_inicio, hora_fin, estado, notas
historia_clinicas — paciente_id, medico_id, cita_id (nullable), motivo_consulta, diagnostico, tratamiento, notas
facturas          — paciente_id, cita_id (nullable), monto, estado_pago, metodo_pago, fecha

# Módulo infraestructura física (24 ago 2026)
camas, quirofanos, internamientos, cirugias (+ pivot médicos adicionales), ordenes_estudio, servicios_ambulancia

# Módulo medicamentos/insumos (24 ago 2026)
items_inventario, lotes_inventario (FEFO, vencimiento), movimientos_inventario
```

**Por qué `areas` es tabla propia**: la clínica tiene 27 especialidades reales (confirmado, ver `Servicios_CB_2026.pdf`) — con tabla aparte, agregar/quitar áreas no requiere tocar código.

## 5. Estado del entorno de desarrollo

- ✅ WSL2 + Docker Desktop + Sail funcionando (`./vendor/bin/sail up`).
- ✅ MySQL conectado, todas las migraciones corridas.
- ✅ Filament en `http://localhost/admin`, con usuario admin.
- ✅ Locale `es` / timezone `America/Guayaquil` configurados (antes en `en`/`UTC` — bug de fondo ya corregido, ver histórico si hace falta el detalle: causaba que el Dashboard dejara de mostrar citas de la tarde/noche).
- ✅ `barryvdh/laravel-dompdf` instalado (exportar Factura a PDF).
- ✅ Git conectado a GitHub, local y remoto sincronizados.

## 6. Preguntas de negocio pendientes

- [ ] ¿El paciente ve precios/servicios en la web o se maneja solo internamente?
- [ ] ¿La cita se confirma automática o la aprueba recepción?
- [ ] ¿Facturación electrónica con el SRI? ¿Seguros médicos/reembolsos, con cuáles aseguradoras?
- [ ] ¿Cuántos médicos/usuarios usarán el sistema?
- [ ] ¿Acceso remoto o solo desde la clínica?
- [ ] ¿Presupuesto y plazo de entrega?
- [ ] ¿Planean más sucursales pronto?
- [ ] ¿Qué es "cuantificos" (término usado por el contacto interno para describir la administración actual, sin aclarar)?
- [ ] ¿Los 3 roles (admin/recepción/médico) alcanzan, o falta un 4º rol de farmacia/bodega? La clínica sí tiene personal dedicado en farmacia, pero no se sabe el mecanismo real de descuento de stock (¿lo registra el médico en el momento, avisa después, o farmacia prepara antes?).
- [ ] Historias clínicas digitales: es objetivo confirmado del proyecto, pero el alcance real es gradual/por fases — falta definir qué se digitaliza primero.

Ya resuelto: especialidades (27, ver `Servicios_CB_2026.pdf`), no se agenda cita desde la web, logo/branding real recibido y aplicado.

## 7. Estado por módulo

**Sistema interno (Filament) — funcional de punta a punta:**
- CRUD completo de los 6 modelos core + roles/permisos (matriz completa en sección 10).
- Módulo de infraestructura física y de inventario (medicamentos/insumos), completos.
- Dashboard gerencial (KPIs, gráficos, alertas operativas) — solo admin.
- Gestión de usuarios desde `/admin/users` (solo admin, con protección de autoborrado).
- Exportar Factura a PDF.
- Filtro "mis pacientes" para rol médico (vía `medico_id` en `users`) — **pendiente**: asignar `medico_id` a los usuarios médico existentes desde `/admin/users`.
- Branding aplicado: logo real de la clínica, color primario Teal, favicon.
- UX pulida: botón "Atrás" en Editar, sidebar agrupado por categorías, buscador global en los 6 Resources, filtros rápidos en Citas, cambio de estado con un clic, theme propio (`theme.css`) para encabezado de tablas.

**Sitio público — solo la Portada (Hero) está construida:**
- Paleta: navy `#0C447C` + verde azulado `#0F6E56` (mismos colores del panel) + acento dorado/champán, exclusivo del sitio público.
- Tipografía: Space Grotesk (titulares) + Instrument Sans (cuerpo).
- Elemento distintivo: línea de pulso SVG animada (plano técnico + pulso dorado en loop), respeta `prefers-reduced-motion`.
- Video de fondo del hero (reemplazó a un slideshow de fotos): solución final aplicada — `hero.blade.php` agrega una imagen de glow detrás del video; `public.css` la amplía y desenfoca, deja `::after` sin fondo y aplica una máscara alfa transparente directamente al video y al poster para que los bordes dejen ver el fondo real. El video quedó ligeramente más grande y más abajo, con los breakpoints responsive ajustados. **Confirmado visualmente por el usuario**.
- **Las 5 secciones están completas**: Portada (Hero), Especialidades, Servicios, Sobre nosotros, Contacto + Footer (28 ago 2026, ver detalle en sección 8.1 abajo).
- **Rediseño en curso (28 ago 2026, segunda entrada del día)**: el usuario marcó que las 4 secciones nuevas (todo menos el Hero) se ven genéricas comparadas con el Hero. Se armó `docs/PLAN_SITIO_PUBLICO.md` (investigación + flujo + banco de frases + dirección de iconos/fondos + plan de animaciones) y se validó el enfoque con **Especialidades primero** (ya implementado: rail de letras A-U, watermark "27" en trazo, marcador de grupo por letra, línea dorada trazada en hover). **Servicios ya implementado también** (28 ago 2026, tercera entrada): se quitó el `cb-orb-teal` (blob de color plano) y se reemplazó por `cb-hero-grid` con máscara propia + un glow ambiental hecho con la foto real de Quirófanos (`.cb-services-glow` en `public.css`) — el mosaico y el hover-scale de las fotos no se tocaron. **Sobre nosotros**: revisada y dejada **como está a propósito** — el plan ya la documentaba como la sección "pausa" del sitio (tipográfica, sin foto, sin decoración nueva, sin animación adicional); no se le aplicó ningún cambio. **Contacto ya implementado** (28 ago 2026, cuarta entrada): se corrigió el ícono duplicado del panel de emergencias (era el mismo pin de ubicación de arriba) por un ícono de pulso/ECG con un pulso de opacidad sutil (`cb-pulse-fade`). **Pendiente**: el fix de `.cb-reveal` a `animation-timeline: view()` (scroll-driven, CSS puro) para que el fade-up se dispare al llegar a cada sección y no al cargar la página — es lo único que falta del plan. Ver `docs/PLAN_SITIO_PUBLICO.md` sección 6 para el brief cerrado, sin preguntas abiertas.
- **Pendiente**: reemplazar 6 placeholders de teléfono/WhatsApp (`593000000000`) antes de publicar — nav (2), hero (2), contacto (2). Confirmar en entorno real (no solo Playwright/capturas) que el mosaico de Servicios y el directorio de Especialidades se ven bien con las fuentes reales cargadas (acá se verificó contra una build estática de Tailwind, sin poder correr `vite build` completo por falta de red a `fonts.bunny.net` en este sandbox).

### 7.1 Detalle: secciones nuevas del sitio público (28 ago 2026)

- **Especialidades** (`sections/especialidades.blade.php`): directorio editorial de las 27 especialidades reales (`AreaSeeder.php`, única fuente), alfabetizado en 3 columnas — se descartó agrupar por categoría (quirúrgica/clínica/diagnóstico) por no venir confirmada esa taxonomía en el material del cliente.
- **Servicios** (`sections/servicios.blade.php`): mosaico (bento grid) con las 5 fotos reales que ya estaban en `public/images/hero-*.jpg` pero solo se usaba una (quirófano) en el hero — ahora las 5 tienen uso. Tile de Quirófanos como ancla (2×2), franja final de "ambulancia propia" (dato ya confirmado, no nuevo).
- **Sobre nosotros** (`sections/sobre.blade.php`, id `#nosotros`): única sección clara (ivory) del sitio, sin fotografía — bloque tipográfico + 3 pilares, todos datos ya confirmados en otras partes del sitio (nada nuevo inventado).
- **Contacto** (`sections/contacto.blade.php`): sin formulario (ya confirmado: no se agenda desde la web) y sin dirección/horario/mapa — no están confirmados por el cliente, no se inventaron.
- **Footer** (`partials/footer.blade.php`): nuevo, logo + nav + WhatsApp + copyright.
- `nav.blade.php` y `home.blade.php` actualizados para conectar las 4 secciones + footer, en orden. Se agregó el link "Nosotros" al nav (desktop y móvil).
- CSS nuevo agregado como bloque propio al final de `public.css` (no intercalado en las reglas del hero, para no arriesgar lo ya confirmado) — reutiliza toda la paleta/tipografía ya definida, sin tokens nuevos.

## 8. Funciones futuras propuestas (investigadas, no priorizadas ni construidas)

- Cumplimiento LOPDP: registro de consentimiento del paciente + auditoría de accesos a datos de salud (candidato: `spatie/laravel-activitylog`).
- Recordatorios de cita por WhatsApp/SMS, portal de autoagendamiento — **descartados explícitamente por ahora**, fase futura.
- Reportes/KPIs adicionales, lista de espera automática al cancelar cita, recall/control preventivo, encuesta de satisfacción post-visita, ficha de "paciente frecuente", turno virtual sin cita, panel ejecutivo ampliado.

Detalle completo de cada propuesta en `docs/historico/MEMORIA_2026-08-23_2026-08-28.md`, sección 9.

## 9. Decisiones técnicas de referencia (para no repetir investigación)

- **Tablas de Filament no permiten juntar título+buscador en una sola fila de forma nativa** — se resolvió con CSS scoped en `theme.css` (`.fi-ta-header`/`.fi-ta-header-toolbar`), no forkeando el Blade del paquete.
- **Botones de acción (Crear/Editar/Eliminar) no se ocultan solos según permisos** — hay que conectar `->visible()` a mano a `canCreate()`/`canEdit()`/`canDelete()` en cada Resource. Ya aplicado en los 18 puntos correspondientes.
- **Un campo oculto con `->visible()` en un formulario Filament sigue guardando su valor** — si depende de otro campo (ej. `medico_id` según `rol`), hay que limpiarlo a mano en `afterStateUpdated()` + `mutateFormDataBeforeSave()`.
- Para ocultar bordes de `<video>` sin depender de igualar el color del fondo, usar una máscara alfa transparente directamente sobre el video/poster; un `::after` coloreado puede crear un rectángulo visible si el fondo tiene gradientes o radial teal. El glow desenfocado detrás suaviza la transición.
- Detalle y verificación de cada una de estas en el histórico si hace falta reconstruir el razonamiento completo.

## 10. Roles y permisos

| Recurso | Admin | Recepción | Médico |
|---|---|---|---|
| Áreas | Todo | Solo ver | Solo ver |
| Médicos | Todo | Solo ver | Solo ver |
| Pacientes | Todo | Todo | Ver y editar (sin eliminar) |
| Citas | Todo | Todo | Ver y editar (sin eliminar, sin crear) |
| Historias Clínicas | Todo | Sin acceso | Todo (eliminar solo admin) |
| Facturas | Todo | Todo | Sin acceso |
| Usuarios | Todo (excepto autoborrado) | Sin acceso | Sin acceso |

Médico ve solo "sus" registros (Citas, Historias Clínicas, Dashboard) vía `medico_id` vinculado en `users`. Si un usuario médico no tiene `medico_id` asignado, ve todo (diseño defensivo — mejor ver de más que bloquear).

## 11. Roadmap inmediato

1. Confirmar en entorno real el sitio público completo (las 5 secciones), en particular el video del hero (overlay) y el mosaico de Servicios con las fuentes reales cargadas.
2. Reemplazar placeholders de teléfono/WhatsApp antes de publicar (6 ocurrencias: nav, hero, contacto).
3. Asignar `medico_id` a usuarios médico existentes.
4. Entrevista formal con el dueño — resolver preguntas de la sección 6 (incluida dirección/horario real para la sección Contacto, hoy sin ese dato).

## 12. Cómo mantener este archivo (a partir de ahora)

Para evitar que `MEMORIA.md`/`CHANGELOG.md` vuelvan a crecer sin control:

- **`CHANGELOG.md`**: cada entrada debe ser corta (qué cambió, en qué archivo, 2-4 líneas). No reconstruir el proceso completo de debugging/iteración (varias vueltas de prueba y error, mediciones, etc.) — eso puede vivir en el mensaje de commit o quedar implícito, no en el changelog.
- **`MEMORIA.md`**: refleja el **estado actual**, no la historia de cómo se llegó ahí. Si una decisión técnica no obvia vale la pena recordar (sección 9), una o dos líneas alcanzan.
- Cuando `MEMORIA.md` o `CHANGELOG.md` vuelvan a superar ~80-100KB cada uno, repetir este mismo proceso: archivar en `docs/historico/` con fecha, y arrancar una versión nueva condensada.

**Flujo de trabajo con el repo**: el usuario no da push directo. Claude clona el repo público, hace el cambio, commitea localmente usando la identidad de Git del usuario (`user.name = isra16class-byte`, `user.email = isra16class@gmail.com`), y genera un patch con `git format-patch -1 HEAD` como archivo descargable. El usuario lo aplica con `git am nombre-del-patch.patch` (conserva autor y mensaje) y hace el `git push` él mismo. Nunca asumir que Claude tiene o debe pedir acceso de escritura directo al repo remoto.
