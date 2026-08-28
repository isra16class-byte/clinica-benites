# Changelog

Historial completo previo al 28 de agosto de 2026 (23-28 ago): ver `docs/historico/CHANGELOG_2026-08-23_2026-08-28.md`.

Formato de entrada a partir de ahora: corta y al grano — qué cambió, en qué archivo(s), 2-4 líneas. El detalle de investigación/depuración vale más en el mensaje de commit que acá.

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
