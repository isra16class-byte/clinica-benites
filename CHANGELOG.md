# Changelog

Historial completo previo al 28 de agosto de 2026 (23-28 ago): ver `docs/historico/CHANGELOG_2026-08-23_2026-08-28.md`.

Formato de entrada a partir de ahora: corta y al grano — qué cambió, en qué archivo(s), 2-4 líneas. El detalle de investigación/depuración vale más en el mensaje de commit que acá.

## [2026-08-31] Fix: anclas del nav/footer compartido no funcionaban fuera de la home

`partials/nav.blade.php` y `partials/footer.blade.php` se incluyen en todas las páginas (no solo en `/`), pero sus links usaban anclas relativas (`#inicio`, `#especialidades`, etc.) que solo funcionan estando ya en la home — desde `/especialidades/{slug}` no navegaban a ningún lado, solo intentaban hacer scroll a un id inexistente en la página actual. Se antepuso `{{ url('/') }}` a los 11 links del nav (logo + 5 desktop + 5 mobile) y a los 6 del footer (logo + 5), para que siempre naveguen a la home primero y después hagan scroll a la sección. Sin cambios de estilo ni de estructura.

## [2026-08-31] Fix: nav con scroll — menú móvil se ocultaba abierto + parpadeo por umbral asimétrico

Dos bugs en el script de `partials/nav.blade.php` que oculta/muestra el nav según dirección de scroll (agregado el mismo día, ver entrada de "Nav público" más abajo): (1) el menú móvil vive dentro del `<header>` que se oculta, así que si el usuario scrolleaba con el menú abierto, el menú entero desaparecía a mitad de uso — se agregó una condición `menuOpen` (lee `#cb-nav-toggle:checked`) que mantiene el nav visible mientras el menú está abierto. (2) el umbral de 6px para detectar "bajando" no tenía equivalente para "subiendo" (cualquier decremento de scroll de 1px mostraba el nav), lo que podía causar parpadeo con scroll de trackpad o rebote elástico de iOS — se agregó `scrollingUp` simétrico con el mismo umbral, sin rama `else` catch-all.

## [2026-08-31] Especialidad individual: fix de espaciado bajo el nav fijo + orden de cascada CSS

Dos ajustes de seguimiento sobre el fix visual anterior: el link "← Volver a especialidades" quedaba tapado por `.cb-nav` (que es `position: fixed`, no empuja contenido) — se agregó `pt-16 sm:pt-20 lg:pt-24` al contenedor de contenido en `especialidad.blade.php`. Aparte, `.cb-specialty-return-link` y `.cb-footer-link` tenían la misma especificidad CSS y `.cb-footer-link` (pensada para el footer oscuro) estaba definida después en `public.css`, así que ganaba y el link volvía a verse casi invisible — se reordenó `.cb-specialty-return-link` para que quede después de `.cb-footer-link` en el archivo. También se eliminó un `<style>` inline duplicado que había quedado en `especialidad.blade.php` con valores de padding distintos a los de `public.css` (fuente de verdad ahora única, solo en `public.css`).

## [2026-08-31] Nav público: reveal por scroll hacia arriba + barra sutil

Se ajusta la navegación del sitio público para ocultarse al bajar y reaparecer al subir, con una barra de acento muy discreta en la parte inferior del header. La lógica se mantiene en `resources/views/partials/nav.blade.php` y los estilos en `resources/css/public.css`, sin tocar contenido ni datos. La barra queda como detalle de marca y no tapa el contenido del primer bloque debajo del nav.

## [2026-08-31] Especialidad individual: espacio superior y contraste del botón de volver

Se corrigen dos issues visuales en `resources/views/especialidad.blade.php` y `resources/css/public.css`: se reduce el espacio excesivo arriba de la primera sección y el link "← Volver a especialidades" pasa a tener contraste suficiente sobre fondo claro sin tocar la base `.cb-footer-link` del footer real. La clase de la página se deja scoped para no afectar al resto del sitio público.

