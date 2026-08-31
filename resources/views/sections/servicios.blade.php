{{--
    Sección: Servicios / áreas de mayor complejidad.

    Tratamiento: mosaico asimétrico (bento grid) con las 5 fotos reales ya
    en el repo (`public/images/hero-*.jpg`) — nunca usadas todavía fuera
    del hero (que solo usa `hero-quirofano.jpg`). Se prefirió esto a un
    grid uniforme de 3 u 4 columnas con ícono+texto: con fotografía real
    disponible, no tiene sentido volver a un ícono genérico — la foto real
    es justamente lo que distingue a una clínica real de una plantilla.
    El tile de Quirófanos es el más grande (2×2): es el único de los 5 que
    ya aparece en el hero y en el trust-strip ("Quirófanos · UCI · UCIN"),
    así que es el ancla visual natural de la sección.

    Los textos de cada tile describen qué hace la especialidad en términos
    generales (no cifras, no promesas puntuales) — mismo criterio de no
    inventar datos que el resto del sitio.

    Rediseño (28 ago 2026, mismo criterio ya validado en Especialidades —
    ver docs/PLAN_SITIO_PUBLICO.md sección 4 y 6): se quitó el
    `cb-orb-teal` (blob de color plano) que tenía la sección — es
    exactamente el cliché "gradiente healthcare tech abstracto" que el
    plan marca como algo a evitar activamente. En su lugar:
    - `cb-hero-grid` (el mismo plano técnico del hero), con una máscara
      en la esquina opuesta a la de Especialidades (arriba-izquierda acá,
      abajo-derecha allá) — mismo motivo reutilizado en dosis distinta
      por sección, no un fondo repetido idéntico.
    - Un glow ambiental detrás del encabezado, hecho con la misma foto
      real que ya usa el tile de Quirófanos (`hero-quirofano.jpg`), no
      un color inventado — mismo mecanismo (`filter: blur`, opacidad
      baja) que ya usa `.cb-hero-video-glow` en el hero, aplicado acá a
      una imagen estática. Queda grounded en contenido real, como pide
      el criterio de la sección 4 del plan ("glow/blur de las fotos
      reales... en vez de un orb de color plano").
    El mosaico y el hover-scale de las fotos no se tocaron: el plan
    (sección 5.5) es explícito en que ya tienen suficiente movimiento
    propio y no hace falta sumarles más.
--}}
<section id="servicios" class="cb-section cb-section--dark relative">
    <div class="overflow-hidden" aria-hidden="true">
        <div class="cb-hero-grid" aria-hidden="true" style="mask-image: radial-gradient(ellipse 55% 50% at 10% 0%, black, transparent 70%); -webkit-mask-image: radial-gradient(ellipse 55% 50% at 10% 0%, black, transparent 70%);"></div>
        <img src="{{ asset('images/hero-quirofano.jpg') }}" alt="" aria-hidden="true" class="cb-services-glow">
    </div>

    <div class="relative z-10 mx-auto max-w-7xl px-6 sm:px-10 lg:px-16">
        <div class="cb-section-head cb-reveal">
            <p class="cb-eyebrow">Servicios</p>
            <h2 class="cb-headline cb-headline--section">Infraestructura de alta complejidad, en un solo lugar.</h2>
            <p class="cb-section-lede">
                Áreas equipadas para acompañar al paciente desde el
                diagnóstico hasta la recuperación, sin salir de la clínica.
            </p>
        </div>

        @php
            $servicios = [
                [
                    'img' => 'hero-quirofano.jpg',
                    'eyebrow' => 'Cirugía',
                    'titulo' => 'Quirófanos',
                    'texto' => 'Central quirúrgica equipada para múltiples especialidades, de la cirugía general a la de alta complejidad.',
                    'size' => 'lg',
                ],
                [
                    'img' => 'hero-uci.jpg',
                    'eyebrow' => 'Cuidados críticos',
                    'titulo' => 'UCI · UCIN',
                    'texto' => 'Monitoreo permanente para pacientes en estado crítico, adultos y neonatales.',
                    'size' => 'sm',
                ],
                [
                    'img' => 'hero-cardiologia.jpg',
                    'eyebrow' => 'Cardiología',
                    'titulo' => 'Cardiología y cateterismo',
                    'texto' => 'Diagnóstico y tratamiento cardiovascular, incluido cateterismo cardiaco.',
                    'size' => 'sm',
                ],
                [
                    'img' => 'hero-neonatologia.jpg',
                    'eyebrow' => 'Pediatría',
                    'titulo' => 'Neonatología',
                    'texto' => 'Atención especializada para los pacientes más pequeños, desde el nacimiento.',
                    'size' => 'sm',
                ],
                [
                    'img' => 'hero-centro-imagen.jpg',
                    'eyebrow' => 'Diagnóstico',
                    'titulo' => 'Centro de imagen',
                    'texto' => 'Estudios de imagen como apoyo al diagnóstico clínico y a la planificación quirúrgica.',
                    'size' => 'sm',
                ],
            ];
        @endphp

        <div class="cb-services-grid cb-reveal" style="animation-delay:.12s">
            @foreach ($servicios as $s)
                <div class="cb-service-tile cb-service-tile--{{ $s['size'] }}">
                    <img src="{{ asset('images/'.$s['img']) }}" alt="{{ $s['titulo'] }}" class="cb-service-tile-img" loading="lazy">
                    <div class="cb-service-tile-scrim" aria-hidden="true"></div>
                    <div class="cb-service-tile-content">
                        <span class="cb-service-tile-eyebrow">{{ $s['eyebrow'] }}</span>
                        <h3 class="cb-service-tile-title">{{ $s['titulo'] }}</h3>
                        <p class="cb-service-tile-text">{{ $s['texto'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{--
            Franja de ambulancia: dato ya confirmado en el trust-strip del
            hero ("Ambulancia propia") — se repite acá como cierre de la
            sección en vez de sumar un 6to tile al mosaico (rompería el
            balance visual 1 grande + 4 chicos).
        --}}
        <div class="cb-services-strip cb-reveal" style="animation-delay:.2s">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M3 16V9a1 1 0 0 1 1-1h9l4 4h3a1 1 0 0 1 1 1v3"/>
                <path d="M3 16h1.75M19.25 16H21M8.75 16h6.5"/>
                <path d="M11 8.5v3.5M9.25 10.25h3.5"/>
                <circle cx="7.5" cy="17.5" r="1.75"/>
                <circle cx="17.25" cy="17.5" r="1.75"/>
            </svg>
            <p>Servicio de ambulancia propio para el traslado de nuestros pacientes.</p>
        </div>
    </div>
</section>
