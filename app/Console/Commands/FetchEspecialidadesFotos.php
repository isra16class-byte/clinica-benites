<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchEspecialidadesFotos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'especialidades:fetch-fotos {--solo= : Lista de slugs separados por coma para procesar solo esos, sin tocar los demás}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Unsplash photos for the specialties list';

    private const ESPECIALIDADES_BUSQUEDA = [
        'anestesiologia-y-terapia-del-dolor' => 'anesthesiology operating room',
        'auditoria-medica' => 'medical audit healthcare documents',
        'cardiologia' => 'cardiology heart doctor',
        'cateterismo-cardiaco' => 'cardiac catheterization procedure',
        'cirugia-general-y-digestiva' => 'general surgery operating room',
        'cirugia-holep-de-prostata' => 'urology surgery hospital',
        'cirugia-oncologica' => 'oncology surgeon operating',
        'cirugia-pediatrica' => 'pediatric surgery children hospital',
        'cirugia-plastica' => 'plastic surgery clinic',
        'cirugia-toracica' => 'chest lung surgery doctor',
        'cirugia-vascular' => 'vascular surgery hospital',
        'coloproctologia' => 'colorectal surgeon consultation',
        'cuidados-criticos' => 'intensive care unit hospital',
        'endocrinologia' => 'thyroid diabetes doctor',
        'gastroenterologia' => 'gastroenterology doctor hospital',
        'ginecologia' => 'pregnancy checkup doctor',
        'laparoscopia' => 'laparoscopic surgery operating room',
        'medico-ocupacional' => 'workplace safety medical exam',
        'neurologia' => 'neurology doctor brain scan',
        'nutricion-clinica' => 'clinical nutrition doctor',
        'nutricionista' => 'nutritionist healthy food consultation',
        'oncocirugia-traumatologica' => 'orthopedic surgery hospital',
        'otorrinolaringologia' => 'ENT doctor examination',
        'pediatria-y-neonatologia' => 'pediatrician baby doctor',
        'terapia-intensiva' => 'intensive care unit hospital',
        'traumatologia-y-ortopedia' => 'orthopedics doctor xray',
        'urologia' => 'kidney urologist exam',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $especialidades = self::ESPECIALIDADES_BUSQUEDA;
        $solo = $this->option('solo');

        if ($solo !== null && trim((string) $solo) !== '') {
            $slugsSolicitados = array_map('trim', explode(',', $solo));
            $slugsSolicitados = array_filter($slugsSolicitados, fn ($slug) => $slug !== '');

            $especialidades = [];
            foreach ($slugsSolicitados as $slug) {
                if (! array_key_exists($slug, self::ESPECIALIDADES_BUSQUEDA)) {
                    $this->warn("El slug '{$slug}' no existe en ESPECIALIDADES_BUSQUEDA");
                    continue;
                }

                $especialidades[$slug] = self::ESPECIALIDADES_BUSQUEDA[$slug];
            }
        }

        $resultados = [];

        foreach ($especialidades as $slug => $termino) {
            $response = Http::withHeaders([
                'Authorization' => 'Client-ID ' . config('services.unsplash.access_key'),
            ])->get('https://api.unsplash.com/search/photos', [
                'query' => $termino,
                'per_page' => 1,
                'orientation' => 'landscape',
            ]);

            $fotoUrl = null;
            $autorNombre = null;
            $autorUrl = null;

            $results = $response->json('results', []);
            $foto = $results[0] ?? null;

            if ($foto) {
                $fotoUrl = $foto['urls']['regular'] ?? null;
                $autorNombre = $foto['user']['name'] ?? null;
                $autorUrl = ($foto['user']['links']['html'] ?? null) ? ($foto['user']['links']['html'] . '?utm_source=clinica_benites&utm_medium=referral') : null;
            } else {
                $this->warn("No se encontraron resultados para {$slug}");
            }

            $resultados[$slug] = [
                'foto_url' => $fotoUrl,
                'foto_autor' => $autorNombre,
                'foto_autor_url' => $autorUrl,
            ];

            $this->line(sprintf('%s %s', $slug, $foto ? '✓' : '✗'));

            sleep(1);
        }

        $directory = base_path('resources/data');

        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $path = $directory . '/especialidades-fotos.json';

        if (file_exists($path)) {
            $jsonExistente = json_decode(file_get_contents($path), true);
            $resultados = array_merge($jsonExistente ?? [], $resultados);
        }

        file_put_contents($path, json_encode($resultados, JSON_PRETTY_PRINT));

        return self::SUCCESS;
    }
}
