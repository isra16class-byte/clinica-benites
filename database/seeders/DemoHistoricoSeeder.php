<?php

namespace Database\Seeders;

use App\Models\Alergia;
use App\Models\Area;
use App\Models\Cama;
use App\Models\Cirugia;
use App\Models\Cita;
use App\Models\Factura;
use App\Models\HistoriaClinica;
use App\Models\Internamiento;
use App\Models\ItemInventario;
use App\Models\LoteInventario;
use App\Models\Medico;
use App\Models\MovimientoInventario;
use App\Models\OrdenEstudio;
use App\Models\Paciente;
use App\Models\Quirofano;
use App\Models\ServicioAmbulancia;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Datos de demostración con historial repartido en los últimos 12 meses,
 * pensado para que el Dashboard gerencial (MEMORIA.md sección 6.6) se vea
 * con contenido real: los 2 ChartWidget necesitan facturas/citas de varios
 * meses distintos, y AlertasOperativasWidget necesita al menos un caso que
 * cruce cada uno de sus 3 umbrales (lote por vencer, factura vencida >30
 * días, cama ocupada >14 días).
 *
 * A propósito NO toca `areas` ni `users` — se asume que ya existen (27
 * áreas reales vía AreaSeeder, y el usuario admin ya creado a mano). Solo
 * inserta sobre una base ya limpia del resto de tablas; no usa
 * firstOrCreate porque estos son datos de prueba, no catálogo real —
 * correrlo dos veces sin limpiar antes simplemente duplica todo.
 *
 * Uso: sail artisan db:seed --class=DemoHistoricoSeeder
 */
class DemoHistoricoSeeder extends Seeder
{
    public function run(): void
    {
        $areas = Area::all();

        if ($areas->isEmpty()) {
            $this->command?->warn('No hay áreas cargadas — corre primero AreaSeeder.');

            return;
        }

        $admin = User::where('rol', 'admin')->first() ?? User::first();

        if (! $admin) {
            $this->command?->warn('No hay ningún usuario en la base — crea el admin primero (ver MEMORIA.md sección 9).');

            return;
        }

        $medicos = $this->crearMedicos($areas, 10);
        $pacientes = $this->crearPacientes(22);
        $this->crearAlergias($pacientes);

        $citas = $this->crearCitas($pacientes, $medicos, $areas, 18);
        $this->crearHistoriaClinicas($citas);
        $this->crearFacturas($pacientes, $citas, 18);

        $items = $this->crearItemsInventario();
        $lotes = $this->crearLotesInventario($items);
        $this->crearMovimientosInventario($lotes, $admin, $pacientes, $citas);

        $camas = $this->crearCamas();
        $quirofanos = $this->crearQuirofanos();
        $this->crearInternamientos($pacientes, $camas, $medicos, $citas);
        $this->crearCirugias($pacientes, $quirofanos, $medicos, $citas);
        $this->crearOrdenesEstudio($pacientes, $medicos, $citas);
        $this->crearServiciosAmbulancia($pacientes);

        $this->command?->info('Datos de demostración cargados.');
    }

    /**
     * @return \Illuminate\Support\Collection<int, Medico>
     */
    private function crearMedicos($areas, int $cantidad)
    {
        $nombres = ['Carlos', 'María', 'Luis', 'Ana', 'Jorge', 'Paula', 'Diego', 'Sofía', 'Andrés', 'Valeria', 'Roberto', 'Camila'];
        $apellidos = ['García', 'Rodríguez', 'Martínez', 'López', 'Pérez', 'Sánchez', 'Torres', 'Vargas', 'Chávez', 'Mendoza', 'Ortiz', 'Cedeño'];

        $medicos = collect();

        for ($i = 0; $i < $cantidad; $i++) {
            $nombre = $nombres[$i % count($nombres)];
            $apellido = $apellidos[($i + 3) % count($apellidos)];

            $medicos->push(Medico::create([
                'nombres' => $nombre,
                'apellidos' => $apellido,
                'area_id' => $areas->random()->id,
                'telefono' => '09'.random_int(10000000, 99999999),
                'email' => strtolower($nombre.'.'.$apellido.$i.'@clinicabenites.test'),
            ]));
        }

        return $medicos;
    }

