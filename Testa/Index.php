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
            <small>Versão 4.14.0</small>
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

        <!-- Formatações Exemplos -->
        <section class="body-section-class">
            <h3># Formatações Exemplos</h3>

            <div class="item-section-class">
                <p></p>
                <div class="class-section-code">
                    <?php
                    echo '<p>';
                    echo '<i>Format::telephone(44999998888)</i> <br>';
                    echo '<b>DD + Telefone: </b>' . Format::telephone(44999998888);
                    echo '</p>';

                    echo '<p>';
                    echo '<i>Format::returnPhoneOrAreaCode(\'44999998888\')</i> <br>';
                    echo '<b>Telefone: </b>' . Format::returnPhoneOrAreaCode('44999998888');
                    echo '</p>';

                    echo '<p>';
                    echo '<i>Format::returnPhoneOrAreaCode(\'44999998888\', true)</i> <br>';
                    echo '<b>DD: </b>' . Format::returnPhoneOrAreaCode('44999998888', true);
                    echo '</p>';

                    echo '<p>';
                    echo '<i>Format::identifier(\'73381209000\')</i> <br>';
                    echo '<b>CPF: </b>' . Format::identifier('73381209000');
                    echo '</p>';

                    echo '</p>';
                    echo '<i>Format::companyIdentification(\'39678379000129\')</i> <br>';
                    echo '<b>CNPJ: </b>' . Format::companyIdentification('39678379000129');
                    echo '</p>';

                    echo '<p>';
                    echo '<i>Format::zipCode(\'87030585\')</i> <br>';
                    echo '<b>CEP: </b>' . Format::zipCode('87030585');
                    echo '</p>';

                    echo '<p>';
                    echo '<i>Format::dateBrazil(\'2020-05-12\')</i> <br>';
                    echo '<b>Data (dd/mm/yyyy): </b>' . Format::dateBrazil('2020-05-12');
                    echo '</p>';

                    echo '<p>';
                    echo '<i>Format::dateAmerican(\'12-05-2020\')</i> <br>';
                    echo '<b>Data (yyyy-mm-dd): </b>' . Format::dateAmerican('12-05-2020');
                    echo '</p>';

                    echo '<p>';
                    echo '<i>Format::currency(\'1123.45\')</i> <br>';
                    echo '<b>Moeda (BR): </b>' . Format::currency('1123.45');
                    echo '</p>';

                    echo '<p>';
                    echo '<i>Format::currencyUsd(\'1123.45\')</i> <br>';
                    echo '<b>Moeda (USD): </b>' . Format::currencyUsd('1123.45');
                    echo '</p>';

                    echo '<p>';
                    echo '<i>Format::pointOnlyValue(\'1.350,45\')</i> <br>';
                    echo '<b>Moeda para gravção no BD: </b>' . Format::pointOnlyValue('1.350,45');
                    echo '</p>';

                    echo '<p>';
                    echo '<i>Format::onlyNumbers(\'548Abc87@\')</i> <br>';
                    echo '<b>Apenas números: </b>' . Format::onlyNumbers('548Abc87@');
                    echo '</p>';

                    echo '<p>';
                    echo '<i>Format::onlyLettersNumbers(\'548Abc87@\')</i> <br>';
                    echo '<b>Letras e números: </b>' . Format::onlyLettersNumbers('548Abc87@');
                    echo '</p>';

                    //[Aplicar qualquer tipo de Mascara, aceita espaço, pontos e outros]
                    echo '<p>';
                    echo '<i>Format::mask(\'#### #### #### ####', '1234567890123456\')</i> <br>';
                    echo '<b>Máscara genérica: </b>' . Format::mask('#### #### #### ####', '1234567890123456');
                    echo '</p>';

                    //Os format abaixo, o segundo parametro escolhe o charset, UTF-8 default
                    echo '<p>';
                    echo '<i>Format::lower(\'CArrO\')</i> <br>';
                    echo '<b>Minúsculo: </b>' . Format::lower('CArrO');
                    echo '</p>';

                    echo '<p>';
                    echo '<i>Format::upper(\'Moto\')</i> <br>';
                    echo '<b>Maiúsculo: </b>' . Format::upper('Moto');
                    echo '</p>';

                    echo '<p>';
                    echo '<i>Format::ucwordsCharset(\'aÇafrÃo maCaRRão\')</i> <br>';
                    echo '<b>Primeira letra maiúcula: </b>' . Format::ucwordsCharset('aÇafrÃo maCaRRão');
                    echo '</p>';

                    echo '<p>';
                    echo '<i>Format::reverse(\'Abacaxi\')</i> <br>';
                    echo '<b>String invertida: </b>' . Format::reverse('Abacaxi');
                    echo '</p>';
                    ?>
                </div>
            </div>

            <div class="item-section-class">
                <p>Format::emptyToNull($array)</p>
                <div class="class-section-code">
                    <?php
                    $array = [
                        0 => '1',
                        1 => '123',
                        'a' => '222',
                        'b' => 333,
                        'c' => ''
                    ];

                    echo "Array para formatação";
                    var_dump($array);
                    echo '<hr>';

                    $arrayComNull = Format::emptyToNull($array);

                    echo "Array formatado";
                    var_dump($arrayComNull); //Converte vazio para null
                    ?>
                </div>
            </div>

            <div class="item-section-class">
                <p>Format::arrayToIntReference()</p>
                <div class="class-section-code">
                    <?php
                    $array = [
                        0 => '1',
                        1 => '123',
                        'a' => '222',
                        'b' => 333,
                        'c' => ''
                    ];

                    echo "Array para formatação";
                    var_dump($array);
                    echo '<hr>';

                    echo "Array formatado";
                    Format::arrayToIntReference($array);
                    var_dump($array); //Converte vazio para null
                    ?>
                </div>
            </div>
        </section>

        <!-- Comparações Exemplos -->
        <section class="body-section-class">
            <h3># Comparações Exemplos</h3>

            <div class="item-section-class">
                <p>Compare::daysDifferenceBetweenData('31/05/2020', '30/06/2020')</p>
                <div class="class-section-code">
                    <?php
                    echo 'Retorna (+30 dias de diferença)<br>';
                    echo Compare::daysDifferenceBetweenData('31/05/2020', '30/06/2020') . '<br>';
                    ?>
                </div>
            </div>

            <div class="item-section-class">
                <p>Compare::startDateLessThanEnd('30/07/2020', '30/06/2020')</p>
                <div class="class-section-code">
                    <?php
                    echo 'Compara se a data inicial é menor que a data final (3º parâmetro, aceita mensagem
                    customizada)<br>';
                    echo 'Data de início é menor que a data final? ';
                    var_dump(Compare::startDateLessThanEnd('30/07/2020', '30/06/2020'));
                    ?>
                </div>
            </div>

            <div class="item-section-class">
                <p>Compare::differenceBetweenHours('10:41:55', '12:18:23')</p>
                <div class="class-section-code">
                    <?php
                    echo 'Diferença entre horas ==> 01:36:28 [Horas exibe negativo e positivo a diferença]<br>';
                    echo Compare::differenceBetweenHours('10:41:55', '12:18:23') . '<br>';
                    ?>
                </div>
            </div>

            <div class="item-section-class">
                <p>Compare::startHourLessThanEnd('12:05:01', '10:20:01')</p>
                <div class="class-section-code">
                    <?php
                    echo 'Compara se a hora inicial é menor que a hora final (3º parâmetro, aceita mensagem
                    customizada)<br>';
                    echo Compare::startHourLessThanEnd('12:05:01', '10:20:01') . '<br>';
                    ?>
                </div>
            </div>

            <div class="item-section-class">
                <p>Compare::calculateAgeInYears('20/05/1989')</p>
                <div class="class-section-code">
                    <?php
                    echo 'Compada a data com a data atual, e retorna a idade da pessoa ';
                    echo Compare::calculateAgeInYears('20/05/1989') . '<br>';
                    ?>
                </div>
            </div>

            <div class="item-section-class">
                <p>Compare::checkDataEquality('AçaFrão', 'Açafrão')</p>
                <div class="class-section-code">
                    <?php
                    echo 'Compara igualdade dos campos retorna booleano <br>';
                    //terceiro parametro opcional, false para não comparar caseSensitive, default true
                    var_dump(Compare::checkDataEquality('AçaFrão', 'Açafrão'));
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
