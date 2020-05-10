<?php

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use brunoconte3\Validation\Validator;

$datas = [
    'sexo' => '',
    'cnpj' => '33452731000159',
    'cnpjComMascara' => '33.452.731/0001-59',
    'telefone' => '44999696',
    'cpf' => '12547845874',
    'cpfComMascara' => '125.478.458-74',
    'nome' => 'a',
    'numero' => 12345678,
    'email' => 'bruno.com',
    'texto' => 'abc',
    'validarPassandoJson' => '@&451',
    'idade' => 'a',
    'senha' => '@11111111',
    'cep' => '8704750',
    'data' => '31/04/1990',
    'hora' => '24:03',
    'url' => 'ww.test.c',
    'ip' => '1.1.1',
    'mac' => '00:00',
    'dia' => 32,
    'qtde' => 3,
];

//Aceita divisao das regras por PIPE ou formato JSON
$rules = [
    'sexo' => 'required',
    'cnpj' => 'required|min:18|max:18|companyIdentification',
    'cnpjComMascara' => 'required|min:18|max:18|companyIdentificationMask',
    'telefone' => 'required|phone',
    'cpf' => 'required|identifier',
    'cpfComMascara' => 'required|identifierMask',
    'nome' => 'required|min:2',
    'numero' => 'max:5',
    'email' => 'email',
    'texto' => 'required|min:5, Mensagem customizada aqui|max:20',
    'validarPassandoJson' => '{"required":"true","type":"alpha"}',
    'idade' => 'numeric',
    'senha' => 'float, O campo senha deve ser do tipo Inteiro!|required|max:8',
    'cep' => '{"type":"zipcode"}',
    'data' => 'dateBrazil',
    'hora' => '{"type":"hour"}',
    'url' => 'url',
    'ip' => 'ip',
    'mac' => 'mac',
    'dia' => 'numMax:31',
    'qtde' => 'numMin:5',
];

$validator = new Validator();
$validator->set($datas, $rules);

echo 'Itens a validar: ' . count($datas) . '<hr>';
if (!$validator->getErros()) {
    echo 'Dados válidados com sucesso!';
} else {
    echo '<pre>';
    echo 'Itens Validados: ' . count($validator->getErros()) . '<hr>';
    print_r($validator->getErros());
}
