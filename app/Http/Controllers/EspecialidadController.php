<?php

namespace App\Http\Controllers;

use App\Support\Especialidades;

class EspecialidadController extends Controller
{
    public function show(string $slug)
    {
        $especialidades = Especialidades::all();
        $especialidad = null;

        foreach ($especialidades as $item) {
            if (($item['slug'] ?? null) === $slug) {
                $especialidad = $item;
                break;
            }
        }

        if ($especialidad === null) {
            abort(404);
        }

        $indiceActual = null;
        foreach ($especialidades as $indice => $item) {
            if (($item['slug'] ?? null) === $slug) {
                $indiceActual = $indice;
                break;
            }
        }

        $relacionadas = [];
        if ($indiceActual !== null) {
            $total = count($especialidades);
            $paso = 1;

            while (count($relacionadas) < 3 && $paso <= $total) {
                $indiceSiguiente = ($indiceActual + $paso) % $total;

                if (($especialidades[$indiceSiguiente]['slug'] ?? null) !== $slug) {
                    $relacionadas[] = $especialidades[$indiceSiguiente];
                }

                $paso++;
            }
        }

        return view('especialidad', [
            'especialidad' => $especialidad,
            'relacionadas' => $relacionadas,
        ]);
    }
}