## [2026-08-31] Especialidades: galería de hasta 3 fotos, "Proceso de atención" y "Especialidades relacionadas"

`especialidades:fetch-fotos` pasa a pedir `per_page=3` a Unsplash y guarda un array `fotos` (hasta 3 elementos, no todas las especialidades llegan a 3 según lo que devuelve Unsplash) en `especialidades-fotos.json`. `Especialidades.php` expone `fotos` completo y sigue llenando `foto_url`/`foto_autor`/`foto_autor_url` con el primer elemento (compatibilidad). `especialidad.blade.php` suma una grilla secundaria con las fotos extra (si hay más de 1), una sección fija "Proceso de atención" (4 pasos genéricos, mismo texto en las 27 páginas, vive directo en el blade) y "Especialidades relacionadas" (3 tarjetas a las siguientes especialidades del array por posición, dando la vuelta al principio si hace falta, sin relación clínica real). `EspecialidadController.php` calcula las relacionadas con `Especialidades::all()` pero sigue usando `Especialidades::find($slug)` para la especialidad principal (necesario para preservar `icono_paths`, que solo `find()` agrega). Sin cambios en `Especialidades.php` fuera de exponer `fotos`.

## [2026-08-31] Especialidades: layout de 2 columnas con "Qué tratamos" y "Cuándo consultar"

`app/Support/Especialidades.php` reemplaza la descripción larga por 3 campos: `descripcion` (1 oración), `que_tratamos` (array de bullets) y `cuando_consultar` (string), genéricos como siempre. `especialidad.blade.php` pasa de 1 a 2 columnas (`max-w-6xl`): ícono+foto a la izquierda, texto+bullets+callout a la derecha. El callout de "Cuándo consultar" recibió su título después de un ajuste de feedback (mismo estilo que "Qué tratamos"). Sin cambios en `fotos()`/`all()`/`find()`.

## [2026-08-31] Especialidades: descripciones expandidas + fondo decorativo en la página individual

Las 27 descripciones de `app/Support/Especialidades.php` pasan de una frase a 2-3 oraciones (qué trata, condiciones comunes, cuándo consultar), mismo criterio genérico de siempre. Se agrega el fondo decorativo `cb-hero-grid` (ya usado en Hero/Servicios/Contacto) a `especialidad.blade.php`, envuelto en `overflow-hidden` para no interferir con el scroll-reveal del sitio. Sin cambios en `fotos()`/`all()`/`find()`.

## [2026-08-31] Especialidades: descripciones reales + fotos de Unsplash en la página individual

Se agrega el comando `especialidades:fetch-fotos` que genera `resources/data/especialidades-fotos.json` (foto + autor + link de Unsplash) para las 27 especialidades. `app/Support/Especialidades.php` gana el campo `descripcion` por especialidad y mezcla el JSON de fotos por slug en `all()`/`find()`. `especialidad.blade.php` reemplaza el placeholder "Estamos preparando el detalle..." por la descripción real y muestra la foto con crédito a Unsplash. Contenido genérico para demo/propuesta, no específico de la clínica (pendiente entrevista con el dueño).

## [2026-08-31] Fix: slug de especialidades generado fuera de la propiedad estática

Se corrige la causa raíz del error 500 en `app/Support/Especialidades.php`: `Str::slug()` no puede ejecutarse dentro de una propiedad estática inicializada con un array en PHP. La lista original de 27 especialidades se mantiene igual y el cálculo del `slug` se mueve a un método privado, sin cambiar los nombres ni los iconos.

## [2026-08-31] Especialidades: filas del directorio enlazan a su página individual

Cada fila de `especialidades.blade.php` pasa de `<div>` a `<a href>`, apuntando a la ruta `especialidades.show` con el slug generado desde el nombre (`Str::slug`). Se agrega `text-decoration: none` a `.cb-directory-row` en `public.css` para que el navegador no subraye el texto por defecto al ser ahora un link.

