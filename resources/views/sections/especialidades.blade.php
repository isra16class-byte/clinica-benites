{{--
    Sección: Especialidades.

    Tratamiento: directorio editorial (tipo índice de hotel/estudio), con
    un ícono por especialidad (agregado a pedido del usuario el 28 ago
    2026 — antes se había descartado a propósito, ver histórico de esta
    sección abajo). Para que no se sienta como "ícono genérico por fila",
    los 27 nombres se agrupan por sistema/órgano clínico real (cardíaco,
    óseo, urológico, digestivo, etc.) y comparten ícono dentro del mismo
    grupo — ej. Cardiología y Cateterismo Cardiaco usan el mismo corazón,
    Traumatología y Ortopedia y Oncocirugía Traumatológica el mismo hueso.
    27 nombres, 20 íconos distintos (`$iconos` abajo, trazo fino, mismo
    estilo que la flecha de hover ya existente; Neurología usa un rayo
    de impulso nervioso en vez de un cerebro — el cerebro de dos lóbulos
    no se leía bien a 20px, se veía como un círculo partido). Se descartó
    agrupar
    visualmente por categoría (quirúrgica/clínica/diagnóstico): esa
    taxonomía no viene confirmada en `Servicios_CB_2026.pdf` — el ícono
    agrupa por afinidad clínica obvia (mismo órgano), no inventa una
    clasificación nueva del negocio. Se ordena alfabéticamente: es la
    única agrupación de texto que no presume nada y es la más útil para
    alguien que ya sabe qué especialidad busca.

    Fondo: blanco/ivory (a pedido del usuario, 28 ago 2026) — mismo
    tratamiento `cb-section--light` que ya usa "Sobre nosotros", con los
    overrides de color correspondientes en `public.css` (sección
    "Especialidades — directorio editorial", bloque final "fondo claro").

    Layout: 3 columnas en desktop (27 ÷ 3 = 9 filas parejas), 1 en móvil.
    Cada fila es una línea fina + ícono + hover que corre el texto y
    revela una flecha.

    Historial de iteraciones previas (28 ago 2026, mismo día):
    1. Rediseño inicial: rail de letras a la izquierda + marcador de letra
       inline por grupo + watermark "27" en trazo + línea dorada trazada
       en hover.
    2. Referencia mobbin.com: rail fijo → letras dispersas en los márgenes
       (`cb-directory-scatter`) + "27" en primer plano (`cb-stat-callout`).
    3. Feedback "quítale el alfabeto": se sacó todo el tratamiento de
       letras. Quedó: watermark "27", cifra grande, divisores verticales,
       línea dorada en hover.
    4. Feedback (esta ronda): ícono por especialidad + fondo blanco.
--}}
<section id="especialidades" class="cb-section cb-section--light relative">
    <div class="overflow-hidden" aria-hidden="true">
        <div class="cb-hero-grid" aria-hidden="true" style="mask-image: radial-gradient(ellipse 60% 55% at 85% 100%, black, transparent 70%); -webkit-mask-image: radial-gradient(ellipse 60% 55% at 85% 100%, black, transparent 70%);"></div>
        <p class="cb-directory-watermark" aria-hidden="true">27</p>
    </div>

    @php
        // Trazos de ícono por grupo clínico (viewBox 0 0 24 24, mismo
        // estilo de trazo que `.cb-directory-arrow`). Un slug puede
        // repetirse entre especialidades del mismo sistema/órgano.
        $iconos = [
            'moon' => ['M20 14.5A8.5 8.5 0 1 1 9.5 4a7 7 0 0 0 10.5 10.5Z'],
            'clipboard' => ['M9 4h6a1 1 0 0 1 1 1v1h1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h1V5a1 1 0 0 1 1-1Z', 'M9 12l2 2 4-4'],
            'heart' => ['M12 20.5C12 20.5 4.5 15.8 4.5 10.2 4.5 7.3 6.8 5 9.6 5c1.4 0 2.6.6 3.4 1.6C13.8 5.6 15 5 16.4 5c2.8 0 5.1 2.3 5.1 5.2 0 5.6-7.5 10.3-9.5 10.3Z'],
            'scalpel' => ['M19 5L9 15', 'M15 5l4 4', 'M4 20l5-1 1-5-5 1Z'],
            'drop' => ['M12 3c-3 5-6 8.7-6 12a6 6 0 0 0 12 0c0-3.3-3-7-6-12Z'],
            'ribbon' => ['M9 3l3 4.5L15 3', 'M8.7 8.2 6 12a3 3 0 1 0 4.2 4.2L12 13.7l1.8 2.5A3 3 0 1 0 18 12l-2.7-3.8'],
            'sparkle' => ['M12 3l1.6 4.4L18 9l-4.4 1.6L12 15l-1.6-4.4L6 9l4.4-1.6Z'],
            'lungs' => ['M12 4v6', 'M12 10c-1.2 0-2.4 1-3.2 2.8-.9 2-1 5-.2 6.6.8 1.6 3.4 1.2 3.4-1V10Z', 'M12 10c1.2 0 2.4 1 3.2 2.8.9 2 1 5 .2 6.6-.8 1.6-3.4 1.2-3.4-1V10Z'],
            'vein' => ['M6 4v6c0 3 2 4 4 4h4c2 0 4 1 4 4v2', 'M6 10c0 3 2 4 4 4'],
            'spiral' => ['M12 4a8 8 0 1 0 8 8 6 6 0 1 0-6 6 4 4 0 1 0 4-4'],
            'monitor' => ['M3 12h4l1.5-5 3 10 2-8 1.5 3H21'],
            'molecule' => ['M6 7a2 2 0 1 0 .01 0', 'M18 7a2 2 0 1 0 .01 0', 'M12 17a2 2 0 1 0 .01 0', 'M7.8 8.1 10.4 15', 'M16.2 8.1 13.6 15', 'M8 7h8'],
            'stomach' => ['M7 5c-1.7 0-3 1.6-3 4.2 0 4 2 5.3 2 8.3 0 1.9 1.8 3.5 4 3.5 2.8 0 4.7-1.7 5.6-3.6.8-1.7 2.4-2 2.4-4.4 0-3-2.2-5-5-5-1 0-2 .3-2.7.9C9.3 6.4 8.2 5 7 5Z'],
            'venus' => ['M12 3.5a5 5 0 1 0 0 10 5 5 0 0 0 0-10Z', 'M12 13.5V21', 'M9 18h6'],
            'camera' => ['M4 8.5h3l1.5-2h7l1.5 2h3a1 1 0 0 1 1 1V18a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9.5a1 1 0 0 1 1-1Z', 'M12 12.3a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z'],
            'briefcase' => ['M4 8h16a1 1 0 0 1 1 1v9a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1Z', 'M9 8V6a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2', 'M3 13h18'],
            'nervio' => ['M13 3 5.5 13h5l-1 8L18 10h-5l1-7Z'],
            'leaf' => ['M5 19c0-8 6-14 14-14 0 8-6 14-14 14Z', 'M5 19c1.8-3.6 4-6.2 8-8'],
            'ear' => ['M9 12.5c0-4.7 3.1-8.5 7-8.5 3.6 0 6.5 3.1 6.5 7 0 3.2-2.1 4.8-2.1 7.3 0 2-1.5 3.7-3.4 3.7-1.6 0-2.9-1.2-2.9-2.8', 'M9 12.5c0 2.6.9 4.7 2.6 6'],
            'baby' => ['M12 4.5a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z', 'M6.5 20c0-3.9 2.5-6.5 5.5-6.5s5.5 2.6 5.5 6.5'],
            'bone' => ['M5.5 8.5a2 2 0 1 1 3-3l10 10a2 2 0 1 1-3 3l-10-10Z', 'M8.5 5.5a2 2 0 1 1-3 3', 'M15.5 18.5a2 2 0 1 0 3-3'],
        ];

        $especialidades = [
            ['nombre' => 'Anestesiología y Terapia del Dolor', 'icono' => 'moon'],
            ['nombre' => 'Auditoría Médica', 'icono' => 'clipboard'],
            ['nombre' => 'Cardiología', 'icono' => 'heart'],
            ['nombre' => 'Cateterismo Cardiaco', 'icono' => 'heart'],
            ['nombre' => 'Cirugía General y Digestiva', 'icono' => 'scalpel'],
            ['nombre' => 'Cirugía Holep de Próstata', 'icono' => 'drop'],
            ['nombre' => 'Cirugía Oncológica', 'icono' => 'ribbon'],
            ['nombre' => 'Cirugía Pediátrica', 'icono' => 'baby'],
            ['nombre' => 'Cirugía Plástica', 'icono' => 'sparkle'],
            ['nombre' => 'Cirugía Torácica', 'icono' => 'lungs'],
            ['nombre' => 'Cirugía Vascular', 'icono' => 'vein'],
            ['nombre' => 'Coloproctología', 'icono' => 'spiral'],
            ['nombre' => 'Cuidados Críticos', 'icono' => 'monitor'],
            ['nombre' => 'Endocrinología', 'icono' => 'molecule'],
            ['nombre' => 'Gastroenterología', 'icono' => 'stomach'],
            ['nombre' => 'Ginecología', 'icono' => 'venus'],
            ['nombre' => 'Laparoscopía', 'icono' => 'camera'],
            ['nombre' => 'Médico Ocupacional', 'icono' => 'briefcase'],
            ['nombre' => 'Neurología', 'icono' => 'nervio'],
            ['nombre' => 'Nutrición Clínica', 'icono' => 'leaf'],
            ['nombre' => 'Nutricionista', 'icono' => 'leaf'],
            ['nombre' => 'Oncocirugía Traumatológica', 'icono' => 'bone'],
            ['nombre' => 'Otorrinolaringología', 'icono' => 'ear'],
            ['nombre' => 'Pediatría y Neonatología', 'icono' => 'baby'],
            ['nombre' => 'Terapia Intensiva', 'icono' => 'monitor'],
            ['nombre' => 'Traumatología y Ortopedia', 'icono' => 'bone'],
            ['nombre' => 'Urología', 'icono' => 'drop'],
        ];
        $columnas = array_chunk($especialidades, (int) ceil(count($especialidades) / 3));
    @endphp

    <div class="relative z-10 mx-auto max-w-7xl px-6 sm:px-10 lg:px-16">
        <div class="cb-section-head cb-reveal">
            <p class="cb-eyebrow cb-eyebrow--light">Especialidades</p>
            <h2 class="cb-headline cb-headline--section">Un equipo para cada etapa del cuidado.</h2>
            <p class="cb-section-lede">
                27 especialidades médicas y quirúrgicas bajo un mismo techo,
                coordinadas dentro de la misma clínica — sin derivar al
                paciente a centros externos para cada consulta.
            </p>
        </div>

        <div class="cb-stat-callout cb-reveal" style="animation-delay:.08s">
            <span class="cb-stat-number">27</span>
            <span class="cb-stat-label">especialidades<br>bajo un mismo techo</span>
        </div>

        <div class="cb-directory-wrap cb-reveal" style="animation-delay:.16s">
            <div class="cb-directory">
                @foreach ($columnas as $columna)
                    <div class="cb-directory-col">
                        @foreach ($columna as $item)
                            <div class="cb-directory-row">
                                <span class="cb-directory-left">
                                    <svg class="cb-directory-icon" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        @foreach ($iconos[$item['icono']] as $d)
                                            <path d="{{ $d }}"/>
                                        @endforeach
                                    </svg>
                                    <span class="cb-directory-name">{{ $item['nombre'] }}</span>
                                </span>
                                <svg class="cb-directory-arrow" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M5 12h13M13 6l6 6-6 6"/>
                                </svg>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
