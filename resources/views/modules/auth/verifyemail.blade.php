@if(session('mensaje'))
<script>
    Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: '{{ session('mensaje')}}',
    });
</script>
@endif

<p>Por favor, verifica tu correo electrónico antes de continuar.</p>

<form method="POST" action="{{ route('verification.send') }}">
    @csrf
    <button type="submit">Reenviar correo de verificación</button>
</form>
