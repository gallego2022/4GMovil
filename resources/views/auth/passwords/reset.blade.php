<form method="POST" action="{{ route('password.update') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <input type="email" name="correo_electronico" value="{{ old('correo_electronico') }}" required>
    <input type="password" name="password" required placeholder="Nueva contraseña">
    <input type="password" name="password_confirmation" required placeholder="Confirmar contraseña">
    <button type="submit">Restablecer contraseña</button>
</form>
