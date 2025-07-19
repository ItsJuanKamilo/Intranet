@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo">
@else
<img src="https://artilec-chile.s3.sa-east-1.amazonaws.com/logos/LOGO_ARTILEC.png"  width="220" alt="Artilec">
@endif
</a>
</td>
</tr>
