<?php

declare(strict_types=1);

namespace brunoconte3\Test;

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use brunoconte3\Validation\{
    Arrays,
    Compare,
    Format,
    Validator
};

$datas = [
    'texto' => 'abc',
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
    'texto' => 'required|min:5, Mensagem customizada aqui|max:20',
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
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>brunoconte3/validation</title>

    <style>
        body {
            padding: 0px;
            margin: 0px;
            background: #F8F9FA;
        }

        .container {
            width: auto;
            max-width: 1280px;
            padding: 15px 15px;
            margin-right: auto;
            margin-left: auto;
        }

        .container>header#body-title-page>h1 {
            margin: 0px;
            text-align: center;
            color: green;
        }

        .container>header#body-title-page>small {
            display: block;
            width: 100%;
            text-align: center;
        }

        .container>section.body-section-class {
            padding: 20px;
            margin-top: 30px;
            background: #EEEEEE;
            border: 1px solid #eee;
            border-radius: .25rem;
        }

        .container>section.body-section-class>h3 {
            margin: 0px 0px 15px 0px;
        }

        .container>section.body-section-class>div.item-section-class {
            display: block;
            width: 100%;
            height: auto;
        }

        .container>section.body-section-class>div.item-section-class>p {
            margin: 30px 0px 10px 0px;
        }

        .container>section.body-section-class>div.item-section-class>ol {
            margin: 30px 0px 10px 0px;
            padding-left: 15px;
        }

        .container>section.body-section-class>div.item-section-class>ol>li {
            padding: 2px 0px;
        }

        .container>section.body-section-class>div.item-section-class>div {
            background: rgb(255, 255, 255);
            padding: 15px;
            border: 1px solid #eee;
            border-left: 4px solid #4CAF50;
            overflow-x: auto;
        }
    </style>
</head>

