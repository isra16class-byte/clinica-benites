# Changelog

Historial completo previo al 28 de agosto de 2026 (23-28 ago): ver `docs/historico/CHANGELOG_2026-08-23_2026-08-28.md`.

Formato de entrada a partir de ahora: corta y al grano — qué cambió, en qué archivo(s), 2-4 líneas. El detalle de investigación/depuración vale más en el mensaje de commit que acá.

## [2026-08-28] Sitio público: secciones Especialidades, Servicios, Sobre nosotros y Contacto

Se completaron las 4 secciones que faltaban del sitio público (`resources/views/sections/`) + un footer nuevo (`partials/footer.blade.php`), conectadas en `home.blade.php` y `nav.blade.php` (se agregó el link "Nosotros"). Especialidades usa las 27 reales de `AreaSeeder.php` como directorio alfabetizado; Servicios arma un mosaico con las 5 fotos reales ya existentes en `public/images/`; Sobre nosotros es la única sección clara (ivory) del sitio; Contacto no incluye formulario, dirección ni mapa (no confirmados por el cliente). Estilos nuevos agregados como bloque propio al final de `public.css`, sin tocar las reglas del hero ni sumar paleta nueva.

## [2026-08-28] Hero: bordes transparentes y video ampliado

Se agregó un glow desenfocado detrás del video en `hero.blade.php` y se ajustó `public.css` para desvanecer video/poster con transparencia alfa real, sin pintar colores sobre el fondo. También se amplió y bajó levemente el video, incluidos sus breakpoints responsive.

## [2026-08-28] Reorganización: MEMORIA.md y CHANGELOG.md archivados

`MEMORIA.md` y `CHANGELOG.md` habían crecido a ~535KB combinados (sobre todo por el detalle vuelta-por-vuelta de los ajustes del video del hero). Se movieron a `docs/historico/` con fecha, y se reescribieron versiones nuevas y compactas de ambos archivos con el mismo esqueleto pero sin la narrativa sesión-por-sesión. Ver `MEMORIA.md` sección 12 para el criterio a seguir de ahora en adelante.
