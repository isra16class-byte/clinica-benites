# Plan de contenido y flujo — Sitio público Clínica Benites

Documento de trabajo (28 ago 2026). No se ha tocado código todavía — esto es
la base para decidir antes de seguir rediseñando secciones.

---

## 1. Qué confirma la investigación

**Sobre la estructura:** la mayoría de guías "clinic website best practices"
2026 son de EE.UU. y asumen dos cosas que **no aplican a Clínica Benites**:
agendamiento online (ya descartado, MEMORIA.md §1) y compliance HIPAA (acá
correspondería LOPDP si algún día hay formularios con datos de salud). Filtrando
eso, lo que sí aplica: contacto visible siempre, mobile-first, carga rápida,
navegación de 1-2 clics, tono que calme en vez de generar ansiedad.

**Sobre el tamaño del sitio:** clínicas de tamaño chico/mediano SÍ resuelven
bien con una sola página + secciones ancladas (no un sitio multi-página desde
el día uno). Referencia real: Clínica Oxeo (fisioterapia) usa Inicio / Sobre
nosotros / Servicios / Blog / Contacto como estructura completa. Confirma que
lo que ya tenemos (`home.blade.php` con anclas) es un patrón válido, no un
punto flojo a corregir.

**Sobre el mercado local:** Clínica Kennedy (Guayaquil) — la referencia más
cercana — combina un tagline emocional corto con una promesa de "calidez +
profesionalismo + experiencia + seguridad". Confirma la dirección de copy que
ya usa el hero ("Precisión quirúrgica. Calidez humana.") — no hay que
reinventar el tono, solo extenderlo de forma consistente al resto del sitio.

**Sobre iconos y fondos:** el "look" por defecto del sector (bancos de stock)
es cruces, hexágonos, moléculas de ADN, iconos de línea genéricos en patrón
repetido — exactamente el cliché que el proyecto ya evita (ver decisión en
`especialidades.blade.php`: sin ícono genérico por especialidad). Confirma
seguir por el camino de fotografía real + iconos dibujados a medida + el
motivo propio del "plano técnico + pulso dorado" del hero, en vez de sumar un
set de iconos de stock.

---

## 2. Flujo del sitio (propuesta — sigue siendo 1 sola página)

```
Nav (fijo)
 │
 ├─ Hero            [oscuro]  → primera impresión + CTA directo (ya está bien)
 ├─ Especialidades  [oscuro]  → "qué tan completos somos" (detalle, catálogo)
 ├─ Servicios       [oscuro]  → "cómo se ve" (fotografía real, infraestructura)
 ├─ Sobre nosotros  [claro]   → pausa visual + "por qué confiar" (filosofía)
 ├─ Contacto        [oscuro]  → conversión (WhatsApp / llamada)
 └─ Footer          [oscuro]
```

**Por qué este orden y no "Sobre nosotros" primero (como en Oxeo):** el hero
ya hace el trabajo de generar confianza inicial (trust-strip con +26
especialidades, quirófanos/UCI, ambulancia, emergencias) — así que
"Especialidades" puede ir de una vez al detalle sin repetir esa introducción.
"Sobre nosotros" funciona mejor como respiro (único bloque claro/ivory) a
mitad de camino que como apertura. Es una decisión consciente, no un olvido —
pero si preferís el orden más convencional (Sobre nosotros justo después del
hero), es un cambio de 1 línea en `home.blade.php`, no de diseño.

**Lo que NO sumaría todavía** (y por qué):
- **Página de Equipo médico / fichas de doctores** — no hay entrevista formal
  con el dueño, no hay fotos ni bios confirmadas de médicos (MEMORIA.md §6).
- **Preguntas frecuentes** — las únicas respuestas 100% confirmadas hoy
  (no se agenda online, contacto por WhatsApp/tel) ya están cubiertas en
  Contacto; un FAQ con 2 preguntas se vería vacío. Mejor esperar a la
  entrevista con el dueño para tener contenido real que llenarlo.
- **Blog** — exige mantenimiento continuo de contenido; no hay quién lo
  sostenga hoy (esto es un sitio de una clínica sin presencia digital previa).
- **Multi-página (`/especialidades/cardiologia`, etc.)** — tiene sentido a
  futuro (SEO por especialidad), pero requiere contenido médico real por
  especialidad que hoy no existe. Fase futura, no ahora — mismo criterio que
  ya se aplicó a "portal de autoagendamiento" en MEMORIA.md §8.

---

## 3. Banco de frases por sección

Frases de **tono/voz de marca** (genéricas, no dependen de datos no
confirmados — se pueden usar ya) vs. frases marcadas **[dato pendiente]**
(necesitan la entrevista con el dueño antes de publicarse).

### Hero
Ya definido y validado — no tocar:
> "Precisión quirúrgica. Calidez humana."

