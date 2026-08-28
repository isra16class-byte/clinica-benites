{{--
    Sección: Portada (hero).

    Elemento distintivo de esta sección: la "línea de pulso" (SVG animado,
    más abajo) — une dos ideas del brief en un solo trazo: "precisión
    quirúrgica" (se dibuja como un plano técnico al cargar la página) y
    "calidez humana" (late, con un pulso dorado que la recorre en loop).
    Es la única pieza de movimiento realmente protagonista de la sección;
    el resto (aparición escalonada del texto, aura de fondo) es deliberadamente
    discreto para no competir con ella — ver DISEÑO.md sobre restraint, mismo
    criterio aplicado acá aunque este archivo sea del sitio público.

    Números y afirmaciones (26 especialidades, servicios listados) tomados
    tal cual del material real de marketing de la clínica
    (`Servicios_CB_2026.pdf`, compartido por el usuario) — no se inventó
    ningún dato (ni horario de emergencias, ni año de fundación, ni teléfono:
    lo que no está confirmado en la fuente, no se afirma en el texto).
--}}
<section id="inicio" class="cb-hero relative overflow-hidden">
    <div class="cb-hero-grid" aria-hidden="true"></div>
    <div class="cb-orb cb-orb-teal" aria-hidden="true"></div>
    <div class="cb-orb cb-orb-gold" aria-hidden="true"></div>

    {{--
        Columna derecha del hero: video único en loop, "de fondo" —
        27 ago 2026 (segunda entrada del día), a pedido del usuario tras
        ver el primer resultado (marco tipo tarjeta, vertical, al costado
        del texto): "por qué lo hiciste vertical? lo podemos hacer medio
        transparente y más grande, no importa que toque las letras de
        Precisión quirúrgica. Calidez humana., ya que el video va a estar
        como de fondo". Confirmado el alcance antes de tocar CSS: "más
        grande que ahora y superpuesto al título, pero sin cubrir todo el
        hero (botones/trust-strip siguen sobre fondo sólido)".
        Cambios sobre la versión anterior (ver `public.css` para el
        detalle completo de cada regla):
        - `.cb-hero-side` crece bastante (de min(21rem,27vw) a
          min(34rem,42vw)) y sube (`top` de 12.5rem a 7rem) para quedar
          detrás del título en vez de al costado.
        - `.cb-hero-slideshow` ya no es una tarjeta (se sacaron
          `border-radius`/`box-shadow`/`outline`): ahora es semitransparente
          (`opacity: 0.5`) con un `mask-image` radial que desvanece los 4
          bordes, para que nunca se lea como una foto pegada encima del
          texto sino como una textura de fondo.
        - `aspect-ratio` pasa de 4/5 (vertical, heredado sin querer del
          slideshow de fotos anterior) a 4/3 (más horizontal, coherente
          con el video real de 16:9 y con el uso "de fondo").
        El texto sigue arriba en el z-index (`.cb-hero-content` tiene
        z-10, `.cb-hero-side` no define z-index) — el video puede
        meterse debajo del título sin taparlo. Único límite que se
        mantuvo: no invadir la fila de botones ni la franja de confianza
        — verificado con Playwright (bounding boxes reales) en la misma
        matriz de anchos/alturas de siempre, ver `public.css`.
        Requisitos técnicos del `<video>`: `muted` (obligatorio para que
        `autoplay` funcione en todos los navegadores), `autoplay`,
        `loop`, `playsinline` (evita que iOS lo abra en pantalla
        completa), y `poster` (reutiliza `hero-quirofano.jpg`, la misma
        foto que ya se usaba como primer slide) para que se vea algo
        mientras el video carga.
        `.cb-hero-video-poster` es un `<img>` con la misma foto, superpuesto
        y oculto por CSS: no es redundante con el atributo `poster` del
        video (ese solo se ve *antes* de que cargue/arranque) — sirve
        para el caso `prefers-reduced-motion`, donde el video se oculta
        del todo (`display: none`) y esta imagen fija lo reemplaza, mismo
        criterio que ya usaba el slideshow (congelarse en la primera
        foto) — sin necesitar JS para pausar el video (el sitio público
        no tiene JS propio, es deliberadamente CSS puro, ver DISEÑO.md).
        **Pendiente confirmar por el usuario en su propio entorno real**
        (en particular, si el punto de fusión con el título se ve bien
        con el video corriendo — acá se verificó con Playwright, que en
        este sandbox no reproduce H.264 en vivo, ver nota en MEMORIA.md).

        28 ago 2026 (tercera vuelta) — a pedido del usuario, que marcó con
        un recuadro sobre una captura real dónde quería el video: mucho
        más grande, cubriendo casi toda la mitad derecha, pegado al borde
        real de la pantalla (no al borde del bloque de texto centrado como
        antes), sin bajar de la fila de iconos del trust-strip. Solo
        cambiaron los valores de `.cb-hero-side` en `public.css` (top,
        right, width, y los 2 escalones de compresión) — ver el comentario
        grande ahí para el detalle completo de la medición y verificación.
        El resto (mask-image de bordes desvanecidos, aspect-ratio 4/3,
        opacidad) no se tocó, sigue siendo lo que ya le había gustado al
        usuario.
    --}}
    <div class="cb-hero-side hidden xl:block" aria-hidden="true">
        <div class="cb-hero-slideshow">
            <video
                class="cb-hero-video"
                src="{{ asset('videos/hero-clinica.mp4') }}"
                poster="{{ asset('images/hero-quirofano.jpg') }}"
                muted
                autoplay
                loop
                playsinline
            ></video>
            <img class="cb-hero-video-poster" src="{{ asset('images/hero-quirofano.jpg') }}" alt="">
        </div>
    </div>

    {{--
        Espaciado vertical (pt/pb/gaps entre bloques) movido a la clase
        `.cb-hero-content` en vez de utilidades Tailwind fijas (pt-32/pb-20):
        en pantallas de poca altura (laptops 1366x768, 1440x900, 1536x864 —
        las 3 resoluciones más comunes en la región, medido con Playwright)
        el contenido fijo del hero (~830px) no entraba en el viewport real
        (~650-780px, descontando la barra del navegador) y la franja de
        confianza (trust-strip) quedaba cortada por el fold al 100% de zoom
        — reportado por el usuario, confirmado con medición real antes de
        tocar el CSS. `.cb-hero-content` comprime los mt/pt del bloque con
        `@media (max-height: ...)` (ver public.css) para esos casos, sin
        afectar pantallas altas donde ya entraba bien.
    --}}
    <div class="cb-hero-content relative z-10 mx-auto flex min-h-screen max-w-7xl flex-col justify-center px-6 sm:px-10 lg:px-16">
        <p class="cb-eyebrow cb-reveal" style="animation-delay:.05s">
            Clínica privada &middot; Guayaquil
        </p>

        <h1 class="cb-headline cb-reveal" style="animation-delay:.18s">
            <span class="block">Precisión quirúrgica.</span>
            <span class="cb-headline-accent block">Calidez humana.</span>
        </h1>

        {{-- Línea de pulso --}}
        <div class="cb-pulse-wrap cb-reveal" style="animation-delay:.4s" aria-hidden="true">
            <svg viewBox="0 0 800 120" preserveAspectRatio="none" class="h-auto w-full">
                <defs>
                    <linearGradient id="cb-pulse-gradient" x1="0" y1="0" x2="1" y2="0">
                        <stop offset="0%" stop-color="#4aa88c"/>
                        <stop offset="55%" stop-color="#ddc48c"/>
                        <stop offset="100%" stop-color="#4aa88c"/>
                    </linearGradient>
                </defs>
                <path
                    pathLength="1"
                    class="cb-pulse-path"
                    d="M2,60 L150,60 L167,22 L184,98 L201,38 L218,60 L270,60 C 330,60 360,32 400,52 C 440,72 470,50 530,50 L 798,50"
                />
                <circle class="cb-pulse-blip" r="4.5"/>
            </svg>
        </div>

        <p class="cb-subheadline cb-reveal" style="animation-delay:.5s">
            Más de 26 especialidades médicas y quirúrgicas, quirófanos y
            cuidados intensivos de alta complejidad, en un solo centro
            privado de Guayaquil.
        </p>

        <div class="cb-cta-row cb-reveal" style="animation-delay:.62s">
            {{--
                TODO: reemplazar por el número real de WhatsApp de la clínica
                antes de publicar. El mensaje precargado (?text=) ya queda
                andando solo con reemplazar el número — no depende de eso.
            --}}
            <a href="https://wa.me/593000000000?text=Hola%2C%20deseo%20agendar%20una%20cita%20en%20Cl%C3%ADnica%20Benites." class="cb-btn-primary" target="_blank" rel="noopener">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M4 20l1.4-4.1A8 8 0 1 1 9 18.6z"/>
                    <path d="M9 10.5c.4 1.8 1.7 3.1 3.5 3.5"/>
                </svg>
                Agendar por WhatsApp
            </a>
            {{-- TODO: reemplazar por el número real de recepción antes de publicar. --}}
            <a href="tel:+593000000000" class="cb-btn-ghost">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M5 4h3l1.6 4.3-2 1.5a12 12 0 0 0 5.6 5.6l1.5-2L19 15v3a2 2 0 0 1-2.2 2A16 16 0 0 1 3 5.2 2 2 0 0 1 5 4Z"/>
                </svg>
                Llamar a recepción
            </a>
        </div>

        <dl class="cb-trust-strip cb-reveal" style="animation-delay:.78s">
            <div class="cb-trust-item">
                <span class="cb-trust-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="21" height="21" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="8" height="8" rx="1.5"/>
                        <rect x="13" y="3" width="8" height="8" rx="1.5"/>
                        <rect x="3" y="13" width="8" height="8" rx="1.5"/>
                        <rect x="13" y="13" width="8" height="8" rx="1.5"/>
                    </svg>
                </span>
                <div>
                    <dt class="cb-trust-title">+26 especialidades</dt>
                    <dd class="cb-trust-caption">Médicas y quirúrgicas, bajo un mismo techo</dd>
                </div>
            </div>

            <div class="cb-trust-item">
                <span class="cb-trust-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="21" height="21" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 20V9a1 1 0 0 1 1-1h3l2-3h4l2 3h3a1 1 0 0 1 1 1v11"/>
                        <path d="M9 20v-4a3 3 0 0 1 6 0v4"/>
                    </svg>
                </span>
                <div>
                    <dt class="cb-trust-title">Quirófanos &middot; UCI &middot; UCIN</dt>
                    <dd class="cb-trust-caption">Central de quirófanos y cuidados críticos</dd>
                </div>
            </div>

            <div class="cb-trust-item">
                <span class="cb-trust-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="21" height="21" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 16V9a1 1 0 0 1 1-1h9l4 4h3a1 1 0 0 1 1 1v3"/>
                        <path d="M3 16h1.75M19.25 16H21M8.75 16h6.5"/>
                        <path d="M11 8.5v3.5M9.25 10.25h3.5"/>
                        <circle cx="7.5" cy="17.5" r="1.75"/>
                        <circle cx="17.25" cy="17.5" r="1.75"/>
                    </svg>
                </span>
                <div>
                    <dt class="cb-trust-title">Ambulancia propia</dt>
                    <dd class="cb-trust-caption">Servicio de traslado para nuestros pacientes</dd>
                </div>
            </div>

            <div class="cb-trust-item">
                <span class="cb-trust-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="21" height="21" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="9"/>
                        <path d="M12 7.5v9M7.5 12h9"/>
                    </svg>
                </span>
                <div>
                    <dt class="cb-trust-title">Atención de emergencias</dt>
                    <dd class="cb-trust-caption">Equipo listo para casos urgentes</dd>
                </div>
            </div>
        </dl>
    </div>

    <a href="#especialidades" class="cb-scroll-cue">
        Conocer la clínica
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M12 4v15M6 13l6 6 6-6"/>
        </svg>
    </a>
</section>
