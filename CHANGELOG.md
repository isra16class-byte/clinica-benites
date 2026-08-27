# 📜 CHANGELOG — Clínica Benites

Registro cronológico de cambios del proyecto. Formato: más nuevo arriba, nunca se borran entradas viejas.

Ver `MEMORIA.md` para el estado actual y contexto técnico completo — este archivo es solo la bitácora de "qué cambió cuándo".

---

---

## [2026-08-27] Mensaje precargado de WhatsApp + bajada del hero más corta (a partir de feedback externo)

- El usuario compartió una devolución de Perplexity sobre la Portada (capturada antes del patch del emblema). Se evaluó punto por punto en vez de aplicarla entera.
- **Descartado a propósito**: agregar "años de trayectoria", "número de especialistas" o certificaciones — son justamente los datos que MEMORIA.md ya tiene listados como pendientes de confirmar con el dueño; inventarlos rompía el criterio ya establecido de no afirmar nada no confirmado.
- **Verificado en vez de asumido**: la advertencia sobre contraste de color se chequeó con los ratios WCAG reales (calculados con Python/Pillow) para cada texto del hero contra los dos extremos del degradado de fondo. Peor caso: 3.58:1 (acento verde-dorado del titular sobre la parte más clara del fondo) — pasa igual porque ese texto es grande/negrita (mínimo 3:1, no 4.5:1). Resto, todo por encima de 6:1. No hacía falta ningún cambio.
- **Aplicado**: los 3 links de WhatsApp del sitio (`hero.blade.php`, y `nav.blade.php` desktop + mobile) ahora incluyen `?text=Hola%2C%20deseo%20agendar%20una%20cita%20en%20Cl%C3%ADnica%20Benites.` — funciona ya mismo, no depende de tener el número real (el TODO de reemplazar `593000000000` sigue pendiente, sin cambios).
- **Aplicado**: la bajada del hero (`.cb-subheadline`) se acortó, sacando la cláusula final más genérica. Se mantuvo el único dato concreto ("+26 especialidades", ya confirmado contra `Servicios_CB_2026.pdf`) — no se agregó ningún dato nuevo, solo se recortó redacción.
- Verificado con balance de tags en ambos archivos `.blade.php` afectados.

---

## [2026-08-27] Emblema técnico en el espacio vacío del hero (Portada)

- El usuario, viendo una captura real de la Portada en escritorio, preguntó para qué estaba pensado el espacio vacío a la derecha del texto. Respuesta: para nada — es consecuencia de que `.cb-subheadline` limita el ancho (`max-width: 38rem`, legibilidad) dentro de un contenedor bastante más ancho (`max-w-7xl`), en un layout de una sola columna. Solo lo ocupaban los orbes decorativos de fondo.
- Se presentaron 4 direcciones antes de tocar código (vacío a propósito / card de confianza / elemento gráfico grande / fotos reales) — el usuario eligió el elemento gráfico, mismo lenguaje visual que la línea de pulso ya existente.
- Nuevo `<div class="cb-hero-emblem">` en `hero.blade.php`: SVG con un aro de 24 marcas calculadas por trigonometría real (`@for` + `deg2rad`, no a mano), que gira muy despacio (150s) como atmósfera — no compite con la línea de pulso, que sigue siendo el único movimiento protagonista de la sección. Aro exterior que se dibuja al cargar (mismo truco `pathLength="1"` de la línea de pulso), 4 escuadras tipo reticle de cámara, y en el centro el mismo ícono cruz-en-círculo del trust-strip ("Atención de emergencias") ampliado, con su brazo horizontal convertido en una réplica en miniatura de la línea de pulso (mismo gradiente, con blip dorado que late).
- Solo visible desde `xl` (1280px) — se descartó `lg` (1024px) tras calcular que a esos anchos el texto todavía puede superponerse.
- El CSS nuevo se agregó dentro del `@layer components` existente a propósito — mismo cuidado que el fix del ícono de hamburguesa (CSS custom sin capa le gana a las utilidades de Tailwind sin importar el orden).
- Respeta `prefers-reduced-motion` (rotación/dibujado/latido se desactivan).
- Verificado con balance de llaves del CSS (92/92) y de tags del Blade, y con una vista previa estática renderizada aparte (`cairosvg`) para validar el diseño antes de integrarlo — sin acceso a Sail/npm en esta sesión. **Pendiente confirmar visualmente por el usuario en el entorno real**, en una pantalla ≥1280px.

---

## [2026-08-27] Logo horizontal blanco reemplazado por versión generada con IA (Gemini)

- El usuario compartió una imagen del logo generada con Gemini (JPG, 1833×560), con más calidad/detalle 3D que el archivo actual, y pidió integrarla al sistema y al sitio público.
- La imagen original no se podía usar tal cual: sin transparencia (fondo navy horneado) y con ícono+texto pegados en una sola composición horizontal.
- Se separó el fondo con una máscara de transparencia basada en luminosidad (histograma confirmó dos clusters bien separados: fondo oscuro vs. blanco del arte) — resultado sin halos, verificado componiendo el recorte tanto sobre fondo claro como sobre el navy real de marca (`#0C447C`).
- **Hallazgo**: al probarlo sobre fondo claro, el arte (blanco con sombra 3D) queda casi invisible — es funcionalmente una versión "para fondo oscuro", igual que la familia `*-white.png` ya existente, no un reemplazo de la familia navy (`logo.png`/`logo-horizontal.png`, usadas sobre fondo claro en el sidebar del panel y el PDF de factura). Esas dos quedaron intactas.
- Se reemplazó únicamente `public/images/logo-horizontal-white.png` (único uso real: `resources/views/partials/nav.blade.php`, el nav del sitio público sobre el hero navy) por coincidir en composición (horizontal, ícono izq. + texto der.) con la imagen nueva. `logo-white.png` (composición vertical, sin uso activo en el código) no se tocó — forzar ahí una imagen horizontal la habría deformado.
- El archivo se recortó (margen transparente sobrante) y se redimensionó de 1833×560 a 1056×260 (de sobra para retina al tamaño real que se muestra, `h-8`/`h-9` en el nav) — bajó de 746KB a 146KB.
- El archivo anterior se conservó en `public/images/_legacy/logo-horizontal-white-anterior.png`, mismo criterio que el logo placeholder viejo.
- Ningún archivo `.php`/`.blade.php` cambió — el nav ya apuntaba a ese nombre de archivo, solo se reemplazó el binario de la imagen.
- **Pendiente confirmar por el usuario en el entorno real** — al ser un archivo estático (no CSS/JS), alcanza con recargar la página, no hace falta recompilar assets.

---

## [2026-08-27] Azul de marca real, medido de la fachada física y el PDF de servicios

- El usuario compartió una foto real de la fachada de la clínica (Google Street View) y preguntó por qué se habían elegido esos colores, sugiriendo acercar el azul del sitio al que usa la clínica de verdad (comparando con la portada de `Servicios_CB_2026.pdf`).
- Medición real en píxeles (no de memoria, con Pillow sobre recortes limpios): fachada física `#2B5E9F`; PDF de `#0066B4` (claro) a `#001F5F` (oscuro). El navy que ya usaba el sitio (`#071B33`) resultó tener el mismo matiz de azul (~210-213° HSL) pero mucho más oscuro/apagado que ambas fuentes reales.
- Se presentaron 3 direcciones con una vista previa comparativa antes de tocar código (ajuste sutil / azul real solo como acento / azul real como fondo del hero) — el usuario eligió la última.
- Cambio en `resources/css/public.css`: los 4 tokens `--color-cb-navy-950/900/800/700` (bloque `@theme`) se reemplazaron por los valores reales medidos, y el `rgb(7 27 51 / 70%)` hardcodeado del fondo del nav se actualizó a `rgb(0 31 95 / 70%)` para que coincida. El verde azulado y el dorado no se tocaron.
- **Alcance acotado a pedido del usuario**: solo el sitio público. El panel Filament usa su propio primario (`Color::Teal`) y no comparte estos tokens — queda sin cambios.
- Verificado con balance de llaves (script, 75/75). **Sin acceso a Sail/npm en esta sesión** — pendiente confirmar por el usuario tras recompilar.

---

## [2026-08-27] Fix: ícono de hamburguesa visible en escritorio (Portada/Hero)

- El usuario confirmó, con captura de pantalla real del Escritorio (`localhost/#inicio`, patch anterior ya aplicado), que la Portada se ve como se diseñó — pero reportó un bug: el ícono de hamburguesa (☰) aparece al lado del menú de escritorio, cuando debería quedar oculto en pantallas grandes y solo mostrarse en móvil.
- **Causa raíz**: en Tailwind v4, las utilidades (`lg:hidden`, la clase que oculta el botón en escritorio en `nav.blade.php`) viven dentro de `@layer utilities` — una capa CSS que, por especificidad de capas (no de selector), **siempre pierde contra cualquier regla CSS normal fuera de una capa**, sin importar el orden en que aparezcan en el archivo. Las reglas custom del sitio público (`.cb-burger { display: flex; ... }` y el resto de `public.css`) estaban escritas fuera de cualquier `@layer`, así que le ganaban a `lg:hidden` en cualquier tamaño de pantalla.
- **Solución**: se envolvió todo el CSS custom de `public.css` en las capas que Tailwind v4 espera — `@layer base` para `html`/`body`, `@layer components` para el resto (nav, hero, botones, franja de confianza, animaciones). `@import 'tailwindcss'` y el bloque `@theme` (tokens de color/tipografía) quedan sin capa, como corresponde. Con esto, las utilidades responsivas de Tailwind (`lg:hidden`, `lg:flex`, etc.) vuelven a poder ganarles a las reglas custom cuando corresponde, sin cambiar ninguna clase en los archivos `.blade.php`.
- Ningún nombre de clase ni archivo Blade cambió — es un fix puramente de organización de capas CSS en `resources/css/public.css`.
- Verificado con balance de llaves (script, 75/75). **Confirmado por el usuario en el entorno real**: tras recompilar, el ícono de hamburguesa ya no aparece en escritorio.

---

## [2026-08-27] Sitio web público — arranca con la Portada/Hero

- Arranca la otra mitad del proyecto (ver sección 1 de `MEMORIA.md`): hasta ahora solo existía el panel interno de Filament. Se construye la primera sección del sitio público, a pedido explícito del usuario ("VIP plus", con animaciones).
- Archivos nuevos: `resources/css/public.css` (paleta/tipografía/animaciones, nuevo entrypoint de Vite), `resources/views/components/layouts/public.blade.php` (layout base), `resources/views/partials/nav.blade.php` (navegación fija, menú móvil sin JS vía checkbox+CSS), `resources/views/sections/hero.blade.php` (portada), `resources/views/home.blade.php` (compone todo, punto de entrada para sumar futuras secciones).
- `routes/web.php`: `/` ahora devuelve `view('home')` en vez de `view('welcome')` (el default de Laravel queda sin usar, no se borró). `vite.config.js`: se agregó `resources/css/public.css` a los entrypoints.
- Paleta: reutiliza los 2 colores de marca ya confirmados en el panel (navy `#0C447C`, verde azulado `#0F6E56`), suma un acento dorado/champán nuevo y exclusivo del sitio público. Tipografía: Space Grotesk (titulares) e Instrument Sans (cuerpo) — ambas ya eran dependencias del proyecto, no se sumó ninguna familia nueva.
- No sigue `DISEÑO.md` (ese prompt es explícitamente para el panel Filament, no para una landing) — se tomó el pedido del usuario como brief de diseño nuevo, documentado en la sección 8.5 de `MEMORIA.md`.
- Elemento distintivo: línea de pulso SVG animada (se dibuja como plano técnico al cargar + un pulso dorado en loop vía `offset-path`, con `@supports` de respaldo) — resto de animaciones (aparición escalonada, aura de fondo) deliberadamente discreto. Respeta `prefers-reduced-motion`.
- Contenido tomado tal cual de `Servicios_CB_2026.pdf` (26+ especialidades, quirófanos, UCI/UCIN, ambulancia, emergencias) — sin inventar datos no confirmados (horario, año de fundación, dirección).
- **Placeholders sin reemplazar**: número de WhatsApp/teléfono (`593000000000`, marcado `TODO` en 4 lugares) — no está confirmado en ninguna fuente real, hay que reemplazarlo antes de publicar.
- Verificado a mano: balance de llaves/paréntesis de `public.css` (script) y balance heurístico de etiquetas HTML/Blade (script). **Sin acceso a Sail/npm/composer en esta sesión** — nada de esto se vio corrido en un navegador real todavía, pendiente confirmar por el usuario tras `npm run build`/`npm run dev`.

---

## [2026-08-26] Íconos diferenciados en el sidebar para Resources que compartían el genérico

- Pendiente desde la sección 8.4 (agrupar el menú lateral), que había quedado sin resolver por no tener `vendor/` disponible para confirmar el enum real `Filament\Support\Icons\Heroicon`.
- Se instaló PHP CLI en el entorno de trabajo y se clonó el código fuente real de **Filament 5.7.6** (tag `v5.7.6`) para confirmar cada nombre de ícono contra `packages/support/src/Icons/Heroicon.php` antes de usarlo — ninguno se puso de memoria.
- **Hallazgo al revisar el código real**: el pendiente original decía 6 Resources compartiendo `OutlinedRectangleStack` (Áreas, Citas, Facturas, Historia Clínicas, Médicos, Pacientes); en realidad son 7 (también Internamiento), y hay un segundo duplicado no documentado: Item Inventario y Orden de Estudio comparten `OutlinedBeaker`.
- **Alcance**: se priorizaron los duplicados que sí generan confusión real — mismo grupo del sidebar, uno al lado del otro. Eso deja 2 grupos a resolver: **Atención al paciente** (Pacientes/Citas/Historia Clínicas, los 3 con el mismo ícono) y **Administración** (Áreas/Médicos). Internamiento (Infraestructura) e Item Inventario/Orden de Estudio (Infraestructura vs. Inventario, grupos distintos) no se tocaron por no compartir grupo con otro Resource del mismo ícono.
- Cambios: `PacienteResource` → `OutlinedIdentification`; `CitaResource` → `OutlinedCalendarDays`; `HistoriaClinicaResource` → `OutlinedClipboardDocumentList`; `AreaResource` → `OutlinedSquares2x2`; `MedicoResource` → `OutlinedUserCircle` (distinto de `OutlinedUsers`, ya usado por Usuarios).
- Verificado con `php -l` (sin errores, los 5 archivos modificados) y cada nombre de ícono confirmado con `grep` contra el enum real clonado.
- Cambio puramente de un valor de propiedad estática (`$navigationIcon`) en cada Resource — no toca lógica, permisos, ni ningún otro comportamiento. **Confirmado por el usuario en el entorno real**, con 2 capturas de pantalla mostrando los grupos "Atención al paciente" y "Administración" ya con íconos distinguibles ("creo que si").

---

## [2026-08-26] Fix: diamante del popover desalineado verticalmente con el texto

- El usuario reportó, con captura de pantalla real del Escritorio (popover de "Ocupación de camas", entrada de abajo), que los 3 diamantes (Hospitalización/UCIN/UCI) se veían pegados arriba de cada línea, no alineados con el centro vertical del texto.
- **Causa raíz**: el diamante (`::before` en `.cb-stat-detalle-texto` y cada `<li>` de `.cb-stat-detalle-lista`) se posicionaba con `top: 0.4em` — un offset fijo pensado para acercarse al tope de una línea de texto, pero que asume una relación fija entre tamaño de fuente y altura de línea. El `line-height` real del proyecto deja el texto centrado dentro de su propia caja, así que ese offset en `em` lo dejaba visiblemente más arriba del centro real — más notorio en `.cb-stat-detalle-lista li` por tratarse de un contenedor flex de dos columnas (Hospitalización/2 de 5), donde el desfase se nota más al haber dos elementos alineados en la misma fila.
- **Solución**: se cambió `top: 0.4em` por `top: 50%` + `transform: translateY(-50%) rotate(45deg)` — centra el diamante respecto al contenedor (`<li>` o párrafo, ambos con `position: relative`), sin depender de la relación entre tamaño de fuente y `line-height`. Es más robusto que un valor en `em`: no se desalinea de nuevo si el `line-height` cambia más adelante.
- De paso se corrigió un dato incorrecto en el comentario de esa misma sección de `theme.css` (decía "cuadrado de 0.4rem", el tamaño real del diamante ya era `0.5rem` desde que se implementó, ver la entrada de abajo).
- Verificado con balance de llaves de `theme.css` (script). Es un fix puramente CSS (no toca PHP), así que solo necesita recompilar assets (`sail npm run build`) para verse — no requiere `sail artisan` ni tocar la base de datos. **Confirmado por el usuario en el entorno real** ("listo si funciono").

---

## [2026-08-26] Popover de tarjetas: diamante de color a la izquierda de cada línea de detalle

- El usuario confirmó (captura de pantalla real) que la dirección C (entrada de abajo) funciona bien y la prefiere sobre A, y pidió personalizarla más: "un diamantito al lado de cada texto con su respectivo color dinámico".
- `valorConPopover()` ahora recibe un tercer parámetro `$color` (el mismo color semántico que cada stat ya usa en `->color()`) y lo agrega como clase `cb-stat-popover-accent-{color}` al panel. Las 4 llamadas (`statIngresosDelMes`, `statPorCobrar`, `statCitasAtendidas`, `statOcupacionCamas`) se actualizaron para pasarlo.
- `theme.css`: 5 reglas `cb-stat-popover-accent-{color}` definen una custom property `--cb-popover-accent` reutilizando las mismas variables reales de Filament (`--color-{success,danger,warning,info}-600`, `--color-gray-400`) ya en uso para el acento del borde izquierdo de la tarjeta (`cb-stat-accent-{color}`) — una sola fuente de verdad de color, sin duplicar el `match` de colores.
- El diamante en sí es un `::before` (cuadrado de `0.5rem` rotado 45°, `position: absolute`) en `.cb-stat-detalle-texto` y cada `<li>` de `.cb-stat-detalle-lista` — no se agregó ningún ícono SVG nuevo, no hacía falta para un marcador tan chico. El estado vacío (`.cb-stat-detalle-vacio`, ej. "Sin facturas pendientes.") queda **sin** diamante a propósito, para no sugerir un dato que no existe.
- Verificado con `php -l` (sin errores) y balance de llaves de `theme.css` con script. **Aún sin confirmar por el usuario en el entorno real** — sin acceso a Sail/npm en esta sesión.

---

## [2026-08-26] Tarjetas de KPI presionables — reemplazo de dirección A por dirección C "popover flotante"

- A pedido del usuario, tras confirmar la dirección A (expande hacia abajo, entrada de abajo) y su fix de `diffInDays()`: "me gustaría implementar la C y descartar la A para ver cuál me queda mejor" — retomando la conversación de la sesión que se quedó sin créditos, donde se habían propuesto 3 direcciones (A expande hacia abajo, B expande hacia el costado, C popover/modal) y se había elegido A. Esta sesión reemplaza A por C sobre el mismo código, sin tocar el contenido de los detalles (mismas funciones `desglose*()`, sin cambios).
- **Diferencia clave con C tal como se había descartado originalmente**: en vez de calcular la posición del panel a mano en CSS (`position: absolute` con `top`/`left` fijos, con el riesgo de que se corte en los bordes del viewport — más notorio en "Ocupación de camas", la última tarjeta de la fila de 4), se usa la directiva `x-anchor` del plugin `@alpinejs/anchor` (basado en Floating UI). Confirmado clonando Livewire 4.4.1 real (`js/lifecycle.js`) que este plugin **ya viene registrado globalmente** (`Alpine.plugin(anchor)`), igual que `x-collapse` en la dirección A — no hace falta instalar nada nuevo. Floating UI reposiciona el panel solo (`flip`/`shift` con `padding: 5`, confirmado en el código fuente real de `@alpinejs/anchor`, `src/index.js`, que vienen activados por defecto) si no entra en la pantalla, sin cálculo manual.
- `IndicadoresGerencialesWidget.php`: `atributosExpandible()` → `atributosPopover()` (agrega `x-ref="stat"` para que el panel pueda anclarse, y `@click.outside="open = false"` en la tarjeta para cerrar el panel al clickear afuera — Alpine core, sin plugin extra); `valorConDetalle()` → `valorConPopover()` (el bloque de detalle pasa de `x-show + x-collapse` dentro del flujo normal a `x-show + x-transition.origin.top + x-anchor.bottom-start.offset.8="$refs.stat"` fuera del flujo, con `@click.stop` para que un clic adentro del panel no burbujee y lo cierre al toque de abrirlo). El nombre de la variable Alpine pasa de `expanded` a `open` (mismo significado).
- **Ya no hace falta el `:has()` de `theme.css`** que desactivaba el `align-items: stretch` del grid mientras una tarjeta estaba expandida (problema exclusivo de la dirección A, donde la tarjeta crecía en alto): como el panel de C se posiciona con `position: absolute` fuera del flujo, la tarjeta nunca cambia de tamaño — una de las ventajas de robustez de C sobre A, además de las ya conocidas frente a B.
- `theme.css`: se reemplaza el bloque de la dirección A por el de C — se quita el `:has()`, `flex-wrap` del value (ya no hace falta empujar el detalle a su propia línea) y las reglas de `.cb-stat-detalle` (contenedor inline); se agrega `.cb-stat-popover` (panel flotante, mismo lenguaje visual que `.fi-dropdown-panel` de Filament — `bg-white shadow-lg ring-1 ring-gray-950/5 rounded-lg`, confirmado en `packages/support/resources/css/components/dropdown/index.css` de la v5.7.6 — traducido a CSS plano en vez de reutilizar esa clase, para no acoplarse al JS propio del dropdown de Filament). Las clases de contenido (`.cb-stat-detalle-texto`, `.cb-stat-detalle-vacio`, `.cb-stat-detalle-lista`) se mantienen sin cambios, reutilizadas dentro del panel nuevo.
- Verificado con `php -l` (sin errores) y balance de llaves de `theme.css` verificado con un script. **Aún sin confirmar por el usuario en el entorno real** — sin acceso a Sail/npm en esta sesión, no se pudo correr `npm run build` ni ver el panel flotante en el navegador. La dirección A queda en el historial de commits (y en la entrada de abajo de este changelog) por si el usuario prefiere volver a ella tras comparar.

