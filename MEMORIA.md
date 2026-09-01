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
                        # Cama, Quirofano, Internamiento, Cirugia, OrdenEstudio, ServicioAmbulancia,
                        # Alergia, Antecedente, SignosVitales (expediente clínico completo, 3/3 — sección 8)
    Http/Controllers/
      FacturaPdfController.php   # Exporta factura a PDF (dompdf)
    Filament/
      Concerns/
        HasBackFormAction.php    # Trait: botón "Atrás" en vez de "Cancelar" en pantallas de Editar
      Resources/                 # Un Resource completo (Form+Table+Pages) por cada modelo de arriba,
                                  # excepto SignosVitales (embebido en HistoriaClinicaForm/Infolist, sin nav propia)
        Pacientes/RelationManagers/
          AlergiasRelationManager.php       # Tab "Alergias" dentro de Editar Paciente
          AntecedentesRelationManager.php   # Tab "Antecedentes" dentro de Editar Paciente
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
      partials/
        nav.blade.php
        footer.blade.php
      sections/                           # las 5 secciones del sitio público, completas (28 ago 2026)
        hero.blade.php
        especialidades.blade.php          # directorio de las 27 especialidades reales
        servicios.blade.php               # mosaico (bento grid) con las 5 fotos reales
        sobre.blade.php                   # "Sobre nosotros" (#nosotros)
        contacto.blade.php                # sin formulario, con mapa embebido
      especialidad.blade.php              # página individual /especialidades/{slug} (contenido genérico Unsplash, pendiente entrevista)
      home.blade.php                      # compone layout+nav+5 secciones+footer
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

# Expediente clínico completo — módulo 1 de 3 (31 ago 2026)
alergias — paciente_id, alergeno, tipo (medicamento/alimento/otro), severidad (leve/moderada/severa), reaccion, notas

# Expediente clínico completo — módulo 2 de 3 (31 ago 2026)
antecedentes — paciente_id, categoria (personal/quirurgico/familiar/habito), descripcion, notas
pacientes.grupo_sanguineo — columna nueva agregada a `pacientes` (nullable): dato único del paciente, no una lista categorizada, por eso no vive en `antecedentes`

# Expediente clínico completo — módulo 3 de 3 (01 sep 2026)
signos_vitales — historia_clinica_id (unique, 1 a 1), presion_arterial, temperatura, frecuencia_cardiaca, frecuencia_respiratoria, peso, talla, saturacion_oxigeno, notas. A diferencia de alergias/antecedentes, va por consulta, no por paciente.
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
- **Dashboard gerencial** (solo admin): 4 indicadores clave (ingresos del mes vs. mes anterior, por cobrar, citas atendidas hoy, ocupación de camas) como tarjetas presionables — al hacer clic abren un popover flotante (`x-anchor`/Floating UI, sin JS propio más allá de Alpine core) con el detalle real (montos, desgloses por área/tipo), cada línea con un marcador de diamante del mismo color semántico de la tarjeta (`IndicadoresGerencialesWidget.php`, `atributosPopover()`/`valorConPopover()`, `theme.css`). Más 2 gráficos (ingresos por mes, facturación por área, con selectores de rango/métrica) y el widget de alertas operativas (lotes por vencer, facturas vencidas, camas ocupadas hace mucho). Todo confirmado funcionando en el entorno real.
- **Topbar del panel rediseñado**: fondo navy sólido (en vez del blanco/gris default de Filament), logo intercambiado a la variante blanca por CSS (`content: url(...)`, sin tocar el logo navy que sigue usándose en el drawer móvil/login), buscador global con placeholder específico ("Buscar pacientes, citas, médicos..."), y menú de usuario con nombre + rol + "Editar perfil" + "Cerrar sesión" (`->userMenuItems()` en `AdminPanelProvider.php`). Página propia `/admin/profile` (`App\Filament\Pages\EditProfile extends Filament\Auth\Pages\EditProfile`) con el campo Rol de solo lectura agregado. Confirmado funcionando en el entorno real.
- Gestión de usuarios desde `/admin/users` (solo admin, con protección de autoborrado).
- Exportar Factura a PDF.
- Filtro "mis pacientes" para rol médico (vía `medico_id` en `users`) — `medico_id` ya asignado a los usuarios médico existentes desde `/admin/users`, **confirmado funcionando en el entorno real** (31 ago 2026).
- Branding aplicado: logo real de la clínica, color primario navy `#0C447C` (cambiado de `Color::Teal`, 25 ago 2026), favicon.
- UX pulida: botón "Atrás" en Editar, sidebar agrupado por categorías (con ítem activo en fondo navy sólido), buscador global en los 6 Resources, filtros rápidos en Citas, cambio de estado con un clic, theme propio (`theme.css`) para encabezado de tablas, fondo de página gris-azulado y sombra sutil en tarjetas/secciones para separar visualmente del contenido.

