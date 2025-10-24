@php
    use Illuminate\Support\Str;
@endphp

    {{-- Encabezado y alerta dentro del contenedor --}}
    <div>
        <h1 class="text-center fs-2 mb-5" style="font-weight: 900;">
            Tiempo de Espera para Atención Urgencia Adulto
        </h1>

        @if ($hayCriticos)
            <div class="alert alert-danger text-center fs-3 py-4 text-uppercase" style="background-color: #E82A2A; color: white; font-weight: 900;">
                PACIENTE EN RIESGO VITAL, SU ESPERA PUEDE VERSE ENLENTECIDA
            </div>
        @endif
    </div>
    

    {{-- Bloques de ocupación en fila horizontal, fuera del contenedor --}}
    <div class="d-flex justify-content-center gap-4">
        @foreach ($categorias as $categoria)
            @if ($categoria['codigo'] !== 'SIN CATEGORIZAR')
                @php
                    $ocupacion = $categoria['cupo'] > 0 ? ($categoria['total'] / $categoria['cupo']) * 100 : 0;
                @endphp

                <div style="width: 220px;" class="card text-center bg-{{ $categoria['color'] }} text-dark border-0 shadow-none">
                    <div class="card-body p-4">
                        <strong class="fs-3">{{ $categoria['codigo'] }}</strong><br>
                        <span class="fs-1">
                            {{ $categoria['total'] }} / {{ $categoria['cupo'] }}
                        </span>

                        @if ($ocupacion > 100)
                            <div class="mt-3">
                                <span class="badge bg-danger text-white w-100 py-2 fs-6 text-center rounded">
                                    <strong>SATURADO</strong>
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        @endforeach
    </div>


        {{-- Contenedor desplazable horizontalmente --}}
        <div id="panel-categorias" class="d-flex justify-content-center flex-nowrap gap-4 px-4 py-3">
            {{-- Tarjetas de categorías --}}
            @foreach ($categorias as $categoria)
                {{-- Excluir categoría 'SIN CATEGORIZAR' --}}
                @if ($categoria['codigo'] !== 'SIN CATEGORIZAR')
                    {{-- Tarjeta de categoría con los bordes arriba --}}
                    {{-- <div class="card border-{{ $categoria['color'] }} shadow" style="width: 22vw; min-width: 300px;"
                    data-categoria="{{ $categoria['codigo'] }}">
                    {{-- Encabezado de la tarjeta --}}
                    {{-- <div class="card-header text-dark bg-{{ $categoria['color'] }} fs-4 text-center">
                        <strong>{{ $categoria['codigo'] }}<br>{{ $categoria['nombre'] }}</strong>
                    </div>
                    <div class="card-body bg-white p-3"> --}}

                    {{-- Tarjeta de categoría con banda lateral --}}
                    <div class="card d-flex flex-row shadow" style="width: 22vw; min-width: 300px;"
                        data-categoria="{{ $categoria['codigo'] }}">
                        {{-- Banda vertical izquierda con color clínico --}}
                        <div
                            style="width: 12px; background-color: var(--bs-{{ $categoria['color'] }}); border-top-left-radius: 0.5rem; border-bottom-left-radius: 0.5rem;">
                        </div>
                        {{-- Contenido de la tarjeta --}}
                        <div class="flex-grow-1 bg-white p-3">
                            {{-- Título de categoría --}}
                            <div class="text-center mb-2">
                                <strong class="fs-5 text-dark">{{ $categoria['codigo'] }}</strong>
                            </div>
                            {{-- Estados dentro de la categoría --}}
                            @foreach ($categoria['estados'] as $estadoNombre => $estadoData)
                                {{-- Cálculo de variables para la visualización --}}
                                @php
                                    $estadoSlug = Str::slug($estadoNombre);
                                    $espera = $estadoData['promedio'];
                                    $umbral = $categoria['umbrales'];
                                    $color =
                                        $espera > $umbral
                                            ? 'danger'
                                            : ($espera > $umbral * 0.7
                                                ? 'warning'
                                                : 'success');
                                @endphp
                                {{-- Tarjeta de estado --}}
                                <div class="info-box d-flex bg-light text-dark rounded mb-3 shadow"
                                    id="card-{{ Str::slug($categoria['codigo']) }}-{{ $estadoSlug }}">

                                    {{-- Contenido del estado --}}
                                    <div class="d-flex align-items-center">

                                        {{-- Icono y detalles del estado --}}
                                        <span class="me-3">
                                            <i class="{{ $estadoData['icono'] }} fa-2x text-{{ $categoria['color'] }}"></i>
                                        </span>
                                        {{-- Detalles del estado --}}
                                        <div>
                                            <div class="fs-5 fw-bold text-uppercase">{{ $estadoNombre }}</div>
                                            <div class="fs-3 fw-bold">{{ $estadoData['cantidad'] }} pacientes</div>
                                            {{-- Tiempo de espera promedio --}}
                                            {{-- Visualización de tiempo y barra de espera --}}
                                            
                                            @unless (
                                                $categoria['codigo'] === 'ESI 1' ||
                                                Str::lower(trim($estadoNombre)) === 'en atencion' ||
                                                Str::lower(trim($estadoNombre)) === 'en espera de cama'
                                            )
                                                <span class="fs-3 text-dark fw-bold"> {{ $espera }} min </span>
                                            @endunless

                                            <div class="progress mt-2" style="height: 12px;">
                                                <div class="progress-bar bg-{{ $categoria['color'] }}"
                                                    style="width: {{ min($espera * 2, 100) }}%">
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>