    /**
     * @return \Illuminate\Support\Collection<int, Paciente>
     */
    private function crearPacientes(int $cantidad)
    {
        $nombres = ['Juan', 'Lucía', 'Pedro', 'Gabriela', 'Miguel', 'Daniela', 'Fernando', 'Isabel', 'Ricardo', 'Verónica', 'Esteban', 'Mónica', 'Iván', 'Karla', 'Freddy', 'Nathaly', 'Wilson', 'Priscila', 'Xavier', 'Johanna', 'Marco', 'Belén'];
        $apellidos = ['Benites', 'Zambrano', 'Vera', 'Cedeño', 'Loor', 'Alcívar', 'Solórzano', 'Delgado', 'Moreira', 'Intriago', 'Bravo', 'Pinargote', 'Zamora', 'Vélez', 'Anchundia', 'Pincay', 'Macías', 'Suárez', 'Quiroz', 'Rivadeneira', 'Pico', 'Cañarte'];
        $sexos = ['Masculino', 'Femenino'];

        $pacientes = collect();

        for ($i = 0; $i < $cantidad; $i++) {
            $nombre = $nombres[$i % count($nombres)];
            $apellido = $apellidos[($i + 5) % count($apellidos)];
            $cedula = '13'.str_pad((string) random_int(0, 99999999), 8, '0', STR_PAD_LEFT);

            $pacientes->push(Paciente::create([
                'nombres' => $nombre,
                'apellidos' => $apellido,
                'cedula' => $cedula,
                'fecha_nacimiento' => Carbon::now()->subYears(random_int(1, 85))->subDays(random_int(0, 365))->format('Y-m-d'),
                'telefono' => '09'.random_int(10000000, 99999999),
                'email' => strtolower($nombre.'.'.$apellido.$i.'@example.test'),
                'direccion' => 'Av. de prueba '.random_int(1, 999).' y calle '.random_int(1, 50),
                'sexo' => $sexos[$i % 2],
            ]));
        }

        return $pacientes;
    }

    /**
     * Citas repartidas en los últimos 12 meses (para el gráfico "Por
     * área — año en curso" solo cuentan las de 2026, así que la mayoría
     * cae dentro del año en curso). Las de fecha pasada quedan
     * "atendida" o "cancelada"; las de fecha futura cercana quedan
     * "pendiente"/"confirmada".
     *
     * @return \Illuminate\Support\Collection<int, Cita>
     */
    private function crearCitas($pacientes, $medicos, $areas, int $cantidad)
    {
        $citas = collect();

        for ($i = 0; $i < $cantidad; $i++) {
            // Reparte en los últimos 11 meses + próximos 15 días.
            $offsetDias = random_int(-335, 15);
            $fecha = Carbon::now()->addDays($offsetDias);

            $esFutura = $fecha->isFuture();
            $estado = $esFutura
                ? ['pendiente', 'confirmada'][random_int(0, 1)]
                : ['atendida', 'atendida', 'atendida', 'cancelada'][random_int(0, 3)]; // mayoría atendida

            $medico = $medicos->random();
            $horaInicio = random_int(8, 16);

            $citas->push(Cita::create([
                'paciente_id' => $pacientes->random()->id,
                'medico_id' => $medico->id,
                'area_id' => $medico->area_id,
                'fecha' => $fecha->format('Y-m-d'),
                'hora_inicio' => sprintf('%02d:00:00', $horaInicio),
                'hora_fin' => sprintf('%02d:30:00', $horaInicio),
                'estado' => $estado,
                'origen' => 'programada',
                'notas' => null,
            ]));
        }

        return $citas;
    }

    private function crearHistoriaClinicas($citas): void
    {
        $motivos = ['Control rutinario', 'Dolor abdominal', 'Chequeo post-operatorio', 'Consulta por resultados de laboratorio', 'Seguimiento de tratamiento', 'Malestar general'];
        $diagnosticos = ['Sin hallazgos relevantes', 'Cuadro viral leve', 'Hipertensión controlada', 'Gastritis', 'Evolución favorable', 'Requiere estudios adicionales'];

        foreach ($citas->where('estado', 'atendida') as $cita) {
            HistoriaClinica::create([
                'paciente_id' => $cita->paciente_id,
                'medico_id' => $cita->medico_id,
                'cita_id' => $cita->id,
                'motivo_consulta' => $motivos[array_rand($motivos)],
                'diagnostico' => $diagnosticos[array_rand($diagnosticos)],
                'tratamiento' => 'Indicaciones entregadas al paciente.',
                'notas' => null,
            ]);
        }
    }

