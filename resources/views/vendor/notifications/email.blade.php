<x-mail::message>
{{-- Saudação --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
# Olá!
@endif

{{-- Linhas de introdução --}}
@foreach ($introLines as $line)
{{ $line }}

@endforeach

{{-- Botão de ação --}}
@isset($actionText)
<?php
    $color = match ($level) {
        'success', 'error' => $level,
        default => 'primary',
    };
?>
<x-mail::button :url="$actionUrl" :color="$color">
{{ $actionText }}
</x-mail::button>
@endisset

{{-- Linhas de encerramento --}}
@foreach ($outroLines as $line)
{{ $line }}

@endforeach

{{-- Assinatura --}}
@if (! empty($salutation))
{{ $salutation }}
@else
Atenciosamente,<br>
{{ config('app.name') }}
@endif

{{-- Subcopy com link alternativo --}}
@isset($actionText)
<x-slot:subcopy>
Se o botão "{{ $actionText }}" não funcionar, copie e cole o endereço abaixo no seu navegador:
<span class="break-all">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
</x-slot:subcopy>
@endisset
</x-mail::message>
