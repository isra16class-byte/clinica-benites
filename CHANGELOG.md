# Changelog

Historial completo previo al 28 de agosto de 2026 (23-28 ago): ver `docs/historico/CHANGELOG_2026-08-23_2026-08-28.md`.

Formato de entrada a partir de ahora: corta y al grano — qué cambió, en qué archivo(s), 2-4 líneas. El detalle de investigación/depuración vale más en el mensaje de commit que acá.

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
