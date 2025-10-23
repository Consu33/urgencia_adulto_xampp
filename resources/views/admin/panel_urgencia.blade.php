@extends('layouts.vista')

@section('content')
<div class="container-fluid px-5">
    <h1 class="text-center fs-1 mb-5"></h1>

    {{-- Contenedor dinámico --}}
    <div id="panel-urgencia-dinamico">
        @include('admin.panel_urgencia_parcial', [
            'hayCriticos' => $hayCriticos,
            'categorias' => $categorias
        ])
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        function actualizarPanelUrgencia() {
            const panel = document.getElementById("panel-urgencia-dinamico");
            if (!panel) return;

            fetch("{{ route('admin.panel_urgencia.dinamico') }}")
                .then(response => {
                    if (!response.ok) throw new Error("Error al cargar el panel");
                    return response.text();
                })
                .then(html => {
                    panel.innerHTML = html;
                })
                .catch(error => {
                    console.error("Error al actualizar el panel:", error);
                });
        }

        setInterval(actualizarPanelUrgencia, 5000);
    });
</script>
@endpush