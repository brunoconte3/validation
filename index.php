<?php

    require 'vendor/autoload.php';
    
    $validator = new Mammoth\Validation\Validator();
    
    $datas = [
        'nome'  => 'mammoth',
        'email' => 'mammoth.support@web.com',
        'senha' => 'mammoth.web'
    ];
    
    $validator->set($datas, [
        'nome'  => 'required|regex:/^[a-zA-Z\s]+$/, O campo nome só deve conter caracteres alfabéticos.',
        'email' => 'required|email|max:50',
        'senha' => 'required|min:8, no mínimo 8.|max:12, no máximo 12.'
    ]);
    
    if(!$validator->getErros()){
        echo 'Dados validados com sucesso!';
    } else {
        var_dump($validator->getErros());
    }
    
