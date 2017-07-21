<?php

    require 'vendor/autoload.php';
    
    $validator = new Mammoth\Validation\Validator();
    
    $datas = [
        'nome'  => 'mauricio',
        'email' => 'mauricio.web@gmail.com',
        'senha' => '12345678'
    ];
    
    $validator->set($datas, [
        'nome'  => 'required|regex:/^[a-zA-Z]+$/',
        'email' => 'required|email|max:50',
        'senha' => 'required|min:8|max:12'
    ]);
    
    if(!$validator->getErros()){
        echo 'OK';
    } else {
        var_dump($validator->getErros());
    }
    