## [2026-08-31] Vista única de especialidad

Se agrega la página individual de detalle por slug para cada especialidad usando `Especialidades::find($slug)`, con 404 si no existe. La ruta nueva es `/especialidades/{slug}` y la vista reutiliza el layout público y el patrón visual del sitio (`partials.nav`, `partials.footer`, `cb-footer-link`, `cb-btn-primary`, `cb-about-body`). Archivos tocados: `app/Support/Especialidades.php`, `app/Http/Controllers/EspecialidadController.php`, `routes/web.php`, `resources/views/especialidad.blade.php`.

## [2026-08-31] Fix scroll-reveal: `overflow-hidden` en decorativos, no en `<section>`

El scroll-reveal de las 4 secciones (Especialidades, Servicios, Sobre nosotros, Contacto) no funcionaba: los elementos `.cb-reveal` estaban visibles en `opacity: 1` fuera del viewport en lugar de mantener `opacity: 0` y transicionar al entrar. **Causa raíz**: `overflow-hidden` en el `<section>` padre altera el contenedor de referencia de `animation-timeline: view()`, haciendo que calcule la entrada/salida contra el contenedor en lugar del viewport. **Fix**: se quitó `overflow-hidden` de los `<section>` y se envolvió únicamente el decorativo (`cb-hero-grid` + `cb-directory-watermark` en Especialidades; `cb-hero-grid` + `cb-services-glow` en Servicios; `cb-hero-grid` + `cb-orb` en Contacto; nada en Sobre nosotros por no tener decorativos) en un `<div class="overflow-hidden">` nuevo. El recorte visual se mantiene igual, pero ahora `animation-timeline: view()` funciona correctamente — el fade-up empieza en `opacity: 0` fuera de vista y transiciona suavemente al entrar. Archivos tocados: `resources/views/sections/especialidades.blade.php`, `servicios.blade.php`, `sobre.blade.php`, `contacto.blade.php`.

## [2026-08-28] Contacto: mapa embebido con ubicación real

El usuario encontró el listado real de "Clínica Benites" en Google Maps y confirmó las coordenadas (-2.142825, -79.9050583). Se agrega un `<iframe>` de Google Maps (sin API key, `output=embed`) debajo del grid de 2 columnas en `contacto.blade.php`, con estilos nuevos en `public.css` (`.cb-contact-map`, filtro sutil para acercarlo a la paleta navy del sitio). **Ojo**: el listado de Google está sin reclamar por el negocio ("Reclamar este negocio" visible en el panel) — solo se usan las coordenadas, no el horario ni el teléfono que muestra ahí, que siguen sin confirmar con el dueño real (mismo criterio de siempre: nada se publica sin confirmar).

## [2026-08-28] Hero: fix real del desvanecido — elipse con tamaño explícito, no `farthest-corner`

El usuario mandó captura: con `ellipse farthest-corner`, el desvanecido solo cubría bien las esquinas — los bordes rectos del medio (arriba/derecha en la caja de 4:3) se veían como línea dura sin importar qué tan alto se pusiera `black`, porque geométricamente quedan más cerca del centro que las esquinas. No era un problema de calibrar el número, era el método. Fix: `radial-gradient(ellipse 50% 50% at 50% 50%, ...)` con tamaño explícito en vez de la palabra clave — así el degradado termina exactamente en el borde real en los 4 lados por igual, y las esquinas (más lejos que ese 100%) quedan transparentes automáticamente. `black 62%, transparent 100%`.

## [2026-08-28] Hero: video aún más visible (tercera vuelta)

Mismo pedido del usuario repetido. `black` sube de 58% a 66%, `transparent` sigue fijo en 78% (margen de desvanecido angostado a 12 puntos — el mínimo razonable antes de arriesgar que reaparezcan bordes duros).

## [2026-08-28] Hero: video aún más visible (segunda vuelta, mismo criterio)