Variantes de respaldo por si se necesita para otra pieza (redes, print) con
el mismo tono:
- "Alta complejidad, trato cercano."
- "La medicina seria de Guayaquil, sin perder la calidez."

### Especialidades
- Eyebrow actual: "Especialidades" — funciona, mantener.
- Alternativa de headline si se quiere variar: *"Un solo lugar, todas las
  etapas del cuidado."*
- Microcopy para el rail de letras (si se agrega): *"Directorio A–U"*

### Servicios
- Ya tiene buen copy por tile. Frase de cierre alternativa a la franja de
  ambulancia actual: *"Del diagnóstico a la recuperación, sin salir de la
  clínica."* (repite la idea del lede de Especialidades — usar solo una de
  las dos para no ser redundante).

### Sobre nosotros
- Lede actual funciona bien. Alternativa más corta para un posible resumen
  en redes: *"La persona y el caso clínico reciben la misma atención."*
- **[dato pendiente]** año de fundación / cantidad de médicos / si son parte
  de una red más grande — todo lo que daría más "peso" institucional a esta
  sección, pero no está confirmado.

### Contacto
- CTA actual ("Agendar por WhatsApp" / "Llamar a recepción") — mantener, es
  el patrón correcto para cómo la clínica opera hoy (manual, sin online).
- **[dato pendiente]** dirección exacta, horario de atención, si hay
  atención 24/7 real o solo en horario de consulta — necesario antes de
  poder prometer nada de "emergencias" con más detalle del genérico actual.

### Footer
- Tagline corto opcional bajo el logo: *"Clínica privada · Guayaquil"*
  (mismo eyebrow que ya usa el hero, refuerzo de marca sin inventar nada).

---

## 4. Dirección visual: iconos y fondos

**Seguir haciendo** (ya es lo correcto, confirmado contra la referencia de
stock del sector):
- Iconos SVG dibujados a mano, uno por concepto real (no un set genérico
  descargado) — como ya están en el trust-strip del hero.
- Fotografía real de la clínica como protagonista visual (Servicios), nunca
  ilustración genérica de "doctor con estetoscopio".
- El motivo "plano técnico + pulso dorado" del hero como firma — reutilizable
  en dosis pequeñas (una línea, un trazo) en otras secciones, no como fondo
  repetido en todas partes.

**Evitar activamente** (el cliché confirmado por la búsqueda de stock):
- Hexágonos, cruces médicas, moléculas de ADN, iconos de línea genéricos en
  patrón de fondo repetido.
- Gradientes "healthcare tech" abstractos (azul-verde difuso sin forma).
- Ilustración de stock de personas (doctores/pacientes genéricos).

**Fondos ya disponibles para variar sin salir de la paleta:**
1. `cb-hero-grid` (grid técnico existente) — ya se reutiliza en Especialidades
   y Contacto, con máscara distinta cada vez. Se puede seguir así en Servicios
   con otra posición de máscara, para que cada sección se sienta relacionada
   pero no idéntica.
2. Glow/blur de las fotos reales (igual que el hero) — usarlo como fondo
   ambiental detrás de bloques de texto en vez de un orb de color plano.
3. El watermark numérico grande tipo "27" (propuesto para Especialidades) —
   aplicable a otros datos reales confirmados si aparecen (ej. si se confirma
   antigüedad de la clínica, un año grande de fondo sería coherente con este
   lenguaje).

---

## 5.5 Animaciones — investigación

**Hallazgo técnico importante (afecta a todo el sitio, no solo a una
sección):** `.cb-reveal` (el fade-up que usan hero y las 4 secciones nuevas)
se dispara **al cargar la página**, con `animation-delay` en milisegundos —
no al hacer scroll hasta cada sección. Como no hay JS en el sitio público
(decisión ya tomada, ver comentarios de `hero.blade.php`), no hay forma de
detectar "esta sección entró en pantalla" con la técnica actual. Consecuencia
real: cuando alguien llega a "Contacto" scrolleando, esa animación terminó
hace varios segundos — nunca la ve. Es una de las razones (no la única, pero
sí una concreta y arreglable) por la que las secciones de abajo se sienten
más planas que el hero: el hero sí se ve animado porque está arriba desde el
primer frame.

**La solución sin romper la regla de "sin JS":** CSS scroll-driven
animations (`animation-timeline: view()`), spec nativa de CSS. Estado de
soporte a mediados de 2026: Chrome, Edge y Safari con soporte completo desde
2025; Firefox es la única excepción, todavía detrás de una bandera
(`layout.css.scroll-driven-animations.enabled`), pero ya es prioridad
declarada de Mozilla para 2026. Soporte global ~82-84%. Se usa envuelta en
`@supports (animation-timeline: view())`, así que en Firefox el contenido
simplemente aparece visible de una (sin animación rota ni elementos pegados
en `opacity: 0`) — degradación segura, no un riesgo.

