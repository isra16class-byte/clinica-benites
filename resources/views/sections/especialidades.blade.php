{{--
    Sección: Especialidades.

    Tratamiento: directorio editorial (tipo índice de hotel/estudio), no
    grid de tarjetas con ícono — con 27 especialidades reales (ver
    `database/seeders/AreaSeeder.php`, única fuente), un ícono genérico
    por cada una no aporta información y es exactamente el patrón "IA
    genérica" que se quiere evitar. Se descartó también agrupar por
    categoría (quirúrgica/clínica/diagnóstico): esa taxonomía no viene
    confirmada en `Servicios_CB_2026.pdf` y clasificarla a criterio propio
    sería inventar una estructura que no es del cliente — mismo criterio
    de "no inventar datos" ya aplicado en el resto del sitio (ver
    comentarios de `hero.blade.php`). Se ordena alfabéticamente: es la
    única agrupación que no presume nada y además es la más útil para
    alguien que ya sabe qué especialidad busca.

    Layout: 3 columnas en desktop (27 ÷ 3 = 9 filas parejas), 1 en móvil.
    Cada fila es una línea fina + hover que corre el texto y revela una
    flecha — la miga de pan de un despacho de abogados o un menú de
    autor, no un dashboard de SaaS.

    Rediseño (28 ago 2026, tras feedback del usuario: "se ve simple
    comparado con el hero"): se agregaron 3 elementos, todos grounded en
    datos reales, no decoración porque sí (ver docs/PLAN_SITIO_PUBLICO.md
    para el detalle de la investigación/validación con preview HTML):
    - Rail de letras (A, C, E...) a la izquierda: es un índice de
      directorio real (las letras con las que arrancan las 27
      especialidades reales), no un elemento inventado — funciona como el
      "thumb index" de un libro de referencia. Oculto en mobile (no cabe
      con el layout de 1 columna). **Superado por el "segundo ajuste" más
      abajo** (28 ago 2026): el rail como columna fija se reemplazó por
      esas mismas letras dispersas (`cb-directory-scatter`) — se deja esta
      nota para no perder el porqué original de usar letras reales.
    - Marcador de letra inline antes de cada grupo dentro de cada columna:
      rompe visualmente el bloque de "C" (11 de las 27 especialidades
      empiezan con C) para que no se lea como una pared de texto.
    - Watermark "27" en trazo (no relleno) de fondo: es el conteo real y
      confirmado de especialidades (mismo dato que ya usa el lede) hecho
      elemento gráfico, no un número inventado.
    - La fila ahora traza una línea dorada (gradiente teal→dorado→teal, la
      misma paleta del pulso del hero) en hover en vez de solo cambiar el
      color del borde — conecta visualmente con la firma del hero sin
      copiarla literal. Deliberadamente NO se numeró cada especialidad
      (01, 02, 03...): están alfabetizadas, no son una secuencia real.

    Segundo ajuste (28 ago 2026, referencia visual: mobbin.com — pantalla
    de inicio con logos dispersos alrededor de una cifra grande): el
    usuario pidió traer la "sensación general" (dispersión + escala del
    número), no el patrón literal (logos reales por ítem no aplica acá,
    mismo motivo que ya se descartó arriba para íconos por especialidad).
    Dos piezas nuevas, ambas grounded en el mismo dato real (27):
    - `cb-stat-callout`: el "27" pasa a primer plano como cifra grande
      (antes solo vivía como watermark de fondo en trazo fino) — mismo
      tratamiento tipográfico que el "1,428 apps" de la referencia.
    - `cb-directory-scatter`: el rail de letras (columna prolija a la
      izquierda) se reemplaza por esas mismas letras reales dispersas en
      los márgenes de la sección — posiciones calculadas (no al azar) a
      partir del índice de cada letra, alternando lado/rotación, para que
      no se amontonen. Decorativo (aria-hidden, pointer-events:none),
      visible desde lg (antes xl — se bajó tras feedback de que la
      sección se sentía plana en anchos intermedios).

    `$letras`: letras únicas del array de especialidades (para el
    scatter), en el mismo orden en que aparecen. El marcador de grupo por
    columna se calcula inline en el loop (`$letraAnterior`), sin precalcular una
    estructura aparte — el array de especialidades sigue siendo la única
    fuente de datos, igual que antes.
--}}
<section id="especialidades" class="cb-section cb-section--dark relative overflow-hidden">
    <div class="cb-hero-grid" aria-hidden="true" style="mask-image: radial-gradient(ellipse 60% 55% at 85% 100%, black, transparent 70%); -webkit-mask-image: radial-gradient(ellipse 60% 55% at 85% 100%, black, transparent 70%);"></div>
    <p class="cb-directory-watermark" aria-hidden="true">27</p>

    @php
        $especialidades = [
            'Anestesiología y Terapia del Dolor',
            'Auditoría Médica',
            'Cardiología',
            'Cateterismo Cardiaco',
            'Cirugía General y Digestiva',
            'Cirugía Holep de Próstata',
            'Cirugía Oncológica',
            'Cirugía Pediátrica',
            'Cirugía Plástica',
            'Cirugía Torácica',
            'Cirugía Vascular',
            'Coloproctología',
            'Cuidados Críticos',
            'Endocrinología',
            'Gastroenterología',
            'Ginecología',
            'Laparoscopía',
            'Médico Ocupacional',
            'Neurología',
            'Nutrición Clínica',
            'Nutricionista',
            'Oncocirugía Traumatológica',
            'Otorrinolaringología',
            'Pediatría y Neonatología',
            'Terapia Intensiva',
            'Traumatología y Ortopedia',
            'Urología',
        ];
        $columnas = array_chunk($especialidades, (int) ceil(count($especialidades) / 3));
        $letras = array_values(array_unique(array_map(fn ($n) => mb_strtoupper(mb_substr($n, 0, 1)), $especialidades)));

        // Posiciones del scatter: calculadas a partir del índice de cada
        // letra (no al azar, para que el layout sea estable), repartidas
        // 6%→94% de alto, alternando margen izquierdo/derecho y con
        // rotación/offset que varían por índice para que no queden en
        // línea recta — ver comentario arriba del archivo.
        $totalLetras = max(count($letras) - 1, 1);
        $scatter = collect($letras)->values()->map(function ($letra, $i) use ($totalLetras) {
            return [
                'letra' => $letra,
                'top' => round(6 + ($i * (88 / $totalLetras)), 1),
                'side' => $i % 2 === 0 ? 'left' : 'right',
                'edge' => [1, 2.5, 4][$i % 3],
                'rotate' => ($i % 2 === 0 ? -1 : 1) * (3 + ($i % 4) * 2),
            ];
        });
    @endphp

    <div class="cb-directory-scatter hidden lg:block" aria-hidden="true">
        @foreach ($scatter as $s)
            <span class="cb-directory-scatter-letter" style="top:{{ $s['top'] }}%; {{ $s['side'] }}:{{ $s['edge'] }}%; transform: rotate({{ $s['rotate'] }}deg);">{{ $s['letra'] }}</span>
        @endforeach
    </div>

    <div class="relative z-10 mx-auto max-w-7xl px-6 sm:px-10 lg:px-16">
        <div class="cb-section-head cb-reveal">
            <p class="cb-eyebrow">Especialidades</p>
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
                    @php($letraAnterior = null)
                    <div class="cb-directory-col">
                        @foreach ($columna as $nombre)
                            @php($letra = mb_strtoupper(mb_substr($nombre, 0, 1)))
                            @if ($letra !== $letraAnterior)
                                <span class="cb-directory-group-letter">{{ $letra }}</span>
                                @php($letraAnterior = $letra)
                            @endif
                            <div class="cb-directory-row">
                                <span class="cb-directory-name">{{ $nombre }}</span>
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