---

## [2026-08-26] Fix: antigüedad de factura mostraba número decimal sin redondear

- El usuario reportó, con captura de pantalla real del Escritorio, que el detalle de "Por cobrar" (agregado en la entrada de abajo, tarjetas de KPI presionables) mostraba **"La más antigua, hace 316.95005496735 dias."** en vez de un número entero legible.
- **Causa raíz**: `IndicadoresGerencialesWidget::desglosePorCobrar()` usaba `Carbon::parse($masAntigua->fecha)->diffInDays()` asumiendo que devuelve `int` (comportamiento de Carbon 2). Confirmado clonando el código fuente real de `nesbot/carbon` tag `3.13.2` (versión real instalada, según `composer.lock`) que **en Carbon 3, `diffInDays()` cambió su tipo de retorno a `float`** (mide precisión de microsegundos, no solo días de calendario) — confirmado en `src/Carbon/Traits/Difference.php`, firma `diffInDays($date = null, bool $absolute = false, bool $utc = false): float`.
- Esto rompía dos cosas a la vez: el `match(true)` con `===` estricto nunca matcheaba el `float` contra los `int` `0`/`1` de los casos "hoy"/"hace 1 día" (siempre caía al `default`), y el string interpolado (`"hace {$dias} días"`) imprimía el float completo con todos los decimales.
- **Este mismo caso ya estaba resuelto en otro archivo del proyecto** — `alertas-operativas-widget.blade.php` usa `(int) round(now()->diffInDays(...))` en sus 3 usos de `diffInDays()` (lotes por vencer, facturas vencidas, camas ocupadas hace mucho) — solo `IndicadoresGerencialesWidget.php` (pieza más nueva) no había aplicado ese mismo patrón.
- **Solución**: se envuelve el resultado con `(int) round(...)`, igual que en `alertas-operativas-widget.blade.php`, para que quede consistente en todo el proyecto.
- Verificado con `php -l` (sin errores de sintaxis). **Aún sin confirmar por el usuario en el entorno real** — sin acceso a Sail/npm en esta sesión para correr `sail npm run build` y probarlo en el navegador (aunque este fix es solo PHP, no toca CSS/JS, así que no necesita recompilación de assets — alcanza con que el usuario recargue la página tras aplicar el patch).

---

## [2026-08-26] Tarjetas de KPI presionables — dirección A "expande hacia abajo"

- A pedido del usuario ("que las tarjetas sean presionables... y muestre más detalles"), retomando una propuesta de una sesión anterior que se quedó sin créditos justo tras elegir la dirección A (de 3: A expande hacia abajo/bajo riesgo, B expande hacia el costado/más riesgo, C popover — descartadas).
- `IndicadoresGerencialesWidget.php`: las 4 tarjetas ahora son presionables (clic o Enter/Espacio) vía Alpine.js (`x-data`/`@click`/`x-show`/`x-collapse`, agregado con `extraAttributes()`, sin forkear el Blade de Filament). Al presionar, debajo del número aparece un bloque de detalle con animación suave de alto:
  - Ingresos del mes → monto del mes anterior + diferencia en $.
  - Por cobrar → cantidad de facturas pendientes + antigüedad de la más vieja (idea reciclada de la dirección C descartada — ver entradas de abajo — ahora como detalle bajo demanda).
  - Citas atendidas hoy → desglose por área.
  - Ocupación de camas → desglose por tipo (Hospitalización/UCI/UCIN), vía `withCount()` sobre la relación `Cama::internamientos()`.
- `Stat::value()` pasa de string plano a un `HtmlString` (número + flecha + bloque de detalle) — confirmado en el código fuente real de Filament 5.7.6 que `Stat::value()`/`description()` aceptan `Htmlable` y que Blade no los escapa si lo son.
- `x-collapse` (la animación de alto) no la registra Filament — se confirmó clonando Livewire 4.4.1 (`js/lifecycle.js`, `Alpine.plugin(collapse)`) que ya está disponible globalmente sin instalar nada nuevo.
- `theme.css`: estilos del bloque de detalle/flecha, `cursor:pointer`, hover sutil, y una regla `[x-cloak]{display:none}` nueva (Filament usa `x-cloak` en varios componentes propios pero nunca definió esa regla CSS — se agrega porque el proyecto nunca la había necesitado hasta ahora).
- **Problema encontrado al escribir el CSS, no anticipado en la propuesta original**: el grid de las 4 tarjetas usa `align-items: stretch` de fábrica — expandir una tarjeta estiraba la fila completa y dejaba a las otras 3 con aire vacío, pero sacar el stretch siempre rompía a "Por cobrar" (`cb-stat-destacado`, que depende de ese stretch para verse pareja pese a su padding extra). Resuelto con un selector `:has()` que desactiva el stretch solo mientras hay una tarjeta expandida.
- Verificado con `php -l`, sin errores; CSS con llaves balanceadas verificado a mano.
- **Aún sin confirmar por el usuario en el entorno real** — sin acceso a Sail/npm en esta sesión, no se pudo correr `npm run build` ni verlo en el navegador.

---

## [2026-08-26] Revertido — dirección C completa (grid asimétrico + fix de contenido)

- El usuario pidió retroceder los 2 commits de la dirección C ("Dashboard gerencial: grid asimétrico" y "Por cobrar: descripción dinámica") — no le convenció ni siquiera con el ajuste de contenido de la entrada anterior.
- Se restauraron `IndicadoresGerencialesWidget.php`, `IngresosPorMesChartWidget.php`, `FacturacionPorAreaChartWidget.php` y `theme.css` al estado exacto del commit `87e675d` ("acento de color en las 4 tarjetas de KPI"), el último confirmado por el usuario en el entorno real.
- Dashboard gerencial vuelve a: 4 tarjetas en grid parejo de 4, sin `columnSpan`, sin `cb-stat-asimetrico`, sin la suavizada de grilla del eje Y en los gráficos. El acento de color por tarjeta, el radio/sombra unificado (dirección A) y el resto del panel quedan intactos.
- Las 2 entradas de abajo (grid asimétrico y su fix de contenido) se dejan sin borrar, como registro de qué se intentó y por qué se descartó.
- Verificado con `php -l`, sin errores; diff contra `87e675d` confirmado en cero para los 4 archivos restaurados.

---

## [2026-08-26] Fix de contenido — "Por cobrar" aprovecha su ancho doble (dirección C, opción B)

- El usuario reportó, con captura real, que el ancho doble de "Por cobrar" (dirección C, entrada anterior) se veía como espacio vacío: la descripción seguía siendo el texto fijo de siempre, así que el ancho extra no aportaba nada.
- De las 3 opciones propuestas (A: sacar el span; B: llenar el espacio con contenido real; C: reducir el span), se eligió **B**.
- `IndicadoresGerencialesWidget::statPorCobrar()`: la descripción pasa de texto fijo a dinámica — cantidad de facturas pendientes + antigüedad de la más vieja (ej. "3 facturas pendientes — la más antigua, hace 12 días"), solo cuando hay deuda > 0 ("Sin facturas pendientes" si no la hay). Requiere `Carbon::parse()` sobre `fecha` (columna `date` sin cast en el modelo `Factura`) antes de `diffInDays()`.
- Sin cambios de CSS ni de grid — el `columnSpan(2)`/`cb-stat-asimetrico` de la entrada anterior se mantienen igual, esto solo cambia qué texto se muestra dentro.
- Verificado con `php -l`, sin errores.
- **Aún sin confirmar por el usuario en el entorno real** — sin acceso a Sail/npm/base de datos real en esta sesión.

---

## [2026-08-26] Confirmado + dirección C — grid asimétrico en el Dashboard gerencial

- El usuario confirmó en el entorno real que el acento de color en las 4 tarjetas de KPI (entrada anterior) se ve bien. Con eso, la dirección A + el pedido de generalizar el acento quedan cerrados y confirmados.
- Se implementó la **dirección C — grid asimétrico** (tercera de las 3 propuestas para "Refinar tarjetas y gráficos", ver `MEMORIA.md`/`DISEÑO.md`), como una vuelta extra sobre el mismo pulido visual, no un reemplazo de A:
  - `IndicadoresGerencialesWidget::getColumns()`: de `int 4` a `['default' => 1, 'lg' => 5]`. "Por cobrar" (`statPorCobrar()`) gana `->columnSpan(['default' => 1, 'lg' => 2])`; las otras 3 tarjetas quedan en su span por defecto (1) — fila exacta 2+1+1+1=5, sin sobrantes ni huecos, desde `lg` (1024px+); por debajo sigue apilado en 1 columna.
  - Confirmado en el código fuente real de Filament 5.7.6 (`packages/support/src/Concerns/CanSpanColumns.php`, `Filament\Schemas\Concerns\HasColumns`) que `Stat` sí soporta `->columnSpan()` y que el `int` fijo anterior se resolvía como `'lg' => 4` con el mobile ya en 1 columna — el array nuevo mantiene ese mismo comportamiento responsivo, no lo pierde.
  - `theme.css`: clase nueva `cb-stat-asimetrico` — fondo con tinte sutil de warning vía `color-mix()` sobre `--color-warning-600` (misma variable ya usada para el borde de acento), aplicada solo cuando hay deuda > 0 (mismo condicional que `cb-stat-destacado`).
  - `IngresosPorMesChartWidget` y `FacturacionPorAreaChartWidget`: grilla del eje Y suavizada (`scales.y.grid.color` en el `RawJs` de `getOptions()`), confirmado contra el JS real de Filament (`chart.js`, `applyChartColors()`) que ese valor tiene prioridad sobre el gris por defecto. Eje X no se tocó (ya viene sin grilla de fábrica).
  - El radio/sombra unificado de tarjetas y gráficos (también parte de la dirección C) ya estaba resuelto desde la dirección A — no hizo falta CSS nuevo para eso.
- Verificado con `php -l` (PHP 8.3.6 CLI) sobre los 3 archivos PHP tocados, sin errores; balance de llaves del CSS verificado a mano.
- **Aún sin confirmar por el usuario en el entorno real** — sin acceso a Sail/npm en esta sesión.

---

## [2026-08-26] Confirmado + mejora — acento de color en las 4 tarjetas de KPI

- El usuario confirmó que la línea quedó resuelta, y pidió extender el acento de borde izquierdo de "Por cobrar" a las otras 3 tarjetas, cada una con su color real.
- Como el color de cada stat es dinámico (depende de los datos, ver `getStats()`), se generalizó a nivel PHP: cada método `stat*()` de `IndicadoresGerencialesWidget.php` ahora pasa `extraAttributes(['class' => "cb-stat-accent-{$color}"])` usando el mismo `$color` que ya le pasaba a `->color()` — sin duplicar esa lógica.
- Confirmado en el código fuente real de Filament 5.7.6 (`packages/widgets/src/StatsOverviewWidget/Stat.php`) que `->color()` no pinta la tarjeta por sí solo — de ahí la necesidad de la clase aparte.
- `theme.css`: el borde izquierdo (antes solo en `.cb-stat-destacado`) pasa a una regla base en `.fi-wi-stats-overview-stat` + 5 clases `cb-stat-accent-{success,danger,warning,info,gray}`, todas con las variables reales de Filament (`--color-{color}-600`/`--color-gray-400`). `.cb-stat-destacado` queda solo con el aire extra abajo (lo único que sigue siendo exclusivo de "Por cobrar").
- Sin cambios de estructura ni de grid.
- **Aún sin confirmar por el usuario en el entorno real** — sin acceso a PHP/Sail/npm en esta sesión.

---

## [2026-08-26] Fix definitivo — se saca el box-shadow del todo (cualquier sombra simétrica se lee como línea)

- El usuario reportó que, tras el fix anterior (blur → blur+ring), la línea empezó a verse **también arriba** de la fila de 4 KPIs, además de seguir abajo — de forma uniforme.
- Causa real: un `ring` (box-shadow sin offset, solo spread) se aplica por igual en los 4 lados de cada tarjeta. Sobre una fila de tarjetas de igual altura y alineadas, ese borde se lee como una línea continua en CUALQUIER lado donde se aplique — no es un problema de blur vs. ring específicamente, es inherente a poner un efecto simétrico sobre una fila pareja de elementos.
- `theme.css`: se saca `box-shadow` del todo de `.fi-section`/`.fi-wi-stats-overview-stat` (sin reemplazo — la separación entre tarjetas y fondo ya la da el color de `.fi-main`, ajuste del 25 ago). Para `.cb-stat-destacado`, en vez de una sombra más marcada, se usa un acento de un solo lado: borde izquierdo de 3px en `var(--color-warning-600)` (variable real de Filament 5.7.6, confirmada en el código fuente, mismo naranja que ya usa la tarjeta en su descripción) — al no ser simétrico, no puede alinearse con sus vecinas en una línea.
- Sin cambios de estructura ni de grid — 100% CSS.
- **Aún sin confirmar por el usuario en el entorno real** — sin acceso a PHP/Sail/npm en esta sesión.

---

## [2026-08-26] Fix — la línea bajo los 4 KPIs seguía ahí (causa real: box-shadow sin ring, no el padding)

- El usuario aclaró que la captura analizada en la entrada anterior ya tenía aplicado el fix de "textos descuadrados y línea" (padding-block) — y la línea seguía viéndose igual. Esto descarta que el padding fuera la causa de la línea (sí era la causa correcta del descuadre de textos, pero eran 2 bugs distintos superpuestos).
- Diagnóstico original (pixel a pixel contra el fondo con Python/Pillow, comparado contra `stats-overview-widget.css`/`section.css` de Filament 5.7.6 real): el `box-shadow` de `.fi-section`/`.fi-wi-stats-overview-stat` usa 2 capas con blur puro (`0 1px 2px`, `0 1px 3px`) sin ningún componente sólido tipo `ring` — a diferencia del default de Filament (`shadow-sm ring-1`, que sí trae un borde de 1px sin blur). Sobre el fondo plano de `.fi-main`, ese blur se lee como una franja continua bajo la fila, incluso en los huecos entre tarjetas.
- `theme.css`: se corrige tanto la regla compartida (`.fi-section`/`.fi-wi-stats-overview-stat`) como `.cb-stat-destacado` (tenía el mismo problema, con una sombra aún más intensa) — ambas pasan a usar 1 sombra suave con blur chico + 1 borde nítido de 1px (spread, blur 0), igual que el patrón real de Filament.
- Sin cambios de estructura ni de grid — 100% CSS.
- **Aún sin confirmar por el usuario en el entorno real** — sin acceso a PHP/Sail/npm en esta sesión.

---

## [2026-08-26] Fix — tipografía uniforme de "Por cobrar" (pendiente tras el fix de padding)

- El fix anterior ("textos descuadrados y línea", ver debajo) corrigió el `padding-block: 0.25rem` que aplastaba a las 3 tarjetas normales — confirmado funcionando en el entorno real por el usuario (`sail npm run build` sin errores). Quedaba pendiente el pedido original de tipografía uniforme: la cifra de "Por cobrar" seguía en `font-size: 2.75rem` mientras las otras 3 quedan en `2.25rem`.
- `theme.css`: se quita `.cb-stat-destacado .fi-wi-stats-overview-stat-value { font-size: ...; line-height: ...; }`. La jerarquía de "Por cobrar" queda solo en `padding-bottom: 2rem` extra y la sombra — sin diferencia de tipografía.
- Sin cambios de estructura ni de grid — 100% CSS.
- **Nota de proceso**: el patch anterior se había aplicado localmente sin `git push` — al generar este patch sobre un clon del repo público, `git am` falló por no encontrar esa base. Resuelto con `git am --abort` + `git push origin main` del usuario, re-clonando antes de continuar.
- **Aún sin confirmar por el usuario en el entorno real** — sin acceso a PHP/Sail/npm en esta sesión.

---

## [2026-08-26] Fix: textos descuadrados y "línea fea" bajo los widgets del Dashboard (dirección A)

- Reportado por el usuario con captura de pantalla del Escritorio real: en la fila de 4 KPIs, la tarjeta "Por cobrar" mostraba su etiqueta/cifra/descripción arrancando más abajo que las otras 3, y aparecía una línea gris oscura, plana y de ancho completo, en el hueco entre la fila de KPIs y la fila de gráficos.
- **Causa raíz (confirmada contra el CSS real de `packages/widgets/resources/css/stats-overview-widget.css` de Filament 5.7.6, clonado para verificar)**: la entrada anterior (dirección A, más arriba en este changelog) agregó `padding-block: 0.25rem` a `.fi-section`/`.fi-wi-stats-overview-stat` pensando en dar "más aire interno" — pero `.fi-wi-stats-overview-stat` ya trae `p-6` (1.5rem) de fábrica, y esa propiedad, al no ser un comodín y venir después en el CSS, ganaba igual sin necesitar `!important` — así que en la práctica **aplastaba** el padding vertical de las 3 tarjetas normales de 1.5rem a solo 0.25rem. La tarjeta "Por cobrar" no se veía afectada porque `.cb-stat-destacado` forzaba su propio `padding: 1.5rem !important` en las 4 direcciones — quedando con el padding de fábrica mientras sus 3 vecinas quedaban comprimidas. Ese desfase de padding vertical es lo que descuadraba los textos entre tarjetas, y la sombra más marcada de `.cb-stat-destacado` cayendo sobre una tarjeta más alta (y más pegada a la fila de gráficos) es lo que se leía como la línea oscura suelta.
- **Solución aplicada, en `theme.css`**:
  - Se quita `padding-block: 0.25rem` de la regla compartida `.fi-section`/`.fi-wi-stats-overview-stat` — se deja solo el `border-radius: 1rem`. Las 4 tarjetas de KPI vuelven a compartir el mismo padding vertical de fábrica, así que sus textos vuelven a arrancar a la misma altura.
  - `.cb-stat-destacado` ya no fuerza `padding: 1.5rem` en las 4 direcciones (redundante ahora que el padding base ya no está aplastado) — en su lugar agrega solo `padding-bottom: 2rem` extra, así el contenido sigue arrancando alineado con las otras 3 tarjetas, pero la tarjeta queda igual más alta/espaciosa abajo, conservando la idea de "jerarquía por tamaño" de la dirección A.
  - La sombra de `.cb-stat-destacado` baja de intensidad (de `0 4px 6px .07 / 0 2px 4px .06` a `0 2px 4px .06 / 0 1px 2px .05`) para no repetir el efecto de línea marcada bajo esa tarjeta.
- **Aún sin confirmar por el usuario en el entorno real** — corregido sin acceso a PHP/Sail/npm en esta sesión (verificado a mano el balance de llaves del único archivo tocado, `theme.css`, y el CSS contra el código fuente real de Filament 5.7.6); pendiente que el usuario aplique el patch, corra `sail npm run build` y confirme con una captura nueva del Escritorio.