**Sitio público — las 5 secciones completas, rediseño del 28 ago 2026 cerrado:**
- Paleta: navy `#0C447C` + verde azulado `#0F6E56` (mismos colores del panel) + acento dorado/champán, exclusivo del sitio público.
- Tipografía: Space Grotesk (titulares) + Instrument Sans (cuerpo).
- Elemento distintivo: línea de pulso SVG animada (plano técnico + pulso dorado en loop), respeta `prefers-reduced-motion`.
- Video de fondo del hero (reemplazó a un slideshow de fotos): solución final aplicada — `hero.blade.php` agrega una imagen de glow detrás del video; `public.css` la amplía y desenfoca, deja `::after` sin fondo y aplica una máscara alfa transparente directamente al video y al poster para que los bordes dejen ver el fondo real. El video quedó ligeramente más grande y más abajo, con los breakpoints responsive ajustados. **Confirmado visualmente por el usuario**.
- **Las 5 secciones están completas**: Portada (Hero), Especialidades, Servicios, Sobre nosotros, Contacto + Footer (28 ago 2026, ver detalle en sección 7.1 abajo).
- **Página individual de especialidad**: se agregó la ruta `/especialidades/{slug}` con `EspecialidadController`, de modo que cada especialidad puede abrir una vista propia (`resources/views/especialidad.blade.php`) usando `Especialidades::find($slug)` y devolviendo 404 si no existe. La vista reutiliza los patrones del sitio público (`<x-layouts.public>`, `partials.nav`, `partials.footer`, `cb-footers-link`, `cb-btn-primary`, `cb-about-body`) y renderiza el SVG del ícono guardado en `icono_paths`.
- **Fix visual de la página individual**: se corrige el hueco superior excesivo de la sección de especialidad y el contraste del enlace "← Volver a especialidades" sobre fondo claro, sin tocar `app/Support/Especialidades.php` ni los datos. La solución usa una clase scoped en `resources/css/public.css` (única fuente de verdad, sin `<style>` inline duplicado) y un ajuste puntual de layout en `resources/views/especialidad.blade.php`, sin cambiar el estilo base del footer. El link de volver lleva `pt-16 sm:pt-20 lg:pt-24` en su contenedor para no quedar tapado por `.cb-nav` (que es `position: fixed`), y su clase de color está ubicada después de `.cb-footer-link` en `public.css` para ganar la cascada (misma especificidad, gana la que está más abajo en el archivo — ojo con este patrón si se agregan más overrides de `.cb-footer-link` a futuro).
- **Nav público con reveal por scroll hacia arriba**: el header fijo del sitio público se oculta al bajar y reaparece al subir, con una línea sutil de acento en la base. Se mantiene siempre visible si el usuario está en el tope de la página o si el menú móvil está abierto (fix aplicado tras detectar que el menú desaparecía a mitad de uso). El umbral de 6px para detectar dirección de scroll es simétrico (bajar/subir), sin rama catch-all, para evitar parpadeo con scroll de trackpad o rebote elástico de iOS. La lógica vive en `resources/views/partials/nav.blade.php` y la parte visual en `resources/css/public.css`.
- **Nav y footer compartidos usan `{{ url('/') }}` en sus anclas**: como ambos se incluyen en todas las páginas (no solo la home), sus links a `#inicio`/`#especialidades`/`#servicios`/`#nosotros`/`#contacto` (incluido el logo) llevan `{{ url('/') }}` antepuesto — si no, desde páginas como `/especialidades/{slug}` no navegaban a ningún lado. Tenerlo presente si se agrega un link de este tipo en el futuro (footer, nav, o cualquier partial compartido).
- **Fix del bug de `Str::slug()` en la clase de especialidades**: la causa raíz del 500 era inicializar `private static array $especialidades` con expresiones dinámicas (`Str::slug(...)`), algo que PHP no permite en propiedades estáticas. La lista original de 27 nombres se conserva igual, y el cálculo del `slug` se mueve a un método privado para evitar la validación de constante inválida.
- **Contenido real en la página individual de especialidad (31 ago 2026)**: reemplaza el placeholder "Estamos preparando el detalle..." por contenido de producción para demo/propuesta (info genérica de internet, no específica de la clínica — aún no hubo entrevista con el dueño). Comando `especialidades:fetch-fotos` (`app/Console/Commands/FetchEspecialidadesFotos.php`) consulta la API de Unsplash y genera `resources/data/especialidades-fotos.json` con un array `fotos` (hasta 3 elementos por especialidad — `per_page=3`, no todas llegan a 3 según lo que devuelve Unsplash para cada término) con `foto_url`/`foto_autor`/`foto_autor_url` (con `utm_source=clinica_benites` en el link del autor, obligatorio por términos de Unsplash) para las 27 especialidades — `cuidados-criticos`/`terapia-intensiva` comparten foto a propósito por ser conceptualmente lo mismo. `Especialidades::all()`/`find()` mezclan ese JSON por slug (fallback a array vacío si falta) y además siguen llenando `foto_url`/`foto_autor`/`foto_autor_url` con el primer elemento de `fotos` (compatibilidad). Cada especialidad tiene además `descripcion` (1 oración), `que_tratamos` (array de bullets) y `cuando_consultar` (string), todo genérico. `especialidad.blade.php` quedó con layout de 2 columnas (`max-w-6xl`): izquierda ícono + foto principal con crédito a Unsplash + grilla secundaria con las fotos extra (solo si hay más de 1); derecha eyebrow/título/descripción, bloque "Qué tratamos" (lista con check verde) y bloque "Cuándo consultar" (callout con título, mismo estilo que "Qué tratamos"), botón de WhatsApp. Debajo, sección fija "Proceso de atención" (4 pasos genéricos, mismo texto en las 27 páginas, vive directo en el blade) y "Especialidades relacionadas" (3 tarjetas a las siguientes especialidades del array por posición, dando la vuelta al principio si hace falta, sin relación clínica real — requiere `EspecialidadController.php`, que calcula las relacionadas con `Especialidades::all()` pero sigue usando `Especialidades::find($slug)` para la especialidad principal, necesario para preservar `icono_paths`). Fondo decorativo `cb-hero-grid` (mismo patrón que Hero/Servicios/Contacto), envuelto en `overflow-hidden` para no romper el scroll-reveal.
- **Rediseño (28 ago 2026) cerrado**: Especialidades, Servicios y Contacto recibieron cada uno un elemento propio grounded en contenido real (nunca decoración porque sí); Sobre nosotros se revisó y se dejó sin cambios a propósito (es la sección "pausa" del sitio); y el fix de `.cb-reveal` → `animation-timeline: view()` (scroll-driven, CSS puro, scoped a las 4 secciones — no al Hero) resuelve que el fade-up se dispare al llegar a cada sección scrolleando, no solo al cargar la página. Detalle completo en `docs/PLAN_SITIO_PUBLICO.md` sección 6 y en sección 7.1 abajo.
- **Nombre de la clínica en el Hero (28 ago 2026)**: "Clínica Benites" solo vivía como `alt` del logo en el nav — el eyebrow arriba del titular pasó de "Clínica privada · Guayaquil" a "Clínica Benites · Guayaquil" (se descartó agregarlo como línea propia en el `<h1>`, primer intento).
- **Copy sin rayas (28 ago 2026)**: a pedido del usuario, el texto visible del sitio público no usa em-dash (—) — se reescribió con coma/dos puntos donde aparecía (lede de Sobre nosotros, lede de Contacto, meta description). Los comentarios de desarrollo (no visibles) y el separador de listas del panel admin no se tocaron.
- **Video del Hero (28 ago 2026)**: confirmado por el usuario en su entorno real que se veía demasiado tenue — se subió la opacidad final (0.72→0.88, `public.css`). Varias vueltas subiendo el núcleo sólido de la máscara con `ellipse farthest-corner` terminaron mostrando el problema real (captura del usuario): esa palabra clave ajusta el desvanecido a las esquinas, no a los bordes rectos del medio, que en una caja 4:3 quedan más cerca del centro — línea dura ahí sin importar el número. Fix intermedio: elipse con tamaño explícito (`ellipse 50% 50% at 50% 50%`, `black 62%, transparent 100%`) directo sobre `.cb-hero-video`/`.cb-hero-video-poster` — el desvanecido termina exactamente en el borde real en los 4 lados por igual, esquinas transparentes automáticamente. **Fix real definitivo (misma fecha, última vuelta del día)**: aun así, en el entorno real con el video reproduciéndose (no la foto de respaldo) el desvanecido seguía sin verse — causa: `mask-image` no es confiable sobre elementos `<video>` en todos los navegadores/GPUs (con decodificación acelerada por hardware, el navegador puede pintar los frames reales del video por una vía que ignora el `mask-image` del contenedor, aunque sí lo respeta sobre una imagen estática — por eso nunca se detectó en el sandbox, que no reproduce `.mp4` en vivo). Se agregó un pseudo-elemento `.cb-hero-slideshow::after` que pinta ENCIMA del video (overlay, no recorte) un degradado radial transparente en el centro y navy sólido en los bordes — técnica más vieja y compatible, sin depender de que el navegador aplique `mask` sobre `<video>`. Las dos técnicas conviven hoy en el código (máscara alfa en video/poster + overlay `::after` en el contenedor), no son excluyentes. **Confirmado por el usuario en su entorno real con el video reproduciéndose** (31 ago 2026, captura de `localhost` mostrando el hero con los bordes del video desvanecidos hacia el navy, sin marco recto visible).
- **Fix de `.cb-hero-content` sin tope en viewports altos (31 ago 2026)**: `min-h-screen` (Tailwind) no tenía techo, dejando espacio en blanco arriba/abajo del contenido del Hero en zoom bajo (viewport con altura lógica grande). Se sacó `min-h-screen` de `hero.blade.php` y se agregó `min-height: min(100vh, 60rem)` a `.cb-hero-content` en `public.css`, sin tocar padding ni las compresiones `@media (max-height: 900px/700px)` ya existentes. **Confirmado por el usuario con medición real en consola** (`getBoundingClientRect().height`) en tres puntos: 100% zoom sin cambios, 50% y 25% con el tope de 960px aplicado correctamente, sin zona intermedia sin cubrir.
- **Pendiente**: reemplazar 6 placeholders de teléfono/WhatsApp (`593000000000`) antes de publicar — nav (2), hero (2), contacto (2). Confirmar en entorno real (no solo Playwright/capturas) que el mosaico de Servicios, el directorio de Especialidades y el nuevo scroll-reveal se ven bien con las fuentes reales cargadas (acá se verificó contra una build estática de Tailwind, sin poder correr `vite build` completo por falta de red a `fonts.bunny.net` en este sandbox).

