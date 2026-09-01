<x-filament-widgets::widget>
    <x-filament::section
        icon="heroicon-o-bell-alert"
        icon-color="warning"
    >
        <x-slot name="heading">
            Alertas operativas
        </x-slot>

        <x-slot name="description">
            Cosas que hoy solo se ven si alguien entra a buscarlas a mano.
        </x-slot>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

            {{-- Lotes de inventario vencidos o por vencer --}}
            <div class="rounded-xl border border-gray-200 p-4 dark:border-white/10">
                <div class="flex items-center justify-between gap-2">
                    <h3 class="text-sm font-semibold text-gray-950 dark:text-white">
                        Lotes por vencer
                    </h3>
                    <x-filament::badge :color="$lotes['total'] > 0 ? 'danger' : 'success'">
                        {{ $lotes['total'] }}
                    </x-filament::badge>
                </div>

                @if ($lotes['total'] > 0)
                    <ul class="mt-3 space-y-2">
                        @foreach ($lotes['items'] as $lote)
                            <li class="text-sm">
                                <span class="font-medium text-gray-950 dark:text-white">
                                    {{ $lote->item?->nombre ?? 'Ítem eliminado' }}
                                </span>
                                <span class="text-gray-500 dark:text-gray-400">
                                    — lote {{ $lote->numero_lote }},
                                    vence {{ $lote->fecha_vencimiento->translatedFormat('d M Y') }}
                                    ({{ $lote->fecha_vencimiento->isPast() ? 'vencido' : 'en ' . (int) round(now()->diffInDays($lote->fecha_vencimiento, true)) . ' días' }})
                                </span>
                            </li>
                        @endforeach
                    </ul>

                    @if ($lotes['total'] > 5)
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            Y {{ $lotes['total'] - 5 }} más.
                        </p>
                    @endif
                @else
                    <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                        Ningún lote vence dentro de los próximos {{ $diasLotePorVencer }} días.
                    </p>
                @endif

                <x-filament::link :href="$lotes['url']" size="sm" class="mt-3 inline-block">
                    Ver lotes de inventario
                </x-filament::link>
            </div>

            {{-- Facturas vencidas --}}
            <div class="rounded-xl border border-gray-200 p-4 dark:border-white/10">
                <div class="flex items-center justify-between gap-2">
                    <h3 class="text-sm font-semibold text-gray-950 dark:text-white">
                        Facturas vencidas
                    </h3>
                    <x-filament::badge :color="$facturas['total'] > 0 ? 'danger' : 'success'">
                        {{ $facturas['total'] }}
                    </x-filament::badge>
                </div>

                @if ($facturas['total'] > 0)
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        ${{ number_format($facturas['monto_total'], 2) }} pendientes de cobro hace más de {{ $diasFacturaVencida }} días.
                    </p>

                    <ul class="mt-3 space-y-2">
                        @foreach ($facturas['items'] as $factura)
                            <li class="text-sm">
                                <span class="font-medium text-gray-950 dark:text-white">
                                    {{ $factura->paciente?->nombres ?? 'Paciente eliminado' }}
                                </span>
                                <span class="text-gray-500 dark:text-gray-400">
                                    — ${{ number_format($factura->total, 2) }},
                                    emitida {{ \Illuminate\Support\Carbon::parse($factura->fecha)->translatedFormat('d M Y') }}
                                    ({{ (int) round(now()->diffInDays(\Illuminate\Support\Carbon::parse($factura->fecha), true)) }} días)
                                </span>
                            </li>
                        @endforeach
                    </ul>

                    @if ($facturas['total'] > 5)
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            Y {{ $facturas['total'] - 5 }} más.
                        </p>
                    @endif
                @else
                    <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                        Ninguna factura pendiente supera los {{ $diasFacturaVencida }} días desde su emisión.
                    </p>
                @endif

                <x-filament::link :href="$facturas['url']" size="sm" class="mt-3 inline-block">
                    Ver facturas
                </x-filament::link>
            </div>

            {{-- Camas ocupadas hace demasiado tiempo --}}
            <div class="rounded-xl border border-gray-200 p-4 dark:border-white/10">
                <div class="flex items-center justify-between gap-2">
                    <h3 class="text-sm font-semibold text-gray-950 dark:text-white">
                        Camas ocupadas hace mucho
                    </h3>
                    <x-filament::badge :color="$camas['total'] > 0 ? 'warning' : 'success'">
                        {{ $camas['total'] }}
                    </x-filament::badge>
                </div>

                @if ($camas['total'] > 0)
                    <ul class="mt-3 space-y-2">
                        @foreach ($camas['items'] as $cama)
                            @php $internamiento = $cama->internamientoActivo(); @endphp
                            <li class="text-sm">
                                <span class="font-medium text-gray-950 dark:text-white">
                                    Cama {{ $cama->numero }} ({{ $cama->tipo }})
                                </span>
                                <span class="text-gray-500 dark:text-gray-400">
                                    — {{ $internamiento?->paciente?->nombres ?? 'paciente sin datos' }},
                                    {{ $internamiento ? (int) round(now()->diffInDays($internamiento->fecha_ingreso, true)) : '?' }} días internado
                                </span>
                            </li>
                        @endforeach
                    </ul>

                    @if ($camas['total'] > 5)
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            Y {{ $camas['total'] - 5 }} más.
                        </p>
                    @endif
                @else
                    <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                        Ninguna cama ocupada supera los {{ $diasCamaOcupadaLarga }} días de internamiento.
                    </p>
                @endif

                <x-filament::link :href="$camas['url']" size="sm" class="mt-3 inline-block">
                    Ver camas
                </x-filament::link>
            </div>

        </div>
    </x-filament::section>
</x-filament-widgets::widget>