```css
@media (prefers-reduced-motion: no-preference) {
  @supports (animation-timeline: view()) {
    .cb-reveal {
      animation-timeline: view();
      animation-range: entry 0% cover 30%;
    }
  }
}
```

Regla de rendimiento a respetar (ya la cumplimos, mantenerla): animar solo
`transform` y `opacity` — corren en el compositor, no fuerzan layout. Nunca
`width`/`height`/`margin` para esto.

### Dónde poner animación y de qué tipo (con criterio de restraint)

La literatura del sector es consistente en un punto para healthcare
específicamente: micro-animaciones "gentiles" sí, nada que se sienta como
"circo" — el objetivo es transmitir calma y confianza, no impresionar. Con
eso de filtro, esto es lo que le pondría a cada sección (y lo que
deliberadamente NO le pondría):

| Sección | Animación | Por qué |
|---|---|---|
| **Hero** | Ya tiene su firma (línea de pulso + reveal escalonado) | No tocar — es el "momento audaz" del sitio, no hay que repetirlo en otro lado (ver principio de restraint: la audacia se gasta en un solo lugar) |
| **Nav** | Ninguna nueva | Ya tiene underline en hover — suficiente para un elemento que está siempre visible |
| **Especialidades** | Fade-up ahora sí disparado por scroll (`view()`) + la línea dorada trazada en hover (ya prototipada) | El scroll-reveal hace que el directorio "aparezca" cuando el usuario realmente llega ahí, en vez de ya estar visible |
| **Servicios (bento)** | Fade-up con scroll + mantener el scale sutil de la foto en hover (ya existe) | Nada nuevo de fondo — el mosaico ya tiene suficiente movimiento con las fotos |
| **Sobre nosotros** | Fade-up con scroll, sin nada adicional | Es la sección "pausa" del sitio (única clara) — dejarla quieta refuerza esa función, no competir con las demás |
| **Contacto** | Fade-up con scroll + pulso muy sutil (opacidad, no escala) en el ícono de emergencias | Es contenido real (atención de emergencias), no decoración — un pulso lento ahí comunica "activo/disponible" sin ser gimmick |
| **Footer** | Ninguna | Cierre del sitio — sin movimiento, a propósito |

**Qué NO voy a agregar** (aunque son tendencia 2026 en el research):
scroll-driven parallax en imágenes, cursores con efecto trail, transiciones
3D/WebGL, animaciones de "storytelling" con múltiples fases por sección. Son
para sitios de producto/SaaS que buscan impresionar — en una clínica, ese
tipo de movimiento compite con la sensación de calma que se busca, y el
research específico de salud lo confirma: "cold hospital" y "overly playful"
son los dos extremos a evitar, no solo uno.

**Mobile:** los estados hover (línea dorada en Especialidades, scale de
fotos en Servicios) no existen al tacto — ya está bien así, no hay que
simularlos con JS. El fade-up por scroll SÍ funciona igual en mobile (no
depende de hover), así que ahí es donde vale la pena invertir el esfuerzo.

---

## 6. Decisiones confirmadas (28 ago 2026) — brief cerrado

Sin preguntas abiertas: esto es lo que se decidió, para que cualquier sesión
que retome el trabajo no tenga que repreguntar nada.

1. **Orden de secciones**: se queda como está (Especialidades y Servicios
   antes de Sobre nosotros). No tocar `home.blade.php` en ese sentido.
2. **Frases del banco (sección 3)**: se queda el copy actual tal cual está
   escrito en cada sección hoy. Las alternativas de la sección 3 quedan
   documentadas como banco de respaldo para otras piezas (redes, print), no
   para reemplazar el copy del sitio.
3. **Especialidades**: diseño aprobado (rail de letras + watermark "27" +
   marcador de grupo por letra + línea dorada trazada en hover) y ya
   implementado en `especialidades.blade.php` + `public.css` (ver
   CHANGELOG.md, entrada del 28 ago 2026).
4. **Fix de animación (`.cb-reveal` → scroll-driven `view()`)**: confirmado,
   pero **pendiente** — se aplica al final, junto con el resto de las
   secciones, no ahora. No implementar todavía.
5. **Servicios**: implementado (28 ago 2026, tercera entrada) — se quitó el
   `cb-orb-teal` (color plano) y se reemplazó por `cb-hero-grid` con máscara
   propia + glow ambiental hecho con la foto real de Quirófanos
   (`.cb-services-glow`). El mosaico y el hover-scale de las fotos no se
   tocaron (ver CHANGELOG.md, entrada del 28 ago 2026).

### Siguiente paso pendiente

Rediseño de **Sobre nosotros** y **Contacto**, mismo criterio ya validado en
Especialidades y Servicios: un elemento propio grounded en contenido real
(no decoración porque sí), presentado primero como preview antes de tocar el
Blade/CSS real — mismo flujo que se siguió en las dos secciones anteriores.
Por último, el fix de animación (punto 4) aplicado a todo el sitio de una
vez.
