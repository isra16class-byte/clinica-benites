<x-layouts.public>
    @include('partials.nav')

    @include('sections.hero')

    {{--
        Próximas secciones del sitio público se agregan acá, en orden, cada
        una como su propio archivo en resources/views/sections/ (mismo
        patrón que sections.hero) e incluida con @include — así cada sesión
        nueva solo suma una línea acá sin tocar lo ya construido.
        Ver MEMORIA.md para el estado de qué falta: Especialidades, Servicios,
        Sobre la clínica, Contacto/ubicación.
    --}}
</x-layouts.public>
