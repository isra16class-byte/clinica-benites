<x-layouts.public
    title="{{ $especialidad['nombre'] }} · Clínica Benites"
    description="Información sobre {{ $especialidad['nombre'] }} en Clínica Benites, Guayaquil."
>
    @include('partials.nav')

    <section class="cb-section cb-section--light relative">
        <div class="mx-auto max-w-4xl px-6 py-24 sm:px-10 lg:px-16">
            <a href="{{ url('/') }}#especialidades" class="cb-footer-link">← Volver a especialidades</a>

            <div class="mt-8">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" aria-hidden="true">
                    @foreach ($especialidad['icono_paths'] as $d)
                        <path d="{{ $d }}"/>
                    @endforeach
                </svg>
            </div>

            <p class="cb-eyebrow cb-eyebrow--light mt-6">Especialidad</p>
            <h1 class="cb-headline cb-headline--section">{{ $especialidad['nombre'] }}</h1>

            <p class="cb-about-body mt-6">
                Estamos preparando el detalle de esta especialidad: médicos a cargo, horarios de atención y servicios específicos. Mientras tanto, podés consultarnos directamente por WhatsApp o teléfono.
            </p>

            <a href="https://wa.me/593000000000?text=Hola%2C%20deseo%20agendar%20una%20cita%20en%20Cl%C3%ADnica%20Benites." class="cb-btn-primary mt-8 inline-flex" target="_blank" rel="noopener">
                Consultar por WhatsApp
            </a>
        </div>
    </section>

    @include('partials.footer')
</x-layouts.public>
