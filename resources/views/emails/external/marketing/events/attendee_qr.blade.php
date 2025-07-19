@component('mail::message')
# Hola {{ $attendee->name }},

### Aquí tienes tu código QR para el evento

Este QR garantiza tu acceso al evento recuerda confirmar
tu asistencia cuando asistas. Ante cualquier duda no dudes
en contactar con tu vendedor asignado o con marketing@artilec.com

@component('mail::panel')
    <li>🔗 <a href="{{$event->url}}">{{$event->title}}</a>
    <li>📍 Lugar: {{$event->location_gmap}}
    <li>🌟 Exponente: {{$event->exponent}}
    <li>📅 Fecha: {{$event->date_start . ' a ' . $event->date_end}}
    <li>⏰ Horario: {{$event->date_time_start . ' a ' . $event->date_time_end}}
@endcomponent

Gracias por participar en nuestro evento. 💙
<div style="text-align: center;">
    <img src="{{ $attendee->qr_image_url }}" alt="Código QR" style="max-width:200px;">
</div>
@component('mail::button', ['url' => $attendee->qr_image_url])
    Ver Código QR
@endcomponent



@endcomponent