    /**
     * Facturas repartidas en los últimos 12 meses. A propósito deja
     * varias 'pendiente' con fecha de hace más de 30 días (para que
     * dispare la alerta de "facturas vencidas") y varias 'pagado' en
     * distintos meses (para que el gráfico de ingresos tenga curva).
     */
    private function crearFacturas($pacientes, $citas, int $cantidad): void
    {
        $metodos = ['Efectivo', 'Tarjeta', 'Transferencia'];

        for ($i = 0; $i < $cantidad; $i++) {
            $offsetDias = random_int(5, 365);
            $fecha = Carbon::now()->subDays($offsetDias);

            // Las facturas de hace más de 30 días: mayoría pagada, pero
            // aseguramos varias pendientes viejas para la alerta.
            if ($offsetDias > 30) {
                $estado = $i % 4 === 0 ? 'pendiente' : ['pagado', 'pagado', 'anulado'][random_int(0, 2)];
            } else {
                // Facturas recientes: mezcla normal, sin forzar vencidas.
                $estado = ['pagado', 'pagado', 'pendiente'][random_int(0, 2)];
            }

            $cita = $citas->random();

            Factura::create([
                'paciente_id' => $pacientes->random()->id,
                'cita_id' => random_int(0, 4) > 0 ? $cita->id : null, // la mayoría enlaza a una cita
                'monto' => random_int(20, 450) + (random_int(0, 99) / 100),
                'estado_pago' => $estado,
                'metodo_pago' => $estado === 'pagado' ? $metodos[array_rand($metodos)] : null,
                'fecha' => $fecha->format('Y-m-d'),
            ]);
        }
    }

    /**
     * @return \Illuminate\Support\Collection<int, ItemInventario>
     */
    private function crearItemsInventario()
    {
        $items = [
            ['nombre' => 'Paracetamol 500mg', 'tipo' => 'medicamento', 'unidad_medida' => 'tableta', 'stock_minimo' => 100, 'precio_unitario' => 0.05],
            ['nombre' => 'Suero fisiológico 1L', 'tipo' => 'insumo', 'unidad_medida' => 'unidad', 'stock_minimo' => 30, 'precio_unitario' => 2.50],
            ['nombre' => 'Guantes de nitrilo (caja)', 'tipo' => 'insumo', 'unidad_medida' => 'caja', 'stock_minimo' => 20, 'precio_unitario' => 8.00],
            ['nombre' => 'Amoxicilina 500mg', 'tipo' => 'medicamento', 'unidad_medida' => 'tableta', 'stock_minimo' => 50, 'precio_unitario' => 0.15],
            ['nombre' => 'Jeringas 5ml', 'tipo' => 'insumo', 'unidad_medida' => 'unidad', 'stock_minimo' => 200, 'precio_unitario' => 0.10],
            ['nombre' => 'Gasa estéril', 'tipo' => 'insumo', 'unidad_medida' => 'unidad', 'stock_minimo' => 100, 'precio_unitario' => 0.20],
        ];

        return collect($items)->map(fn (array $data) => ItemInventario::create($data));
    }

    /**
     * A propósito incluye: un lote ya vencido, dos por vencer dentro de
     * 90 días (el umbral de AlertasOperativasWidget), y el resto con
     * vencimiento lejano — para que la alerta de inventario tenga
     * contenido real sin que todo el catálogo salga como riesgo.
     *
     * @return \Illuminate\Support\Collection<int, LoteInventario>
     */
    private function crearLotesInventario($items)
    {
        $lotes = collect();
        $diasVencimiento = [-10, 20, 60, 200, 400, 500, 15, 300];

        foreach ($items as $i => $item) {
            $dias = $diasVencimiento[$i % count($diasVencimiento)];

            $lotes->push(LoteInventario::create([
                'item_id' => $item->id,
                'numero_lote' => strtoupper(substr($item->nombre, 0, 3)).'-'.random_int(1000, 9999),
                'fecha_vencimiento' => Carbon::now()->addDays($dias)->format('Y-m-d'),
            ]));
        }

        // Un segundo lote por vencer para un par de ítems, para que la
        // alerta no dependa de un único registro.
        foreach ($items->take(2) as $item) {
            $lotes->push(LoteInventario::create([
                'item_id' => $item->id,
                'numero_lote' => strtoupper(substr($item->nombre, 0, 3)).'-'.random_int(1000, 9999),
                'fecha_vencimiento' => Carbon::now()->addDays(45)->format('Y-m-d'),
            ]));
        }

        return $lotes;
    }