---

## [2026-08-26] Dashboard gerencial — dirección A: jerarquía por tamaño (Refinar tarjetas y gráficos)

- Segunda de las 3 direcciones de "toque visual" del panel (ver entrada de tipografía, abajo). El usuario propuso 3 caminos para "refinar tarjetas y gráficos" del Dashboard gerencial — A (jerarquía por tamaño, mínimo riesgo), B (borde lateral de color por estado) y C (grid asimétrico) — y eligió **A**.
- `IndicadoresGerencialesWidget.php`: la tarjeta "Por cobrar" (`statPorCobrar()`) gana la clase `cb-stat-destacado` vía `->extraAttributes()`, pero **solo si el monto pendiente es > 0** — si no hay deuda (estado "success", $0), no tiene sentido destacarla, tal como se anticipó al proponer esta dirección.
- `IngresosPorMesChartWidget.php`: color de la serie "Facturado" corregido de `#85B7EB` (celeste genérico, sin relación con la marca) a `#6D8FB0` (el navy de marca `#0C447C` aclarado ~40% con blanco) — "Cobrado" ya usaba el verde azulado exacto (`#0F6E56`) y quedó sin tocar.
- `theme.css`: radio propio (`border-radius: 1rem`, en vez del `rounded-xl` de Tailwind por defecto) y algo más de padding en `.fi-section`/`.fi-wi-stats-overview-stat` (cubre las 4 tarjetas de KPI y los 2 gráficos, que ya renderizan dentro de `.fi-section`). Nueva clase `.cb-stat-destacado` con padding y sombra algo mayores, y la cifra de esa tarjeta a `2.75rem` (vs. `2.25rem` del resto).
- **No se tocó** el grid (`getColumns()`) ni la estructura de ningún widget — es 100% CSS + un `extraAttributes()` condicional, tal como se buscaba con la opción de "menor riesgo".
- **Aún sin confirmar por el usuario en el entorno real** — no hubo acceso a PHP/Sail/npm en esta sesión (ni `php -l`, verificado solo a mano el balance de llaves de los 2 archivos PHP tocados); pendiente que el usuario aplique el patch, corra `sail npm run build` y confirme visualmente, igual patrón que otras entradas de esta sección.

---

## [2026-08-26] Prompt de dirección de diseño guardado en el repo (`DISEÑO.md`)

- El usuario compartió un `.md` con un prompt de dirección de diseño para el panel (técnica del cookbook de Anthropic `prompting_for_frontend_aesthetics.ipynb` — guiar tipografía/color/movimiento por separado, nombrar defaults evitados — adaptada a un panel clínico interno, priorizando claridad/confianza sobre originalidad).
- Se pidió guardarlo versionado en el repo en vez de mantenerlo solo como algo pegado a mano cada vez. Se creó `DISEÑO.md` (archivo nuevo, standalone) con el prompt completo y su justificación, y se referenció desde `MEMORIA.md` (sección 3 y encabezado de últimas actualizaciones) para no duplicar contenido.
- Uso previsto: pegar el bloque de `DISEÑO.md` al pedir un cambio visual **grande** del panel; para ajustes puntuales no hace falta.
- No se tocó ningún archivo de código — es documentación de proceso, igual que `MEMORIA.md`/`CHANGELOG.md`.

---

## [2026-08-26] Confirmado funcionando: tipografía Space Grotesk + jerarquía numérica

- El usuario aplicó el patch, resolvió el mismo problema de PATH de WSL/npm ya visto antes (usando `sail npm install`/`sail npm run build` en vez del npm de Windows) y confirmó por captura de pantalla del Escritorio.
- Se ve Space Grotesk en el título de página, títulos de sección y cifras de KPI, con jerarquía numérica clara.

## [2026-08-26] Tipografía + jerarquía numérica (primera de 3 direcciones para pulir el panel)

- El usuario sintió que al panel le faltaba personalidad pese al rediseño del encabezado. Se propusieron 3 direcciones (tipografía, tarjetas/gráficos, detalles de marca) y se eligió empezar por tipografía.
- Se agregó Space Grotesk (autoalojada vía `@fontsource/space-grotesk`, npm) para título de página, títulos de sección/widget y la cifra de los 4 KPIs del Dashboard.
- El resto del panel (tablas, formularios) sigue en Inter para no arriesgar legibilidad.
- Cifra de cada KPI subida de tamaño y con tracking ajustado para que destaque más que su etiqueta.
- **Requiere `npm install` tras aplicar el patch** (nueva dependencia en `package.json`). **Confirmado funcionando en el entorno real** (ver entrada siguiente).

---

## [2026-08-25] Confirmado funcionando: scrollbar del sidebar en color de marca

- El usuario aplicó el patch, recompiló y confirmó por captura de pantalla del Escritorio que la scrollbar del sidebar ya se ve fina y en azul de marca, integrada con el resto del rediseño.
- **Con esto, el rediseño completo del encabezado y sidebar (topbar navy, buscador, menú de usuario, perfil, scrollbar) queda cerrado y confirmado de punta a punta.**

## [2026-08-25] Scrollbar del sidebar afinada y en color de marca

- El usuario notó que la scrollbar gris nativa del sidebar se sentía descuadrada con el resto del rediseño y pidió afinarla y pintarla en el azul de marca.
- Se confirmó contra `sidebar.css` de Filament 5.7.6 que el contenedor scrolleable real es `.fi-sidebar-nav`.
- Se agregó estilizado con las 2 sintaxis existentes (`scrollbar-width`/`scrollbar-color` estándar + `::-webkit-scrollbar*`) para cubrir Firefox y Chrome/Edge/Safari sin depender de un solo motor: barra de 6px, azul de marca al 35% de opacidad (55% en hover), con variante `.dark` en celeste claro.

---

## [2026-08-25] Confirmado funcionando: topbar sólido en navy

- El usuario aplicó el patch y confirmó por captura de pantalla que el topbar se ve navy sólido, el logo se lee correctamente en blanco (el swap por CSS funcionó), el ícono de sidebar se ve blanco, y el buscador/avatar contrastan bien.
- El usuario confirmó que ahora sí "sintió que cogió formato".
- **Con esto, el rediseño del encabezado queda cerrado, en su versión final (topbar sólido en navy).**

---

## [2026-08-25] Rediseño más audaz: topbar sólido en color de marca (navy)

- El usuario, tras ver confirmado el rediseño anterior, dijo que lo esperaba más audaz — el acento de 2px se sentía "igual de genérico".
- Se mostraron 3 conceptos (topbar sólido, topbar flotante, bloque de marca en el logo) y el usuario eligió el **topbar sólido en navy**.
- Se clonó de nuevo el código fuente real de Filament v5.7.6 para verificar contraste de cada elemento hijo del topbar (no solo el fondo): botones de sidebar (`.fi-icon-btn`), logo y buscador.
- **Hallazgo clave**: el logo actual es texto navy sobre transparente — invisible sobre topbar también navy. El proyecto ya tenía preparada una variante blanca (`public/images/logo-horizontal-white.png`); se usa un swap por CSS puro (`content: url(...)`) solo para la instancia del logo dentro del topbar, sin afectar el logo del drawer móvil ni el de login (que siguen sobre fondo claro).
- El bloque de "acento sutil" de la entrada anterior se reemplaza por: `.fi-topbar` con fondo navy sólido y sombra en vez del ring gris; `.fi-icon-btn` del topbar en blanco/blanco translúcido; logo swapeado. El buscador no necesitó cambios — su fondo blanco ya contrasta bien.
- **Aún sin confirmar por el usuario en el entorno real.**

---

## [2026-08-25] Confirmado funcionando: rediseño del encabezado (topbar)

- El usuario aplicó el patch de la entrada anterior y corrió el build con `./vendor/bin/sail npm run build` (el `npm run build` directo falló primero por un problema de PATH de WSL —npm/vite resolvían al Node de Windows—, sin relación con el código del patch; resuelto usando `sail`).
- Confirmado por captura de pantalla del Escritorio: el buscador global ya muestra el placeholder específico, con el ancho ampliado en desktop; el acento de marca de 2px (degradado navy→verde azulado) se ve en el borde inferior del topbar, sutil y sin afectar el fondo blanco ni el texto; y el resto del panel (sidebar, tarjetas, gráficos) se ve intacto.
- El usuario confirmó explícitamente que también probó el menú de usuario (nombre+rol+Editar perfil+Cerrar sesión) y la página `/admin/profile`, y que todo está bien.
- **Con esto, el rediseño del encabezado del panel queda cumplido y confirmado en el entorno real.**

---

## [2026-08-25] Implementado el rediseño del encabezado (topbar): buscador, menú de usuario y página de perfil

- Se implementó, sin re-investigar nada, el plan ya redactado y verificado en la entrada anterior (vigesimoquinta entrada de `MEMORIA.md`).
- **Archivo nuevo** `lang/vendor/filament-panels/es/global-search.php`: sobreescribe el placeholder del buscador global sin tocar el vendor (`"Buscar pacientes, citas, médicos, facturas..."`).
- **Archivo nuevo** `app/Filament/Pages/EditProfile.php`: extiende `Filament\Auth\Pages\EditProfile` y agrega un campo `Rol` de solo lectura (mismo mapeo admin/recepción/médico que `UsersTable.php`/`UserForm.php`), no editable desde el propio perfil.
- **`AdminPanelProvider.php`**: se agregó `->profile(\App\Filament\Pages\EditProfile::class, isSimple: false)` y un `->userMenuItems([...])` con el ítem `'profile'` mostrando nombre+rol (vía `HtmlString`, sin URL ni acción, así Filament lo renderiza como header no-clickeable) y el ítem `'edit_profile'` apuntando a la página de perfil. No se tocó `'logout'` — Filament lo agrega solo, ya en español.
- **CSS** (`theme.css`): acento de 2px en el borde inferior de `.fi-topbar` con degradado de los 2 colores de marca, `.fi-global-search` con `min-width: 20rem` desde `lg`, `.fi-user-menu .fi-dropdown-panel` ensanchado a 18rem y `truncate` neutralizado para que nombre+rol se vean en 2 líneas, y el badge de rol con color diferenciado por rol (navy admin / verde azulado médico / gris recepción), incluidas variantes `.dark`.
- Se instaló PHP en el entorno de esta sesión (no estaba disponible al principio) y se corrió `php -l` sobre los 2 archivos nuevos y sobre `AdminPanelProvider.php` modificado — los 3 pasaron sin errores de sintaxis. El código se copió tal cual quedó redactado en `MEMORIA.md`, sin necesidad de ajustar nada al verlo.
- **Aún sin confirmar por el usuario en el entorno real** — pendiente aplicar el patch, correr `npm run build` y verificar: placeholder del buscador, menú de usuario (nombre+rol+Editar perfil+Cerrar sesión), página `/admin/profile`, y que no se haya afectado ningún otro dropdown/badge del panel.

---

## [2026-08-25] Investigación y plan completo para el rediseño del encabezado (topbar)

- Sesión dedicada 100% a investigación/planificación, a pedido explícito del usuario — **no se tocó ningún archivo de código**, para dejar todo listo y verificado antes de implementar.
- Se clonó el código fuente real del tag `v5.7.6` de `filamentphp/filament` (mismo criterio que el fix del sidebar) y se verificaron uno por uno los 4 requisitos del usuario contra el Blade/PHP/CSS real del paquete instalado, no contra documentación genérica.
- **Buscador**: el placeholder se puede cambiar sin tocar el vendor, sobreescribiendo la traducción del paquete en `lang/vendor/filament-panels/es/global-search.php` (Laravel permite overridear traducciones de paquetes así).
- **Menú de usuario**: se descubrió que Filament tiene un mecanismo **oficial** para header de nombre+rol (el ítem `'profile'` sin URL ni acción se renderiza como header, no como link) y que `Action::label()` acepta HTML controlado vía `Illuminate\Support\HtmlString` — no hace falta ningún hack de CSS ni renderHook.
- **Página de perfil**: se descubrió que Filament 5.7.6 **ya trae una página de perfil nativa** (`Filament\Auth\Pages\EditProfile`, activable con `->profile()`) con nombre/email/contraseña ya funcionando — solo hace falta extenderla para agregar el campo Rol de solo lectura, no reconstruirla de cero.
- **Estilo/legibilidad**: se identificaron y documentaron las clases CSS reales a tocar (`.fi-dropdown-panel` con ancho fijo de 14rem por defecto, `.fi-dropdown-header span` con `truncate` que hay que neutralizar para 2 líneas, `.fi-topbar`/`.fi-global-search` para el acento de marca y el ancho del buscador) y se dejó redactado el CSS completo, con los 2 colores de marca aplicados al badge de rol sin tocar la paleta global de colores del panel (evita riesgo sobre badges/botones ya confirmados en el resto del sistema).
- Se dejó en `MEMORIA.md` (vigesimoquinta entrada del día) el plan completo con el código de los 2 archivos nuevos, los 2 cambios a `AdminPanelProvider.php` y el bloque de CSS completo, ya redactados y verificados — listos para copiar/pegar y ajustar en la próxima sesión, sin tener que re-investigar nada.
- **Qué falta**: crear los archivos, aplicar los cambios, correr `php -l` si hay PHP disponible, y probar en el entorno real (buscador, menú de usuario, página `/admin/profile`, y que no se haya afectado ningún otro dropdown/badge del panel).

---

## [2026-08-25] Feedback pendiente: encabezado del panel se siente genérico

- El usuario, tras confirmar el rediseño de paleta/contraste, señaló que el encabezado (fila del logo + buscador + avatar) "se siente muy básico" y sugirió agregar el módulo de usuarios como un botón desplegable ahí mismo.
- Diagnóstico compartido con el usuario (sin implementar aún): el buscador tiene placeholder genérico ("Buscar") sin contexto de qué se puede buscar, y el avatar es un círculo con una sola letra sin mostrar nombre/rol — información real que sí importa en una clínica con roles distintos (admin/recepción/médico).
- Se verificó que `AdminPanelProvider.php` no tiene ninguna personalización de `userMenuItems()`, notificaciones, ni breadcrumbs — está 100% en default de Filament.
- El usuario pidió que el menú del avatar muestre: **nombre + rol + editar perfil + cerrar sesión**. Se verificó que el modelo `User` tiene campo `rol` (string libre: admin/recepcion/medico, con helpers `isAdmin()`/`isRecepcion()`/`isMedico()`) pero **no existe ninguna página de perfil de usuario todavía** — hay que crearla desde cero.
- **No se implementó nada de esto todavía** — se llegó al límite de la sesión. Se le entregó al usuario un prompt (ver mensaje final de la conversación) para retomar esto en una sesión nueva, con toda la info de contexto necesaria para no tener que re-investigar el modelo `User` ni el estado del `AdminPanelProvider`.

---

## [2026-08-25] Confirmado: rediseño visual completo funcionando en el entorno real

- El usuario confirmó por captura de pantalla que el fix 3 (clases CSS verificadas contra el código fuente de Filament 5.7.6) funcionó: "Escritorio" en el menú lateral ahora muestra fondo azul marino sólido con texto e ícono en blanco.
- Con esto, el rediseño visual completo de esta sesión queda confirmado de punta a punta: paleta de marca (azul marino + verde azulado) en el color primario del panel y en los 2 `ChartWidget`, contraste de superficies (fondo de página, sombras en tarjetas) y estado activo del menú lateral.
- Solo actualización de estado en `MEMORIA.md`/`CHANGELOG.md` — no se tocó código en esta entrada.

---

## [2026-08-25] Fix 3: clases CSS reales de Filament 5.7.6 (verificadas contra el código fuente)

- El usuario confirmó por captura que el fix 2 (anidamiento CSS + `!important`) tampoco tuvo efecto — el ítem activo del menú seguía igual.
- Causa: la documentación/ejemplos usados como referencia en los 2 intentos anteriores correspondían a clases de versiones previas de Filament (`.fi-sidebar-item-active`, `.fi-sidebar-item-button`), que no existen en la 5.7.6 instalada en este proyecto.
- Se clonó el código fuente real del tag `v5.7.6` de `filamentphp/filament` (mismo criterio que el bug del `$view` estático, sección 10 de `MEMORIA.md`) y se confirmó contra `sidebar.css`/`item.blade.php` que la clase activa real es `.fi-active` sobre `<li class="fi-sidebar-item">`, el botón es `.fi-sidebar-item-btn`, y el ícono se puede seleccionar por `.fi-sidebar-item-icon` (coincide con `.fi-icon` en el mismo `<svg>`).
- Reescrito con los selectores exactos verificados, sin depender de sintaxis de anidamiento (por las dudas de compatibilidad con el pipeline de build del proyecto).
- **Aún sin confirmar por el usuario en el entorno real** — corregido sin acceso a Sail/npm, pero esta vez validado contra el código fuente real de la versión exacta instalada, no contra documentación genérica.

---

## [2026-08-25] Fix 2: ítem activo del menú lateral seguía sin fondo sólido

- El usuario confirmó por captura de pantalla, tras aplicar el patch anterior y recompilar, que (1) el fondo de página y (2) las sombras en tarjetas sí se veían — buena separación entre página y tarjetas. Pero (3) el ítem activo del menú lateral ("Escritorio") seguía con el pill tenue por defecto de Filament, no el fondo sólido azul marino esperado.
- Causa: la regla usaba combinador de hijo directo (`.fi-sidebar-item-active > .fi-sidebar-item-button`), que no coincidía con la estructura real del DOM, y sin `!important` tampoco ganaba contra el CSS propio de Filament.
- Fix: reescrito con anidamiento CSS y combinador descendiente (mismo patrón que usa la documentación oficial de Filament para este hook exacto) + `!important` en los 3 valores.
- **Aún sin confirmar por el usuario en el entorno real** — corregido sin acceso a Sail/npm en esta sesión, pendiente que el usuario aplique el patch y recompile.

---

## [2026-08-25] Fix: falta de contraste entre superficies (fondo, sidebar, tarjetas)

- El usuario, tras ver el cambio de paleta anterior aplicado en el entorno real (captura de pantalla), señaló que "el sistema no tiene contraste con nada" — el color de marca ya se veía bien en los gráficos, pero el fondo de página, la barra lateral y las tarjetas usaban tonos casi idénticos entre sí, así que nada se separaba visualmente (ítem activo del menú apenas se distinguía, tarjetas "fundidas" con el fondo).
- `theme.css`: se agregaron 3 reglas puntuales, sin tocar layout — fondo de página (`.fi-main`) gris-azulado en vez de casi blanco; sombra sutil en tarjetas/secciones (`.fi-section`) y en los 4 indicadores (`.fi-wi-stats-overview-stat`); ítem activo del menú lateral con fondo sólido azul marino y texto blanco en vez de solo texto azul.
- Se usaron hook classes documentadas como estables de Filament (`.fi-main`, `.fi-section`, `.fi-sidebar-item-active`, etc.), pero no se pudieron confirmar contra el HTML real del proyecto (sin acceso a Sail/inspector en esta sesión) — si alguna clase no coincide exactamente con la versión instalada (Filament 5.7.6), la regla correspondiente simplemente no tendría efecto, sin romper nada.
- **Aún sin confirmar por el usuario en el entorno real.**

---

## [2026-08-25] Rediseño visual: paleta de marca (azul marino + verde azulado) en panel y gráficos

- A pedido del usuario, que compartió una captura del Dashboard gerencial pidiendo ideas de color/organización, se propuso una paleta de 3 colores con significado (no decorativa) y se aplicó directo al panel.
- `AdminPanelProvider.php`: color primario del panel (botones, navegación, elementos activos de Filament) cambiado de `Color::Teal` (paleta Tailwind por defecto) a `Color::hex('#0C447C')` — azul marino, tomado del logo real de la clínica (ver sección 8.1 de `MEMORIA.md`).
- `IngresosPorMesChartWidget.php`: color de la serie "Facturado" cambiado de gris (`#94a3b8`) a azul claro (`#85B7EB`); color de la serie "Cobrado" cambiado de cian (`#0e7490`) a verde azulado (`#0F6E56`) — refuerza la distinción semántica ya existente entre "lo que se vendió" y "lo que entró a caja" (facturado = tono neutro de la marca, cobrado = verde, asociado a positivo/completado).
- `FacturacionPorAreaChartWidget.php`: color de la única serie (citas o monto facturado, según el selector) cambiado de cian (`#0e7490`) al mismo azul marino de marca (`#0C447C`), consistente con el resto del panel.
- Solo colores — no se tocó ninguna query, layout, ni estructura de datos de los 3 widgets. `theme.css` no requirió cambios (el color primario de Filament no depende de ese archivo, se resuelve vía `FilamentColor`/`Color::hex()`).
- **Aún sin confirmar por el usuario en el entorno real** — cambio de colores puros, sin acceso a Sail/npm para compilar y ver el resultado; no se pudo correr `php -l` porque el entorno de trabajo no tiene PHP disponible esta vez (instalación de `php-cli` falló por 404 en los paquetes de Ubuntu). Cambios acotados a valores de string/hex, riesgo de sintaxis bajo, pero pendiente de confirmación visual real.

