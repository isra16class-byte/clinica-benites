# 🧠 MEMORIA DEL PROYECTO — Clínica Benites

Este archivo es un resumen de contexto para retomar el desarrollo en cualquier momento (por ti mismo o pegándoselo a una IA). Explica qué es el proyecto, cómo está armado, qué decisiones se tomaron y por qué, y qué falta.

Última actualización: 25 de agosto de 2026 — vigesimonovena entrada del día (**confirmado funcionando por el usuario en el entorno real**, por captura de pantalla del Escritorio, tras aplicar el patch de la entrada anterior y recompilar. El usuario dijo explícitamente que ahora sí "sintió que cogió formato" — a diferencia del acento sutil de la iteración previa. Confirmado visualmente: el topbar se ve navy sólido de punta a punta, el logo se lee en blanco con buen contraste (confirmando que el swap por CSS a `logo-horizontal-white.png` funcionó correctamente, sin el riesgo de invisibilidad que se había anticipado en la entrada anterior), el ícono de menú/sidebar también se ve en blanco, y el buscador y el avatar mantienen su fondo blanco/negro de siempre, contrastando bien contra el navy. El resto del panel (sidebar, tarjetas, gráficos) sigue intacto. **Con esto, el rediseño del encabezado queda cerrado y confirmado, en su versión final más audaz** (topbar sólido, no el acento de 2px de la vigesimosexta/vigesimoséptima entrada, descartado por sentirse insuficiente).

Última actualización anterior: 25 de agosto de 2026 — vigesimoctava entrada del día (**el usuario, tras ver el resultado ya confirmado del rediseño anterior, dijo explícitamente que lo esperaba más audaz** — el acento sutil de 2px se sentía "igual de genérico". Se le mostraron 3 conceptos visuales más fuertes (topbar sólido en color de marca, topbar flotante tipo tarjeta, bloque de marca solo en el logo) y **el usuario eligió el topbar sólido en navy** (opción A). Antes de escribir el CSS, se clonó de nuevo el código fuente real del tag `v5.7.6` de `filamentphp/filament` (mismo criterio de siempre) para verificar los selectores de los elementos que viven *dentro* del `.fi-topbar` — no solo el fondo — porque un cambio de este tamaño (fondo sólido, no un acento) obliga a revisar contraste de cada elemento hijo, no solo del texto: `.fi-icon-btn` (botones de abrir/cerrar sidebar, `text-gray-500` por defecto), el logo (`<x-filament-panels::logo>`, usado también en el drawer móvil y en la página de login sobre fondo claro, así que no se puede cambiar globalmente) y el buscador (`.fi-input-wrp`, ya viene con `bg-white`, así que no hace falta tocarlo). **Hallazgo clave que evitó repetir el error del sidebar oscuro**: el logo actual (`public/images/logo-horizontal.png`) es texto navy sobre fondo transparente — con el topbar también navy, quedaría invisible. Se encontró que el proyecto **ya tiene** `public/images/logo-horizontal-white.png` (variante blanca, ya preparada) — se usa un swap por CSS puro (`content: url(...)` sobre `.fi-topbar-start .fi-logo`, sin tocar `AdminPanelProvider.php` ni el Blade) para que solo la instancia del topbar cambie de imagen, dejando intacto el logo navy del drawer móvil y del login (que siguen sobre fondo claro). El bloque de "acento sutil de 2px" de la entrada anterior queda reemplazado por: `.fi-topbar` con `background-color` navy sólido + sombra sutil (en vez del `ring` gris por defecto), `.fi-icon-btn` dentro del topbar en blanco/blanco translúcido, y el logo swapeado a la variante blanca — todo con `!important` porque son overrides de clases utilitarias de Tailwind con la misma especificidad, mismo criterio que ya costó 2 intentos fallidos en el fix del sidebar (ver sección 8.1). **Aún sin confirmar por el usuario en el entorno real** — corregido sin acceso a Sail/npm en esta sesión, pendiente que el usuario aplique el patch y recompile.

Última actualización anterior: 25 de agosto de 2026 — vigesimoséptima entrada del día (**confirmado funcionando por el usuario en el entorno real**, por captura de pantalla del Escritorio tras aplicar el patch de la entrada anterior y correr `./vendor/bin/sail npm run build` — nota: el `npm run build` directo falló primero por un problema de PATH de WSL, resuelto usando `sail` en vez del npm de Windows, sin relación con el código del patch en sí. Se confirmó visualmente: (1) el buscador global ya muestra el placeholder específico ("Buscar pacientes, citas, médicos, fact..."), con el ancho ampliado en desktop; (2) el acento de marca de 2px con degradado navy→verde azulado se ve en el borde inferior del `.fi-topbar`, sutil y sin afectar el fondo blanco ni el texto; (3) el resto del panel (sidebar, tarjetas de indicadores, los 2 gráficos) se ve intacto, sin ningún efecto colateral del CSS nuevo. El usuario confirmó explícitamente que ya probó el menú de usuario (nombre+rol+Editar perfil+Cerrar sesión) y la página `/admin/profile`, y que todo está bien, aunque sin una segunda captura de esa parte puntual. **Con esto, el rediseño del encabezado (topbar) queda cumplido y confirmado en el entorno real** — buscador, acento de marca, menú de usuario con perfil de rol, y página de edición de perfil con campo Rol de solo lectura.

Última actualización anterior: 25 de agosto de 2026 — vigesimosexta entrada del día (**implementación del plan de rediseño del encabezado (topbar), dejado 100% redactado y verificado en la entrada anterior** — esta sesión se limitó a ejecutarlo tal cual, sin re-investigar nada del código fuente de Filament, según lo pedido explícitamente por el usuario. Se crearon los 2 archivos nuevos (`lang/vendor/filament-panels/es/global-search.php`, `app/Filament/Pages/EditProfile.php`) con el contenido exacto que quedó documentado, se aplicaron los 2 cambios a `AdminPanelProvider.php` (`->profile()` + `->userMenuItems()`) y se agregó el bloque de CSS completo al final de `theme.css`. **Diferencia con el entorno de la sesión anterior**: esta vez sí había forma de instalar PHP en el entorno de trabajo (no estaba disponible al principio, se instaló vía `apt-get install php-cli`, quedó PHP 8.3.6 CLI funcionando) — se corrió `php -l` sobre los 2 archivos nuevos y sobre el `AdminPanelProvider.php` modificado, y los 3 pasaron sin errores de sintaxis. No hizo falta ajustar nada del código redactado en la entrada anterior — copió/pegó tal cual, confirmando que el nivel de verificación de esa sesión (contra el Blade/PHP/CSS real de Filament 5.7.6) fue suficiente. No se tuvo acceso a un entorno Laravel completo (sin `vendor/`, sin base de datos) para correr `npm run build` ni probar el panel en vivo — eso queda pendiente para cuando el usuario aplique el patch. Se generó el patch con `git format-patch -1 HEAD` de la forma acostumbrada.

Última actualización anterior: 25 de agosto de 2026 — vigesimoquinta entrada del día (**sesión de investigación y planificación completa para el rediseño del encabezado (topbar) del panel** — pedido pendiente desde la entrada anterior. A pedido explícito del usuario, esta sesión se dedicó por completo a dejar todo investigado y documentado con precisión quirúrgica para que la próxima sesión pueda implementarlo de punta a punta sin tener que re-investigar nada — **no se tocó ningún archivo de código todavía**, mismo criterio ya usado antes en el proyecto para sesiones de planificación pura (ver secciones 6.2/6.3 "propuesta, sin tocar código" antes de construirse). Se clonó el repo (`https://github.com/isra16class-byte/clinica-benites`) y, siguiendo al pie de la letra la lección aprendida en la sesión del bug del sidebar (ver más abajo en esta misma sección 8.1, "Fix 2"), también se clonó el código fuente real del tag `v5.7.6` de `filamentphp/filament` en GitHub — no se confió en ningún ejemplo de documentación/comunidad sin versión específica. Sin acceso a PHP/Sail en este entorno de trabajo (igual que varias sesiones anteriores), así que nada de esto se validó corriendo código — pero sí se validó línea por línea contra el Blade/PHP/CSS reales del paquete instalado, que es el nivel de confianza más alto posible sin un entorno vivo.

**Los 4 requisitos del usuario, y qué se encontró para cada uno:**

1. **Buscador con placeholder específico** (hoy dice solo "Buscar", genérico): el placeholder no vive en `AdminPanelProvider.php` ni en ningún Resource — Filament lo resuelve vía traducción, clave `filament-panels::global-search.field.placeholder`, definida en el paquete en `vendor/filament/filament/packages/panels/resources/lang/es/global-search.php` (confirmado que Filament **sí trae el paquete de idioma español completo**, incluida esta clave, ya que `config('app.locale') = 'es'` desde el fix de la sección 5). Laravel permite **sobreescribir cualquier traducción de un paquete** creando el mismo archivo en `lang/vendor/{paquete}/{locale}/{archivo}.php` de la app — el paquete se registra internamente como `filament-panels` (confirmado en `FilamentServiceProvider.php`, línea `->name('filament-panels')` + `->hasTranslations()`). **No hace falta tocar ningún archivo del vendor ni forkear nada** — un archivo nuevo en `lang/vendor/filament-panels/es/global-search.php` (con el mismo array que trae el paquete, pero el `placeholder` cambiado) alcanza. El proyecto **no tiene carpeta `lang/` todavía** — hay que crearla.

2. **Menú de usuario con nombre + rol + editar perfil + cerrar sesión** (hoy no existe ninguna personalización, el `AdminPanelProvider.php` no llama a `->userMenuItems()`): se encontró, leyendo `packages/panels/src/Panel/Concerns/HasUserMenu.php` y `packages/panels/resources/views/components/user-menu.blade.php` del código fuente real, que Filament **ya tiene un mecanismo oficial para exactamente este caso** — no hace falta ningún hack de CSS ni renderHook a mano:
   - El ítem del menú con key `'profile'` es especial: si se le quita la URL (`->url(null)`) y no tiene ninguna acción asociada, el Blade lo detecta (`$hasProfileHeader = $itemsBeforeThemeSwitcher->has('profile') && blank($item->getUrl()) && (! $item->hasAction())`) y en vez de renderizarlo como un link cualquiera, lo renderiza como un **header** (`<x-filament::dropdown.header>`) — o sea, texto no clickeable, ideal para mostrar "Nombre + Rol".
   - `Action::label()` (definido en `packages/actions/src/Concerns/HasLabel.php`) acepta `string | Htmlable | Closure | null` — **no solo texto plano**. Esto significa que se puede pasar un `Illuminate\Support\HtmlString` con HTML propio (ej. nombre en un `<span>` y rol en otro `<span>` más chico, con clases CSS propias) y Blade lo va a insertar sin escapar (porque `{{ }}` en Blade llama a `->toHtml()` en vez de escapar cuando el valor implementa `Htmlable`) — es el mecanismo **oficialmente soportado** por Filament para HTML custom en labels, no una vulnerabilidad ni un hack.
   - Para el link "Editar perfil" (separado del header de nombre/rol), alcanza con registrar un segundo ítem con otra key (ej. `'edit_profile'`) apuntando a la URL de la página de perfil (`Filament::getProfileUrl()` / helper `filament()->getProfileUrl()`), con `->sort(-1)` para que aparezca pegado al header, antes de cualquier otro ítem.
   - "Cerrar sesión" **no hay que tocarlo** — si no se registra explícitamente una key `'logout'`, Filament la agrega sola al final (`getUserLogoutMenuItem()`), ya en español (`__('filament-panels::layout.actions.logout.label')`), con su propio ícono y el POST correcto al logout — tocarla sería trabajo de más y riesgo de romper el logout real.

3. **Página de perfil de usuario** (hoy no existe): se encontró que Filament 5.7.6 **ya trae una página de perfil lista para usar**, `Filament\Auth\Pages\EditProfile` (`packages/panels/src/Auth/Pages/EditProfile.php`), activable con **una sola línea** en el panel: `->profile()`. Esa página nativa ya incluye, sin escribir nada: campo Nombre, campo Email (con verificación de cambio de email si está habilitada, no es el caso acá), campo Contraseña + confirmación (opcional, si se deja vacío no cambia la contraseña), campo "Contraseña actual" (aparece solo si se cambia email o contraseña, por seguridad), y ya tiene traducción al español completa (`packages/panels/resources/lang/es/auth/pages/edit-profile.php`, confirmado que existe). O sea: **el 100% de "editar nombre, email y contraseña" que pide el punto 3 ya viene resuelto por Filament**, no hay que reconstruir un formulario de cero. Lo único que agrega valor real construir encima es un campo extra de solo lectura mostrando el Rol (para contexto, ya que el usuario no puede cambiar su propio rol desde acá — eso sigue siendo exclusivo de `/admin/users`, con los mismos permisos ya documentados en la sección 10) — para eso alcanza con crear `App\Filament\Pages\EditProfile extends Filament\Auth\Pages\EditProfile` y sobreescribir el método `form()` insertando un `TextInput::make('rol')->disabled()->dehydrated(false)` con el mismo `formatStateUsing()` (Administrador/Recepción/Médico) que ya usan `UsersTable.php`/`UserForm.php`, para que el rol se vea con el mismo texto en todo el panel. `dehydrated(false)` es clave — evita que ese campo se intente guardar (el usuario no puede cambiar su propio rol desde su perfil, por diseño, coherente con la matriz de permisos de la sección 10).

4. **Verse profesional, jerarquía visual, paleta consistente, legibilidad nombre/rol diferenciados**: se confirmó contra el CSS real del paquete (`packages/support/resources/css/components/dropdown/header.css`, `dropdown/index.css`, `dropdown/list/item.css`, `packages/panels/resources/css/components/user-menu.css`, `topbar.css`, `global-search.css`) varios puntos que hay que tener en cuenta para que el resultado se vea bien y no roto:
   - El panel desplegable del menú de usuario (`.fi-dropdown-panel`) tiene por defecto un ancho máximo fijo de **`max-w-[14rem]`** (224px, con `!important` de Tailwind) — bastante angosto para mostrar nombre + rol en dos líneas cómodamente sin que se corte. Hay que ensancharlo un poco con una regla scoped a `.fi-user-menu .fi-dropdown-panel` (la clase `.fi-user-menu` ya está confirmada en el propio `user-menu.blade.php`, se agrega vía `->class(['fi-user-menu'])` al dropdown — así el ensanchado no afecta a ningún otro dropdown del panel, solo a este).
   - El `<span>` interno de `.fi-dropdown-header` (donde va el label) tiene la clase Tailwind **`truncate`** por defecto (`header.css`) — corta con `...` cualquier texto que no entre en una sola línea. Si el HTML del label mete nombre y rol en dos `<span>` apilados (`display: flex; flex-direction: column`), hay que **neutralizar el `truncate` heredado** en una regla propia (`.fi-user-menu .fi-dropdown-header span { white-space: normal; }` o similar), si no el rol/nombre se corta feo.
   - El avatar (`<x-filament-panels::avatar.user>`) **no necesita ningún archivo de imagen** — por defecto Filament usa `Filament\AvatarProviders\UiAvatarsProvider` (confirmado en `packages/panels/src/Panel/Concerns/HasAvatars.php`), que genera un avatar con las iniciales del nombre automáticamente (servicio ui-avatars.com) — el "círculo con una sola letra" que el usuario mencionó como básico en el feedback ya es ese default, no hace falta cambiarlo para esta tarea (es un problema aparte, de branding, no de este pedido puntual).
   - Los 2 colores de marca (azul marino `#0C447C`, verde azulado `#0F6E56`) **no están registrados como colores semánticos de Filament** (`->colors(['primary' => ...])` solo tiene el primario) — se aplicaron directo en hex en los `ChartWidget` (ver sección 6.6.2). Para el badge de rol en el header del menú, la decisión tomada (a falta de poder confirmarla con el usuario en esta sesión de solo-documentación) es usar los 2 colores de marca + un gris neutro para el 3er rol, **sin tocar la paleta global de colores de Filament** (evita el riesgo de que cambiar `->colors()` globalmente afecte sin querer otros badges/botones ya confirmados en el resto del panel, ej. los badges de estado de Citas/Facturas): **admin → navy `#0C447C`** (autoridad/marca principal), **médico → verde azulado `#0F6E56`** (color clínico/positivo, ya usado para "Cobrado" en el gráfico de ingresos), **recepción → gris neutro** (rol de uso más frecuente/operativo, no hace falta un color de marca ahí). Queda como supuesto razonable, ajustable después si el usuario prefiere otro criterio — mismo patrón de "decisión con supuesto documentado, editable" que ya se usó varias veces en el proyecto (secciones 6.2/6.3).
   - `.fi-topbar` (`topbar.css`) es hoy `flex items-center bg-white px-4 shadow-xs ring-1 ring-gray-950/5` — fondo blanco liso. Para que se sienta menos genérico sin arriesgar nada (recordar el sidebar oscuro descartado en 8.1 por texto invisible), la idea validada es un detalle sutil y de bajo riesgo: una franja/acento de 2-3px con degradado de los 2 colores de marca pegado al borde inferior del `.fi-topbar` (vía `::after` o `box-shadow`, sin tocar el fondo blanco principal ni el texto) — refuerza marca sin repetir el intento fallido de forzar colores de texto sobre fondos oscuros.
   - `.fi-global-search` (contenedor del buscador dentro de `.fi-topbar-end`) tiene `flex-1` pero **no un ancho mínimo** — en pantallas grandes se ve angosto/apretado. Se puede dar un `min-width` razonable (ej. `20rem`) **solo desde el breakpoint `lg`** (mismo breakpoint que ya usa Filament para `.fi-topbar-start`, que oculta el logo del topbar por debajo de `lg` porque en mobile/tablet se usa el logo del sidebar en su lugar) — así no se arriesga a romper el layout en pantallas chicas/medianas.

**Plan de implementación completo, listo para ejecutar en la próxima sesión** (código ya redactado y verificado contra el código fuente real de Filament 5.7.6 — falta solo crear los archivos, correr `php -l`/probar en el entorno real, y ajustar si algo no calza):

**Archivo nuevo 1 — `lang/vendor/filament-panels/es/global-search.php`** (sobreescribe el placeholder del buscador global, sin tocar el vendor):
```php
<?php

return [
    'field' => [
        'label' => 'Búsqueda global',
        'placeholder' => 'Buscar pacientes, citas, médicos, facturas...',
    ],
    'no_results_message' => 'No se han encontrado resultados.',
];
```
(Se mantienen `label` y `no_results_message` iguales al original del paquete — Laravel reemplaza el archivo completo, no hace merge clave por clave, así que hay que copiar todo el array aunque solo cambie una línea.)

**Archivo nuevo 2 — `app/Filament/Pages/EditProfile.php`** (extiende la página nativa de Filament, agrega el campo Rol de solo lectura):
```php
<?php

namespace App\Filament\Pages;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;

class EditProfile extends BaseEditProfile
{
    public function form(Schema $schema): Schema
    {
        return $schema->components([
            $this->getNameFormComponent(),
            $this->getEmailFormComponent(),
            $this->getRolFormComponent(),
            $this->getPasswordFormComponent(),
            $this->getPasswordConfirmationFormComponent(),
            $this->getCurrentPasswordFormComponent(),
        ]);
    }

    protected function getRolFormComponent(): Component
    {
        // Mismo mapeo de etiquetas que UsersTable.php/UserForm.php, para
        // que el rol se vea igual en todo el panel. Solo lectura: el
        // usuario no puede cambiar su propio rol desde su perfil (sigue
        // siendo exclusivo de /admin/users, ver matriz de la sección 10).
        return TextInput::make('rol')
            ->label('Rol')
            ->formatStateUsing(fn (?string $state): string => match ($state) {
                'admin' => 'Administrador',
                'recepcion' => 'Recepción',
                'medico' => 'Médico',
                default => $state ?? '—',
            })
            ->disabled()
            ->dehydrated(false);
    }
}
```

**Cambios en `AdminPanelProvider.php`** — agregar, después de `->favicon(...)`:
```php
->profile(\App\Filament\Pages\EditProfile::class, isSimple: false)
```
(`isSimple: false` para que la página de perfil use el layout completo del panel, con sidebar/topbar, en vez del layout centrado tipo login — más coherente con que el usuario ya está navegando dentro del panel, no llegando de afuera.)

Y agregar, en algún punto del builder (antes de `->discoverResources(...)`, mismo orden que el resto de configuración visual):
```php
->userMenuItems([
    'profile' => fn (\Filament\Actions\Action $action) => $action
        ->label(fn () => new \Illuminate\Support\HtmlString(
            '<span class="fi-user-menu-header-name">' .
                e(filament()->getUserName(filament()->auth()->user())) .
            '</span>' .
            '<span class="fi-user-menu-header-role fi-user-menu-header-role-' .
                e(filament()->auth()->user()->rol ?? 'default') . '">' .
                e(match (filament()->auth()->user()->rol ?? null) {
                    'admin' => 'Administrador',
                    'recepcion' => 'Recepción',
                    'medico' => 'Médico',
                    default => 'Sin rol asignado',
                }) .
            '</span>'
        ))
        ->url(null)
        ->icon(null),
    'edit_profile' => fn () => \Filament\Actions\Action::make('edit_profile')
        ->label('Editar perfil')
        ->icon(\Filament\Support\Icons\Heroicon::OutlinedPencilSquare)
        ->url(fn (): ?string => filament()->getProfileUrl())
        ->sort(-1),
])
```
(No hace falta registrar `'logout'` — Filament la agrega sola al final, ya en español, ver arriba. Ojo con el `use` de `Action`/`HtmlString`/`Heroicon` arriba del archivo si se prefiere no usar el nombre completo con `\` inline como en el borrador de arriba — cualquiera de los dos estilos es válido, en el proyecto ya conviven ambos según el archivo.)

**Adiciones a `theme.css`** (al final del archivo, mismo patrón de comentario extenso que ya usa el resto del archivo, explicando el porqué y citando el archivo fuente real de Filament donde se verificó cada selector):
```css
/*
 * Rediseño del encabezado (topbar): buscador, menú de usuario con
 * nombre+rol, y acento de marca. Selectores verificados contra el código
 * fuente real de Filament v5.7.6 (mismo criterio que el resto de este
 * archivo) — ver MEMORIA.md, entrada del 25 ago 2026 (vigesimoquinta),
 * para el detalle de qué archivo del paquete se usó para confirmar cada
 * clase.
 */

/* Acento sutil de marca en el borde inferior del topbar — franja de 2px
   con degradado de los 2 colores de marca, sin tocar el fondo blanco ni
   el texto (evita repetir el problema de contraste del sidebar oscuro
   descartado, ver sección 8.1). */
.fi-topbar {
    position: relative;
}

.fi-topbar::after {
    content: '';
    position: absolute;
    inset-inline: 0;
    bottom: 0;
    height: 2px;
    background: linear-gradient(90deg, rgb(12 68 124), rgb(15 110 86));
}

/* Buscador global un poco más ancho en desktop, para que no se vea
   apretado — solo desde `lg` (mismo breakpoint que ya usa Filament para
   mostrar/ocultar el logo del topbar, ver .fi-topbar-start en
   topbar.css), para no arriesgar el layout en pantallas chicas/medianas. */
@media (min-width: 1024px) {
    .fi-global-search {
        min-width: 20rem;
    }
}

/* Menú de usuario: header de nombre+rol más ancho que el default de
   Filament (14rem, ver dropdown/index.css) para que no se corte, y el
   `truncate` heredado del <span> de .fi-dropdown-header (header.css)
   neutralizado para poder mostrar 2 líneas en vez de una sola cortada
   con "...". */
.fi-user-menu .fi-dropdown-panel {
    max-width: 18rem !important;
}

.fi-user-menu .fi-dropdown-header {
    padding: 0.75rem 1rem;
}

.fi-user-menu .fi-dropdown-header span {
    display: flex;
    flex-direction: column;
    gap: 0.125rem;
    white-space: normal;
    overflow: visible;
    text-overflow: clip;
}

.fi-user-menu-header-name {
    font-size: 0.875rem;
    font-weight: 600;
    color: rgb(15 23 42);
}

.dark .fi-user-menu-header-name {
    color: rgb(255 255 255);
}

/* Rol como badge chico, diferenciado tipográficamente del nombre (más
   chico, con color de marca) y visualmente por color según el rol —
   navy para admin, verde azulado para médico (mismo verde ya usado para
   "Cobrado" en IngresosPorMesChartWidget, sección 6.6.2), gris neutro
   para recepción. Colores en hex directo, sin tocar ->colors() global
   del panel, para no afectar badges/botones ya confirmados en el resto
   del sistema (ver el punto sobre esto en MEMORIA.md, misma entrada). */
.fi-user-menu-header-role {
    display: inline-flex;
    width: fit-content;
    align-items: center;
    padding: 0.0625rem 0.5rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
}

.fi-user-menu-header-role-admin {
    background-color: rgb(12 68 124 / 0.1);
    color: rgb(12 68 124);
}

.dark .fi-user-menu-header-role-admin {
    background-color: rgb(12 68 124 / 0.25);
    color: rgb(147 197 253);
}

.fi-user-menu-header-role-medico {
    background-color: rgb(15 110 86 / 0.1);
    color: rgb(15 110 86);
}

.dark .fi-user-menu-header-role-medico {
    background-color: rgb(15 110 86 / 0.25);
    color: rgb(110 231 183);
}

.fi-user-menu-header-role-recepcion {
    background-color: rgb(100 116 139 / 0.1);
    color: rgb(71 85 105);
}

.dark .fi-user-menu-header-role-recepcion {
    background-color: rgb(100 116 139 / 0.25);
    color: rgb(203 213 225);
}
```

**Pendiente antes de dar por bueno el CSS de arriba**: los valores de color en `.dark` son un supuesto razonable (no hay ningún dark mode confirmado/probado en este proyecto todavía — `->darkMode()` nunca se llamó en `AdminPanelProvider.php`, así que Filament decide el modo según preferencia del sistema operativo del usuario por defecto) — si en el entorno real el panel nunca entra en modo oscuro, esas reglas simplemente no tienen efecto, sin romper nada; no vale la pena investigarlo más a fondo para esta tarea puntual.

**Qué falta hacer en la próxima sesión, en orden:**
1. Crear los 2 archivos nuevos (`lang/vendor/filament-panels/es/global-search.php`, `app/Filament/Pages/EditProfile.php`) con el contenido de arriba.
2. Aplicar los 2 cambios a `AdminPanelProvider.php` (`->profile()` + `->userMenuItems()`).
3. Agregar el bloque de CSS de arriba al final de `theme.css`.
4. Si hay acceso a PHP en esa sesión, correr `php -l` sobre los 2 archivos nuevos y el provider modificado antes de dar el patch por bueno (esta sesión no tuvo PHP disponible en el entorno de trabajo, igual que varias sesiones anteriores documentadas en este archivo).
5. Generar el patch de la forma acostumbrada (identidad `isra16class-byte`/`isra16class@gmail.com`, `git format-patch -1 HEAD`) y entregarlo.
6. Una vez que el usuario lo aplique (`git am`), corra `npm run build` (cambio de CSS) y pruebe en el entorno real: confirmar que (a) el placeholder del buscador cambió, (b) el menú del avatar muestra nombre+rol+Editar perfil+Cerrar sesión con la jerarquía tipográfica esperada, (c) `/admin/profile` carga y permite cambiar nombre/email/contraseña, y (d) nada del resto del panel (otros dropdowns, otros badges) se vio afectado por el CSS nuevo — actualizar `MEMORIA.md`/`CHANGELOG.md` con el resultado, siguiendo el mismo patrón de "confirmado/aún sin confirmar" que usa el resto de este archivo.

**Qué NO se hizo en esta entrada, a propósito**: no se creó ni modificó ningún archivo de código (`.php`/`.css`) — es 100% investigación y plan, dejado con el nivel de detalle suficiente para que la implementación de la próxima sesión sea directa (copiar/pegar el código ya redactado arriba, ajustando lo que no calce al probarlo en el entorno real) en vez de tener que re-investigar el código fuente de Filament desde cero.

Última actualización anterior: 25 de agosto de 2026 — vigesimocuarta entrada del día (el usuario, tras confirmar el rediseño de paleta/contraste, señaló que el encabezado del panel — fila de logo, buscador y avatar de usuario — "se siente muy básico" y pidió mejorar esa zona, incluyendo mostrar el módulo de usuarios como un desplegable ahí. Se compartió un diagnóstico (buscador con placeholder genérico, avatar sin nombre/rol visible) y se confirmó que `AdminPanelProvider.php` no tiene ninguna personalización de `userMenuItems()`/notificaciones/breadcrumbs — 100% default de Filament. El usuario definió que el menú del avatar debe mostrar **nombre + rol + editar perfil + cerrar sesión**. Se verificó el modelo `User` (campo `rol` string libre: admin/recepcion/medico) y se confirmó que **no existe página de perfil de usuario** — hay que crearla. **No se implementó nada** por límite de sesión — se le entregó al usuario un prompt para la sesión siguiente con todo este contexto ya investigado, para no perder tiempo re-explorando el modelo o el provider.)

Última actualización anterior: 25 de agosto de 2026 — vigesimotercera entrada del día (**confirmado funcionando por el usuario en el entorno real**, por captura de pantalla: tras aplicar el fix 3 (selectores verificados contra el código fuente de Filament 5.7.6) y recompilar, "Escritorio" en el menú lateral ya se ve con fondo azul marino sólido y texto/ícono en blanco, como estaba pensado. **Con esto, el rediseño visual completo de esta sesión queda cumplido**: color primario del panel y de los 2 `ChartWidget` en la paleta de marca (azul marino + verde azulado, entrada decimonovena), contraste de superficies — fondo de página gris-azulado, sombra sutil en tarjetas (entrada vigésima) — y estado activo del menú lateral con los selectores CSS reales de la versión instalada (entradas 21-22-23). Los 4 indicadores, los 2 gráficos y la navegación del panel están confirmados con la nueva identidad visual.)

Última actualización anterior: 25 de agosto de 2026 — vigesimosegunda entrada del día (el usuario confirmó por captura que el fix 2 tampoco tuvo efecto — el ítem activo del menú seguía con el pill tenue de siempre. **Causa real**: los 2 intentos anteriores se basaron en documentación/ejemplos de la comunidad sobre clases (`.fi-sidebar-item-active`, `.fi-sidebar-item-button`) que corresponden a versiones anteriores de Filament, no a la 5.7.6 instalada en este proyecto. **Corregido de verdad esta vez**: se clonó el código fuente real del tag `v5.7.6` de `filamentphp/filament` (mismo criterio ya usado para el bug del `$view` estático, sección 10) y se confirmó contra `packages/panels/resources/css/components/sidebar.css` e `item.blade.php` que la clase activa real es `.fi-active` sobre `<li class="fi-sidebar-item">`, el botón es `.fi-sidebar-item-btn`, y el ícono se puede seleccionar por `.fi-sidebar-item-icon` (coincide con `.fi-icon` en el mismo `<svg>`, confirmado en `generate_icon_html()`). Reescrito con los selectores exactos verificados, sin depender de anidamiento CSS. **Aún sin confirmar por el usuario en el entorno real** — pero por primera vez en esta serie de intentos, validado contra el código fuente real de la versión exacta instalada, no contra documentación genérica desactualizada. **Lección para el futuro**: ante cualquier hook class de Filament que no tenga el efecto esperado, clonar el tag exacto de la versión instalada y revisar el CSS/Blade fuente directamente, en vez de confiar en ejemplos de la comunidad sin versión especificada — mismo patrón que ya costó 2 intentos fallidos acá y que ya se había aprendido con el bug del `$view` estático.)

Última actualización anterior: 25 de agosto de 2026 — vigesimoprimera entrada del día (el usuario confirmó por captura de pantalla, tras aplicar el patch de la entrada anterior y recompilar, que (1) el fondo de página gris-azulado y (2) las sombras en tarjetas sí se veían aplicados — buena separación entre página y tarjetas. Pero (3) el ítem activo del menú lateral ("Escritorio") seguía con el pill tenue por defecto de Filament (fondo primary-50/texto primary-600, reaccionando solo al color primario ya cambiado, no a la regla nueva), no el fondo sólido azul marino + texto blanco esperado. **Corregido**: la regla usaba combinador de hijo directo (`>`), que no coincidía con la estructura real del DOM de Filament, y sin `!important` tampoco ganaba contra el CSS propio del framework — reescrita con anidamiento CSS + combinador descendiente (mismo patrón de la documentación oficial de Filament para este hook) y `!important` en los 3 valores. **Aún sin confirmar por el usuario en el entorno real** — corregido sin acceso a Sail/npm en esta sesión, pendiente que el usuario aplique el patch y recompile.)

Última actualización anterior: 25 de agosto de 2026 — vigésima entrada del día (el usuario, tras ver en el entorno real el cambio de paleta de la entrada anterior (captura de pantalla), señaló que "el sistema no tiene contraste con nada": el color de marca ya se veía en los gráficos, pero el fondo de página, la barra lateral y las tarjetas usaban tonos casi idénticos entre sí (blanco/gris muy claro), por lo que nada se distinguía visualmente — el ítem activo del menú lateral ("Escritorio") apenas se notaba, solo con texto azul sin fondo. **Aplicado** en `theme.css`, sin tocar ningún layout: (1) fondo de página (`.fi-main`) cambiado a gris-azulado para separarlo del blanco de las tarjetas; (2) sombra sutil agregada a tarjetas/secciones (`.fi-section`) y a los 4 indicadores del dashboard (`.fi-wi-stats-overview-stat`); (3) ítem activo del menú lateral con fondo sólido azul marino y texto blanco, en vez de solo texto azul. Se usaron hook classes de Filament documentadas como estables entre versiones, pero **aún sin confirmar por el usuario en el entorno real** — no se pudo verificar contra el HTML/CSS compilado real del proyecto en esta sesión (sin acceso a Sail/npm/inspector), así que si alguna clase no coincide exactamente con la instalada (Filament 5.7.6) esa regla puntual no tendría efecto, sin romper el resto.)

Última actualización anterior: 25 de agosto de 2026 — decimonovena entrada del día (el usuario compartió una captura de pantalla del Dashboard gerencial (ya confirmado funcionando en la entrada anterior) pidiendo ideas de color/organización visual para que se vea "profesional y elegante". Se propuso primero, solo en conversación, un mockup de referencia y una paleta de 3 colores con significado — **azul marino** (`#0C447C`, tomado del logo real de la clínica, sección 8.1) para lo principal/marca, **verde azulado** (`#0F6E56`) para lo positivo/cobrado, **ámbar** para lo pendiente — en vez de colores genéricos sin criterio. El usuario pidió aplicarlo. **Aplicado**: `AdminPanelProvider.php` (color primario del panel, de `Color::Teal` a `Color::hex('#0C447C')`), `IngresosPorMesChartWidget.php` (series Facturado/Cobrado, de gris/cian a azul claro/verde azulado) y `FacturacionPorAreaChartWidget.php` (serie única, de cian a azul marino). Solo colores — no se tocó ninguna query ni estructura de los 3 widgets, y `theme.css` no requirió cambios porque el color primario de Filament se resuelve vía `FilamentColor`, no por CSS. **Aún sin confirmar por el usuario en el entorno real** — no se pudo validar ni siquiera con `php -l` esta vez: el entorno de trabajo no tenía PHP instalado y la instalación de `php-cli` falló por error 404 en los paquetes del repositorio de Ubuntu (`archive.ubuntu.com`), a diferencia de sesiones anteriores donde sí se pudo instalar. Cambios de bajo riesgo (solo valores string/hex), pero pendiente que el usuario aplique el patch, compile assets (`npm run build`) y confirme visualmente.)

Última actualización anterior: 25 de agosto de 2026 — decimoctava entrada del día (**confirmado funcionando por el usuario en el entorno real**, por captura de pantalla: tras el fix de Carbon 3 de la entrada anterior, el widget "Alertas operativas" ya muestra días enteros y positivos en sus 3 secciones — "en 19 días", "316 días", "22 días internado" — y el resto del Dashboard gerencial (4 indicadores, gráfico de ingresos por mes con curva en varios meses, gráfico por área respondiendo al selector de métrica) se ve correcto. **Con esto, el objetivo de esta sesión de trabajo queda cumplido**: `DemoHistoricoSeeder` (decimocuarta entrada) generó datos de prueba históricos repartidos en 12 meses, y los 2 bugs que ese volumen de datos destapó en `AlertasOperativasWidget` — `translatedFormat()` sobre un string sin castear (decimosexta) y el cambio de comportamiento de `diffInDays()` en Carbon 3 (decimoséptima) — quedan corregidos y confirmados. El Dashboard gerencial completo (sección 6.6) sigue construido y confirmado de punta a punta, ahora además probado con volumen real de datos históricos, no solo con casos sueltos de prueba.)

Última actualización anterior: 25 de agosto de 2026 — decimoséptima entrada del día (**segundo bug encontrado y corregido**, tras confirmar el usuario que el fix de `translatedFormat()` de la entrada anterior sí quitó el error 500 — pero el widget "Alertas operativas" quedó con números raros: días con decimales larguísimos (`en 19.232496744222 días`) y, peor, **negativos** en Facturas vencidas y Camas ocupadas (`-315.675032824 días`, `-22.002572832199 días internado`) cuando debían ser positivos. **Causa**: cambio de comportamiento de Carbon 3 (que usa este proyecto sobre Laravel 13) respecto a Carbon 2 — `diffInDays()` ahora devuelve, por defecto, una diferencia **con signo** (negativa si la fecha pasada como argumento es anterior a `now()`) **y con decimales** (fracción exacta del día), en vez del entero absoluto que devolvía Carbon 2 por defecto. Los 3 usos de `now()->diffInDays(...)` en `alertas-operativas-widget.blade.php` (lotes por vencer, facturas vencidas, camas ocupadas) asumían el comportamiento viejo. **Corregido**: se cambiaron las 3 llamadas a `now()->diffInDays($fecha, true)` (segundo argumento `true` = valor absoluto, sin signo) envueltas en `(int) round(...)` para quedarse con días enteros. No se tocó ningún modelo, solo la vista Blade. **Aún sin confirmar por el usuario en el entorno real** — corregido sin acceso a Sail/MySQL.)

Última actualización anterior: 25 de agosto de 2026 — decimosexta entrada del día (**bug encontrado y corregido**, preexistente en el proyecto — no causado por `DemoHistoricoSeeder`, solo destapado por él: al recargar el panel con datos ya cargados, el widget "Alertas operativas" tiraba `Call to a member function translatedFormat() on string` en la sección de Facturas vencidas, tumbando esa sección del Dashboard. **Causa**: `Factura.fecha` es una columna `date`, pero el modelo `Factura` no la castea a `Carbon` (a diferencia de, por ejemplo, `Cirugia.fecha` o `LoteInventario.fecha_vencimiento`, que sí tienen `casts()`) — el resto del código ya lo sabía y usaba `Carbon::parse($record->fecha)` en vez de asumir un objeto Carbon (ver `FacturaResource::getGlobalSearchResultDetails()` y el equivalente en `CitaResource`), pero la vista `resources/views/filament/widgets/alertas-operativas-widget.blade.php` (Sesión 3) llamaba `$factura->fecha->translatedFormat(...)` directo, asumiendo que ya era Carbon. Nunca se había notado porque hasta ahora no existía ninguna factura de prueba que cumpliera "pendiente y con más de 30 días de antigüedad" — la sección de Facturas vencidas nunca se había renderizado con datos, solo con el mensaje vacío. **Corregido**: se envolvió `$factura->fecha` en `\Illuminate\Support\Carbon::parse(...)` en las 2 líneas que lo usaban dentro de esa vista — mismo patrón ya usado en el resto del proyecto, no se tocó el modelo `Factura` ni ningún otro archivo. **Aún sin confirmar por el usuario en el entorno real** — corregido sin acceso a Sail/MySQL, pendiente que el usuario aplique el patch y recargue el panel.)

Última actualización anterior: 25 de agosto de 2026 — decimoquinta entrada del día (**confirmado corriendo sin errores por el usuario en el entorno real**: `./vendor/bin/sail artisan db:seed --class=DemoHistoricoSeeder` de la entrada anterior terminó con "Datos de demostración cargados." en ~1.8s, sin ningún error de foreign key ni de tipo. Nota de entorno, no del proyecto: el usuario no tenía definido el alias `sail` de la documentación de Laravel (`sail: command not found`), se le indicó usar la ruta directa `./vendor/bin/sail` — no requirió ningún cambio de código. **Pendiente**: que el usuario confirme visualmente en `/admin` que el Dashboard gerencial (indicadores, gráficos con curva mensual, alertas operativas) se ve poblado como se esperaba con estos datos.)

Última actualización anterior: 25 de agosto de 2026 — decimocuarta entrada del día (a pedido del usuario, se creó `database/seeders/DemoHistoricoSeeder.php`: un seeder standalone (no registrado en `DatabaseSeeder.php`, se corre aparte con `sail artisan db:seed --class=DemoHistoricoSeeder`) que genera ~110 registros de prueba con fechas repartidas en los últimos 12 meses, para que el Dashboard gerencial (sección 6.6) se vea con datos reales en vez de vacío. Antes de escribirlo, el usuario había limpiado a mano (vía `tinker`, con el orden de borrado que se le indicó respetando llaves foráneas) todas las tablas excepto `areas` y `users`, así que el seeder asume esa base: reutiliza las áreas y el usuario admin existentes, y crea desde cero médicos, pacientes, citas, historias clínicas, facturas, inventario (ítems/lotes/movimientos) e infraestructura (camas, quirófanos, internamientos, cirugías, órdenes de estudio, servicios de ambulancia), respetando el mismo orden de dependencias. Contempla a propósito los 3 umbrales de `AlertasOperativasWidget` (lote por vencer a 90 días, factura pendiente vencida a 30 días, cama ocupada a 14 días) para que esa alerta también tenga contenido, no solo los 2 gráficos. **Confirmado corriendo sin errores por el usuario en el entorno real** (ver entrada siguiente) — escrito sin acceso a Sail/MySQL en el entorno de trabajo, se había validado solo sintaxis con `php -l`.

Última actualización anterior: 25 de agosto de 2026 — decimotercera entrada del día (**confirmado funcionando por el usuario en el entorno real**: tras aplicar el patch del `@source` faltante y recompilar con `npm run build`, las 3 tarjetas de "Alertas operativas" ya se ven lado a lado en 3 columnas, como estaba pensado. **Con esto, el Dashboard gerencial completo (sección 6.6) queda construido y confirmado de punta a punta en el entorno real** — los 4 indicadores clave (Sesión 1), los 2 gráficos (Sesión 2), y las 3 alertas operativas (Sesión 3), todos probados y funcionando.)

Última actualización anterior: 25 de agosto de 2026 — duodécima entrada del día (**otro bug encontrado y corregido**, al confirmar en el entorno real que el panel ya cargaba tras el fix de `$view` de la entrada anterior: el usuario reportó que el widget "Alertas operativas" se veía correcto en contenido, pero sus 3 secciones (Lotes por vencer / Facturas vencidas / Camas ocupadas) salían **apiladas verticalmente en una sola columna**, en vez del layout de 3 columnas lado a lado con el que se había diseñado. **Causa, distinta a la del bug anterior y preexistente en el proyecto, no introducida en esta sesión**: `resources/css/filament/admin/theme.css` (el tema del panel, sección 8.2) le dice a Tailwind qué carpetas escanear para generar solo las clases CSS realmente usadas (`@source`) — pero solo tenía `@source '../../../../app/Filament';`, **nunca** `resources/views/filament`, la carpeta donde viven vistas Blade personalizadas de widgets. Hasta ahora nunca se había notado porque ningún widget anterior (`IndicadoresGerencialesWidget`, los 2 `ChartWidget`) usaba una vista Blade propia con clases sueltas de Tailwind (`grid`, `grid-cols-1`, `lg:grid-cols-3`, etc.) — todos se apoyaban en componentes ya estilizados de Filament. `AlertasOperativasWidget` (Sesión 3) es el primer widget con vista Blade propia del proyecto, y sus clases de grid nunca llegaron al CSS compilado por no estar esa carpeta en el `@source`, quedando sin ningún efecto visual. **Corregido** agregando `@source '../../../../resources/views/filament';` al `theme.css` — una sola línea, no se tocó el widget ni su vista. **Aún sin confirmar por el usuario en el entorno real** — corregido sin acceso a `npm run build`/Vite, pendiente que el usuario aplique el patch y recompile los assets del tema.)

Última actualización anterior: 25 de agosto de 2026 — undécima entrada del día (**bug encontrado y corregido** al aplicar el patch de la Sesión 3 en el entorno real: `sail artisan optimize:clear` tiraba `Cannot redeclare non static Filament\Widgets\Widget::$view as static App\Filament\Widgets\AlertasOperativasWidget::$view`, un error fatal de PHP que rompía **todo el panel**, no solo el widget nuevo — el panel completo no cargaba porque Filament descubre todos los widgets al arrancar. **Causa**: se escribió `AlertasOperativasWidget` copiando el patrón de la propia clase base `Widget` de Filament documentado en varios ejemplos de la comunidad (`protected static string $view`), pero esos ejemplos corresponden a **Filament 3.x**, donde `$view` sí es estática — en la versión que usa este proyecto (**Filament 5.7.6**), esa misma propiedad en `Filament\Widgets\Widget` es `protected string $view;`, **sin `static`**. PHP no permite que una clase hija redeclare una propiedad no estática del padre como estática (ni al revés) — de ahí el fatal error. **Corregido**: se cambió a `protected string $view = '...';` (no estática) en `AlertasOperativasWidget.php`, igual que la clase base real de la versión instalada. Se verificó el código fuente exacto de `Filament\Widgets\Widget` en la v5.7.6 (clonando el repo `filamentphp/filament` en el tag correspondiente) antes de dar el fix por bueno, en vez de confiar de nuevo en documentación/ejemplos de otra versión. **Aún sin confirmar por el usuario en el entorno real** — corregido y validado con `php -l`, pendiente que el usuario aplique el patch nuevo y confirme que el panel vuelve a cargar.)

Última actualización anterior: 25 de agosto de 2026 — décima entrada del día (se construyó la **Sesión 3** del Dashboard gerencial, la última de las 3 planificadas en la sección 6.6: `app/Filament/Widgets/AlertasOperativasWidget.php` (widget custom con vista propia, no `StatsOverviewWidget`/`ChartWidget`) con 3 alertas — lotes de inventario por vencer, facturas vencidas sin cobrar, y camas ocupadas hace demasiado tiempo — cada una con contador, hasta 5 ítems concretos y link al listado completo. Los 2 umbrales de negocio que quedaban pendientes se le preguntaron directo al usuario, que pidió que se decidiera un valor razonable en su lugar: quedaron en **14 días** para cama ocupada "hace mucho" y **30 días** para factura vencida (el umbral de lotes, 90 días, ya estaba confirmado igual al default existente de `LoteInventario::porVencer()`). Ubicado en `$sort = 3`, entre los 2 gráficos de la Sesión 2 y `CitasDeHoyWidget` (que se corrió de `$sort = 3` a `4`). No se creó ninguna tabla/migración nueva, solo queries sobre `LoteInventario`, `Factura` y `Cama`/`Internamiento` ya existentes. **Con esto, el Dashboard gerencial completo queda construido** — ver la sección 6.6.3 nueva más abajo para el detalle completo. **Aún sin confirmar por el usuario en el entorno real** — escrito sin acceso directo a Sail, se validó sintaxis instalando `php-cli` en el entorno de trabajo y corriendo `php -l`.)

Última actualización anterior: 25 de agosto de 2026 — novena entrada del día (**bug encontrado y corregido**, y **confirmado funcionando por el usuario en el entorno real**, al probar el ajuste de formato `$` de la entrada anterior: el gráfico "Por área" quedaba completamente en blanco al tener seleccionada la métrica "Cantidad de citas" — solo funcionaba con "Monto facturado". Causa: `getOptions()` en `FacturacionPorAreaChartWidget` devolvía tipos distintos según la métrica — un array vacío `[]` para citas, un objeto `RawJs` para facturación — y ese cambio de *tipo* entre una y otra rompía el JS del gráfico al recambiar el selector vía Livewire. **Corregido**: ahora `getOptions()` siempre devuelve `RawJs` con la misma estructura, y la decisión de anteponer `$` o no se resuelve *adentro* del JS interpolando un booleano de PHP (`{$esFacturacion} ? '$' : ''`), en vez de cambiar la forma de lo que se devuelve. Con esto, la **Sesión 2 completa del Dashboard gerencial queda confirmada de punta a punta en el entorno real**.)

Última actualización anterior: 25 de agosto de 2026 — octava entrada del día (la Sesión 2 del Dashboard gerencial (entrada anterior) se probó en el entorno real con datos de prueba — **confirmado funcionando**, incluidos los 2 selectores y la limitación documentada de facturas sin cita (se verificó con una factura real sin `cita_id`, que efectivamente queda fuera del gráfico "Por área" tal como estaba previsto). A pedido del usuario, se agregó formato `$` + separador de miles al eje Y y al tooltip de ambos `ChartWidget` — antes mostraban el número pelado (ej. "1200" en vez de "$1,200"). En `FacturacionPorAreaChartWidget` el formato solo aplica cuando la métrica activa es "Monto facturado" (con "Cantidad de citas" se deja el formato numérico simple por defecto); en `IngresosPorMesChartWidget` aplica siempre, ya que ambas series son montos. Se usó `getOptions()` con `RawJs` (callback de Chart.js), mismo mecanismo documentado oficialmente por Filament para personalizar formato de ejes/tooltips. **Aún sin confirmar este ajuste puntual por el usuario en el entorno real** — el resto de la Sesión 2 sí está confirmado.)

Última actualización anterior: 25 de agosto de 2026 — séptima entrada del día (se construyó la **Sesión 2** del Dashboard gerencial: 2 `ChartWidget` nuevos — `app/Filament/Widgets/IngresosPorMesChartWidget.php` (barras, Facturado vs. Cobrado por mes, con selector de rango: 6/12 meses, año actual, año anterior) y `app/Filament/Widgets/FacturacionPorAreaChartWidget.php` (barras, citas o facturación por área, con selector de métrica) — ubicados entre `IndicadoresGerencialesWidget` y `CitasDeHoyWidget` (que se corrió de `$sort = 1` a `3`). Las 2 decisiones que estaban pendientes (sección 6.6) se le preguntaron directo al usuario: el gráfico por área mide **ambos con selector**, y el rango de fechas es **seleccionable** — aplicado en el gráfico de ingresos; el gráfico por área quedó con alcance fijo al año en curso, documentado como supuesto editable (ver sección 6.6.2 nueva más abajo). No se creó ninguna tabla/migración nueva, solo queries sobre `Factura`, `Cita` y `Area` ya existentes. **Aún sin confirmar por el usuario en el entorno real** — escrito sin acceso directo a Sail, se validó sintaxis instalando `php-cli` en el entorno de trabajo y corriendo `php -l`.)

Última actualización anterior: 25 de agosto de 2026 — sexta entrada del día (se construyó la **Sesión 1** del Dashboard gerencial planificado en la entrada anterior: `app/Filament/Widgets/IndicadoresGerencialesWidget.php`, un `StatsOverviewWidget` visible solo para `admin` con los 4 indicadores clave — ingresos del mes con comparación % vs. mes anterior, por cobrar, citas atendidas hoy/semana, y ocupación de camas en tiempo real — ubicado arriba de `CitasDeHoyWidget` en el Dashboard. No se creó ninguna tabla/migración nueva, solo queries sobre `Factura`, `Cita` y `Cama`/`Internamiento` ya existentes. Ver la sección 6.6.1 nueva más abajo para el detalle completo. **Aún sin confirmar por el usuario en el entorno real** — escrito sin acceso a PHP/Sail, se validó sintaxis con `php -l`.)

Última actualización anterior: 25 de agosto de 2026 — quinta entrada del día (el usuario pidió dividir la construcción del **Dashboard gerencial** (propuesta de la sección 6.6, cuarta entrada de hoy) en **3 sesiones separadas**, porque el módulo completo es demasiado para una sola sesión. Se agregó el plan de división dentro de la misma sección 6.6 — Sesión 1: indicadores clave (`StatsOverviewWidget`); Sesión 2: los 2 gráficos (ingresos por mes, por área); Sesión 3: alertas operativas. **Solo planificación, no se generó ningún código** — a pedido explícito del usuario.)

Última actualización anterior: 25 de agosto de 2026 — cuarta entrada del día (el usuario preguntó cómo puede el administrador saber si la clínica "está ganando o no", dado que el Dashboard no tiene ningún indicador financiero ni gráfico — solo la tabla de Citas de hoy. Se confirmó el problema revisando el Dashboard actual y se agregó, **solo como documentación** a pedido explícito del usuario, una propuesta de **Dashboard gerencial** para el rol admin: indicadores clave de ingresos/por cobrar/ocupación, gráfico de ingresos por mes, gráfico por área, y alertas operativas (inventario por vencer, facturas vencidas). No se tocó ningún código. Ver la sección 6.6 nueva más abajo para el detalle completo.)

Última actualización anterior: 25 de agosto de 2026 — tercera entrada del día (el usuario, revisando el formulario de **Movimientos de Inventario** (sección 6.3), preguntó por qué en un traslado entre áreas seguían apareciendo los campos "Paciente" y "Cita relacionada" — no tenía sentido, esos campos son para dejar constancia de consumo real en la atención de un paciente, no de un traslado de stock. Se corrigió agregándoles `->visible()` condicionado a que `tipo_movimiento` sea "Salida", mismo patrón reactivo que ya usaban `area_origen`/`area_destino`. De paso se confirmó que la ausencia del campo "Usuario" en el formulario es intencional — se completa solo con el usuario logueado y queda visible después en la columna "Registrado por" del listado, no es un bug. Ver la sección 6.3 actualizada más abajo. **Confirmado funcionando por el usuario en el entorno real.**)

Última actualización anterior: 25 de agosto de 2026 — segunda entrada del día (a pedido del usuario, se organizó el menú lateral del panel en grupos de navegación (`$navigationGroup`), ya que solo el módulo de Infraestructura tenía grupo propio y los otros 10 Resources aparecían todos sueltos y mezclados. Ver el detalle completo en la sección 8.4 (nueva) más abajo — no se tocaron íconos ni ningún otro comportamiento, solo la agrupación del menú.)

Última actualización anterior: 25 de agosto de 2026 (se sincronizó el repo con `git pull` (traía el patch de la sección 6.2 aplicado en la sesión anterior) y se reemplazó el logo provisorio dibujado a mano por el **logo real de la clínica**, que el usuario compartió (imagen suelta + embebido en un PDF de servicios). Ver la nueva entrada "Logo real recibido y aplicado (25 ago 2026)" dentro de la sección 8.1 para el detalle completo. De paso, y en una conversación aparte sobre cómo mostrar el sistema en la entrevista sin llevar la laptop pesada, se agregó `URL::forceScheme('https')` condicional en `AppServiceProvider.php` — soluciona un problema de "mixed content" (CSS/JS bloqueados) al exponer el sistema local por un túnel HTTPS (Cloudflare Tunnel); no afecta nada en local porque solo se activa si `APP_URL` empieza con `https://`.)

Última actualización anterior: 24 de agosto de 2026 — decimocuarta entrada del día (a pedido del usuario, se cambió otra vez el criterio del nombre de archivo del adjunto de Orden de Estudio — de "ULID + nombre original subido" (decimotercera entrada) a **"ULID + nombre del paciente + tipo de estudio"** (ej. `01jz3k9x...-paul-guerrero-laboratorio.pdf`), para que el nombre en el disco identifique a quién pertenece el resultado en vez de depender del nombre que traía el archivo del navegador. Usa `Get $get` dentro de `getUploadedFileNameForStorageUsing()` para leer `paciente_id`/`tipo` del propio formulario en el momento del upload (mismo patrón que ya usaba `UserForm.php` para mostrar/ocultar el campo `medico_id` según el rol) — si el usuario sube el archivo antes de seleccionar paciente/tipo, cae a un texto genérico en vez de fallar. **Confirmado funcionando por el usuario en el entorno real.**)

Última actualización anterior: 24 de agosto de 2026 — decimotercera entrada del día (dos bugs más encontrados y corregidos al probar **Órdenes de Estudio** en el entorno real: (1) el adjunto (`resultado_archivo`) no tenía botones de ver/descargar — se agregó `->openable()`/`->downloadable()` — y no tenía restricción de tipo de archivo — se limitó a PDF/JPG/PNG/WEBP; (2) al usarlos, el botón "ver" daba `ERR_CONNECTION_REFUSED` en `localhost:8000` — causa real: el `.env` del usuario tenía `APP_URL=http://localhost:8000` pero Sail expone la app en el puerto 80 (`http://localhost`, sin puerto) según `compose.yaml` — se corrigió cambiando `APP_URL` en su `.env` local, no fue necesario tocar código. **Ambos confirmados funcionando por el usuario en el entorno real.** Además, a pedido del usuario, se cambió el nombre generado para el archivo guardado: en vez del nombre aleatorio de 26 caracteres por defecto de Filament (ilegible), ahora usa un ULID corto + el nombre original slugificado + extensión (`->getUploadedFileNameForStorageUsing()`), para que sea identificable sin arriesgar colisión entre archivos con el mismo nombre.)

Última actualización anterior: 24 de agosto de 2026 — duodécima entrada del día (fix de bug reportado por el usuario al probar 6.2 en el entorno real por primera vez: al crear una Cirugía con al menos un "médico adicional", Filament tiraba `SQLSTATE[HY000]: Field 'nombres' doesn't have a default value` al insertar en `medicos`. Causa: el `Repeater` de médicos adicionales usaba `->relationship()` sobre `Cirugia::medicosAdicionales()`, que es un `belongsToMany` — Filament interpreta eso como "crear un registro NUEVO en la tabla relacionada por cada fila", no como "asociar un médico ya existente + guardar su rol en el pivote `cirugia_medico`". Se quitó `->relationship()` del Repeater (`CirugiaForm.php`) y se reemplazó por sync manual del pivote: `CreateCirugia::afterCreate()` y `EditCirugia::mutateFormDataBeforeFill()` + `afterSave()`. **Confirmado funcionando por el usuario en el entorno real.** De paso, el usuario detectó que los clics repetidos al botón "Crear cirugía" (mientras la pantalla no daba feedback por el error de arriba) sí habían alcanzado a guardar la cirugía antes de fallar en el repeater, dejando 4 registros duplicados — se le indicó borrarlos a mano desde el listado, no fue necesario tocar código para eso. Este bug solo afectaba a Cirugía (único Resource con un Repeater sobre relación `belongsToMany` con pivote); el resto del módulo 6.2 no usa este patrón.)

Última actualización anterior: 24 de agosto de 2026 — undécima entrada del día (la sección **6.2** — infraestructura física: camas/internamiento, quirófanos/cirugías, procedimientos/estudios, emergencias, ambulancia — pasó de propuesta documentada a **módulo construido**, mismo criterio que se usó con 6.3: el usuario pidió avanzar con **supuestos razonables** sobre las 5 decisiones que seguían sin confirmar con la clínica, documentándolos para poder ajustarlos después. Se crearon 7 tablas nuevas (`camas`, `quirofanos`, `internamientos`, `cirugias`, `cirugia_medico`, `ordenes_estudio`, `servicios_ambulancia`) + 2 columnas nuevas en `citas` (`origen`/`prioridad`), 6 modelos nuevos, 6 Resources completos de Filament, y se tocó `CitaForm`/`CitasTable` para agregar los campos de emergencia. Ver la sección 6.2 actualizada abajo para el detalle completo. **Aún sin confirmar por el usuario en el entorno real** — escrito sin acceso a PHP/Sail (se validó sintaxis con `php -l`, pero no se corrió la migración ni se probó en `/admin`).)

Última actualización anterior: 24 de agosto de 2026 — décima entrada del día (se le explicó al usuario, en un documento Word entregado aparte (no versionado en el repo), qué es cada módulo del sistema, sus opciones y cómo se relacionan entre sí. De esa conversación salió una pregunta pendiente nueva, agregada a la sección 6: si los 3 roles actuales alcanzan o falta un rol de farmacia, dado que hoy el médico no tiene acceso al módulo de inventario aunque es quien aplica los insumos. Solo documentación — no se tocó código.)

Última actualización anterior: 24 de agosto de 2026 — novena entrada del día (el módulo de **Medicamentos e Insumos** (sección 6.3), construido en la entrada anterior, quedó **confirmado funcionando por el usuario en el entorno real**: catálogo, lotes con vencimiento, movimientos de entrada/salida con stock recalculado en vivo, protección contra borrado y permisos por rol, todo probado en vivo sin problemas.)

Última actualización anterior: 24 de agosto de 2026 — octava entrada del día (primer módulo **construido** con código, no solo documentado: **Medicamentos e Insumos**, sección 6.3. El usuario decidió empezar por este módulo en vez de la infraestructura de la 6.2, y pidió avanzar con **supuestos razonables** sobre las decisiones aún sin confirmar con la clínica, documentándolos para poder ajustarlos después en vez de esperar esa confirmación. Se crearon 3 tablas (`items_inventario`, `lotes_inventario`, `movimientos_inventario`), 3 modelos y 3 Resources completos de Filament — ver la sección 6.3 actualizada abajo para el detalle completo de qué se construyó y qué supuesto se aplicó en cada decisión pendiente. **Aún sin confirmar por el usuario en el entorno real** — no se ha corrido la migración todavía.)

Última actualización anterior: 24 de agosto de 2026 — séptima entrada del día (a pedido del usuario, se investigó en internet cómo resuelven este mismo problema otros sistemas hospitalarios/clínicos y estándares del sector, para validar las propuestas de planificación de las secciones 6.2 y 6.3 y detectar huecos antes de construir nada. Resultado: ambas propuestas están bien encaminadas y no se rediseñan, pero se les agregan **3 ajustes concretos** encontrados en la investigación — ver el detalle marcado "(validado con investigación externa, 24 ago 2026)" dentro de 6.2 y 6.3 — y se agrega una sección nueva, **6.4**, con el marco legal ecuatoriano aplicable (protección de datos de salud y normativa del MSP sobre historia clínica electrónica), que no estaba documentado. Explícitamente **solo documentación e investigación, sin tocar código**.)

Última actualización anterior: 24 de agosto de 2026 — sexta entrada del día (a pedido del usuario, se agregó la sección **6.3**: una propuesta de planificación — sin tocar código, mismo estilo que la 6.2 — de cómo modelar a futuro el módulo de **medicamentos e insumos** mencionado por el contacto interno en la sección 6.1, que debe vivir en farmacia, quirófano, admisión y facturación. A diferencia de la infraestructura física de la 6.2, este módulo no tenía ninguna propuesta previa — es dominio completamente nuevo, sin nada construido hoy. Explícitamente **solo documentación**, para planificar fase 2/3.)

Última actualización anterior: 24 de agosto de 2026 — quinta entrada del día (a pedido del usuario, se agregó la sección **6.2**: una propuesta de planificación — sin tocar código — de cómo modelar a futuro la infraestructura del PDF de la sección 6.1 (UCI, quirófanos, hospitalización, laboratorio, etc.), agrupada en 5 conceptos posibles (camas/internamiento, quirófanos/cirugías, procedimientos/estudios, emergencias, ambulancia), más las decisiones que faltan confirmar con la clínica antes de construir cualquiera de ellos. Explícitamente **solo documentación**, el usuario aclaró que es para planificar la fase 2/3, no para construir ahora.)

Última actualización anterior: 24 de agosto de 2026 — cuarta entrada del día (se creó `database/seeders/AreaSeeder.php` con las 27 especialidades reales de la clínica, la respuesta del contacto interno documentada en la entrada anterior — usa `firstOrCreate` para no duplicar si ya había áreas de prueba cargadas. Registrado en `DatabaseSeeder.php` para correr con `db:seed`, o solo con `--class=AreaSeeder`. No se cargaron los "servicios/infraestructura" del PDF (UCI, quirófanos, etc.) como áreas — no son especialidades médicas en el sentido del modelo actual, quedan solo como contexto documentado. **Confirmado funcionando por el usuario en el entorno real.** Ver sección 6.1 y 9 para el detalle.)

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
      ItemInventario.php       # Con $fillable y métodos stockActual()/bajoStockMinimo() (sección 6.3)
      LoteInventario.php       # Con $fillable, relación item, y stockActual()/vencido()/porVencer() (sección 6.3)
      MovimientoInventario.php # Con $fillable y relaciones lote/usuario/paciente/cita (sección 6.3)
      Cama.php                 # Con $fillable, relación internamientos, y ocupada() derivado (sección 6.2)
      Quirofano.php            # Con $fillable y relación cirugias (sección 6.2)
      Internamiento.php        # Con $fillable y relaciones paciente/cama/medico/cita (sección 6.2)
      Cirugia.php              # Con $fillable, relaciones + medicosAdicionales (BelongsToMany con pivot) (sección 6.2)
      OrdenEstudio.php         # Con $fillable y relaciones paciente/medicoSolicitante/cita (sección 6.2)
      ServicioAmbulancia.php   # Con $fillable y relación paciente (sección 6.2)
    Http/
      Controllers/
        FacturaPdfController.php  # Genera el PDF de una factura (dompdf), reutiliza permisos de FacturaResource
    Filament/
      Resources/
        Areas/                   # Resource completo (Form, Table, Pages)
        Pacientes/               # Resource completo
        Medicos/                 # Resource completo, selector de Área por relación
        Citas/                   # Resource completo, selectores por relación + estado con colores + origen/prioridad (sección 6.2)
        HistoriaClinicas/        # Resource completo + vista de solo lectura (Infolist)
        Facturas/                # Resource completo, selectores por relación + estado con colores, exportar a PDF
        Users/                   # Resource completo, solo accesible por admin (gestión de usuarios/roles)
        ItemsInventario/         # Resource completo — catálogo de medicamentos/insumos (sección 6.3)
        LotesInventario/         # Resource completo — lotes con vencimiento, FEFO (sección 6.3)
        MovimientosInventario/   # Resource completo — entradas/salidas/traslados/ajustes (sección 6.3)
        Camas/                   # Resource completo — catálogo de camas, estado derivado (sección 6.2)
        Quirofanos/              # Resource completo — catálogo de quirófanos, estado editable (sección 6.2)
        Internamientos/          # Resource completo — ingreso/alta, filtrado "mis pacientes" para médico (sección 6.2)
        Cirugias/                # Resource completo — agenda de quirófano, médicos adicionales con Repeater (sección 6.2)
        OrdenesEstudio/          # Resource completo — laboratorio/imagenología, adjunto opcional (sección 6.2)
        ServiciosAmbulancia/     # Resource completo — traslados, el más simple del módulo (sección 6.2)
      Widgets/
        CitasDeHoyWidget.php               # Tabla de citas del día en el Dashboard (sección 8, punto 1)
        IndicadoresGerencialesWidget.php   # Stats: ingresos del mes, por cobrar, citas atendidas, ocupación de camas — solo admin (sección 6.6.1)
        IngresosPorMesChartWidget.php      # Gráfico de barras: facturado vs. cobrado por mes, rango seleccionable — solo admin (sección 6.6.2)
        FacturacionPorAreaChartWidget.php  # Gráfico de barras: citas o facturación por área, métrica seleccionable — solo admin (sección 6.6.2)
        AlertasOperativasWidget.php        # Lotes por vencer, facturas vencidas, camas ocupadas hace mucho — solo admin (sección 6.6.3)
  database/
    migrations/
      ..._create_areas_table.php            # Completa (nombre)
      ..._create_pacientes_table.php        # Completa (datos personales + cedula unique)
      ..._create_medicos_table.php          # Completa (FK area_id)
      ..._create_citas_table.php            # Completa (FKs paciente/medico/area, horario, estado)
      ..._create_historia_clinicas_table.php # Completa (FKs paciente/medico/cita nullable)
      ..._create_facturas_table.php         # Completa (FKs paciente/cita nullable, monto, pago)
      ..._create_items_inventario_table.php       # Completa — catálogo medicamentos/insumos (sección 6.3)
      ..._create_lotes_inventario_table.php       # Completa — lotes con vencimiento (sección 6.3)
      ..._create_movimientos_inventario_table.php # Completa — ledger de movimientos (sección 6.3)
      ..._create_camas_table.php                  # Completa — camas hospitalización/UCI/UCIN (sección 6.2)
      ..._create_quirofanos_table.php              # Completa — quirófanos con estado editable (sección 6.2)
      ..._create_internamientos_table.php          # Completa — FKs paciente/cama/medico/cita, origen/prioridad (sección 6.2)
      ..._create_cirugias_table.php                # Completa — FKs paciente/quirofano/medico_principal/cita (sección 6.2)
      ..._create_cirugia_medico_table.php          # Completa — pivote médicos adicionales de una cirugía (sección 6.2)
      ..._create_ordenes_estudio_table.php         # Completa — laboratorio/imagenología/etc, adjunto opcional (sección 6.2)
      ..._create_servicios_ambulancia_table.php    # Completa — traslados (sección 6.2)
      ..._add_origen_prioridad_to_citas_table.php  # Alter — cubre "Emergencias" sin tabla propia (sección 6.2)
  resources/
    views/
      pdf/
        factura.blade.php       # Plantilla del comprobante de factura (CSS simple, para dompdf)
      filament/
        widgets/
          alertas-operativas-widget.blade.php  # Vista de AlertasOperativasWidget (sección 6.6.3), 3 columnas con badges/links
  public/
    images/
      logo.png                   # Logo real (vertical, ícono + texto), navy — de la marca oficial, extraído de PDF del cliente
      logo-white.png              # Misma versión, en blanco (para fondos oscuros)
      logo-horizontal.png         # Recomposición horizontal (ícono izq. + texto der.) navy — usada en el header del panel (2.5rem de alto)
      logo-horizontal-white.png   # Misma versión horizontal, en blanco
      _legacy/                    # Logo placeholder (SVG dibujado a mano) anterior a tener el logo real; se conserva por si acaso
    favicon.ico                 # Favicon regenerado desde el monograma real del logo
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
- [ ] **¿Los 3 roles actuales (admin/recepción/médico) alcanzan, o falta un 4º rol de farmacia?** Surgió al explicarle el sistema al usuario (24 ago 2026): hoy el rol **médico no tiene acceso a Ítems/Lotes/Movimientos de Inventario** (ni siquiera para ver), aunque es quien físicamente aplica el medicamento/insumo en la consulta — el registro de ese consumo depende de que recepción lo cargue por su cuenta. Es una decisión de negocio, no un bug: hay que decidir (a) si el médico debería poder registrar sus propios movimientos de inventario (ej. solo lo usado en su propia cita), y (b) si más adelante, cuando farmacia empiece a operar como un puesto dedicado (ver sección 6.3, punto sobre si "farmacia" es una entidad propia), conviene un rol **farmacéutico/bodega** separado con acceso a inventario pero sin ver historias clínicas ni facturación. Por ahora, con el volumen actual, 3 roles sigue pareciendo suficiente — no se ha tomado ninguna decisión ni se tocó código.
  **Parcialmente respondido (24 ago 2026, por WhatsApp con el contacto interno, fuera del entorno de código):** la clínica **sí tiene personal dedicado en farmacia** — esto respalda que probablemente conviene el rol `farmacéutico`/`bodega` separado. **Sigue sin confirmarse el mecanismo real**: no se sabe si el médico registra el consumo del insumo en el momento, si avisa después a farmacia, o si farmacia ya prepara el insumo antes de la consulta — sin esa respuesta no se puede diseñar bien el flujo prescripción → despacho → descuento de stock (`movimientos_inventario`, sección 6.3). Pregunta pendiente para la entrevista formal del 25 ago con Ysrael Calle (contabilidad, conoce el flujo operativo). Ver sección 6.5 para el detalle completo y el resto de preguntas preparadas para esa entrevista.

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

### 6.2 Infraestructura física — MÓDULO CONSTRUIDO (24 ago 2026)

**Estado**: **construido y en pruebas en el entorno real.** El usuario decidió avanzar con **supuestos razonables** sobre las 5 decisiones que seguían sin confirmar con la clínica (mismo criterio que se usó en 6.3), documentándolos abajo para poder ajustarlos después. Al probar **Cirugías** por primera vez apareció un bug (Repeater de médicos adicionales insertaba médicos vacíos en vez de asociar existentes — ver duodécima entrada del día arriba) — **ya corregido y confirmado funcionando**. Al probar **Órdenes de Estudio** aparecieron 2 bugs más en el adjunto (sin botones de ver/descargar, y `APP_URL` mal configurado en el entorno del usuario) — **ambos corregidos y confirmados funcionando**, ver decimotercera entrada del día arriba. El resto del módulo (Camas, Quirófanos, Internamientos, Servicios de Ambulancia) todavía no se ha probado uno por uno en el entorno real.

**Por qué no entra en el modelo actual de `areas`**: un `Area` hoy representa una especialidad médica (algo a lo que un `Medico` pertenece). Hospitalización, UCI, quirófanos, etc. no son especialidades — son **capacidad física/operativa** (camas, salas, equipos) que se ocupa y se libera con el tiempo. Necesitan su propio concepto de "ocupado/libre" y de "quién está usando qué, desde cuándo hasta cuándo", que `Area` no tiene ni debería tener.

**Estructura construida** (7 tablas nuevas + 2 columnas en `citas`, migraciones `2026_08_24_130000` a `2026_08_24_130007`):

1. **Camas — `camas`** (`app/Models/Cama.php`): `numero` (único), `tipo` (hospitalizacion/uci/ucin), `piso` (nullable). **No tiene columna `estado`** — `ocupada()` la deriva en vivo de si existe un `Internamiento` sin `fecha_alta`, mismo criterio que el stock del módulo de inventario (sección 6.3), para que nunca se desincronice de la realidad.

2. **Internamientos — `internamientos`** (`app/Models/Internamiento.php`): `paciente_id`, `cama_id`, `medico_id` responsable, `cita_id` (nullable), `fecha_ingreso`, `fecha_alta` (nullable — nulo mientras sigue internado), `motivo`, `origen` (programado/emergencia), `prioridad` (nullable, escala ESI), `notas`.

3. **Quirófanos — `quirofanos`** (`app/Models/Quirofano.php`): `numero` (único), `nombre` (nullable), `estado` — a diferencia de `camas`, este sí es una columna guardada y editable, con 4 pasos (preparación → en cirugía → limpieza → libre) en vez de solo libre/ocupado, tal como se validó con la investigación externa de la entrada anterior.

4. **Cirugías — `cirugias`** (`app/Models/Cirugia.php`): `paciente_id`, `quirofano_id`, `medico_principal_id` (cirujano responsable), `cita_id` (nullable), `fecha`, `hora_inicio`, `hora_fin` (nullable), `tipo_cirugia`, `estado` (programada/en_curso/completada/cancelada), `notas`. Médicos adicionales (anestesiólogo, ayudantes) se modelan en una tabla pivote aparte — ver siguiente punto.

5. **Médicos adicionales de una cirugía — `cirugia_medico`** (pivote, gestionado con un `Repeater` en el formulario de Cirugía, sin Resource propio): `cirugia_id`, `medico_id`, `rol` (texto libre, ej. "Anestesiólogo"). Resuelve el punto abierto de la propuesta original: una cirugía suele involucrar más de un médico, a diferencia de `Cita` que asume uno solo.

6. **Órdenes de estudio — `ordenes_estudio`** (`app/Models/OrdenEstudio.php`): modelo unificado para Laboratorio, Rayos X, Ecografía, Centro de Imagen, Endoscopía (alta/baja), Gastroenterología y Procedimientos Ambulatorios, en vez de una tabla por tipo. Campos: `paciente_id`, `medico_solicitante_id`, `cita_id` (nullable), `tipo`, `fecha_solicitud`, `fecha_realizacion` (nullable), `estado` (solicitado/en_proceso/completado), `resultado_texto`, `resultado_archivo` (path, nullable), `notas`.

7. **Servicios de ambulancia — `servicios_ambulancia`** (`app/Models/ServicioAmbulancia.php`): la tabla más simple del módulo — `paciente_id` (nullable), `origen`, `destino`, `fecha_hora`, `motivo`, `notas`.

8. **`citas.origen` / `citas.prioridad`** (columnas nuevas en la tabla ya existente, no una tabla nueva): cubren "Emergencias" — una emergencia que no requiere internamiento queda registrada como una Cita normal con `origen = emergencia` y su `prioridad` ESI, en vez de necesitar un modelo propio. Se agregaron también a `CitaForm`/`CitasTable` (selector condicional de prioridad solo visible si origen es emergencia, badge rojo en la tabla, filtro rápido "Emergencias").

**6 Resources completos de Filament** (mismo patrón de carpetas que los módulos existentes), todos agrupados bajo el grupo de navegación "Infraestructura": `Camas`, `Quirofanos`, `Internamientos`, `Cirugias`, `OrdenesEstudio`, `ServiciosAmbulancia`.

**Permisos aplicados**:
- Camas, Quirófanos (catálogo de recurso físico, mismo criterio que Áreas/Médicos): cualquier usuario logueado ve; admin y recepción editan (recepción necesita asignar camas/quirófanos en el día a día); solo admin crea/borra.
- Internamientos, Cirugías (operación clínica, mismo criterio que Citas): admin y recepción crean/administran; cualquier usuario logueado edita, pero un médico vinculado (`users.medico_id`) solo ve/edita los suyos (filtrado por `medico_id`/`medico_principal_id`, igual patrón que `CitaResource::getEloquentQuery()`); solo admin borra.
- Órdenes de estudio: igual que arriba, pero el médico **también puede crear** (es quien solicita el estudio), filtrado por `medico_solicitante_id`.
- Servicios de ambulancia: admin y recepción crean/editan/borran (admin); médico solo ve (no participa directamente del transporte).

**Las 5 decisiones que estaban pendientes — resueltas con supuestos razonables, editables después**:

| Decisión pendiente | Supuesto aplicado | Qué implica si la clínica responde distinto |
|---|---|---|
| ¿Estado de camas/quirófanos en tiempo real o histórico alcanza? | **Tiempo real** — camas derivado en vivo (`ocupada()`), quirófanos con columna editable de estado granular. | Ya cubierto en ambos escenarios, igual criterio que el stock de 6.3. |
| ¿Una cirugía siempre nace de una `Cita` ya agendada? | **No** — `cita_id` nullable, una cirugía puede agendarse directo, igual que `Factura`/`HistoriaClinica` ya tratan `cita_id` como opcional. | Si la clínica confirma que siempre pasa por Citas, se podría hacer `cita_id` obligatorio más adelante sin romper lo ya guardado (solo agregaría una validación). |
| ¿Resultados de estudios con archivo adjunto desde el día uno? | **Sí** — `FileUpload` opcional en `OrdenEstudioForm`, disco local de Sail (`disk('public')`), además del texto libre. No se evaluó storage externo tipo S3. | Migrar a S3 más adelante es un cambio de configuración del disco, no de la columna (`resultado_archivo` solo guarda la ruta). |
| ¿"Emergencias" necesita triage/prioridad, no solo `origen`? | **Sí** — se implementó `prioridad` con escala ESI (5 niveles) en `citas` e `internamientos`, además de `origen`, tal como se validó con la investigación externa de la entrada anterior. | N/A, ya resuelta con la investigación previa. |
| ¿Más de un quirófano/UCI si la clínica crece a otra sede? | **No se modela sede/sucursal todavía** — el sistema sigue asumiendo una sola ubicación (igual que el resto del proyecto, ver sección 5 sobre timezone). | Si la clínica confirma expansión, hace falta agregar una tabla `sedes` y `sede_id` en `camas`/`quirofanos` (y probablemente en `areas`) — cambio más grande, no solo de este módulo. |

**Qué NO se hizo todavía**:
- No se corrió la migración ni se probó en el entorno real (a diferencia de 6.3, que sí se confirmó en vivo) — falta que el usuario corra `sail artisan migrate` y pruebe el flujo completo.
- No se cargó ningún dato real (camas/quirófanos existentes de la clínica) — no hay seeder, la clínica no ha dado esa información.
- No se conectó con el módulo de inventario (sección 6.3) — la `MovimientoInventario` sigue sin `cirugia_id`, tal como quedó documentado como pendiente en 6.3 punto 6 de la tabla de decisiones. Se puede agregar sin romper nada de lo actual.
- No se validó a nivel de formulario que una cama/quirófano esté libre antes de asignarla a un internamiento/cirugía nuevo — el formulario solo muestra un mensaje de ayuda pidiendo verificar en el listado correspondiente. Si la clínica confirma que hace falta bloquear la selección de recursos ocupados, es un ajuste puntual en `InternamientoForm`/`CirugiaForm`.
- No se filtra por médicos adicionales de una cirugía (`medicosAdicionales`) al mostrar la lista a un médico logueado — solo ve las cirugías donde es el responsable principal, no las que le asignaron como anestesiólogo/ayudante. Simplificación documentada en `CirugiaResource::getEloquentQuery()`.

**Validación externa (24 ago 2026)**: se investigó cómo modelan esto sistemas hospitalarios reales y estándares del sector (HL7 FHIR, sistemas open source como OpenMRS/OpenHospital/Bahmni, literatura de gestión hospitalaria). Conclusión general: la estructura propuesta arriba (separar "ubicación/recurso físico" de "ocupación en el tiempo", que es la base de los puntos 1 y 2) coincide con el patrón estándar de la industria, conocido como **ADT** (Admission-Discharge-Transfer) — el módulo que virtualmente todo sistema hospitalario tiene para esto. No hay que rediseñar nada, solo se incorporaron los 2 ajustes marcados arriba (estado de quirófano más granular, prioridad/triage en emergencias).

### 6.3 Medicamentos e insumos — MÓDULO CONSTRUIDO Y CONFIRMADO (24 ago 2026)

**Estado**: **construido y confirmado funcionando por el usuario en el entorno real** (probado en vivo: creación de ítem, lote, movimientos de entrada y salida con stock recalculado correctamente, protección contra borrado y permisos por rol). El usuario decidió empezar por este módulo (en vez de la infraestructura física de la 6.2) y pidió avanzar con **supuestos razonables** sobre las decisiones que seguían sin confirmar con la clínica, documentándolos para poder ajustarlos después en vez de bloquear el desarrollo esperando esa confirmación.

**Encargo explícito del usuario (24 ago 2026, misma sesión)**: pidió avanzar con una propuesta de modelado para el módulo de medicamentos/insumos que el contacto interno mencionó en la sección 6.1 ("debe vivir en farmacia, quirófano, admisión y facturación"). Igual que la sección 6.2, es **solo para planificar** — no se tocó ningún modelo, migración, Resource ni seeder.

**Punto de partida — a diferencia de la infraestructura de la sección 6.2, acá no hay nada construido ni cargado todavía**: ni una tabla, ni un campo, ni un seeder. `Factura` solo modela `monto`/`estado_pago`, no ítems ni insumos. Es dominio nuevo desde cero.

**Por qué no entra en ningún modelo actual**: lo que describe el contacto interno mezcla dos cosas distintas que conviene separar desde el diseño:
1. Un **catálogo** de qué medicamentos/insumos existen y cuánto stock hay (inventario).
2. Un **movimiento** de esos ítems entre las 4 áreas que mencionó (farmacia, quirófano, admisión, facturación) — entradas, salidas, traslados y consumo real en la atención de un paciente.

**Estructura construida** (3 tablas nuevas, migraciones `2026_08_24_120000` a `2026_08_24_120002`):

1. **Catálogo — `items_inventario`** (`app/Models/ItemInventario.php`): `nombre`, `tipo` (medicamento/insumo), `unidad_medida`, `stock_minimo` (nullable, para alertas), `precio_unitario` (nullable, si se factura). Un solo catálogo para ambos tipos en vez de dos tablas separadas. **No tiene columna `stock_actual`** — se calcula en vivo con `ItemInventario::stockActual()`, sumando el `stockActual()` de todos sus lotes, para que nunca se desincronice de la realidad (mismo criterio que el estado de una cama en 6.2). `bajoStockMinimo()` compara ese valor contra `stock_minimo`.

2. **Lotes — `lotes_inventario`** (`app/Models/LoteInventario.php`): `item_id` FK, `numero_lote`, `fecha_vencimiento`. Trazabilidad FEFO (First-Expired-First-Out) implementada tal como se validó con la investigación externa — cada lote es una unidad separada con su propio vencimiento. Tampoco tiene columna de cantidad: `LoteInventario::stockActual()` la calcula sumando sus movimientos (entrada + ajuste, menos salida). `vencido()` y `porVencer($dias = 90)` para la lógica de color en la tabla del panel.

3. **Movimientos — `movimientos_inventario`** (`app/Models/MovimientoInventario.php`, el ledger/fuente de verdad): `lote_id` FK, `tipo_movimiento` (entrada/salida/traslado/ajuste), `cantidad`, `area_origen`/`area_destino` (nullable, texto de una lista fija), `fecha_hora`, `user_id` (quién lo registró — se asigna solo, no se pide en el formulario), `paciente_id`/`cita_id` (nullable, para dejar registrado consumo real en la atención de alguien), `notas`. Toda entrada de stock —incluida la carga inicial de un lote— se registra como un movimiento de tipo "entrada", nunca como una columna editable a mano.

**3 Resources completos de Filament** (mismo patrón de carpetas que los 7 existentes): `ItemsInventario`, `LotesInventario`, `MovimientosInventario`. Formulario de movimientos con campos "Área de origen"/"Área de destino" condicionales según `tipo_movimiento` (`->live()` + `->visible()`). **Fix (25 ago 2026):** "Paciente"/"Cita relacionada" también son condicionales ahora — solo se muestran cuando `tipo_movimiento` es "Salida" (el único caso que representa consumo real en atención); antes aparecían siempre, incluido en traslados/ajustes, donde no aplican. Mismo patrón `->live()`+`->visible()` que ya usaban `area_origen`/`area_destino`. **Confirmado funcionando por el usuario en el entorno real.** El campo "Usuario" sigue sin estar en el formulario a propósito (se completa solo con `Auth::id()`, ver `CreateMovimientoInventario.php`) — queda visible después en la columna "Registrado por" del listado. Protección contra borrado: un ítem no se borra si tiene lotes, un lote no se borra si tiene movimientos (mismo patrón que Área/Médico/Paciente/Cita).

**Permisos aplicados** (no existe rol "farmacia" separado — el sistema solo tiene admin/recepcion/medico):
- Catálogo (Ítems, Lotes): mismo criterio que Áreas/Médicos — admin todo, recepción solo ver, médico sin acceso.
- Movimientos: mismo criterio que Facturas — admin y recepción todo, médico sin acceso.
- **Queda abierto** si la clínica confirma que necesita un rol propio de farmacia; hoy cualquier persona con rol `recepcion` puede registrar movimientos.

**Las 6 decisiones que estaban pendientes — resueltas con supuestos razonables, editables después** (a diferencia de la 6.2, aquí sí se avanzó a construir sin esperar la confirmación de la clínica, a pedido explícito del usuario):

| Decisión pendiente | Supuesto aplicado | Qué implica si la clínica responde distinto |
|---|---|---|
| ¿Farmacia es área física propia o solo un paso lógico? | Solo un paso lógico — texto libre (`area_origen`/`area_destino`, lista fija: farmacia/quirófano/admisión/facturación/bodega) dentro del movimiento, sin Resource propio. | Si la clínica confirma que necesita responsable/horario propio por área, habría que crear una tabla `areas_inventario` (o reusar el concepto) y convertir esos campos en relaciones. |
| ¿Stock en tiempo real o histórico alcanza? | **Tiempo real** — se implementó calculado en vivo desde los movimientos (`stockActual()`), nunca una columna editable a mano. | Ya cubierto en ambos escenarios: si solo hiciera falta histórico, este diseño lo sigue dando gratis (es más, no menos, de lo mínimo necesario). |
| ¿Se cobra todo insumo consumido al paciente? | **No se conectó con `Factura` todavía** — `paciente_id`/`cita_id` en el movimiento solo deja constancia de qué se usó, no genera cobro automático. | Si la clínica confirma que sí se cobra, falta construir la tabla intermedia `factura_items` que ya proponía la 6.3 original (ver más abajo, en "Qué falta"). |
| ¿Hace falta registrar proveedores/compras externas? | **No** — un movimiento "entrada" solo registra que llegó stock, sin detalle de quién lo vendió. | Se puede agregar una tabla `proveedores` + FK opcional en el movimiento más adelante, sin romper lo ya construido. |
| ¿Trazabilidad por lote/vencimiento? | **Sí** (ya no era pregunta abierta desde la investigación externa) — implementado tal cual, ver `lotes_inventario` arriba. | N/A, ya resuelta. |
| ¿Depende de la infraestructura de 6.2 (quirófanos/cirugías)? | **No, se construyó independiente.** El movimiento solo referencia `cita_id` (ya existe), no `cirugia_id` (esa tabla no existe todavía). | Cuando se construya la 6.2, se puede agregar `cirugia_id` nullable al movimiento sin romper nada de lo actual. |

**Relación con Facturación — todavía no construida**: sigue siendo la propuesta original (tabla intermedia `factura_items`: factura_id, item_id, cantidad, precio_unitario), para no mezclar "qué se usó clínicamente" con "qué se cobró". No se construyó en esta pasada — es lo próximo si la clínica confirma que sí se factura el consumo de insumos.

**Qué NO se hizo todavía**:
- No se cargó ningún ítem/catálogo real (la clínica no ha dado esa lista) — no hay seeder, el módulo está construido y probado, pero vacío de datos reales.
- No se conectó con `Factura` (ver arriba).
- No se agregó ningún rol nuevo (`farmacia`) — se usan los 3 roles existentes con el criterio explicado arriba.

**Validación externa (24 ago 2026)**: la separación catálogo/movimientos con stock derivado (nunca editado a mano) coincide con cómo lo resuelven sistemas de farmacia hospitalaria reales — es prácticamente unánime en las fuentes consultadas. Se incorporaron los 2 ajustes marcados arriba (lotes/vencimiento con FEFO, y separar prescrito/dispensado/administrado — este último **no se implementó todavía**, sigue siendo relevante para cuando se conecte con el futuro módulo de prescripciones de 2027, ver 6.1). Ver también la sección 6.4 sobre el marco legal de datos de salud, relevante para este módulo si en algún momento maneja datos de pacientes.

### 6.4 Marco legal ecuatoriano aplicable (contexto nuevo, investigación externa, 24 ago 2026)

**Por qué se agrega esta sección**: al investigar cómo modelan otros sistemas los módulos de 6.2/6.3, apareció contexto legal específico de Ecuador que no estaba documentado en ningún lado del proyecto hasta ahora, y que aplica no solo a los módulos futuros sino, en menor medida, a lo ya construido (Pacientes, HistoriaClinicas). Es información de contexto, no implica ningún cambio de código inmediato.

**Ley Orgánica de Protección de Datos Personales (LOPDP)**: vigente en Ecuador desde el 26 de mayo de 2021, con reglamento general desde noviembre de 2023. Clasifica explícitamente los **datos de salud como "dato sensible"** (junto con datos biométricos, genéticos, etc.), con reglas más estrictas que datos personales comunes — en particular, el tratamiento de datos sensibles requiere en general **consentimiento explícito del titular**, salvo excepciones (la ley sí prevé que instituciones y profesionales de salud puedan tratar datos de salud de sus propios pacientes bajo tratamiento, sin necesitar ese consentimiento caso por caso). También establece el principio de "protección de datos desde el diseño" — pensar en seguridad/minimización desde que se diseña un sistema, no agregarlo después.

**Normativa del Ministerio de Salud Pública (MSP) sobre historia clínica electrónica**: existen Acuerdos Ministeriales (1190-2012, 0009-2017 y un reglamento específico de 2017) que disponen el uso del estándar internacional **HL7** para historia clínica electrónica, obligatorios para instituciones de salud **tanto públicas como privadas** en Ecuador, y definen un conjunto de datos mínimos estandarizados para la "Historia Clínica Única".

**Qué implica esto para el proyecto (sin ser una lista de tareas urgentes, solo contexto a tener presente)**:
- No cambia nada de lo ya construido hoy — el sistema actual (login, roles, permisos por recurso, protección contra borrado, etc.) ya va en la línea correcta de cuidar el acceso a datos sensibles, aunque no fue diseñado citando esta ley explícitamente.
- Es relevante sobre todo **de cara al futuro**: cuando se aborde el módulo de prescripciones (2027, sección 6.1) o cualquier ampliación de `HistoriaClinica`, conviene tener en mente el principio de "protección desde el diseño" (quién puede ver qué, por qué, y durante cuánto tiempo se conserva la información).
- El estándar HL7 mencionado por el MSP es la misma familia de estándares (HL7/FHIR) que se usó como referencia para validar las propuestas de 6.2 y 6.3 — no es una coincidencia forzada, es el estándar de facto del sector tanto a nivel internacional como en la normativa local.
- **No hay ninguna acción pendiente inmediata por esto** — se documenta como contexto para que, si en algún momento la clínica pregunta por cumplimiento normativo o el dueño lo plantea en la entrevista formal (sección 1, aún pendiente), ya haya un punto de partida investigado en vez de partir de cero.

### 6.5 Preparación entrevista de seguimiento (24-25 ago 2026) — planificación, sin tocar código

**Contexto**: sesión aparte, en paralelo a la que construyó 6.2/6.3 y el resto de cambios de UX (sección 8). Sirvió solo para preparar la entrevista formal del **25 ago 2026, después de las 9am, con Ysrael Calle** (contabilidad, pero conoce el flujo operativo) — no generó ningún cambio de código hasta que el usuario lo pidió explícitamente. El detalle completo de la conversación queda en `MEMORIA_SESION_ENTREVISTA_2026-08-24.md` y `PLAN_MODULOS.md` (archivos de trabajo, no versionados en el repo salvo lo que se resume acá).

**Expediente clínico completo — alcance confirmado por el cliente**: al preguntarle directamente si "digitalizar la mayor parte del historial clínico" significa un expediente completo (antecedentes, alergias, signos vitales, resultados de exámenes, todo conectado), **confirmó que sí**. Esto amplía lo que hoy hace `HistoriaClinica` (solo `diagnostico`/`tratamiento`/`notas` en texto libre, ver sección 4).

**Reconciliado contra lo ya construido en 6.2/6.3 (importante — evita duplicar trabajo)**:
- **Resultados de exámenes con archivo adjunto**: **ya cubierto**, es exactamente lo que hace `OrdenEstudio` (sección 6.2) — no hace falta ningún módulo nuevo para esto.
- **Antecedentes, alergias, signos vitales**: **no existen todavía**, ni como tabla ni como campo — son los 3 módulos nuevos reales que hacen falta para completar el expediente.

**Los 3 módulos pendientes, con diseño ya pensado (no implementado)**:
1. **Alergias**: por paciente (no por consulta), tipo (medicamento/alimento/otro) + severidad. Debe verse destacado en la ficha del paciente y en Historia Clínica, no como texto libre escondido — es la razón de separarlo en su propia tabla.
2. **Antecedentes**: por paciente, categorizado (personal/quirúrgico/familiar/hábito), más grupo sanguíneo.
3. **Signos vitales**: por consulta (vinculado a `HistoriaClinica`), presión arterial/temperatura/frecuencia cardíaca/frecuencia respiratoria/peso/talla/saturación de oxígeno.

Orden sugerido para construirlos (por seguridad del paciente primero, y porque alergias/antecedentes son más simples que signos vitales al ser "por paciente" en vez de "por consulta"): alergias → antecedentes → signos vitales.

**Requerimiento del cliente sobre inventario — matiz nuevo, no resuelto en 6.3**: el cliente pidió que el registro de insumos cubra **farmacia, quirófano, admisión y facturación** — 4 puntos, no solo farmacia. El módulo de 6.3 ya soporta esto a nivel de dato (`area_origen`/`area_destino` como texto libre con esa lista fija, ver 6.3 punto 1 de la tabla de decisiones), pero no está confirmado si debe ser un inventario compartido entre las 4 áreas o si cada una maneja el suyo, ni qué hace específicamente cada área con ese registro — depende de la misma pregunta pendiente sobre el mecanismo real de farmacia (ver ítem sin resolver en la sección 6, arriba).

**Prescripciones (2027)**: sigue sin construirse (ya documentado en 6.1). Nuevo detalle: falta confirmar si el médico prescribe solo lo que existe en `items_inventario`, o también medicamentos que el paciente compra afuera de la clínica — define si la prescripción se vincula o no al inventario de 6.3.

**Checklist completo preparado para la entrevista del 25/08** (documento Word entregado al usuario, no versionado en el repo):
- Mecanismo real de farmacia (pregunta nueva, prioridad alta — ver arriba).
- Prioridad dentro del expediente clínico: ¿qué construir primero de los 3 módulos pendientes?
- Qué significa "innovar los consultorios" (2027).
- Mecánica de prescripciones: ¿solo inventario interno, o también medicamentos externos?
- El resto de preguntas de negocio ya documentadas en la sección 6 principal (precios web, confirmación de citas, facturación SRI/seguros, cantidad de usuarios, "cuantificos", acceso remoto, presupuesto, plazos, sucursales futuras) — sin cambios, siguen abiertas.

**Puntos detectados en esta planificación que ni siquiera tienen pregunta preparada todavía** (quedan para una vuelta futura, después de que se resuelvan las preguntas de arriba):
- Si algún examen (`OrdenEstudio`) consume insumos del inventario (agujas, reactivos) y si eso debería descontar stock automáticamente.
- Permisos/roles para los 3 módulos nuevos (alergias, antecedentes, signos vitales) — falta extender la matriz de la sección 10 cuando se construyan.
- Si un antecedente/alergia corregido debe conservar historial de cambios o simplemente editarse.

**Qué NO se hizo en esta sesión de planificación**: no se tocó ningún modelo, migración, Resource ni seeder — es 100% preparación para la entrevista, a pedido explícito del usuario de no interferir con el patch grande que se estaba aplicando en otra sesión en paralelo.

### 6.6 Dashboard gerencial para el admin — propuesta, sin tocar código (25 ago 2026)

**Contexto**: a raíz de una pregunta directa del usuario — *"¿cómo puede el administrador saber si está ganando o no? No hay nada en el sistema que le indique qué está pasando, ni gráfica"* — se revisó el Dashboard actual (`/admin`) y se confirmó el problema: el único widget que existe es `CitasDeHoyWidget` (una tabla de citas del día, ver sección 8 punto 1). No hay ningún indicador financiero, gráfico de tendencia, ni resumen operativo — para saber cuánto factura la clínica hoy habría que sumar facturas a mano, factura por factura, desde `/admin/facturas`.

**Qué se propone construir** (todo como Widgets nuevos de Filament, visibles solo para el rol `admin` — mismo patrón `->visible()` que ya usan otros controles del panel, ver sección 10 — ubicados arriba del `CitasDeHoyWidget` existente, que no se toca):

1. **Fila de indicadores clave (`StatsOverviewWidget`)**:
   - Ingresos del mes — suma de `facturas.monto` donde `estado_pago = pagado`, mes actual — con comparación (%) contra el mes anterior.
   - Por cobrar — suma de facturas en estado `pendiente` (dinero ya facturado que todavía no entró a caja).
   - Citas atendidas hoy / en la semana.
   - Ocupación de camas en tiempo real (camas ocupadas / total, usando `Cama`/`Internamiento` de la sección 6.2).

2. **Gráfico de ingresos** (línea o barra, últimos 6-12 meses) — monto facturado por mes, para ver tendencia en vez de solo la foto del día.

3. **Gráfico por área/especialidad** — qué áreas generan más citas o más facturación, para saber qué especialidades sostienen el negocio.

4. **Alertas operativas** (hoy invisibles a menos que alguien entre a buscarlas a mano):
   - Lotes de inventario (sección 6.3) vencidos o por vencer.
   - Facturas vencidas sin cobrar.
   - Camas ocupadas hace más de X días (posible dato desactualizado, a definir el umbral).

**Alcance confirmado**: no hace falta ninguna tabla ni migración nueva — `facturas`, `citas`, `camas`/`internamientos` y `lotes_inventario` ya existen con todo lo necesario. Es 100% Widgets nuevos + queries sobre modelos ya construidos.

**Pendiente de definir antes de construir** (no bloqueante, pero conviene decidirlo con el usuario en la próxima sesión):
- Umbral de "cama ocupada hace demasiado tiempo" para la alerta.
- Si el gráfico por área cuenta citas, facturación, o ambos con un selector.
- Si conviene un rango de fechas seleccionable en el dashboard o queda fijo (mes actual + últimos 6-12 meses).

**Qué NO se hizo en esta entrada**: solo documentación de la propuesta, a pedido explícito del usuario — no se creó ningún Widget, no se tocó `CitasDeHoyWidget` ni ningún modelo.

**Plan de construcción dividido en 3 sesiones (25 ago 2026, sin tocar código todavía)**: a pedido del usuario, la propuesta de arriba se divide en 3 partes independientes para construirse en sesiones separadas — el módulo completo (4 puntos, con gráficos y queries agregadas) es demasiado para una sola sesión. Cada sesión entrega algo funcional por sí solo (no depende de que las otras ya estén hechas), y todas siguen el mismo patrón ya usado en el resto del panel: Widgets nuevos, visibles solo para `admin` (`->visible()`), ubicados arriba de `CitasDeHoyWidget`, sin tablas/migraciones nuevas.

- [x] ~~**Sesión 1 — Indicadores clave (`StatsOverviewWidget`)**~~ — **construida** (25 ago 2026, sexta entrada del día). Los 4 números del punto 1 de la propuesta (ingresos del mes con comparación % vs. mes anterior, por cobrar, citas atendidas hoy/semana, ocupación de camas). Es la pieza más simple — un solo widget de tipo "stats", sin librería de gráficos — y la que responde más directo a la pregunta original del usuario ("¿está ganando o no?"), por eso fue primera. Ver el detalle completo en la nueva sección **6.6.1** más abajo. **Aún sin confirmar por el usuario en el entorno real** — escrito sin acceso a PHP/Sail (se validó sintaxis con `php -l`, pero no se corrió en `/admin`).
- [x] ~~**Sesión 2 — Gráficos (`ChartWidget` x2)**~~ — **construida y confirmada funcionando por el usuario en el entorno real** (25 ago 2026, novena entrada del día, tras corregir el bug del gráfico "Por área" en blanco — ver sección 6.6.2). El punto 2 (ingresos por mes, con selector de rango) y el punto 3 (por área/especialidad, con selector citas/facturación) de la propuesta. Las 2 preguntas pendientes se le hicieron directo al usuario en vez de asumir.
- [x] ~~**Sesión 3 — Alertas operativas**~~ — **construida y confirmada funcionando por el usuario en el entorno real** (25 ago 2026, décima a decimotercera entrada del día). El punto 4 de la propuesta (lotes de inventario vencidos/por vencer, facturas vencidas, camas ocupadas hace demasiado tiempo). Los 2 umbrales de negocio que faltaban se le preguntaron directo al usuario, que en ambos casos pidió que se decidiera un valor razonable en vez de darlo él — ver el detalle completo, los valores elegidos, y los 2 bugs encontrados y corregidos en el camino (uno de arranque del panel, otro de layout) en la sección **6.6.3** más abajo. **Con esto, el Dashboard gerencial completo (las 3 sesiones) queda construido y confirmado de punta a punta en el entorno real.**

**Pendiente de definir antes de construir** (sin cambios respecto a la entrada anterior, se repite aquí para que quede junto al plan): umbral de días para la alerta de camas (Sesión 3), si el gráfico por área mide citas/facturación/ambos (Sesión 2), y si el rango de fechas del dashboard es fijo o seleccionable (Sesión 2). La Sesión 1 no tiene ninguna decisión pendiente — puede construirse primero sin esperar respuesta a nada.

**Qué NO se hizo en esta entrada**: solo se dividió el plan en sesiones — sigue sin tocarse ningún Widget, modelo ni código.

### 6.6.1 Sesión 1 construida — Indicadores clave (25 ago 2026, sexta entrada del día)

Se creó `app/Filament/Widgets/IndicadoresGerencialesWidget.php` (extiende `Filament\Widgets\StatsOverviewWidget`), con los 4 números acordados en el plan de sesiones de arriba:

1. **Ingresos del mes** — suma de `facturas.monto` donde `estado_pago = pagado` con `fecha` dentro del mes actual, con la variación % contra el mismo total del mes anterior como descripción (flecha verde arriba / roja abajo). Si el mes anterior no tuvo ingresos pagados, se evita la división entre cero mostrando un texto ("Sin ingresos este mes" / "Mes anterior sin ingresos registrados") en vez de un porcentaje.
2. **Por cobrar** — suma de `facturas.monto` donde `estado_pago = pendiente`, **sin** restringir por mes: es la cartera pendiente completa (dinero ya facturado que no ha entrado a caja), no solo la del mes en curso, para que no se pierda de vista una factura pendiente de un mes anterior.
3. **Citas atendidas hoy** — cuenta de `citas` con `fecha = hoy` y `estado = atendida`, con la cuenta de la semana (`startOfWeek()`/`endOfWeek()` de Carbon, lunes a domingo) como descripción. A diferencia del widget `CitasDeHoyWidget` ya existente (que muestra **todas** las citas del día sin importar su estado), este número cuenta solo las que ya se marcaron como atendidas.
4. **Ocupación de camas** — camas con un internamiento activo (`fecha_alta` nula) sobre el total de camas, mismo criterio que `Cama::ocupada()` (sección 6.2) pero como una sola query agregada (`whereHas`) en vez de recorrer cama por cama. Muestra "X / Y" con el porcentaje como descripción, y cambia de color (verde/ámbar/rojo) según el porcentaje de ocupación.

**Visibilidad**: `public static function canView(): bool` restringido a `Auth::user()?->isAdmin() ?? false` — mismo criterio de "solo admin" que ya usan otros controles del panel (ver sección 10), pero a nivel de widget completo en vez de un botón suelto, que es el mecanismo que Filament expone para widgets (no hay un botón que ocultar, es la tarjeta entera).

**Ubicación en el Dashboard**: `protected static ?int $sort = 0`, un puesto antes que `CitasDeHoyWidget` (`$sort = 1`), así los indicadores gerenciales quedan arriba de la tabla de citas del día — sin tocar ese widget existente.

**Alcance**: no se creó ninguna tabla, migración ni modelo nuevo — es 100% queries sobre `Factura`, `Cita` y `Cama`/`Internamiento`, como estaba confirmado en la propuesta original.

**Qué NO se hizo en esta entrada**: no se tocaron los 2 gráficos ni las alertas operativas (Sesiones 2 y 3, siguen pendientes) — la Sesión 1 se entrega completa por sí sola.

### 6.6.2 Sesión 2 construida — Gráficos (25 ago 2026, séptima entrada del día)

**Decisiones pendientes, resueltas por el usuario antes de construir**: se le preguntó directo (en vez de asumir, ya que estas dos definen la forma del módulo) — el gráfico por área mide **ambos, con selector** (citas y facturación, alternable), y el rango de fechas del dashboard es **seleccionable por el usuario** (no fijo).

Se crearon 2 `ChartWidget` nuevos (extienden `Filament\Widgets\ChartWidget`, tipo `bar`), ambos visibles solo para `admin` (mismo `canView()` que `IndicadoresGerencialesWidget`), ubicados entre ese widget (`$sort = 0`) y `CitasDeHoyWidget` — que se corrió de `$sort = 1` a `3` para dejarle el lugar a los 2 nuevos.

1. **Ingresos por mes** (`IngresosPorMesChartWidget.php`, `$sort = 1`): barras con 2 series por mes — "Facturado" (suma de `facturas.monto` sin filtrar por estado) y "Cobrado" (mismo total pero solo `estado_pago = pagado`) — misma distinción que ya usa `IndicadoresGerencialesWidget` entre "ingresos del mes" y "por cobrar", pero mostrada como tendencia en vez de un solo número. Acá se aplicó el selector de rango pedido por el usuario: `getFilters()` con 4 opciones — "Últimos 6 meses" (default), "Últimos 12 meses", "Año actual", "Año anterior" — usando el mecanismo estándar de Filament (`$this->filter`, validado con `match()` + caso `default` en vez de usarlo directo en la query, como recomienda la documentación). El rango nunca muestra meses futuros vacíos (capado al mes en curso, salvo "año anterior" que es un rango cerrado).

2. **Por área** (`FacturacionPorAreaChartWidget.php`, `$sort = 2`): barras, una por cada `Area`, con el selector de métrica pedido por el usuario (`getFilters()`: "Cantidad de citas" / "Monto facturado", default citas). Citas cuenta todas sin filtrar por estado (una cancelada también indica demanda del área). Facturación pasa por `Factura -> Cita -> Area` (`whereHas('cita', ...)`) — **limitación conocida**: una factura sin `cita_id` (permitido, es nullable) no se puede atribuir a ningún área con el modelo actual, queda fuera de este gráfico. **Alcance fijo al año en curso**, sin selector de rango propio — ver el supuesto explicado abajo.

**Por qué el gráfico por área no tiene también un selector de rango**: el mecanismo simple de Filament (`getFilters()`) solo soporta **un** filtro tipo dropdown por widget — ya usado acá para la métrica (citas/facturación). Un segundo selector (rango de fechas) requeriría el mecanismo más nuevo `HasFiltersSchema` (schemas con `DatePicker`, ver documentación de Filament 4.x/5.x), que no se ha probado en este proyecto y agrega superficie de riesgo sin confirmar en el entorno real — mismo criterio de cautela que ya se aplicó con el sidebar oscuro descartado en la sección 8.1. Se prefirió resolver el rango seleccionable en el gráfico de ingresos (donde tiene más sentido, es una tendencia en el tiempo) y dejar el de por área con alcance fijo al año en curso — **supuesto razonable, editable después**: si hace falta, se puede agregar el mismo patrón de `getFilters()` con rangos predefinidos (mismo que ya tiene el gráfico de ingresos) en vez de un date-picker libre, sin tocar el resto del widget.

**Alcance confirmado**: no se creó ninguna tabla, migración ni modelo nuevo — es 100% queries sobre `Factura`, `Cita` y `Area`, como estaba confirmado en la propuesta original (sección 6.6).

**Qué NO se hizo en esta entrada**: no se tocaron las alertas operativas (Sesión 3, sigue pendiente) — la Sesión 2 se entrega completa por sí sola. Tampoco se resolvió la limitación de facturas sin `cita_id` en el gráfico por área (documentada arriba, no bloqueante).

**Confirmado funcionando por el usuario en el entorno real (25 ago 2026, octava entrada del día)**: probado con datos de prueba reales (4 facturas, una de ellas — Ivan Antonio, $40,000, pendiente — sin `cita_id` asociado). Se verificó que esa factura sale correctamente excluida del gráfico "Por área" (queda en $1,240 la suma visible entre Urología/Otorrinolaringología, vs. los $41,220 totales que sí cuenta "Ingresos por mes", que no depende de la cita) — confirma que la limitación documentada arriba es el comportamiento esperado, no un bug.

**Ajuste posterior — formato `$` en eje y tooltip (25 ago 2026, octava entrada del día)**: a pedido del usuario, ambos widgets ahora formatean los montos con signo `$` y separador de miles (antes el eje Y mostraba el número pelado, ej. "1200"). Se implementó con `getOptions()` devolviendo `Filament\Support\RawJs` — un callback de Chart.js (`ticks.callback` para el eje, `tooltip.callbacks.label` para el tooltip), que es el mecanismo que la documentación oficial de Filament recomienda para este caso (no se puede lograr con un array de opciones simple, porque JSON no puede representar una función JS). En `FacturacionPorAreaChartWidget` el formato es condicional: solo se aplica si `$this->filter === 'facturacion'` (con "Cantidad de citas" se devuelve un array vacío, dejando el formato numérico simple de Chart.js). En `IngresosPorMesChartWidget` aplica siempre, sin condicional, porque ahí las 2 series ("Facturado"/"Cobrado") son montos en todos los casos. **Aún sin confirmar este ajuste puntual por el usuario en el entorno real.**

**Bug encontrado y corregido — gráfico "Por área" en blanco con "Cantidad de citas" (25 ago 2026, novena entrada del día)**: al probar el ajuste anterior en el entorno real, el usuario reportó que el gráfico "Por área" quedaba **completamente vacío** (sin barras, sin ejes, sin nada) al tener seleccionada la métrica "Cantidad de citas" — solo se veía bien con "Monto facturado" seleccionado. **Causa**: la implementación original de `getOptions()` en `FacturacionPorAreaChartWidget` devolvía un **array vacío `[]`** para "Cantidad de citas" y un **objeto `RawJs`** para "Monto facturado" — dos tipos de dato distintos según la métrica activa. Al cambiar el selector, Livewire vuelve a pedir `getOptions()` y ese cambio de tipo entre una respuesta y otra rompía el JS que Chart.js necesita para re-renderizar, dejando el canvas en blanco sin ningún error visible en pantalla. **Solución aplicada**: `getOptions()` ahora **siempre** devuelve `RawJs`, con la misma estructura en ambos casos — la decisión de anteponer `$` (o no) al valor se resuelve *adentro* del JS, interpolando un booleano de PHP (`$esFacturacion ? 'true' : 'false'`) directamente en el callback (`({$esFacturacion} ? '$' : '') + value.toLocaleString(...)`), en vez de cambiar la forma de lo que el método devuelve. Se verificó con un script PHP chico (fuera del repo, solo para probar el heredoc) que la interpolación genera el JS esperado en ambos casos antes de dar el fix por bueno. `IngresosPorMesChartWidget` no tenía este problema — su `getOptions()` siempre devolvía `RawJs` desde el principio, nunca cambiaba de tipo. **Confirmado funcionando por el usuario en el entorno real** — el gráfico "Por área" ahora muestra las barras correctamente en ambas métricas, con el signo `$` aplicado solo cuando corresponde.

**Troubleshooting general para casos futuros similares**: si un `ChartWidget` con `getFilters()` se rompe (queda en blanco, sin errores visibles en pantalla) solo con *algunas* opciones del selector y no con otras, sospechar de un método que devuelve **tipos de dato distintos** según el filtro activo (`getOptions()`, `getData()`, etc.) — Livewire/Chart.js esperan una estructura consistente entre una actualización y la siguiente. Revisar la consola del navegador (F12) también ayuda a confirmar si hay un error de JS, aunque en este caso el fallo fue silencioso.

**Confirmado funcionando por el usuario en el entorno real (25 ago 2026, novena entrada del día)**: el fix resolvió el problema — el gráfico "Por área" ahora muestra las barras correctamente en ambas métricas ("Cantidad de citas" y "Monto facturado"), con el signo `$` aplicado solo cuando corresponde. Con esto, la **Sesión 2 completa queda confirmada de punta a punta** en el entorno real: los 2 `ChartWidget`, sus selectores, el formato `$`, y la limitación conocida de facturas sin `cita_id` (verificada en la entrada anterior).

### 6.6.3 Sesión 3 construida — Alertas operativas (25 ago 2026, décima entrada del día)

**Qué se construyó**: `app/Filament/Widgets/AlertasOperativasWidget.php` + su vista `resources/views/filament/widgets/alertas-operativas-widget.blade.php`, un widget custom (no `StatsOverviewWidget` ni `ChartWidget`, sino `Filament\Widgets\Widget` con vista propia, siguiendo el patrón `<x-filament-widgets::widget>` + `<x-filament::section>` que documenta Filament para widgets a medida) — 3 columnas lado a lado, cada una con un contador (badge rojo/ámbar si hay algo, verde si no), hasta 5 ítems concretos, y un link al listado completo del recurso ya existente:

1. **Lotes por vencer**: lotes de `LoteInventario` con `fecha_vencimiento` dentro de los próximos 90 días (mismo default que ya usaba `LoteInventario::porVencer()`, confirmado por el usuario que se mantiene igual), filtrando además los que ya tienen `stockActual() > 0` — un lote agotado no representa ningún riesgo aunque su fecha ya haya pasado. Ordenados por fecha de vencimiento ascendente (lo más urgente primero). Enlaza a `/admin/lotes-inventario`.
2. **Facturas vencidas**: facturas con `estado_pago = pendiente` y `fecha` de emisión con más de 30 días de antigüedad. Se usa la fecha de emisión como proxy porque `facturas` no tiene ninguna columna de fecha de vencimiento propia — solo `fecha` (de emisión). Muestra también el monto total pendiente que cae en esta categoría. Enlaza a `/admin/facturas`.
3. **Camas ocupadas hace mucho**: camas con un internamiento activo (`fecha_alta` nula) cuya `fecha_ingreso` supera los 14 días — mismo criterio de "ocupado" que ya usa `Cama::ocupada()`. Enlaza a `/admin/camas`.

**Los 2 umbrales de negocio que quedaban pendientes (ver sección 6.6, "Pendiente de definir antes de construir")** se le preguntaron directo al usuario en vez de asumirlos — para los 2, el usuario pidió explícitamente que se decidiera un valor razonable en su lugar. Se eligieron:
- **Cama ocupada "hace demasiado tiempo" → 14 días.** Es un valor intermedio pensado para hospitalización general (no UCI, que naturalmente tiene estadías más largas) — la alerta busca detectar más un dato desactualizado (alta no registrada) que una estadía clínicamente larga, así que se prefirió un umbral corto que dispare la revisión temprano.
- **Factura vencida → pendiente hace más de 30 días desde su emisión.** Se alinea con un ciclo de facturación mensual típico — a los 30 días ya pasó un mes completo sin cobrarse, momento razonable para que aparezca en una alerta en vez de esperar más.
- El tercer umbral (lotes por vencer, 90 días) no se tocó — el usuario confirmó mantener el mismo default que ya usaba `LoteInventario::porVencer()`.

**Los 3 umbrales están como constantes privadas** (`DIAS_CAMA_OCUPADA_LARGA`, `DIAS_FACTURA_VENCIDA`, `DIAS_LOTE_POR_VENCER`) arriba de la clase — si alguno resulta no ajustarse bien al uso real de la clínica, se cambia en un solo lugar sin tocar el resto del widget.

**Alcance confirmado**: no se creó ninguna tabla, migración ni modelo nuevo — 100% queries sobre `LoteInventario`, `Factura` y `Cama`/`Internamiento` ya existentes, como estaba confirmado en la propuesta original (sección 6.6).

**Ubicación en el Dashboard**: `$sort = 3`, debajo de los 2 `ChartWidget` de la Sesión 2 (sort 1 y 2) y arriba de `CitasDeHoyWidget`, que se corrió de `$sort = 3` a `4` para dejarle lugar (mismo patrón usado cada vez que se agrega un widget nuevo arriba del Dashboard).

**Con esto, el Dashboard gerencial completo (sección 6.6) queda construido de punta a punta** — los 3 puntos originales de la propuesta (indicadores clave, gráficos, alertas operativas) tienen su widget correspondiente. **Aún sin confirmar por el usuario en el entorno real** — escrito sin acceso directo a Sail, se validó sintaxis instalando `php-cli` en el entorno de trabajo y corriendo `php -l`, pero no se ha visto renderizado en `/admin` todavía.

**Qué falta para poder darlo por probado**: correr `sail up` y entrar a `/admin` como admin para confirmar que las 3 columnas se ven bien (incluido el layout responsive en pantallas chicas, ya que son 3 columnas en desktop mismo criterio de `IndicadoresGerencialesWidget` con sus 4 stats) y que los 3 links llevan al listado correcto. Si en el entorno real no hay datos de prueba que disparen alguna de las 3 alertas, conviene cargar al menos un caso de cada una (un lote con vencimiento cercano, una factura vieja pendiente, un internamiento sin alta de hace más de 14 días) para verificar que el conteo/listado no solo el estado "vacío".

**Bug encontrado y corregido al aplicar en el entorno real (25 ago 2026, undécima entrada del día)**: `AlertasOperativasWidget` se escribió inicialmente con `protected static string $view = '...'`, copiando un patrón visto en documentación/ejemplos de **Filament 3.x** — pero la versión instalada en este proyecto es **Filament 5.7.6**, donde `Filament\Widgets\Widget::$view` es una propiedad **no estática** (`protected string $view;`). Redeclararla como estática en la clase hija rompía la carga de **todo el panel** (`sail artisan optimize:clear` y cualquier request a `/admin` tiraban un error fatal de PHP, `Cannot redeclare non static ... as static`), no solo el widget nuevo — Filament descubre todos los widgets al arrancar el panel, así que un solo widget mal declarado tumba el panel entero. **Corregido** cambiando a `protected string $view = '...'` (no estática), verificando el código fuente real de la clase base en la v5.7.6 antes de dar el fix por bueno, en vez de volver a confiar en un ejemplo de otra versión.

**Troubleshooting general para casos futuros similares**: si el panel completo deja de cargar (no solo un widget puntual) justo después de agregar un widget custom nuevo, sospechar de una propiedad redeclarada con una firma (estática/no estática, tipo) distinta a la de la clase base real — y confirmar contra el código fuente de la versión de Filament realmente instalada (`composer.lock`), no contra documentación o ejemplos genéricos que pueden corresponder a otra versión mayor.

**Segundo bug encontrado y corregido — clases de Tailwind sin efecto en la vista del widget (25 ago 2026, duodécima entrada del día)**: tras el fix de arriba, el usuario confirmó que el panel volvía a cargar, pero reportó que las 3 secciones del widget salían apiladas en una sola columna en vez del layout de 3 columnas lado a lado. **Causa, preexistente en el proyecto y no introducida en esta sesión**: `resources/css/filament/admin/theme.css` (tema del panel, ver sección 8.2) declara qué carpetas escanea Tailwind v4 para generar el CSS (`@source`) — tenía `app/Filament` pero **le faltaba `resources/views/filament`**, la carpeta de vistas Blade custom de widgets. Nunca se había notado porque `AlertasOperativasWidget` es el **primer** widget del proyecto con una vista Blade propia que usa clases sueltas de Tailwind (`grid`, `lg:grid-cols-3`, etc.) — el resto de widgets (`IndicadoresGerencialesWidget`, los 2 `ChartWidget`) se apoya solo en componentes ya estilizados de Filament, que sí vienen cubiertos por el CSS del vendor. Sin esa carpeta en el `@source`, las clases de la vista nunca llegaron al CSS compilado y quedaron sin ningún efecto. **Corregido** agregando una sola línea, `@source '../../../../resources/views/filament';`, sin tocar el widget ni su vista. **Troubleshooting para casos futuros**: si una vista Blade custom de un widget (o de cualquier componente propio) se ve "sin estilos" — como si las clases de Tailwind no existieran, aunque estén bien escritas — revisar primero el `@source` del `theme.css` del panel en vez de sospechar del propio Blade; Tailwind v4 solo genera CSS para las clases que encuentra en las rutas declaradas ahí. **Aún sin confirmar por el usuario en el entorno real** — corregido sin acceso a Vite/`npm run build`, pendiente que el usuario aplique el patch y recompile los assets.

**Confirmado funcionando por el usuario en el entorno real (25 ago 2026, decimotercera entrada del día)**: tras aplicar el patch del `@source` faltante y recompilar con `npm run build`, las 3 tarjetas quedan lado a lado en 3 columnas como estaba pensado. Con esto, la Sesión 3 (y el Dashboard gerencial completo) queda confirmada de punta a punta en el entorno real.

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

### Logo real recibido y aplicado (25 ago 2026, previo a la entrevista formal)

El usuario compartió el logo oficial de la clínica (imagen suelta + embebido en dos páginas de un PDF de servicios, `Servicios_CB_2026.pdf`). Reemplaza al logo provisorio diseñado a mano (triángulo/hoja) descrito arriba.

- **Origen del archivo**: no hay vectorial disponible, solo raster de baja resolución (184×185px, extraído del PDF recuperando su máscara de transparencia real — la primera extracción directa vino sin alpha, con fondo blanco sólido, así que se reconstruyó combinando la imagen de color con su `SMask` del PDF). Es un lockup **vertical**: monograma (ícono estilizado, parece una "F"/"B" entrelazadas o similar abstracción — no un texto literal) arriba, "CLÍNICA BENITES" + "EXCELENCIA QUIRÚRGICA" abajo, en dos líneas.
- **Archivos nuevos en `public/images/`** (reemplazan a `logo.svg`/`icon.svg`, que se archivaron sin borrar en `public/images/_legacy/` por las dudas):
  - `logo.png` — el lockup vertical completo, recoloreado a navy (`#12395c`, mismo tono que se venía usando) preservando la transparencia real. Usado en el encabezado de la factura PDF (ahí sí hay espacio vertical de sobra).
  - `logo-white.png` — misma pieza, recoloreada a blanco, para fondos oscuros (no está en uso todavía, queda lista por si se necesita).
  - `logo-horizontal.png` / `logo-horizontal-white.png` — **recomposición horizontal** (ícono a la izquierda + las dos líneas de texto a la derecha), armada recortando y reacomodando las mismas piezas del logo real (sin redibujar ni usar una tipografía nueva). Fue necesaria porque el lockup vertical, escalado a la altura chica del header del panel (`brandLogoHeight('2.5rem')` ≈ 40px), dejaba el texto ilegible — a esa altura el ancho resultante es demasiado angosto. La versión horizontal sí es legible a ese tamaño (probado con un render simulado a 40px de alto antes de aplicar).
  - `public/favicon.ico` regenerado desde el monograma (recortado del logo real, con margen y fondo blanco para que se vea bien en pestañas claras u oscuras del navegador).
- **Código actualizado**: `AdminPanelProvider.php` → `->brandLogo(asset('images/logo-horizontal.png'))` y `->favicon(asset('favicon.ico'))`. `resources/views/pdf/factura.blade.php` → se agregó el logo (`logo.png`, 50px de alto) en el encabezado, que antes solo tenía el nombre en texto.
- **Color primario Teal sin cambios** — el navy del logo real es distinto al azul/turquesa del logo provisorio anterior, pero no se tocó `Color::Teal` en esta vuelta porque el usuario no lo pidió; queda como posible ajuste futuro si al verlo en el entorno real no combina bien.
- **Limitación conocida**: la resolución fuente es baja (184×185px) porque no hay un archivo vectorial ni de alta resolución disponible — se ve bien a los tamaños actuales (favicon, header 2.5rem, factura 50px), pero si en el futuro se necesita el logo grande (ej. para imprimir, o un hero en la página web pública), conviene pedirle al dueño el archivo original en alta resolución o vectorial en la entrevista.
- **Pendiente probar en el entorno real**: cómo se ve el logo horizontal en el header del panel y en el login, y el logo vertical en una factura generada de verdad (hasta ahora solo se verificó con renders simulados fuera de la app).

**Otros pendientes de fondo, sin definir aún**:
- Construir la página web pública (diseño, contenido) — sigue sin arrancar.
- Sigue pendiente la respuesta del contacto interno de la clínica sobre cuántas áreas/especialidades tiene — no bloquea el desarrollo (el sistema ya soporta cualquier número de áreas dinámicamente), pero sería bueno tenerla para cargar datos reales en vez de datos de prueba.
- No se ha hecho la entrevista formal con el dueño de la clínica.
- ~~Si se consigue el logo real de la clínica (archivo original), reemplazar el diseño provisorio de branding~~ — hecho, ver entrada de arriba (25 ago 2026).


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

**Bug encontrado y corregido (25 ago 2026, sección 6.6.3) — faltaba un `@source`**: `theme.css` solo declaraba `@source '../../../../app/Filament';`, sin la carpeta `resources/views/filament` (vistas Blade custom de widgets) — quedó sin notarse hasta que `AlertasOperativasWidget` (Sesión 3 del Dashboard gerencial) fue el primer widget del proyecto en tener una vista Blade propia con clases sueltas de Tailwind. Ver el detalle completo en la sección 6.6.3.


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

## 8.4 Agrupar el menú lateral en categorías (navigationGroup)

**Motivo**: solo el módulo de Infraestructura (Camas, Quirófanos, Cirugías, Internamientos, Órdenes de Estudio, Servicios Ambulancia) tenía `$navigationGroup` asignado. Los otros 10 Resources (Pacientes, Citas, Facturas, Historia Clínicas, Áreas, Médicos, Usuarios, Item/Lote/Movimiento Inventarios) aparecían todos sueltos en el sidebar, sin ningún criterio de orden — el usuario lo notó al ver la captura del listado de Facturas y pidió aplicar el mismo criterio de agrupación que ya existía para Infraestructura.

**Grupos definidos** (criterio: frecuencia de uso del día a día primero, catálogos/administración al final):
- **Atención al paciente**: Pacientes, Citas, Historia Clínicas.
- **Facturación**: Facturas.
- **Infraestructura** (ya existía, sin cambios): Camas, Quirófanos, Cirugías, Internamientos, Órdenes de Estudio, Servicios Ambulancia.
- **Inventario**: Item Inventarios, Lote Inventarios, Movimiento Inventarios.
- **Administración**: Áreas, Médicos, Usuarios.
- **Escritorio** (Dashboard) queda sin grupo, como está ahora — es una Page, no un Resource, y va arriba de todo.

**Qué se hizo**: en cada uno de los 10 Resources, se agregó `use UnitEnum;` (import) y la propiedad `protected static string|UnitEnum|null $navigationGroup = '...';`, mismo patrón ya usado en Infraestructura. Cambio puramente de organización del menú — no se tocaron permisos, íconos, ni ningún otro comportamiento.

**Nota sobre permisos y grupos**: Filament oculta automáticamente un grupo entero del sidebar si el usuario logueado no tiene acceso a ningún Resource de ese grupo (según los `canViewAny()` ya existentes en cada Resource) — no hizo falta ningún ajuste extra para que, por ejemplo, un médico no vea el grupo "Administración".

**Orden de los grupos forzado explícitamente**: el usuario confirmó que quería el orden propuesto (no el alfabético por defecto de Filament), así que se agregó `->navigationGroups([...])` en `AdminPanelProvider.php` con el orden exacto: Atención al paciente → Facturación → Infraestructura → Inventario → Administración.

**Pendiente, no incluido en este cambio**: diferenciar los íconos del sidebar — hoy varios Resources comparten el mismo ícono genérico (`Heroicon::OutlinedRectangleStack`): Áreas, Citas, Facturas, Historia Clínicas, Médicos y Pacientes lo usan los seis. Ayudaría a distinguirlos de un vistazo dentro de cada grupo, pero no se aplicó en esta vuelta porque este entorno de trabajo no tiene `vendor/` instalado (no se pudo verificar contra el enum real `Filament\Support\Icons\Heroicon` qué nombres de ícono son válidos en la versión de Filament instalada, y poner un nombre inválido rompería el panel). Si se quiere hacer, conviene definir los íconos junto al usuario con el entorno real corriendo para poder probar cada uno antes de commitear.

**Confirmado funcionando por el usuario en el entorno real** (los 5 grupos aparecen colapsados en el sidebar, con los ítems correctos en cada uno — ver captura compartida). El orden explícito (`navigationGroups()`) se agregó después, aún sin confirmar visualmente por el usuario.

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
