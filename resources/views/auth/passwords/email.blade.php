@extends('layouts.app')

<form method="POST" action="{{ route('password.email') }}">
    @csrf
    <input type="email" name="correo_electronico" required placeholder="Tu correo electrónico">
    <button type="submit">Enviar enlace</button>
</form>