---

## [2026-08-25] Confirmación final: Dashboard gerencial probado con datos históricos reales

- El usuario confirmó por captura que, tras el fix de Carbon 3, "Alertas operativas" muestra días enteros y positivos en sus 3 secciones, y el resto del Dashboard gerencial (indicadores, ambos gráficos) se ve correcto.
- Con esto se cierra el ciclo completo de esta sesión: `DemoHistoricoSeeder` generó datos históricos de prueba, y los 2 bugs que destapó (`translatedFormat()` sin cast, `diffInDays()` con signo/decimales de Carbon 3) quedan corregidos y confirmados.
- Solo actualización de estado en `MEMORIA.md`/`CHANGELOG.md` — no se tocó código en esta entrada.

---

## [2026-08-25] Fix: días con decimales y en negativo en Alertas operativas (Carbon 3)

- Tras el fix del error 500 anterior, el usuario confirmó por captura de pantalla que el widget cargaba, pero mostraba días con decimales larguísimos y **en negativo** en Facturas vencidas (`-315.675032824 días`) y Camas ocupadas (`-22.002572832199 días internado`).
- Causa: Carbon 3 (usado en este proyecto sobre Laravel 13) cambió el comportamiento por defecto de `diffInDays()` — ahora devuelve una diferencia con signo y con decimales, en vez del entero absoluto de Carbon 2.
- Fix: las 3 llamadas a `now()->diffInDays(...)` en `alertas-operativas-widget.blade.php` (lotes, facturas, camas) ahora pasan `true` como segundo argumento (valor absoluto) y se envuelven en `(int) round(...)`.
- Ver `MEMORIA.md` (decimoséptima entrada del día) para el detalle completo.
- **Aún sin confirmar por el usuario en el entorno real** — corregido sin acceso a Sail/MySQL.

---

## [2026-08-25] Fix: `Call to a member function translatedFormat() on string` en Facturas vencidas

- Al recargar el panel con datos del `DemoHistoricoSeeder` ya cargados, el widget "Alertas operativas" tiraba un error 500 al renderizar la sección de Facturas vencidas.
- Causa (preexistente, no introducida por el seeder — solo nunca se había disparado antes por falta de datos de prueba con esa condición): `Factura.fecha` no tiene cast a `Carbon` en el modelo, pero `resources/views/filament/widgets/alertas-operativas-widget.blade.php` llamaba `$factura->fecha->translatedFormat(...)` asumiendo que sí lo era.
- Fix: se envolvió `$factura->fecha` en `Carbon::parse(...)` en las 2 líneas afectadas de esa vista, mismo patrón ya usado en `FacturaResource`/`CitaResource`. No se tocó el modelo ni ningún otro archivo.
- Ver `MEMORIA.md` (decimosexta entrada del día) para el detalle completo.
- **Aún sin confirmar por el usuario en el entorno real** — corregido sin acceso a Sail/MySQL.

---

## [2026-08-25] Confirmación: DemoHistoricoSeeder corrió sin errores en el entorno real

- El usuario corrió `./vendor/bin/sail artisan db:seed --class=DemoHistoricoSeeder` y terminó con "Datos de demostración cargados." en ~1.8s, sin errores de foreign key ni de tipo.
- Nota de entorno (no del proyecto): el usuario no tenía el alias `sail` definido — se usó la ruta directa `./vendor/bin/sail`, sin cambios de código.
- Pendiente: confirmación visual de que el Dashboard gerencial se ve poblado como se esperaba en `/admin`.
- Solo actualización de estado en `MEMORIA.md`/`CHANGELOG.md` — no se tocó código en esta entrada.

---

## [2026-08-25] Seeder de datos históricos de demostración

- A pedido del usuario, se creó `database/seeders/DemoHistoricoSeeder.php` para poblar el sistema con datos de prueba realistas y ver el Dashboard gerencial (sección 6.6) funcionando con contenido en vez de vacío.
- Genera ~110 registros repartidos en los últimos 12 meses: 10 médicos, 22 pacientes, 18 citas, historias clínicas para las atendidas, 18 facturas, 6 ítems de inventario con 8 lotes, ~16 movimientos de inventario, 6 camas, 2 quirófanos, 5 internamientos, 3 cirugías (con médico adicional en el pivote), 5 órdenes de estudio y 2 servicios de ambulancia.
- Diseñado a propósito para cruzar los 3 umbrales de `AlertasOperativasWidget` (lote por vencer ≤90 días, factura pendiente vencida >30 días, cama ocupada >14 días) además de darle curva mensual a los 2 `ChartWidget`.
- Es standalone, no se agregó a `DatabaseSeeder.php` — se corre aparte con `sail artisan db:seed --class=DemoHistoricoSeeder`, sobre una base ya limpia de `citas`/`facturas`/etc. (el usuario limpió antes a mano vía `tinker`, conservando `areas` y `users`).
- Ver `MEMORIA.md` (decimocuarta entrada del día) para el detalle completo.
- **Aún sin confirmar por el usuario en el entorno real** — validado solo con `php -l`, no se corrió contra una base de datos real (sin acceso a Sail/MySQL en este entorno de trabajo).

---

## [2026-08-25] Confirmación: Dashboard gerencial completo, de punta a punta en el entorno real

- El usuario confirmó que, tras aplicar el patch del `@source` faltante y recompilar con `npm run build`, las 3 tarjetas de "Alertas operativas" ya se ven lado a lado en 3 columnas.
- Con esto, el Dashboard gerencial completo — las 3 sesiones planificadas en `MEMORIA.md` sección 6.6 (indicadores clave, gráficos, alertas operativas) — queda **construido y confirmado de punta a punta en el entorno real**.
- Solo actualización de estado en `MEMORIA.md` (secciones 6.6, 6.6.3) y `CHANGELOG.md` — no se tocó código en esta entrada.

---

## [2026-08-25] Fix: layout de 3 columnas roto en AlertasOperativasWidget — faltaba `@source` en theme.css

- Tras el fix de `$view`, el panel volvía a cargar, pero el usuario reportó que las 3 secciones de "Alertas operativas" salían apiladas en una sola columna en vez de lado a lado.
- Causa (preexistente en el proyecto, no introducida en esta sesión): `resources/css/filament/admin/theme.css` solo declaraba `@source '../../../../app/Filament';` — le faltaba `resources/views/filament`, la carpeta de vistas Blade custom de widgets. Nunca se había notado porque `AlertasOperativasWidget` es el primer widget del proyecto con una vista Blade propia que usa clases sueltas de Tailwind (el resto se apoya solo en componentes ya estilizados de Filament).
- Fix: se agregó `@source '../../../../resources/views/filament';` al `theme.css` — una sola línea, no se tocó el widget ni su vista.
- Ver `MEMORIA.md` secciones 6.6.3 y 8.2 para el detalle completo.
- **Aún sin confirmar por el usuario en el entorno real** — corregido sin acceso a Vite/`npm run build`, pendiente que el usuario aplique el patch y recompile los assets del tema.

---

## [2026-08-25] Fix: panel completo caído por `$view` estática en AlertasOperativasWidget

- Al aplicar el patch de la Sesión 3 y correr `sail artisan optimize:clear`, apareció un error fatal de PHP que tumbaba **todo el panel** (`/admin` no cargaba en absoluto): `Cannot redeclare non static Filament\Widgets\Widget::$view as static App\Filament\Widgets\AlertasOperativasWidget::$view`.
- Causa: `AlertasOperativasWidget` declaraba `protected static string $view`, siguiendo un patrón de documentación/ejemplos de **Filament 3.x**. Este proyecto usa **Filament 5.7.6**, donde `Filament\Widgets\Widget::$view` es no estática (`protected string $view;`) — PHP no permite cambiar de estática a no estática (ni al revés) al heredar.
- Fix: se cambió a `protected string $view = '...'` (no estática) en `app/Filament/Widgets/AlertasOperativasWidget.php`, verificado contra el código fuente real de la v5.7.6.
- Ver `MEMORIA.md` sección 6.6.3 para el detalle completo y una nota de troubleshooting para casos similares.
- **Aún sin confirmar por el usuario en el entorno real** — corregido y validado con `php -l`, pendiente que el usuario aplique el patch nuevo.

---

## [2026-08-25] Sesión 3 del Dashboard gerencial — Alertas operativas (última de las 3 sesiones)

- Se construyó `app/Filament/Widgets/AlertasOperativasWidget.php` + su vista `resources/views/filament/widgets/alertas-operativas-widget.blade.php`, un widget custom (`Filament\Widgets\Widget`, no `StatsOverviewWidget`/`ChartWidget`) con 3 alertas operativas, cada una con contador, hasta 5 ítems concretos y link al listado completo del recurso:
  - **Lotes por vencer**: `LoteInventario` con `fecha_vencimiento` dentro de 90 días y stock actual > 0.
  - **Facturas vencidas**: `Factura` con `estado_pago = pendiente` y más de 30 días desde su `fecha` de emisión.
  - **Camas ocupadas hace mucho**: `Cama` con internamiento activo (`fecha_alta` nula) con más de 14 días desde `fecha_ingreso`.
- Los 2 umbrales sin confirmar (cama ocupada, factura vencida) se le preguntaron al usuario, que pidió que se decidiera un valor razonable en su lugar — quedaron en 14 y 30 días respectivamente, documentados como constantes editables en la clase. El umbral de lotes (90 días) se mantuvo igual al default ya existente en `LoteInventario::porVencer()`.
- `CitasDeHoyWidget` se corrió de `$sort = 3` a `4` para dejarle lugar al widget nuevo en `$sort = 3`.
- No se creó ninguna tabla, migración ni modelo nuevo — 100% queries sobre `LoteInventario`, `Factura` y `Cama`/`Internamiento` ya existentes.
- Con esto, el Dashboard gerencial completo (las 3 sesiones planificadas en `MEMORIA.md` sección 6.6) queda construido.
- Ver `MEMORIA.md` sección 6.6.3 para el detalle completo.
- **Aún sin confirmar por el usuario en el entorno real** — escrito sin acceso directo a Sail, se validó sintaxis instalando `php-cli` en el entorno de trabajo y corriendo `php -l`.

---

## [2026-08-25] Confirmación: Sesión 2 del Dashboard gerencial, completa en el entorno real

- El usuario confirmó que el fix del gráfico "Por área" (entrada anterior) funcionó — ahora muestra las barras correctamente en ambas métricas ("Cantidad de citas" y "Monto facturado").
- Con esto, la Sesión 2 completa (2 gráficos, sus selectores, el formato `$`, y la limitación de facturas sin `cita_id`) queda **confirmada de punta a punta en el entorno real**.
- Solo actualización de estado en `MEMORIA.md` (secciones 6.6.2 y 7) y `CHANGELOG.md` — no se tocó código en esta entrada.

---

## [2026-08-25] Fix: gráfico "Por área" en blanco con métrica "Cantidad de citas"

- Bug reportado por el usuario al probar en el entorno real el ajuste de formato `$` de la entrada anterior: el gráfico "Por área" quedaba completamente vacío (sin barras, sin ejes) con "Cantidad de citas" seleccionado; solo funcionaba con "Monto facturado".
- Causa: `getOptions()` en `FacturacionPorAreaChartWidget` devolvía tipos de dato distintos según la métrica — array vacío `[]` para citas, objeto `RawJs` para facturación. Ese cambio de tipo entre una respuesta y otra rompía el JS del gráfico al recambiar el selector con Livewire.
- Fix: `getOptions()` ahora siempre devuelve `RawJs`, con la misma estructura en ambos casos — el signo `$` se decide *adentro* del JS interpolando un booleano de PHP, no cambiando el tipo devuelto.
- Verificado con un script PHP chico (fuera del repo) simulando el heredoc, para confirmar la interpolación antes de dar el fix por bueno.
- Sintaxis validada con `php -l`. **Pendiente confirmar este fix puntual en el entorno real.**
- Detalle completo, incluido el troubleshooting general para casos similares, en la sección **6.6.2** de `MEMORIA.md`.

---

## [2026-08-25] Fix/UX: formato $ en gráficos del Dashboard gerencial + confirmación en real

- Sesión 2 (entrada anterior) **confirmada funcionando en el entorno real**: probada con 4 facturas de prueba, incluida una sin `cita_id` — se verificó que queda correctamente excluida del gráfico "Por área" (limitación ya documentada), mientras sí cuenta en "Ingresos por mes".
- A pedido del usuario, se agregó formato `$` + separador de miles al eje Y y al tooltip de `IngresosPorMesChartWidget` (siempre, ambas series son montos) y `FacturacionPorAreaChartWidget` (solo cuando la métrica activa es "Monto facturado"). Implementado con `getOptions()` + `Filament\Support\RawJs` (callback de Chart.js), el mecanismo documentado por Filament para este caso.
- Sintaxis validada con `php -l`. **Pendiente confirmar este ajuste puntual en el entorno real.**
- Detalle completo en la sección **6.6.2** de `MEMORIA.md`.

---

## [2026-08-25] Feature: Sesión 2 del Dashboard gerencial — gráficos

- Se construyeron los 2 `ChartWidget` de la Sesión 2: `app/Filament/Widgets/IngresosPorMesChartWidget.php` y `app/Filament/Widgets/FacturacionPorAreaChartWidget.php`, ubicados entre `IndicadoresGerencialesWidget` (`$sort = 0`) y `CitasDeHoyWidget` (corrido de `$sort = 1` a `3`). Visibles solo para el rol `admin`, mismo patrón `canView()` que el resto del Dashboard gerencial.
- **Ingresos por mes** (`$sort = 1`, gráfico de barras): 2 series por mes — "Facturado" (total) y "Cobrado" (`estado_pago = pagado`) — con un selector de rango (`getFilters()`): últimos 6 meses (default), últimos 12 meses, año actual, año anterior. Resuelve la pregunta pendiente de "rango de fechas seleccionable", respondida por el usuario.
- **Por área** (`$sort = 2`, gráfico de barras): cantidad de citas o monto facturado por área/especialidad, con un selector de métrica (`getFilters()`: "Cantidad de citas" / "Monto facturado"), también respondida por el usuario. Alcance fijo al año en curso — no se agregó un segundo selector de rango en este widget para no depender del mecanismo de filtros por schema de Filament (más nuevo, sin validar en este proyecto); queda documentado como supuesto editable en MEMORIA.md 6.6.2.
- Limitación conocida documentada: el monto facturado por área solo cuenta facturas con `cita_id` asociado (una factura sin cita no se puede atribuir a ningún área con el modelo actual).
- No se creó ninguna tabla ni migración — solo queries sobre `Factura`, `Cita` y `Area`, ya existentes.
- Sintaxis validada con `php -l`. **Pendiente confirmar en el entorno real.**
- Detalle completo en la sección **6.6.2** de `MEMORIA.md`.

---

## [2026-08-25] Feature: Sesión 1 del Dashboard gerencial — indicadores clave

- Se construyó `app/Filament/Widgets/IndicadoresGerencialesWidget.php` (`StatsOverviewWidget`), la Sesión 1 del plan documentado en la entrada anterior.
- 4 indicadores, visibles solo para el rol `admin` (`canView()`), ubicados arriba de `CitasDeHoyWidget` en el Dashboard (`$sort = 0`):
  - **Ingresos del mes**: suma de `facturas.monto` pagadas en el mes actual, con variación % vs. mes anterior.
  - **Por cobrar**: suma de `facturas.monto` en estado pendiente (cartera completa, no solo del mes).
  - **Citas atendidas hoy**: cuenta de citas con estado `atendida` hoy, con el total de la semana como descripción.
  - **Ocupación de camas**: camas con internamiento activo sobre el total, con porcentaje y color según el nivel de ocupación.
- No se creó ninguna tabla ni migración — solo queries sobre `Factura`, `Cita` y `Cama`/`Internamiento`, ya existentes.
- Sintaxis validada con `php -l`. **Pendiente confirmar en el entorno real.**
- Detalle completo en la sección **6.6.1** de `MEMORIA.md`.

---

## [2026-08-25] Documentación: plan del Dashboard gerencial dividido en 3 sesiones

- El usuario pidió dividir la construcción del Dashboard gerencial (propuesta documentada en la entrada anterior) en 3 sesiones separadas, porque el módulo completo es demasiado para una sola sesión.
- Se documentó el plan (sin tocar código, a pedido explícito del usuario): **Sesión 1** — indicadores clave (`StatsOverviewWidget`, sin decisiones pendientes, puede construirse ya); **Sesión 2** — los 2 gráficos (ingresos por mes y por área), depende de definir si el gráfico por área mide citas/facturación/ambos y si el rango de fechas es fijo o seleccionable; **Sesión 3** — alertas operativas, depende de definir el umbral de días para la alerta de camas.
- Detalle completo en la sección **6.6** de `MEMORIA.md`.

---

## [2026-08-25] Documentación: propuesta de Dashboard gerencial para el admin

- El usuario preguntó cómo puede el administrador saber si la clínica "está ganando o no" — el Dashboard actual solo tiene el widget de Citas de hoy, sin ningún indicador financiero, gráfico ni resumen operativo.
- Se documentó una propuesta (sin tocar código, a pedido explícito del usuario): Widgets nuevos visibles solo para `admin` — indicadores clave (ingresos del mes vs. mes anterior, por cobrar, citas atendidas, ocupación de camas), gráfico de ingresos por mes, gráfico por área/especialidad, y alertas operativas (inventario por vencer, facturas vencidas, camas ocupadas hace demasiado tiempo).
- Confirmado que no hace falta ninguna tabla ni migración nueva — todo se puede construir con `facturas`, `citas`, `camas`/`internamientos` y `lotes_inventario`, que ya existen.
- Detalle completo en la sección **6.6** de `MEMORIA.md`.

---

## [2026-08-25] Fix: Paciente/Cita relacionada ya no se muestran en traslados/ajustes de inventario

- El usuario, revisando el formulario de **Movimientos de Inventario**, notó dos cosas: (1) al registrar un traslado entre áreas seguían apareciendo los campos "Paciente" y "Cita relacionada", que no tienen sentido ahí porque un traslado no es consumo de un paciente; y (2) confirmó que la ausencia del campo "Usuario" en el formulario es intencional (se completa solo con `Auth::id()` al crear, ver `CreateMovimientoInventario.php`) y que el registro queda visible después en la columna "Registrado por" del listado.
- `MovimientoInventarioForm.php`: se agregó `->visible(fn ($get) => $get('tipo_movimiento') === 'salida')` a `paciente_id` y `cita_id`, mismo patrón reactivo (`->live()` en `tipo_movimiento`) que ya usaban `area_origen`/`area_destino`. Ahora esos dos campos solo aparecen cuando el tipo de movimiento es "Salida", el único caso que representa consumo real en atención al paciente.
- No se tocó nada más del formulario ni de la tabla — `area_origen`/`area_destino` y la columna "Registrado por" (`usuario.name`) siguen igual, ya funcionaban correctamente.
- **Confirmado funcionando por el usuario en el entorno real.**

---

## [2026-08-25] Preparación entrevista de seguimiento — expediente clínico y checklist

- Sesión de planificación en paralelo al patch grande, sin tocar código hasta este cambio (documentación únicamente).
- El cliente confirmó por WhatsApp que la clínica **sí tiene personal dedicado en farmacia** — respalda la idea de un 4º rol `farmacéutico`/`bodega`, aunque el mecanismo real (cuándo/quién registra el consumo de insumos) sigue sin confirmarse, pendiente para la entrevista formal del 25/08 con Ysrael Calle.
- El cliente confirmó explícitamente que "digitalizar el historial clínico" apunta a un **expediente completo**: antecedentes, alergias, signos vitales y resultados de exámenes, todo conectado.
- Al reconciliar contra lo ya construido (ver sección 6.2/6.3 de `MEMORIA.md`), se detectó que **resultados de exámenes con archivo adjunto ya está cubierto** por `OrdenEstudio` — no hace falta módulo nuevo para eso. Los 3 módulos que sí faltan: **Alergias, Antecedentes, Signos Vitales** (diseño pensado, sin implementar).
- Se detectó un matiz nuevo sobre el módulo de inventario (sección 6.3): el cliente pidió que el registro de insumos cubra farmacia, quirófano, admisión y facturación — 4 puntos, no solo farmacia. Pendiente de confirmar el detalle en la entrevista.
- Se armó un checklist completo de preguntas para la entrevista (entregado al usuario como documento Word, no versionado en el repo).
- Detalle completo en la sección **6.5** de `MEMORIA.md`.

