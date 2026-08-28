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
--}}
<section id="especialidades" class="cb-section cb-section--dark relative overflow-hidden">
    <div class="cb-hero-grid" aria-hidden="true" style="mask-image: radial-gradient(ellipse 60% 55% at 85% 100%, black, transparent 70%); -webkit-mask-image: radial-gradient(ellipse 60% 55% at 85% 100%, black, transparent 70%);"></div>

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
        @endphp

        <div class="cb-directory cb-reveal" style="animation-delay:.12s">
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
</section>
