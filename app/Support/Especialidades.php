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
            ['nombre' => 'Anestesiología y Terapia del Dolor', 'icono' => 'moon', 'descripcion' => 'Especialidad encargada de garantizar la seguridad y el confort del paciente durante procedimientos quirúrgicos, además del manejo integral del dolor agudo y crónico mediante técnicas farmacológicas e intervencionistas.', 'que_tratamos' => ['Evaluación preanestésica antes de cirugía', 'Monitoreo y manejo del dolor durante la cirugía', 'Control del dolor postoperatorio', 'Dolor crónico (lumbar, articular, neuropático) mediante bloqueos e infiltraciones'], 'cuando_consultar' => 'Antes de cualquier cirugía programada, para la valoración con el anestesiólogo.'],
            ['nombre' => 'Auditoría Médica', 'icono' => 'clipboard', 'descripcion' => 'Área dedicada a la revisión y control de calidad de los procesos asistenciales, verificando que la atención brindada cumpla con protocolos clínicos, normativas vigentes y estándares de seguridad del paciente.', 'que_tratamos' => ['Revisión de historias clínicas', 'Indicadores de calidad hospitalaria', 'Cumplimiento de guías de práctica clínica'], 'cuando_consultar' => 'Es un área de gestión interna, no de atención directa al paciente.'],
            ['nombre' => 'Cardiología', 'icono' => 'heart', 'descripcion' => 'Diagnóstico, tratamiento y seguimiento de enfermedades del corazón y del sistema circulatorio.', 'que_tratamos' => ['Hipertensión arterial', 'Arritmias', 'Insuficiencia cardíaca', 'Prevención de enfermedad coronaria'], 'cuando_consultar' => 'Ante dolor en el pecho, palpitaciones, fatiga inusual o antecedentes familiares de enfermedad cardíaca.'],
            ['nombre' => 'Cateterismo Cardiaco', 'icono' => 'heart', 'descripcion' => 'Procedimiento diagnóstico e intervencionista mínimamente invasivo que permite evaluar las arterias coronarias y tratar obstrucciones.', 'que_tratamos' => ['Evaluación de arterias coronarias mediante catéter', 'Angioplastia', 'Colocación de stents'], 'cuando_consultar' => 'Tras hallazgos anormales en pruebas de esfuerzo o ante síntomas de enfermedad coronaria.'],
            ['nombre' => 'Cirugía General y Digestiva', 'icono' => 'scalpel', 'descripcion' => 'Tratamiento quirúrgico de patologías del aparato digestivo, pared abdominal y órganos relacionados.', 'que_tratamos' => ['Apendicectomía', 'Colecistectomía (vesícula)', 'Hernias', 'Tumores del tubo digestivo'], 'cuando_consultar' => 'Desde procedimientos electivos hasta emergencias abdominales; muchos se realizan hoy por vía laparoscópica.'],
            ['nombre' => 'Cirugía Holep de Próstata', 'icono' => 'drop', 'descripcion' => 'Técnica quirúrgica mínimamente invasiva con láser para el tratamiento de la hiperplasia prostática benigna.', 'que_tratamos' => ['Hiperplasia prostática benigna', 'Síntomas urinarios obstructivos (dificultad para orinar, chorro débil, retención)'], 'cuando_consultar' => 'Cuando el crecimiento benigno de la próstata causa síntomas urinarios; permite evitar la cirugía abierta tradicional.'],
            ['nombre' => 'Cirugía Oncológica', 'icono' => 'ribbon', 'descripcion' => 'Tratamiento quirúrgico de tumores malignos en distintos órganos, con enfoque en la resección completa de la lesión.', 'que_tratamos' => ['Resección de tumores malignos', 'Preservación de función y calidad de vida'], 'cuando_consultar' => 'Se coordina con oncología clínica, radioterapia y patología; la detección temprana mejora el pronóstico.'],
            ['nombre' => 'Cirugía Pediátrica', 'icono' => 'baby', 'descripcion' => 'Atención quirúrgica especializada para recién nacidos, niños y adolescentes.', 'que_tratamos' => ['Cirugías menores (hernias, fimosis)', 'Corrección de malformaciones congénitas'], 'cuando_consultar' => 'El equipo y entorno están diseñados para reducir el estrés del niño y la familia.'],
            ['nombre' => 'Cirugía Plástica', 'icono' => 'sparkle', 'descripcion' => 'Procedimientos reconstructivos y estéticos orientados a restaurar la forma y función de tejidos.', 'que_tratamos' => ['Reconstrucción tras quemaduras', 'Cirugía de mama post-mastectomía', 'Procedimientos estéticos faciales y corporales'], 'cuando_consultar' => 'La evaluación previa considera tanto el resultado estético como funcional.'],
            ['nombre' => 'Cirugía Torácica', 'icono' => 'lungs', 'descripcion' => 'Tratamiento quirúrgico de patologías de pulmones, esófago, mediastino y pared torácica.', 'que_tratamos' => ['Biopsias pulmonares', 'Resecciones por cáncer de pulmón', 'Tratamiento de neumotórax recurrente'], 'cuando_consultar' => 'Muchas intervenciones se realizan hoy mediante toracoscopia (mínimamente invasiva).'],
            ['nombre' => 'Cirugía Vascular', 'icono' => 'vein', 'descripcion' => 'Diagnóstico y tratamiento quirúrgico de enfermedades de arterias y venas.', 'que_tratamos' => ['Várices', 'Aneurismas', 'Obstrucciones circulatorias periféricas'], 'cuando_consultar' => 'El diagnóstico se apoya en eco-doppler vascular.'],
            ['nombre' => 'Coloproctología', 'icono' => 'spiral', 'descripcion' => 'Diagnóstico y tratamiento de enfermedades del colon, recto y ano, por vía médica o quirúrgica.', 'que_tratamos' => ['Hemorroides', 'Fisuras anales', 'Enfermedad inflamatoria intestinal', 'Pólipos colónicos (colonoscopías de tamizaje)'], 'cuando_consultar' => 'Ante sangrado rectal, cambios persistentes en el hábito intestinal o dolor anal.'],
            ['nombre' => 'Cuidados Críticos', 'icono' => 'monitor', 'descripcion' => 'Atención especializada a pacientes en estado grave que requieren monitoreo constante y soporte vital avanzado.', 'que_tratamos' => ['Soporte respiratorio (ventilación mecánica)', 'Soporte hemodinámico', 'Manejo de pacientes post-quirúrgicos complejos o con falla orgánica'], 'cuando_consultar' => 'El equipo trabaja de forma coordinada las 24 horas.'],
            ['nombre' => 'Endocrinología', 'icono' => 'molecule', 'descripcion' => 'Diagnóstico y manejo de trastornos hormonales y metabólicos.', 'que_tratamos' => ['Diabetes', 'Enfermedades de tiroides', 'Alteraciones del crecimiento', 'Trastornos de glándulas suprarrenales', 'Osteoporosis y síndrome metabólico'], 'cuando_consultar' => 'El seguimiento suele ser a largo plazo, con control periódico de laboratorio.'],
            ['nombre' => 'Gastroenterología', 'icono' => 'stomach', 'descripcion' => 'Diagnóstico y tratamiento de enfermedades del sistema digestivo.', 'que_tratamos' => ['Reflujo', 'Úlceras', 'Enfermedad inflamatoria intestinal', 'Afecciones hepáticas'], 'cuando_consultar' => 'Ante dolor abdominal persistente, reflujo frecuente o cambios digestivos inexplicados.'],
            ['nombre' => 'Ginecología', 'icono' => 'venus', 'descripcion' => 'Atención integral de la salud del sistema reproductivo femenino.', 'que_tratamos' => ['Control ginecológico preventivo (citología)', 'Miomas y quistes ováricos', 'Trastornos del ciclo menstrual', 'Seguimiento hormonal'], 'cuando_consultar' => 'Se recomienda un control ginecológico al menos una vez al año.'],
            ['nombre' => 'Laparoscopía', 'icono' => 'camera', 'descripcion' => 'Técnica quirúrgica mínimamente invasiva que utiliza pequeñas incisiones y cámara.', 'que_tratamos' => ['Cirugía de vesícula, apéndice y hernias', 'Cirugía ginecológica (quistes ováricos)'], 'cuando_consultar' => 'Reduce el dolor y el tiempo de hospitalización frente a la cirugía abierta tradicional.'],
            ['nombre' => 'Médico Ocupacional', 'icono' => 'briefcase', 'descripcion' => 'Evaluación y seguimiento de la salud de trabajadores en relación con su entorno laboral.', 'que_tratamos' => ['Exámenes de ingreso, periódicos y de retiro laboral', 'Prevención de riesgos ocupacionales'], 'cuando_consultar' => 'Clave para el cumplimiento de normativas de seguridad e higiene laboral.'],
            ['nombre' => 'Neurología', 'icono' => 'nervio', 'descripcion' => 'Diagnóstico y tratamiento de enfermedades del sistema nervioso central y periférico.', 'que_tratamos' => ['Migrañas', 'Epilepsia', 'Accidentes cerebrovasculares', 'Trastornos neurodegenerativos', 'Trastornos del movimiento y del sueño'], 'cuando_consultar' => 'Ante dolores de cabeza intensos o recurrentes, mareos persistentes, pérdida de fuerza o alteraciones de la memoria.'],
            ['nombre' => 'Nutrición Clínica', 'icono' => 'leaf', 'descripcion' => 'Evaluación y manejo nutricional de pacientes con enfermedades que requieren un abordaje dietético específico.', 'que_tratamos' => ['Diabetes', 'Insuficiencia renal', 'Obesidad', 'Soporte nutricional en pacientes hospitalizados'], 'cuando_consultar' => 'Trabaja en conjunto con el médico tratante como parte del manejo integral de enfermedades crónicas.'],
            ['nombre' => 'Nutricionista', 'icono' => 'leaf', 'descripcion' => 'Orientación en hábitos alimenticios saludables y diseño de planes nutricionales personalizados.', 'que_tratamos' => ['Planes de alimentación personalizados', 'Acompañamiento en objetivos de peso y bienestar'], 'cuando_consultar' => 'Dirigido a quienes buscan mejorar su alimentación, sin que medie necesariamente una enfermedad.'],
            ['nombre' => 'Oncocirugía Traumatológica', 'icono' => 'bone', 'descripcion' => 'Tratamiento quirúrgico especializado de tumores óseos y de tejidos blandos del sistema musculoesquelético.', 'que_tratamos' => ['Tumores óseos primarios', 'Tumores de tejidos blandos', 'Reconstrucción con prótesis'], 'cuando_consultar' => 'Se coordina habitualmente con oncología y radiología, con enfoque en preservar la función del miembro afectado.'],
            ['nombre' => 'Otorrinolaringología', 'icono' => 'ear', 'descripcion' => 'Diagnóstico y tratamiento de enfermedades de oído, nariz y garganta.', 'que_tratamos' => ['Trastornos auditivos', 'Sinusitis', 'Amigdalitis recurrente', 'Vértigo de origen ótico', 'Trastornos de la voz'], 'cuando_consultar' => 'Ante pérdida auditiva, congestión nasal crónica o dolor de garganta persistente.'],
            ['nombre' => 'Pediatría y Neonatología', 'icono' => 'baby', 'descripcion' => 'Atención integral de la salud de recién nacidos, niños y adolescentes.', 'que_tratamos' => ['Control de crecimiento y vacunación', 'Cuidado del recién nacido, especialmente prematuros', 'Enfermedades propias de la infancia'], 'cuando_consultar' => 'Los controles periódicos permiten detectar a tiempo problemas de desarrollo.'],
            ['nombre' => 'Terapia Intensiva', 'icono' => 'monitor', 'descripcion' => 'Monitoreo y soporte vital especializado para pacientes con enfermedades graves o inestables.', 'que_tratamos' => ['Soporte respiratorio', 'Soporte cardiovascular y multiorgánico', 'Vigilancia permanente'], 'cuando_consultar' => 'Trabaja de forma coordinada con las demás especialidades según la causa del ingreso.'],
            ['nombre' => 'Traumatología y Ortopedia', 'icono' => 'bone', 'descripcion' => 'Diagnóstico y tratamiento de lesiones y enfermedades del sistema musculoesquelético.', 'que_tratamos' => ['Fracturas y esguinces', 'Lesiones deportivas y de ligamentos', 'Reemplazo articular (cadera, rodilla)'], 'cuando_consultar' => 'La rehabilitación es parte central del tratamiento en la mayoría de los casos.'],
            ['nombre' => 'Urología', 'icono' => 'drop', 'descripcion' => 'Diagnóstico y tratamiento de enfermedades del aparato urinario y del sistema reproductor masculino.', 'que_tratamos' => ['Cálculos renales', 'Infecciones urinarias', 'Afecciones prostáticas', 'Incontinencia urinaria y disfunción eréctil'], 'cuando_consultar' => 'Ante dolor al orinar, sangre en la orina o dolor lumbar tipo cólico.'],
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
            $fotosEspecialidad = is_array($datosFoto['fotos'] ?? null) ? $datosFoto['fotos'] : [];
            $primeraFoto = $fotosEspecialidad[0] ?? null;

            $lista[$index]['fotos'] = $fotosEspecialidad;
            $lista[$index]['foto_url'] = $primeraFoto['foto_url'] ?? null;
            $lista[$index]['foto_autor'] = $primeraFoto['foto_autor'] ?? null;
            $lista[$index]['foto_autor_url'] = $primeraFoto['foto_autor_url'] ?? null;
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
