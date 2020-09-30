<?php

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use brunoconte3\Validation\{
    Arrays,
    Compare,
    Format,
    Validator
};

$datas = [
    'infOpcional' => 'a',
    'sexo' => '',
    'cnpj' => '52186923000120',
    'telefone' => '449565',
    'cpf' => '12547845874',
    'nome' => 'a',
    'numero' => 12345678,
    'email' => 'bruno.com',
    'texto' => 'abc',
    'validarPassandoJson' => '@&451',
    'idade' => 'a',
    'senha' => '@11111111',
    'cep' => '8704750',
    'data' => '31/04/1990',
    'dataAmericana' => '1990-04-31',
    'hora' => '24:03',
    'url' => 'ww.test.c',
    'ip' => '1.1.1',
    'mac' => '00:00',
    'dia' => '32',
    'qtde' => 3,
    'dataBoleto' => '16/05/2020',
    'dataOutroBoleto' => '2020-05-17',
    'teste' => 'a',
    'testeInt' => '1a23',
    'testeBool' => '1e',
    'testeFloat' => '35,3',
    'testeNumeric' => '59.6a',
    'senhaAlphaNumNoSpace' => '59.6a',
    'nomeAlphaNum' => 'Bru Con 457 !@',
    'campoSomenteTexto' => 'José da Silva1',
    'textoSemAscentos' => 'Téste',
    'textoMaiusculo' => 'NOME comPLETO',
    'textoMinusculo' => 'nome Completo',
    'validarValores' => 'SA',
    'validarEspaco' => 'BRU C',
    'validaJson' => '
        "nome": "Bruno"
    }',
    'validaMes' => 13,
    'cpfOuCnpn' => '83.113.366.0001/01'
];

//Aceita divisao das regras por PIPE ou formato JSON
$rules = [
    'infOpcional' => 'optional|min:2|int',
    'sexo' => 'required',
    'cnpj' => 'required|min:14|max:18|companyIdentification',
    'telefone' => 'required|phone',
    'cpf' => 'required|identifier',
    'nome' => 'required|min:2',
    'numero' => 'max:5',
    'email' => 'email',
    'texto' => 'required|min:5, Mensagem customizada aqui|max:20',
    'validarPassandoJson' => '{"required":"true","type":"alpha"}',
    'idade' => 'numeric',
    'senha' => 'float, O campo senha deve ser do tipo Inteiro!|required|max:8',
    'cep' => '{"type":"zipcode"}',
    'data' => 'dateBrazil',
    'dataAmericana' => 'dateAmerican',
    'hora' => '{"type":"hour"}',
    'url' => 'url',
    'ip' => 'ip',
    'mac' => 'mac',
    'dia' => 'convert|int|numMax:31',
    'qtde' => 'numMin:5',
    'dataBoleto' => 'noWeekend',
    'dataOutroBoleto' => 'noWeekend',
    'teste' => 'array',
    'testeInt' => 'int|convert',
    'testeBool' => 'bool|convert',
    'testeFloat' => 'convert|float',
    'testeNumeric' => 'convert|numeric',
    'senhaAlphaNumNoSpace' => 'alphaNumNoSpecial',
    'nomeAlphaNum' => 'alphaNum',
    'campoSomenteTexto' => 'alpha',
    'textoSemAscentos' => 'alphaNoSpecial',
    'textoMaiusculo' => 'upper',
    'textoMinusculo' => 'lower',
    'validarValores' => 'arrayValues:S-N-T',
    'validarEspaco' => 'notSpace',
    'validaJson' => 'type:json',
    'validaMes' => 'numMonth',
    'cpfOuCnpn' => 'identifierOrCompany'
];

$validator = new Validator();
Format::convertTypes($datas, $rules);
$validator->set($datas, $rules);

echo 'Itens a validar: ' . count($datas) . '<hr>';
if (!$validator->getErros()) {
    echo 'Dados válidados com sucesso!';
} else {
    echo '<pre>';
    echo 'Itens Validados: ' . count($validator->getErros()) . '<hr>';
    print_r($validator->getErros());
}

echo '<br><br>Formatações Exemplos<hr>';

$array = [
    0 => '1',
    1 => '123',
    'a' => '222',
    'b' => 333,
    'c' => ''
];

echo Format::telephone('44999998888') . '<br>';  //Formata Telefone ==> (44) 99999-8888
echo Format::identifier('73381209000') . '<br>';  //Formata CPF ==>  733.812.090-00
echo Format::companyIdentification('39678379000129') . '<br>'; //Formata CNPJ ==> 39.678.379/0001-29
echo Format::zipCode('87030585') . '<br>'; //Formata CEP ==>  87030-585
echo Format::dateBrazil('2020-05-12') . '<br>'; //Formata Data ==>  12/05/2020
echo Format::dateAmerican('12-05-2020') . '<br>'; //Formata Data ==>  2020-05-12
echo Format::currency('1123.45') . '<br>'; //Formata Moeda ==>  1.123,45
echo Format::pointOnlyValue('1.350,45') . '<br>'; //Formata moeda para gravação no BD ==>  1350.45
echo Format::onlyNumbers('548Abc87@') . '<br>'; //Retorna apenas números => 54887;
echo Format::onlyLettersNumbers('548Abc87@') . '<br>'; //Retorna apenas letras e números => 548Abc87;
//[Aplicar qualquer tipo de Mascara, aceita espaço, pontos e outros]
echo Format::mask('#### #### #### ####', '1234567890123456') . '<br>'; //1234 5678 9012 3456

