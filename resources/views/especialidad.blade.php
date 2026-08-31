<x-layouts.public
    title="{{ $especialidad['nombre'] }} · Clínica Benites"
    description="Información sobre {{ $especialidad['nombre'] }} en Clínica Benites, Guayaquil."
>
    @include('partials.nav')

    <section class="cb-section cb-section--light relative">
        <div class="overflow-hidden">
            <div class="cb-hero-grid" aria-hidden="true"></div>
        </div>
        <div class="mx-auto max-w-6xl px-6 py-24 sm:px-10 lg:px-16">
            <a href="{{ url('/') }}#especialidades" class="cb-footer-link">← Volver a especialidades</a>

            <div class="grid grid-cols-1 gap-12 md:grid-cols-2 md:items-start mt-8">
                <!-- Columna izquierda: ícono y foto -->
                <div>
                    <div>
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" aria-hidden="true">
                            @foreach ($especialidad['icono_paths'] as $d)
                                <path d="{{ $d }}"/>
                            @endforeach
                        </svg>
                    </div>

                    @if($especialidad['foto_url'] ?? null)
                        <img src="{{ $especialidad['foto_url'] }}" alt="{{ $especialidad['nombre'] }}" class="mt-6 h-80 w-full rounded-2xl object-cover sm:h-96">
                        <p class="mt-2 text-xs text-gray-400">
                            Foto: <a href="{{ $especialidad['foto_autor_url'] }}" target="_blank" rel="noopener" class="text-gray-500 underline underline-offset-2">{{ $especialidad['foto_autor'] }}</a> / Unsplash
                        </p>
                    @endif

                    @if (is_array($especialidad['fotos'] ?? null) && count($especialidad['fotos']) > 1)
                        <div class="mt-4 grid grid-cols-2 gap-3">
                            @foreach (array_slice($especialidad['fotos'], 1) as $foto)
                                @php
                                    $fotoUrl = $foto['foto_url'] ?? null;
                                    $fotoAutor = $foto['foto_autor'] ?? 'Autor';
                                    $fotoAutorUrl = $foto['foto_autor_url'] ?? '#';
                                @endphp

                                @if ($fotoUrl)
                                    <div>
                                        <img src="{{ $fotoUrl }}" alt="{{ $especialidad['nombre'] }}" class="h-32 w-full rounded-lg object-cover">
                                        <p class="mt-2 text-[10px] text-gray-400">
                                            Foto: <a href="{{ $fotoAutorUrl }}" target="_blank" rel="noopener" class="text-gray-400 underline underline-offset-2">{{ $fotoAutor }}</a> / Unsplash
                                        </p>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Columna derecha: contenido principal -->
                <div>
                    <p class="cb-eyebrow cb-eyebrow--light">Especialidad</p>
                    <h1 class="cb-headline cb-headline--section">{{ $especialidad['nombre'] }}</h1>

                    <p class="cb-about-body mt-6">
                        {{ $especialidad['descripcion'] }}
                    </p>

                    <!-- Qué tratamos -->
                    <div class="mt-8">
                        <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Qué tratamos</h2>
                        <ul class="mt-4 space-y-2">
                            @foreach ($especialidad['que_tratamos'] as $item)
                                <li class="flex items-start">
                                    <svg class="h-4 w-4 mr-3 mt-0.5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-gray-700">{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Cuándo consultar -->
                    <div class="rounded-xl border border-gray-200 bg-white/60 p-4 mt-6">
                        <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Cuándo consultar</h2>
                        <p class="text-sm text-gray-700 mt-2">{{ $especialidad['cuando_consultar'] }}</p>
                    </div>

                    <a href="https://wa.me/593000000000?text=Hola%2C%20deseo%20agendar%20una%20cita%20en%20Cl%C3%ADnica%20Benites." class="cb-btn-primary mt-8 inline-flex" target="_blank" rel="noopener">
                        Consultar por WhatsApp
                    </a>
                </div>
            </div>

            <section class="mt-12">
                <p class="cb-eyebrow cb-eyebrow--light">Atención</p>
                <h2 class="cb-headline cb-headline--section">Proceso de atención</h2>

                <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-2xl border border-gray-200 bg-white/70 p-5">
                        <div class="text-4xl font-bold text-amber-600">1</div>
                        <h3 class="mt-4 text-lg font-semibold text-gray-900">Consulta inicial</h3>
                        <p class="mt-2 text-sm text-gray-600">Evaluación con el especialista para entender el motivo de consulta y antecedentes.</p>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-white/70 p-5">
                        <div class="text-4xl font-bold text-amber-600">2</div>
                        <h3 class="mt-4 text-lg font-semibold text-gray-900">Diagnóstico</h3>
                        <p class="mt-2 text-sm text-gray-600">Exámenes y estudios complementarios según el caso, para confirmar el diagnóstico.</p>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-white/70 p-5">
                        <div class="text-4xl font-bold text-amber-600">3</div>
                        <h3 class="mt-4 text-lg font-semibold text-gray-900">Tratamiento</h3>
                        <p class="mt-2 text-sm text-gray-600">Definición del plan de tratamiento más adecuado, médico o quirúrgico según corresponda.</p>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-white/70 p-5">
                        <div class="text-4xl font-bold text-amber-600">4</div>
                        <h3 class="mt-4 text-lg font-semibold text-gray-900">Seguimiento</h3>
                        <p class="mt-2 text-sm text-gray-600">Controles posteriores para evaluar la evolución y ajustar el tratamiento si hace falta.</p>
                    </div>
                </div>
            </section>

            @if (!empty($relacionadas))
                <section class="mt-12">
                    <p class="cb-eyebrow cb-eyebrow--light">Explorar</p>
                    <h2 class="cb-headline cb-headline--section">Especialidades relacionadas</h2>

                    <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-3">
                        @foreach ($relacionadas as $relacionada)
                            <a href="{{ url('/especialidades/' . $relacionada['slug']) }}" class="group block rounded-2xl border border-gray-200 bg-white/70 p-5 transition duration-200 hover:border-amber-400 hover:shadow-sm">
                                <span class="text-[10px] font-semibold uppercase tracking-[0.2em] text-gray-500">Especialidad</span>
                                <h3 class="mt-3 text-lg font-semibold text-gray-900 group-hover:text-amber-700">{{ $relacionada['nombre'] }}</h3>
                                <span class="mt-4 inline-flex items-center text-sm font-medium text-amber-700">Ver especialidad →</span>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </section>

    @include('partials.footer')
</x-layouts.public>
