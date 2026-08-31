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
        </div>
    </section>

    @include('partials.footer')
</x-layouts.public>
