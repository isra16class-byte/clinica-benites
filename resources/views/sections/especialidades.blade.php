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

    Historial de iteraciones sobre esta sección (28 ago 2026, mismo día,
    3 rondas de feedback — se deja el resumen para no repetir caminos ya
    descartados):
    1. Rediseño inicial: rail de letras a la izquierda + marcador de letra
       inline por grupo + watermark "27" en trazo + línea dorada trazada
       en hover (en vez de solo cambiar el color del borde).
    2. Referencia visual del usuario (mobbin.com — logos dispersos
       alrededor de una cifra grande): el rail fijo se reemplazó por esas
       mismas letras dispersas en los márgenes (`cb-directory-scatter`) y
       se agregó el "27" en primer plano (`cb-stat-callout`).
    3. Feedback: "no me gusta, quítale el alfabeto" — se sacó por completo
       el tratamiento de letras (marcador de grupo inline Y el scatter de
       los márgenes). Se mantiene: el watermark "27" de fondo, la cifra
       grande en primer plano, los divisores verticales entre columnas y
       la línea dorada en hover. El array sigue alfabetizado (es el orden
       de los datos, no un elemento visual), pero ya no se anota con
       letras en ningún lado.
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
    @endphp

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
                    <div class="cb-directory-col">
                        @foreach ($columna as $nombre)
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
