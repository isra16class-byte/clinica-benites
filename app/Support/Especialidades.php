<?php

namespace App\Support;

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

        foreach ($lista as $index => $item) {
            $lista[$index]['slug'] = Str::slug($item['nombre']);
        }

        return $lista;
    }

    public static function all(): array
    {
        return self::especialidades();
    }

    public static function find(string $slug): ?array
    {
        foreach (self::especialidades() as $especialidad) {
            if (($especialidad['slug'] ?? null) === $slug) {
                $resultado = $especialidad;
                $resultado['icono_paths'] = self::$iconos[$especialidad['icono']] ?? [];

                return $resultado;
            }
        }

        return null;
    }
}
