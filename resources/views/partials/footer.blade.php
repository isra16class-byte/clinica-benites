{{--
    Footer del sitio público. Deliberadamente simple (logo + links + WhatsApp
    + copyright) — no repite contenido de la sección Contacto, solo cierra
    la página. Año dinámico (`date('Y')`) para no tener que tocar esto en
    enero de cada año.
--}}
<footer class="cb-footer">
    <div class="mx-auto flex max-w-7xl flex-col gap-8 px-6 py-14 sm:px-10 lg:flex-row lg:items-center lg:justify-between lg:px-16">
        <a href="{{ url('/') }}#inicio">
            <img src="{{ asset('images/logo-horizontal-white.png') }}" alt="Clínica Benites" class="h-7 w-auto">
        </a>

        <nav class="flex flex-wrap gap-x-8 gap-y-3" aria-label="Navegación del pie de página">
            <a href="{{ url('/') }}#inicio" class="cb-footer-link">Inicio</a>
            <a href="{{ url('/') }}#especialidades" class="cb-footer-link">Especialidades</a>
            <a href="{{ url('/') }}#servicios" class="cb-footer-link">Servicios</a>
            <a href="{{ url('/') }}#nosotros" class="cb-footer-link">Nosotros</a>
            <a href="{{ url('/') }}#contacto" class="cb-footer-link">Contacto</a>
        </nav>

        {{-- TODO: reemplazar por el número real de WhatsApp de la clínica antes de publicar. --}}
        <a href="https://wa.me/593000000000?text=Hola%2C%20deseo%20agendar%20una%20cita%20en%20Cl%C3%ADnica%20Benites." class="cb-btn-ghost" target="_blank" rel="noopener">
            Agendar por WhatsApp
        </a>
    </div>

    <div class="cb-footer-bottom">
        <p>&copy; {{ date('Y') }} Clínica Benites. Todos los derechos reservados.</p>
    </div>
</footer>
