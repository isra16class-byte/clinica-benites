<?php

namespace App\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class Especialidades
{
    private static array $iconos = [
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

    private static function especialidades(): array
    {
        $lista = [
            ['nombre' => 'Anestesiología y Terapia del Dolor', 'icono' => 'moon', 'descripcion' => 'Especialidad encargada de garantizar la seguridad y el confort del paciente durante procedimientos quirúrgicos, además del manejo integral del dolor agudo y crónico mediante técnicas farmacológicas e intervencionistas.'],
            ['nombre' => 'Auditoría Médica', 'icono' => 'clipboard', 'descripcion' => 'Área dedicada a la revisión y control de calidad de los procesos asistenciales, verificando que la atención brindada cumpla con protocolos clínicos, normativas vigentes y estándares de seguridad del paciente.'],
            ['nombre' => 'Cardiología', 'icono' => 'heart', 'descripcion' => 'Diagnóstico, tratamiento y seguimiento de enfermedades del corazón y del sistema circulatorio, incluyendo hipertensión, arritmias, insuficiencia cardíaca y prevención de enfermedad coronaria.'],
            ['nombre' => 'Cateterismo Cardiaco', 'icono' => 'heart', 'descripcion' => 'Procedimiento diagnóstico e intervencionista mínimamente invasivo que permite evaluar las arterias coronarias y tratar obstrucciones mediante angioplastia o colocación de stents.'],
            ['nombre' => 'Cirugía General y Digestiva', 'icono' => 'scalpel', 'descripcion' => 'Tratamiento quirúrgico de patologías del aparato digestivo, pared abdominal y órganos relacionados, abarcando desde procedimientos electivos hasta emergencias abdominales.'],
            ['nombre' => 'Cirugía Holep de Próstata', 'icono' => 'drop', 'descripcion' => 'Técnica quirúrgica mínimamente invasiva con láser para el tratamiento de la hiperplasia prostática benigna, con menor sangrado y recuperación más rápida que la cirugía convencional.'],
            ['nombre' => 'Cirugía Oncológica', 'icono' => 'ribbon', 'descripcion' => 'Tratamiento quirúrgico de tumores malignos en distintos órganos, con enfoque en la resección completa de la lesión y la preservación de la función y calidad de vida del paciente.'],
            ['nombre' => 'Cirugía Pediátrica', 'icono' => 'baby', 'descripcion' => 'Atención quirúrgica especializada para recién nacidos, niños y adolescentes, adaptando técnicas y cuidados a las particularidades anatómicas y fisiológicas de cada etapa de crecimiento.'],
            ['nombre' => 'Cirugía Plástica', 'icono' => 'sparkle', 'descripcion' => 'Procedimientos reconstructivos y estéticos orientados a restaurar la forma y función de tejidos afectados por trauma, enfermedad o malformaciones congénitas, así como intervenciones electivas.'],
            ['nombre' => 'Cirugía Torácica', 'icono' => 'lungs', 'descripcion' => 'Tratamiento quirúrgico de patologías de pulmones, esófago, mediastino y pared torácica, incluyendo procedimientos oncológicos y no oncológicos del tórax.'],
            ['nombre' => 'Cirugía Vascular', 'icono' => 'vein', 'descripcion' => 'Diagnóstico y tratamiento quirúrgico de enfermedades de arterias y venas, como várices, aneurismas y obstrucciones circulatorias periféricas.'],
            ['nombre' => 'Coloproctología', 'icono' => 'spiral', 'descripcion' => 'Especialidad enfocada en el diagnóstico y tratamiento de enfermedades del colon, recto y ano, tanto por vía médica como quirúrgica.'],
            ['nombre' => 'Cuidados Críticos', 'icono' => 'monitor', 'descripcion' => 'Atención especializada a pacientes en estado grave que requieren monitoreo constante y soporte vital avanzado, generalmente en unidades de cuidados intensivos.'],
            ['nombre' => 'Endocrinología', 'icono' => 'molecule', 'descripcion' => 'Diagnóstico y manejo de trastornos hormonales y metabólicos, incluyendo diabetes, enfermedades de tiroides y alteraciones del crecimiento.'],
            ['nombre' => 'Gastroenterología', 'icono' => 'stomach', 'descripcion' => 'Diagnóstico y tratamiento de enfermedades del sistema digestivo, como reflujo, úlceras, enfermedad inflamatoria intestinal y afecciones hepáticas.'],
            ['nombre' => 'Ginecología', 'icono' => 'venus', 'descripcion' => 'Atención integral de la salud del sistema reproductivo femenino, incluyendo control ginecológico preventivo, tratamiento de patologías y seguimiento hormonal.'],
            ['nombre' => 'Laparoscopía', 'icono' => 'camera', 'descripcion' => 'Técnica quirúrgica mínimamente invasiva que utiliza pequeñas incisiones y cámara para realizar procedimientos abdominales con menor dolor y recuperación más rápida.'],
            ['nombre' => 'Médico Ocupacional', 'icono' => 'briefcase', 'descripcion' => 'Evaluación y seguimiento de la salud de trabajadores en relación con su entorno laboral, incluyendo exámenes preventivos y manejo de enfermedades relacionadas con el trabajo.'],
            ['nombre' => 'Neurología', 'icono' => 'nervio', 'descripcion' => 'Diagnóstico y tratamiento de enfermedades del sistema nervioso central y periférico, como migrañas, epilepsia, accidentes cerebrovasculares y trastornos neurodegenerativos.'],
            ['nombre' => 'Nutrición Clínica', 'icono' => 'leaf', 'descripcion' => 'Evaluación y manejo nutricional de pacientes con enfermedades que requieren un abordaje dietético específico, como diabetes, insuficiencia renal u obesidad.'],
            ['nombre' => 'Nutricionista', 'icono' => 'leaf', 'descripcion' => 'Orientación en hábitos alimenticios saludables, diseño de planes nutricionales personalizados y acompañamiento en objetivos de salud y bienestar.'],
            ['nombre' => 'Oncocirugía Traumatológica', 'icono' => 'bone', 'descripcion' => 'Tratamiento quirúrgico especializado de tumores óseos y de tejidos blandos del sistema musculoesquelético, con enfoque en preservar la función del miembro afectado.'],
            ['nombre' => 'Otorrinolaringología', 'icono' => 'ear', 'descripcion' => 'Diagnóstico y tratamiento de enfermedades de oído, nariz y garganta, incluyendo trastornos auditivos, sinusitis y afecciones de la vía respiratoria superior.'],
            ['nombre' => 'Pediatría y Neonatología', 'icono' => 'baby', 'descripcion' => 'Atención integral de la salud de recién nacidos, niños y adolescentes, incluyendo control de crecimiento, vacunación y manejo de enfermedades propias de la infancia.'],
            ['nombre' => 'Terapia Intensiva', 'icono' => 'monitor', 'descripcion' => 'Monitoreo y soporte vital especializado para pacientes con enfermedades graves o inestables, con equipos y personal dedicados a la atención crítica continua.'],
            ['nombre' => 'Traumatología y Ortopedia', 'icono' => 'bone', 'descripcion' => 'Diagnóstico y tratamiento de lesiones y enfermedades del sistema musculoesquelético, incluyendo fracturas, lesiones deportivas y patologías articulares.'],
            ['nombre' => 'Urología', 'icono' => 'drop', 'descripcion' => 'Diagnóstico y tratamiento de enfermedades del aparato urinario y del sistema reproductor masculino, incluyendo cálculos renales, infecciones urinarias y afecciones prostáticas.'],
        ];

        foreach ($lista as $index => $item) {
            $lista[$index]['slug'] = Str::slug($item['nombre']);
        }

        return $lista;
    }

    private static function fotos(): array
    {
        $ruta = base_path('resources/data/especialidades-fotos.json');

        if (! File::exists($ruta)) {
            return [];
        }

        $contenido = File::get($ruta);

        return json_decode($contenido, true) ?: [];
    }

    public static function all(): array
    {
        $lista = self::especialidades();
        $fotos = self::fotos();

        foreach ($lista as $index => $item) {
            $slug = $item['slug'];
            $datosFoto = $fotos[$slug] ?? [];

            $lista[$index]['foto_url'] = $datosFoto['foto_url'] ?? null;
            $lista[$index]['foto_autor'] = $datosFoto['foto_autor'] ?? null;
            $lista[$index]['foto_autor_url'] = $datosFoto['foto_autor_url'] ?? null;
        }

        return $lista;
    }

    public static function find(string $slug): ?array
    {
        foreach (self::all() as $especialidad) {
            if (($especialidad['slug'] ?? null) === $slug) {
                $resultado = $especialidad;
                $resultado['icono_paths'] = self::$iconos[$especialidad['icono']] ?? [];

                return $resultado;
            }
        }

        return null;
    }
}