### 7.1 Detalle: secciones nuevas del sitio público (28 ago 2026)

- **Especialidades** (`sections/especialidades.blade.php`): directorio editorial de las 27 especialidades reales (`AreaSeeder.php`, única fuente), alfabetizado en 3 columnas — se descartó agrupar por categoría (quirúrgica/clínica/diagnóstico) por no venir confirmada esa taxonomía en el material del cliente. 4 rondas de feedback el mismo día (28 ago 2026, ver historial completo en el comentario del propio archivo Blade): rediseño inicial con rail/marcador de letras; referencia visual del usuario (mobbin.com) que sumó el "27" en primer plano y reemplazó el rail por letras dispersas en los márgenes; feedback directo ("quítale el alfabeto") que sacó todo el tratamiento de letras; y por último ícono por especialidad + fondo blanco. Estado actual: fondo `cb-section--light` (mismo tratamiento que "Sobre nosotros"), watermark "27" de fondo + cifra grande en primer plano (`cb-stat-callout`) + divisores verticales entre columnas + línea dorada en hover, y un ícono de trazo fino por fila agrupado por sistema/órgano clínico real (20 íconos para 27 filas — ej. Cardiología/Cateterismo Cardiaco comparten corazón). Verificado con preview HTML renderizado vía Playwright (CSS real compilado con `vite build`, sin la fuente Bunny por falta de red en el sandbox) antes de cerrar el cambio. Cada fila del directorio ahora es un link a `/especialidades/{slug}` (31 ago 2026).
- **Servicios** (`sections/servicios.blade.php`): mosaico (bento grid) con las 5 fotos reales que ya estaban en `public/images/hero-*.jpg` pero solo se usaba una (quirófano) en el hero — ahora las 5 tienen uso. Tile de Quirófanos como ancla (2×2), franja final de "ambulancia propia" (dato ya confirmado, no nuevo). Rediseño: se quitó el `cb-orb-teal` (color plano) y se reemplazó por `cb-hero-grid` con máscara propia + glow ambiental con la foto real de Quirófanos (`.cb-services-glow`).
- **Sobre nosotros** (`sections/sobre.blade.php`, id `#nosotros`): única sección clara (ivory) del sitio, sin fotografía — bloque tipográfico + 3 pilares, todos datos ya confirmados en otras partes del sitio (nada nuevo inventado). Revisada en el rediseño y dejada sin cambios a propósito: es la sección "pausa", documentada así desde antes.
- **Contacto** (`sections/contacto.blade.php`): sin formulario (ya confirmado: no se agenda desde la web) y sin dirección/horario exacto — no están confirmados por el cliente, no se inventaron. **Sí tiene mapa embebido** (28 ago 2026): coordenadas reales confirmadas por el usuario contra Google Maps, pero horario/teléfono de ese listado no se usan (negocio sin reclamar en Google, no confiable). Rediseño: ícono de pulso/ECG (reemplaza al pin de ubicación duplicado que tenía el panel de emergencias) con un pulso sutil de opacidad (`.cb-pulse-fade`).
- **Footer** (`partials/footer.blade.php`): nuevo, logo + nav + WhatsApp + copyright.
- `nav.blade.php` y `home.blade.php` actualizados para conectar las 4 secciones + footer, en orden. Se agregó el link "Nosotros" al nav (desktop y móvil).
- CSS nuevo agregado como bloque propio al final de `public.css` (no intercalado en las reglas del hero, para no arriesgar lo ya confirmado) — reutiliza toda la paleta/tipografía ya definida, sin tokens nuevos.
- **Fix de scroll-reveal (31 ago 2026)**: tras descubrir en verificación visual que el fade-up no funcionaba (elementos `.cb-reveal` visibles en `opacity: 1` fuera de viewport en lugar de `opacity: 0`), se investigó y se encontró la **causa raíz**: `overflow-hidden` en el `<section>` padre altera el contenedor de referencia de `animation-timeline: view()` (Chromium 148 + `@supports (animation-timeline: view())`). **Fix**: se quitó `overflow-hidden` de los 4 `<section>` y se envolvió únicamente el decorativo (watermark "27" + grid en Especialidades; grid + glow en Servicios; grid + orb en Contacto; nada en Sobre nosotros) en un `<div class="overflow-hidden">` nuevo — el recorte visual permanece idéntico, pero `animation-timeline: view()` ahora funciona correctamente (fade-up empieza en `opacity: 0` fuera de vista y transiciona al entrar). Archivos tocados: `especialidades.blade.php`, `servicios.blade.php`, `sobre.blade.php`, `contacto.blade.php`. Verificado: transición correcta en todos los checkpoints de scroll, watermarks recortados sin desbordarse, sin regresiones visuales.
- Anterior (28 ago 2026, implementación de `.cb-reveal` → `animation-timeline: view()`): fade-up scoped a `.cb-section .cb-reveal` para no afectar al Hero (`.cb-hero`), que mantiene su fade-up escalonado al cargar intacto (es el "momento audaz" ya confirmado del sitio; el fix habría roto esa experiencia de primer frame). Ver comentario en `public.css`.

