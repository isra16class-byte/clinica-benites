<?php

namespace App\Http\Controllers;

use App\Support\Especialidades;

class EspecialidadController extends Controller
{
    public function show(string $slug)
    {
        $especialidad = Especialidades::find($slug);

        if ($especialidad === null) {
            abort(404);
        }

        return view('especialidad', ['especialidad' => $especialidad]);
    }
}
