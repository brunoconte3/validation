<?php

    require 'vendor/autoload.php';
    
    $validator = new Mammoth\Validation\Validator();
    
    $datas = [
        'nome'  => '',
        'email' => '',
        'senha' => ''
    ];
    
    $validator->set($datas, [
        'nome'  => 'required|regex:/^[a-zA-Z\s]+$/',
        'email' => 'required|email|max:50',
        'senha' => 'required|min:8|max:12'
    ]);
    
    if(!$validator->getErros()){
        echo 'OK';
    } else {
        var_dump($validator->getErros());
    }
    