## 8. Funciones futuras propuestas (investigadas, no priorizadas ni construidas)

- **Expediente clínico completo — alcance confirmado por el cliente** (entrevista 25 ago 2026, Ysrael Calle): al preguntarle directamente, confirmó que "digitalizar la mayor parte del historial clínico" significa un expediente completo (antecedentes, alergias, signos vitales, resultados de exámenes, todo conectado) — no solo el `diagnostico`/`tratamiento`/`notas` en texto libre que hoy tiene `HistoriaClinica`. Resultados de exámenes con archivo adjunto **ya está cubierto** por `OrdenEstudio` (no hace falta módulo nuevo ahí). Orden de construcción: alergias → antecedentes → signos vitales (seguridad del paciente primero; los 2 primeros son más simples al ser "por paciente" en vez de "por consulta").
  - **(1) Alergias — construido y confirmado en el entorno real (31 ago 2026)**: modelo `Alergia` (paciente_id, alergeno, tipo [medicamento/alimento/otro], severidad [leve/moderada/severa], reaccion, notas). `AlergiaResource` con CRUD completo (nav propia, grupo "Atención al paciente") + `AlergiasRelationManager` como tab dentro de Editar Paciente (así queda "destacado en la ficha del paciente", no escondido en una nota) + sección destacada de solo lectura en el infolist de Historia Clínica cuando el paciente tiene alergias registradas, más un aviso en vivo (`Placeholder` reactivo a `->live()` en el Select de paciente) al crear/editar una Historia Clínica. **Confirmado por el usuario probando en vivo**: pantalla de Crear Alergia, tab de Alergias en Editar Paciente y el aviso en Historia Clínica (el `Placeholder` del formulario), todo funcionando. **Corrección (01 sep 2026)**: lo que **no** se había probado en esa confirmación era la pantalla "Ver" de Historia Clínica (el infolist) — recién se probó al construir Signos vitales y salió un error real (`Class "Filament\Infolists\Components\Section" not found`, namespace incorrecto desde que se escribió el infolist). Bug de código, no de lógica: corregido, ver entrada del 01 sep 2026 en CHANGELOG.md. Permisos: **se aplicó por decisión propia el mismo criterio que Historia Clínica** (admin + médico ven/editan, solo admin elimina, recepción sin acceso) — no es una decisión confirmada por el cliente, sigue pendiente extender la matriz de la sección 10 formalmente para este módulo (ver nota en `AlergiaResource`). Seeder de demo (`DemoHistoricoSeeder::crearAlergias()`) agrega alergias a un subconjunto de pacientes para poder ver la función funcionando sin cargar datos a mano.
  - **(2) Antecedentes — construido y confirmado en el entorno real (01 sep 2026)**: modelo `Antecedente` (paciente_id, categoria [personal/quirúrgico/familiar/hábito], descripcion, notas) — mismo patrón que Alergias: `AntecedenteResource` con CRUD completo (nav propia, grupo "Atención al paciente") + `AntecedentesRelationManager` como tab dentro de Editar Paciente. `grupo_sanguineo` **no** vive en `antecedentes` (es un dato único del paciente, no una lista categorizada) — se agregó como columna directa en `pacientes` y como campo (`Select`, 8 opciones + vacío) en `PacienteForm`/`PacientesTable` (columna oculta por defecto). Permisos: mismo criterio que Alergias por analogía (admin + médico ven/editan, solo admin elimina, recepción sin acceso) — sigue sin confirmar con el cliente (ver sección 10). Seeder de demo (`DemoHistoricoSeeder::crearAntecedentes()`) agrega 1-3 antecedentes a un subconjunto de pacientes, y `crearPacientes()` ahora asigna `grupo_sanguineo` a la mayoría (a propósito deja algunos sin confirmar, `null`). **Confirmado por el usuario probando en vivo**: pantalla de Crear Antecedente, tab de Antecedentes en Editar Paciente, campo Grupo sanguíneo en la ficha del paciente, y permisos por rol (recepción sin acceso, médico ve solo sus pacientes, eliminar solo admin), todo funcionando.
  - **(3) Signos vitales — construido y confirmado en el entorno real (01 sep 2026)**: a diferencia de Alergias y Antecedentes, va **por consulta** (1 a 1 con `HistoriaClinica`), no por paciente — así que no tiene Resource/nav propia: se integró directo en `HistoriaClinicaForm` (`Section` con `->relationship('signosVitales')`, Filament carga/guarda los campos contra el registro relacionado automáticamente) y en `HistoriaClinicaInfolist` (sección de solo lectura, oculta si no se cargó nada). Modelo `SignosVitales` (historia_clinica_id único, presion_arterial, temperatura, frecuencia_cardiaca, frecuencia_respiratoria, peso, talla, saturacion_oxigeno, notas) — todos los campos nullable, no todas las consultas miden los 7. Hereda los permisos de `HistoriaClinicaResource` (no tiene los suyos propios, al no ser un recurso independiente). Seeder de demo (`DemoHistoricoSeeder::crearSignosVitales()`) agrega signos vitales a 3 de cada 4 historias clínicas de demo (a propósito deja algunas sin datos). Verificado contra el código fuente real de `filamentphp/filament` v5.7.6 (clonado desde GitHub en el sandbox) que el patrón `->relationship()` sobre `Section` es válido para relaciones singulares tipo `HasOne` — esa misma verificación destapó y permitió corregir el bug de namespace descrito en CHANGELOG.md (01 sep 2026). **Confirmado por el usuario probando en vivo**: pantalla "Ver" sin sección cuando no hay datos, sección "Signos vitales" visible y editable en Editar, guardado correcto (fila creada en `signos_vitales`) y visible después en "Ver" — todo funcionando.
  - **Los 3 módulos del expediente clínico completo (Alergias, Antecedentes, Signos vitales) están construidos y confirmados en el entorno real (01 sep 2026).**
  - Con Signos vitales construido, **los 3 módulos del expediente clínico completo están construidos** — todos pendientes de confirmación en el entorno real y de la matriz de permisos formal con el cliente (sección 10).
  - Preguntas sin resolver que quedaron abiertas al construir Alergias (aplican a los 3 módulos): si un registro corregido debe conservar historial de cambios o editarse directo, y la matriz de permisos formal de la sección 10.