---

## [2026-08-25] Agrupar el menú lateral del panel en categorías

- Solo el módulo de Infraestructura tenía `$navigationGroup` asignado; los otros 10 Resources aparecían sueltos y mezclados en el sidebar. El usuario lo notó al ver el listado de Facturas y pidió aplicar el mismo criterio de agrupación al resto.
- Se definieron 5 grupos: **Atención al paciente** (Pacientes, Citas, Historia Clínicas), **Facturación** (Facturas), **Infraestructura** (sin cambios), **Inventario** (Item/Lote/Movimiento Inventarios), **Administración** (Áreas, Médicos, Usuarios).
- En cada uno de los 10 Resources: se agregó `use UnitEnum;` y `protected static string|UnitEnum|null $navigationGroup = '...';`, mismo patrón que ya usaba Infraestructura.
- Cambio puramente organizativo — no se tocaron permisos ni íconos. Ver sección 8.4 de `MEMORIA.md` para el detalle completo, incluyendo por qué no se tocaron los íconos repetidos en esta vuelta.
- **Pendiente probar en el entorno real** (no se pudo correr la app en este entorno de trabajo).

---

## [2026-08-25] Logo real de la clínica + fix de mixed content para túneles HTTPS

- **Reemplazo del logo**: el usuario compartió el logo oficial de la clínica (imagen suelta + embebido en `Servicios_CB_2026.pdf`). Se reemplazó el logo provisorio (triángulo/hoja dibujado a mano en SVG, ver entrada `[2026-08-23]`) por el real.
- Fuente: raster 184×185px (no hay vectorial disponible), reconstruido con su transparencia real combinando la imagen de color del PDF con su `SMask` — la primera extracción directa venía sin alpha (fondo blanco sólido horneado).
- Archivos nuevos en `public/images/`: `logo.png` y `logo-white.png` (lockup vertical completo, navy y blanco), `logo-horizontal.png` y `logo-horizontal-white.png` (recomposición horizontal armada recortando/reacomodando las mismas piezas del logo real — necesaria porque el lockup vertical original, a la altura chica del header del panel, dejaba el texto ilegible). `public/favicon.ico` regenerado desde el monograma real. El logo placeholder anterior (`logo.svg`, `icon.svg`) se archivó en `public/images/_legacy/`, no se borró.
- `AdminPanelProvider.php`: `->brandLogo()` ahora apunta a `logo-horizontal.png`, `->favicon()` a `favicon.ico`.
- `resources/views/pdf/factura.blade.php`: se agregó el logo (`logo.png`, 50px alto) al encabezado de la factura, que antes solo mostraba el nombre en texto.
- **Pendiente probar en el entorno real** (no se pudo correr la app en este entorno de trabajo, solo se validó con renders simulados fuera de Laravel).

- **Fix de "mixed content" con túneles HTTPS**: al exponer el sistema local con Cloudflare Tunnel (para mostrarlo en la entrevista sin llevar la laptop pesada — la app corre en la laptop de casa y se accede por un link temporal), el navegador bloqueaba todo el CSS/JS del panel por "mixed content": la página cargaba por HTTPS (vía túnel) pero Laravel generaba los links a los assets con `http://`, porque no detecta que la petición original llegó por HTTPS detrás del proxy.
- `AppServiceProvider.php`: se agregó `URL::forceScheme('https')` dentro de `boot()`, condicionado a que `config('app.url')` empiece con `https://`. En local (`APP_URL=http://localhost`) no se activa, así que no cambia nada del comportamiento normal de desarrollo.

---

## [2026-08-24] Orden de Estudio: nombre del adjunto usa paciente + tipo de estudio

- Segundo cambio de criterio en el nombre de archivo del adjunto (`resultado_archivo`), a pedido del usuario, después de confirmar que el criterio anterior (ULID + nombre original del archivo subido, ver entrada anterior) funcionaba. El nombre original que trae el archivo del navegador no dice nada sobre a quién pertenece el resultado, así que se cambió a **`ULID-nombre-del-paciente-tipo-de-estudio.ext`** (ej. `01jz3k9x8h2m4n6p8q0r2s4t-paul-guerrero-laboratorio.pdf`).
- `getUploadedFileNameForStorageUsing()` ahora recibe `Get $get` además de `TemporaryUploadedFile $file`, para leer `paciente_id` y `tipo` de otros campos del mismo formulario en el momento exacto del upload — mismo patrón que ya usaba `UserForm.php` (`->visible(fn (Get $get) => $get('rol') === 'medico')`).
- Se extrajo la lista de tipos de estudio a un método privado `tiposEstudio()` en `OrdenEstudioForm`, reusado tanto por el `Select::make('tipo')` como por el nombre de archivo, para no repetir el arreglo en dos lugares.
- **Importante para el flujo de trabajo**: el paciente y el tipo deben estar seleccionados en el formulario *antes* de subir el archivo — si se sube primero, el nombre cae a un texto genérico (`...-paciente-estudio.pdf`) en vez de fallar, ya que el orden de llenado de los campos no está forzado en el formulario.
- **Confirmado funcionando por el usuario en el entorno real.**

---

## [2026-08-24] Fix: Orden de Estudio — adjunto sin ver/descargar, sin restricción de tipo, y nombre de archivo ilegible

- **Bug 1 — sin botones de ver/descargar**: el campo `FileUpload::make('resultado_archivo')` en `OrdenEstudioForm.php` no tenía `->openable()`/`->downloadable()`, así que no aparecía ninguna forma de abrir o descargar el archivo ya subido desde el formulario de edición.
- **Bug 2 — sin restricción de tipo de archivo**: el campo aceptaba cualquier tipo de archivo (Word, ZIP, ejecutables, etc.), no ideal para un campo de "resultado de estudio". Se restringió con `->acceptedFileTypes()` a PDF, JPG, PNG y WEBP.
- **Bug 3 — `ERR_CONNECTION_REFUSED` al usar "ver"**: después de agregar `->openable()`, el botón llevaba a `http://localhost:8000/storage/...`, un puerto donde no había nada escuchando. **Causa real, de configuración del entorno del usuario, no de código**: su `.env` tenía `APP_URL=http://localhost:8000`, pero Sail expone la app en el puerto 80 (`http://localhost`, sin puerto), según `compose.yaml` (`APP_PORT:-80`). Se corrigió cambiando `APP_URL=http://localhost` en el `.env` del usuario y corriendo `sail artisan config:clear` — no fue necesario ningún cambio de código para este punto. **Confirmado funcionando.**
- **Cambio adicional a pedido del usuario — nombre de archivo legible**: el nombre generado por Filament por defecto es un string aleatorio de 26 caracteres sin relación con el archivo original (ej. `01M0VJ41XRD4TQH9RXGDZAV491.pdf`), lo cual es normal (evita colisiones) pero dificulta identificar el archivo a simple vista en el disco. Se agregó `->getUploadedFileNameForStorageUsing()` para generar en cambio un **ULID corto + el nombre original slugificado + extensión** (ej. `01jz3k9x8h2m4n6p8q0r2s4t-radiografia-torax.pdf`). Se descartó usar `->preserveFilenames()` a secas (nombre original tal cual) porque tiene riesgo real de colisión — dos resultados con el mismo nombre de archivo se sobreescribirían entre sí sin aviso — el ULID adelante lo evita sin perder legibilidad.
- **Confirmado funcionando por el usuario en el entorno real** (ver y descargar el PDF adjunto, con nombre legible).

---

## [2026-08-24] Fix: Cirugía — Repeater de médicos adicionales insertaba médicos vacíos

- **Bug reportado por el usuario** al probar el módulo de Infraestructura física (sección 6.2) en el entorno real por primera vez: al crear una Cirugía agregando al menos un "médico adicional" (anestesiólogo/ayudante), Filament tiraba `SQLSTATE[HY000]: General error: 1364 Field 'nombres' doesn't have a default value` al intentar `insert into medicos (updated_at, created_at) values (...)`.
- **Causa**: `Repeater::make('medicosAdicionales')->relationship()` en `CirugiaForm.php`, sobre la relación `Cirugia::medicosAdicionales()` (un `belongsToMany` con pivote `cirugia_medico`, columna extra `rol`). Filament interpreta `Repeater::relationship()` como "crear un registro **nuevo** en la tabla relacionada (`medicos`) por cada fila del repeater" — no como "seleccionar un médico **ya existente** y guardar su rol en la tabla pivote". Como el schema del repeater solo tenía `medico_id` y `rol` (campos que no existen en `medicos`), el insert quedaba vacío salvo los timestamps.
- **Efecto secundario detectado por el usuario**: mientras el error no daba feedback visual claro, dio clic varias veces al botón "Crear cirugía" — cada clic sí llegó a insertar la fila en `cirugias` antes de fallar en el paso del repeater, dejando 4 registros duplicados en el listado. Se le indicó borrarlos a mano desde `/admin/cirugias` (selección múltiple + Eliminar) — no era necesario ni posible corregir eso con código, ya estaban en la base de datos del usuario.
- **Solución aplicada**:
  - `CirugiaForm.php`: se quitó `->relationship()` del `Repeater('medicosAdicionales')` — ahora es un campo de estado normal, sin auto-guardado hacia el modelo relacionado.
  - `CreateCirugia.php`: nuevo `mutateFormDataBeforeCreate()` que extrae los datos del repeater antes de crear la Cirugía (no son un campo de `cirugias`, se descartarían igual por `$fillable`, pero se sacan explícito para claridad), y nuevo `afterCreate()` que hace `sync()` manual de `medicosAdicionales()` con el `rol` de cada médico en la tabla pivote.
  - `EditCirugia.php`: nuevo `mutateFormDataBeforeFill()` que carga los médicos adicionales ya guardados (con su `rol`) al abrir el formulario de edición, para que se vean en el Repeater; `mutateFormDataBeforeSave()` + `afterSave()` con el mismo patrón de `sync()` que en Create.
- **Alcance del bug**: solo afectaba a Cirugía — es el único Resource del módulo 6.2 (o de todo el sistema) con un Repeater manejando una relación `belongsToMany` con datos extra de pivote. El resto de Resources de 6.2 (Camas, Quirófanos, Internamientos, Órdenes de Estudio, Servicios de Ambulancia) no usa este patrón y no se vio afectado.
- **Confirmado funcionando por el usuario en el entorno real** (creación de cirugía con médico adicional y su rol, guardado correctamente en la tabla pivote).

---

## [2026-08-24] Segundo módulo construido: Infraestructura física (camas, quirófanos, cirugías, estudios, ambulancia)

- A pedido del usuario, se **construyó** (mismo criterio que el módulo de Medicamentos e Insumos) la propuesta de la sección 6.2 de `MEMORIA.md` — infraestructura física: Hospitalización/UCI/UCIN, Quirófanos/Cirugías, Procedimientos/Estudios, Emergencias, Ambulancia. Se avanzó con **supuestos razonables** sobre las 5 decisiones que seguían sin confirmar con la clínica, documentándolos en la sección 6.2 actualizada para poder ajustarlos después.
- **7 tablas nuevas** (migraciones `2026_08_24_130000` a `2026_08_24_130006`):
  - `camas` — número, tipo (hospitalización/UCI/UCIN), piso opcional. Sin columna de estado — se deriva de si tiene un internamiento activo, mismo criterio que el stock del módulo de inventario.
  - `quirofanos` — número, nombre opcional, y sí con columna de estado editable (libre/preparación/en cirugía/limpieza), a diferencia de camas.
  - `internamientos` — paciente, cama, médico responsable, cita opcional, fecha de ingreso/alta (alta nullable mientras sigue internado), motivo, origen (programado/emergencia) y prioridad ESI de triage.
  - `cirugias` — paciente, quirófano, cirujano principal, cita opcional, fecha/hora, tipo de cirugía, estado, notas.
  - `cirugia_medico` — pivote para médicos adicionales de una cirugía (anestesiólogo, ayudantes), gestionado con un `Repeater` en el formulario en vez de un Resource propio.
  - `ordenes_estudio` — modelo unificado para laboratorio, rayos X, ecografía, centro de imagen, endoscopía alta/baja, gastroenterología y procedimientos ambulatorios, con resultado en texto y/o archivo adjunto opcional.
  - `servicios_ambulancia` — la más simple: origen, destino, fecha/hora, paciente opcional (puede no estar registrado todavía).
- **2 columnas nuevas en `citas`** (`origen`, `prioridad`, migración `2026_08_24_130007`) — cubren "Emergencias" sin necesitar una tabla propia: una emergencia que no requiere internamiento queda como una Cita normal con esos campos. Se agregaron también al formulario y tabla de Citas existentes (selector de prioridad condicional, badge de emergencia, filtro rápido).
- **6 modelos nuevos**: `Cama` (con `ocupada()` derivado), `Quirofano`, `Internamiento`, `Cirugia` (con `medicosAdicionales()` BelongsToMany), `OrdenEstudio`, `ServicioAmbulancia`. Se agregaron también las relaciones inversas correspondientes a `Paciente`, `Medico` y `Cita`.
- **6 Resources completos de Filament**, agrupados bajo "Infraestructura" en el menú: `Camas`, `Quirofanos`, `Internamientos`, `Cirugias`, `OrdenesEstudio`, `ServiciosAmbulancia`.
  - Camas/Quirófanos: criterio de catálogo (admin todo, recepción edita, médico solo ve).
  - Internamientos/Cirugías: criterio operativo tipo Citas (admin y recepción crean, médico ve/edita solo lo suyo filtrado por `medico_id`/`medico_principal_id`, nadie salvo admin borra).
  - Órdenes de estudio: igual, pero el médico también puede crear (es quien solicita el estudio).
  - Servicios de ambulancia: admin/recepción gestionan, médico solo ve.
- **Supuestos documentados en esta entrada** (ver detalle ampliado con tabla en la sección 6.2 actualizada de `MEMORIA.md`): estado en tiempo real (sí, igual que inventario); cirugía puede agendarse sin pasar por Cita (sí, `cita_id` nullable); resultados de estudio con adjunto desde el día uno (sí, disco local de Sail, sin evaluar S3 todavía); triage con escala ESI (sí, ya validado con investigación externa en una entrada previa); sedes/sucursales múltiples (no, sigue asumiendo una sola ubicación).
- **Validado con `php -l`** (se instaló PHP CLI temporalmente en el contenedor de trabajo para poder chequear sintaxis) — **pero, a diferencia del módulo de inventario, no se corrió la migración ni se probó en el entorno real todavía**, esta entrada se escribió sin acceso a Sail/MySQL.
- Entregado como patch (`git am`).

## [2026-08-24] Documentación: pregunta pendiente sobre si faltan roles (farmacia)

- Se le explicó al usuario, en un documento Word aparte, qué es cada módulo del sistema (con sus campos/opciones) y cómo se relacionan entre sí, incluyendo qué módulos no tienen relación directa.
- De ahí surgió la pregunta de si los 3 roles actuales (admin/recepción/médico) alcanzan. Al revisar la matriz de permisos real del código se confirmó un hueco: el rol **médico no tiene acceso a Ítems/Lotes/Movimientos de Inventario** (ni para ver), aunque es quien aplica el medicamento/insumo en la consulta — hoy ese registro depende de que recepción lo cargue aparte.
- Se agregó como pregunta pendiente nueva en la sección 6 de `MEMORIA.md`: si conviene darle al médico acceso a registrar sus propios movimientos de inventario, y si más adelante (cuando farmacia opere como puesto dedicado) conviene un 4º rol de farmacéutico/bodega. Es una decisión de negocio, no una corrección — con el volumen actual, 3 roles sigue pareciendo suficiente.
- **No se tocó ningún modelo, migración, Resource ni permiso** — es documentación de una pregunta abierta, el sistema actual no se ve afectado.
- Entregado como patch (`git am`).

## [2026-08-24] Confirmado en el entorno real: módulo de Medicamentos e Insumos

- El usuario probó en vivo el módulo construido en la entrada anterior (catálogo, lotes, movimientos) y confirmó que **todo funciona perfectamente**: creación de ítem del catálogo, creación de lote con vencimiento, movimiento de entrada (stock del lote y del ítem sube correctamente, calculado en vivo), movimiento de salida (stock baja correctamente), protección contra borrado de ítems/lotes con dependencias, y permisos por rol (recepción solo ve catálogo pero gestiona movimientos, médico sin acceso al módulo).
- Sin cambios de código en esta entrada — solo actualización de `MEMORIA.md` (sección 6.3) marcando el módulo como confirmado, mismo criterio que se usó con los demás módulos del sistema.
- Entregado como patch (`git am`).

## [2026-08-24] Primer módulo construido: Medicamentos e Insumos (catálogo, lotes, movimientos)

- A pedido del usuario, se empezó a **construir** (por primera vez con código, no solo documentación) la propuesta de la sección 6.3 de `MEMORIA.md` — el módulo de medicamentos/insumos que el contacto interno pidió, en vez de la infraestructura de la 6.2. El usuario también pidió avanzar con **supuestos razonables** sobre las decisiones que seguían sin confirmar con la clínica, documentándolos para poder ajustarlos después en vez de bloquear el desarrollo esperando esa confirmación.
- **3 tablas nuevas** (migraciones `2026_08_24_120000` a `2026_08_24_120002`):
  - `items_inventario` — catálogo (nombre, tipo medicamento/insumo, unidad_medida, stock_minimo opcional, precio_unitario opcional). Sin columna de stock — se deriva de los movimientos.
  - `lotes_inventario` — un lote por ítem (numero_lote, fecha_vencimiento), trazabilidad FEFO. Tampoco tiene columna de cantidad, mismo criterio.
  - `movimientos_inventario` — el ledger real: tipo_movimiento (entrada/salida/traslado/ajuste), cantidad, area_origen/area_destino (texto libre de una lista fija), fecha_hora, user_id (quién lo registró), paciente_id y cita_id opcionales (para enganchar consumo real en la atención de alguien), notas.
- **3 modelos nuevos**: `ItemInventario` (con `stockActual()` y `bajoStockMinimo()`), `LoteInventario` (con `stockActual()`, `vencido()`, `porVencer()`), `MovimientoInventario`. Se agregó también `movimientosInventario()` (hasMany) a `Paciente` y `Cita`, siguiendo el mismo patrón de relaciones inversas que ya tenían con `Factura`.
- **3 Resources completos de Filament** (mismo patrón de carpetas que los 7 existentes — `XResource.php`, `Schemas/XForm.php`, `Tables/XTable.php`, `Pages/{List,Create,Edit}X.php`): `ItemsInventario`, `LotesInventario`, `MovimientosInventario`.
  - Tabla de ítems: columna "Stock actual" calculada en vivo (no de BD), en rojo si cae bajo el `stock_minimo`.
  - Tabla de lotes: columna "Vence" con color (rojo si ya venció, ámbar si vence en ≤90 días), y stock del lote también calculado en vivo.
  - Formulario de movimientos: campos "Área de origen"/"Área de destino" que solo aparecen según el `tipo_movimiento` elegido (ej. "origen" no tiene sentido en una entrada). El campo `usuario` no se pide en el formulario — se asigna solo con el usuario logueado al crear (`CreateMovimientoInventario::mutateFormDataBeforeCreate`), mismo criterio que ya se usaba para no poder registrar algo a nombre de otra persona por error.
  - Protección contra borrado (mismo patrón que Área/Médico/Paciente/Cita): un ítem no se puede borrar si tiene lotes, un lote no se puede borrar si tiene movimientos. Un movimiento sí se puede borrar libremente (nada depende de él), igual que Factura/HistoriaClinica.
