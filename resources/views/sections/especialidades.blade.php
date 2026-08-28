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
      con el layout de 1 columna).
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

    `$letras`: letras únicas del array de especialidades (para el rail),
    en el mismo orden en que aparecen. El marcador de grupo por columna se
    calcula inline en el loop (`$letraAnterior`), sin precalcular una
    estructura aparte — el array de especialidades sigue siendo la única
    fuente de datos, igual que antes.
--}}
<section id="especialidades" class="cb-section cb-section--dark relative overflow-hidden">
    <div class="cb-hero-grid" aria-hidden="true" style="mask-image: radial-gradient(ellipse 60% 55% at 85% 100%, black, transparent 70%); -webkit-mask-image: radial-gradient(ellipse 60% 55% at 85% 100%, black, transparent 70%);"></div>
    <p class="cb-directory-watermark" aria-hidden="true">27</p>

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
        @endphp

        <div class="cb-directory-wrap cb-reveal" style="animation-delay:.12s">
            <div class="cb-directory-rail hidden md:flex" aria-hidden="true">
                @foreach ($letras as $letra)
                    <span class="cb-directory-rail-letter">{{ $letra }}</span>
                @endforeach
            </div>

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