A pedido del usuario, tras confirmar que el fix anterior funcionó. Mismo principio: `transparent` se deja fijo en 78% (el punto que mantiene los bordes ocultos) y se sigue agrandando el núcleo sólido, ahora de `black 40%` a `black 58%`.

## [2026-08-28] Hero: corrección — bordes del video visibles tras el ajuste anterior

El ajuste anterior (ensanchar la máscara a `black 32%, transparent 92%`) provocó que los bordes medios del video se vieran como línea dura — el punto `transparent` de 78% no era arbitrario (validado con medición del canal alfa en rondas previas): es el mínimo necesario para que el desvanecido termine antes de los bordes medios del rectángulo (más cerca del centro que las esquinas, en un radial-gradient "farthest-corner" sobre una caja no cuadrada). Fix: `transparent` vuelve a 78%, y en su lugar se agranda el núcleo sólido (`black` de 18% a 40%) para mostrar más video nítido en el centro sin tocar el punto ya validado.

## [2026-08-28] Hero: video más visible (menos lavado)

El usuario confirmó en su entorno real que el video se veía demasiado tenue/borroso — las verificaciones anteriores fueron contra la foto de respaldo estática, no contra el video corriendo. Dos ajustes en `public.css`: la máscara radial del `<video>` pasa de `black 18%, transparent 78%` a `black 32%, transparent 92%` (se ve más video antes de que arranque el desvanecido hacia los bordes) y la opacidad final del keyframe `cb-hero-photo-fade-in` sube de 0.72 a 0.88. Sigue sin llegar a opaco del todo — el criterio de "video de fondo, no tarjeta pegada al título" no cambia, solo cuánto se ve.

## [2026-08-28] Copy público: se sacan las rayas (—) del texto visible

A pedido del usuario ("se ve como IA"). Se reescribieron sin em-dash las 3 frases del sitio público que lo usaban: el lede de "Sobre nosotros" (`sobre.blade.php`, "atención — no uno a costa del otro" → "atención, sin que uno vaya a costa del otro"), el lede de "Contacto" (`contacto.blade.php`, "por teléfono — sin pasos de más" → "por teléfono, sin pasos de más") y el `<meta name="description">` (`components/layouts/public.blade.php`, cambia a dos puntos). No se tocaron los comentarios de desarrollo (uso interno, no visibles en la web) ni el separador `—` del panel admin (`alertas-operativas-widget.blade.php`, es un patrón de lista distinto).

## [2026-08-28] Hero: eyebrow pasa de "Clínica privada" a "Clínica Benites"

El usuario notó que "Clínica Benites" solo aparecía como `alt` del logo (chico, en el nav) — ningún texto legible de la página nombraba la clínica. Primer intento: agregarla como línea propia dentro del `<h1>` — descartado por el usuario al revisarlo. Solución final: el eyebrow arriba del titular (`cb-eyebrow` en `hero.blade.php`) cambia de "Clínica privada · Guayaquil" a "Clínica Benites · Guayaquil", sin agregar elementos nuevos ni tocar `public.css` (la clase `.cb-headline-brand` del intento descartado se quitó). El eslogan ("Precisión quirúrgica. Calidez humana.") queda intacto.

## [2026-08-28] Especialidades: ícono por fila + fondo blanco

A pedido del usuario: la sección pasa de `cb-section--dark` a `cb-section--light` (mismo tratamiento que "Sobre nosotros"), con overrides de color en `public.css` para watermark/cifra/divisores/nombre/flecha sobre fondo claro. Cada fila del directorio suma un ícono de trazo fino (`especialidades.blade.php`, array `$iconos`) agrupado por sistema/órgano clínico real (ej. Cardiología y Cateterismo Cardiaco comparten el corazón) en vez de un ícono distinto sin criterio por cada una de las 27. Verificado con una vista previa HTML renderizada con Playwright (CSS real compilado vía `vite build`, sin la fuente Bunny por falta de red en el sandbox) antes de dar el cambio por cerrado — el ícono de Neurología pasó de un cerebro de dos lóbulos (no se leía a 20px) a un rayo de impulso nervioso, más claro a ese tamaño.

