<?php

declare(strict_types=1);

namespace brunoconte3\Test;

use brunoconte3\Validation\Format;
use brunoconte3\Validation\Validator;

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
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
            min-height: 400px;
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

                    if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {
                        $fileUploadSingle = $_FILES['fileUploadSingle'];
                        $fileUploadMultiple = $_FILES['fileUploadMultiple'];

                        $array = [
                            'fileUploadSingle' => $fileUploadSingle,
                            'fileUploadMultiple' => $fileUploadMultiple
                        ];

                        $rules = [
                            'fileUploadSingle' => 'mimeType:txt;',
                            'fileUploadMultiple' => 'maxUploadSize:10'
                        ];

                        $validator = new Validator();
                        $validator->set($array, $rules);

                        echo '<pre>';
                        print_r($validator->getErros());

                        // var_dump(Format::restructFileArray($fileUploadSingle));
                        // var_dump(Format::restructFileArray($fileUploadMultiple));
                    }

                    ?>

                    <div style="background-color: #eee; padding: 15px; margin-top: 30px;">
                        <form method="POST" enctype="multipart/form-data">
                            <!-- Upload de um único arquivo. -->
                            <div>
                                <input type="file" name="fileUploadSingle" />
                            </div>
                            <!-- Upload de um ou multiplos arquivos. -->
                            <div>
                                <input type="file" name="fileUploadMultiple[]" multiple="multiple">
                            </div>
                            <div>
                                <hr>
                                <button type="submit">Upload</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div>
                    <?php

                    // echo '<p>Aqui vem os seus testes!</p>';

                    // $array = ['cpfOuCnpj' => '04764334879'];
                    // $rules = ['cpfOuCnpj' => 'identifierOrCompany'];

                    // $validator = new Validator();
                    // $validator->set($array, $rules);

                    // echo '<pre>';
                    // print_r($validator->getErros());
                    ?>
                </div>
            </div>
        </section>
    </div>
</body>

</html>
