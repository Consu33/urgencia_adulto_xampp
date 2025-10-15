@php
    use Illuminate\Support\Str;
@endphp

<div class="row mb-4">
    <h2 class="mb-3">Tiempo de Espera para Atención</h2>

    @if ($hayCriticos)
        <div class="alert text-center" style="background-color: #dc3545; color: white;">
            <h2>Ingreso de Paciente en Riesgo Vital, su espera puede ser mayor. </h2>
        </div>
    @endif

    {{-- Bloques de ocupación --}}
    <div class="row mb-3">
        @foreach ($categorias as $categoria)
            @if ($categoria['codigo'] !== 'SIN CATEGORIZAR')
                @php
                    $ocupacion = $categoria['cupo'] > 0 ? ($categoria['total'] / $categoria['cupo']) * 100 : 0;
                @endphp
                <div class="col-md-2">
                    <div class="card text-center bg-{{ $categoria['color'] }} text-dark">
                        <div class="card-body p-2">
                            <strong class="fs-5">{{ $categoria['codigo'] }}</strong><br>
                            <span style="font-size: 4em;">
                                {{ $categoria['total'] }} / {{ $categoria['cupo'] }}
                            </span>
                            @if ($ocupacion > 100)
                                <div class="fs-5 mt-1 badge bg-danger text-white">Sobre capacidad</div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    {{-- Panel principal de categorías --}}
    <div class="row" id="panel-categorias">
        @foreach ($categorias as $categoria)
            @if ($categoria['codigo'] !== 'SIN CATEGORIZAR')
                <div class="col-md-4 mb-4">
                    <div class="card border-{{ $categoria['color'] }}" data-categoria="{{ $categoria['codigo'] }}">
                        <div class="card-header text-dark bg-{{ $categoria['color'] }}">
                            <strong>{{ $categoria['codigo'] . ' - ' . $categoria['nombre'] }}</strong>
                        </div>
                        <div class="card-body bg-white">
                            <div class="row">
                                @foreach ($categoria['estados'] as $estadoNombre => $estadoData)
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

                                    <div class="col-md-12 mb-3">
                                        <div class="info-box border border-{{ $categoria['color'] }} bg-white text-dark"
                                            id="card-{{ Str::slug($categoria['codigo']) }}-{{ $estadoSlug }}">

                                            <span
                                                class="info-box-icon border-end border-{{ $categoria['color'] }} bg-white text-dark">
                                                <i class="{{ $estadoData['icono'] }}"></i>
                                            </span>

                                            <div class="info-box-content">
                                                <span
                                                    class="info-box-text fw-bold text-uppercase fw-semibold fs-4">{{ $estadoNombre }}</span>
                                                <span
                                                    class="info-box-number cantidad-{{ $estadoSlug }} contador fs-3">
                                                    {{ $estadoData['cantidad'] }} pacientes
                                                </span>
                                                
                                                @unless ($categoria['codigo'] === 'ESPERA-CAMA')
                                                    <span class="fs-5 text-{{ $color }}"> {{ $espera }} min </span>                                                    
                                                @endunless

                                                <div class="progress mt-2">
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
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</div>