- **Inventario multi-área** (misma entrevista): el cliente pidió que el registro de insumos cubra farmacia, quirófano, admisión y facturación (4 áreas, no solo farmacia) — el modelo ya soporta esto a nivel de dato (`area_origen`/`area_destino`), pero falta confirmar si es un inventario compartido entre las 4 o si cada una maneja el suyo (depende de la misma pregunta pendiente sobre el mecanismo real de farmacia, sección 6).
- **Prescripciones (2027)**: sigue sin construirse. Falta confirmar si el médico prescribe solo lo que existe en `items_inventario`, o también medicamentos que el paciente compra afuera de la clínica — define si se vincula o no al inventario.
- **Marco legal ecuatoriano aplicable** (investigación 24 ago 2026, no implica cambio de código inmediato): LOPDP clasifica los datos de salud como "dato sensible" (consentimiento explícito en general, con excepción para instituciones/profesionales de salud tratando datos de sus propios pacientes) y establece "protección de datos desde el diseño". Además, **Acuerdos Ministeriales del MSP (1190-2012, 0009-2017 y su reglamento de 2017) obligan al estándar HL7 para historia clínica electrónica, para instituciones de salud tanto públicas como privadas en Ecuador** — no es opcional a futuro, aplica directo cuando se construya el expediente clínico completo de arriba. Cumplimiento LOPDP: registro de consentimiento del paciente + auditoría de accesos a datos de salud (candidato: `spatie/laravel-activitylog`).
- Recordatorios de cita por WhatsApp/SMS, portal de autoagendamiento — **descartados explícitamente por ahora**, fase futura.
- Reportes/KPIs adicionales, lista de espera automática al cancelar cita, recall/control preventivo, encuesta de satisfacción post-visita, ficha de "paciente frecuente", turno virtual sin cita, panel ejecutivo ampliado.