    private function crearMovimientosInventario($lotes, User $admin, $pacientes, $citas): void
    {
        // Carga inicial (entrada) de cada lote, repartida en los últimos meses.
        foreach ($lotes as $lote) {
            MovimientoInventario::create([
                'lote_id' => $lote->id,
                'tipo_movimiento' => 'entrada',
                'cantidad' => random_int(50, 300),
                'area_origen' => null,
                'area_destino' => 'bodega',
                'fecha_hora' => Carbon::now()->subDays(random_int(30, 300)),
                'user_id' => $admin->id,
                'paciente_id' => null,
                'cita_id' => null,
                'notas' => 'Carga inicial de lote.',
            ]);
        }

        // Algunas salidas (consumo real en atención), sobre los primeros lotes.
        foreach ($lotes->take(6) as $lote) {
            MovimientoInventario::create([
                'lote_id' => $lote->id,
                'tipo_movimiento' => 'salida',
                'cantidad' => random_int(1, 20),
                'area_origen' => 'bodega',
                'area_destino' => null,
                'fecha_hora' => Carbon::now()->subDays(random_int(1, 25)),
                'user_id' => $admin->id,
                'paciente_id' => $pacientes->random()->id,
                'cita_id' => $citas->random()->id,
                'notas' => 'Consumo registrado en atención.',
            ]);
        }
    }

    /**
     * @return \Illuminate\Support\Collection<int, Cama>
     */
    private function crearCamas()
    {
        $camas = collect();
        $tipos = ['hospitalizacion', 'hospitalizacion', 'hospitalizacion', 'uci', 'uci', 'ucin'];

        foreach ($tipos as $i => $tipo) {
            $camas->push(Cama::create([
                'numero' => strtoupper(substr($tipo, 0, 3)).'-'.($i + 1),
                'tipo' => $tipo,
                'piso' => (string) random_int(1, 4),
            ]));
        }

        return $camas;
    }

    /**
     * @return \Illuminate\Support\Collection<int, Quirofano>
     */
    private function crearQuirofanos()
    {
        return collect([
            Quirofano::create(['numero' => 'Q-1', 'nombre' => 'Quirófano 1', 'estado' => 'libre']),
            Quirofano::create(['numero' => 'Q-2', 'nombre' => 'Quirófano 2', 'estado' => 'libre']),
        ]);
    }

    /**
     * A propósito incluye un internamiento activo con más de 14 días
     * (umbral de AlertasOperativasWidget) para que la alerta de "camas
     * ocupadas hace mucho" tenga contenido real, más otro activo
     * reciente (no dispara la alerta) y el resto ya dados de alta.
     */
    private function crearInternamientos($pacientes, $camas, $medicos, $citas): void
    {
        // Activo, hace mucho (dispara la alerta de >14 días).
        Internamiento::create([
            'paciente_id' => $pacientes->random()->id,
            'cama_id' => $camas[0]->id,
            'medico_id' => $medicos->random()->id,
            'cita_id' => null,
            'fecha_ingreso' => Carbon::now()->subDays(22),
            'fecha_alta' => null,
            'motivo' => 'Observación prolongada.',
            'origen' => 'programado',
            'prioridad' => null,
            'notas' => null,
        ]);

        // Activo, reciente (no dispara la alerta).
        Internamiento::create([
            'paciente_id' => $pacientes->random()->id,
            'cama_id' => $camas[1]->id,
            'medico_id' => $medicos->random()->id,
            'cita_id' => null,
            'fecha_ingreso' => Carbon::now()->subDays(3),
            'fecha_alta' => null,
            'motivo' => 'Postoperatorio.',
            'origen' => 'emergencia',
            'prioridad' => 'esi_3',
            'notas' => null,
        ]);

        // Ya dados de alta, repartidos en meses pasados.
        for ($i = 0; $i < 3; $i++) {
            $ingreso = Carbon::now()->subDays(random_int(40, 300));

            Internamiento::create([
                'paciente_id' => $pacientes->random()->id,
                'cama_id' => $camas[($i + 2) % $camas->count()]->id,
                'medico_id' => $medicos->random()->id,
                'cita_id' => null,
                'fecha_ingreso' => $ingreso,
                'fecha_alta' => $ingreso->copy()->addDays(random_int(2, 10)),
                'motivo' => 'Internamiento programado.',
                'origen' => 'programado',
                'prioridad' => null,
                'notas' => null,
            ]);
        }
    }

