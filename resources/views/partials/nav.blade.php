{{--
    Navegación fija del sitio público. El menú móvil se resuelve sin JS
    (checkbox + CSS, `resources/css/public.css`) porque `resources/js/app.js`
    está vacío en este proyecto — evita sumar una dependencia (Alpine, etc.)
    solo para esto. Los links a #especialidades / #servicios / #nosotros /
    #contacto apuntan a las secciones correspondientes en `home.blade.php`.
--}}
<header class="cb-nav cb-nav--visible" id="cb-nav">
    <input type="checkbox" id="cb-nav-toggle" class="cb-nav-checkbox">

    <div class="cb-nav-inner mx-auto flex max-w-7xl items-center justify-between gap-6 px-6 py-4 sm:px-10 lg:px-16">
        <a href="{{ url('/') }}#inicio" class="shrink-0">
            <img
                src="{{ asset('images/logo-horizontal-white.png') }}"
                alt="Clínica Benites"
                class="h-8 w-auto sm:h-9"
            >
        </a>

        <nav class="hidden items-center gap-9 lg:flex" aria-label="Navegación principal">
            <a href="{{ url('/') }}#inicio" class="cb-nav-link">Inicio</a>
            <a href="{{ url('/') }}#especialidades" class="cb-nav-link">Especialidades</a>
            <a href="{{ url('/') }}#servicios" class="cb-nav-link">Servicios</a>
            <a href="{{ url('/') }}#nosotros" class="cb-nav-link">Nosotros</a>
            <a href="{{ url('/') }}#contacto" class="cb-nav-link">Contacto</a>
        </nav>

        <div class="hidden lg:block">
            {{--
                TODO: reemplazar por el número real de WhatsApp de la clínica
                antes de publicar. El mensaje precargado (?text=) ya queda
                andando solo con reemplazar el número — no depende de eso.
            --}}
            <a href="https://wa.me/593000000000?text=Hola%2C%20deseo%20agendar%20una%20cita%20en%20Cl%C3%ADnica%20Benites." class="cb-btn-primary" target="_blank" rel="noopener">
                Agendar por WhatsApp
            </a>
        </div>

        <label for="cb-nav-toggle" class="cb-burger lg:hidden" aria-label="Abrir menú">
            <span class="cb-burger-line"></span>
            <span class="cb-burger-line"></span>
            <span class="cb-burger-line"></span>
        </label>
    </div>

    <nav class="cb-nav-mobile" aria-label="Navegación móvil">
        <a href="{{ url('/') }}#inicio" class="cb-nav-link">Inicio</a>
        <a href="{{ url('/') }}#especialidades" class="cb-nav-link">Especialidades</a>
        <a href="{{ url('/') }}#servicios" class="cb-nav-link">Servicios</a>
        <a href="{{ url('/') }}#nosotros" class="cb-nav-link">Nosotros</a>
        <a href="{{ url('/') }}#contacto" class="cb-nav-link">Contacto</a>
        <a href="https://wa.me/593000000000?text=Hola%2C%20deseo%20agendar%20una%20cita%20en%20Cl%C3%ADnica%20Benites." class="cb-btn-primary mt-3 justify-center" target="_blank" rel="noopener">
            Agendar por WhatsApp
        </a>
    </nav>

    <script>
        (() => {
            const nav = document.getElementById('cb-nav');
            if (!nav) return;

            let lastScrollY = window.scrollY;
            let ticking = false;

            const applyNavState = () => {
                const currentY = window.scrollY;
                const scrollingDown = currentY > lastScrollY + 6;
                const atTop = currentY <= 12;

                if (atTop) {
                    nav.classList.add('cb-nav--visible');
                    nav.classList.remove('cb-nav--accent');
                } else if (scrollingDown) {
                    nav.classList.remove('cb-nav--visible');
                    nav.classList.remove('cb-nav--accent');
                } else {
                    nav.classList.add('cb-nav--visible');
                    nav.classList.add('cb-nav--accent');
                }

                lastScrollY = currentY;
                ticking = false;
            };

            const onScroll = () => {
                if (!ticking) {
                    window.requestAnimationFrame(applyNavState);
                    ticking = true;
                }
            };

            window.addEventListener('scroll', onScroll, { passive: true });
            applyNavState();
        })();
    </script>
</header>