- **Permisos**: no existe un rol "farmacia" separado (el sistema solo tiene admin/recepcion/medico) — se asumió que el catálogo (ítems, lotes) sigue el criterio de "configuración" (admin todo, recepción solo ver, médico sin acceso, igual que Áreas/Médicos) y que los movimientos siguen el criterio "operativo" (admin y recepción todo, médico sin acceso, igual que Facturas). **Queda abierto** si la clínica confirma que necesita un rol propio de farmacia.
- **Supuestos documentados en esta entrada, en vez de bloquear el desarrollo** (ver detalle ampliado en la sección 6.3 actualizada de `MEMORIA.md`):
  1. **Stock en tiempo real**: sí, se implementó — el stock de cada lote/ítem se calcula al vuelo sumando sus movimientos (nunca una columna editable a mano), así que siempre está actualizado.
  2. **¿Farmacia es un área física propia?**: no, todavía no tiene su propio Resource — se modeló como un valor de texto (lista fija: farmacia/quirófano/admisión/facturación/bodega) dentro de cada movimiento, no como una entidad con responsable/horario propio.
  3. **¿Todo insumo se cobra al paciente?**: no se construyó todavía la conexión con `Factura` (la `factura_items` que proponía la 6.3) — el `paciente_id`/`cita_id` en el movimiento solo deja registrado *qué se usó* en la atención de alguien, no genera un cobro automático. Facturar ese consumo, si aplica, se sigue haciendo aparte por ahora.
  4. **Proveedores/compras externas**: no se modeló ninguna tabla de proveedores — un "entrada" simplemente registra que llegó stock, sin detalle de quién lo vendió. Se puede agregar después si hace falta.
  5. **¿Depende de la infraestructura de la 6.2 (quirófanos/cirugías)?**: no — se construyó de forma independiente. El movimiento solo referencia `cita_id` (ya existe), no `cirugia_id` (esa tabla no existe todavía). Se puede agregar esa columna sin romper nada cuando se construya la 6.2.
- **Qué NO se hizo todavía**: no se cargó ningún ítem/catálogo real (la clínica no ha dado esa lista), no hay seeder de datos de prueba, y no se conectó con `Factura`. El módulo está construido pero vacío, listo para que la clínica empiece a cargar su catálogo real.
- Entregado como patch (`git am`) — incluye 3 migraciones, 3 modelos (+2 modelos existentes con una relación agregada cada uno), 18 archivos de Filament, y este changelog junto con `MEMORIA.md` actualizado.

## [2026-08-24] Documentación: validación externa de 6.2/6.3 + marco legal ecuatoriano (sección 6.4 nueva)

- El usuario pidió investigar en internet si las propuestas de planificación de las secciones 6.2 (infraestructura: camas/UCI, quirófanos, procedimientos, emergencias, ambulancia) y 6.3 (medicamentos e insumos) están bien encaminadas, comparándolas con cómo lo resuelven otras clínicas/hospitales y estándares del sector, para dejar el sistema cubriendo esas buenas prácticas de antemano.
- Investigación realizada sobre: sistemas ADT (Admission-Discharge-Transfer) y el estándar HL7 FHIR (recursos `Location`/`Encounter` para camas/ubicaciones), programación de quirófanos con equipo multidisciplinario, gestión de inventario farmacéutico hospitalario (lotes, vencimiento, FEFO), el estándar `MedicationRequest` de FHIR para prescripciones, sistemas hospitalarios open source comparables en tamaño (OpenMRS, OpenHospital, Bahmni), sistemas de triage de emergencias (ESI — Emergency Severity Index), y normativa ecuatoriana (Ley Orgánica de Protección de Datos Personales 2021/2023 y normativa del MSP sobre historia clínica electrónica con estándar HL7).
- **Conclusión general**: ambas propuestas (6.2 y 6.3) están bien fundamentadas y coinciden con el patrón estándar de la industria — no se rediseñaron. Se les agregaron 3 ajustes concretos y una sección nueva de contexto legal:
  - **6.2 — quirófanos**: el estado `libre/ocupado` se amplía a algo más granular (ej. preparación → en cirugía → limpieza → libre), porque el tiempo de limpieza entre cirugías es un dato real que afecta disponibilidad.
  - **6.2 — emergencias**: un simple campo `origen` (programada/emergencia) se queda corto; se agrega la posibilidad de un campo de **prioridad/triage** (patrón ESI, 5 niveles), y se refina la pregunta pendiente para la clínica: si ya aplican algún criterio de prioridad hoy, aunque sea informal.
  - **6.3 — inventario**: la trazabilidad por **lote y fecha de vencimiento** deja de ser "pregunta abierta" y pasa a tratarse como prácticamente obligatoria (patrón FEFO — First-Expired-First-Out), con una posible tabla de lotes separada del catálogo. Además, se documenta que el estándar del sector separa el ciclo de un medicamento en 3 eventos distintos (prescrito / dispensado / administrado), relevante para cuando se conecte con el futuro módulo de prescripciones de 2027.
  - **Sección 6.4 (nueva)**: marco legal ecuatoriano — Ley Orgánica de Protección de Datos Personales (datos de salud como "dato sensible") y normativa del MSP que exige el estándar HL7 para historia clínica electrónica en instituciones públicas y privadas. No implica cambios de código, es contexto para futuras decisiones de diseño y para la eventual entrevista formal con el dueño de la clínica.
- **No se tocó ningún modelo, migración, Resource ni seeder** — es una actualización puramente de documentación en `MEMORIA.md` (refinamiento de 6.2/6.3 + sección 6.4 nueva), igual que las dos entradas anteriores de este changelog. El sistema actual no se ve afectado.
- Entregado como patch (`git am`).

## [2026-08-24] Documentación: propuesta de planificación para medicamentos e insumos

- El usuario pidió una propuesta de modelado (solo documentación, sin código) para el módulo de **medicamentos e insumos** que el contacto interno mencionó en la sección 6.1 de `MEMORIA.md` ("debe vivir en farmacia, quirófano, admisión y facturación").
- Se agregó la sección **6.3** en `MEMORIA.md`, con el mismo criterio que la 6.2 (propuesta de arquitectura para discutir, no diseño final). A diferencia de la infraestructura física de la 6.2, este módulo parte de cero — no hay ninguna tabla, campo ni seeder relacionado hoy en el sistema.
- Propuesta: separar **catálogo** (`items_inventario`: medicamentos e insumos en una sola tabla, con `tipo` para distinguirlos, y stock) de **movimientos** (`movimientos_inventario`: entradas/salidas/traslados/ajustes, con área origen/destino y referencia opcional a paciente/cita si es consumo clínico real) — el stock se derivaría de los movimientos en vez de editarse a mano, mismo criterio que se propuso para las camas en la sección 6.2.
- Se propuso una posible tabla intermedia `factura_items` para conectar el consumo de insumos con `Factura`, sin mezclar "qué se usó clínicamente" con "qué se cobró".
- Se listaron las **decisiones pendientes de confirmar con la clínica**: si "farmacia" es un área física propia o solo un paso lógico, si el stock necesita ser en tiempo real, si todo insumo se cobra al paciente o hay costos que la clínica absorbe, si hace falta registrar proveedores/compras externas, si se necesita trazabilidad por lote/vencimiento (relevante por norma sanitaria en medicamentos), y si este módulo depende de que exista primero `quirofanos`/`cirugias` (sección 6.2) o puede avanzar independiente.
- **No se tocó ningún modelo, migración, Resource ni seeder** — documentación de planificación, igual que la sección 6.2. El sistema actual no se ve afectado.
- Entregado como patch (`git am`).

## [2026-08-24] Documentación: propuesta de planificación para la infraestructura (UCI, quirófanos, laboratorio, etc.)

- El usuario pidió continuar con la infraestructura del PDF (`Servicios_CB_2026.pdf`, ver entrada anterior en este changelog y sección 6.1 de `MEMORIA.md`), aclarando explícitamente que es para **planificar el módulo futuro (fase 2/3), no para construir código todavía**.
- Se agregó la sección **6.2** en `MEMORIA.md`: una propuesta de cómo se podrían modelar los 15 ítems de infraestructura del PDF, agrupados en 5 conceptos posibles — (1) camas/internamiento (Hospitalización, UCI, UCIN), (2) quirófanos/cirugías (Central de Quirófanos), (3) procedimientos/estudios (Laboratorio, Rayos X, Ecografía, Centro de Imagen, Endoscopía, Centro de Gastroenterología, Procedimientos Ambulatorios), (4) emergencias (posiblemente solo un campo `origen`, no una tabla nueva), y (5) ambulancia (menor prioridad). Se aclaró que Consulta Externa ya está cubierta por `Cita` (no es un hueco) y que Cafetería se descarta por no ser infraestructura clínica.
- Para cada grupo se detalla una posible estructura de tablas/columnas (ej. `camas` + `internamientos`, `quirofanos` + `cirugias` con posible tabla intermedia `cirugia_medico` porque una cirugía puede tener más de un médico a diferencia de `Cita`, `ordenes_estudio` unificando los distintos tipos de estudio en vez de una tabla por tipo).
- Se listaron explícitamente las **decisiones pendientes de confirmar con la clínica** antes de poder pasar de propuesta a diseño final: si se necesita disponibilidad en tiempo real de camas/quirófanos o alcanza con histórico, si una cirugía siempre nace de una `Cita` previa, si los resultados de estudios necesitan archivos adjuntos desde el inicio, si "emergencias" implica triage/prioridad, y si habrá más de una sede.
- **No se tocó ningún modelo, migración, Resource ni seeder** — es documentación de planificación, igual que la sección 6.1. El sistema actual (Áreas/Médicos/Pacientes/Citas/HistoriaClinicas/Facturas) no se ve afectado.
- Entregado como patch (`git am`).

## [2026-08-24] Seeder con las 27 especialidades reales de la clínica

- Siguiente paso mecánico tras la respuesta del contacto interno sobre áreas/especialidades (ver entrada anterior en este changelog y sección 6.1 de `MEMORIA.md`).
- Archivo nuevo `database/seeders/AreaSeeder.php`: crea las 27 especialidades reales (Auditoría Médica, Anestesiología y Terapia del Dolor, Cardiología, ... hasta Urología — lista completa en el archivo y en `MEMORIA.md` sección 6.1) usando `Area::firstOrCreate(['nombre' => ...])` por cada una, para no duplicar filas si el seeder se corre más de una vez (ej. si ya había áreas de prueba cargadas con el mismo nombre).
- `database/seeders/DatabaseSeeder.php`: se agregó `$this->call(AreaSeeder::class);` al final de `run()`, así que `./vendor/bin/sail artisan db:seed` (o `migrate:fresh --seed`) carga las áreas reales automáticamente junto con el usuario de prueba existente. También se puede correr solo, sin tocar el resto: `./vendor/bin/sail artisan db:seed --class=AreaSeeder`.
- No se tocó ningún modelo, migración, ni Resource de Filament — el modelo de datos actual (`areas` como tabla con solo `nombre`) ya soportaba esto sin cambios de código (confirmado en la sesión anterior, sección 6.1 de `MEMORIA.md`).
- Nota: no se cargaron los "servicios/infraestructura adicional" del PDF (Hospitalización, UCI, Central de Quirófanos, etc.) como filas de `areas` — esos no son especialidades médicas en el sentido del modelo actual (un área con médicos asignados), son infraestructura de la clínica; se dejaron documentados en `MEMORIA.md` como contexto, no como datos a cargar.
- Sintaxis no se pudo validar con `php -l` en este entorno (sin PHP instalado); se revisó manualmente el archivo.
- Entregado como patch (`git am`). **Confirmado funcionando por el usuario en el entorno real.**

## [2026-08-24] Documentación: respuesta del contacto interno sobre áreas/especialidades y alcance por fases

- El usuario le preguntó al contacto interno (amigo que trabaja en la clínica) cuántas áreas/especialidades tiene la clínica — pregunta pendiente desde el inicio del proyecto (sección 6 de `MEMORIA.md`).
- Respuesta recibida junto con material de marketing real de la clínica (`Servicios_CB_2026.pdf`): **27 especialidades** (Auditoría Médica, Anestesiología, Cardiología, Cirugía General y Digestiva, Cirugía Plástica, Neurología, Traumatología, Urología, etc. — lista completa en `MEMORIA.md` sección 6.1) y una lista de servicios/infraestructura (UCI, Central de Quirófanos, Hospitalización, Ambulancia, Laboratorio, Rayos X, Ecografía, UCIN, Emergencias, entre otros) que confirma que la clínica opera con lógica de hospital, no solo consultorios.
- El contacto también aclaró el alcance real del sistema por fases, en texto: (1) el registro de pacientes que ya existe en el sistema corresponde solo a **admisión**; (2) un futuro módulo de **medicamentos e insumos** debe vivir en farmacia, quirófano, admisión y facturación — no construido, dominio nuevo; (3) para 2027 planean innovar consultorios y agregar un sistema de **registro de prescripciones** (recetas médicas) — tampoco existe hoy; (4) el propósito general es digitalizar la mayor parte del historial clínico del paciente.
- **No se tocó código en este cambio** — es puramente actualización de contexto/documentación en `MEMORIA.md` (nueva sección 6.1) y este changelog, para que quede registrado de cara a planificar los próximos módulos.
- Pendiente como siguiente paso mecánico (no hecho todavía): cargar las 27 especialidades reales en la tabla `areas` (vía seeder o a mano desde `/admin/areas`) — el modelo de datos actual ya las soporta sin cambios de código.
- Entregado como patch (`git am`), junto con el PDF de referencia (no versionado en el repo, solo usado como fuente de la información).

## [2026-08-24] Botón "Cancelar" → "Atrás" en las pantallas de Editar

- Pedido original: en la pantalla de Editar (probado en Pacientes), el botón "Cancelar" junto a "Guardar cambios" resultaba redundante — el registro ya está guardado, no hay nada que "cancelar" ahí, solo tiene sentido volver al listado. Se aclaró explícitamente que el "Cancelar" de las pantallas de **Crear** sí es útil (descarta un formulario sin guardar) y no debía tocarse.
- Se confirmó con el usuario aplicar el cambio a **todas** las pantallas de Editar del panel (no solo Pacientes), y que el botón navegue al listado del recurso (ej. `/admin/pacientes`), no a la página anterior del historial del navegador.
- Se creó `app/Filament/Concerns/HasBackFormAction.php` — un trait que sobreescribe `getFormActions()` (el método de Filament que arma los botones del formulario de Editar) para devolver `[Guardar, Atrás]` en vez de `[Guardar, Cancelar]`. El botón "Atrás" usa `Heroicon::OutlinedArrowLeft`, color gris (igual que tenía "Cancelar"), y navega con `$this->getResourceUrl()` sin argumentos, que en Filament resuelve siempre al listado (`index`) del Resource actual.
- Se aplicó `use HasBackFormAction;` en las 7 páginas de Editar existentes: `EditArea`, `EditMedico`, `EditPaciente`, `EditCita`, `EditHistoriaClinica`, `EditFactura`, `EditUser`. Ninguna tenía ya un `getFormActions()` propio.
- No se tocó ninguna página de Crear — conservan `Crear` + `Cancelar` por defecto de Filament.
- Sintaxis no se pudo validar con `php -l` en este entorno (sin PHP instalado); se revisó manualmente cada archivo tocado. Cambio de configuración de botones vía un trait compartido, sin lógica de negocio nueva.
- Entregado como patch (`git am`). **Confirmado funcionando por el usuario en el entorno real.**

## [2026-08-24] Filtros de Citas: de "arriba de la tabla colapsable" a dropdown junto al buscador

- Al confirmar en el entorno real el cambio anterior (título+buscador en una sola fila en todas las tablas), la tabla `/admin/citas` quedó viéndose distinta a las demás: un hueco vacío grande entre el buscador y el ícono de filtro.
- Causa: Citas es la única tabla que usaba `layout: FiltersLayout::AboveContentCollapsible` para sus 3 filtros rápidos ("Hoy"/"Pendientes"/"Confirmadas") — por defecto aparecen colapsados (solo el ícono de embudo), y al expandirse abren un bloque grande debajo con los 3 switches + "Aplicar filtros" + "Resetear los filtros". Nada que ver con el CSS agregado en el cambio anterior.
- Se cambió `CitasTable.php` para quitar el `layout: FiltersLayout::AboveContentCollapsible` explícito (queda el default de Filament, `Dropdown`) — mismo patrón que ya se había adoptado en `UsersTable` en una sesión anterior. El botón de filtro ahora aparece pegado al buscador, y el panel con los 3 switches se abre flotando al hacer clic, sin ocupar espacio fijo ni dejar huecos.
- Se quitó el `use Filament\Tables\Enums\FiltersLayout;` de `CitasTable.php`, sin uso tras el cambio.
- Los 3 filtros siguen funcionando exactamente igual (mismos toggles combinables) — solo cambió cómo se muestran, no la lógica de filtrado.
- Sintaxis no se pudo validar con `php -l` en este entorno (sin PHP instalado); cambio de configuración de un solo Resource, sin lógica nueva.
- Entregado como patch (`git am`). **Confirmado funcionando por el usuario en el entorno real.**

## [2026-08-24] Título y buscador en la misma fila en todas las tablas del panel (primer theme propio)

- Pedido original: en el widget "Citas de hoy" del Dashboard, el título quedaba en una fila y la barra de búsqueda en la fila de abajo — se pidió juntarlos en una sola fila.
- Primer intento (moviendo el `heading` de nivel widget a `->heading()` en `table()`) no cambió nada visualmente — se confirmó leyendo el código fuente de `filament/widgets` que ambos caminos son equivalentes internamente. La causa real es que **todas** las tablas de Filament separan el título (`.fi-ta-header`) y la barra de búsqueda/filtros (`.fi-ta-header-toolbar`) en dos bloques apilados, por diseño de fábrica — no es un bug de este widget.
- Se evaluaron 3 opciones con el usuario: dejarlo como está, forkear la plantilla Blade completa de la tabla (2604 líneas del paquete `filament/tables`, descartado por el riesgo de congelar esa copia y perder actualizaciones/parches de seguridad futuros de Filament en todas las tablas), o lograr el mismo resultado con CSS scoped sin tocar archivos de Filament. Se eligió la tercera.
- El proyecto no tenía un theme propio de Filament configurado — se creó por primera vez: `resources/css/filament/admin/theme.css` (importa el theme base de Filament + el ajuste de CSS que junta ambos bloques en una fila, dentro de un `@media (min-width: 640px)` para no apretar en mobile), registrado con `->viteTheme(...)` en `AdminPanelProvider.php` y agregado al `input` de `vite.config.js`.
- Efecto: aplica a **todas** las tablas del panel (Áreas, Citas, Facturas, Historia Clínicas, Médicos, Pacientes, Usuarios, y el widget "Citas de hoy"), consistente en todo el sistema, no un parche aislado del widget.
- Sintaxis no se pudo validar con `npm`/`php` en este entorno (sin Node/PHP instalados); cambio de CSS + configuración de Vite/Filament, sin lógica de negocio.
- Entregado como patch (`git am`). **Confirmado funcionando por el usuario en el entorno real, en todas las tablas del panel** (no solo el widget). Nota operativa: hubo que correr `npm install` antes del `npm run build`, porque el proyecto nunca había instalado el frontend dentro del contenedor de Sail hasta este cambio.

## [2026-08-23] Nuevo logo vectorial (inspirado en el cartel real) + color primario Teal

- El usuario generó con IA (Gemini) una imagen aproximada del cartel físico de la clínica (emblema triangular, hoja/llama bicolor azul-turquesa, texto curvo "CLÍNICA BENITES") y pidió usarla como referencia para el logo del panel.
- Esa imagen es una foto/render (fondo gris de mármol, reflejos 3D), no un vector — se le presentó al usuario el trade-off (se vería con recuadro gris en el header, y perdería detalle a tamaño de favicon) y se le preguntó cómo prefería usarla. Eligió: rehacer una versión vectorial propia inspirada en la imagen, con fondo transparente.
- Se reemplazaron `public/images/icon.svg` (triángulo con contorno navy `#12395c` y hoja partida en dos mitades, azul `#1f6fa8` / turquesa `#1fae8e`) y `public/images/logo.svg` (mismo ícono + "CLÍNICA BENITES" en dos líneas), sustituyendo el monograma "CB" provisional anterior.
- `public/favicon.ico` regenerado desde el `icon.svg` nuevo usando `cairosvg` (`pip install cairosvg --break-system-packages`, no estaba instalado en este entorno) — más preciso que el enfoque anterior con Pillow dibujando a mano; ahora el favicon se puede regenerar fielmente desde el SVG cada vez que cambie. Verificado visualmente con capturas a 32px y 200px antes de dar por bueno el diseño.
- Color primario del panel cambiado de vuelta (había quedado en el ámbar por defecto tras la reversión anterior) a `Color::Teal`, porque los colores del logo nuevo calzan casi exacto con ese Tailwind color predefinido. A diferencia del intento anterior con Cyan, **no se agregó CSS a medida** — solo la línea `->colors(['primary' => Color::Teal])`, sin tocar sidebar/topbar/tablas, para no repetir el problema de clases internas de Filament que no calzaron la vez pasada.
- Sintaxis PHP no se pudo validar con `php -l` en este entorno (sin PHP instalado); cambio simple de assets + una línea de configuración, sin lógica nueva.
- Entregado como patch (`git am`). **Pendiente confirmar en el entorno real**: cómo se ve el logo en el header/login, y si el Teal contrasta bien con el resto de la interfaz.

