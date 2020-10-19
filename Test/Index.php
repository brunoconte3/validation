<?php

declare(strict_types=1);

namespace brunoconte3\Test;

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use brunoconte3\Validation\{
    Format,
    Validator
};

$datas = [
    'texto' => 'abc',
    'validarEspaco' => 'BRU C',
    'validaJson' => '
        "nome": "Bruno"
    }',
    'validaMes' => 13,
    'cpfOuCnpn' => '83.113.366.0001/01'
];

//Aceita divisao das regras por PIPE ou formato JSON
$rules = [
    'texto' => 'required|min:5, Mensagem customizada aqui!|max:20',
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
            margin-bottom: 15px;
        }

        section.body-section-class {
            background-color: white;
            padding: 15px;
        }
    </style>
</head>

<body>
    <div class="container">
        <header id="body-title-page">
            <h1>Brunoconte3/Validation</h1>
            <small>Espaço para fazer seus testes</small>
        </header>

        <section class="body-section-class">
            <div class="item-section-class">
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
    </div>
</body>

</html>
