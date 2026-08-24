<?php

namespace Database\Seeders;

use App\Models\Area;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    /**
     * Especialidades reales de Clínica Benites, según el material de marketing
     * compartido por el contacto interno (Servicios_CB_2026.pdf, 24 ago 2026).
     * Ver MEMORIA.md, sección 6.1, para el detalle completo de esta respuesta.
     */
    public function run(): void
    {
        $especialidades = [
            'Auditoría Médica',
            'Anestesiología y Terapia del Dolor',
            'Cardiología',
            'Cateterismo Cardiaco',
            'Cirugía General y Digestiva',
            'Cirugía Vascular',
            'Cirugía Plástica',
            'Cirugía Oncológica',
            'Cirugía Pediátrica',
            'Cirugía Holep de Próstata',
            'Cirugía Torácica',
            'Coloproctología',
            'Cuidados Críticos',
            'Endocrinología',
            'Gastroenterología',
            'Ginecología',
            'Médico Ocupacional',
            'Nutrición Clínica',
            'Nutricionista',
            'Neurología',
            'Laparoscopía',
            'Otorrinolaringología',
            'Oncocirugía Traumatológica',
            'Pediatría y Neonatología',
            'Traumatología y Ortopedia',
            'Terapia Intensiva',
            'Urología',
        ];

        foreach ($especialidades as $nombre) {
            // firstOrCreate evita duplicados si el seeder se corre más de una vez
            // (ej. en un entorno donde ya se habían cargado áreas de prueba con el mismo nombre).
            Area::firstOrCreate(['nombre' => $nombre]);
        }
    }
}
