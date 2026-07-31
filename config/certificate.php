<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Assinaturas do certificado
    |--------------------------------------------------------------------------
    |
    | Quem é o diretor vem do banco: o palestrante marcado com is_director.
    | Ele assina TODO certificado emitido e, quando é ele quem ministra o curso,
    | o certificado sai só com a assinatura dele.
    |
    | O nome abaixo é apenas o fallback para quando nenhum palestrante estiver
    | marcado — evita certificado sem assinatura enquanto a flag não é definida.
    | O cargo é texto fixo do layout, não vem do cadastro.
    |
    */

    'director' => [
        'name' => 'Fábio Telles de Souza',
        'role' => 'Diretor Empreende Vitória',
    ],

    // Cargo exibido sob a assinatura do palestrante do evento.
    'speaker_role' => 'Palestrante',

];
