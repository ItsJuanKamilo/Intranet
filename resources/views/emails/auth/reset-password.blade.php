@component('mail::message')
# Hemos recibido una solicitud para restablecer tu contraseña.

Si no hiciste esta solicitud, puedes ignorar este correo.

@component('mail::button', ['url' => $url])
Restablecer Contraseña
@endcomponent

Este enlace expirará en 60 minutos.

Si tienes problemas con el botón, copia y pega el siguiente enlace en tu navegador:
[{{ $url }}]({{ $url }})

Gracias 💙
@endcomponent
