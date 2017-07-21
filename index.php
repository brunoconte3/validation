<?php

    require 'vendor/autoload.php';
    
    $datas = [
        'nome'  => 'mauricio123',
        'email' => 'mauricio.web',
        'senha' => '12345678'
    ];
    
    $rules = [
        'nome'  => 'required|regex:/^[a-zA-Z]+$/',
        'email' => 'required|email|max:50',
        'senha' => 'required|min:8|max:12'
    ];
    
    $teste = new Mammoth\Validation\Validator();
    
    $teste->set($datas, $rules);
    
    if(!$teste->getErros()){
        echo 'OK';
    } else {
        var_dump($teste->getErros());
    }

