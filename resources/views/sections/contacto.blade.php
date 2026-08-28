{{--
    Sección: Contacto.

    Sin formulario de agendamiento (decisión ya confirmada: el paciente
    no agenda cita desde la web, ver MEMORIA.md sección 1) — el CTA es
    siempre WhatsApp/teléfono, coherente con cómo recepción registra
    citas hoy (manual, ver MEMORIA.md). Sin dirección exacta ni horario:
    no están confirmados por el cliente, no se inventan (mismo criterio
    del resto del sitio). Sin mapa embebido por la misma razón — un
    iframe de Google Maps con coordenadas inventadas sería peor que no
    tener mapa. El panel derecho es decorativo (mismo lenguaje visual del
    hero: grid + orb + un pin), no informativo, para no tener que fingir
    datos que todavía no existen.

    TODOs de números reales: mismos placeholders que `nav.blade.php` y
    `hero.blade.php` (593000000000) — reemplazar los 3 juntos antes de
    publicar.
--}}
<section id="contacto" class="cb-section cb-section--dark relative overflow-hidden">
    <div class="cb-hero-grid" aria-hidden="true"></div>
    <div class="cb-orb cb-orb-gold" style="bottom:-8rem; left:auto; right:-8rem;" aria-hidden="true"></div>

    <div class="relative z-10 mx-auto grid max-w-7xl gap-14 px-6 sm:px-10 lg:grid-cols-12 lg:items-center lg:px-16">
        <div class="cb-reveal lg:col-span-7">
            <p class="cb-eyebrow">Contacto</p>
            <h2 class="cb-headline cb-headline--section">Conversemos sobre tu cita.</h2>
            <p class="cb-section-lede">
                Nuestro equipo de recepción confirma horarios por WhatsApp
                o por teléfono — sin pasos de más.
            </p>

            <div class="cb-cta-row" style="margin-top:2.75rem;">
                {{-- TODO: reemplazar por el número real de WhatsApp de la clínica antes de publicar. --}}
                <a href="https://wa.me/593000000000?text=Hola%2C%20deseo%20agendar%20una%20cita%20en%20Cl%C3%ADnica%20Benites." class="cb-btn-primary" target="_blank" rel="noopener">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M4 20l1.4-4.1A8 8 0 1 1 9 18.6z"/>
                        <path d="M9 10.5c.4 1.8 1.7 3.1 3.5 3.5"/>
                    </svg>
                    Agendar por WhatsApp
                </a>
                {{-- TODO: reemplazar por el número real de recepción antes de publicar. --}}
                <a href="tel:+593000000000" class="cb-btn-ghost">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M5 4h3l1.6 4.3-2 1.5a12 12 0 0 0 5.6 5.6l1.5-2L19 15v3a2 2 0 0 1-2.2 2A16 16 0 0 1 3 5.2 2 2 0 0 1 5 4Z"/>
                    </svg>
                    Llamar a recepción
                </a>
            </div>

            <div class="cb-contact-location">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 21s-7-6.1-7-11.3A7 7 0 0 1 19 9.7C19 15 12 21 12 21Z"/>
                    <circle cx="12" cy="9.5" r="2.4"/>
                </svg>
                Guayaquil, Ecuador
            </div>
        </div>

        <div class="cb-reveal lg:col-span-5" style="animation-delay:.15s">
            <div class="cb-contact-panel">
                <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 21s-7-6.1-7-11.3A7 7 0 0 1 19 9.7C19 15 12 21 12 21Z"/>
                    <circle cx="12" cy="9.5" r="2.4"/>
                </svg>
                <p class="cb-contact-panel-title">Atención de emergencias</p>
                <p class="cb-contact-panel-text">
                    Equipo listo para casos urgentes, con ambulancia propia
                    para el traslado de pacientes.
                </p>
            </div>
        </div>
    </div>
</section>
