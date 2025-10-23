@php
    use Illuminate\Support\Str;
@endphp

@extends('layouts.admin')

@section('content')
   <div class="container">
    <h1 class="mb-4"></h1>

    {{-- Contenedor donde se inyecta la vista parcial --}}
    <div id="panel-dinamico">
        @include('admin.panel.parcial')
    </div>
</div>
@endsection

@push('scripts')
<script>
    function actualizarPanel() {
        fetch("{{ route('admin.panel.dinamico') }}")
            .then(response => {
                if (!response.ok) throw new Error("Error al cargar el panel");
                return response.text();
            })
            .then(html => {
                document.getElementById("panel-dinamico").innerHTML = html;
            })
            .catch(error => {
                console.error("Error al actualizar el panel:", error);
            });
    }
    // Actualiza el panel cada 10 segundos
    setInterval(actualizarPanel, 10000);
</script>
@endpush