    private function crearCirugias($pacientes, $quirofanos, $medicos, $citas): void
    {
        $tipos = ['Apendicectomía', 'Colecistectomía', 'Cirugía de rodilla'];
        $estadosPasados = ['completada', 'completada', 'cancelada'];

        for ($i = 0; $i < 3; $i++) {
            $fecha = Carbon::now()->subDays(random_int(15, 250));

            $cirugia = Cirugia::create([
                'paciente_id' => $pacientes->random()->id,
                'quirofano_id' => $quirofanos->random()->id,
                'medico_principal_id' => $medicos->random()->id,
                'cita_id' => null,
                'fecha' => $fecha->format('Y-m-d'),
                'hora_inicio' => '09:00:00',
                'hora_fin' => '11:30:00',
                'tipo_cirugia' => $tipos[$i],
                'estado' => $estadosPasados[$i],
                'notas' => null,
            ]);

            // Un médico adicional (ej. anestesiólogo) en cada cirugía.
            $adicional = $medicos->where('id', '!=', $cirugia->medico_principal_id)->random();
            $cirugia->medicosAdicionales()->attach($adicional->id, ['rol' => 'anestesiologo']);
        }
    }

    private function crearOrdenesEstudio($pacientes, $medicos, $citas): void
    {
        $tipos = ['laboratorio', 'rayos_x', 'ecografia', 'laboratorio', 'centro_imagen'];

        foreach ($tipos as $tipo) {
            $solicitud = Carbon::now()->subDays(random_int(5, 200));

            OrdenEstudio::create([
                'paciente_id' => $pacientes->random()->id,
                'medico_solicitante_id' => $medicos->random()->id,
                'cita_id' => null,
                'tipo' => $tipo,
                'fecha_solicitud' => $solicitud,
                'fecha_realizacion' => $solicitud->copy()->addDays(random_int(0, 3)),
                'estado' => 'completado',
                'resultado_texto' => 'Resultado dentro de parámetros esperados.',
                'resultado_archivo' => null,
                'notas' => null,
            ]);
        }
    }

    /**
     * Primer módulo del expediente clínico completo (MEMORIA.md sección 8).
     * Solo una parte de los pacientes tiene alergias registradas (no todos
     * las tienen en la realidad) — a propósito no es 1 por paciente.
     */
    private function crearAlergias($pacientes): void
    {
        $alergias = [
            ['alergeno' => 'Penicilina', 'tipo' => 'medicamento', 'severidad' => 'severa', 'reaccion' => 'Anafilaxia.'],
            ['alergeno' => 'Aspirina', 'tipo' => 'medicamento', 'severidad' => 'moderada', 'reaccion' => 'Urticaria y dificultad respiratoria leve.'],
            ['alergeno' => 'Maní', 'tipo' => 'alimento', 'severidad' => 'severa', 'reaccion' => 'Hinchazón facial y de garganta.'],
            ['alergeno' => 'Mariscos', 'tipo' => 'alimento', 'severidad' => 'moderada', 'reaccion' => 'Ronchas en piel.'],
            ['alergeno' => 'Látex', 'tipo' => 'otro', 'severidad' => 'leve', 'reaccion' => 'Irritación en el contacto.'],
            ['alergeno' => 'Sulfas', 'tipo' => 'medicamento', 'severidad' => 'moderada', 'reaccion' => 'Erupción cutánea.'],
        ];

        // Un subconjunto de pacientes (no todos) tiene alguna alergia
        // registrada, para que el aviso destacado se vea tanto en pacientes
        // con alergias como en los que no.
        foreach ($pacientes->random(min(8, $pacientes->count())) as $paciente) {
            $alergia = $alergias[array_rand($alergias)];

            Alergia::create([
                'paciente_id' => $paciente->id,
                'alergeno' => $alergia['alergeno'],
                'tipo' => $alergia['tipo'],
                'severidad' => $alergia['severidad'],
                'reaccion' => $alergia['reaccion'],
                'notas' => null,
            ]);
        }
    }

    private function crearServiciosAmbulancia($pacientes): void
    {
        for ($i = 0; $i < 2; $i++) {
            ServicioAmbulancia::create([
                'paciente_id' => $pacientes->random()->id,
                'origen' => 'Domicilio del paciente',
                'destino' => 'Clínica Benites',
                'fecha_hora' => Carbon::now()->subDays(random_int(10, 180)),
                'motivo' => 'Traslado por emergencia médica.',
                'notas' => null,
            ]);
        }
    }
}