## [2026-08-28] Especialidades: se saca por completo el tratamiento de letras

Feedback directo del usuario: "no me gusta, quítale el alfabeto". Se eliminó de `especialidades.blade.php` + `public.css` todo lo relacionado a letras — el marcador de grupo inline dentro de cada columna (`cb-directory-group-letter`) y el scatter de letras dispersas en los márgenes (`cb-directory-scatter`, agregado en el commit anterior tras la referencia de mobbin.com). Queda: watermark "27" de fondo, cifra grande en primer plano (`cb-stat-callout`), divisores verticales entre las 3 columnas y línea dorada en hover. El array de especialidades sigue alfabetizado (es el orden de los datos), simplemente ya no se anota visualmente. `$letras`/`$scatter` se sacaron del Blade por no usarse más.

## [2026-08-28] Especialidades: refuerzo tras feedback ("se ve muy simple")

Sobre el ajuste anterior (cifra grande + scatter), el usuario lo vio simple en pantalla — el bloque central seguía siendo texto plano en 3 columnas y el scatter solo se veía desde `xl`. Cuatro retoques en `especialidades.blade.php` + `public.css`: scatter bajado a `lg` (más anchos de pantalla real lo muestran); divisores verticales entre las 3 columnas del directorio (antes solo espacio en blanco); marcador de letra pasa de texto suelto a chip con fondo (más textura); línea de acento teal→dorado debajo del "27" para anclarlo visualmente. Directorio de texto y demás secciones sin cambios.

## [2026-08-28] Especialidades: cifra grande + letras dispersas (referencia mobbin.com)

El usuario pidió traer la "sensación general" (dispersión + escala del número grande) de una captura de mobbin.com, sin caer en íconos genéricos por especialidad — mismo criterio ya documentado en el archivo que descartó eso desde el rediseño original. Dos piezas nuevas en `especialidades.blade.php` + `public.css`: `cb-stat-callout` pone el "27" real en primer plano con la escala tipográfica de la referencia (antes solo vivía como watermark de fondo); `cb-directory-scatter` reemplaza el rail vertical fijo de letras por esas mismas letras reales dispersas en los márgenes de la sección (posiciones calculadas por índice, no al azar), visible solo desde `xl` por espacio de margen. El directorio de texto (nombre + flecha en hover) no se tocó.

## [2026-08-28] Fix de scroll-reveal: `.cb-reveal` ahora se dispara con el scroll, no al cargar

`animation-timeline: view()` (scroll-driven, CSS puro, con `@supports` para degradar seguro) reemplaza el disparo por `animation-delay` en ms — antes el fade-up terminaba antes de que el usuario llegara scrolleando a secciones como Contacto, y nunca se veía. **Scoped a `.cb-section .cb-reveal`** (no al Hero, que usa `.cb-hero`): el Hero mantiene su fade-up escalonado al cargar la página intacto — es el "momento audaz" ya confirmado del sitio, y aplicar el fix ahí lo habría roto (el Hero ya está visible en scroll=0, así que su rango "entry" ya habría pasado al cargar). Con esto, el brief cerrado de `docs/PLAN_SITIO_PUBLICO.md` queda completo.

## [2026-08-28] Rediseño Contacto: ícono de pulso + pulso de opacidad sutil

Se reemplazó el ícono duplicado del panel "Atención de emergencias" (era el mismo pin de ubicación que ya se usa arriba) por un ícono de pulso/ECG, que conecta con la línea de pulso del hero en dosis mínima. Se le agregó una animación de opacidad sutil (`cb-pulse-fade`, sin escala, respeta `prefers-reduced-motion`) siguiendo `docs/PLAN_SITIO_PUBLICO.md` sección 5.5 — contenido real (ambulancia/emergencias ya confirmado), no decoración porque sí. No depende del fix de `.cb-reveal`→`view()`, que sigue diferido para el final.