## [2026-08-23] Revertir colores del panel a los defaults de Filament

- El usuario probó en el entorno real el branding con color turquesa/sidebar celeste (compartió captura de `/admin/citas` con datos reales cargados) y decidió que el color no convence.
- En `AdminPanelProvider.php` se quitó por completo el bloque `->colors(['primary' => Color::Cyan, 'gray' => Color::Slate])` y todo el `renderHook` de CSS a medida (tinte celeste del sidebar, línea de acento en topbar y encabezado de tabla, color de los botones de acción) — ese CSS dependía de las variables `--primary-*`/`--gray-*`, así que no tenía sentido dejarlo apuntando a un color que ya no se usa.
- El panel queda con los colores **por defecto de Filament** (ámbar primario, gris neutro estándar de Filament), sin ningún CSS de acento agregado.
- **No se tocó** `brandName('Clínica Benites')`, `brandLogo()` ni `favicon()` — el pedido fue solo sobre los colores, el logo/nombre se mantienen.
- Se limpiaron los `use` de `Filament\Support\Colors\Color` y `Filament\View\PanelsRenderHook` en `AdminPanelProvider.php`, ya sin uso tras quitar ese bloque.
- Sintaxis PHP no se pudo validar con `php -l` en este entorno (sin PHP instalado); cambio simple de eliminar bloques de configuración, sin lógica nueva.
- Entregado como patch (`git am`). **Pendiente confirmar en el entorno real** cómo se ve el ámbar por defecto contra el logo/nombre actuales.

## [2026-08-23] Fix: locale español y timezone Guayaquil (fechas en inglés + Dashboard sin citas de hoy)

- El usuario reportó dos problemas a la vez: las fechas en las tablas no se veían "completas" (ej. "Apr 12, 2024") y el Dashboard no mostraba las citas de hoy.
- **Causa raíz común**: `config('app.locale')` y `config('app.timezone')` nunca se habían configurado para la clínica — se quedaron en los defaults de Laravel (`en` / `UTC`) desde el inicio del proyecto.
  - El mes en inglés ("Apr") venía de que la app corre en locale inglés.
  - El bug del Dashboard es más serio: el widget `CitasDeHoyWidget` filtra con `today()`, que Laravel calcula según la zona horaria de la app. Con la app en UTC y la clínica en Guayaquil (UTC-5), a partir de las 19:00 hora local ya es el día siguiente en UTC — así que `today()` devolvía la fecha de mañana y las citas de hoy dejaban de aparecer justo en las horas de más uso del sistema (tarde/noche).
- **Fix**: en `config/app.php`, `locale`/`fallback_locale`/`faker_locale` pasan a `es`/`es`/`es_ES`, y `timezone` pasa de estar hardcodeado en `'UTC'` a `env('APP_TIMEZONE', 'America/Guayaquil')` (ahora se puede sobreescribir por `.env` si hiciera falta). `.env.example` actualizado con las mismas variables.
- **Importante**: el `.env` real de cada entorno no se actualiza solo (no está versionado) — hay que agregar/corregir `APP_LOCALE=es`, `APP_FALLBACK_LOCALE=es`, `APP_TIMEZONE=America/Guayaquil` ahí a mano, y correr `./vendor/bin/sail artisan config:clear` después (Laravel puede tener la config vieja cacheada).
- No se tocó el formato de las columnas `->date()` en sí (Citas, Facturas, Historia Clínica, Pacientes) — con el locale en español, Carbon ya traduce el mes automáticamente (ej. "abr. 12, 2024"), sin necesitar cambiar cada columna una por una.
- Sintaxis PHP no se pudo validar con `php -l` en este entorno (sin PHP instalado); son cambios simples de valores de configuración, sin lógica nueva.
- Entregado como patch (`git am`). **Pendiente confirmar en el entorno real** (recordar actualizar el `.env` real y correr `config:clear`, o el fix no se nota).

## [2026-08-23] Simplificar el sidebar: se descarta el fondo oscuro, tinte celeste claro sin forzar el texto

- El intento de sidebar oscuro (entrada de abajo) siguió sin verse bien en la prueba real incluso después del primer fix (texto invisible en el ítem activo, ver entrada de abajo): el usuario probó de nuevo en `/admin/areas` y seguía mal. Se investigó el código fuente real de Filament v5.7.6 (`item.blade.php`, descargado directo desde GitHub para la versión exacta instalada según `composer.lock`) y se confirmó que el `<li>` sí lleva las clases `fi-sidebar-item fi-active` como se esperaba — así que el CSS en teoría debía funcionar. Sin acceso a inspeccionar el DOM en vivo (herramientas de desarrollador del navegador del usuario) no se pudo determinar con certeza si el problema real era cache del navegador/Livewire (`wire:navigate` no siempre recarga el `<head>`) o algo de especificidad CSS.
- Ante la complicación, el usuario pidió simplificar: descartar el sidebar oscuro por completo y volver a un tinte celeste claro de fondo, sin forzar el color del texto en ningún estado (normal/hover/activo) — se deja el gris que Filament trae por defecto, que sobre un fondo claro ya contrasta bien.
- En `AdminPanelProvider.php`: se eliminaron todas las reglas de `.fi-sidebar-item-label`, `.fi-icon`, `.fi-sidebar-group-label`, `:hover` y `.fi-active` (siete reglas en total). Queda solo `.fi-sidebar { background-color: color-mix(in srgb, var(--primary-500) 8%, white); }` — mismo mecanismo de tinte suave que ya se usaba antes de probar el sidebar oscuro. El resto del `<style>` (línea de acento en topbar/tabla, color de botones de acción) no se tocó, esas clases (`.fi-topbar`, `.fi-ta-table`, `.fi-ta-actions`) son de nivel más alto y ya estaban confirmadas.
- Menor superficie de riesgo: de siete reglas dependientes de clases internas de Filament, queda solo una que apunta a `.fi-sidebar` (clase raíz del componente, ya confirmada desde la primera vuelta de branding).
- Sintaxis PHP no se pudo validar con `php -l` en este entorno (sin PHP instalado), pero el cambio es solo texto dentro del heredoc CSS — no se tocó estructura PHP.
- Entregado como patch (`git am`). **Pendiente confirmar en el entorno real.**

## [2026-08-23] Fix: texto invisible en el ítem activo del menú lateral

- El usuario probó el rediseño del sidebar oscuro (ver entrada de abajo) en el entorno real y mandó una captura de `/admin/facturas`: el ítem "Facturas" (activo, seleccionado) se veía con fondo blanco/claro y texto blanco encima — prácticamente ilegible.
- **Causa**: el fondo y el color de texto del ítem activo se aplicaban con selectores CSS distintos — el del fondo (`.fi-sidebar-item.fi-active .fi-sidebar-item-button`) dependía de una clase interna del botón que no calzó contra la versión real de Filament instalada; el del texto (`.fi-sidebar-item.fi-active .fi-sidebar-item-label`) sí calzó y forzó el texto a blanco. Resultado: texto blanco forzado sobre un fondo que nunca cambió de blanco.
- **Fix**: en `AdminPanelProvider.php`, el fondo y el color activo ahora se aplican juntos al `<li class="fi-sidebar-item fi-active">` (clase confirmada) y a todos sus descendientes (selector universal `*`), en vez de nombrar clases internas del botón/label que pueden variar entre versiones — así nunca vuelve a pasar que uno de los dos cambie sin el otro. Mismo criterio aplicado al estado `:hover`.
- Sintaxis PHP validada con `php -l`. Entregado como patch (`git am`). **Pendiente confirmar en el entorno real que el ítem activo ya se ve bien.**

## [2026-08-23] Rediseño del color extra con criterio (60/30/10, sidebar oscuro)

- El usuario pidió una segunda vuelta más pensada sobre el ajuste de color anterior ("¿cómo lo arreglarías vos? buscá en internet la mejor combinación"). Se investigaron guías de diseño (UAB Medicine, CMS.gov, regla 60/30/10) antes de tocar el código de nuevo.
- Se identificó un problema en la versión anterior: pintar todo el encabezado de la tabla de turquesa sólido competía visualmente con los badges de estado (`pendiente`/`confirmada`/`cancelada`), que ya usan color con significado propio (gray/info/danger). Se revirtió esa parte: el encabezado de tabla vuelve a blanco/gris neutro, con una línea de acento fina debajo.
- Se rediseñó el sidebar con un fondo turquesa oscuro sólido (`var(--primary-900)`) en vez de un tinte casi imperceptible — patrón común en paneles SaaS (Linear, Vercel). Texto/íconos del menú en turquesa clarito, blanco en hover y en el ítem activo. La cabecera con el logo se mantiene sin cambios (blanca).
- Se mantiene el resto sin cambios: línea de acento en la barra superior, botones de acción de tabla en color primario.
- **Nota de riesgo**: las clases nuevas de esta vuelta (`.fi-sidebar-item-label`, `.fi-icon`, `.fi-sidebar-group-label`) todavía no están confirmadas contra Filament v5.7 real — si el texto del menú queda invisible sobre el fondo oscuro, hay que inspeccionar el HTML y ajustar el selector.
- Sintaxis PHP validada con `php -l`. Falta probar en el entorno real.
- Entregado como patch (`git am`).

## [2026-08-23] Más color todavía: encabezados de tabla y botones de acción

- A pedido del usuario, sobre el ajuste anterior de color (sidebar/topbar/ítem activo): se sumó al mismo `<style>` en `AdminPanelProvider` un tinte turquesa en el encabezado de las tablas (`.fi-ta-header-cell`, con línea de acento debajo) y color turquesa forzado en los botones de acción de fila (ej. "Editar", vía `.fi-ta-actions`), que antes quedaban en gris y se perdían un poco.
- Mismo mecanismo que el ajuste anterior (CSS inline vía `renderHook`, sin build nuevo) y mismo riesgo advertido: clases sin confirmar contra Filament v5.7 corriendo de verdad.
- Sintaxis PHP validada con `php -l`. Falta probar en el entorno real.
- Entregado como patch (`git am`).

## [2026-08-23] Más color en el panel: sidebar, topbar e ítem de menú activo

- A pedido del usuario tras confirmar el branding base ("falta más color", ver captura de `/admin/areas`): se agregó un `<style>` inline vía `->renderHook(PanelsRenderHook::HEAD_END, ...)` en `AdminPanelProvider`. Tiñe levemente el fondo del sidebar con el color primario, agrega una línea de acento turquesa bajo la barra superior, y le da más presencia de color al ítem de menú activo (antes quedaba en gris clarito).
- Se cambió también el color neutro del panel de `Color::Gray` (default) a `Color::Slate`, con un tinte azulado que combina mejor con el turquesa primario.
- Se usó CSS inline con `->renderHook()` en vez de compilar un tema custom de Filament (que requiere `composer create filament:theme` + build con Vite/Tailwind) — mucho más liviano para un ajuste puntual de color, sin agregar un paso de build nuevo al proyecto.
- **Nota de riesgo dejada explícita en `MEMORIA.md`**: las clases CSS usadas (`.fi-topbar`, `.fi-sidebar`, `.fi-sidebar-item.fi-active .fi-sidebar-item-button`) no se pudieron confirmar contra Filament v5.7 corriendo de verdad (no hay forma de levantar el panel real desde este entorno) — si no se nota el cambio, hay que inspeccionar el HTML del panel en el navegador y ajustar los selectores.
- Sintaxis PHP validada con `php -l`. Falta probar en el entorno real.
- Entregado como patch (`git am`).

## [2026-08-23] Confirmación en entorno real: branding del panel

- Confirmado por el usuario con una captura de `/admin/areas`: el logo, el nombre "Clínica Benites" y el color primario turquesa se ven correctamente aplicados en el panel.
- Feedback del usuario: "falta más color" — el resto del panel queda muy neutro (blanco/gris) por el comportamiento por defecto de Filament, que solo usa el color primario en botones y acentos puntuales. Ver entrada siguiente para el ajuste hecho a partir de este feedback.

## [2026-08-23] Branding del panel: nombre, colores, logo y favicon

- `AdminPanelProvider`: `->brandName('Clínica Benites')` (antes mostraba "Laravel" por defecto), `->brandLogo(asset('images/logo.svg'))`, `->brandLogoHeight('2.5rem')`, `->favicon(asset('images/icon.svg'))` y color primario cambiado de `Color::Amber` (default de Filament) a `Color::Cyan` — turquesa, a pedido del usuario y acorde a los colores reales de la fachada/cartel de la clínica (fotos de Google Street View compartidas por el usuario).
- Nuevos assets en `public/images/`: `icon.svg` (monograma "CB" en cuadrado redondeado turquesa, usado como favicon) y `logo.svg` (versión horizontal ícono + "Clínica Benites", usada como logo del header del panel). Son un diseño original simple (no una reproducción exacta del cartel físico, del que no se tiene el archivo vectorial) pensado para verse bien a tamaño chico en el panel.
- `public/favicon.ico` regenerado con Pillow (antes era un archivo vacío de 0 bytes, sin favicon real) a partir del mismo diseño del monograma "CB", en los tamaños estándar 16/32/48/64px — este es el favicon que se sirve para el sitio en general (fuera del panel de Filament, que usa `icon.svg` vía `->favicon()`).
- `.env.example`: `APP_NAME=Laravel` → `APP_NAME="Clínica Benites"`. **Importante**: el `.env` real (no versionado) de cada entorno hay que actualizarlo a mano con el mismo valor — este archivo solo cambia la plantilla, no el `.env` que ya existe localmente. Afecta el título de la pestaña del navegador y el remitente de los correos (`MAIL_FROM_NAME`), entre otros usos de `config('app.name')` en Laravel.
- **No se tocó** la página pública (`resources/views/welcome.blade.php`) más allá del efecto indirecto del cambio de `APP_NAME` en el `<title>` — sigue siendo la página de bienvenida por defecto de Laravel, la construcción del sitio público sigue pendiente (ver `MEMORIA.md`, sección 8).
- Sintaxis PHP validada con `php -l`. Falta probar visualmente en el entorno real (colores, tamaño del logo en el header/login, favicon en la pestaña del navegador).
- Entregado como patch (`git am`).

## [2026-08-23] Confirmación en entorno real: filtro de rol junto a la barra de búsqueda

- Confirmado por el usuario: el botón de filtro de rol aparece correctamente junto a la barra de búsqueda en `/admin/users`.
- `MEMORIA.md` actualizado quitando la nota de "falta probar".

## [2026-08-23] Ajuste de UX: filtro de rol junto a la barra de búsqueda

- A pedido del usuario tras confirmar el filtro anterior: se quitó `layout: FiltersLayout::AboveContentCollapsible` del `SelectFilter` de `UsersTable`. Con el layout `Dropdown` por defecto de Filament, el botón de filtro ahora aparece junto a la barra de búsqueda (como en la mayoría de tablas de Filament), en vez de en su propia fila arriba de la tabla.
- Se eligió no usar `AboveContentCollapsible` aquí (a diferencia de Citas) porque con un solo filtro no se justifica el espacio extra que ocupa esa fila.
- Sintaxis validada con `php -l`.
- Entregado como patch (`git am`).

## [2026-08-23] Confirmación en entorno real: filtro rápido por rol en /admin/users

- Confirmado por el usuario: el `SelectFilter` "Rol" en `/admin/users` funciona correctamente.
- `MEMORIA.md` actualizado quitando la nota de "falta probar".

## [2026-08-23] Filtro rápido por rol en /admin/users

- `UsersTable`: nuevo `SelectFilter` "Rol" (admin/recepción/médico), colocado arriba de la tabla vía `FiltersLayout::AboveContentCollapsible` (mismo patrón visual que los filtros rápidos de Citas).
- Se usó `SelectFilter` (una sola opción a la vez) en vez de 3 `Filter->toggle()` como en Citas, porque el rol de un usuario es mutuamente excluyente — no tiene sentido combinar "admin" y "médico" a la vez, a diferencia de "Hoy"/"Pendientes" en Citas.
- Sintaxis validada con `php -l`. Falta probar en el entorno real.
- Entregado como patch (`git am`).

## [2026-08-23] Confirmación en entorno real: fix de `medico_id` + filtro "mis pacientes"

- Confirmado por el usuario: el fix de `medico_id` (se limpia correctamente al cambiar el rol de un usuario, tanto en el `Select` en vivo como al guardar) funciona como se esperaba.
- Con esto, el filtro "mis pacientes" para el rol médico (Citas, Historias Clínicas, Dashboard, autoselect al crear) queda confirmado de punta a punta en el entorno real.
- `MEMORIA.md` actualizado quitando las notas de "pendiente probar".

## [2026-08-23] Fix: `medico_id` no se limpiaba al cambiar el rol de un usuario

- **Bug encontrado por el usuario al probar el filtro "mis pacientes"**: al editar un usuario `medico` (con médico vinculado) y cambiarle el rol a `recepcion`/`admin`, el campo "Médico vinculado" se ocultaba (`->visible()`) pero su valor seguía guardándose — Filament no descarta el valor de un campo solo por ocultarlo. El usuario quedaba con `rol` correcto pero `medico_id` apuntando a un médico "fantasma".
- Fix en dos capas:
  - `UserForm`: `->afterStateUpdated()` en el `Select` de `rol`, resetea `medico_id` a `null` en el estado del formulario en cuanto el rol deja de ser `medico`.
  - `CreateUser::mutateFormDataBeforeCreate()` y `EditUser::mutateFormDataBeforeSave()`: cinturón de seguridad adicional, fuerza `medico_id = null` justo antes de guardar si el rol no es `medico`.
- Sintaxis validada con `php -l`.
- **Aclaración sobre un segundo reporte del usuario (no era un bug)**: un usuario con rol médico no veía el botón "Crear" en `/admin/citas` pero sí en `/admin/historia-clinicas`. Es la matriz de permisos ya documentada desde antes (sección 10 de `MEMORIA.md`): médico nunca tuvo permiso de crear Citas (solo ver/editar), pero sí tiene permiso completo en Historia Clínica. No se tocó código por esto.
- Entregado como patch (`git am`).

## [2026-08-23] Filtrar "mis pacientes" para el rol médico

- Nueva migración `2026_08_23_220000_add_medico_id_to_users_table.php`: agrega `medico_id` (nullable, FK a `medicos`, `nullOnDelete()`) a la tabla `users`, conectando por fin `users` con `medicos` (hasta ahora eran tablas independientes).
- `User::medico()` (relación `belongsTo`) y `medico_id` agregado al atributo `#[Fillable]` del modelo.
- `UserForm` (`/admin/users`): nuevo campo `Select` "Médico vinculado", visible solo cuando el rol seleccionado es `medico` (el `Select` de `rol` ahora usa `->live()` para poder reaccionar a ese cambio sin recargar la página).
- `UsersTable`: nueva columna "Médico vinculado" (toggleable), muestra "—" cuando no aplica.
- `CitaResource::getEloquentQuery()` y `HistoriaClinicaResource::getEloquentQuery()`: si el usuario logueado tiene rol `medico` y `medico_id` asignado, la consulta base se filtra por `medico_id` — afecta tabla, edición y búsqueda global (que reutiliza `getEloquentQuery()` en Filament).
- `CitasDeHoyWidget`: mismo filtro aplicado a la query del widget de dashboard.
- `CitaForm` y `HistoriaClinicaForm`: el campo `medico_id` trae `->default()` que preselecciona al médico logueado si está vinculado (sigue siendo editable).
- Diseño defensivo: un usuario con rol `medico` sin `medico_id` asignado sigue viendo todo (sin filtro), igual que el comportamiento anterior — evita bloquear a alguien por un dato sin migrar.
- **Pendiente**: correr la migración en el entorno real (`sail artisan migrate`) y asignar `medico_id` a los usuarios médico existentes desde `/admin/users` para que el filtro empiece a aplicar. Falta probar de punta a punta en el entorno real.
- Sintaxis validada con `php -l` (PHP 8.3 CLI en un entorno aislado) en los 9 archivos nuevos/modificados.
- Entregado como patch (`git am`).