Detalle completo de cada propuesta en `docs/historico/MEMORIA_2026-08-23_2026-08-28.md`, sección 9 (funciones futuras) y sección 6.5 (expediente clínico, preparación de entrevista).

## 9. Decisiones técnicas de referencia (para no repetir investigación)

- **Tablas de Filament no permiten juntar título+buscador en una sola fila de forma nativa** — se resolvió con CSS scoped en `theme.css` (`.fi-ta-header`/`.fi-ta-header-toolbar`), no forkeando el Blade del paquete.
- **Botones de acción (Crear/Editar/Eliminar) no se ocultan solos según permisos** — hay que conectar `->visible()` a mano a `canCreate()`/`canEdit()`/`canDelete()` en cada Resource. Ya aplicado en los 18 puntos correspondientes.
- **Un campo oculto con `->visible()` en un formulario Filament sigue guardando su valor** — si depende de otro campo (ej. `medico_id` según `rol`), hay que limpiarlo a mano en `afterStateUpdated()` + `mutateFormDataBeforeSave()`.
- **`mask-image` no es confiable sobre elementos `<video>`** en todos los navegadores/GPUs (con decodificación acelerada por hardware, el navegador puede pintar los frames reales por una vía que ignora la máscara del contenedor) — solo se detecta con el video corriendo de verdad, no contra una imagen estática de respaldo. Para desvanecer los bordes de un `<video>` de forma confiable, combinar máscara alfa directa sobre el video/poster **con** un `::after` overlay (degradado radial sólido pintado encima, no recorte) sobre el contenedor — no alcanza con una sola de las dos técnicas. El glow desenfocado detrás suaviza la transición en ambos casos.
- **Para topar un `min-height: 100vh` sin romper el rango normal, usar `min-height: min(100vh, Xrem)` directo en la regla, no un `@media (min-height: Ypx)` con `!important`** — la versión con `@media` solo activa el tope por encima de un umbral fijo, dejando sin cubrir cualquier viewport entre el rango normal ya confirmado y ese umbral (zona reproducible con zoom intermedio, ej. 50%), y además genera un salto brusco justo en el borde del umbral en vez de una transición continua. `min()` cubre todo el rango de una sola regla, sin casos intermedios sin probar.
- Detalle y verificación de cada una de estas en el histórico si hace falta reconstruir el razonamiento completo.