//Os format abaixo, o segundo parametro escolhe o charset, UTF-8 default
echo Format::lower('CArrO') . '<br>'; //Minusculo,  ==> carro
echo Format::upper('Moto') . '<br>'; //Mauiusculo ==> MOTO
echo Format::ucwordsCharset('aÇafrÃo maCaRRão') . '<br>'; //Primeira letra maiuscula ==> Açafrão Macarrão
echo Format::reverse('Abacaxi') . '<br>'; //Retorna string invertida ==> ixacabA

echo '<pre>';
$arrayComNull = Format::emptyToNull($array);
var_dump($arrayComNull); //Converte vazio para null
echo '<br>';

//$value = Format::arrayToInt($array); ==> Opção para sem ser por Referencia
Format::arrayToIntReference($array);
var_dump($array);

echo '<br><br>Comparações Exemplos<hr>';

echo 'Retorna (+30 dias de diferença)<br>';
echo Compare::daysDifferenceBetweenData('31/05/2020', '30/06/2020') . '<br>';

echo 'Compara se a data inicial é menor que a data final (3º parâmetro, aceita mensagem customizada)<br>';
echo 'Data de início é menor que a data final? ';
var_dump(Compare::startDateLessThanEnd('30/07/2020', '30/06/2020'));
echo '<br>'; //Aceita data Americana também

echo 'Diferença entre horas ==> 01:36:28 [Horas exibe negativo e positivo a diferença]<br>';
echo Compare::differenceBetweenHours('10:41:55', '12:18:23') . '<br>';

echo 'Compara se a hora inicial é menor que a hora final (3º parâmetro, aceita mensagem customizada)<br>';
echo Compare::startHourLessThanEnd('12:05:01', '10:20:01') . '<br>';

echo 'Compada a data com a data atual, e retorna a idade da pessoa ';
echo Compare::calculateAgeInYears('20/05/1989') . '<br>';

echo 'Compara igualdade dos campos retorna booleano <br>';
//terceiro parametro opcional, false para não comparar caseSensitive, default true
var_dump(Compare::checkDataEquality('AçaFrão', 'Açafrão'));

echo '<br><br>Manipular Arrays<hr>';

$array = ['primeiro' => 15, 'segundo' => 25];
var_dump(Arrays::searchKey($array, 'primeiro'));   // Procura chave no array, e retorna a posição ==> returns 0
var_dump(Arrays::searchKey($array, 'segundo'));    // Procura chave no array, e retorna a posição ==> returns 1
var_dump(Arrays::searchKey($array, 'nao-existe')); // Procura chave no array, e retorna a posição ==> returns null

$array = ['primeiro' => 10, 'segundo' => 20];
Arrays::renameKey($array, 'primeiro', 'novoNome');
var_dump($array); //Renomeia a chave do array ==> ['renamed' => 10, 'second' => 20];

$array = [
    'frutas' => ['fruta_1' => 'Maçã', 'fruta_2' => 'Pêra', 'fruta_3' => 'fruta', 'fruta_4' => 'Uva'],
    'verduras' => ['verdura_1' => 'Rúcula', 'verdura_2' => 'Acelga', 'verdura_3' => 'Alface'],
    'legume' => 'Tomate'
];

// Verifica no array, se existe algum índice com o valor desejado
var_dump(Arrays::checkExistIndexByValue($array, 'Tomate'));

// Realiza a busca no array, através da key e retorna um array com todos índices localizados
var_dump(Arrays::findValueByKey($array, 'verduras'));

// Realiza a busca no array, através de um valor e rotorna um array com todos itens localizados
var_dump(Arrays::findIndexByValue($array, 'Tomate'));

$xml = new SimpleXMLElement('<root/>');
Arrays::convertArrayToXml($array, $xml); // Converte array em Xml
var_dump($xml->asXML());

$array = [
    'frutas' => ['fruta_1' => 'Maçã', 'fruta_2' => 'Pêra', 'fruta_3' => 'fruta', 'fruta_4' => 'Uva'],
    'verduras' => '{"verdura_1": "Rúcula", "verdura_2": "Acelga", "verdura_3": "Alface"}'
];

// Verifica no array, se possui algum índice com JSON e o transforma em array
Arrays::convertJsonIndexToArray($array);
var_dump($array);