<body>
    <div class="container">
        <header id="body-title-page">
            <h1>Brunoconte3/Validation</h1>
            <small>Versão 4.17.1</small>
        </header>

        <!-- Validação de dados -->
        <section class="body-section-class">
            <h3># Validação de dados</h3>

            <div class="item-section-class">
                <p>$validator->set($datas, $rules)</p>
                <div>
                    <?php
                    $validator = new Validator();
                    Format::convertTypes($datas, $rules);
                    $validator->set($datas, $rules);

                    echo 'Itens a validar: ' . count($datas) . '<hr>';
                    if (!$validator->getErros()) {
                        echo 'Dados válidados com sucesso!';
                    } else {
                        echo 'Itens Validados: ' . count($validator->getErros()) . '<hr>';
                        var_dump($validator->getErros());
                    }
                    ?>
                </div>
            </div>
        </section>

        <!-- Manipular Arrays -->
        <section class="body-section-class">
            <h3># Manipular Arrays</h3>

            <div class="item-section-class">
                <ol>
                    <li>Arrays::searchKey($array, 'primeiro')</li>
                    <li>Arrays::searchKey($array, 'segundo')</li>
                    <li>Arrays::searchKey($array, 'nao-existe')</li>
                </ol>
                <div class="class-section-code">
                    <?php
                    $array = ['primeiro' => 15, 'segundo' => 25];
                    // Procura chave no array, e retorna a posição ==> returns 0
                    var_dump(Arrays::searchKey($array, 'primeiro'));
                    // Procura chave no array, e retorna a posição ==> returns 1
                    var_dump(Arrays::searchKey($array, 'segundo'));
                    // Procura chave no array, e retorna a posição ==> returns null
                    var_dump(Arrays::searchKey($array, 'nao-existe'));
                    ?>
                </div>
            </div>

            <div class="item-section-class">
                <p>Arrays::renameKey()</p>
                <div class="class-section-code">
                    <?php
                    $array = ['primeiro' => 10, 'segundo' => 20];
                    echo 'Array base';
                    var_dump($array);

                    echo '<hr>';

                    echo 'Array alterado';
                    Arrays::renameKey($array, 'primeiro', 'novoNome');
                    var_dump($array); //Renomeia a chave do array ==> ['renamed' => 10, 'second' => 20];
                    ?>
                </div>
            </div>

            <div class="item-section-class">
                <p>Arrays::checkExistIndexByValue()</p>
                <div class="class-section-code">
                    <?php
                    $array = [
                        'frutas' => [
                            'fruta_1' => 'Maçã', 'fruta_2' => 'Pêra', 'fruta_3' => 'fruta', 'fruta_4' => 'Uva'
                        ],
                        'verduras' => ['verdura_1' => 'Rúcula', 'verdura_2' => 'Acelga', 'verdura_3' => 'Alface'],
                        'legume' => 'Tomate'
                    ];

                    $array = ['primeiro' => 10, 'segundo' => 20];
                    echo 'Array para busca';
                    var_dump($array);

                    echo '<hr>';
                    echo 'Retorno da busca';

                    // Verifica no array, se existe algum indíce com o valor desejado
                    var_dump(Arrays::checkExistIndexByValue($array, 'Tomate'));
                    ?>
                </div>
            </div>

            <div class="item-section-class">
                <p>Arrays::findValueByKey()</p>
                <div class="class-section-code">
                    <?php
                    $array = [
                        'frutas' => [
                            'fruta_1' => 'Maçã', 'fruta_2' => 'Pêra', 'fruta_3' => 'fruta', 'fruta_4' => 'Uva'
                        ],
                        'verduras' => ['verdura_1' => 'Rúcula', 'verdura_2' => 'Acelga', 'verdura_3' => 'Alface'],
                        'legume' => 'Tomate'
                    ];

                    echo 'Array para busca';
                    var_dump($array);

                    echo '<hr>';
                    echo 'Retorno da busca';

                    // Realiza a busca no array, através da key e retorna um array com todos indíces localizados
                    var_dump(Arrays::findValueByKey($array, 'fruta_2'));
                    ?>
                </div>
            </div>

            <div class="item-section-class">
                <p>Arrays::findIndexByValue()</p>
                <div class="class-section-code">
                    <?php
                    $array = [
                        'frutas' => [
                            'fruta_1' => 'Maçã', 'fruta_2' => 'Pêra', 'fruta_3' => 'fruta', 'fruta_4' => 'Uva'
                        ],
                        'verduras' => ['verdura_1' => 'Rúcula', 'verdura_2' => 'Acelga', 'verdura_3' => 'Alface'],
                        'legume' => 'Tomate'
                    ];

                    echo 'Array para busca';
                    var_dump($array);

                    echo '<hr>';
                    echo 'Retorno da busca';

                    // Realiza a busca no array, através de um valor e rotorna um array com todos itens localizados
                    var_dump(Arrays::findIndexByValue($array, 'Rúcula'));
                    ?>
                </div>
            </div>

            <div class="item-section-class">
                <p>Arrays::convertArrayToXml()</p>
                <div class="class-section-code">
                    <?php
                    $xml = new \SimpleXMLElement('<root/>');
                    Arrays::convertArrayToXml($array, $xml); // Converte array em Xml
                    var_dump($xml->asXML());
                    ?>
                </div>
            </div>

            <div class="item-section-class">
                <p>Arrays::convertJsonIndexToArray()</p>
                <div class="class-section-code">
                    <?php
                    $array = [
                        'frutas' => [
                            'fruta_1' => 'Maçã', 'fruta_2' => 'Pêra', 'fruta_3' => 'fruta', 'fruta_4' => 'Uva'
                        ],
                        'verduras' => '{"verdura_1": "Rúcula", "verdura_2": "Acelga", "verdura_3": "Alface"}'
                    ];

                    echo "Array para formatação";
                    var_dump($array);
                    echo "<hr>";

                    // Verifica no array, se possui algum indíce com JSON e o transforma em array
                    echo "Array formatado";
                    Arrays::convertJsonIndexToArray($array);
                    var_dump($array);
                    ?>
                </div>
            </div>
        </section>
    </div>
</body>

</html>
