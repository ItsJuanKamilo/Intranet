<x-mail::layout>
{{-- Header
<x-slot:header>
<x-mail::header :url="config('app.url')">
{{ config('app.name') }}
</x-mail::header>
</x-slot:header>
--}}
[![Artilec](https://artilec-chile.s3.sa-east-1.amazonaws.com/emailing/general/header-email.png)](https://www.artilec.com)
{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
<x-slot:subcopy>
<x-mail::subcopy>
@isset($subcopy)
{{ $subcopy }}
@endisset

[![Artilec](https://artilec-chile.s3.sa-east-1.amazonaws.com/emailing/general/footer/footer_email.jpg)](https://www.artilec.com)

[![Artilec](https://artilec-chile.s3.sa-east-1.amazonaws.com/emailing/general/artilec-1.png)](https://www.artilec.com)

</x-mail::subcopy>
</x-slot:subcopy>


{{-- Footer --}}
<x-slot:footer>
<x-mail::footer>
Este es un mensaje automático Por favor, no responda a este correo.<br>
Si tiene alguna pregunta o necesita asistencia, por favor contacte al equipo de soporte.<br>
Horario de atención: Lunes a Viernes 09:00 - 18:00 Chile.<br>
Pueden llamar al +56222407500.<br><br>
© {{ date('Y') }} {{ __('Artilec')  }}. {{ __('Todos los derechos reservados.') }}
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