## [2026-08-28] Rediseño Servicios: fondo grounded en foto real (reemplaza el orb plano)

Se quitó el `cb-orb-teal` (blob de color plano) de `servicios.blade.php` — es justo el cliché "gradiente healthcare tech abstracto" que `docs/PLAN_SITIO_PUBLICO.md` marca a evitar. En su lugar: `cb-hero-grid` reutilizado con máscara en la esquina opuesta a la de Especialidades, y un glow ambiental (`filter: blur`, clase nueva `.cb-services-glow` en `public.css`) hecho con la misma foto real que ya usa el tile de Quirófanos (`hero-quirofano.jpg`), mismo mecanismo que `.cb-hero-video-glow` del hero. El mosaico (bento grid) y el hover-scale de las fotos no se tocaron — el plan es explícito en que ya tienen suficiente movimiento propio. Pendiente confirmar visualmente en entorno real (mismo caveat que el resto de ajustes visuales, sin `vite build` completo en este sandbox).

## [2026-08-28] Rediseño Especialidades + plan de contenido/flujo del sitio público

El usuario marcó que las 4 secciones nuevas se veían genéricas comparadas con el Hero. Se investigó (best practices de sitios de clínica, referencia local Clínica Kennedy, dirección de iconos/fondos, animaciones) y se documentó todo en `docs/PLAN_SITIO_PUBLICO.md`. Se validó el enfoque con un preview HTML de Especialidades antes de tocar código real, y ya se implementó: rail de letras A-U (`especialidades.blade.php`), watermark "27" en trazo, marcador de grupo por letra, línea dorada trazada en hover (gradiente teal→dorado→teal, misma paleta del pulso del hero) en vez de solo cambiar el color del borde. Estilos nuevos en `public.css` (sección "Especialidades — directorio editorial"). Pendiente: mismo tratamiento para Servicios, Sobre nosotros, Contacto, y al final el fix de animación a `animation-timeline: view()` (scroll-driven, CSS puro) — ver brief cerrado en la sección 6 del plan.

## [2026-08-28] Sitio público: secciones Especialidades, Servicios, Sobre nosotros y Contacto

Se completaron las 4 secciones que faltaban del sitio público (`resources/views/sections/`) + un footer nuevo (`partials/footer.blade.php`), conectadas en `home.blade.php` y `nav.blade.php` (se agregó el link "Nosotros"). Especialidades usa las 27 reales de `AreaSeeder.php` como directorio alfabetizado; Servicios arma un mosaico con las 5 fotos reales ya existentes en `public/images/`; Sobre nosotros es la única sección clara (ivory) del sitio; Contacto no incluye formulario, dirección ni mapa (no confirmados por el cliente). Estilos nuevos agregados como bloque propio al final de `public.css`, sin tocar las reglas del hero ni sumar paleta nueva.

## [2026-08-28] Hero: bordes transparentes y video ampliado

Se agregó un glow desenfocado detrás del video en `hero.blade.php` y se ajustó `public.css` para desvanecer video/poster con transparencia alfa real, sin pintar colores sobre el fondo. También se amplió y bajó levemente el video, incluidos sus breakpoints responsive.

## [2026-08-28] Reorganización: MEMORIA.md y CHANGELOG.md archivados

`MEMORIA.md` y `CHANGELOG.md` habían crecido a ~535KB combinados (sobre todo por el detalle vuelta-por-vuelta de los ajustes del video del hero). Se movieron a `docs/historico/` con fecha, y se reescribieron versiones nuevas y compactas de ambos archivos con el mismo esqueleto pero sin la narrativa sesión-por-sesión. Ver `MEMORIA.md` sección 12 para el criterio a seguir de ahora en adelante.
