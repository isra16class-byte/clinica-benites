<?php

namespace App\Http\Controllers;

class EspecialidadController extends Controller
{
    public function show(string $slug)
    {
        $especialidad = \App\Support\Especialidades::find($slug);

        if ($especialidad === null) {
            abort(404);
        }

        return view('especialidad', ['especialidad' => $especialidad]);
    }
}