## 10. Roles y permisos

| Recurso | Admin | Recepción | Médico |
|---|---|---|---|
| Áreas | Todo | Solo ver | Solo ver |
| Médicos | Todo | Solo ver | Solo ver |
| Pacientes | Todo | Todo | Ver y editar (sin eliminar) |
| Citas | Todo | Todo | Ver y editar (sin eliminar, sin crear) |
| Historias Clínicas | Todo | Sin acceso | Todo (eliminar solo admin) |
| Alergias *(no confirmado con el cliente)* | Todo | Sin acceso | Todo (eliminar solo admin) |
| Antecedentes *(no confirmado con el cliente)* | Todo | Sin acceso | Todo (eliminar solo admin) |
| Facturas | Todo | Todo | Sin acceso |
| Usuarios | Todo (excepto autoborrado) | Sin acceso | Sin acceso |

Médico ve solo "sus" registros (Citas, Historias Clínicas, Alergias, Antecedentes, Dashboard) vía `medico_id` vinculado en `users`. Si un usuario médico no tiene `medico_id` asignado, ve todo (diseño defensivo — mejor ver de más que bloquear).

**Alergias** y **Antecedentes**: filas agregadas al construir cada módulo (31 ago 2026) usando el mismo criterio que Historias Clínicas, por analogía (dato clínico sensible) — ninguna de las dos es una fila confirmada por el cliente todavía. Si en la entrevista formal se decide otra cosa (ej. que recepción sí necesite verlas por seguridad al agendar), ajustar `canViewAny()`/`canViewForRecord()` de ambos Resources y RelationManagers.

