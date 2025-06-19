@extends('layouts.main')

@section('titulo_pagina', 'Verificación de correo')

@section('contenido')
<div class="text-center text-white">
    <h2 class="text-2xl font-bold mb-4">Verifica tu correo electrónico</h2>
    <p class="mb-4">Hemos enviado un enlace de verificación a tu correo electrónico.</p>

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn btn-primary px-4 py-2">Reenviar correo</button>
    </form>

    @if (session('message'))
    <script>
        Swal.fire({
            title: '¡Enviado!',
            text: "{{ session('message') }}",
            icon: 'success',
            confirmButtonText: 'Aceptar'
        });
    </script>
    @endif

</div>
@endsection