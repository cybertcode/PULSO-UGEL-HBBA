@component('mail::message')
Fuiste invitado a unirte al equipo **{{ $invitation->team->name }}** en PULSO UGEL.

@if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::registration()))
Si aún no tienes una cuenta, puedes crear una haciendo clic en el botón de abajo. Luego podrás aceptar la invitación al equipo desde el correo.

@component('mail::button', ['url' => route('register')])
Crear cuenta
@endcomponent

Si ya tienes una cuenta, acepta la invitación haciendo clic aquí:
@else
Acepta la invitación haciendo clic en el botón de abajo:
@endif

@component('mail::button', ['url' => $acceptUrl])
Aceptar invitación al equipo
@endcomponent

Si no esperabas recibir esta invitación, puedes ignorar este mensaje.

Atentamente,<br>
**{{ config('app.name') }}**
@endcomponent