**Signos vitales** (01 sep 2026): no tiene fila propia en esta tabla porque no es un Resource independiente — vive embebido en Historias Clínicas (ver sección 8), así que hereda exactamente los mismos permisos de esa fila sin código de permisos propio.

## 11. Roadmap inmediato

1. Confirmar en entorno real el mosaico de Servicios, el directorio de Especialidades y el nuevo scroll-reveal (`.cb-reveal` → `view()`) con las fuentes reales cargadas (el video del hero ya está confirmado, 31 ago 2026 — ver sección 7).
2. Reemplazar placeholders de teléfono/WhatsApp antes de publicar (6 ocurrencias: nav, hero, contacto).
3. Entrevista formal con el dueño — resolver preguntas de la sección 6 (incluida confirmar dirección exacta/horario real para la sección Contacto — el mapa ya tiene coordenadas reales, pero horario/teléfono siguen sin confirmar).
4. Confirmar con el cliente la matriz de permisos de Alergias, Antecedentes y Signos vitales (sección 10, hoy es una decisión propia por analogía con Historias Clínicas). Con los 3 módulos del expediente clínico completo construidos y confirmados en el entorno real, no queda ningún módulo nuevo pendiente de diseño ni de probar — el resto son confirmaciones con el cliente.

## 12. Cómo mantener este archivo (a partir de ahora)

Para evitar que `MEMORIA.md`/`CHANGELOG.md` vuelvan a crecer sin control:

- **`CHANGELOG.md`**: cada entrada debe ser corta (qué cambió, en qué archivo, 2-4 líneas). No reconstruir el proceso completo de debugging/iteración (varias vueltas de prueba y error, mediciones, etc.) — eso puede vivir en el mensaje de commit o quedar implícito, no en el changelog.
- **`MEMORIA.md`**: refleja el **estado actual**, no la historia de cómo se llegó ahí. Si una decisión técnica no obvia vale la pena recordar (sección 9), una o dos líneas alcanzan.
- Cuando `MEMORIA.md` o `CHANGELOG.md` vuelvan a superar ~80-100KB cada uno, repetir este mismo proceso: archivar en `docs/historico/` con fecha, y arrancar una versión nueva condensada.

**Flujo de trabajo con el repo**: el usuario no da push directo. Claude clona el repo público, hace el cambio, commitea localmente usando la identidad de Git del usuario (`user.name = isra16class-byte`, `user.email = isra16class@gmail.com`), y genera un patch con `git format-patch -1 HEAD` como archivo descargable. El usuario lo aplica con `git am nombre-del-patch.patch` (conserva autor y mensaje) y hace el `git push` él mismo. Nunca asumir que Claude tiene o debe pedir acceso de escritura directo al repo remoto.