## [2026-08-23] Confirmación en entorno real: gestión de usuarios + exportar Facturas a PDF

- Se corrió `./vendor/bin/sail composer require barryvdh/laravel-dompdf` (usando el composer de Sail, no el composer nativo de Windows/WSL, que no tiene PHP en el PATH y fallaba con `php: not found`).
- Probado en `/admin/users`: listar, crear y editar usuarios con los 3 roles, contraseña opcional al editar, y bloqueo confirmado al intentar que un admin se elimine a sí mismo.
- Probado el botón "Exportar PDF" en la tabla de Facturas y en `EditFactura`: descarga correcta del comprobante con los datos de paciente/cita/médico/área.
- Probado con un rol sin permiso (médico): la ruta `/facturas/{id}/pdf` responde 403, igual que el resto del Resource de Facturas.
- Todo confirmado funcionando sin ajustes adicionales. `MEMORIA.md` actualizado (secciones 7, 9 y 10) quitando las notas de "falta probar".

## [2026-08-23] Gestión de usuarios (Resource) + Exportar Facturas a PDF

- **Gestión de usuarios**: nuevo `app/Filament/Resources/Users/` (`UserResource.php`, `Schemas/UserForm.php`, `Tables/UsersTable.php`, `Pages/{ListUsers,CreateUser,EditUser}.php`), mismo patrón de carpetas que los otros 6 Resources. Solo `admin` puede ver/crear/editar/eliminar (`canViewAny()` bloquea el acceso completo para recepción/médico, incluyendo la entrada en el menú). Formulario con `name`, `email` (único, `ignoreRecord: true`), `Select` de `rol` (admin/recepcion/medico) y `password` (obligatorio solo al crear, opcional al editar — dejarlo en blanco no cambia la contraseña actual, usando el patrón estándar de Filament `dehydrateStateUsing`/`dehydrated` + `Hash::make`). Protección agregada: `canDelete()` excluye la propia cuenta del usuario logueado (no puede eliminarse a sí mismo), y el `DeleteBulkAction` de la tabla repite esa misma validación con un `->before()` porque un borrado masivo no pasa por `canDelete()` registro por registro. No se usó ningún paquete de permisos granulares (`spatie/laravel-permission`, Filament Shield) — no hace falta con solo 3 roles fijos.
- **Exportar Facturas a PDF**: nueva dependencia `barryvdh/laravel-dompdf` (⚠️ correr `composer require barryvdh/laravel-dompdf` — no se editó `composer.json`/`composer.lock` a mano para no dejarlos desincronizados entre sí). Se agregó `resources/views/pdf/factura.blade.php` (plantilla con CSS simple e inline; dompdf solo soporta un subconjunto de CSS, por eso no se usó ningún framework de estilos), `app/Http/Controllers/FacturaPdfController.php` (reutiliza `FacturaResource::canViewAny()` en vez de duplicar la regla de permisos) y la ruta `GET /facturas/{factura}/pdf` en `routes/web.php`, fuera de `/admin` porque descarga un archivo binario en vez de mostrar una pantalla de Filament, protegida con el middleware `auth` (mismo guard de sesión que usa Filament). Botón "Exportar PDF" agregado en la tabla de Facturas (acción por fila) y en la cabecera de `EditFactura`. Se dejó pendiente para una próxima pasada, si se pide: el mismo patrón aplicado a Historia Clínica, y la exportación nativa de tabla completa a Excel/CSV (`ExportAction` de Filament, no requiere código nuevo).
- **Nota de entorno**: igual que el resto del código entregado en este proyecto, se escribió sin acceso a Sail/MySQL del entorno real. Se instaló PHP 8.3 CLI en un entorno aislado para validar sintaxis (`php -l`) de los 11 archivos nuevos/modificados, y se verificaron contra la documentación oficial de Filament los nombres exactos de los iconos usados (`Heroicon::OutlinedUsers`, `Heroicon::OutlinedDocumentArrowDown`) y la firma de la Facade de `barryvdh/laravel-dompdf` (`Barryvdh\DomPDF\Facade\Pdf`). **Falta probar en el entorno real**, y falta correr `composer require barryvdh/laravel-dompdf` antes de que el botón de exportar funcione.
- Entregado como patch (`git am`).

## [2026-08-23] Investigación de gestión de usuarios y exportación + pregunta pendiente sobre "cuantificos"

- Se investigó gestión de usuarios desde el panel (hoy solo se puede por consola, ver sección 10) y exportación de registros (ej. Facturas). Conclusiones agregadas a la sección 9 de `MEMORIA.md`:
  - Gestión de usuarios: no hace falta un paquete de permisos granulares (`spatie/laravel-permission`, Filament Shield) dados los 3 roles fijos actuales — alcanza con un `UserResource` normal, solo visible para `admin`.
  - Exportación: Filament ya trae exportación nativa de tablas a Excel/CSV (`ExportAction`); para un comprobante individual con formato (ej. una factura) el patrón de la comunidad es `barryvdh/laravel-dompdf` con una plantilla Blade propia.
- Se agregó una pregunta pendiente en la sección 6: el contacto interno mencionó que la administración se maneja mediante algo que llamó "cuantificos" — término sin aclarar, queda pendiente de la entrevista formal.
- No hay cambios de código en esta entrada, solo documentación e investigación. Entregado como patch (`git am`).

## [2026-08-23] Investigación de funciones futuras — propuesta documentada, sin priorizar

- Sesión de investigación (buenas prácticas de software de gestión clínica, requisitos de la LOPDP de Ecuador para datos de salud, e ideas de otras industrias) para tener un banco de ideas listo cuando se quiera ampliar el sistema.
- Se agregó la sección 9 en `MEMORIA.md` ("Propuesta de funciones futuras") con: requisitos de cumplimiento pendientes (consentimiento del paciente, registro de auditoría vía `spatie/laravel-activitylog`), funciones típicas de EHR/practice management que aún faltan (exportar a PDF, reportes/KPIs, filtro "mis pacientes" por médico), y funciones "cruzadas" de otras industrias: lista de espera automática para cancelaciones (patrón de restaurantes/hoteles), recall/control preventivo (patrón de CRM de retail y clínicas dentales), encuesta de satisfacción post-visita, marcado de paciente frecuente, turno virtual para walk-ins, y panel ejecutivo de KPIs del negocio.
- **Decisión del usuario**: por ahora no se prioriza ni se construye nada de esto — queda solo como propuesta documentada para una futura sesión.
- No hay cambios de código en esta entrada, solo documentación. Entregado como patch (`git am`), igual que el resto de cambios a `MEMORIA.md`/`CHANGELOG.md`.

## [2026-08-23] Validación de cédula única — confirmado funcionando en el entorno real

- Se probó en `/admin/pacientes`: crear un paciente con una cédula repetida muestra el mensaje de validación en vez del error crudo de MySQL; editar un paciente sin cambiar su propia cédula guarda sin problema (confirma que `ignoreRecord: true` funciona); editar un paciente cambiando su cédula por la de otro paciente existente también dispara la validación correctamente.
- No hay cambios de código en esta entrada, solo la confirmación.

## [2026-08-23] Deuda técnica: validación de cédula única en el formulario de Pacientes

- `app/Filament/Resources/Pacientes/Schemas/PacienteForm.php`: se agregó `->unique(table: 'pacientes', column: 'cedula', ignoreRecord: true)` al campo `cedula`. Antes solo existía la restricción `unique` a nivel de base de datos (definida en la migración), así que crear o editar un paciente con una cédula repetida desde `/admin/pacientes` mostraba el error crudo de MySQL (`Integrity constraint violation`) en vez de un mensaje de validación claro.
- Se usó `ignoreRecord: true` porque este formulario se comparte entre la página de Crear y la de Editar — sin esa opción, guardar un paciente existente sin tocar su cédula fallaría la validación al "encontrarse a sí mismo" como duplicado.
- Este mismo problema ya se había corregido puntualmente en el modal de creación rápida de paciente dentro de `CitaForm.php` (ver entrada del punto 3 del plan de UX más abajo), pero no en el formulario original — quedaba documentado como deuda técnica en `MEMORIA.md` sección 7 y ahora se cierra.
- **Nota de entorno**: se validó sintaxis con `php -l` y la firma de `->unique(ignoreRecord: true)` contra la documentación oficial de Filament. Falta probarlo en el entorno real.
- Entregado como patch (`git am`).

## [2026-08-23] Buscador global — confirmado funcionando en el entorno real

- Se probó en `/admin` del entorno real: buscar "ju" en el buscador global devolvió correctamente las categorías "Citas" y "Pacientes", ambas mostrando a Julio Jaramillo con el título compuesto ("Cita — Julio Jaramillo Montenegro Aguirre") y los detalles de contexto (Médico, Área, Fecha, Estado / Cédula, Teléfono) tal como se diseñó en el punto 5 del plan de UX.
- Se detectó de paso un dato de prueba mal cargado (no un bug del código): un médico tiene el nombre completo duplicado entre los campos `nombres` y `apellidos`, lo que hace que se vea repetido en cualquier pantalla que muestre "nombres + apellidos" (incluyendo el detalle del buscador). Se corrige editando ese registro en `/admin/medicos`, no requiere cambio de código.
- Con esto quedan confirmados en el entorno real 4 de los 5 puntos del plan de UX (1, 2, 3 y 5); el punto 4 (filtros rápidos) sigue resuelto en código pero pendiente de esa confirmación.

## [2026-08-23] Buscador global (punto 5 del plan de UX)

- **Áreas** y **Médicos/Pacientes** ya tenían el buscador global habilitado por defecto (vía `$recordTitleAttribute`), pero solo buscaban por un único campo (`nombre`/`nombres`). Se amplió en `PacienteResource.php` y `MedicoResource.php` con `getGloballySearchableAttributes()` para buscar también por apellido, cédula, email y teléfono, y `getGlobalSearchResultTitle()` para mostrar "Nombres Apellidos" en vez de solo el nombre de pila.
- **Citas**, **Historias Clínicas** y **Facturas** no tenían buscador global activo (no tienen un único campo de texto representativo, así que nunca se les puso `$recordTitleAttribute`). Se agregó a los tres:
  - `$recordTitleAttribute` (un campo real cualquiera, solo para cumplir el requisito de habilitación — el título mostrado se sobreescribe).
  - `getGloballySearchableAttributes()` con "dot notation" para buscar dentro de las relaciones (ej. `paciente.nombres`, `medico.apellidos`) además de campos propios (`notas`, `motivo_consulta`, `diagnostico`, `estado_pago`, `metodo_pago`).
  - `getGlobalSearchResultTitle()` con un título compuesto ("Cita — Juan Pérez", "Historia clínica — Juan Pérez", "Factura — Juan Pérez").
  - `getGlobalSearchResultDetails()` con datos de contexto bajo el título (médico, área, fecha/hora y estado para Citas; médico, motivo y diagnóstico para Historias; monto, estado de pago y fecha para Facturas).
  - `getGlobalSearchEloquentQuery()` con `->with([...])` para precargar las relaciones usadas en título/detalles y evitar N+1 en cada resultado de búsqueda.
- Los permisos existentes se respetan sin cambios adicionales: Filament excluye automáticamente de la búsqueda global cualquier recurso cuyo `canViewAny()` devuelva `false` para el usuario actual (ej. recepción no ve Historias Clínicas ni en el menú ni en el buscador; médico no ve Facturas).
- **Nota de entorno**: se escribió sin acceso a PHP/Composer/Sail del proyecto real, pero se validó la sintaxis de los 5 archivos modificados instalando PHP CLI en un entorno aislado (`php -l`), y las firmas de los métodos se verificaron contra la documentación oficial de Filament 5.x (`getGloballySearchableAttributes()`, `getGlobalSearchResultTitle()`, `getGlobalSearchResultDetails()`, `getGlobalSearchEloquentQuery()`). Falta probarlo corriendo el proyecto real (igual que los puntos 2, 3 y 4 del plan de UX en su momento).
- Entregado como patch (`git am`).

## [2026-08-23] Filtros rápidos en la tabla de Citas (punto 4 del plan de UX)

- `app/Filament/Resources/Citas/Tables/CitasTable.php`: se agregaron 3 filtros tipo toggle (switch) en `->filters()`: "Hoy" (`whereDate('fecha', today())`), "Pendientes" y "Confirmadas" (`where('estado', ...)`).
- Se usó `layout: FiltersLayout::AboveContentCollapsible` para que los filtros aparezcan como controles visibles arriba de la tabla (colapsables) en vez de escondidos detrás del ícono de filtro por defecto — así son más rápidos de encontrar y usar en el día a día.
- No se agregaron filtros para "Atendida"/"Cancelada" — el plan solo pedía los 3 más usados; agregar más sigue el mismo patrón si hace falta después.
- **Nota de entorno**: igual que los puntos anteriores, se escribió sin acceso a PHP/Composer/Sail. Se verificó la firma exacta de `Filter::toggle()`, `->query()` y `Table::filters(array, layout:)` contra el código fuente y la documentación oficial de Filament 5, pero falta probarlo corriendo el proyecto real.
- Entregado como patch (`git am`).

## [2026-08-23] Crear paciente sin salir del formulario de Cita (punto 3 del plan de UX)

- `app/Filament/Resources/Citas/Schemas/CitaForm.php`: se agregó `->createOptionForm([...])` al selector `paciente_id`, con los mismos campos que el formulario de Pacientes (nombres, apellidos, cédula, fecha de nacimiento, teléfono, email, dirección, sexo). Ahora hay un botón "+" junto al selector que abre un modal para dar de alta un paciente nuevo sin perder los datos ya cargados en la cita.
- Se agregó validación `->unique(table: 'pacientes', column: 'cedula')` al campo cédula de ese modal — el formulario original de Pacientes no la tenía (solo la restricción de la base de datos), así que sin esto el modal habría mostrado el error crudo de MySQL en vez de un mensaje de validación claro si se repetía una cédula. **Nota**: esta validación se agregó solo en el modal nuevo, no en `PacienteForm.php` original — queda documentado como deuda técnica pendiente en `MEMORIA.md` sección 7.
- Se mejoró el selector de paciente en el mismo formulario: antes solo mostraba `nombres`, ahora muestra "Nombres Apellidos" (`getOptionLabelFromRecordUsing`) y permite buscar por nombre, apellido o cédula — para poder distinguir pacientes con el mismo nombre de pila.
- **Nota de entorno**: igual que los puntos 1 y 2, se escribió sin acceso a PHP/Composer/Sail. Se verificó la API exacta de `createOptionForm()`, `getOptionLabelFromRecordUsing()` y la firma de `unique()` contra el código fuente y la documentación oficial de Filament 5, pero falta probarlo corriendo el proyecto real.
- Entregado como patch (`git am`).

## [2026-08-23] Cambiar estado de una cita con un clic (punto 2 del plan de UX)

- `app/Filament/Resources/Citas/Tables/CitasTable.php`: se agregó un `ActionGroup` "Cambiar estado" en la tabla de Citas, antes del botón Editar. Contiene un botón por cada estado válido (Pendiente/Confirmada/Atendida/Cancelada); cada uno se oculta si la cita ya está en ese estado, y al hacer clic actualiza `estado` directo (`$record->update(...)`) sin abrir el formulario ni navegar de página, mostrando una notificación de éxito.
- Se extrajo el `match` de colores de estado (antes duplicado inline en la columna) a un método `colorEstado()` compartido, usado tanto por la columna con badge como por los nuevos botones de cambio de estado — así ambos quedan visualmente consistentes.
- El grupo completo respeta permisos: solo visible si `CitaResource::canEdit($record)`, igual que el botón Editar existente.
- **No implementado en esta pasada**: el mismo flujo rápido en el widget "Citas de hoy" del Dashboard (que por ahora solo tiene Editar). Queda como mejora natural para una próxima sesión si se quiere.
- **Nota de entorno**: igual que el punto 1, este cambio se escribió sin acceso a PHP/Composer/Sail. Se verificó la API exacta (`Filament\Actions\Action`, `ActionGroup`, `Notification::make()->send()`) contra el código fuente y la documentación oficial de Filament 5, pero falta probarlo corriendo el proyecto real.
- Entregado como patch (`git am`).

## [2026-08-23] Dashboard: widget de "citas de hoy" (punto 1 del plan de UX)

- Nuevo `app/Filament/Widgets/CitasDeHoyWidget.php`: widget de tabla (`Filament\Widgets\TableWidget`) que muestra las citas con `fecha` = hoy, ordenadas por `hora_inicio`, con columnas Hora/Paciente/Médico/Área/Estado (mismos colores de badge que la tabla de Citas) y acción de Editar respetando `CitaResource::canEdit()`. Se autodescubre solo porque `AdminPanelProvider` ya apuntaba `discoverWidgets()` a esa carpeta.
- `AdminPanelProvider`: se quitaron `AccountWidget` y `FilamentInfoWidget` (las tarjetas genéricas "Welcome"/"filament") para que el widget de citas de hoy sea lo primero que se ve al entrar a `/admin`.
- **No implementado en esta pasada**: filtrar las citas por médico logueado si el rol es `medico`. Sigue aplicando la limitación conocida de que `users` y `medicos` no están conectados (ver `MEMORIA.md` sección 9) — por ahora todos los roles ven todas las citas del día.
- **Nota de entorno**: este cambio se escribió sin acceso a PHP/Composer/Sail (entorno de generación del patch no tiene esas herramientas), así que no se pudo correr `php artisan` ni probar visualmente. Se siguió al pie de la letra la convención de los Resources existentes (namespace, sintaxis Filament 5 con `recordActions`, colores de badge). **Falta probar en el entorno real** levantando Sail y entrando a `/admin`.
- Entregado como patch (`git am`).

## [2026-08-23] Investigación de buenas prácticas de agendamiento clínico + plan de UX

Con el sistema interno ya funcional de punta a punta (CRUD + roles confirmados), se investigaron buenas prácticas de software de agendamiento clínico para identificar las mejoras de experiencia de uso con mayor impacto para recepción/médicos en el día a día.

Se dejó un plan priorizado de 5 mejoras (documentado en `MEMORIA.md` sección 8):
1. Dashboard con "citas de hoy" al entrar al panel (⭐ mayor impacto).
2. Cambiar el estado de una cita con un clic, sin abrir el formulario completo.
3. Crear un paciente nuevo sin salir del formulario de Cita (modal).
4. Filtros rápidos en la lista de Citas ("Hoy", "Pendientes", "Confirmadas").
5. Buscador global mejorado.

Explícitamente descartado por ahora (fase futura): recordatorios automáticos por WhatsApp/SMS, portal de autoagendamiento para pacientes.

No se tocó código en esta sesión — es planificación para la siguiente. Pendiente confirmar con el usuario cuál de los 5 puntos priorizar primero; sugerencia por defecto: puntos 1 y 2.

## [2026-08-23] Fix de fondo: botones no conectados a los permisos por rol

Diagnóstico del problema reportado (botón "Crear" visible para rol sin permiso, y borrado de Paciente ejecutándose sin 403 en vez de bloquearse):

**Causa raíz**: `canCreate()`/`canEdit()`/`canDelete()` en el Resource solo se aplican automáticamente al navegar por URL completa a las páginas de Crear/Editar (ahí sí bloquean con 403 correctamente). La *visibilidad* de los botones en pantalla y la ejecución del botón "Eliminar" (una acción de Livewire sin navegación de página) **no** estaban conectadas a esos métodos — Filament no lo hace automático, hay que conectarlo a mano.

**Impacto real**: no era solo cosmético — el botón "Eliminar" se podía ejecutar sin chequeo de rol; en la prueba anterior solo lo salvó la restricción de integridad de MySQL (el paciente tenía citas asociadas). Un registro sin relaciones se habría borrado sin ser admin.

**Fix**: se agregó `->visible()` en 18 puntos: los 6 `CreateAction` (páginas de lista), los 6 `EditAction` (tablas), los 6 `DeleteAction` (páginas de edición), y los 6 `DeleteBulkAction` (tablas) — cada uno conectado al método de autorización correspondiente del Resource.

Entregado como patch (`git am`). **Confirmado funcionando**: probado con el usuario de rol `recepcion` — Áreas/Médicos sin botones de Crear/Editar/Eliminar, Pacientes/Citas/Facturas con Crear/Editar pero sin Eliminar, Historia Clínicas sigue sin aparecer en el menú. Sistema de roles y permisos completo y verificado de punta a punta.

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
